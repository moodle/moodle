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

    if (!$context = get_context_instance(CONTEXT_MODULE, $cmid)) {
        return false;
    }
    if (!$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid)) {
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
    $code .= "<img title=\"".strip_tags(format_string($title))."\" class=\"resourceimage\" src=\"$fullurl\" alt=\"\" />";
    $code .= '</div>';

    return $code;
}

/**
 * Returns mp3 embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_mp3($fullurl, $title, $clicktoopen) {

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out(false);
    }

    $id = 'resource_mp3_'.time(); //we need something unique because it might be stored in text cache

    // note: size is specified in theme, it can be made as wide as necessary, but the height can not be changed

    $output = '<div class="resourcecontent resourcemp3">';
    $output .= html_writer::tag('span', $clicktoopen, array('id'=>$id, 'class'=>'resourcemediaplugin resourcemediaplugin_mp3', 'title'=>$title));
    $output .= html_writer::script(js_writer::function_call('M.util.add_audio_player', array($id, $fullurl, false)));
    $output .= '</div>';

    return $output;
}

/**
 * Returns flash video embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_flashvideo($fullurl, $title, $clicktoopen) {
    global $CFG, $PAGE;

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out(false);
    }

    $id = 'resource_flv_'.time(); //we need something unique because it might be stored in text cache

    //note: nobody should be adding any dimensions to themes!!!

    if (preg_match('/\?d=([\d]{1,4}%?)x([\d]{1,4}%?)/', $fullurl, $matches)) {
        $width    = $matches[1];
        $height   = $matches[2];
        $autosize = false;
    } else {
        $width    = 400;
        $height   = 300;
        $autosize = true;
    }
    $output = '<div class="resourcecontent resourceflv">';
    $output .= html_writer::tag('span', $clicktoopen, array('id'=>$id, 'class'=>'resourcemediaplugin resourcemediaplugin_flv', 'title'=>$title));
    $output .= html_writer::script(js_writer::function_call('M.util.add_video_player', array($id, $fullurl, $width, $height, $autosize)));
    $output .= '</div>';

    return $output;
}

/**
 * Returns flash embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_flash($fullurl, $title, $clicktoopen) {
    if (preg_match('/[#\?]d=([\d]{1,4}%?)x([\d]{1,4}%?)/', $fullurl, $matches)) {
        $width    = $matches[1];
        $height   = $matches[2];
    } else {
        $width    = 400;
        $height   = 300;
    }

    $code = <<<EOT
<div class="resourcecontent resourceswf">
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$width" height="$height">
    <param name="movie" value="$fullurl" />
    <param name="autoplay" value="true" />
    <param name="loop" value="true" />
    <param name="controller" value="true" />
    <param name="scale" value="aspect" />
    <param name="base" value="." />
<!--[if !IE]>-->
    <object type="application/x-shockwave-flash" data="$fullurl" width="$width" height="$height">
      <param name="controller" value="true" />
      <param name="autoplay" value="true" />
      <param name="loop" value="true" />
      <param name="scale" value="aspect" />
      <param name="base" value="." />
<!--<![endif]-->
$clicktoopen
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</div>
EOT;

    return $code;
}

/**
 * Returns ms media embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_mediaplayer($fullurl, $title, $clicktoopen) {
    $code = <<<EOT
<div class="resourcecontent resourcewmv">
  <object type="video/x-ms-wmv" data="$fullurl">
    <param name="controller" value="true" />
    <param name="autostart" value="true" />
    <param name="src" value="$fullurl" />
    <param name="scale" value="noScale" />
    $clicktoopen
  </object>
</div>
EOT;

    return $code;
}

/**
 * Returns quicktime embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_quicktime($fullurl, $title, $clicktoopen) {
    $code = <<<EOT
<div class="resourcecontent resourceqt">
  <object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
    <param name="src" value="$fullurl" />
    <param name="autoplay" value="true" />
    <param name="loop" value="true" />
    <param name="controller" value="true" />
    <param name="scale" value="aspect" />
<!--[if !IE]>-->
    <object type="video/quicktime" data="$fullurl">
      <param name="controller" value="true" />
      <param name="autoplay" value="true" />
      <param name="loop" value="true" />
      <param name="scale" value="aspect" />
<!--<![endif]-->
$clicktoopen
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</div>
EOT;

    return $code;
}

/**
 * Returns mpeg embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_mpeg($fullurl, $title, $clicktoopen) {
    $code = <<<EOT
<div class="resourcecontent resourcempeg">
  <object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsm p2inf.cab#Version=5,1,52,701" type="application/x-oleobject">
    <param name="fileName" value="$fullurl" />
    <param name="autoStart" value="true" />
    <param name="animationatStart" value="true" />
    <param name="transparentatStart" value="true" />
    <param name="showControls" value="true" />
    <param name="Volume" value="-450" />
<!--[if !IE]>-->
    <object type="video/mpeg" data="$fullurl">
      <param name="controller" value="true" />
      <param name="autostart" value="true" />
      <param name="src" value="$fullurl" />
<!--<![endif]-->
$clicktoopen
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</div>
EOT;

    return $code;
}

/**
 * Returns real media embedding html.
 * @param string $fullurl
 * @param string $title
 * @param string $clicktoopen
 * @return string html
 */
function resourcelib_embed_real($fullurl, $title, $clicktoopen) {
    $code = <<<EOT
<div class="resourcecontent resourcerm">
  <object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" data="$fullurl" width="320" height="240">
    <param name="src" value="$fullurl" />
    <param name="controls" value="All" />
<!--[if !IE]>-->
    <object type="audio/x-pn-realaudio-plugin" data="$fullurl" width="320" height="240">
    <param name="src" value="$fullurl" />
      <param name="controls" value="All" />
<!--<![endif]-->
$clicktoopen
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</div>
EOT;

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
 * @return string html
 */
function resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype) {
    global $CFG, $PAGE;

    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out();
    }

    $iframe = false;

    $param = '<param name="src" value="'.$fullurl.'" />';

    // IE can not embed stuff properly if stored on different server
    // that is why we use iframe instead, unfortunately this tag does not validate
    // in xhtml strict mode
    if ($mimetype === 'text/html' and check_browser_version('MSIE', 5)) {
        // The param tag needs to be removed to avoid trouble in IE.
        $param = '';
        if (preg_match('(^https?://[^/]*)', $fullurl, $matches)) {
            if (strpos($CFG->wwwroot, $matches[0]) !== 0) {
                $iframe = true;
            }
        }
    }

    if ($iframe) {
        $code = <<<EOT
<div class="resourcecontent resourcegeneral">
  <iframe id="resourceobject" src="$fullurl">
    $clicktoopen
  </iframe>
</div>
EOT;
    } else {
        $code = <<<EOT
<div class="resourcecontent resourcegeneral">
  <object id="resourceobject" data="$fullurl" type="$mimetype"  width="800" height="600">
    $param
    $clicktoopen
  </object>
</div>
EOT;
    }

    // the size is hardcoded in the boject obove intentionally because it is adjusted by the following function on-the-fly
    $PAGE->requires->js_init_call('M.util.init_maximised_embed', array('resourceobject'), true);

    return $code;
}
