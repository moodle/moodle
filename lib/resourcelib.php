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
 * Recourse module like helper functions
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Try the best way */
define('RESOURCELIB_DISPLAY_AUTO', 0);
/** Display using object tag */
define('RESOURCELIB_DISPLAY_EMBED', 1);
/** Display inside frame */
define('RESOURCELIB_DISPLAY_FRAME', 2);
/** Display normal link in new window */
define('RESOURCELIB_DISPLAY_NEW', 3);
/** Force download of file instead of display */
define('RESOURCELIB_DISPLAY_DOWNLOAD', 4);
/** Open directly */
define('RESOURCELIB_DISPLAY_OPEN', 5);
/** Open in "emulated" pop-up without navigation */
define('RESOURCELIB_DISPLAY_POPUP', 6);

/** Legacy files not needed or new resource */
define('RESOURCELIB_LEGACYFILES_NO', 0);
/** Legacy files conversion marked as completed */
define('RESOURCELIB_LEGACYFILES_DONE', 1);
/** Legacy files conversion in progress*/
define('RESOURCELIB_LEGACYFILES_ACTIVE', 2);


/**
 * Try on demand migration of file from old course files
 * @param string $filepath old file path
 * @param int $cmid migrated course module if
 * @param int $courseid
 * @param string $component
 * @param string $filearea new file area
 * @param int $itemid migrated file item id
 * @return mixed, false if not found, stored_file instance if migrated to new area
 */
function resourcelib_try_file_migration($filepath, $cmid, $courseid, $component, $filearea, $itemid) {
    $fs = get_file_storage();

    if (stripos($filepath, '/backupdata/') === 0 or stripos($filepath, '/moddata/') === 0) {
        // do not steal protected files!
        return false;
    }

    if (!$context = context_module::instance($cmid)) {
        return false;
    }
    if (!$coursecontext = context_course::instance($courseid)) {
        return false;
    }

    $fullpath = rtrim("/$coursecontext->id/course/legacy/0".$filepath, '/');
    do {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            if ($file = $fs->get_file_by_hash(sha1("$fullpath/.")) and $file->is_directory()) {
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
                    break;
                }
            }
            return false;
        }
    } while (false);

    // copy and keep the same path, name, etc.
    $file_record = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid);
    try {
        return $fs->create_file_from_storedfile($file_record, $file);
    } catch (Exception $e) {
        // file may exist - highly unlikely, we do not want upgrades to stop here
        return false;
    }
}

/**
 * Returns list of available display options
 * @param array $enabled list of options enabled in module configuration
 * @param int $current current display options for existing instances
 * @return array of key=>name pairs
 */
function resourcelib_get_displayoptions(array $enabled, $current=null) {
    if (is_number($current)) {
        $enabled[] = $current;
    }

    $options = array(RESOURCELIB_DISPLAY_AUTO     => get_string('resourcedisplayauto'),
                     RESOURCELIB_DISPLAY_EMBED    => get_string('resourcedisplayembed'),
                     RESOURCELIB_DISPLAY_FRAME    => get_string('resourcedisplayframe'),
                     RESOURCELIB_DISPLAY_NEW      => get_string('resourcedisplaynew'),
                     RESOURCELIB_DISPLAY_DOWNLOAD => get_string('resourcedisplaydownload'),
                     RESOURCELIB_DISPLAY_OPEN     => get_string('resourcedisplayopen'),
                     RESOURCELIB_DISPLAY_POPUP    => get_string('resourcedisplaypopup'));

    $result = array();

    foreach ($options as $key=>$value) {
        if (in_array($key, $enabled)) {
            $result[$key] = $value;
        }
    }

    if (empty($result)) {
        // there should be always something in case admin misconfigures module
        $result[RESOURCELIB_DISPLAY_OPEN] = $options[RESOURCELIB_DISPLAY_OPEN];
    }

    return $result;
}

/**
 * Tries to guess correct mimetype for arbitrary URL
 * @param string $fullurl
 * @return string mimetype
 */
function resourcelib_guess_url_mimetype($fullurl) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out(false);
    }

    $matches = null;
    if (preg_match("|^(.*)/[a-z]*file.php(\?file=)?(/[^&\?#]*)|", $fullurl, $matches)) {
        // remove the special moodle file serving hacks so that the *file.php is ignored
        $fullurl = $matches[1].$matches[3];
    }

    if (preg_match("|^(.*)#.*|", $fullurl, $matches)) {
        // ignore all anchors
        $fullurl = $matches[1];
    }

    if (strpos($fullurl, '.php')){
        // we do not really know what is in general php script
        return 'text/html';

    } else if (substr($fullurl, -1) === '/') {
        // directory index (http://example.com/smaples/)
        return 'text/html';

    } else if (strpos($fullurl, '//') !== false and substr_count($fullurl, '/') == 2) {
        // just a host name (http://example.com), solves Australian servers "audio" problem too
        return 'text/html';

    } else {
        // ok, this finally looks like a real file
        $parts = explode('?', $fullurl);
        $url = reset($parts);
        return mimeinfo('type', $url);
    }
}

/**
 * Looks for the extension.
 *
 * @param string $fullurl
 * @return string file extension
 */
function resourcelib_get_extension($fullurl) {

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out(false);
    }

    $matches = null;
    if (preg_match("|^(.*)/[a-z]*file.php(\?file=)?(/.*)|", $fullurl, $matches)) {
        // remove the special moodle file serving hacks so that the *file.php is ignored
        $fullurl = $matches[1].$matches[3];
    }

    $matches = null;
    if (preg_match('/^[^#\?]+\.([a-z0-9]+)([#\?].*)?$/i', $fullurl, $matches)) {
        return strtolower($matches[1]);
    }

    return '';
}

/**
 * Returns image embedding html.
 * @param string $fullurl
 * @param string $title
 * @return string html
 */
function resourcelib_embed_image($fullurl, $title) {
    $code = '';
    $code .= '<div class="resourcecontent resourceimg">';
    $code .= "<img title=\"".s(strip_tags(format_string($title)))."\" class=\"resourceimage\" src=\"$fullurl\" alt=\"\" />";
    $code .= '</div>';

    return $code;
}

/**
 * Returns general link or pdf embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_pdf($fullurl, $title, $clicktoopen) {
    global $CFG, $PAGE;

    $code = <<<EOT
<div class="resourcecontent resourcepdf">
  <object id="resourceobject" data="$fullurl" type="application/pdf" width="800" height="600">
    <param name="src" value="$fullurl" />
    $clicktoopen
  </object>
</div>
EOT;

    // the size is hardcoded in the boject obove intentionally because it is adjusted by the following function on-the-fly
    $PAGE->requires->js_init_call('M.util.init_maximised_embed', array('resourceobject'), true);

    return $code;
}

/**
 * Returns general link or file embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @param string $mimetype
 * @return string html
 */
function resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype) {
    global $CFG, $PAGE;

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out();
    }

    $title = s($title);

    // Always use iframe embedding because object tag does not work much,
    // this is ok in HTML5.
    $code = <<<EOT
<div class="resourcecontent resourcegeneral">
  <iframe id="resourceobject" src="$fullurl" title="$title">
    $clicktoopen
  </iframe>
</div>
EOT;

    // the size is hardcoded in the boject obove intentionally because it is adjusted by the following function on-the-fly
    $PAGE->requires->js_init_call('M.util.init_maximised_embed', array('resourceobject'), true);

    return $code;
}
