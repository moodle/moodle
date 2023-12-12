<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost'; // Replace with your RDS endpoint
$CFG->dbname    = 'moodle'; // Replace with your database name
$CFG->dbuser    = 'root';   // Replace with your database username
$CFG->dbpass    = 'Muwemi2015*';   // Replace with your database password
$CFG->prefix    = 'mdl_'; // You can change the table prefix if needed
$CFG->dboptions = ['dbcollation' => 'utf8mb4_unicode_ci'];

$CFG->wwwroot   = 'https://localhost';
$CFG->dataroot  = 'C:\\xampp\\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
