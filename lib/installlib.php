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
 * @package    moodlecore
 * @subpackage install
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
        return get_string($ex->errorcode, $ex->module, $ex->a).'<br />'.$ex->debuginfo;
    }
}

/**
 * Returns content of config.php file.
 *
 * Uses PHP_EOL for generating proper end of lines for the given platform.
 *
 * @param moodle_database $database database instance
 * @param object $cfg copy of $CFG
 * @param bool $userealpath allows symbolic links in dirroot
 * @return string
 */
function install_generate_configphp($database, $cfg, $userealpath=false) {
    $configphp = '<?php  // Moodle Configuration File ' . PHP_EOL . PHP_EOL;

    $configphp .= 'unset($CFG);' . PHP_EOL;
    $configphp .= '$CFG = new stdClass();' . PHP_EOL . PHP_EOL; // prevent PHP5 strict warnings

    $dbconfig = $database->export_dbconfig();

    foreach ($dbconfig as $key=>$value) {
        $key = str_pad($key, 9);
        $configphp .= '$CFG->'.$key.' = '.var_export($value, true) . ';' . PHP_EOL;
    }
    $configphp .= PHP_EOL;

    $configphp .= '$CFG->wwwroot   = '.var_export($cfg->wwwroot, true) . ';' . PHP_EOL ;

    if ($userealpath) {
        $dirroot = str_replace('\\', '/', $cfg->dirroot); // win32 fix
        $dirroot = rtrim($dirroot, '/');  // no trailing /
        $configphp .= '$CFG->dirroot   = realpath('.var_export($dirroot, true).');' . PHP_EOL; // fix for sym links
    } else {
        $dirroot = str_replace('\\', '/', $cfg->dirroot); // win32 fix
        $dirroot = rtrim($dirroot, '/');  // no trailing /
        $configphp .= '$CFG->dirroot   = '.var_export($dirroot, true) . ';' . PHP_EOL;
    }

    $dataroot = str_replace('\\', '/', $cfg->dataroot); // win32 fix
    $dataroot = rtrim($dataroot, '/');  // no trailing /
    $configphp .= '$CFG->dataroot  = '.var_export($dataroot, true) . ';' . PHP_EOL;

    $configphp .= '$CFG->admin     = '.var_export($cfg->admin, true) . ';' . PHP_EOL . PHP_EOL;

    $configphp .= '$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode' . PHP_EOL . PHP_EOL;

    $configphp .= '$CFG->passwordsaltmain = '.var_export(complex_random_string(), true) . ';' . PHP_EOL . PHP_EOL;

    $configphp .= 'require_once("$CFG->dirroot/lib/setup.php");' . PHP_EOL . PHP_EOL;
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
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html dir="'.(right_to_left() ? 'rtl' : 'ltr').'">
          <head>
          <link rel="shortcut icon" href="theme/standard/pix/favicon.ico" />
          <link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/install.php?css=1" />
          <title>'.get_string('installation','install').'</title>
          <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
          <meta http-equiv="pragma" content="no-cache" />
          <meta http-equiv="expires" content="0" />';

    echo '</head><body>';
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
    echo $OUTPUT->close_window_button(); //TODO: MUST NOT USE $OUTPUT HERE!!!
    echo '</body></html>';
    die;
}

/**
 * Prints installation page header, we can no use weblib yet in isntaller.
 *
 * @global object
 * @param array $config
 * @param string $stagename
 * @param string $heading
 * @param string $stagetext
 * @return void
 */
function install_print_header($config, $stagename, $heading, $stagetext) {
    global $CFG;

    @header('Content-Type: text/html; charset=UTF-8');
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html dir="'.(right_to_left() ? 'rtl' : 'ltr').'">
          <head>
          <link rel="shortcut icon" href="theme/standard/pix/favicon.ico" />';

    $sheets = array('pagelayout','core');
    $csss = array();
    foreach ($sheets as $sheet) {
        $csss[] = $CFG->wwwroot.'/theme/base/style/'.$sheet.'.css';
    }
    $sheets = array('core', 'css3');
    foreach ($sheets as $sheet) {
        $csss[] = $CFG->wwwroot.'/theme/standard/style/'.$sheet.'.css';
    }
    foreach ($csss as $css) {
        echo '<link rel="stylesheet" type="text/css" href="'.$css.'" />'."\n";
    }

    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/install.php?css=1" />
          <title>'.get_string('installation','install').' - Moodle '.$CFG->target_release.'</title>
          <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
          <meta http-equiv="pragma" content="no-cache" />
          <meta http-equiv="expires" content="0" />';

    echo '</head><body class="notloggedin">
            <div id="page" class="stage'.$config->stage.'">
                <div id="page-header">
                    <div id="header" class=" clearfix">
                        <h1 class="headermain">'.get_string('installation','install').'</h1>
                        <div class="headermenu">&nbsp;</div>
                    </div>
                    <div class="navbar clearfix">
                        <div class="breadcrumb">
                            <ul><li class="first">'.$stagename.'</li></ul>
                        </div>
                        <div class="navbutton">&nbsp;</div>
                    </div>
                </div>
          <!-- END OF HEADER -->
          <div id="installdiv">';

    echo '<h2>'.$heading.'</h2>';

    if ($stagetext !== '') {
        echo '<div class="stage generalbox box">';
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
        $first = '<input type="submit" id="previousbutton" name="previous" value="&laquo; '.s(get_string('previous')).'" />';
    } else {
        $first = '<input type="submit" id="previousbutton" name="next" value="'.s(get_string('reload', 'admin')).'" />';
        $first .= '<script type="text/javascript">
//<![CDATA[
    var first = document.getElementById("previousbutton");
    first.style.visibility = "hidden";
//]]>
</script>
';
    }

    if ($reload) {
        $next = '<input type="submit" id="nextbutton" name="next" value="'.s(get_string('reload', 'admin')).'" />';
    } else {
        $next = '<input type="submit" id="nextbutton" name="next" value="'.s(get_string('next')).' &raquo;" />';
    }

    echo '</fieldset><fieldset id="nav_buttons">'.$first.$next.'</fieldset>';

    $homelink  = '<div class="sitelink">'.
       '<a title="Moodle '. $CFG->target_release .'" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">'.
       '<img style="width:100px;height:30px" src="pix/moodlelogo.gif" alt="moodlelogo" /></a></div>';

    echo '</form></div>';
    echo '<div id="footer"><hr />'.$homelink.'</div>';
    echo '</div></body></html>';
}


/**
 * Prints css needed on installation page, tries to look like the rest of installation.
 * Does not return.
 *
 * @global object
 */
function install_css_styles() {
    global $CFG;

    @header('Content-type: text/css');  // Correct MIME type
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

//TODO: add rtl support here, make it match new default theme MDL-21149

    echo '

h2 {
  text-align:center;
}

#installdiv {
  width: 800px;
  margin-left:auto;
  margin-right:auto;
}

#installdiv dt {
  font-weight: bold;
}

#installdiv dd {
  padding-bottom: 0.5em;
}

.stage {
  margin-top: 2em;
  margin-bottom: 2em;
  width: 100%;
  padding:25px;
}

#installform {
  width: 100%;
}

#nav_buttons input {
  margin: 5px;
}

#envresult {
  text-align:left;
  width: auto;
  margin-left:10em;
}

#envresult dd {
  color: red;
}

.formrow {
  clear:both;
  text-align:left;
  padding: 8px;
}

.formrow label.formlabel {
  display:block;
  float:left;
  width: 260px;
  margin-right:5px;
  text-align:right;
}

.formrow .forminput {
  display:block;
  float:left;
}

fieldset {
  text-align:center;
  border:none;
}

.hint {
  display:block;
  clear:both;
  padding-left: 265px;
  color: red;
}

.configphp {
  text-align:left;
  background-color:white;
  padding:1em;
  width:95%;
}

.stage6 .stage {
  font-weight: bold;
  color: red;
}

';

    die;
}
