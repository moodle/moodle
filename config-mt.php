<?php  // Moodle configuration file
define('SITE_MAIN_DOMAIN', 'qubits.localhost.com');
unset($CFG);
global $CFG;
$CFG = new stdClass();

/* --- Debugging mode is on ---- */
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('log_errors', 'on');
ini_set('error_reporting', E_ALL);
$CFG->cursitesettings = ''; // Site Settings
//$CFG->debug = 32767; // DEBUG_DEVELOPER // NOT FOR PRODUCTION SERVERS!
// Refer this url https://docs.moodle.org/2x/ca/Debugging

/* --- Debugging mode ends ---- */

$CFG->dbtype = "pgsql";
$CFG->dblibrary = "native";
$CFG->dbhost = "localhost";
$CFG->dbname = "qubits";
$CFG->dbuser = "postgres";
$CFG->dbpass = "mWcdr456#";
$CFG->prefix = "mdl_";
$CFG->dboptions = array(
    'dbpersist' => 0,
    'dbport' => 5432,
    'dbsocket' => "",
    'dbcollation' => "utf8mb4_unicode_ci",
);

$aCurrenturl = explode('.', @$_SERVER['HTTP_HOST']);
if (($aCurrenturl && count($aCurrenturl) > 2) && SITE_MAIN_DOMAIN != $_SERVER['HTTP_HOST']){
    $tenantdomain = $aCurrenturl[0];
} else {
    $tenantdomain = '';
}

//$CFG->wwwroot   = 'http://qubits.localhost.com';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $CFG->wwwroot = 'https://' . $_SERVER['HTTP_HOST'];
    $CFG->maindomainwwwroot = 'https://' . SITE_MAIN_DOMAIN;
} else {
    $CFG->wwwroot = 'http://' . $_SERVER['HTTP_HOST'];
    $CFG->maindomainwwwroot = 'http://' . SITE_MAIN_DOMAIN;
}

//$CFG->dataroot  = 'D:\\xampp\\htdocs\\qubits\\moodledata';
// Creating the separate cache
$CFG->dataroot = __DIR__ . "/../moodledata/";
$CFG->tempdir = $CFG->dataroot . ($tenantdomain ? '/' . $tenantdomain : '') . '/tempdir';
$CFG->cachedir = $CFG->dataroot . ($tenantdomain ? '/' . $tenantdomain : '') . '/cache';
$CFG->localcachedir = $CFG->dataroot . ($tenantdomain ? '/' . $tenantdomain : '') . '/localcachedir';
$CFG->tenantdir = $CFG->dataroot . ($tenantdomain ? '/' . $tenantdomain : '');

// Grades and Sections
$CFG->coursegrades = array("grade1" => "Grade 1", "grade2" => "Grade 2", "grade3" => "Grade 3", "grade4" => "Grade 4", "grade5" => "Grade 5", 
"grade6" => "Grade 6", "grade7" => "Grade 7", "grade8" => "Grade 8", "grade9" => "Grade 9", "grade10" => "Grade 10", 
"grade11" => "Grade 11", "grade12" => "Grade 12");
$CFG->coursesections = array("sectiona" => "Section A", "sectionb" => "Section B", "sectionc" => "Section C", "sectiond" => "Section D",
"sectione" => "Section E", "sectionf" => "Section F", "sectiong" => "Section G");

$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');
if(!defined('ABORT_AFTER_CONFIG'))
    require_once(__DIR__ . '/local/qubitssite/site-checker.php'); // For checking domain

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
