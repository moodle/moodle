<?php /// $Id$
      /// install.php - helps admin user to create a config.php file

/// If config.php exists already then we are not needed.

if (file_exists('./config.php')) {
    header('Location: index.php');
    die();
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
$CFG->directorypermissions = 0777;


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

/// Any special action we need to take?

if(isset($_POST['specialaction'])) {
    switch($_POST['specialaction']) {
        case 'downloadconfig':
            $str = generate_config_php();
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="config.php"');
            header('Content-Length: '.strlen($str));
            header('Connection: Close');
            echo $str;
            die();
        break;
    }
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

    /// check wwwroot
    if (($fh = @fopen($INSTALL['wwwroot'].'/install.php', 'r')) === false) {
        $errormsg = get_string('wwwrooterror', 'install');
    } else {
        fclose($fh);

        /// check dirroot
        if (($fh = @fopen($INSTALL['dirroot'].'/install.php', 'r')) === false ) {
            $CFG->dirroot = dirname(__FILE__);
            $INSTALL['dirroot'] = dirname(__FILE__);
            $errormsg = get_string('dirrooterror', 'install');
        } else {
            fclose($fh);

            $CFG->dirroot = $INSTALL['dirroot'];

            /// check dataroot
            $CFG->dataroot = $INSTALL['dataroot'];
            if (make_upload_directory('sessions', false) === false ) {
                $errormsg = get_string('datarooterror', 'install');
            }
        }
    }


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
    if (($fh = @fopen($INSTALL['wwwroot'].'/'.$INSTALL['admindirname'].'/site.html', 'r')) !== false) {
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
    if (( $configsuccess = ($fh = @fopen($configfile, 'w')) ) !== false) {
        $str = generate_config_php();
        fwrite($fh, $str);
        fclose($fh);
    }
}



//==========================================================================//

?>



<html dir="<?php echo (get_string('this_direction') == 'rtl') ? 'rtl' : 'ltr' ?>">
<head>
<link rel="shortcut icon" href="http://moodle.dougiamas.net/theme/standard/favicon.ico" />
<title>Moodle Install</title>
<meta http-equiv="content-type" content="text/html; charset=<?php print_string('thischarset') ?>" />
<?php css_styles() ?>

</head>

<body>

<table align="center">
</table>


<?php
if (isset($_GET['help'])) {
    print_install_help($_GET['help']);
} else {
?>


<table class="main" align="center" cellpadding="3" cellspacing="0">
    <tr>
        <td class="td_mainlogo">
            <p class="p_mainlogo"><img src="pix/moodlelogo-med.gif" width="240" height="60"></p>
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
    if ($configsuccess) {
        echo "<p>".get_string('configfilewritten', 'install')."</p>\n";
    } else {
        echo "<p>".get_string('configfilenotwritten', 'install')."</p>";
        echo '<form name="installform" method="post" action="install.php"><p>';
        echo '<input type="hidden" name="specialaction" value="downloadconfig" />';
        echo '<input type="submit" name="download" value="'.get_string('downloadconfigphp').'" />';
        echo '</p></form>';
        echo "<hr />\n";
    }
    $options = array();
    $options['lang'] = $INSTALL['language'];
    print_single_button("index.php", $options, get_string('continue')."  &raquo;");
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
            print_compatibility_row(check_php_version("4.1.0"), get_string('PHPversion', 'install'), get_string('PHPversionerror', 'install'), 'phpversionhelp');
            /// Check safe mode
            print_compatibility_row(!ini_get_bool('safe_mode'), get_string('safemode', 'install'), get_string('safemodeerror', 'install'), 'safemodehelp');
            /// Check session auto start
            print_compatibility_row(!ini_get_bool('session.auto_start'), get_string('sessionautostart', 'install'), get_string('sessionautostarterror', 'install'), 'sessionautostarthelp');
            /// Check session save path
            print_compatibility_row(!ini_get_bool('session.save_path'), get_string('sessionsavepath', 'install'), get_string('sessionsavepatherror', 'install'), 'sessionsavepathhelp');
            /// Check magic quotes
            print_compatibility_row(!ini_get_bool('magic_quotes_runtime'), get_string('magicquotesruntime', 'install'), get_string('magicquotesruntimeerror', 'install'), 'magicquotesruntimehelp');
            /// Check file uploads
            print_compatibility_row(ini_get_bool('file_uploads'), get_string('fileuploads', 'install'), get_string('fileuploadserror', 'install'), 'fileuploadshelp');
            /// Check GD version
            print_compatibility_row(check_gd_version(), get_string('gdversion', 'install'), get_string('gdversionerror', 'install'), 'gdversionhelp');
            /// Check memory limit
            print_compatibility_row(check_memory_limit(), get_string('memorylimit', 'install'), get_string('memorylimiterror', 'install'), 'memorylimithelp');


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
                    <input type="text" size="40" name="dbpass" value="<?php echo $INSTALL['dbpass'] ?>" />
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
    </table>

    <div class="nav">
        <div style="float: right;"><?php echo ($nextstage < 5) ? "<input type=\"submit\" name=\"next\" value=\"".get_string('next')."  &raquo;\" />\n" : "&nbsp;\n" ?></div>
        <div style="float: left;"><?php echo ($nextstage > 0) ? "<input type=\"submit\" name=\"prev\" value=\"&laquo;  ".get_string('previous')."\" />\n" : "&nbsp;\n" ?></div>
        <div style="clear: left; height: 2px;">&nbsp;</div>
    </div>


    </form>

<?php
}



//==========================================================================//

function print_compatibility_row($success, $testtext, $errormessage, $helpfield='') {
    echo "<tr>\n";
    echo "<td class=\"td_left\" valign=\"top\" nowrap><p>$testtext</p></td>\n";
    if ($success) {
         echo "<td valign=\"top\"><p class=\"p_pass\">".get_string('pass', 'install')."</p></td>\n";
         echo "<td valign=\"top\">&nbsp;</td>\n";
    } else {
         echo "<td valign=\"top\"><p class=\"p_fail\">".get_string('fail', 'install')."</p></td>\n";
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
    echo "<img src=\"./pix/help.gif\" height=\"17\" width=\"22\" alt=\"$title\"";
    echo "border=\"0\" align=\"absmiddle\" title=\"$title\" ";
    echo "onClick=\"return window.open('$url', 'Help', 'menubar=0,location=0,scrollbars,resizable,width=500,height=400')\">";
    echo "</a>\n";
}



//==========================================================================//

function print_install_help($help) {
    echo "<p class=\"p_help\">";
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
    echo "</p>\n";
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

function generate_config_php() {
    global $INSTALL;

    $str  = '<?php  /// Moodle Configuration File '."\n";
    $str .= "\n";

    $str .= 'unset($CFG);'."\n";
    $str .= "\n";

    $str .= '$CFG->dbtype    = \''.$INSTALL['dbtype']."';\n";
    $str .= '$CFG->dbhost    = \''.$INSTALL['dbhost']."';\n";
    if ($INSTALL['dbtype'] == 'mysql') {
        $str .= '$CFG->dbname    = \''.$INSTALL['dbname']."';\n";
        $str .= '$CFG->dbuser    = \''.$INSTALL['dbuser']."';\n";
        $str .= '$CFG->dbpass    = \''.$INSTALL['dbpass']."';\n";
    }
    $str .= '$CFG->dbpersist =  false;'."\n";
    $str .= '$CFG->prefix    = \''.$INSTALL['prefix']."';\n";
    $str .= "\n";

    $str .= '$CFG->wwwroot   = \''.$INSTALL['wwwroot']."';\n";
    $str .= '$CFG->dirroot   = \''.$INSTALL['dirroot']."';\n";
    $str .= '$CFG->dataroot  = \''.$INSTALL['dataroot']."';\n";
    $str .= "\n";

    $str .= '$CFG->directorypermissions = 0777;'."\n";
    $str .= "\n";

    $str .= 'require_once("$CFG->dirroot/lib/setup.php");'."\n";
    $str .= '// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,'."\n";
    $str .= '// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.'."\n";
    $str .= '?>';

    return $str;
}


//==========================================================================//

function css_styles() {
?>

<style type="text/css">

    body { background-color: #ffeece; }
    p {
        font-family: helvetica, arial, sans-serif;
        font-size: 10pt;
    }
    a { text-decoration: none; color: blue; }
    .errormsg {
        color: red;
        font-weight: bold;
    }
    blockquote {
        font-family: helvetica, arial, sans-serif;
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
    .p_help {
        text-align: center;
        font-family: helvetica, arial, sans-serif;
        font-size: 14pt;
        font-weight: bold;
        color: #333333;
    }
    div.nav {
        margin: 6px;
    }

</style>

<?php
}
?>
