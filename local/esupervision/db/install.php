<?php
// local/your_plugin/db/install.php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_esupervision_install() {
    // Create the necessary tables
    xmldb_local_esupervision_create_supervisors_table();
    xmldb_local_esupervision_create_projects_table();
    xmldb_local_esupervision_create_students_table();
    xmldb_local_esupervision_create_feedback_table();

    // Additional installation steps, if any
}

function xmldb_local_esupervision_create_projects_table() {
    global $DB;

    $sql = "
        CREATE TABLE {esupervision_projects} (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            supervisor_id INT(10) UNSIGNED NOT NULL,
            status VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (supervisor_id) REFERENCES {esupervision_supervisors} (id)
        ) ENGINE = InnoDB
    ";

    $DB->execute($sql);
}

function xmldb_local_esupervision_create_supervisors_table() {
    global $DB;

    $sql = "
        CREATE TABLE {esupervision_supervisors} (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE = InnoDB
    ";

    $DB->execute($sql);
}

function xmldb_local_esupervision_create_students_table() {
    global $DB;

    $sql = "
        CREATE TABLE {esupervision_students} (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE = InnoDB
    ";

    $DB->execute($sql);
}

function xmldb_local_esupervision_create_feedback_table() {
    global $DB;

    $sql = "
        CREATE TABLE {esupervision_feedback} (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id INT(10) UNSIGNED NOT NULL,
            student_id INT(10) UNSIGNED NOT NULL,
            feedback_text TEXT NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (project_id) REFERENCES {esupervision_projects} (id),
            FOREIGN KEY (student_id) REFERENCES {esupervision_students} (id)
        ) ENGINE = InnoDB
    ";

    $DB->execute($sql);
}
