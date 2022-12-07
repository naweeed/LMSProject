<style>
    #uni_modal .modal-footer{
        display:none !important
    }
</style>
<?php 
require_once('DBConnection.php');
$qry = $conn->query("SELECT s.*,(s.lastname || ', ' || s.firstname || ' ' || s.middlename) as `name`,c.name as course, yl.year_level FROM `student_list` s INNER JOIN `course_list` c on s.course_id = c.course_id inner join `year_lvl` yl on s.yl_id = yl.yl_id where s.student_id = '{$_GET['id']}'")->fetchArray();
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}

?>
<div class="cotainer-flui">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Name:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $name ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Gender:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1">
                        <?php echo $gender ?>
                        <?php if($gender == "Male"): ?>
                            <span class="fa fa-mars mx-1 text-primary opacity-50" title="Male"></span>
                        <?php else: ?>
                            <span class="fa fa-venus mx-1 text-danger opacity-50" title="Female"></span>
                        <?php endif; ?>
                        </span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Email:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $email ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Contact:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $contact ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Student Code:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $student_code ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Course:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $course ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Year Level:</b></label>
                    <span class="border-bottom border-dark px-2 col-auto flex-grow-1"><?php echo $year_level ?></span>
                </div>
            </div>
        </div>
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>