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
 * Private url module utility functions
 *
 * @package    mod_url
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/url/lib.php");

/**
 * This methods does weak url validation, we are looking for major problems only,
 * no strict RFE validation.
 *
 * @param $url
 * @return bool true is seems valid, false if definitely not valid URL
 */
function url_appears_valid_url($url) {
    if (preg_match('/^(\/|https?:|ftp:)/i', $url)) {
        // note: this is not exact validation, we look for severely malformed URLs only
        return (bool) preg_match('/^[a-z]+:\/\/([^:@\s]+:[^@\s]+@)?[^ @]+(:[0-9]+)?(\/[^#]*)?(#.*)?$/i', $url);
    } else {
        return (bool)preg_match('/^[a-z]+:\/\/...*$/i', $url);
    }
}

/**
 * Fix common URL problems that we want teachers to see fixed
 * the next time they edit the resource.
 *
 * This function does not include any XSS protection.
 *
 * @param string $url
 * @return string
 */
function url_fix_submitted_url($url) {
    // note: empty urls are prevented in form validation
    $url = trim($url);

    // remove encoded entities - we want the raw URI here
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');

    if (!preg_match('|^[a-z]+:|i', $url) and !preg_match('|^/|', $url)) {
        // invalid URI, try to fix it by making it normal URL,
        // please note relative urls are not allowed, /xx/yy links are ok
        $url = 'http://'.$url;
    }

    return $url;
}

/**
 * Return full url with all extra parameters
 *
 * This function does not include any XSS protection.
 *
 * @param stdClass $url
 * @param object $cm
 * @param object $course
 * @param object $config
 * @return string url with & encoded as &amp;
 */
function url_get_full_url($url, $cm, $course, $config=null) {

    $parameters = empty($url->parameters) ? [] : (array) unserialize_array($url->parameters);

    // make sure there are no encoded entities, it is ok to do this twice
    $fullurl = html_entity_decode($url->externalurl, ENT_QUOTES, 'UTF-8');

    $letters = '\pL';
    $latin = 'a-zA-Z';
    $digits = '0-9';
    $symbols = '\x{20E3}\x{00AE}\x{00A9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}' .
               '\x{2300}-\x{23FF}\x{2600}-\x{27BF}\x{2B00}-\x{2BF0}';
    $arabic = '\x{FE00}-\x{FEFF}';
    $math = '\x{2190}-\x{21FF}\x{2900}-\x{297F}';
    $othernumbers = '\x{2460}-\x{24FF}';
    $geometric = '\x{25A0}-\x{25FF}';
    $emojis = '\x{1F000}-\x{1F6FF}';

    if (preg_match('/^(\/|https?:|ftp:)/i', $fullurl) or preg_match('|^/|', $fullurl)) {
        // encode extra chars in URLs - this does not make it always valid, but it helps with some UTF-8 problems
        // Thanks to 💩.la emojis count as valid, too.
        $allowed = "[" . $letters . $latin . $digits . $symbols . $arabic . $math . $othernumbers . $geometric .
            $emojis . "]" . preg_quote(';/?:@=&$_.+!*(),-#%', '/');
        $fullurl = preg_replace_callback("/[^$allowed]/u", 'url_filter_callback', $fullurl);
    } else {
        // encode special chars only
        $fullurl = str_replace('"', '%22', $fullurl);
        $fullurl = str_replace('\'', '%27', $fullurl);
        $fullurl = str_replace(' ', '%20', $fullurl);
        $fullurl = str_replace('<', '%3C', $fullurl);
        $fullurl = str_replace('>', '%3E', $fullurl);
    }

    if (!$config) {
        $config = get_config('url');
    }

    // add variable url parameters
    if ($config->allowvariables && !empty($parameters)) {
        $paramvalues = url_get_variable_values($url, $cm, $course, $config);

        foreach ($parameters as $parse=>$parameter) {
            if (isset($paramvalues[$parameter])) {
                $parameters[$parse] = rawurlencode($parse).'='.rawurlencode($paramvalues[$parameter]);
            } else {
                unset($parameters[$parse]);
            }
        }

        if (!empty($parameters)) {
            if (stripos($fullurl, 'teamspeak://') === 0) {
                $fullurl = $fullurl.'?'.implode('?', $parameters);
            } else {
                $join = (strpos($fullurl, '?') === false) ? '?' : '&';
                $fullurl = $fullurl.$join.implode('&', $parameters);
            }
        }
    }

    // encode all & to &amp; entity
    $fullurl = str_replace('&', '&amp;', $fullurl);

    return $fullurl;
}

/**
 * Unicode encoding helper callback
 * @internal
 * @param array $matches
 * @return string
 */
function url_filter_callback($matches) {
    return rawurlencode($matches[0]);
}

/**
 * Print url header.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return void
 */
function url_print_header($url, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$url->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($url);
    echo $OUTPUT->header();
}

/**
 * Get url introduction.
 *
 * @param object $url
 * @param object $cm
 * @param bool $ignoresettings print even if not specified in modedit
 * @return string
 */
function url_get_intro(object $url, object $cm, bool $ignoresettings = false): string {
    $options = empty($url->displayoptions) ? [] : (array) unserialize_array($url->displayoptions);
    if ($ignoresettings or !empty($options['printintro'])) {
        if (trim(strip_tags($url->intro))) {
            return format_module_intro('url', $url, $cm->id);
        }
    }

    return '';
}

/**
 * Display url frames.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function url_display_frame($url, $cm, $course) {
    global $PAGE, $OUTPUT, $CFG;

    $frame = optional_param('frameset', 'main', PARAM_ALPHA);

    if ($frame === 'top') {
        $PAGE->set_pagelayout('frametop');
        $PAGE->activityheader->set_attrs([
            'description' => url_get_intro($url, $cm),
            'title' => format_string($url->name)
        ]);
        url_print_header($url, $cm, $course);
        echo $OUTPUT->footer();
        die;

    } else {
        $config = get_config('url');
        $context = context_module::instance($cm->id);
        $exteurl = url_get_full_url($url, $cm, $course, $config);
        $navurl = "$CFG->wwwroot/mod/url/view.php?id=$cm->id&amp;frameset=top";
        $coursecontext = context_course::instance($course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
        $title = strip_tags($courseshortname.': '.format_string($url->name));
        $framesize = $config->framesize;
        $modulename = s(get_string('modulename','url'));
        $contentframetitle = s(format_string($url->name));
        $dir = get_string('thisdirection', 'langconfig');

        $extframe = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html dir="$dir">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>$title</title>
  </head>
  <frameset rows="$framesize,*">
    <frame src="$navurl" title="$modulename"/>
    <frame src="$exteurl" title="$contentframetitle"/>
  </frameset>
</html>
EOF;

        @header('Content-Type: text/html; charset=utf-8');
        echo $extframe;
        die;
    }
}

/**
 * Print url info and link.
 * @param object $url
 * @param object $cm
 * @param object $course
 */
function url_print_workaround($url, $cm, $course) {
    global $OUTPUT, $PAGE, $USER;

    $PAGE->activityheader->set_description(url_get_intro($url, $cm, true));
    url_print_header($url, $cm, $course);

    $fullurl = new moodle_url(url_get_full_url($url, $cm, $course));

    $display = url_get_final_display_type($url);
    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $jsfullurl = addslashes_js($fullurl->out(false));
        $options = empty($url->displayoptions) ? [] : (array) unserialize_array($url->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $attributes = ['onclick' => "window.open('$jsfullurl', '', '$wh'); return false;"];

    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $attributes = ['onclick' => "this.target='_blank';"];

    } else {
        $attributes = [];
    }

    echo '<div class="urlworkaround">';
    print_string('clicktoopen', 'url', html_writer::link($fullurl, format_string($cm->name), $attributes));
    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

/**
 * Display embedded url file.
 * @param object $url
 * @param object $cm
 * @param object $course
 */
function url_display_embed($url, $cm, $course) {
    global $PAGE, $OUTPUT;

    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);
    $fullurl  = url_get_full_url($url, $cm, $course);
    $title    = $url->name;

    $moodleurl = new moodle_url($fullurl);
    $link = html_writer::link($moodleurl, format_string($cm->name));
    $clicktoopen = get_string('clicktoopen', 'url', $link);

    $extension = resourcelib_get_extension($url->externalurl);

    $mediamanager = core_media_manager::instance($PAGE);
    $embedoptions = array(
        core_media_manager::OPTION_TRUSTED => true,
        core_media_manager::OPTION_BLOCK => true
    );

    if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
        $code = resourcelib_embed_image($fullurl, $title);

    } else if ($mediamanager->can_embed_url($moodleurl, $embedoptions)) {
        // Media (audio/video) file.
        $code = $mediamanager->embed_url($moodleurl, $title, 0, 0, $embedoptions);

    } else {
        // anything else - just try object tag enlarged as much as possible
        $code = resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype);
    }

    $PAGE->activityheader->set_description(url_get_intro($url, $cm));
    url_print_header($url, $cm, $course);

    echo $code;

    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best display format.
 * @param object $url
 * @return int display type constant
 */
function url_get_final_display_type($url) {
    global $CFG;

    if ($url->display != RESOURCELIB_DISPLAY_AUTO) {
        return $url->display;
    }

    // detect links to local moodle pages
    if (strpos($url->externalurl, $CFG->wwwroot) === 0) {
        if (strpos($url->externalurl, 'file.php') === false and strpos($url->externalurl, '.php') !== false ) {
            // most probably our moodle page with navigation
            return RESOURCELIB_DISPLAY_OPEN;
        }
    }

    // Binaries and other formats that are known to cause trouble for external links.
    static $download = ['application/zip', 'application/x-tar', 'application/g-zip',
                        'application/pdf', 'text/html', 'document/unknown'];
    static $embed    = array('image/gif', 'image/jpeg', 'image/png', 'image/svg+xml',         // images
                             'application/x-shockwave-flash', 'video/x-flv', 'video/x-ms-wm', // video formats
                             'video/quicktime', 'video/mpeg', 'video/mp4',
                             'audio/mp3', 'audio/x-realaudio-plugin', 'x-realaudio-plugin',   // audio formats,
                            );

    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);

    if (in_array($mimetype, $download)) {
        return RESOURCELIB_DISPLAY_DOWNLOAD;
    }
    if (in_array($mimetype, $embed)) {
        return RESOURCELIB_DISPLAY_EMBED;
    }

    // let the browser deal with it somehow
    return RESOURCELIB_DISPLAY_OPEN;
}

/**
 * Get the parameters that may be appended to URL
 * @param object $config url module config options
 * @return array array describing opt groups
 */
function url_get_variable_options($config) {
    global $CFG;

    $options = array();
    $options[''] = array('' => get_string('chooseavariable', 'url'));

    $options[get_string('course')] = array(
        'courseid'        => 'id',
        'coursefullname'  => get_string('fullnamecourse'),
        'courseshortname' => get_string('shortnamecourse'),
        'courseidnumber'  => get_string('idnumbercourse'),
        'coursesummary'   => get_string('summary'),
        'courseformat'    => get_string('format'),
    );

    $options[get_string('modulename', 'url')] = array(
        'urlinstance'     => 'id',
        'urlcmid'         => 'cmid',
        'urlname'         => get_string('name'),
        'urlidnumber'     => get_string('idnumbermod'),
    );

    $options[get_string('miscellaneous')] = array(
        'sitename'        => get_string('fullsitename'),
        'serverurl'       => get_string('serverurl', 'url'),
        'currenttime'     => get_string('time'),
        'lang'            => get_string('language'),
    );
    if (!empty($config->secretphrase)) {
        $options[get_string('miscellaneous')]['encryptedcode'] = get_string('encryptedcode');
    }

    $options[get_string('user')] = array(
        'userid'          => 'id',
        'userusername'    => get_string('username'),
        'useridnumber'    => get_string('idnumber'),
        'userfirstname'   => get_string('firstname'),
        'userlastname'    => get_string('lastname'),
        'userfullname'    => get_string('fullnameuser'),
        'useremail'       => get_string('email'),
        'userphone1'      => get_string('phone1'),
        'userphone2'      => get_string('phone2'),
        'userinstitution' => get_string('institution'),
        'userdepartment'  => get_string('department'),
        'useraddress'     => get_string('address'),
        'usercity'        => get_string('city'),
        'usertimezone'    => get_string('timezone'),
    );

    if ($config->rolesinparams) {
        $roles = role_fix_names(get_all_roles());
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions['course'.$role->shortname] = get_string('yourwordforx', '', $role->localname);
        }
        $options[get_string('roles')] = $roleoptions;
    }

    return $options;
}

/**
 * Get the parameter values that may be appended to URL
 * @param object $url module instance
 * @param object $cm
 * @param object $course
 * @param object $config module config options
 * @return array of parameter values
 */
function url_get_variable_values($url, $cm, $course, $config) {
    global $USER, $CFG;

    $site = get_site();

    $coursecontext = context_course::instance($course->id);

    $values = array (
        'courseid'        => $course->id,
        'coursefullname'  => format_string($course->fullname, true, array('context' => $coursecontext)),
        'courseshortname' => format_string($course->shortname, true, array('context' => $coursecontext)),
        'courseidnumber'  => $course->idnumber,
        'coursesummary'   => $course->summary,
        'courseformat'    => $course->format,
        'lang'            => current_language(),
        'sitename'        => format_string($site->fullname, true, array('context' => $coursecontext)),
        'serverurl'       => $CFG->wwwroot,
        'currenttime'     => time(),
        'urlinstance'     => $url->id,
        'urlcmid'         => $cm->id,
        'urlname'         => format_string($url->name, true, array('context' => $coursecontext)),
        'urlidnumber'     => $cm->idnumber,
    );

    if (isloggedin()) {
        $values['userid']          = $USER->id;
        $values['userusername']    = $USER->username;
        $values['useridnumber']    = $USER->idnumber;
        $values['userfirstname']   = $USER->firstname;
        $values['userlastname']    = $USER->lastname;
        $values['userfullname']    = fullname($USER);
        $values['useremail']       = $USER->email;
        $values['userphone1']      = $USER->phone1;
        $values['userphone2']      = $USER->phone2;
        $values['userinstitution'] = $USER->institution;
        $values['userdepartment']  = $USER->department;
        $values['useraddress']     = $USER->address;
        $values['usercity']        = $USER->city;
        $now = new DateTime('now', core_date::get_user_timezone_object());
        $values['usertimezone']    = $now->getOffset() / 3600.0; // Value in hours for BC.
    }

    // weak imitation of Single-Sign-On, for backwards compatibility only
    // NOTE: login hack is not included in 2.0 any more, new contrib auth plugin
    //       needs to be createed if somebody needs the old functionality!
    if (!empty($config->secretphrase)) {
        $values['encryptedcode'] = url_get_encrypted_parameter($url, $config);
    }

    //hmm, this is pretty fragile and slow, why do we need it here??
    if ($config->rolesinparams) {
        $coursecontext = context_course::instance($course->id);
        $roles = role_fix_names(get_all_roles($coursecontext), $coursecontext, ROLENAME_ALIAS);
        foreach ($roles as $role) {
            $values['course'.$role->shortname] = $role->localname;
        }
    }

    return $values;
}

/**
 * BC internal function
 * @param object $url
 * @param object $config
 * @return string
 */
function url_get_encrypted_parameter($url, $config) {
    global $CFG;

    if (file_exists("$CFG->dirroot/local/externserverfile.php")) {
        require_once("$CFG->dirroot/local/externserverfile.php");
        if (function_exists('extern_server_file')) {
            return extern_server_file($url, $config);
        }
    }
    return md5(getremoteaddr().$config->secretphrase);
}

/**
 * Optimised mimetype detection from general URL
 * @param $fullurl
 * @param null $unused This parameter has been deprecated since 4.3 and should not be used anymore.
 * @return string|null mimetype or null when the filetype is not relevant.
 */
function url_guess_icon($fullurl, $unused = null) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    if ($unused !== null) {
        debugging('Deprecated argument passed to ' . __FUNCTION__, DEBUG_DEVELOPER);
    }

    if (substr_count($fullurl, '/') < 3 or substr($fullurl, -1) === '/') {
        // Most probably default directory - index.php, index.html, etc. Return null because
        // we want to use the default module icon instead of the HTML file icon.
        return null;
    }

    try {
        // There can be some cases where the url is invalid making parse_url() to return false.
        // That will make moodle_url class to throw an exception, so we need to catch the exception to prevent errors.
        $moodleurl = new moodle_url($fullurl);
        $fullurl = $moodleurl->out_omit_querystring();
    } catch (\moodle_exception $e) {
        // If an exception is thrown, means the url is invalid. No need to log exception.
        return null;
    }

    $icon = file_extension_icon($fullurl);
    $htmlicon = file_extension_icon('.htm');
    $unknownicon = file_extension_icon('');
    $phpicon = file_extension_icon('.php'); // Exception for php files.

    // We do not want to return those icon types, the module icon is more appropriate.
    if ($icon === $unknownicon || $icon === $htmlicon || $icon === $phpicon) {
        return null;
    }

    return $icon;
}
