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
/// 0. Welcome and language settings
/// 1. Compatibility
/// 2. Database settings
/// 3. Host settings
/// 4. Administration directory name
/// 5. Save or display the settings
/// 6. Redirect to index.php
///==========================================================================//



/// Begin the session as we are holding all information in a session
/// variable until the end.

session_name('MoodleSession');
@session_start();

if (! isset($_SESSION['INSTALL'])) {
    $_SESSION['INSTALL'] = array();
}

$INSTALL = &$_SESSION['INSTALL'];   // Makes it easier to reference


/// If it's our first time through this script then we need to set some default values

if ( empty($INSTALL['language']) and empty($_POST['language']) ) {

    /// set defaults
    $INSTALL['language']        = 'en';

    $INSTALL['dbhost']          = 'localhost';
    $INSTALL['dbuser']          = '';
    $INSTALL['dbpass']          = '';
    $INSTALL['dbtype']          = 'mysql';
    $INSTALL['dbname']          = 'moodle';
    $INSTALL['prefix']          = 'mdl_';

    $INSTALL['wwwroot']         = '';
    $INSTALL['dirroot']         = dirname(__FILE__);
    $INSTALL['dataroot']        = dirname(dirname(__FILE__)) . '/moodledata';

    $INSTALL['admindirname']    = 'admin';

    $INSTALL['stage'] = 0;
}



//==========================================================================//

/// Fake some settings so that we can use selected functions from moodlelib.php and weblib.php

$SESSION->lang = (!empty($_POST['language'])) ? $_POST['language'] : $INSTALL['language'];
$CFG->dirroot = $INSTALL['dirroot'];
$CFG->dataroot = $INSTALL['dataroot'];
$CFG->directorypermissions = 00777;


/// Include some moodle libraries

require_once('./lib/moodlelib.php');
require_once('./lib/weblib.php');
require_once('./lib/adodb/adodb.inc.php');


/// guess the www root
if ($INSTALL['wwwroot'] == '') {
    list($INSTALL['wwwroot'], $xtra) = explode('/install.php', qualified_me());
}

$stagetext = array(0 => get_string('chooselanguage', 'install'),
                        get_string('compatibilitysettings', 'install'),
                        get_string('directorysettings', 'install'),
                        get_string('databasesettings', 'install'),
                        get_string('admindirsetting', 'install'),
                        get_string('configurationcomplete', 'install')
                    );




//==========================================================================//

/// Are we in help mode?

if (isset($_GET['help'])) {
    $nextstage = -1;
}



//==========================================================================//

/// Are we in config download mode?

if (isset($_GET['download'])) {
    header("Content-Type: application/download\n"); 
    header("Content-Disposition: attachment; filename=\"config.php\"");
    echo $INSTALL['config'];
    exit;
}



//==========================================================================//

/// Was data submitted?

if (isset($_POST['stage'])) {

    /// Get the stage for which the form was set and the next stage we are going to


    if ( $goforward = (! empty( $_POST['next'] )) ) {
        $nextstage = $_POST['stage'] + 1;
    } else {
        $nextstage = $_POST['stage'] - 1;
    }
    
    if ($nextstage < 0) $nextstage = 0;
    

    /// Store any posted data
    foreach ($_POST as $setting=>$value) {
        $INSTALL[$setting] = $value;
    }
    
} else {

    $goforward = true;
    $nextstage = 0;
    
}



//==========================================================================//

/// Check the directory settings

if ($INSTALL['stage'] == 2) {

    error_reporting(0);
    
            
    /// check dirroot
    if (($fh = @fopen($INSTALL['dirroot'].'/install.php', 'r')) === false ) {
        $CFG->dirroot = dirname(__FILE__);
        $INSTALL['dirroot'] = dirname(__FILE__);
        $errormsg .= get_string('dirrooterror', 'install').'<br />';
    } 
    if ($fh) fclose($fh);
            
    $CFG->dirroot = $INSTALL['dirroot'];

    /// check wwwroot
    if (ini_get('allow_url_fopen')) {
        if (($fh = @fopen($INSTALL['wwwroot'].'/install.php', 'r')) === false) {
            $errormsg .= get_string('wwwrooterror', 'install').'<br />';
        }
    }
    if ($fh) fclose($fh);

    /// check dataroot
    $CFG->dataroot = $INSTALL['dataroot'];
    if (make_upload_directory('sessions', false) === false ) {
        $errormsg = get_string('datarooterror', 'install').'<br />';
    }
    if ($fh) fclose($fh); 

    if (!empty($errormsg)) $nextstage = 2;

    error_reporting(7);
}



//==========================================================================//

/// Check database settings if stage 3 data submitted
/// Try to connect to the database. If that fails then try to create the database

if ($INSTALL['stage'] == 3) {

    if (empty($INSTALL['dbname'])) {
        $INSTALL['dbname'] = 'moodle';
    }
    
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
            $nextstage = 3;
        }
    }

    if (empty($errormsg)) {

        $db = &ADONewConnection($INSTALL['dbtype']);

        error_reporting(0);  // Hide errors 

        if (! $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname'])) {
            /// The following doesn't seem to work but we're working on it
            /// If you come up with a solution for creating a database in MySQL 
            /// feel free to put it in and let us know
            if ($dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'])) {
                switch ($INSTALL['dbtype']) {   /// Try to create a database
                    case 'mysql':
                        if ($db->Execute("CREATE DATABASE {$INSTALL['dbname']};")) {
                            $dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname']);
                        } else {
                            $errormsg = get_string('dbcreationerror', 'install');
                            $nextstage = 3;
                        }
                        break;
                }
            }
        }
    }

    error_reporting(7);

    if (($dbconnected === false) and (empty($errormsg)) ) {
        $errormsg = get_string('dbconnectionerror', 'install');
        $nextstage = 3;
    }
}



//==========================================================================//

/// If the next stage is admin directory settings OR we have just come from there then
/// check the admin directory.
/// If we can open a file then we know that the admin name is correct.

if ($nextstage == 4 or $INSTALL['stage'] == 4) {
    if (!ini_get('allow_url_fopen')) {
        $nextstage = ($goforward) ? 5 : 3;
    } else if (($fh = @fopen($INSTALL['wwwroot'].'/'.$INSTALL['admindirname'].'/site.html', 'r')) !== false) {
        $nextstage = ($goforward) ? 5 : 3;
        fclose($fh);
    } else {
        if ($nextstage != 4) {
            $errormsg = get_string('admindirerror', 'install');
            $nextstage = 4;
        }
    }
}



//==========================================================================//

/// Display or print the data
/// Put the data into a string
/// Try to open config file for writing.

if ($nextstage == 5) {

    $str  = '<?php  /// Moodle Configuration File '."\r\n";
    $str .= "\r\n";

    $str .= 'unset($CFG);'."\r\n";
    $str .= "\r\n";

    $str .= '$CFG->dbtype    = \''.$INSTALL['dbtype']."';\r\n";
    $str .= '$CFG->dbhost    = \''.addslashes($INSTALL['dbhost'])."';\r\n";
    if (!empty($INSTALL['dbname'])) {
        $str .= '$CFG->dbname    = \''.$INSTALL['dbname']."';\r\n";
        $str .= '$CFG->dbuser    = \''.$INSTALL['dbuser']."';\r\n";
        $str .= '$CFG->dbpass    = \''.$INSTALL['dbpass']."';\r\n";
    }
    $str .= '$CFG->dbpersist =  false;'."\r\n";
    $str .= '$CFG->prefix    = \''.$INSTALL['prefix']."';\r\n";
    $str .= "\r\n";

    $str .= '$CFG->wwwroot   = \''.$INSTALL['wwwroot']."';\r\n";
    $str .= '$CFG->dirroot   = \''.$INSTALL['dirroot']."';\r\n";
    $str .= '$CFG->dataroot  = \''.$INSTALL['dataroot']."';\r\n";
    $str .= '$CFG->admin     = \''.$INSTALL['admindirname']."';\r\n";
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



<html dir="<?php echo (get_string('this_direction') == 'rtl') ? 'rtl' : 'ltr' ?>">
<head>
<link rel="shortcut icon" href="theme/standard/favicon.ico" />
<title>Moodle Install</title>
<meta http-equiv="content-type" content="text/html; charset=<?php print_string('thischarset') ?>" />
<?php css_styles() ?>

</head>

<body>


<?php
if (isset($_GET['help'])) {
    print_install_help($_GET['help']);
    close_window_button();
} else {
?>


<table class="main" align="center" cellpadding="3" cellspacing="0">
    <tr>
        <td class="td_mainlogo">
            <p class="p_mainlogo"><img src="pix/moodlelogo-med.gif" width="240" height="60" alt=\"\"></p>
        </td>
        <td class="td_mainlogo" valign="bottom">
            <p class="p_mainheader"><?php print_string('installation', 'install') ?></p>
        </td>
    </tr>

    <tr>
        <td class="td_mainheading" colspan="2">
            <p class="p_mainheading"><?php echo $stagetext[$nextstage] ?></p>
        </td>
    </tr>

    <tr>
        <td class="td_main" colspan="2">
    
<?php

if (!empty($errormsg)) echo "<p class=\"errormsg\" align=\"center\">$errormsg</p>\n";


if ($nextstage == 5) {
    $INSTALL['stage'] = 0;
    $options = array();
    $options['lang'] = $INSTALL['language'];
    if ($configsuccess) {
        echo "<p>".get_string('configfilewritten', 'install')."</p>\n";

        echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "<td width=\"33.3%\">&nbsp;</td>\n";
        echo "<td width=\"33.3%\">&nbsp;</td>\n";
        echo "<td width=\"33.3%\" align=\"right\">\n";        
        print_single_button("index.php", $options, get_string('continue')."  &raquo;");
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";

    } else {
        echo "<p class=\"errormsg\">".get_string('configfilenotwritten', 'install')."</p>";
        
        echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "<td width=\"33.3%\">&nbsp;</td>\n";
        echo "<td width=\"33.3%\" align=\"center\">\n";        
        $installoptions = array();
        $installoptions['download'] = 1; 
        print_single_button("install.php", $installoptions, get_string('download', 'install'));
        echo "</td>\n";
        echo "<td width=\"33.3%\" align=\"right\">\n";        
        print_single_button("index.php", $options, get_string('continue')."  &raquo;");
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";

        echo "<hr />\n";
        echo "<div style=\"text-align: left\">\n";
        print_object(htmlentities($str));
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


function print_object($object) {
    echo "<pre>\n";
    print_r($object);
    echo "</pre>\n";
}



//==========================================================================//

function form_table($nextstage = 0, $formaction = "install.php") {
    global $INSTALL;

    /// standard lines for all forms
?>

    <form name="installform" method="post" action="<?php echo $formaction ?>">
    <input type="hidden" name="stage" value="<?php echo $nextstage ?>" />
    <table class="install_table" cellspacing="3" cellpadding="3" align="center">

<?php
    /// what we do depends on the stage we're at
    switch ($nextstage) {
        case 0: /// Language settings
?>
            <tr>
                <td class="td_left"><p><?php print_string('language') ?></p></td>
                <td class="td_right">
                <?php choose_from_menu (get_list_of_languages(), 'language', $INSTALL['language'], '') ?>
                </td>
            </tr>

<?php
            break;
        case 1: /// Compatibilty check
            $compatsuccess = true;
            
            /// Check that PHP is of a sufficient version
            print_compatibility_row(check_php_version("4.1.0"), get_string('phpversion', 'install'), get_string('phpversionerror', 'install'), 'phpversionhelp');
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
        case 2: /// Directory settings
?>

            <tr>
                <td class="td_left"><p><?php print_string('wwwroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40"name="wwwroot" value="<?php echo $INSTALL['wwwroot'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('dirroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dirroot" value="<?php echo $INSTALL['dirroot'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('dataroot', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dataroot" value="<?php echo $INSTALL['dataroot'] ?>" />
                </td>
            </tr>

<?php
            break;
        case 3: /// Database settings
?>

            <tr>
                <td class="td_left"><p><?php print_string('dbtype', 'install') ?></p></td>
                <td class="td_right">
                <?php choose_from_menu (array("mysql" => "mysql", "postgres7" => "postgres7"), 'dbtype', $INSTALL['dbtype'], '') ?>
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('dbhost', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dbhost" value="<?php echo $INSTALL['dbhost'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('database', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dbname" value="<?php echo $INSTALL['dbname'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('user') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="dbuser" value="<?php echo $INSTALL['dbuser'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('password') ?></p></td>
                <td class="td_right">
                    <input type="password" size="40" name="dbpass" value="<?php echo $INSTALL['dbpass'] ?>" />
                </td>
            </tr>
            <tr>
                <td class="td_left"><p><?php print_string('dbprefix', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="prefix" value="<?php echo $INSTALL['prefix'] ?>" />
                </td>
            </tr>

<?php
            break;
        case 4: /// Administration directory setting
?>

            <tr>
                <td class="td_left"><p><?php print_string('admindirname', 'install') ?></p></td>
                <td class="td_right">
                    <input type="text" size="40" name="admindirname" value="<?php echo $INSTALL['admindirname'] ?>" />
                </td>
            </tr>


<?php
            break;
        default:
    }
?>

    <tr>
        <td colspan="<?php echo ($nextstage == 1) ? '3' : '2'; ?>">

            <?php echo ($nextstage < 5) ? "<input type=\"submit\" name=\"next\" value=\"".get_string('next')."  &raquo;\" style=\"float: right\"/>\n" : "&nbsp;\n" ?>
            <?php echo ($nextstage > 0) ? "<input type=\"submit\" name=\"prev\" value=\"&laquo;  ".get_string('previous')."\" style=\"float: left\"/>\n" : "&nbsp;\n" ?>


        </td>

    </tr>
    
    </table>
    </form>

<?php
}



//==========================================================================//

function print_compatibility_row($success, $testtext, $errormessage, $helpfield='', $caution=false) {
    echo "<tr>\n";
    echo "<td class=\"td_left\" valign=\"top\" nowrap width=\"160\"><p>$testtext</p></td>\n";
    if ($success) {
        echo "<td valign=\"top\"><p class=\"p_pass\">".get_string('pass', 'install')."</p></td>\n";
        echo "<td valign=\"top\">&nbsp;</td>\n";
    } else {
        echo "<td valign=\"top\"";
        echo ($caution) ? "<p class=\"p_caution\">".get_string('caution', 'install') : "<p class=\"p_fail\">".get_string('fail', 'install');
        echo "</p></td>\n";
        echo "<td valign=\"top\">";
        echo "<p>$errormessage ";
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
    echo "<a href=\"javascript: void(0)\">";
    echo "<img src=\"./pix/help.gif\" height=\"17\" width=\"17\" alt=\"$title\"";
    echo "border=\"0\" align=\"middle\" title=\"$title\" ";
    echo "onClick=\"return window.open('$url', 'Help', 'menubar=0,location=0,scrollbars,resizable,width=500,height=400')\">";
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

    /// if limit is already 16M or more then we don't care if we can change it or not
    if ((int)str_replace('M', '', get_memory_limit()) >= 16) {
        return true;
    }

    /// Otherwise, see if we can change it ourselves
    @ini_set('memory_limit', '16M');
    return ((int)str_replace('M', '', get_memory_limit()) >= 16);
}

//==========================================================================//

function css_styles() {
?>

<style type="text/css">

    body { background-color: #ffeece; }
    p, li { 
        font-family: helvetica, arial, sans-serif;
        font-size: 10pt;
    }
    a { text-decoration: none; color: blue; }
    .errormsg {
        color: red;
        font-weight: bold;
    }
    blockquote {
        font-family: courier, monospace;
        font-size: 10pt;
    }
    .install_table {
        width: 500px;
    }
    .td_left {
        text-align: right;
        font-weight: bold;
    }
    .td_right {
        text-align: left;
    }
    .main {
        width: 500px;
        border-width: 1px;
        border-style: solid;
        border-color: #ffc85f;
        -moz-border-radius-bottomleft: 15px;
        -moz-border-radius-bottomright: 15px;
    }
    .td_mainheading {
        background-color: #fee6b9;
        padding: 10px;
    }
    .td_main {
        text-align: center;
    }
    .td_mainlogo {
    }
    .p_mainlogo {
    }
    .p_mainheading {
        font-size: 11pt;
    }
    .p_mainheader{
        text-align: right;
        font-size: 20pt;
        font-weight: bold;
    }
    .p_pass {
        color: green;
        font-weight: bold;
    }
    .p_fail {
        color: red;
        font-weight: bold;
    }
    .p_caution {
        color: #ff6600;
        font-weight: bold;
    }
    .p_help {
        text-align: center;
        font-family: helvetica, arial, sans-serif;
        font-size: 14pt;
        font-weight: bold;
        color: #333333;
    }
        
</style>

<?php
}
?>
