<?php
if(isset($_GET['course_id']) && is_numeric($_GET['course_id']) && $_GET['course_id'] > 0)
    $course_id = $_GET['course_id'];
if(isset($_GET['yl_id']) && is_numeric($_GET['yl_id']) && $_GET['yl_id'] > 0)
    $yl_id = $_GET['yl_id'];
?>
<div class="card rounded-0 mb-3">
    <div class="card-header rounded-0">
        <div class="card-title"><b>Filter Attendance Record</b></div>
    </div>
    <div class="card-body rounded-0">
        <div class="container-fluid">
            <form action="" id="filter-form">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="course_id" class="control-label">Course</label>
                            <select name="course_id" id="course_id" class="form-select form-select-sm rounded-0">
                                <option <?php echo (!isset($course_id)) ? 'selected' : '' ?>>All</option>
                                <?php
                                $dept_qry = $conn->query("SELECT * FROM course_list where `status` = 1 ".(isset($course_id) ? " or course_id ='{$course_id}'" : "")." order by `name` asc");
                                while($row= $dept_qry->fetchArray()):
                                ?>
                                    <option value="<?php echo $row['course_id'] ?>" <?php echo (isset($course_id) && $course_id == $row['course_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="yl_id" class="control-label">Year Level</label>
                            <select name="yl_id" id="yl_id" class="form-select form-select-sm rounded-0">
                                <option <?php echo (!isset($yl_id)) ? 'selected' : '' ?>>All</option>
                                <?php
                                $desg_qry = $conn->query("SELECT * FROM year_lvl order by `year_level` asc");
                                while($row= $desg_qry->fetchArray()):
                                ?>
                                    <option value="<?php echo $row['yl_id'] ?>" <?php echo (isset($yl_id) && $yl_id == $row['yl_id'] ) ? 'selected' : '' ?>><?php echo $row['year_level'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <button class="btn btn-primary btn-sm rounded-0"><i class="fa fa-filter"></i> Filter Records</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Attendance Records</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-success btn-sm py-1 rounded-0" type="button" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover" id="att-list">
            <colgroup>
                <col width="5%">
                <col width="45%">
                <col width="25%">
                <col width="25%">
            </colgroup>
            <thead>
                <tr>
                    <th class="p-0 text-center">#</th>
                    <th class="p-0 text-center">Student</th>
                    <th class="p-0 text-center">Attendance Type</th>
                    <th class="p-0 text-center">Attendance DateTime</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $where = "";
                if(isset($course_id))
                $where .= " and s.course_id = '{$course_id}' ";
                if(isset($yl_id))
                $where .= " and s.yl_id = '{$yl_id}' ";
                 $att_qry = $conn->query("SELECT a.*,(s.lastname || ', ' || s.firstname || ' ' || s.middlename) as `fullname`,s.student_code FROM `attendance_list` a inner join student_list s on a.student_id = s.student_id where s.sy_id= '{$_SESSION['sy_id']}' and date(a.date_created) = '".date("Y-m-d")."' {$where} order by `fullname` asc ");
                 $i = 1;
                 while($row = $att_qry->fetchArray()):
                    $bg = "primary";
                    if($row['type'] == 2)
                    $bg = "danger";
                ?>
                <tr>
                    <td class="align-middle py-0 px-1 text-center"><?php echo $i++; ?></td>
                    <td class="align-middle py-0 px-1">
                        <p class="m-0">
                            <small><b>Student Code:</b> <?php echo $row['student_code'] ?></small><br>
                            <small><b>Name:</b> <?php echo $row['fullname'] ?></small>
                        </p>
                    </td>
                    <td class="align-middle py-0 px-1 text-center">
                        <span class="badge bg-<?php echo $bg ?>"><?php echo $row['type'] == 1 ? "Time In" : "Time Out" ?></span>
                    </td>
                    <td class="align-middle py-0 px-1 text-end"><?php echo date("M d, Y h:i A",strtotime($row['date_created']))  ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if(!$att_qry->fetchArray()): ?>
                    <tr>
                        <th class="text-center" colspan="4">No attendance record found.</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            var _h = $("head").clone()
            var _table = $('#att-list').clone()
            var _el = $("<div>")
            _el.append("<h2 class='text-center'>Attendance List</h2>")
            <?php if(isset($course_id)): ?>
                var course_text = $('#course_id option[value="<?= $course_id ?>"]').text()
                _el.append("<h3 class='text-center'>"+course_text+"</h3>")
            <?php else: ?>
                _el.append("<h3 class='text-center'>All Course</h3>")
            <?php endif; ?>
            <?php if(isset($yl_id)): ?>
                var yl_text = $('#yl_id option[value="<?= $yl_id ?>"]').text()
                _el.append("<h3 class='text-center'>"+yl_text+"</h3>")
            <?php else: ?>
                _el.append("<h3 class='text-center'>All Year Level</h3>")
            <?php endif; ?>
            _el.append("<hr/>")
            _el.append(_table)
            var nw = window.open("","_blank","width=1200,height=900")
                     nw.document.querySelector('head').innerHTML = _h[0].outerHTML
                     nw.document.querySelector('body').innerHTML += _el.html()
                     nw.document.querySelector('body').setAttribute('onload',`window.print(); setTimeout(()=>{ window.close() },500);`)
                     nw.document.close()
                    
        })
        $('#filter-form').submit(function(e){
            e.preventDefault()
            location.href= './?page=attendance_list&'+$(this).serialize()
        })
    })
</script>