<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'sqlsrv';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'DB-HOST-NAME';
$CFG->dbname    = 'DB-NAME';
$CFG->dbuser    = 'DB-USER-NAME';
$CFG->dbpass    = 'DB-USER-PASSWORD';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => true,
  'dbport' => 1433,
  'dbsocket' => false,
  'dbcollation' => 'SQL_Latin1_General_CP1_CI_AS'
);

$CFG->wwwroot   = 'WWW-ROOT';
$CFG->dataroot  = 'DATA-ROOT';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

$CFG->tool_generator_users_password = 'TOOL-GENERATOR-PASSWORD';

$CFG->sslproxy = true;

$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = 'phpu_moodledata';

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
