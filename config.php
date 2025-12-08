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

// SMTP Configuration
$CFG->smtphosts = getenv('SMTP_HOST') . ':' . (getenv('SMTP_PORT') ?: '587');
$CFG->smtpsecure = getenv('SMTP_SECURITY') ?: 'starttls';
$CFG->smtpuser = getenv('SMTP_USER') ?: '';
$CFG->smtppass = getenv('SMTP_PASSWORD') ?: '';
$CFG->noreplyaddress = getenv('SMTP_FROM') ?: 'noreply@aust-mfg.com';
// $CFG->debugsmtp = true; // Enable SMTP debugging


require_once(__DIR__ . '/lib/setup.php');

