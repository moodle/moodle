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
 * Library of functions and constants for module label
 *
 * @package mod_label
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/** LABEL_MAX_NAME_LENGTH = 50 */
define("LABEL_MAX_NAME_LENGTH", 50);

/**
 * @uses LABEL_MAX_NAME_LENGTH
 * @param object $label
 * @return string
 */
function get_label_name($label) {
    // Return label name if not empty.
    if ($label->name) {
        return $label->name;
    }

    $context = context_module::instance($label->coursemodule);
    $intro = format_text($label->intro, $label->introformat, ['filter' => false, 'context' => $context]);
    $name = html_to_text(format_string($intro, true, ['context' => $context]));
    $name = preg_replace('/@@PLUGINFILE@@\/[[:^space:]]+/i', '', $name);
    // Remove double space and also nbsp; characters.
    $name = preg_replace('/\s+/u', ' ', $name);
    $name = trim($name);
    if (core_text::strlen($name) > LABEL_MAX_NAME_LENGTH) {
        $name = core_text::substr($name, 0, LABEL_MAX_NAME_LENGTH) . "...";
    }

    if (empty($name)) {
        // arbitrary name
        $name = get_string('modulename','label');
    }

    return $name;
}
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $label
 * @return bool|int
 */
function label_add_instance($label) {
    global $DB;

    $label->name = get_label_name($label);
    $label->timemodified = time();

    $id = $DB->insert_record("label", $label);

    $completiontimeexpected = !empty($label->completionexpected) ? $label->completionexpected : null;
    \core_completion\api::update_completion_date_event($label->coursemodule, 'label', $id, $completiontimeexpected);

    return $id;
}

/**
 * Sets the special label display on course page.
 *
 * @param cm_info $cm Course-module object
 */
function label_cm_info_view(cm_info $cm) {
    $cm->set_custom_cmlist_item(true);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $label
 * @return bool
 */
function label_update_instance($label) {
    global $DB;

    $label->name = get_label_name($label);
    $label->timemodified = time();
    $label->id = $label->instance;

    $completiontimeexpected = !empty($label->completionexpected) ? $label->completionexpected : null;
    \core_completion\api::update_completion_date_event($label->coursemodule, 'label', $label->id, $completiontimeexpected);

    return $DB->update_record("label", $label);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function label_delete_instance($id) {
    global $DB;

    if (! $label = $DB->get_record("label", array("id"=>$id))) {
        return false;
    }

    $result = true;

    $cm = get_coursemodule_from_instance('label', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'label', $label->id, null);

    if (! $DB->delete_records("label", array("id"=>$label->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function label_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($label = $DB->get_record('label', array('id'=>$coursemodule->instance), 'id, name, intro, introformat')) {
        if (empty($label->name)) {
            // label name missing, fix it
            $label->name = "label{$label->id}";
            $DB->set_field('label', 'name', $label->name, array('id'=>$label->id));
        }
        $info = new cached_cm_info();
        // no filtering hre because this info is cached and filtered later
        $info->content = format_module_intro('label', $label, $coursemodule->id, false);
        $info->name  = $label->name;
        return $info;
    } else {
        return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function label_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function label_supports($feature) {
    return match ($feature) {
        FEATURE_IDNUMBER => true,
        FEATURE_GROUPS => false,
        FEATURE_GROUPINGS => false,
        FEATURE_MOD_INTRO => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => false,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_RESOURCE,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_NO_VIEW_LINK => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

/**
 * Register the ability to handle drag and drop file uploads.
 *
 * @return array containing details of the files / types the mod can handle
 */
function label_dndupload_register() {
    $strdnd = get_string('dnduploadlabel', 'mod_label');

    // Web images get the image-details dialogue (alternative text, decorative flag, display size)
    // before the activity is created, so tell the uploader which extensions those are.
    $webimageextensions = file_get_typegroup('extension', 'web_image');
    $mediaextensions = file_get_typegroup('extension', ['web_image', 'web_video', 'web_audio']);
    $files = [];
    foreach ($mediaextensions as $extn) {
        $extn = trim($extn, '.');
        $files[] = [
            'extension' => $extn,
            'message' => $strdnd,
            'imagedetails' => in_array('.' . $extn, $webimageextensions, true),
        ];
    }
    $ret = ['files' => $files];

    $strdndtext = get_string('dnduploadlabeltext', 'mod_label');
    return array_merge(
        $ret,
        [
            'types' => [
                ['identifier' => 'text/html', 'message' => $strdndtext, 'noname' => true],
                ['identifier' => 'text', 'message' => $strdndtext, 'noname' => true],
            ],
        ],
    );
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function label_dndupload_handle($uploadinfo) {
    global $USER;

    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;

    // Extract the first (and only) file from the file area and add it to the label as an img tag.
    if (!empty($uploadinfo->draftitemid)) {
        $fs = get_file_storage();
        $draftcontext = context_user::instance($USER->id);
        $context = context_module::instance($uploadinfo->coursemodule);
        $files = $fs->get_area_files($draftcontext->id, 'user', 'draft', $uploadinfo->draftitemid, '', false);
        if ($file = reset($files)) {
            if (file_mimetype_in_typegroup($file->get_mimetype(), 'web_image')) {
                if (!empty($uploadinfo->imagedetails)) {
                    // The author supplied image details (alt text, decorative flag, display size) via the
                    // "Add media to course page" shortcut - build the img tag from those choices.
                    $data->intro = label_generate_image_from_details($file, $uploadinfo->imagedetails);
                } else {
                    // It is an image - resize it, if too big, then insert the img tag.
                    $config = get_config('label');
                    $data->intro = label_generate_resized_image($file, $config->dndresizewidth, $config->dndresizeheight);
                }
            } else {
                // We aren't supposed to be supporting non-image types here, but fallback to adding a link, just in case.
                $url = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
                $data->intro = html_writer::link($url, $file->get_filename());
            }
            $data->intro = file_save_draft_area_files($uploadinfo->draftitemid, $context->id, 'mod_label', 'intro', 0,
                                                      null, $data->intro);
        }
    } else if (!empty($uploadinfo->content)) {
        $data->intro = $uploadinfo->content;
        if ($uploadinfo->type != 'text/html') {
            $data->introformat = FORMAT_PLAIN;
        }
    }

    return label_add_instance($data, null);
}

/**
 * Resize the image, if required, then generate an img tag and, if required, a link to the full-size image
 * @param stored_file $file the image file to process
 * @param int $maxwidth the maximum width allowed for the image
 * @param int $maxheight the maximum height allowed for the image
 * @return string HTML fragment to add to the label
 */
function label_generate_resized_image(stored_file $file, $maxwidth, $maxheight) {
    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $link = null;
    $attrib = array('alt' => $file->get_filename(), 'src' => $fullurl);

    if ($imginfo = $file->get_imageinfo()) {
        // Work out the new width / height, bounded by maxwidth / maxheight
        $naturalwidth = $imginfo['width'];
        $width = $naturalwidth;
        $height = $imginfo['height'];
        if (!empty($maxwidth) && $width > $maxwidth) {
            $height *= (float)$maxwidth / $width;
            $width = $maxwidth;
        }
        if (!empty($maxheight) && $height > $maxheight) {
            $width *= (float)$maxheight / $height;
            $height = $maxheight;
        }

        $attrib['width'] = $width;
        $attrib['height'] = $height;

        // If the size has changed, generate a smaller physical file and link to the original.
        $resized = label_generate_resized_image_src($file, $width, $height, $naturalwidth, true);
        $attrib['src'] = $resized['src'];
        $link = $resized['link'];
    } else {
        // Assume this is an image type that get_imageinfo cannot handle (e.g. SVG)
        $attrib['width'] = $maxwidth;
    }

    $attrib['class'] = "img-fluid";
    $img = html_writer::empty_tag('img', $attrib);
    if ($link) {
        return html_writer::link($link, $img);
    } else {
        return $img;
    }
}

/**
 * Generate a smaller physical copy of an image file when it is shown smaller than its source, so the
 * full-resolution file is not downloaded just to show a smaller image.
 *
 * Shared by the default drag-and-drop image path and the "Add media to course page" image-details path.
 *
 * @param stored_file $file the image file to resize
 * @param float $width the display width, already capped at the admin resize limits
 * @param float $height the display height, already capped at the admin resize limits
 * @param int $naturalwidth the source image's natural width
 * @param bool $linktooriginal whether to link the returned src to the original file when resized
 * @return array{src: moodle_url, link: moodle_url|null} the (possibly resized) image src, and a link to
 *              the original when the image was resized and $linktooriginal is true
 */
function label_generate_resized_image_src(
    stored_file $file,
    float $width,
    float $height,
    int $naturalwidth,
    bool $linktooriginal,
): array {
    global $CFG;

    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $result = ['src' => $fullurl, 'link' => null];

    if ($width >= $naturalwidth) {
        return $result;
    }

    $mimetype = $file->get_mimetype();
    if ($mimetype !== 'image/gif' && $mimetype !== 'image/jpeg' && $mimetype !== 'image/png') {
        return $result;
    }

    require_once($CFG->libdir . '/gdlib.php');
    $data = $file->generate_image_thumbnail($width, $height);
    if (empty($data)) {
        return $result;
    }

    $fs = get_file_storage();
    $record = [
        'contextid' => $file->get_contextid(),
        'component' => $file->get_component(),
        'filearea' => $file->get_filearea(),
        'itemid' => $file->get_itemid(),
        'filepath' => '/',
        'filename' => 's_' . $file->get_filename(),
    ];
    $smallfile = $fs->create_file_from_string($record, $data);
    $result['src'] = moodle_url::make_draftfile_url(
        $smallfile->get_itemid(),
        $smallfile->get_filepath(),
        $smallfile->get_filename()
    );
    if ($linktooriginal) {
        $result['link'] = $fullurl;
    }
    return $result;
}

/**
 * Generate an img tag for a dropped image using the author's choices from the image-details modal.
 *
 * Used by the "Add media to course page" shortcut so that authors can provide alternative text,
 * mark the image decorative and set its display size before the Text and media activity is created.
 *
 * The image is still capped at the admin dndresizewidth/dndresizeheight settings: a "Custom" size is
 * never honoured above those limits, and an "Original" size (no explicit width/height) is embedded at
 * its natural size but bounded by them. As with the default drag-and-drop path, when the displayed
 * image is smaller than the source file a smaller physical file is generated and linked to the original,
 * so the full-resolution file is not downloaded just to show a smaller image.
 *
 * @param stored_file $file the image file to embed
 * @param array $details the image details: 'alt' (string), 'presentation' (bool), 'width' (int), 'height' (int)
 * @return string HTML fragment (an img tag, optionally wrapped in a link to the original) to add to the label
 */
function label_generate_image_from_details(stored_file $file, array $details) {
    $config = get_config('label');
    $maxwidth = (int) $config->dndresizewidth;
    $maxheight = (int) $config->dndresizeheight;

    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $presentation = !empty($details['presentation']);
    $link = null;

    $attrib = [
        'src' => $fullurl,
        // A decorative image is hidden from assistive technologies, so it carries an empty alt text.
        'alt' => $presentation ? '' : (string) ($details['alt'] ?? ''),
        'class' => 'img-fluid',
    ];
    if ($presentation) {
        $attrib['role'] = 'presentation';
    }

    // A "Custom" size supplies explicit width and height; "Original" leaves them at 0.
    $customwidth = max(0, (int) ($details['width'] ?? 0));
    $customheight = max(0, (int) ($details['height'] ?? 0));
    $explicit = ($customwidth && $customheight);

    if ($imginfo = $file->get_imageinfo()) {
        $naturalwidth = $imginfo['width'];
        $naturalheight = $imginfo['height'];

        // The size to display at: the author's custom size, or the natural size for "Original".
        $width = $explicit ? $customwidth : $naturalwidth;
        $height = $explicit ? $customheight : $naturalheight;

        // Never display (or embed) larger than the admin resize limits, keeping the aspect ratio.
        if (!empty($maxwidth) && $width > $maxwidth) {
            $height = (int) round($height * ((float) $maxwidth / $width));
            $width = $maxwidth;
        }
        if (!empty($maxheight) && $height > $maxheight) {
            $width = (int) round($width * ((float) $maxheight / $height));
            $height = $maxheight;
        }

        // Only "Custom" puts explicit dimensions on the tag; "Original" leaves the (capped) natural size implicit.
        if ($explicit) {
            $attrib['width'] = $width;
            $attrib['height'] = $height;
        }

        // If the image is shown smaller than its source file, generate a smaller file so the full-resolution
        // original is not downloaded just to show a smaller image. Link to the original so it stays reachable,
        // unless the image is decorative: an empty alt text would leave that link with no accessible name.
        $resized = label_generate_resized_image_src($file, $width, $height, $naturalwidth, !$presentation);
        $attrib['src'] = $resized['src'];
        $link = $resized['link'];
    } else {
        // Image type get_imageinfo cannot handle (e.g. SVG): fall back to a width only, capped at the limit.
        $width = $explicit ? $customwidth : $maxwidth;
        $height = $explicit ? $customheight : 0;
        if (!empty($maxwidth) && $width > $maxwidth) {
            if ($height) {
                // Scale the height by the same factor as the width, so the aspect ratio stays correct.
                $height = (int) round($height * ($maxwidth / $width));
            }
            $width = $maxwidth;
        }
        if ($width) {
            $attrib['width'] = $width;
        }
        if ($explicit && $height) {
            $attrib['height'] = $height;
        }
    }

    $img = html_writer::empty_tag('img', $attrib);
    return $link ? html_writer::link($link, $img) : $img;
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function label_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array(), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_label_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory,
                                                      int $userid = 0) {
    $cm = get_fast_modinfo($event->courseid, $userid)->instances['label'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/label/view.php', ['id' => $cm->id]),
        1,
        true
    );
}
