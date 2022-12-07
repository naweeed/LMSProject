<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }

    function last_id(){
        $last_id_qry = $this->query("SELECT last_insert_rowid()");
        $result = $last_id_qry->fetchArray();
        $id = isset($result[0]) ? $result[0] : 0;

        return $id;
    }
    function save_sy(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `school_year` (`school_year`,`status`)VALUES('{$school_year}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `school_year` set {$data} where `sy_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(sy_id) as count from `school_year` where `school_year` = '{$school_year}' ".($id > 0 ? " and sy_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'School Year Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id)){
                    $resp['msg'] = "School Year successfully saved.";
                    $id = $this->last_id();
                }else{
                    $resp['msg'] = "School Year successfully updated.";
                }
                if($status == 1){
                    $this->query("UPDATE `school_year` set `status` = 0 where `sy_id` != '{$id}'");
                }
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New School Year Failed.";
                else
                    $resp['msg'] = "Updating School Year Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_sy(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `school_year` where sy_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'School Year successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_sy(){
        extract($_POST);
        @$update = $this->query("UPDATE `school_year` set `status` = '{$status}' where sy_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'School Year Status has been updated successfully.';
            $this->query("UPDATE `school_year` set `status` = 0 where `sy_id` != '{$id}'");
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_yl(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `year_lvl` (`year_level`)VALUES('{$year_level}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `year_lvl` set {$data} where `yl_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(yl_id) as count from `year_lvl` where `year_level` = '{$year_level}' ".($id > 0 ? " and yl_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Year Level Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Year Level successfully saved.";
                else
                    $resp['msg'] = "Year Level successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Year Level Failed.";
                else
                    $resp['msg'] = "Updating Year Level Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_yl(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `year_lvl` where yl_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Year Level successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_course(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `course_list` (`name`,`status`)VALUES('{$name}','{$status}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `course_list` set {$data} where `course_id` = '{$id}' ";
        }
        @$check= $this->query("SELECT COUNT(course_id) as count from `course_list` where `name` = '{$name}' ".($id > 0 ? " and course_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Course Name already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id)){
                    $resp['msg'] = "Course successfully saved.";
                    $id = $this->last_id();
                }else{
                    $resp['msg'] = "Course successfully updated.";
                }
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Course Failed.";
                else
                    $resp['msg'] = "Updating Course Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_course(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `course_list` where course_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Course successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_course(){
        extract($_POST);
        @$update = $this->query("UPDATE `course_list` set `status` = '{$status}' where course_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Course Status has been updated successfully.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_student(){
        extract($_POST);
        @$check= $this->query("SELECT count(student_id) as `count` FROM `student_list` where `student_code` = '{$student_code}' ".($id > 0 ? " and student_id != '{$id}'" : ''))->fetchArray()['count'];
        if($check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Student Code already exists.";
        }else{
            $data = "";
            foreach($_POST as $k =>$v){
                if(!in_array($k,array('id'))){
                    if(empty($id)){
                        $columns[] = "`{$k}`"; 
                        $values[] = "'{$v}'"; 
                    }else{
                        if(!empty($data)) $data .= ", ";
                        $data .= " `{$k}` = '{$v}'";
                    }
                }
            }
            if(isset($columns) && isset($values)){
                $data = "(".(implode(",",$columns)).") VALUES (".(implode(",",$values)).")";
            }
            if(empty($id)){
                $sql = "INSERT INTO `student_list` {$data}";
            }else{
                $sql = "UPDATE `student_list` set {$data} where student_id = '{$id}'";
            } 
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Student Successfully added.';
                else
                $resp['msg'] = 'Student Successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'An error occured. Error: '.$this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_student(){
        extract($_POST);
        @$delete = $this->query("DELETE FROM `student_list` where student_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Student successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['msg'] = 'An error occure. Error: '.$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_attendance(){
        extract($_POST);
        $type_text = "Time In";
        if($att_type == 2)
        $type_text = "Time Out";
        @$student_id = $this->query("SELECT student_id FROM `student_list` where `student_code` = '{$student_code}'")->fetchArray()['student_id'];
        if($student_id > 0){
            $check = $this->query("SELECT count(attendance_id) as `count` FROM `attendance_list` where `student_id` = '{$student_id}' and `type` = '{$att_type}' and date(`date_created`) = '".date("Y-m-d",strtotime($date_created))."' ")->fetchArray()['count'];
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "You already have ".$type_text. " record today.";
            }else{
            $sql = "INSERT INTO `attendance_list` (`student_id`,`type`,`date_created`) VALUES ('{$student_id}','{$att_type}','{$date_created}')";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
            } else{
                $resp['status'] = 'failed';
                $resp['msg'] = "An error occured. Error: ". $this->lastErrorMsg();
            }
        }

        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Uknown student Code.";
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_sy':
        echo $action->save_sy();
    break;
    case 'delete_sy':
        echo $action->delete_sy();
    break;
    case 'update_stat_sy':
        echo $action->update_stat_sy();
    break;
    case 'save_yl':
        echo $action->save_yl();
    break;
    case 'delete_yl':
        echo $action->delete_yl();
    break;
    case 'save_course':
        echo $action->save_course();
    break;
    case 'delete_course':
        echo $action->delete_course();
    break;
    case 'update_stat_course':
        echo $action->update_stat_course();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'save_student':
        echo $action->save_student();
    break;
    case 'delete_student':
        echo $action->delete_student();
    break;
    case 'save_attendance':
        echo $action->save_attendance();
    break;
    default:
    // default action here
    break;
}