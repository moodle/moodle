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
 * Functions to support installation process
 *
 * @package    core
 * @subpackage install
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** INSTALL_WELCOME = 0 */
define('INSTALL_WELCOME',       0);
/** INSTALL_ENVIRONMENT = 1 */
define('INSTALL_ENVIRONMENT',   1);
/** INSTALL_PATHS = 2 */
define('INSTALL_PATHS',         2);
/** INSTALL_DOWNLOADLANG = 3 */
define('INSTALL_DOWNLOADLANG',  3);
/** INSTALL_DATABASETYPE = 4 */
define('INSTALL_DATABASETYPE',  4);
/** INSTALL_DATABASE = 5 */
define('INSTALL_DATABASE',      5);
/** INSTALL_SAVE = 6 */
define('INSTALL_SAVE',          6);

/**
 * Tries to detect the right www root setting.
 * @return string detected www root
 */
function install_guess_wwwroot() {
    $wwwroot = '';
    if (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
        $wwwroot .= 'http://';
    } else {
        $wwwroot .= 'https://';
    }
    $hostport = explode(':', $_SERVER['HTTP_HOST']);
    $wwwroot .= reset($hostport);
    if ($_SERVER['SERVER_PORT'] != 80 and $_SERVER['SERVER_PORT'] != '443') {
        $wwwroot .= ':'.$_SERVER['SERVER_PORT'];
    }
    $wwwroot .= $_SERVER['SCRIPT_NAME'];

    list($wwwroot, $xtra) = explode('/install.php', $wwwroot);

    return $wwwroot;
}

/**
 * Copy of @see{ini_get_bool()}
 * @param string $ini_get_arg
 * @return bool
 */
function install_ini_get_bool($ini_get_arg) {
    $temp = ini_get($ini_get_arg);

    if ($temp == '1' or strtolower($temp) == 'on') {
        return true;
    }
    return false;
}

/**
 * Creates dataroot if not exists yet,
 * makes sure it is writable, add lang directory
 * and add .htaccess just in case it works.
 *
 * @param string $dataroot full path to dataroot
 * @param int $dirpermissions
 * @return bool success
 */
function install_init_dataroot($dataroot, $dirpermissions) {
    if (file_exists($dataroot) and !is_dir($dataroot)) {
        // file with the same name exists
        return false;
    }

    umask(0000); // $CFG->umaskpermissions is not set yet.
    if (!file_exists($dataroot)) {
        if (!mkdir($dataroot, $dirpermissions, true)) {
            // most probably this does not work, but anyway
            return false;
        }
    }
    @chmod($dataroot, $dirpermissions);

    if (!is_writable($dataroot)) {
        return false; // we can not continue
    }

    // create the directory for $CFG->tempdir
    if (!is_dir("$dataroot/temp")) {
        if (!mkdir("$dataroot/temp", $dirpermissions, true)) {
            return false;
        }
    }
    if (!is_writable("$dataroot/temp")) {
        return false; // we can not continue
    }

    // create the directory for $CFG->cachedir
    if (!is_dir("$dataroot/cache")) {
        if (!mkdir("$dataroot/cache", $dirpermissions, true)) {
            return false;
        }
    }
    if (!is_writable("$dataroot/cache")) {
        return false; // we can not continue
    }

    // create the directory for $CFG->langotherroot
    if (!is_dir("$dataroot/lang")) {
        if (!mkdir("$dataroot/lang", $dirpermissions, true)) {
            return false;
        }
    }
    if (!is_writable("$dataroot/lang")) {
        return false; // we can not continue
    }

    // finally just in case some broken .htaccess that prevents access just in case it is allowed
    if (!file_exists("$dataroot/.htaccess")) {
        if ($handle = fopen("$dataroot/.htaccess", 'w')) {
            fwrite($handle, "deny from all\r\nAllowOverride None\r\nNote: this file is broken intentionally, we do not want anybody to undo it in subdirectory!\r\n");
            fclose($handle);
        } else {
            return false;
        }
    }

    return true;
}

/**
 * Print help button
 * @param string $url
 * @param string $titel
 * @return void
 */
function install_helpbutton($url, $title='') {
    if ($title == '') {
        $title = get_string('help');
    }
    echo "<a href=\"javascript:void(0)\" ";
    echo "onclick=\"return window.open('$url','Help','menubar=0,location=0,scrollbars,resizable,width=500,height=400')\"";
    echo ">";
    echo "<img src=\"pix/help.gif\" class=\"iconhelp\" alt=\"$title\" title=\"$title\"/>";
    echo "</a>\n";
}

/**
 * This is in function because we want the /install.php to parse in PHP4
 *
 * @param object $database
 * @param string $dbhsot
 * @param string $dbuser
 * @param string $dbpass
 * @param string $dbname
 * @param string $prefix
 * @param mixed $dboptions
 * @return string
 */
function install_db_validate($database, $dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions) {
    try {
        try {
            $database->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        } catch (moodle_exception $e) {
            // let's try to create new database
            if ($database->create_database($dbhost, $dbuser, $dbpass, $dbname, $dboptions)) {
                $database->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
            } else {
                throw $e;
            }
        }
        return '';
    } catch (dml_exception $ex) {
        $stringmanager = get_string_manager();
        $errorstring = $ex->errorcode.'oninstall';
        $legacystring = $ex->errorcode;
        if ($stringmanager->string_exists($errorstring, $ex->module)) {
            // By using a different string id from the error code we are separating exception handling and output.
            $returnstring = $stringmanager->get_string($errorstring, $ex->module, $ex->a);
            if ($ex->debuginfo) {
                $returnstring .= '<br />'.$ex->debuginfo;
            }

            return $returnstring;
        } else if ($stringmanager->string_exists($legacystring, $ex->module)) {
            // There are some DML exceptions that may be thrown here as well as during normal operation.
            // If we have a translated message already we still want to serve it here.
            // However it is not the preferred way.
            $returnstring = $stringmanager->get_string($legacystring, $ex->module, $ex->a);
            if ($ex->debuginfo) {
                $returnstring .= '<br />'.$ex->debuginfo;
            }

            return $returnstring;
        }
        // No specific translation. Deliver a generic error message.
        return $stringmanager->get_string('dmlexceptiononinstall', 'error', $ex);
    }
}

/**
 * Returns content of config.php file.
 *
 * Uses PHP_EOL for generating proper end of lines for the given platform.
 *
 * @param moodle_database $database database instance
 * @param object $cfg copy of $CFG
 * @return string
 */
function install_generate_configphp($database, $cfg) {
    $configphp = '<?php  // Moodle configuration file' . PHP_EOL . PHP_EOL;

    $configphp .= 'unset($CFG);' . PHP_EOL;
    $configphp .= 'global $CFG;' . PHP_EOL;
    $configphp .= '$CFG = new stdClass();' . PHP_EOL . PHP_EOL; // prevent PHP5 strict warnings

    $dbconfig = $database->export_dbconfig();

    foreach ($dbconfig as $key=>$value) {
        $key = str_pad($key, 9);
        $configphp .= '$CFG->'.$key.' = '.var_export($value, true) . ';' . PHP_EOL;
    }
    $configphp .= PHP_EOL;

    $configphp .= '$CFG->wwwroot   = '.var_export($cfg->wwwroot, true) . ';' . PHP_EOL ;

    $configphp .= '$CFG->dataroot  = '.var_export($cfg->dataroot, true) . ';' . PHP_EOL;

    $configphp .= '$CFG->admin     = '.var_export($cfg->admin, true) . ';' . PHP_EOL . PHP_EOL;

    if (empty($cfg->directorypermissions)) {
        $chmod = '02777';
    } else {
        $chmod = '0' . decoct($cfg->directorypermissions);
    }
    $configphp .= '$CFG->directorypermissions = ' . $chmod . ';' . PHP_EOL . PHP_EOL;

    if (isset($cfg->upgradekey) and $cfg->upgradekey !== '') {
        $configphp .= '$CFG->upgradekey = ' . var_export($cfg->upgradekey, true) . ';' . PHP_EOL . PHP_EOL;
    }

    $configphp .= 'require_once(__DIR__ . \'/lib/setup.php\');' . PHP_EOL . PHP_EOL;
    $configphp .= '// There is no php closing tag in this file,' . PHP_EOL;
    $configphp .= '// it is intentional because it prevents trailing whitespace problems!' . PHP_EOL;

    return $configphp;
}

/**
 * Prints complete help page used during installation.
 * Does not return.
 *
 * @global object
 * @param string $help
 */
function install_print_help_page($help) {
    global $CFG, $OUTPUT; //TODO: MUST NOT USE $OUTPUT HERE!!!

    @header('Content-Type: text/html; charset=UTF-8');
    @header('X-UA-Compatible: IE=edge');
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html dir="'.(right_to_left() ? 'rtl' : 'ltr').'">
          <head>
          <link rel="shortcut icon" href="theme/clean/pix/favicon.ico" />
          <link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/install/css.php" />
          <title>'.get_string('installation','install').'</title>
          <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
          </head><body>';
    switch ($help) {
        case 'phpversionhelp':
            print_string($help, 'install', phpversion());
            break;
        case 'memorylimithelp':
            print_string($help, 'install', @ini_get('memory_limit'));
            break;
        default:
            print_string($help, 'install');
    }
    echo $OUTPUT->close_window_button(); //TODO: MUST NOT USE $OUTPUT HERE!!!
    echo '</body></html>';
    die;
}

/**
 * Prints installation page header, we can no use weblib yet in installer.
 *
 * @global object
 * @param array $config
 * @param string $stagename
 * @param string $heading
 * @param string $stagetext
 * @param string $stageclass
 * @return void
 */
function install_print_header($config, $stagename, $heading, $stagetext, $stageclass = "alert-info") {
    global $CFG;

    @header('Content-Type: text/html; charset=UTF-8');
    @header('X-UA-Compatible: IE=edge');
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html dir="'.(right_to_left() ? 'rtl' : 'ltr').'">
          <head>
          <link rel="shortcut icon" href="theme/clean/pix/favicon.ico" />';

    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/install/css.php" />
          <title>'.get_string('installation','install').' - Moodle '.$CFG->target_release.'</title>
          <meta name="robots" content="noindex">
          <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
          <meta http-equiv="pragma" content="no-cache" />
          <meta http-equiv="expires" content="0" />';

    echo '</head><body class="notloggedin">
            <div id="page" class="mt-0 container stage'.$config->stage.'">
                <div id="page-header">
                    <div id="header" class=" clearfix">
                        <h1 class="headermain">'.get_string('installation','install').'</h1>
                        <div class="headermenu">&nbsp;</div>
                    </div>
                    <div class="bg-light p-3 mb-3"><h3 class="m-0">'.$stagename.'</h3></div>
                </div>
          <!-- END OF HEADER -->
          <div id="installdiv">';

    echo '<h2>'.$heading.'</h2>';

    if ($stagetext !== '') {
        echo '<div class="alert ' . $stageclass . '">';
        echo $stagetext;
        echo '</div>';
    }
    // main
    echo '<form id="installform" method="post" action="install.php"><fieldset>';
    foreach ($config as $name=>$value) {
        echo '<input type="hidden" name="'.$name.'" value="'.s($value).'" />';
    }
}

/**
 * Prints installation page header, we can no use weblib yet in isntaller.
 *
 * @global object
 * @param array $config
 * @param bool $reload print reload button instead of next
 * @return void
 */
function install_print_footer($config, $reload=false) {
    global $CFG;

    if ($config->stage > INSTALL_WELCOME) {
        $first = '<input type="submit" id="previousbutton" class="btn btn-secondary flex-grow-0 ml-auto" name="previous" value="&laquo; '.s(get_string('previous')).'" />';
    } else {
        $first = '<input type="submit" id="previousbutton" class="btn btn-secondary flex-grow-0  ml-auto" name="next" value="'.s(get_string('reload')).'" />';
        $first .= '<script type="text/javascript">
//<![CDATA[
    var first = document.getElementById("previousbutton");
    first.style.visibility = "hidden";
//]]>
</script>
';
    }

    if ($reload) {
        $next = '<input type="submit" id="nextbutton" class="btn btn-primary ml-1 flex-grow-0 mr-auto" name="next" value="'.s(get_string('reload')).'" />';
    } else {
        $next = '<input type="submit" id="nextbutton" class="btn btn-primary ml-1 flex-grow-0 mr-auto" name="next" value="'.s(get_string('next')).' &raquo;" />';
    }

    echo '</fieldset><div id="nav_buttons" class="mb-3 btn-group w-100 flex-row-reverse">'.$next.$first.'</div>';

    $homelink  = '<div class="sitelink">'.
       '<a title="Moodle '. $CFG->target_release .'" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">'.
       '<img src="pix/moodlelogo.png" alt="'.get_string('moodlelogo').'" /></a></div>';

    echo '</form></div>';
    echo '<div id="page-footer">'.$homelink.'</div>';
    echo '</div></body></html>';
}

/**
 * Install Moodle DB,
 * config.php must exist, there must not be any tables in db yet.
 *
 * @param array $options adminpass is mandatory
 * @param bool $interactive
 * @return void
 */
function install_cli_database(array $options, $interactive) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/environmentlib.php');
    require_once($CFG->libdir.'/upgradelib.php');

    // show as much debug as possible
    @error_reporting(E_ALL | E_STRICT);
    @ini_set('display_errors', '1');
    $CFG->debug = (E_ALL | E_STRICT);
    $CFG->debugdisplay = true;
    $CFG->debugdeveloper = true;

    $CFG->version = '';
    $CFG->release = '';
    $CFG->branch = '';

    $version = null;
    $release = null;
    $branch = null;

    // read $version and $release
    require($CFG->dirroot.'/version.php');

    if ($DB->get_tables() ) {
        cli_error(get_string('clitablesexist', 'install'));
    }

    if (empty($options['adminpass'])) {
        cli_error('Missing required admin password');
    }

    // test environment first
    list($envstatus, $environment_results) = check_moodle_environment(normalize_version($release), ENV_SELECT_RELEASE);
    if (!$envstatus) {
        $errors = environment_get_errors($environment_results);
        cli_heading(get_string('environment', 'admin'));
        foreach ($errors as $error) {
            list($info, $report) = $error;
            echo "!! $info !!\n$report\n\n";
        }
        exit(1);
    }

    if (!$DB->setup_is_unicodedb()) {
        if (!$DB->change_db_encoding()) {
            // If could not convert successfully, throw error, and prevent installation
            cli_error(get_string('unicoderequired', 'admin'));
        }
    }

    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databasesetup'));
    }

    // install core
    install_core($version, true);
    set_config('release', $release);
    set_config('branch', $branch);

    if (PHPUNIT_TEST) {
        // mark as test database as soon as possible
        set_config('phpunittest', 'na');
    }

    // install all plugins types, local, etc.
    upgrade_noncore(true);

    // set up admin user password
    $DB->set_field('user', 'password', hash_internal_user_password($options['adminpass']), array('username' => 'admin'));

    // Set the admin email address if specified.
    if (isset($options['adminemail'])) {
        $DB->set_field('user', 'email', $options['adminemail'], array('username' => 'admin'));
    }

    // rename admin username if needed
    if (isset($options['adminuser']) and $options['adminuser'] !== 'admin' and $options['adminuser'] !== 'guest') {
        $DB->set_field('user', 'username', $options['adminuser'], array('username' => 'admin'));
    }

    // indicate that this site is fully configured
    set_config('rolesactive', 1);
    upgrade_finished();

    // log in as admin - we need do anything when applying defaults
    \core\session\manager::set_user(get_admin());

    // Apply all default settings.
    admin_apply_default_settings(NULL, true);
    set_config('registerauth', '');

    // set the site name
    if (isset($options['shortname']) and $options['shortname'] !== '') {
        $DB->set_field('course', 'shortname', $options['shortname'], array('format' => 'site'));
    }
    if (isset($options['fullname']) and $options['fullname'] !== '') {
        $DB->set_field('course', 'fullname', $options['fullname'], array('format' => 'site'));
    }
    if (isset($options['summary'])) {
        $DB->set_field('course', 'summary', $options['summary'], array('format' => 'site'));
    }

    // Redirect to site registration on first login.
    set_config('registrationpending', 1);
}
