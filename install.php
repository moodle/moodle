<?php /// $Id$
      /// install.php - helps admin user to create a config.php file

/// If config.php exists already then we are not needed.

if (file_exists('./config.php')) {
    header('Location: index.php');
    die;
} else {
    $configfile = './config.php';
}

///==========================================================================//
/// We are doing this in stages
define ('WELCOME',            0); /// 0. Welcome and language settings
define ('COMPATIBILITY',      1); /// 1. Compatibility
define ('DIRECTORY',          2); /// 2. Directory settings
define ('DATABASE',           3); /// 2. Database settings
define ('ADMIN',              4); /// 4. Administration directory name
define ('ENVIRONMENT',        5); /// 5. Administration directory name
define ('DOWNLOADLANG',       6); /// 6. Load complete lang from download.moodle.org
define ('SAVE',               7); /// 7. Save or display the settings
define ('REDIRECT',           8); /// 8. Redirect to index.php
///==========================================================================//


/// This has to be defined to avoid a notice in current_language()
define('SITEID', 0);

/// Begin the session as we are holding all information in a session
/// variable until the end.

session_name('MoodleSession');
@session_start();

/// make sure PHP errors are displayed to help diagnose problems
@error_reporting(1023); //E_ALL not used because we do not want strict notices in PHP5 yet
@ini_set('display_errors', '1');

if (! isset($_SESSION['INSTALL'])) {
    $_SESSION['INSTALL'] = array();
}

$INSTALL = &$_SESSION['INSTALL'];   // Makes it easier to reference

/// detect if install was attempted from diferent directory, if yes reset session to prevent errors,
/// dirroot location now fixed in installer
if (!empty($INSTALL['dirroot']) and $INSTALL['dirroot'] != dirname(__FILE__)) {
    $_SESSION['INSTALL'] = array();
}

/// If it's our first time through this script then we need to set some default values

if ( empty($INSTALL['language']) and empty($_POST['language']) ) {

    /// set defaults
    $INSTALL['language']        = 'en_utf8';

    $INSTALL['dbhost']          = 'localhost';
    $INSTALL['dbuser']          = '';
    $INSTALL['dbpass']          = '';
    $INSTALL['dbtype']          = 'mysqli_adodb';
    $INSTALL['dbname']          = 'moodle';
    $INSTALL['prefix']          = 'mdl_';

    $INSTALL['downloadlangpack']       = false;
    $INSTALL['showdownloadlangpack']   = true;
    $INSTALL['downloadlangpackerror']  = '';

/// To be used by the Installer
    $INSTALL['wwwroot']         = '';
    $INSTALL['dirroot']         = dirname(__FILE__);
    $INSTALL['dataroot']        = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'moodledata';

/// To be configured in the Installer
    $INSTALL['wwwrootform']         = '';
    $INSTALL['dirrootform']         = dirname(__FILE__);

    $INSTALL['admindirname']    = 'admin';

    $INSTALL['stage'] = WELCOME;
}

//==========================================================================//

/// Set the page to Unicode always

header('Content-Type: text/html; charset=UTF-8');

/// Was data submitted?

if (isset($_POST['stage'])) {

    /// Get the stage for which the form was set and the next stage we are going to

    $gpc = ini_get('magic_quotes_gpc');
    $gpc = ($gpc == '1' or strtolower($gpc) == 'on');

    /// Store any posted data
    foreach ($_POST as $setting=>$value) {
        if ($gpc) {
            $value = stripslashes($value);
        }

        $INSTALL[$setting] = $value;
    }

    if ( $goforward = (! empty( $_POST['next'] )) ) {
        $nextstage = $_POST['stage'] + 1;
    } else if (! empty( $_POST['prev'])) {
        $nextstage = $_POST['stage'] - 1;
        $INSTALL['stage'] = $_POST['stage'] - 1;
    } else if (! empty( $_POST['same'] )) {
        $nextstage = $_POST['stage'];
    }

    $nextstage = (int)$nextstage;

    if ($nextstage < 0) {
        $nextstage = WELCOME;
    }


} else {

    $goforward = true;
    $nextstage = WELCOME;

}

//==========================================================================//

/// Fake some settings so that we can use selected functions from moodlelib.php and weblib.php

$SESSION->lang = (!empty($_POST['language'])) ? $_POST['language'] : $INSTALL['language'];
$CFG->dirroot = $INSTALL['dirroot'];
$CFG->libdir = $INSTALL['dirroot'].'/lib';
$CFG->dataroot = $INSTALL['dataroot'];
$CFG->admin = $INSTALL['admindirname'];
$CFG->directorypermissions = 00777;
$CFG->running_installer = true;
$CFG->docroot = 'http://docs.moodle.org';
$CFG->httpswwwroot = $INSTALL['wwwrootform']; // Needed by doc_link() in Server Checks page.
$COURSE->id = 0;

/// Include some moodle libraries

require_once($CFG->libdir.'/setuplib.php');
require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->libdir.'/deprecatedlib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/componentlib.class.php');
require_once($CFG->dirroot.'/version.php');

/// Set version and release
$INSTALL['version'] = $version;
$INSTALL['release'] = $release;
$CFG->release = $release; // needed for environment checks

/// list all supported drivers - unsupported must be installed manually ;-)
$supported = array (
    'mysqli_adodb',
    'mysql_adodb',
    'postgres7_adodb',
    'mssql_n_adodb',
    'mssql_adodb',
    'odbc_mssql_adodb',
    'oci8po_adodb',
    'sqlite3_pdo',
);
$databases = array ();
foreach($supported as $driver) {
    $classname = $driver.'_moodle_database';
    require_once ("$CFG->libdir/dml/$classname.php");
    $databases[$driver] = new $classname ();
}

/// guess the www root
if ($INSTALL['wwwroot'] == '') {
    list($INSTALL['wwwroot'], $xtra) = explode('/install.php', qualified_me());
    $INSTALL['wwwrootform'] = $INSTALL['wwwroot'];

    // now try to guess the correct dataroot not accessible via web
    $CFG->wwwroot = $INSTALL['wwwroot'];
    $i = 0; //safety check - dirname might return some unexpected results
    while(is_dataroot_insecure()) {
        $parrent = dirname($CFG->dataroot);
        $i++;
        if ($parrent == '/' or $parrent == '.' or preg_match('/^[a-z]:\\\?$/i', $parrent) or ($i > 100)) {
            $CFG->dataroot = ''; //can not find secure location for dataroot
            break;
        }
        $CFG->dataroot = dirname($parrent).'/moodledata';
    }
        $INSTALL['dataroot'] = $CFG->dataroot;
}

$headstagetext = array(WELCOME       => get_string('chooselanguagehead', 'install'),
                       COMPATIBILITY => get_string('compatibilitysettingshead', 'install'),
                       DIRECTORY     => get_string('directorysettingshead', 'install'),
                       DATABASE      => get_string('databasesettingshead', 'install'),
                       ADMIN         => get_string('admindirsettinghead', 'install'),
                       ENVIRONMENT   => get_string('environmenthead', 'install'),
                       DOWNLOADLANG  => get_string('downloadlanguagehead', 'install'),
                       SAVE          => get_string('configurationcompletehead', 'install')
                        );

$substagetext = array(WELCOME       => get_string('chooselanguagesub', 'install'),
                      COMPATIBILITY => get_string('compatibilitysettingssub', 'install'),
                      DIRECTORY     => get_string('directorysettingssub', 'install'),
                      DATABASE      => get_string('databasesettingssub', 'install'),
                      ADMIN         => get_string('admindirsettingsub', 'install'),
                      ENVIRONMENT   => get_string('environmentsub', 'install'),
                      DOWNLOADLANG  => get_string('downloadlanguagesub', 'install'),
                      SAVE          => get_string('configurationcompletesub', 'install')
                       );



//==========================================================================//

/// Are we in help mode?

if (isset($_GET['help'])) {
    $nextstage = -1;
}



//==========================================================================//

/// Are we in config download mode?

if (isset($_GET['download'])) {
    header("Content-Type: application/x-forcedownload\n");
    header("Content-Disposition: attachment; filename=\"config.php\"");
    echo $INSTALL['config'];
    exit;
}





//==========================================================================//

/// Check the directory settings

if ($INSTALL['stage'] == DIRECTORY) {

    error_reporting(0);

    /// check wwwroot
    if (ini_get('allow_url_fopen') && false) { /// This was not reliable
        if (($fh = @fopen($INSTALL['wwwrootform'].'/install.php', 'r')) === false) {
            $errormsg .= get_string('wwwrooterror', 'install').'<br />';
            $INSTALL['wwwrootform'] = $INSTALL['wwwroot'];
            fclose($fh);
        }
    }

    /// check dirroot
    if (($fh = @fopen($INSTALL['dirrootform'].'/install.php', 'r')) === false) {
        $errormsg .= get_string('dirrooterror', 'install').'<br />';
        $INSTALL['dirrootform'] = $INSTALL['dirroot'];
        fclose($fh);
    }

    /// check dataroot
    $CFG->dataroot = $INSTALL['dataroot'];
    if (make_upload_directory('sessions', false) === false) {
        $errormsg .= get_string('datarooterror', 'install').'<br />';
    }

    if (!empty($errormsg)) {
        $nextstage = DIRECTORY;
    }

    error_reporting(38911);
}



//==========================================================================//

/// Check database settings if stage 3 data submitted
/// Try to connect to the database. If that fails then try to create the database

if ($INSTALL['stage'] == DATABASE) {

    $DB = $databases[$INSTALL['dbtype']];

    $dbfamily = $DB->get_dbfamily();
    $errormsg = $DB->driver_installed();

    if ($errormsg === true) {
        $errormsg = '';
    } else {
        $nextstage = DATABASE;
    }

    if (empty($INSTALL['prefix']) and $dbfamily != 'mysql') { // All DBs but MySQL require prefix (reserv. words)
        $errormsg = get_string('dbwrongprefix', 'install');
        $nextstage = DATABASE;
    }

    if ($dbfamily == 'oracle' and strlen($INSTALL['prefix']) > 2) { // Oracle max prefix = 2cc (30cc limit)
        $errormsg = get_string('dbwrongprefix', 'install');
        $nextstage = DATABASE;
    }

    if ($dbfamily == 'oracle' and !empty ($INSTALL['dbhost'])) { // Oracle host must be blank (tnsnames.ora has it)
        $errormsg = get_string('dbwronghostserver', 'install');
        $nextstage = DATABASE;
    }

    if (empty($errormsg)) {
        error_reporting(0);  // Hide errors

        if (! $dbconnected = $DB->connect($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix'])) {
            if (!$DB->create_database($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'])) {
                 $errormsg = get_string('dbcreationerror', 'install');
                 $nextstage = DATABASE;
            } else {
                $dbconnected = $DB->connect($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix']);
            }
        } else {
// TODO: db encoding checks ??
        }
    }

    error_reporting(38911);

/// Output db connection error
    if ((empty($errormsg) and ($dbconnected === false)) ) {
        $errormsg = get_string('dbconnectionerror', 'install');
        $nextstage = DATABASE;
    }
}



//==========================================================================//

/// If the next stage is admin directory settings OR we have just come from there then
/// check the admin directory.
/// If we can open a file then we know that the admin name is correct.

if ($nextstage == ADMIN or $INSTALL['stage'] == ADMIN) {
    if (!ini_get('allow_url_fopen')) {
        $nextstage = ($goforward) ? ENVIRONMENT : DATABASE;
    } else if (($fh = @fopen($INSTALL['wwwrootform'].'/'.$INSTALL['admindirname'].'/environment.xml', 'r')) !== false) {
        $nextstage = ($goforward) ? ENVIRONMENT : DATABASE;
        fclose($fh);
    } else {
        $nextstage = ($goforward) ? ENVIRONMENT : DATABASE;
        //if ($nextstage != ADMIN) {
        //    $errormsg = get_string('admindirerror', 'install');
        //    $nextstage = ADMIN;
        // }
    }
}

//==========================================================================//

// Check if we can navigate from the environemt page (because it's ok)

if ($INSTALL['stage'] == ENVIRONMENT) {
    $DB = $databases[$INSTALL['dbtype']];

    error_reporting(0);  // Hide errors
    $dbconnected = $DB->connect($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix']);
    error_reporting(38911);  // Show errors
    if ($dbconnected) {
    /// Execute environment check, printing results
        if (!check_moodle_environment($INSTALL['release'], $environment_results, false)) {
             $nextstage = ENVIRONMENT;
        }
    } else {
    /// We never should reach this because DB has been tested before arriving here
        $errormsg = get_string('dbconnectionerror', 'install');
        $nextstage = DATABASE;
    }
}



//==========================================================================//

// Try to download the lang pack if it has been selected

if ($INSTALL['stage'] == DOWNLOADLANG && $INSTALL['downloadlangpack']) {

    $downloadsuccess = false;
    $downloaderror = '';

    error_reporting(0);  // Hide errors

/// Create necessary lang dir
    if (!make_upload_directory('lang', false)) {
        $downloaderror = get_string('cannotcreatelangdir', 'error');
    }

/// Download and install component
    if (($cd = new component_installer('http://download.moodle.org', 'lang16',
        $INSTALL['language'].'.zip', 'languages.md5', 'lang')) && empty($errormsg)) {
        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
        switch ($status) {
            case COMPONENT_ERROR:
                if ($cd->get_error() == 'remotedownloaderror') {
                    $a = new stdClass();
                    $a->url = 'http://download.moodle.org/lang16/'.$INSTALL['language'].'.zip';
                    $a->dest= $CFG->dataroot.'/lang';
                    $downloaderror = get_string($cd->get_error(), 'error', $a);
                } else {
                    $downloaderror = get_string($cd->get_error(), 'error');
                }
            break;
            case COMPONENT_UPTODATE:
            case COMPONENT_INSTALLED:
                $downloadsuccess = true;
            break;
            default:
                //We shouldn't reach this point
        }
    } else {
        //We shouldn't reach this point
    }

    error_reporting(38911);  // Show errors

    if ($downloadsuccess) {
        $INSTALL['downloadlangpack']       = false;
        $INSTALL['showdownloadlangpack']   = false;
        $INSTALL['downloadlangpackerror']  = $downloaderror;
    } else {
        $INSTALL['downloadlangpack']       = false;
        $INSTALL['showdownloadlangpack']   = false;
        $INSTALL['downloadlangpackerror']  = $downloaderror;
    }
}



//==========================================================================//

/// Display or print the data
/// Put the data into a string
/// Try to open config file for writing.

if ($nextstage == SAVE) {

    $str = '<?php  /// Moodle Configuration File ' . "\r\n";
    $str .= "\r\n";

    $str .= 'unset($CFG);'."\r\n";
    $str .= '$CFG = new stdClass();'."\r\n"; // prevent PHP5 strict warnings
    $str .= "\r\n";

    $DB = $databases[$INSTALL['dbtype']];
    $dbconfig = $DB->export_dbconfig($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix']);

    foreach ($dbconfig as $key=>$value) {
        $key = str_pad($key, 9);
        $str .= '$CFG->'.$key.' = '.var_export($value, true).";\r\n";
    }
    $str .= "\r\n";

    $str .= '$CFG->wwwroot   = '.var_export($INSTALL['wwwrootform'], true).";\r\n";
    $str .= '$CFG->dirroot   = '.var_export($INSTALL['dirrootform'], true).";\r\n";
    $str .= '$CFG->dataroot  = '.var_export($INSTALL['dataroot'], true).";\r\n";
    $str .= '$CFG->admin     = '.var_export($INSTALL['admindirname'], true).";\r\n";
    $str .= "\r\n";

    $str .= '$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode'."\r\n";
    $str .= "\r\n";

    $str .= 'require_once("$CFG->dirroot/lib/setup.php");'."\r\n";
    $str .= '// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,'."\r\n";
    $str .= '// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.'."\r\n";
    $str .= '?>';

    umask(0137);

    if (( $configsuccess = ($fh = @fopen($configfile, 'w')) ) !== false) {
        fwrite($fh, $str);
        fclose($fh);
    }


    $INSTALL['config'] = $str;
}



//==========================================================================//

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html dir="<?php echo (right_to_left() ? 'rtl' : 'ltr'); ?>">
<head>
<link rel="shortcut icon" href="theme/standard/favicon.ico" />
<title>Moodle Install</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php css_styles($databases) ?>
<?php database_js($databases) ?>

</head>

<body>


<?php
if (isset($_GET['help'])) {
    print_install_help($_GET['help']);
    close_window_button();
} else {
?>


<table class="main" cellpadding="3" cellspacing="0">
    <tr>
        <td class="td_mainlogo">
            <p class="p_mainlogo"><img src="pix/moodlelogo-med.gif" width="240" height="60" alt="Moodle logo"/></p>
        </td>
        <td class="td_mainlogo" valign="bottom">
            <p class="p_mainheader"><?php print_string('installation', 'install') ?></p>
        </td>
    </tr>

    <tr>
        <td class="td_mainheading" colspan="2">
            <p class="p_mainheading"><?php echo $headstagetext[$nextstage] ?></p>
            <?php /// Exceptionaly, depending of the DB selected, we show some different text
                  /// from the standard one to show better instructions for each DB
                if ($nextstage == DATABASE) {
                    foreach ($databases as $driver=>$database) {
                        echo '<script type="text/javascript" defer="defer">window.onload=toggledbinfo;</script>';
                        echo '<div id="'.$driver.'">' . $database->get_configuration_hints();
                        echo '<p style="text-align: center">' . get_string('databasesettingswillbecreated', 'install') . '</p>';
                        echo '</div>';
                    }
                } else {
                    if (!empty($substagetext[$nextstage])) {
                        echo '<p class="p_subheading">' . $substagetext[$nextstage] . '</p>';
                    }
                }
            ?>
        </td>
    </tr>

    <tr>
        <td class="td_main" colspan="2">

<?php

if (!empty($errormsg)) echo "<p class=\"errormsg\" style=\"text-align:center\">$errormsg</p>\n";


if ($nextstage == SAVE) {
    $INSTALL['stage'] = WELCOME;
    $options = array();
    $options['lang'] = $INSTALL['language'];
    if ($configsuccess) {
        echo "<p class=\"p_install\">".get_string('configfilewritten', 'install')."</p>\n";

        echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "<td>&nbsp;</td>\n";
        echo "<td>&nbsp;</td>\n";
        echo "<td align=\"right\">\n";
        print_single_button("index.php", $options, get_string('continue'));
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";

    } else {
        echo "<p class=\"errormsg\">".get_string('configfilenotwritten', 'install')."</p>";

        echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "<td>&nbsp;</td>\n";
        echo "<td align=\"center\">\n";
        $installoptions = array();
        $installoptions['download'] = 1;
        print_single_button("install.php", $installoptions, get_string('download', 'install'));
        echo "</td>\n";
        echo "<td align=\"right\">\n";
        print_single_button("index.php", $options, get_string('continue'));
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";

        echo "<hr />\n";
        echo "<div style=\"text-align: ".fix_align_rtl("left")."\">\n";
        echo "<pre>\n";
        p($INSTALL['config']);
        echo "</pre>\n";
        echo "</div>\n";
    }
} else {
    $formaction = (isset($_GET['configfile'])) ? "install.php?configfile=".$_GET['configfile'] : "install.php";
    form_table($nextstage, $formaction, $databases);
}

?>

        </td>
    </tr>
</table>

<?php
}
?>

</body>
</html>










<?php


//==========================================================================//

function form_table($nextstage, $formaction, $databases) {
    global $INSTALL, $DB;

    /// Print the standard form if we aren't in the DOWNLOADLANG page
    /// because it has its own form.
    if ($nextstage != DOWNLOADLANG) {
        $needtoopenform = false;
?>
        <form id="installform" method="post" action="<?php echo $formaction ?>">
        <div><input type="hidden" name="stage" value="<?php echo $nextstage ?>" /></div>

<?php
    } else {
        $needtoopenform = true;
    }
?>
    <table class="install_table" cellspacing="3" cellpadding="3">

<?php
    /// what we do depends on the stage we're at
    switch ($nextstage) {
        case WELCOME: /// Welcome and language settings
?>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('language') ?></p></td>
                <td class="td_right">
                <?php choose_from_menu (get_installer_list_of_languages(), 'language', $INSTALL['language'], '') ?>
                </td>
            </tr>

<?php
            break;
        case COMPATIBILITY: /// Compatibilty check
            $compatsuccess = true;

            /// Check that PHP is of a sufficient version
            print_compatibility_row(inst_check_php_version(), get_string('phpversion', 'install'), get_string('php52versionerror', 'install'), 'php52versionhelp');
            /// Check session auto start
            print_compatibility_row(!ini_get_bool('session.auto_start'), get_string('sessionautostart', 'install'), get_string('sessionautostarterror', 'install'), 'sessionautostarthelp');
            /// Check magic quotes
            print_compatibility_row(!ini_get_bool('magic_quotes_runtime'), get_string('magicquotesruntime', 'install'), get_string('magicquotesruntimeerror', 'install'), 'magicquotesruntimehelp');
            /// Check unsupported PHP configuration
            print_compatibility_row(ini_get_bool('magic_quotes_gpc') || (!ini_get_bool('register_globals')), get_string('globalsquotes', 'install'), get_string('globalsquoteserror', 'install'), 'globalsquoteshelp');
            /// Check safe mode
            print_compatibility_row(!ini_get_bool('safe_mode'), get_string('safemode', 'install'), get_string('safemodeerror', 'install'), 'safemodehelp', true);
            /// Check file uploads
            print_compatibility_row(ini_get_bool('file_uploads'), get_string('fileuploads', 'install'), get_string('fileuploadserror', 'install'), 'fileuploadshelp', true);
            /// Check GD version
            print_compatibility_row(check_gd_version(), get_string('gdversion', 'install'), get_string('gdversionerror', 'install'), 'gdversionhelp', true);
            /// Check memory limit
            print_compatibility_row(check_memory_limit(), get_string('memorylimit', 'install'), get_string('memorylimiterror', 'install'), 'memorylimithelp', true);


            break;
        case DIRECTORY: /// Directory settings
?>

            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('wwwroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40"name="wwwrootform" value="<?php p($INSTALL['wwwrootform'],true) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('dirroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dirrootform" disabled="disabled" value="<?php p($INSTALL['dirrootform'],true) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('dataroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dataroot" value="<?php p($INSTALL['dataroot'],true) ?>" />
                </td>
            </tr>

<?php
            break;
        case DATABASE: /// Database settings
?>

            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('dbtype', 'install') ?></p></td>
                <td class="td_right">

                <?php

            $options = array ();
            foreach ($databases as $type => $database) {
                $name = $database->get_name();
                if ($database->driver_installed() !== true) {
                    $name = "$name - driver not installed"; // TODO: improve missing driver notification
                }
                $options[$type] = $name;
            }
            choose_from_menu($options, 'dbtype', $INSTALL['dbtype'], '', 'toggledbinfo();')
?>
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('dbhost', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" class="input_database" name="dbhost" value="<?php p($INSTALL['dbhost']) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('database', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" class="input_database" name="dbname" value="<?php p($INSTALL['dbname']) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('user') ?></p></td>
                <td class="td_right">
                    <input type="text" class="input_database" name="dbuser" value="<?php p($INSTALL['dbuser']) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('password') ?></p></td>
                <td class="td_right">
                    <input type="password" class="input_database" name="dbpass" value="<?php p($INSTALL['dbpass']) ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('dbprefix', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" class="input_database" name="prefix" value="<?php p($INSTALL['prefix']) ?>" />
                </td>
            </tr>

<?php
            break;
        case ADMIN: /// Administration directory setting
?>

            <tr>
                <td class="td_left"><p class="p_install"><?php print_string('admindirname', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="admindirname" value="<?php p($INSTALL['admindirname']) ?>" />
                </td>
            </tr>


<?php
            break;
        case ENVIRONMENT: /// Environment checks
?>

            <tr>
                <td colspan="2">
                <?php
                    error_reporting(0);  // Hide errors
                    $dbconnected = $DB->connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname'], false, $INSTALL['prefix']);
                    error_reporting(38911);  // Show errors
                    if ($dbconnected) {
                    /// Execute environment check, printing results
                        check_moodle_environment($INSTALL['release'], $environment_results, true);
                    } else {
                    /// We never should reach this because DB has been tested before arriving here
                        $errormsg = get_string('dbconnectionerror', 'install');
                        $nextstage = DATABASE;
                        echo '<p class="errormsg" style="text-align:center">'.get_string('dbconnectionerror', 'install').'</p>';
                    }
                ?>
                </td>
            </tr>

<?php
            break;
        case DOWNLOADLANG: /// Download language from download.moodle.org
?>

            <tr>
                <td colspan="2">
                <?php
                /// Get array of languages, we are going to use it
                    $languages=get_installer_list_of_languages();
                /// Print the download form (button) if necessary
                    if ($INSTALL['showdownloadlangpack'] == true && substr($INSTALL['language'],0,2) != 'en') {
                        $options = array();
                        $options['downloadlangpack'] = true;
                        $options['stage'] = DOWNLOADLANG;
                        $options['same'] = true;
                        print_simple_box_start('center');
                        print_single_button('install.php', $options, get_string('downloadlanguagebutton','install', $languages[$INSTALL['language']]), 'post');
                        print_simple_box_end();
                    } else {
                /// Show result info
                    /// English lang packs aren't downloaded
                        if (substr($INSTALL['language'],0,2) == 'en') {
                            print_simple_box(get_string('downloadlanguagenotneeded', 'install', $languages[$INSTALL['language']]), 'center', '80%');
                        } else {
                            if ($INSTALL['downloadlangpackerror']) {
                                echo "<p class=\"errormsg\" align=\"center\">".$INSTALL['downloadlangpackerror']."</p>\n";
                                print_simple_box(get_string('langdownloaderror', 'install', $languages[$INSTALL['language']]), 'center', '80%');
                            } else {
                                print_simple_box(get_string('langdownloadok', 'install', $languages[$INSTALL['language']]), 'center', '80%');
                            }
                        }
                    }
                ?>
                </td>
            </tr>

<?php
            break;
        default:
    }
?>

    <tr>
        <td colspan="<?php echo ($nextstage == COMPATIBILITY) ? 3 : 2; ?>">

<?php
    if ($needtoopenform) {
?>
            <form id="installform" method="post" action="<?php echo $formaction ?>">
            <div><input type="hidden" name="stage" value="<?php echo $nextstage ?>" /></div>
<?php
    }
?>

            <?php echo ($nextstage < SAVE) ? "<div><input type=\"submit\" name=\"next\" value=\"".get_string('next')."  &raquo;\" style=\"float: ".fix_align_rtl("right")."\"/></div>\n" : "&nbsp;\n" ?>
            <?php echo ($nextstage > WELCOME) ? "<div><input type=\"submit\" name=\"prev\" value=\"&laquo;  ".get_string('previous')."\" style=\"float: ".fix_align_rtl("left")."\"/></div>\n" : "&nbsp;\n" ?>

<?php
    if ($needtoopenform) {
?>
            </form>
<?php
    }
?>


        </td>

    </tr>

    </table>
<?php
    if (!$needtoopenform) {
?>
    </form>
<?php
    }
?>

<?php
}



//==========================================================================//

function print_compatibility_row($success, $testtext, $errormessage, $helpfield='', $caution=false) {
    echo "<tr>\n";
    echo "<td class=\"td_left_nowrap\" valign=\"top\"><p class=\"p_install\">$testtext</p></td>\n";
    if ($success) {
        echo "<td valign=\"top\"><p class=\"p_pass\">".get_string('pass', 'install')."</p></td>\n";
        echo "<td valign=\"top\">&nbsp;</td>\n";
    } else {
        echo "<td valign=\"top\">";
        echo ($caution) ? "<p class=\"p_caution\">".get_string('caution', 'install') : "<p class=\"p_fail\">".get_string('fail', 'install');
        echo "</p></td>\n";
        echo "<td valign=\"top\">";
        echo "<p class=\"p_install\">$errormessage ";
        install_helpbutton("install.php?help=$helpfield");
        echo "</p></td>\n";
    }
    echo "</tr>\n";
    return $success;
}


//==========================================================================//

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



//==========================================================================//

function print_install_help($help) {
    switch ($help) {
        case 'phpversionhelp':
            print_string($help, 'install', phpversion());
            break;
        case 'memorylimithelp':
            print_string($help, 'install', get_memory_limit());
            break;
        default:
            print_string($help, 'install');
    }
}

//==========================================================================//

function css_styles($databases) {
?>

<style type="text/css">

    body { background-color: #ffeece; }
    p, li, td {
        font-family: helvetica, arial, sans-serif;
        font-size: 10pt;
    }
    a { text-decoration: none; color: blue; }
    a img {
        border: none;
    }
    .errormsg {
        color: red;
        font-weight: bold;
    }
    blockquote {
        font-family: courier, monospace;
        font-size: 10pt;
    }
    .input_database {
        width: 270px;
    }
    .install_table {
        width: 500px;
        margin-left:auto;
        margin-right:auto;
    }
    .td_left {
        text-align: <?php echo fix_align_rtl("right") ?>;
        font-weight: bold;
    }
    .td_left_nowrap{
        text-align: <?php echo fix_align_rtl("right") ?>;
        font-weight: bold;
        white-space: nowrap;
        width: 160px;
        padding-left: 10px;
    }
    .td_right {
        text-align: <?php echo fix_align_rtl("left") ?>;
    }
    .main {
        width: 80%;
        border-width: 1px;
        border-style: solid;
        border-color: #ffc85f;
        margin-left:auto;
        margin-right:auto;
        -moz-border-radius-bottomleft: 15px;
        -moz-border-radius-bottomright: 15px;
    }
    .td_mainheading {
        background-color: #fee6b9;
        padding-left: 10px;
    }
    .td_main {
        text-align: center;
    }
    .td_mainlogo {
        vertical-align: middle;
    }
    .p_mainlogo {
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .p_mainheading {
        font-size: 11pt;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    .p_subheading {
        font-size: 10pt;
        padding-left: 10px;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    .p_mainheader{
        text-align: right;
        font-size: 20pt;
        font-weight: bold;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .p_pass {
        color: green;
        font-weight: bold;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .p_fail {
        color: red;
        font-weight: bold;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .p_caution {
        color: #ff6600;
        font-weight: bold;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .p_help {
        text-align: center;
        font-family: helvetica, arial, sans-serif;
        font-size: 14pt;
        font-weight: bold;
        color: #333333;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    /* This override the p tag for every p tag in this installation script,
       but not in lang\xxx\installer.php
      */
    .p_install {
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .environmenttable {
        font-size: 10pt;
        border-color: #ffc85f;
    }
    table.environmenttable .error {
        background-color : red;
        color : inherit;
    }

    table.environmenttable .warn {
        background-color : yellow;
    }

    table.environmenttable .ok {
        background-color : lightgreen;
    }
    .header {
        background-color: #fee6b9;
        font-size: 10pt;
    }
    .cell {
        background-color: #ffeece;
        font-size: 10pt;
    }
    .error {
        color: #ff0000;
    }
    .errorboxcontent {
        text-align: center;
        font-weight: bold;
        padding-left: 20px;
        color: #ff0000;
    }
    .invisiblefieldset {
      display:inline;
      border:0px;
      padding:0px;
      margin:0px;
    }
    <?php
        $list = array();
        foreach ($databases as $driver=>$unused) {
            $list[] = '#'.$driver;
        }
        $list = implode(',', $list);
        echo ($list);
    ?> {
        display: none;
    }

</style>

<?php
}

//==========================================================================//

function database_js($databases) {
?>

<script type="text/javascript" defer="defer">
function toggledbinfo() {
    //Calculate selected value
    var showid = 'mysql';
    if (document.getElementById('installform').dbtype.value) {
        showid = document.getElementById('installform').dbtype.value;
    }
    if (document.getElementById) {
        //Hide all the divs
    <?php
        $list = array();
        foreach ($databases as $driver=>$unused) {
            echo "document.getElementById('$driver').style.display = '';";
        }
    ?>
        //Show the selected div
        document.getElementById(showid).style.display = 'block';
    } else if (document.all) {
        //This is the way old msie versions work
        //Hide all the divs
    <?php
        $list = array();
        foreach ($databases as $driver=>$unused) {
            echo "document.all['$driver'].style.display = '';";
        }
    ?>
        //Show the selected div
        document.all[showid].style.display = 'block';
    } else if (document.layers) {
        //This is the way nn4 works
        //Hide all the divs
    <?php
        $list = array();
        foreach ($databases as $driver=>$unused) {
            echo "document.layers['$driver'].style.display = '';";
        }
    ?>
        //Show the selected div
        document.layers[showid].style.display = 'block';
    }
}
</script>

<?php
}

?>
