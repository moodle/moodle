<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script creates config.php file during installation.
 *
 * @package    core
 * @subpackage install
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_REQUEST['lang'])) {
    $lang = preg_replace('/[^A-Za-z0-9_-]/i', '', $_REQUEST['lang']);
} else {
    $lang = 'en';
}

if (isset($_REQUEST['admin'])) {
    $admin = preg_replace('/[^A-Za-z0-9_-]/i', '', $_REQUEST['admin']);
} else {
    $admin = 'admin';
}

// If config.php exists we just created config.php and need to redirect to continue installation
$configfile = './config.php';
if (file_exists($configfile)) {
    header("Location: $admin/index.php?lang=$lang");
    die;
}

define('CLI_SCRIPT', false); // prevents some warnings later
define('AJAX_SCRIPT', false); // prevents some warnings later
define('CACHE_DISABLE_ALL', true); // Disables caching.. just in case.
define('PHPUNIT_TEST', false);
define('IGNORE_COMPONENT_CACHE', true);
define('MDL_PERF_TEST', false);
define('MDL_PERF', false);
define('MDL_PERFTOFOOT', false);
define('MDL_PERFTOLOG', false);
define('MDL_PERFINC', false);

// Servers should define a default timezone in php.ini, but if they don't then make sure something is defined.
if (!function_exists('date_default_timezone_set') or !function_exists('date_default_timezone_get')) {
    echo("Timezone functions are not available.");
    die;
}
date_default_timezone_set(@date_default_timezone_get());

// make sure PHP errors are displayed - helps with diagnosing of problems
@error_reporting(E_ALL);
@ini_set('display_errors', '1');

// Check that PHP is of a sufficient version as soon as possible.
require_once(__DIR__.'/lib/phpminimumversionlib.php');
moodle_require_minimum_php_version();

// make sure iconv is available and actually works
if (!function_exists('iconv')) {
    // this should not happen, this must be very borked install
    echo 'Moodle requires the iconv PHP extension. Please install or enable the iconv extension.';
    die();
}

if (PHP_INT_SIZE > 4) {
    // most probably 64bit PHP - we need a lot more memory
    $minrequiredmemory = '70M';
} else {
    // 32bit PHP
    $minrequiredmemory = '40M';
}
// increase or decrease available memory - we need to make sure moodle
// installs even with low memory, otherwise developers would overlook
// sudden increases of memory needs ;-)
@ini_set('memory_limit', $minrequiredmemory);

/** Used by library scripts to check they are being called by Moodle */
define('MOODLE_INTERNAL', true);

require_once(__DIR__.'/lib/classes/component.php');
require_once(__DIR__.'/lib/installlib.php');

// TODO: add lang detection here if empty $_REQUEST['lang']

// distro specific customisation
$distro = null;
if (file_exists('install/distrolib.php')) {
    require_once('install/distrolib.php');
    if (function_exists('distro_get_config')) {
        $distro = distro_get_config();
    }
}

$config = new stdClass();
$config->lang = $lang;

if (!empty($_POST)) {
    $config->stage = (int)$_POST['stage'];

    if (isset($_POST['previous'])) {
        $config->stage--;
        if (INSTALL_DATABASETYPE and !empty($distro->dbtype)) {
            $config->stage--;
        }
        if ($config->stage == INSTALL_ENVIRONMENT or $config->stage == INSTALL_DOWNLOADLANG) {
            $config->stage--;
        }
    } else if (isset($_POST['next'])) {
        $config->stage++;
    }

    $config->dbtype   = trim($_POST['dbtype']);
    $config->dbhost   = trim($_POST['dbhost']);
    $config->dbuser   = trim($_POST['dbuser']);
    $config->dbpass   = trim($_POST['dbpass']);
    $config->dbname   = trim($_POST['dbname']);
    $config->prefix   = trim($_POST['prefix']);
    $config->dbport   = (int)trim($_POST['dbport']);
    $config->dbsocket = trim($_POST['dbsocket']);

    if ($config->dbport <= 0) {
        $config->dbport = '';
    }

    $config->admin    = empty($_POST['admin']) ? 'admin' : trim($_POST['admin']);

    $config->dataroot = trim($_POST['dataroot']);

} else {
    $config->stage    = INSTALL_WELCOME;

    $config->dbtype   = empty($distro->dbtype) ? '' : $distro->dbtype; // let distro skip dbtype selection
    $config->dbhost   = empty($distro->dbhost) ? 'localhost' : $distro->dbhost; // let distros set dbhost
    $config->dbuser   = empty($distro->dbuser) ? '' : $distro->dbuser; // let distros set dbuser
    $config->dbpass   = '';
    $config->dbname   = 'moodle';
    $config->prefix   = 'mdl_';
    $config->dbport   = empty($distro->dbport) ? '' : $distro->dbport;
    $config->dbsocket = empty($distro->dbsocket) ? '' : $distro->dbsocket;

    $config->admin    = 'admin';

    $config->dataroot = empty($distro->dataroot) ? null  : $distro->dataroot; // initialised later after including libs or by distro
}

// Fake some settings so that we can use selected functions from moodlelib.php, weblib.php and filelib.php.
global $CFG;
$CFG = new stdClass();
$CFG->lang                 = $config->lang;
$CFG->dirroot              = __DIR__;
$CFG->libdir               = "$CFG->dirroot/lib";
$CFG->wwwroot              = install_guess_wwwroot(); // can not be changed - ppl must use the real address when installing
$CFG->httpswwwroot         = $CFG->wwwroot;
$CFG->dataroot             = $config->dataroot;
$CFG->tempdir              = $CFG->dataroot.'/temp';
$CFG->backuptempdir        = $CFG->tempdir.'/backup';
$CFG->cachedir             = $CFG->dataroot.'/cache';
$CFG->localcachedir        = $CFG->dataroot.'/localcache';
$CFG->admin                = $config->admin;
$CFG->docroot              = 'https://docs.moodle.org';
$CFG->langotherroot        = $CFG->dataroot.'/lang';
$CFG->langlocalroot        = $CFG->dataroot.'/lang';
$CFG->directorypermissions = isset($distro->directorypermissions) ? $distro->directorypermissions : 00777; // let distros set dir permissions
$CFG->filepermissions      = ($CFG->directorypermissions & 0666);
$CFG->umaskpermissions     = (($CFG->directorypermissions & 0777) ^ 0777);
$CFG->running_installer    = true;
$CFG->early_install_lang   = true;
$CFG->ostype               = (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) ? 'WINDOWS' : 'UNIX';
$CFG->debug                = (E_ALL | E_STRICT);
$CFG->debugdisplay         = true;
$CFG->debugdeveloper       = true;

// Require all needed libs
require_once($CFG->libdir.'/setuplib.php');

// we need to make sure we have enough memory to load all libraries
$memlimit = @ini_get('memory_limit');
if (!empty($memlimit) and $memlimit != -1) {
    if (get_real_size($memlimit) < get_real_size($minrequiredmemory)) {
        // do NOT localise - lang strings would not work here and we CAN not move it to later place
        echo "Moodle requires at least {$minrequiredmemory}B of PHP memory.<br />";
        echo "Please contact server administrator to fix PHP.ini memory settings.";
        die;
    }
}

// Point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
// the problem is that we need specific version of quickforms and hacked excel files :-(.
ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));

// Register our classloader.
\core\component::register_autoloader();

// Continue with lib loading.
require_once($CFG->libdir.'/classes/text.php');
require_once($CFG->libdir.'/classes/string_manager.php');
require_once($CFG->libdir.'/classes/string_manager_install.php');
require_once($CFG->libdir.'/classes/string_manager_standard.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->libdir.'/outputlib.php');
require_once($CFG->libdir.'/dmllib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir .'/pagelib.php');
require_once($CFG->libdir.'/deprecatedlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/componentlib.class.php');

require('version.php');
$CFG->target_release = $release;

\core\session\manager::init_empty_session();
global $SESSION;
global $USER;

global $COURSE;
$COURSE = new stdClass();
$COURSE->id = 1;

global $SITE;
$SITE = $COURSE;
define('SITEID', 1);

$hint_dataroot = '';
$hint_admindir = '';
$hint_database = '';

// Are we in help mode?
if (isset($_GET['help'])) {
    install_print_help_page($_GET['help']);
}

//first time here? find out suitable dataroot
if (is_null($CFG->dataroot)) {
    $CFG->dataroot = __DIR__.'/../moodledata';

    $i = 0; //safety check - dirname might return some unexpected results
    while(is_dataroot_insecure()) {
        $parrent = dirname($CFG->dataroot);
        $i++;
        if ($parrent == '/' or $parrent == '.' or preg_match('/^[a-z]:\\\?$/i', $parrent) or ($i > 100)) {
            $CFG->dataroot = ''; //can not find secure location for dataroot
            break;
        }
        $CFG->dataroot = dirname($parrent).DIRECTORY_SEPARATOR.'moodledata';
    }
    $config->dataroot = $CFG->dataroot;
    $config->stage    = INSTALL_WELCOME;
}

// now let's do the stage work
if ($config->stage < INSTALL_WELCOME) {
    $config->stage = INSTALL_WELCOME;
}
if ($config->stage > INSTALL_SAVE) {
    $config->stage = INSTALL_SAVE;
}



if ($config->stage == INSTALL_SAVE) {
    $CFG->early_install_lang = false;

    $database = moodle_database::get_driver_instance($config->dbtype, 'native');
    if (!$database->driver_installed()) {
        $config->stage = INSTALL_DATABASETYPE;
    } else {
        if (function_exists('distro_pre_create_db')) { // Hook for distros needing to do something before DB creation
            $distro = distro_pre_create_db($database, $config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, $config->prefix, array('dbpersist'=>0, 'dbport'=>$config->dbport, 'dbsocket'=>$config->dbsocket), $distro);
        }
        $hint_database = install_db_validate($database, $config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, $config->prefix, array('dbpersist'=>0, 'dbport'=>$config->dbport, 'dbsocket'=>$config->dbsocket));

        if ($hint_database === '') {
            $configphp = install_generate_configphp($database, $CFG);

            umask(0137);
            if (($fh = @fopen($configfile, 'w')) !== false) {
                fwrite($fh, $configphp);
                fclose($fh);
            }

            if (file_exists($configfile)) {
                // config created, let's continue!
                redirect("$CFG->wwwroot/$config->admin/index.php?lang=$config->lang");
            }

            install_print_header($config, 'config.php',
                                          get_string('configurationcompletehead', 'install'),
                                          get_string('configurationcompletesub', 'install').get_string('configfilenotwritten', 'install'), 'alert-error');
            echo '<div class="configphp"><pre>';
            echo p($configphp);
            echo '</pre></div>';

            install_print_footer($config);
            die;

        } else {
            $config->stage = INSTALL_DATABASE;
        }
    }
}



if ($config->stage == INSTALL_DOWNLOADLANG) {
    if (empty($CFG->dataroot)) {
        $config->stage = INSTALL_PATHS;

    } else if (is_dataroot_insecure()) {
        $hint_dataroot = get_string('pathsunsecuredataroot', 'install');
        $config->stage = INSTALL_PATHS;

    } else if (!file_exists($CFG->dataroot)) {
        $a = new stdClass();
        $a->parent = dirname($CFG->dataroot);
        $a->dataroot = $CFG->dataroot;
        if (!is_writable($a->parent)) {
            $hint_dataroot = get_string('pathsroparentdataroot', 'install', $a);
            $config->stage = INSTALL_PATHS;
        } else {
            if (!install_init_dataroot($CFG->dataroot, $CFG->directorypermissions)) {
                $hint_dataroot = get_string('pathserrcreatedataroot', 'install', $a);
                $config->stage = INSTALL_PATHS;
            }
        }

    } else if (!install_init_dataroot($CFG->dataroot, $CFG->directorypermissions)) {
        $hint_dataroot = get_string('pathserrcreatedataroot', 'install', array('dataroot' => $CFG->dataroot));
        $config->stage = INSTALL_PATHS;
    }

    if (empty($hint_dataroot) and !is_writable($CFG->dataroot)) {
        $hint_dataroot = get_string('pathsrodataroot', 'install');
        $config->stage = INSTALL_PATHS;
    }

    if ($config->admin === '' or !file_exists($CFG->dirroot.'/'.$config->admin.'/environment.xml')) {
        $hint_admindir = get_string('pathswrongadmindir', 'install');
        $config->stage = INSTALL_PATHS;
    }
}



if ($config->stage == INSTALL_DOWNLOADLANG) {
    // no need to download anything if en lang selected
    if ($CFG->lang == 'en') {
        $config->stage = INSTALL_DATABASETYPE;
    }
}



if ($config->stage == INSTALL_DATABASETYPE) {
    // skip db selection if distro package supports only one db
    if (!empty($distro->dbtype)) {
        $config->stage = INSTALL_DATABASE;
    }
}


if ($config->stage == INSTALL_DOWNLOADLANG) {
    $downloaderror = '';

    // download and install required lang packs, the lang dir has already been created in install_init_dataroot
    $installer = new lang_installer($CFG->lang);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        if ($langstatus === lang_installer::RESULT_DOWNLOADERROR) {
            $a       = new stdClass();
            $a->url  = $installer->lang_pack_url($langcode);
            $a->dest = $CFG->dataroot.'/lang';
            $downloaderror = get_string('remotedownloaderror', 'error', $a);
        }
    }

    if ($downloaderror !== '') {
        install_print_header($config, get_string('language'), get_string('langdownloaderror', 'install', $CFG->lang), $downloaderror);
        install_print_footer($config);
        die;
    } else {
        if (empty($distro->dbtype)) {
            $config->stage = INSTALL_DATABASETYPE;
        } else {
            $config->stage = INSTALL_DATABASE;
        }
    }

    // switch the string_manager instance to stop using install/lang/
    $CFG->early_install_lang = false;
    $CFG->langotherroot      = $CFG->dataroot.'/lang';
    $CFG->langlocalroot      = $CFG->dataroot.'/lang';
    get_string_manager(true);
}


if ($config->stage == INSTALL_DATABASE) {
    $CFG->early_install_lang = false;

    $database = moodle_database::get_driver_instance($config->dbtype, 'native');

    $sub = '<h3>'.$database->get_name().'</h3>'.$database->get_configuration_help();

    install_print_header($config, get_string('database', 'install'), get_string('databasehead', 'install'), $sub);

    $strdbhost   = get_string('databasehost', 'install');
    $strdbname   = get_string('databasename', 'install');
    $strdbuser   = get_string('databaseuser', 'install');
    $strdbpass   = get_string('databasepass', 'install');
    $strprefix   = get_string('dbprefix', 'install');
    $strdbport   = get_string('databaseport', 'install');
    $strdbsocket = get_string('databasesocket', 'install');

    echo '<div class="row mb-4">';

    $disabled = empty($distro->dbhost) ? '' : 'disabled="disabled';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dbhost">'.$strdbhost.'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dbhost" name="dbhost" '.$disabled.' type="text" class="form-control text-ltr" value="'.s($config->dbhost).'" size="50" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dbname">'.$strdbname.'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dbname" name="dbname" type="text" class="form-control text-ltr" value="'.s($config->dbname).'" size="50" /></div>';
    echo '</div>';

    $disabled = empty($distro->dbuser) ? '' : 'disabled="disabled';
    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dbuser">'.$strdbuser.'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dbuser" name="dbuser" '.$disabled.' type="text" class="form-control text-ltr" value="'.s($config->dbuser).'" size="50" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dbpass">'.$strdbpass.'</label></div>';
    // no password field here, the password may be visible in config.php if we can not write it to disk
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dbpass" name="dbpass" type="text" class="form-control text-ltr" value="'.s($config->dbpass).'" size="50" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_prefix">'.$strprefix.'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_prefix" name="prefix" type="text" class="form-control text-ltr" value="'.s($config->prefix).'" size="10" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_prefix">'.$strdbport.'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dbport" name="dbport" type="text" class="form-control text-ltr" value="'.s($config->dbport).'" size="10" /></div>';
    echo '</div>';

    if (!(stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin'))) {
        echo '<div class="row mb-4">';
        echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dbsocket">'.$strdbsocket.'</label></div>';
        echo '<div class="col-md-9" data-fieldtype="text">';
        echo '<input id="id_dbsocket" name="dbsocket" type="text" class="form-control text-ltr" value="'.s($config->dbsocket).'" size="50" /></div>';
        echo '</div>';
    }

    if ($hint_database !== '') {
        echo '<div class="alert alert-danger">'.$hint_database.'</div>';
    }

    install_print_footer($config);
    die;
}


if ($config->stage == INSTALL_DATABASETYPE) {
    $CFG->early_install_lang = false;

    // Finally ask for DB type
    install_print_header($config, get_string('database', 'install'),
                                  get_string('databasetypehead', 'install'),
                                  get_string('databasetypesub', 'install'));

    $databases = array('mysqli' => moodle_database::get_driver_instance('mysqli', 'native'),
                       'auroramysql' => moodle_database::get_driver_instance('auroramysql', 'native'),
                       'mariadb'=> moodle_database::get_driver_instance('mariadb', 'native'),
                       'pgsql'  => moodle_database::get_driver_instance('pgsql',  'native'),
                       'oci'    => moodle_database::get_driver_instance('oci',    'native'),
                       'sqlsrv' => moodle_database::get_driver_instance('sqlsrv', 'native'), // MS SQL*Server PHP driver
                      );

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="dbtype">'.get_string('dbtype', 'install').'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="select">';
    echo '<select class="form-control" id="dbtype" name="dbtype">';
    $disabled = array();
    $options = array();
    foreach ($databases as $type=>$database) {
        if ($database->driver_installed() !== true) {
            $disabled[$type] = $database;
            continue;
        }
        echo '<option value="'.s($type).'">'.$database->get_name().'</option>';
    }
    if ($disabled) {
        echo '<optgroup label="'.s(get_string('notavailable')).'">';
        foreach ($disabled as $type=>$database) {
            echo '<option value="'.s($type).'" class="notavailable">'.$database->get_name().'</option>';
        }
        echo '</optgroup>';
    }
    echo '</select></div></div>';

    install_print_footer($config);
    die;
}



if ($config->stage == INSTALL_ENVIRONMENT or $config->stage == INSTALL_PATHS) {
    $curl_fail    = ($lang !== 'en' and !extension_loaded('curl')); // needed for lang pack download
    $zip_fail     = ($lang !== 'en' and !extension_loaded('zip'));  // needed for lang pack download

    if ($curl_fail or $zip_fail) {
        $config->stage = INSTALL_ENVIRONMENT;

        install_print_header($config, get_string('environmenthead', 'install'),
                                      get_string('errorsinenvironment', 'install'),
                                      get_string('environmentsub2', 'install'));

        echo '<div id="envresult"><dl>';
        if ($curl_fail) {
            echo '<dt>'.get_string('phpextension', 'install', 'cURL').'</dt><dd>'.get_string('environmentrequireinstall', 'admin').'</dd>';
        }
        if ($zip_fail) {
            echo '<dt>'.get_string('phpextension', 'install', 'Zip').'</dt><dd>'.get_string('environmentrequireinstall', 'admin').'</dd>';
        }
        echo '</dl></div>';

        install_print_footer($config, true);
        die;

    } else {
        $config->stage = INSTALL_PATHS;
    }
}



if ($config->stage == INSTALL_PATHS) {
    $paths = array('wwwroot'  => get_string('wwwroot', 'install'),
                   'dirroot'  => get_string('dirroot', 'install'),
                   'dataroot' => get_string('dataroot', 'install'));

    $sub = '<dl>';
    foreach ($paths as $path=>$name) {
        $sub .= '<dt>'.$name.'</dt><dd>'.get_string('pathssub'.$path, 'install').'</dd>';
    }
    if (!file_exists("$CFG->dirroot/admin/environment.xml")) {
        $sub .= '<dt>'.get_string('admindirname', 'install').'</dt><dd>'.get_string('pathssubadmindir', 'install').'</dd>';
    }
    $sub .= '</dl>';

    install_print_header($config, get_string('paths', 'install'), get_string('pathshead', 'install'), $sub);

    $strwwwroot      = get_string('wwwroot', 'install');
    $strdirroot      = get_string('dirroot', 'install');
    $strdataroot     = get_string('dataroot', 'install');
    $stradmindirname = get_string('admindirname', 'install');

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_wwwroot">'.$paths['wwwroot'].'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_wwwroot" name="wwwroot" type="text" class="form-control text-ltr" value="'.s($CFG->wwwroot).'" disabled="disabled" size="70" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dirroot">'.$paths['dirroot'].'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dirroot" name="dirroot" type="text" class="form-control text-ltr" value="'.s($CFG->dirroot).'" disabled="disabled" size="70" /></div>';
    echo '</div>';

    echo '<div class="row mb-4">';
    echo '<div class="col-md-3 text-md-end pt-1"><label for="id_dataroot">'.$paths['dataroot'].'</label></div>';
    echo '<div class="col-md-9" data-fieldtype="text">';
    echo '<input id="id_dataroot" name="dataroot" type="text" class="form-control text-ltr" value="'.s($config->dataroot).'" size="70" /></div>';
    echo '</div>';
    if ($hint_dataroot !== '') {
        echo '<div class="alert alert-danger">'.$hint_dataroot.'</div>';
    }


    if (!file_exists("$CFG->dirroot/admin/environment.xml")) {
        echo '<div class="row mb-4">';
        echo '<div class="col-md-3 text-md-end pt-1"><label for="id_admin">'.$paths['admindir'].'</label></div>';
        echo '<div class="col-md-9" data-fieldtype="text">';
        echo '<input id="id_admin" name="admin" type="text" class="form-control text-ltr" value="'.s($config->admin).'" size="10" /></div>';
        echo '</div>';
        if ($hint_admindir !== '') {
            echo '<div class="alert alert-danger">'.$hint_admindir.'</div>';
        }
    }

    install_print_footer($config);
    die;
}



$config->stage = INSTALL_WELCOME;

if ($distro) {
    ob_start();
    include('install/distribution.html');
    $sub = ob_get_clean();

    install_print_header($config, get_string('language'),
                                  get_string('chooselanguagehead', 'install'),
                                  $sub, 'alert-success');

} else {
    install_print_header($config, get_string('language'),
                                  get_string('chooselanguagehead', 'install'),
                                  get_string('chooselanguagesub', 'install'));
}

$languages = get_string_manager()->get_list_of_translations();
echo '<div class="row mb-4">';
echo '<div class="col-md-3 text-md-end pt-1"><label for="langselect">'.get_string('language').'</label></div>';
echo '<div class="col-md-9" data-fieldtype="select">';
echo '<select id="langselect" class="form-control" name="lang" onchange="this.form.submit()">';
foreach ($languages as $name=>$value) {
    $selected = ($name == $CFG->lang) ? 'selected="selected"' : '';
    echo '<option value="'.s($name).'" '.$selected.'>'.$value.'</option>';
}
echo '</select></div>';
echo '</div>';

install_print_footer($config);
die;
