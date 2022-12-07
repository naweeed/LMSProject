<?php
session_start();
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `student_list` where student_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="student-form">
        <input type="hidden" name="id" value="<?php echo isset($student_id) ? $student_id : '' ?>">
        <input type="hidden" name="sy_id" value="<?php echo isset($_SESSION['sy_id']) ? $_SESSION['sy_id'] : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_code" class="control-label">Student Code</label>
                        <input type="text" name="student_code" autofocus id="student_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($student_code) ? $student_code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="control-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="middlename" class="control-label">Middle Name</label>
                        <input type="text" name="middlename" id="middlename" required class="form-control form-control-sm rounded-0" placeholder="(optional)" value="<?php echo isset($middlename) ? $middlename : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender" class="control-label">Gender</label>
                        <select name="gender" id="gender" class="form-select form-select-sm rounded-0">
                            <option value="Male" <?php echo (isset($gender) && $gender == "Male" ) ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?php echo (isset($gender) && $gender == "Female" ) ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($contact) ? $contact : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="course_id" class="control-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select form-select-sm rounded-0">
                            <option <?php echo (!isset($course_id)) ? 'selected' : '' ?> disabled>Please Seelect Department</option>
                            <?php
                            $dept_qry = $conn->query("SELECT * FROM course_list where `status` = 1 ".(isset($course_id) ? " or course_id ='{$course_id}'" : "")." order by `name` asc");
                            while($row= $dept_qry->fetchArray()):
                            ?>
                                <option value="<?php echo $row['course_id'] ?>" <?php echo (isset($course_id) && $course_id == $row['course_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="yl_id" class="control-label">Year Level</label>
                        <select name="yl_id" id="yl_id" class="form-select form-select-sm rounded-0">
                            <option <?php echo (!isset($yl_id)) ? 'selected' : '' ?> disabled>Please Seelect Department</option>
                            <?php
                            $desg_qry = $conn->query("SELECT * FROM year_lvl order by `year_level` asc");
                            while($row= $desg_qry->fetchArray()):
                            ?>
                                <option value="<?php echo $row['yl_id'] ?>" <?php echo (isset($yl_id) && $yl_id == $row['yl_id'] ) ? 'selected' : '' ?>><?php echo $row['year_level'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0">
                            <option value="1" <?php echo (isset($status) && $status == 1 ) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo (isset($status) && $status == 0 ) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#student-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'Actions.php?a=save_student',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($student_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>