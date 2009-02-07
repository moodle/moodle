<?php  //$Id$

/**
 * Functions to support installation process
 */

define('INSTALL_WELCOME',       0);
define('INSTALL_ENVIRONMENT',   1);
define('INSTALL_PATHS',         2);
define('INSTALL_DOWNLOADLANG',  3);
define('INSTALL_DATABASETYPE',  4);
define('INSTALL_DATABASE',      5);
define('INSTALL_SAVE',          6);

/**
 *Tries to detect the right www root setting.
 *
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

function install_ini_get_bool($ini_get_arg) {
    $temp = ini_get($ini_get_arg);

    if ($temp == '1' or strtolower($temp) == 'on') {
        return true;
    }
    return false;
}

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

function install_db_validate($database, $dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions, $distro) {
    // this is in function because we want the /install.php to parse in PHP4

    if ($database->get_dbfamily() === 'mysql' and !empty($distro->setdbrootpassword) and $dbpass !== '') {
        // distro hack - set root password
        try {
            if ($database->connect($dbhost, $dbuser, '', $dbname, $prefix, $dboptions)) {
                $sql = "UPDATE user SET password=password(?) WHERE user='root'";
                $params = array($dbpass);
                $database->execute($sql, $params);
                return '';
            }
        } catch (Exception $ignored) {
        }
    }

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
 * This function returns a list of languages and their full names. The
 * list of available languages is fetched from install/lang/xx/installer.php
 * and it's used exclusively by the installation process
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function install_get_list_of_languages() {
    global $CFG;

    $languages = array();

/// Get raw list of lang directories
    $langdirs = get_list_of_plugins('install/lang');
    asort($langdirs);
/// Get some info from each lang
    foreach ($langdirs as $lang) {
        if ($lang == 'en') {
            continue;
        }
        if (file_exists($CFG->dirroot.'/install/lang/'.$lang.'/installer.php')) {
            $string = array();
            include($CFG->dirroot.'/install/lang/'.$lang.'/installer.php');
            if (substr($lang, -5) === '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
            if (!empty($string['thislanguage'])) {
                $languages[$lang] = $string['thislanguage'].' ('.$shortlang.')';
            }
        }
    }
/// Return array
    return $languages;
}

function install_print_help_page($help) {
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
          <link rel="shortcut icon" href="theme/standard/favicon.ico" />
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
    close_window_button();
    echo '</body></html>';
    die;
}

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
          <link rel="shortcut icon" href="theme/standard/favicon.ico" />';

    $sheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz');
    $csss = array();
    foreach ($sheets as $sheet) {
        $csss[] = $CFG->wwwroot.'/theme/standard/'.$sheet.'.css';
    }
    $sheets = array('gradients');
    foreach ($sheets as $sheet) {
        $csss[] = $CFG->wwwroot.'/theme/standardwhite/'.$sheet.'.css';
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
                <div id="header" class=" clearfix"><h1 class="headermain">'.get_string('installation','install').'</h1>
                    <div class="headermenu">&nbsp;</div></div><div class="navbar clearfix">
                    <div class="breadcrumb">
                        <ul><li class="first">'.$stagename.'</li></ul>
                    </div>
                    <div class="navbutton">&nbsp;</div>
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


function install_css_styles() {
    global $CFG;

    @header('Content-type: text/css');  // Correct MIME type
    @header('Cache-Control: no-store, no-cache, must-revalidate');
    @header('Cache-Control: post-check=0, pre-check=0', false);
    @header('Pragma: no-cache');
    @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
    @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

//TODO: add rtl support here

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
