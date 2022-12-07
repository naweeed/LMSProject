<?php
if(!is_dir('./db'))
    mkdir('./db');
if(!defined('db_file')) define('db_file','./db/attendance_db.db');
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
         $this->open(db_file);
         $this->createFunction('md5', 'my_udf_md5');
         $this->exec("PRAGMA foreign_keys = ON;");
         $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 
        $this->exec("CREATE TABLE IF NOT EXISTS course_list (
            `course_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS school_year (
            `sy_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `school_year` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $this->exec("CREATE TABLE IF NOT EXISTS year_lvl (
            `yl_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `year_level` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS student_list (
            `student_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `student_code` TEXT NOT NULL,
            `firstname` TEXT NOT NULL,
            `middlename` TEXT NULL DEFAULT NULL,
            `lastname` TEXT NOT NULL,
            `gender` TEXT NOT NULL,
            `course_id` INTEGER NULL,
            `sy_id` INTEGER NULL,
            `yl_id` INTEGER NULL,
            `contact` TEXT NOT NULL,
            `email` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_updated` TIMESTAMP NULL,
            FOREIGN KEY(`course_id`) REFERENCES `course_list`(`course_id`) ON DELETE SET NULL,
            FOREIGN KEY(`sy_id`) REFERENCES `school_year`(`sy_id`) ON DELETE SET NULL,
            FOREIGN KEY(`yl_id`) REFERENCES `year_lvl`(`yl_id`) ON DELETE SET NULL
        )");

        $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_stud AFTER UPDATE on `student_list`
        BEGIN
            UPDATE `student_list` SET date_updated = CURRENT_TIMESTAMP where student_id = student_id;
        END
        ");

        $this->exec("CREATE TABLE IF NOT EXISTS attendance_list (
            `attendance_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `student_id` TEXT NOT NULL,
            `type` TINYINT(2) NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_updated` TIMESTAMP NULL,
            FOREIGN KEY(`student_id`) REFERENCES `student_list`(`student_id`) ON DELETE CASCADE
        )");
        $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_att AFTER UPDATE on `attendance_list`
        BEGIN
            UPDATE `attendance_list` SET date_updated = CURRENT_TIMESTAMP where attendance_id = attendance_id;
        END
        ");
       
        $this->exec("INSERT or IGNORE INTO `user_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");
        
    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();