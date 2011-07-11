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
 * Private resource module utility functions
 *
 * @package    mod
 * @subpackage resource
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/resource/lib.php");

/**
 * Redirected to migrated resource if needed,
 * return if incorrect parameters specified
 * @param int $oldid
 * @param int $cmid
 * @return void
 */
function resource_redirect_if_migrated($oldid, $cmid) {
    global $DB, $CFG;

    if ($oldid) {
        $old = $DB->get_record('resource_old', array('oldid'=>$oldid));
    } else {
        $old = $DB->get_record('resource_old', array('cmid'=>$cmid));
    }

    if (!$old) {
        return;
    }

    redirect("$CFG->wwwroot/mod/$old->newmodule/view.php?id=".$old->cmid);
}

/**
 * Display embedded resource file.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function resource_display_embed($resource, $cm, $course, $file) {
    global $CFG, $PAGE, $OUTPUT;

    $clicktoopen = resource_get_clicktoopen($file, $resource->revision);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $path = '/'.$context->id.'/mod_resource/content/'.$resource->revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);

    $mimetype = $file->get_mimetype();
    $title    = $resource->name;

    $extension = resourcelib_get_extension($file->get_filename());

    if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
        $code = resourcelib_embed_image($fullurl, $title);

    } else if ($mimetype === 'application/pdf') {
        // PDF document
        $code = resourcelib_embed_pdf($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'audio/mp3') {
        // MP3 audio file
        $code = resourcelib_embed_mp3($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'video/x-flv' or $extension === 'f4v') {
        // Flash video file
        $code = resourcelib_embed_flashvideo($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'application/x-shockwave-flash') {
        // Flash file
        $code = resourcelib_embed_flash($fullurl, $title, $clicktoopen);

    } else if (substr($mimetype, 0, 10) === 'video/x-ms') {
        // Windows Media Player file
        $code = resourcelib_embed_mediaplayer($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'video/quicktime') {
        // Quicktime file
        $code = resourcelib_embed_quicktime($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'video/mpeg') {
        // Mpeg file
        $code = resourcelib_embed_mpeg($fullurl, $title, $clicktoopen);

    } else if ($mimetype === 'audio/x-pn-realaudio') {
        // RealMedia file
        $code = resourcelib_embed_real($fullurl, $title, $clicktoopen);

    } else {
        // anything else - just try object tag enlarged as much as possible
        $code = resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype);
    }

    resource_print_header($resource, $cm, $course);
    resource_print_heading($resource, $cm, $course);

    echo $code;

    resource_print_intro($resource, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

/**
 * Display resource frames.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function resource_display_frame($resource, $cm, $course, $file) {
    global $PAGE, $OUTPUT, $CFG;

    $frame = optional_param('frameset', 'main', PARAM_ALPHA);

    if ($frame === 'top') {
        $PAGE->set_pagelayout('frametop');
        resource_print_header($resource, $cm, $course);
        resource_print_heading($resource, $cm, $course);
        resource_print_intro($resource, $cm, $course);
        echo $OUTPUT->footer();
        die;

    } else {
        $config = get_config('resource');
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $path = '/'.$context->id.'/mod_resource/content/'.$resource->revision.$file->get_filepath().$file->get_filename();
        $fileurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
        $navurl = "$CFG->wwwroot/mod/resource/view.php?id=$cm->id&amp;frameset=top";
        $title = strip_tags(format_string($course->shortname.': '.$resource->name));
        $framesize = $config->framesize;
        $modulename = s(get_string('modulename','resource'));
        $dir = get_string('thisdirection', 'langconfig');

        $file = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html dir="$dir">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>$title</title>
  </head>
  <frameset rows="$framesize,*">
    <frame src="$navurl" title="$modulename" />
    <frame src="$fileurl" title="$modulename" />
  </frameset>
</html>
EOF;

        @header('Content-Type: text/html; charset=utf-8');
        echo $file;
        die;
    }
}

/**
 * Internal function - create click to open text with link.
 */
function resource_get_clicktoopen($file, $revision, $extra='') {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/'.$file->get_contextid().'/mod_resource/content/'.$revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);

    $string = get_string('clicktoopen2', 'resource', "<a href=\"$fullurl\" $extra>$filename</a>");

    return $string;
}

/**
 * Internal function - create click to open text with link.
 */
function resource_get_clicktodownload($file, $revision) {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/'.$file->get_contextid().'/mod_resource/content/'.$revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, true);

    $string = get_string('clicktodownload', 'resource', "<a href=\"$fullurl\">$filename</a>");

    return $string;
}

/**
 * Print resource info and workaround link when JS not available.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function resource_print_workaround($resource, $cm, $course, $file) {
    global $CFG, $OUTPUT;

    resource_print_header($resource, $cm, $course);
    resource_print_heading($resource, $cm, $course, true);
    resource_print_intro($resource, $cm, $course, true);

    $resource->mainfile = $file->get_filename();
    echo '<div class="resourceworkaround">';
    switch (resource_get_final_display_type($resource)) {
        case RESOURCELIB_DISPLAY_POPUP:
            $path = '/'.$file->get_contextid().'/mod_resource/content/'.$resource->revision.$file->get_filepath().$file->get_filename();
            $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
            $options = empty($resource->displayoptions) ? array() : unserialize($resource->displayoptions);
            $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
            $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
            $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
            $extra = "onclick=\"window.open('$fullurl', '', '$wh'); return false;\"";
            echo resource_get_clicktoopen($file, $resource->revision, $extra);
            break;

        case RESOURCELIB_DISPLAY_NEW:
            $extra = 'onclick="this.target=\'_blank\'"';
            echo resource_get_clicktoopen($file, $resource->revision, $extra);
            break;

        case RESOURCELIB_DISPLAY_DOWNLOAD:
            echo resource_get_clicktodownload($file, $resource->revision);
            break;

        case RESOURCELIB_DISPLAY_OPEN:
        default:
            echo resource_get_clicktoopen($file, $resource->revision);
            break;
    }
    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

/**
 * Print resource header.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @return void
 */
function resource_print_header($resource, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$resource->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($resource);
    $PAGE->set_button(update_module_button($cm->id, '', get_string('modulename', 'resource')));
    echo $OUTPUT->header();
}

/**
 * Print resource heading.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function resource_print_heading($resource, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($resource->displayoptions) ? array() : unserialize($resource->displayoptions);
    if ($ignoresettings or !empty($options['printheading'])) {
        echo $OUTPUT->heading(format_string($resource->name), 2, 'main', 'resourceheading');
    }
}

/**
 * Print resource introduction.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function resource_print_intro($resource, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($resource->displayoptions) ? array() : unserialize($resource->displayoptions);
    if ($ignoresettings or !empty($options['printintro'])) {
        if (trim(strip_tags($resource->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'resourceintro');
            echo format_module_intro('resource', $resource, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Print warning that instance not migrated yet.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function resource_print_tobemigrated($resource, $cm, $course) {
    global $DB, $OUTPUT;

    $resource_old = $DB->get_record('resource_old', array('oldid'=>$resource->id));
    resource_print_header($resource, $cm, $course);
    resource_print_heading($resource, $cm, $course);
    resource_print_intro($resource, $cm, $course);
    echo $OUTPUT->notification(get_string('notmigrated', 'resource', $resource_old->type));
    echo $OUTPUT->footer();
    die;
}

/**
 * Print warning that file can not be found.
 * @param object $resource
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function resource_print_filenotfound($resource, $cm, $course) {
    global $DB, $OUTPUT;

    $resource_old = $DB->get_record('resource_old', array('oldid'=>$resource->id));
    resource_print_header($resource, $cm, $course);
    resource_print_heading($resource, $cm, $course);
    resource_print_intro($resource, $cm, $course);
    if ($resource_old) {
        echo $OUTPUT->notification(get_string('notmigrated', 'resource', $resource_old->type));
    } else {
        echo $OUTPUT->notification(get_string('filenotfound', 'resource'));
    }
    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best diaply format.
 * @param object $resource
 * @return int display type constant
 */
function resource_get_final_display_type($resource) {
    global $CFG;

    if ($resource->display != RESOURCELIB_DISPLAY_AUTO) {
        return $resource->display;
    }

    static $download = array('application/zip', 'application/x-tar', 'application/g-zip');    // binary formats
    static $embed    = array('image/gif', 'image/jpeg', 'image/png', 'image/svg+xml',         // images
                             'application/x-shockwave-flash', 'video/x-flv', 'video/x-ms-wm', // video formats
                             'video/quicktime', 'video/mpeg', 'video/mp4',
                             'audio/mp3', 'audio/x-realaudio-plugin', 'x-realaudio-plugin',   // audio formats
                             'application/pdf', 'text/html',
                            );

    if (empty($resource->mainfile)) {
        return RESOURCELIB_DISPLAY_DOWNLOAD;
    } else {
        $mimetype = mimeinfo('type', $resource->mainfile);
    }

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
 * File browsing support class
 */
class resource_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

function resource_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files;

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if ($draftitemid) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_resource', 'content', 0, array('subdirs'=>true));
    }
    $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // only one file attached, set it as main file automatically
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_resource', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
}
