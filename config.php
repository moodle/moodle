<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype = 'mariadb';  // Use 'mysqli' for MySQL, or 'pgsql' for PostgreSQL.
$CFG->dbhost = 'localhost'; // Database server (usually 'localhost').
$CFG->dbname = 'moodle';  // Your database name (the one you created).
$CFG->dbuser = 'yewo'; // Your database username.
$CFG->dbpass = 'Muwemi2015*'; // Your database password.
$CFG->dbport = '3306'; // Port for the database server (default is 3306 for MySQL).

$CFG->prefix = 'mdl_'; // Table prefix (you can leave this as 'mdl_' by default).

$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '3306',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

$CFG->wwwroot   = 'https://localhost:443';
$CFG->dataroot  = 'C:\\xampp\\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
