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

$CFG->dbtype = "mariadb";
$CFG->dblibrary = "native";
$CFG->dbhost = "localhost";
$CFG->dbname = "qubits-new";
$CFG->dbuser = "root";
$CFG->dbpass = "";
$CFG->prefix = "mdl_";
$CFG->dboptions = array(
    'dbpersist' => 0,
    'dbport' => "",
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

$CFG->admin     = 'admin';
$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');
if(!defined('ABORT_AFTER_CONFIG'))
    require_once(__DIR__ . '/local/qubitssite/site-checker.php'); // For checking domain

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
