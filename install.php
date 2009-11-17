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
    $INSTALL['dbtype']          = 'mysql';
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

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/setuplib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->libdir.'/deprecatedlib.php');
require_once($CFG->libdir.'/adodb/adodb.inc.php');
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/componentlib.class.php');
require_once($CFG->dirroot.'/version.php');

/// Set version and release
$INSTALL['version'] = $version;
$INSTALL['release'] = $release;

/// Have the $db object ready because we are going to use it often
define ('ADODB_ASSOC_CASE', 0); //Use lowercase fieldnames for ADODB_FETCH_ASSOC
$db = &ADONewConnection($INSTALL['dbtype']);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

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
    if (ini_get('allow_url_fopen') && false) {  /// This was not reliable
        if (($fh = @fopen($INSTALL['wwwrootform'].'/install.php', 'r')) === false) {
            $errormsg .= get_string('wwwrooterror', 'install').'<br />';
            $INSTALL['wwwrootform'] = $INSTALL['wwwroot'];
        }
    }
    if ($fh) fclose($fh);

    /// check dirroot
    if (($fh = @fopen($INSTALL['dirrootform'].'/install.php', 'r')) === false ) {
        $errormsg .= get_string('dirrooterror', 'install').'<br />';
        $INSTALL['dirrootform'] = $INSTALL['dirroot'];
    }
    if ($fh) fclose($fh);

    /// check dataroot
    $CFG->dataroot = $INSTALL['dataroot'];
    $CFG->wwwroot  = $INSTALL['wwwroot'];
    if (make_upload_directory('sessions', false) === false ) {
        $errormsg .= get_string('datarooterror', 'install').'<br />';

    } else if (is_dataroot_insecure(true) == INSECURE_DATAROOT_ERROR) {
        $errormsg .= get_string('datarootpublicerror', 'install').'<br />';
    }

    if (!empty($errormsg)) $nextstage = DIRECTORY;

    error_reporting(7);
}



//==========================================================================//

/// Check database settings if stage 3 data submitted
/// Try to connect to the database. If that fails then try to create the database

if ($INSTALL['stage'] == DATABASE) {

    /// different format for postgres7 by socket
    if ($INSTALL['dbtype'] == 'postgres7' and ($INSTALL['dbhost'] == 'localhost' || $INSTALL['dbhost'] == '127.0.0.1')) {
        $INSTALL['dbhost'] = "user='{$INSTALL['dbuser']}' password='{$INSTALL['dbpass']}' dbname='{$INSTALL['dbname']}'";
        $INSTALL['dbuser'] = '';
        $INSTALL['dbpass'] = '';
        $INSTALL['dbname'] = '';

        if ($INSTALL['prefix'] == '') { /// must have a prefix
            $INSTALL['prefix'] = 'mdl_';
        }
    }

    if ($INSTALL['dbtype'] == 'mysql') {  /// Check MySQL extension is present
        if (!extension_loaded('mysql')) {
            $errormsg = get_string('mysqlextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'mysqli') {  /// Check MySQLi extension is present
        if (!extension_loaded('mysqli')) {
            $errormsg = get_string('mysqliextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'postgres7') {  /// Check PostgreSQL extension is present
        if (!extension_loaded('pgsql')) {
            $errormsg = get_string('pgsqlextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'mssql') {  /// Check MSSQL extension is present
        if (!function_exists('mssql_connect')) {
            $errormsg = get_string('mssqlextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'mssql_n') {  /// Check MSSQL extension is present
        if (!function_exists('mssql_connect')) {
            $errormsg = get_string('mssqlextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'odbc_mssql') {  /// Check ODBC extension is present
        if (!extension_loaded('odbc')) {
            $errormsg = get_string('odbcextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if ($INSTALL['dbtype'] == 'oci8po') {  /// Check OCI extension is present
        if (!extension_loaded('oci8')) {
            $errormsg = get_string('ociextensionisnotpresentinphp', 'install');
            $nextstage = DATABASE;
        }
    }

    if (empty($INSTALL['prefix']) && $INSTALL['dbtype'] != 'mysql' && $INSTALL['dbtype'] != 'mysqli') { // All DBs but MySQL require prefix (reserv. words)
        $errormsg = get_string('dbwrongprefix', 'install');
        $nextstage = DATABASE;
    }

    if ($INSTALL['dbtype'] == 'oci8po' && strlen($INSTALL['prefix']) > 2) { // Oracle max prefix = 2cc (30cc limit)
        $errormsg = get_string('dbwrongprefix', 'install');
        $nextstage = DATABASE;
    }

    if ($INSTALL['dbtype'] == 'oci8po' && !empty($INSTALL['dbhost'])) { // Oracle host must be blank (tnsnames.ora has it)
        $errormsg = get_string('dbwronghostserver', 'install');
        $nextstage = DATABASE;
    }

    if (empty($errormsg)) {

        error_reporting(0);  // Hide errors

        if (! $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname'])) {
            $db->database = ''; // reset database name cached by ADODB. Trick from MDL-9609
            if ($dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'])) { /// Try to connect without DB
                switch ($INSTALL['dbtype']) {   /// Try to create a database
                    case 'mysql':
                    case 'mysqli':
                        if ($db->Execute("CREATE DATABASE {$INSTALL['dbname']} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;")) {
                            $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname']);
                        } else {
                            $errormsg = get_string('dbcreationerror', 'install');
                            $nextstage = DATABASE;
                        }
                        break;
                }
            }
        } else {
        /// We have been able to connect properly, just test the database encoding now.
        /// It must be Unicode for 1.8 installations.
            $encoding = '';
            switch ($INSTALL['dbtype']) {
                case 'mysql':
                case 'mysqli':
                /// Get MySQL character_set_database value
                    $rs = $db->Execute("SHOW VARIABLES LIKE 'character_set_database'");
                    if ($rs && !$rs->EOF) {
                        $records = $rs->GetAssoc(true);
                        $encoding = $records['character_set_database']['Value'];
                        if (strtoupper($encoding) != 'UTF8') {
                        /// Try to set the encoding now!
                            if (! $db->Metatables()) {  // We have no tables so go ahead
                                $db->Execute("ALTER DATABASE `".$INSTALL['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
                                $rs = $db->Execute("SHOW VARIABLES LIKE 'character_set_database'");  // this works

                            }
                        }
                        /// If conversion fails, skip, let environment testing do the job
                    }
                    break;
                case 'postgres7':
                /// Skip, let environment testing do the job
                    break;
                case 'oci8po':
                /// Skip, let environment testing do the job
                    break;
            }
        }
    }

    error_reporting(7);

    if (($dbconnected === false) and (empty($errormsg)) ) {
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
    error_reporting(0);  // Hide errors
    $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname']);
    error_reporting(7);  // Show errors
    if ($dbconnected) {
    /// Execute environment check, not printing results
        @remove_dir($INSTALL['dataroot'] . '/environment'); /// Always delete downloaded env. info to force use of the released one. MDL-9796
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

    error_reporting(7);  // Show errors

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

    $str  = '<?php  /// Moodle Configuration File '."\r\n";
    $str .= "\r\n";

    $str .= 'unset($CFG);'."\r\n";
    $str .= "\r\n";

    $str .= '$CFG->dbtype    = \''.$INSTALL['dbtype']."';\r\n";
    $str .= '$CFG->dbhost    = \''.addslashes($INSTALL['dbhost'])."';\r\n";
    if (!empty($INSTALL['dbname'])) {
        $str .= '$CFG->dbname    = \''.$INSTALL['dbname']."';\r\n";
        // support single quotes in db user/passwords
        $str .= '$CFG->dbuser    = \''.addsingleslashes($INSTALL['dbuser'])."';\r\n";
        $str .= '$CFG->dbpass    = \''.addsingleslashes($INSTALL['dbpass'])."';\r\n";
    }
    $str .= '$CFG->dbpersist =  false;'."\r\n";
    $str .= '$CFG->prefix    = \''.$INSTALL['prefix']."';\r\n";
    $str .= "\r\n";

    $str .= '$CFG->wwwroot   = \''.s($INSTALL['wwwrootform'],true)."';\r\n";
    $str .= '$CFG->dirroot   = \''.s($INSTALL['dirrootform'],true)."';\r\n";
    $str .= '$CFG->dataroot  = \''.s($INSTALL['dataroot'],true)."';\r\n";
    $str .= '$CFG->admin     = \''.s($INSTALL['admindirname'],true)."';\r\n";
    $str .= "\r\n";

    $str .= '$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode'."\r\n";
    $str .= "\r\n";

    $str .= '$CFG->passwordsaltmain = \''.addsingleslashes(complex_random_string()).'\';'."\r\n";
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
<?php css_styles() ?>
<?php database_js() ?>

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
                    echo '<script type="text/javascript" defer="defer">window.onload=toggledbinfo;</script>';
                    echo '<div id="mysql">' . get_string('databasesettingssub_mysql', 'install');
                    echo '<p style="text-align: center">' . get_string('databasesettingswillbecreated', 'install') . '</p>';
                    echo '</div>';

                    echo '<div id="mysqli">' . get_string('databasesettingssub_mysqli', 'install');
                    echo '<p style="text-align: center">' . get_string('databasesettingswillbecreated', 'install') . '</p>';
                    echo '</div>';

                    echo '<div id="postgres7">' . get_string('databasesettingssub_postgres7', 'install');
                    echo '<p style="text-align: left">' . get_string('postgresqlwarning', 'install') . '</p>';
                    echo '</div>';

                    echo '<div id="mssql">' . get_string('databasesettingssub_mssql', 'install');
                /// Link to mssql installation page
                    echo "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
                    echo "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
                    echo ">";
                    echo '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
                    echo get_string('moodledocslink', 'install') . '</a></p>';
                    echo '</div>';

                    echo '<div id="mssql_n">' . get_string('databasesettingssub_mssql_n', 'install');
                /// Link to mssql installation page
                    echo "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
                    echo "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
                    echo ">";
                    echo '<img src="pix/docs.gif' . '" alt="Docs" />';
                    echo get_string('moodledocslink', 'install') . '</a></p>';
                    echo '</div>';

                    echo '<div id="odbc_mssql">'. get_string('databasesettingssub_odbc_mssql', 'install');
                /// Link to mssql installation page
                    echo "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
                    echo "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
                    echo ">";
                    echo '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
                    echo get_string('moodledocslink', 'install') . '</a></p>';
                    echo '</div>';

                    echo '<div id="oci8po">' . get_string('databasesettingssub_oci8po', 'install');
                /// Link to oracle installation page
                    echo "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
                    echo "onclick=\"return window.open('http://docs.moodle.org/en/Installing_Oracle_for_PHP')\"";
                    echo ">";
                    echo '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
                    echo get_string('moodledocslink', 'install') . '</a></p>';
                    echo '</div>';
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
        print_r(s($str));
        echo "</pre>\n";
        echo "</div>\n";
    }
} else {
    $formaction = (isset($_GET['configfile'])) ? "install.php?configfile=".$_GET['configfile'] : "install.php";
    form_table($nextstage, $formaction);
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

function form_table($nextstage = WELCOME, $formaction = "install.php") {
    global $INSTALL, $db;

    $enablenext = true;

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
            print_compatibility_row(inst_check_php_version(), get_string('phpversion', 'install'), get_string('phpversionerror', 'install'), 'phpversionhelp');
            $enablenext = $enablenext && inst_check_php_version();
            /// Check session auto start
            print_compatibility_row(!ini_get_bool('session.auto_start'), get_string('sessionautostart', 'install'), get_string('sessionautostarterror', 'install'), 'sessionautostarthelp');
            $enablenext = $enablenext && !ini_get_bool('session.auto_start');
            /// Check magic quotes
            print_compatibility_row(!ini_get_bool('magic_quotes_runtime'), get_string('magicquotesruntime', 'install'), get_string('magicquotesruntimeerror', 'install'), 'magicquotesruntimehelp');
            $enablenext = $enablenext && !ini_get_bool('magic_quotes_runtime');
            /// Check unsupported PHP configuration
            print_compatibility_row(!ini_get_bool('register_globals'), get_string('globalsquotes', 'install'), get_string('globalswarning', 'install'));
            $enablenext = $enablenext && !ini_get_bool('register_globals');
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
                <?php choose_from_menu (array('mysql' => get_string('mysql', 'install'),
                                              'mysqli' => get_string('mysqli', 'install'),
                                              'oci8po' => get_string('oci8po', 'install'),
                                              'postgres7' => get_string('postgres7', 'install'),
                                              'mssql' => get_string('mssql', 'install'),
                                              'mssql_n' => get_string('mssql_n', 'install'),
                                              'odbc_mssql' => get_string('odbc_mssql', 'install')),
                                        'dbtype', $INSTALL['dbtype'], '', 'toggledbinfo();') ?>
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
                    $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname']);
                    error_reporting(7);  // Show errors
                    if ($dbconnected) {
                    /// Execute environment check, printing results
                        @remove_dir($INSTALL['dataroot'] . '/environment'); /// Always delete downloaded env. info to force use of the released one. MDL-9796
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

    $disabled = $enablenext ? '' : 'disabled="disabled"';
?>

            <?php echo ($nextstage < SAVE) ? "<div><input $disabled type=\"submit\" name=\"next\" value=\"".get_string('next')."  &raquo;\" style=\"float: ".fix_align_rtl("right")."\"/></div>\n" : "&nbsp;\n" ?>
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
        if ($helpfield !== '') {
            install_helpbutton("install.php?help=$helpfield");
        }
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

function get_memory_limit() {
    if ($limit = ini_get('memory_limit')) {
        return $limit;
    } else {
        return get_cfg_var('memory_limit');
    }
}

//==========================================================================//

function check_memory_limit() {

    /// if limit is already 40 or more then we don't care if we can change it or not
    if ((int)str_replace('M', '', get_memory_limit()) >= 40) {
        return true;
    }

    /// Otherwise, see if we can change it ourselves
    raise_memory_limit('40M');
    return ((int)str_replace('M', '', get_memory_limit()) >= 40);
}

//==========================================================================//

function inst_check_php_version() {
    if (!check_php_version("4.3.0")) {
        return false;
    } else if (check_php_version("5.0.0")) {
        return check_php_version("5.1.0"); // 5.0.x is too buggy
    }
    return true; // 4.3.x or 4.4.x is fine
}
//==========================================================================//

/* This function returns a list of languages and their full names. The
 * list of available languages is fetched from install/lang/xx/installer.php
 * and it's used exclusively by the installation process
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function get_installer_list_of_languages() {

    global $CFG;

    $languages = array();

/// Get raw list of lang directories
    $langdirs = get_list_of_plugins('install/lang');
    asort($langdirs);
/// Get some info from each lang
    foreach ($langdirs as $lang) {
        if (file_exists($CFG->dirroot .'/install/lang/'. $lang .'/installer.php')) {
            include($CFG->dirroot .'/install/lang/'. $lang .'/installer.php');
            if (substr($lang, -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
            if ($lang == 'en') {  //Explain this is non-utf8 en
                $shortlang = 'non-utf8 en';
            }
            if (!empty($string['thislanguage'])) {
                $languages[$lang] = $string['thislanguage'] .' ('. $shortlang .')';
            }
            unset($string);
        }
    }
/// Return array
    return $languages;
}

//==========================================================================//

function css_styles() {
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
    #mysql, #mysqli, #postgres7, #mssql, #mssql_n, #odbc_mssql, #oci8po {
        display: none;
    }

</style>

<?php
}

//==========================================================================//

function database_js() {
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
        document.getElementById('mysql').style.display = '';
        document.getElementById('mysqli').style.display = '';
        document.getElementById('postgres7').style.display = '';
        document.getElementById('mssql').style.display = '';
        document.getElementById('mssql_n').style.display = '';
        document.getElementById('odbc_mssql').style.display = '';
        document.getElementById('oci8po').style.display = '';
        //Show the selected div
        document.getElementById(showid).style.display = 'block';
    } else if (document.all) {
        //This is the way old msie versions work
        //Hide all the divs
        document.all['mysql'].style.display = '';
        document.all['mysqli'].style.display = '';
        document.all['postgres7'].style.display = '';
        document.all['mssql'].style.display = '';
        document.all['mssql_n'].style.display = '';
        document.all['odbc_mssql'].style.display = '';
        document.all['oci8po'].style.display = '';
        //Show the selected div
        document.all[showid].style.display = 'block';
    } else if (document.layers) {
        //This is the way nn4 works
        //Hide all the divs
        document.layers['mysql'].style.display = '';
        document.layers['mysqli'].style.display = '';
        document.layers['postgres7'].style.display = '';
        document.layers['mssql'].style.display = '';
        document.layers['mssql_n'].style.display = '';
        document.layers['odbc_mssql'].style.display = '';
        document.layers['oci8po'].style.display = '';
        //Show the selected div
        document.layers[showid].style.display = 'block';
    }
}
</script>

<?php
}

/**
 * Add slashes for single quotes and backslashes
 * so they can be included in single quoted string
 * (for config.php)
 */
function addsingleslashes($input){
    return preg_replace("/(['\\\])/", "\\\\$1", $input);
}
?>
