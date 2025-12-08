<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'pgsql';
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('PG_HOST') ?: 'db';
$CFG->dbname    = getenv('PG_DBNAME_MOODLE') ?: 'moodle';
$CFG->dbuser    = getenv('PG_USER') ?: 'moodle';
$CFG->dbpass    = getenv('PG_PASSWORD') ?: 'moodle';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbport' => getenv('PG_PORT') ?: '5432',
);

$CFG->wwwroot   = rtrim(getenv('MOODLE_URL') ?: 'http://localhost', '/');
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

