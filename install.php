<?php /// $Id$
      /// install.php - helps admin user to create a config.php file

/// If config.php exists already then we are not needed.

if (file_exists('./config.php')) { 
    header('Location: index.php');
} else {
    $configfile = './config.php';
}

///==========================================================================//
/// We are doing this in stages
/// 1. Welcome and language settings
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

if ( empty($INSTALL['language']) and empty($_POST['language']) ) {   // First time through this script

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

    $INSTALL['stage'] = 1;
}



//==========================================================================//

/// Fake some settings so that we can use selected functions from moodlelib.php and weblib.php
$SESSION->lang = (!empty($_POST['language'])) ? $_POST['language'] : $INSTALL['language'];
$CFG->dirroot = $INSTALL['dirroot'];
$CFG->dataroot = $INSTALL['dataroot'];
$CFG->directorypermissions = 0777;


require_once('./lib/moodlelib.php');
require_once('./lib/weblib.php');
require_once('./lib/adodb/adodb.inc.php');


/// guess the www root
if ($INSTALL['wwwroot'] == '') {
    list($INSTALL['wwwroot'], $xtra) = explode('/install.php', qualified_me());
}

$stagetext = array(1 => get_string('chooselanguage', 'install'),
                          get_string('directorysettings', 'install'),
                          get_string('databasesettings', 'install'),
                          get_string('admindirsetting', 'install'),
                          get_string('configurationcomplete', 'install')
                    );


//==========================================================================//

/// Was data submitted?
if (!empty($_POST['stage'])) {

    /// Get the stage for which the form was set and the next stage we are going to


    if ( $goforward = (! empty( $_POST['next'] )) ) {
        $nextstage = $_POST['stage'] + 1;
    } else {
        $nextstage = $_POST['stage'] - 1;
    }
    
    if ($nextstage < 1) $nextstage = 1;
    

    /// Store any posted data
    foreach ($_POST as $setting=>$value) {
        $INSTALL[$setting] = $value;
    }
    
} else {

    $goforward = true;
    $nextstage = 1;
    
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
    
    /// different format for postgres7
    if ($INSTALL['dbtype'] == 'postgres7') {
        $INSTALL['dbhost'] = "user='{$INSTALL['dbuser']}' password='{$INSTALL['dbpass']}' dbname='{$INSTALL['dbname']}'";
        if ($INSTALL['prefix'] == '') { /// must have a prefix
            $INSTALL['prefix'] = 'mdl_';
        }
    }

    $db = &ADONewConnection($INSTALL['dbtype']);

    error_reporting(0);  // Hide errors 
    
    if (!($dbconnected = $db->Connect($INSTALL['dbhost'],$INSTALL['dbuser'],$INSTALL['dbpass'],$INSTALL['dbname']))) {
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

    if (( $configsuccess = ($fh = @fopen($configfile, 'w')) ) !== false) {
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

<h1 align="center">MOODLE INSTALL</h1>

<table class="main" align="center" cellpadding="3" cellspacing="0">
    <tr>
        <td class="td_mainheading"><p class="p_mainheading"><?php echo $stagetext[$nextstage] ?></p></td>
    </tr>

    <tr>
        <td class="td_main">
    
<?php

if (!empty($errormsg)) echo "<p class=\"errormsg\" align=\"center\">$errormsg</p>\n";


if ($nextstage == 5) {
    if ($configsuccess) {
        echo "<p>".get_string('configfilewritten', 'install')."</p>\n";
    } else {
        echo "<p>".get_string('configfilenotwritten', 'install')."</p>";
        echo "<hr />\n";
        echo "<div style=\"text-align: left\">\n";
        print_object(htmlentities($str));
        echo "</div>\n";
        echo "<hr />\n";
    }
    echo "<p>(<a href=\"index.php?lang={$INSTALL['language']}\">Continue</a>)</p>\n";
} else {
    $formaction = (isset($_GET['configfile'])) ? "install.php?configfile=".$_GET['configfile'] : "install.php";
    form_table($nextstage, $formaction);
}

?>

        </td>
    </tr>
</table>

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

function form_table($nextstage = 1, $formaction = "install.php") {
    global $INSTALL;

    /// standard lines for all forms
?>

    <form name="installform" method="post" action="<?php echo $formaction ?>">
    <input type="hidden" name="stage" value="<?php echo $nextstage ?>" />
    <table class="install_table" cellspacing="3" cellpadding="3" align="center">

<?php
    /// what we do depends on the stage we're at
    switch ($nextstage) {
        case 1: /// Language settings
?>
            <tr>
                <td class="td_left"><p><?php print_string('language') ?></p></td>
                <td class="td_right">
                <?php choose_from_menu (get_list_of_languages(), 'language', $INSTALL['language'], '') ?>
                </td>
            </tr>

<?php
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

    <tr>
        <td align="left">
            <?php echo ($nextstage > 1) ? "<input type=\"submit\" name=\"prev\" value=\"".get_string('previous')."\" />\n" : "&nbsp;\n" ?>
        </td>
        <td align="right">
            <?php echo ($nextstage < 5) ? "<input type=\"submit\" name=\"next\" value=\"".get_string('next')."\" />\n" : "&nbsp;\n" ?>
        </td>
    </tr>
    
    </table>
    </form>

<?php
}



//==========================================================================//

function css_styles() {
?>

<style type="text/css">

    body { background-color: #ffeece; }
    p { font-family: helvetica, arial, sans-serif; size: normal; }
    a { text-decoration: none; color: blue; }
    .errormsg {
        color: red;
        font-weight: bold;
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
    }
    .td_main {
        text-align: center;
    }
    .p_mainheading {
        font-size: 10pt;
    }
        
    
</style>

<?php
}
?>
