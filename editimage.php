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
 * Grid Format - A topics based format that uses a grid of user selectable images to popup a light box of the section.
 *
 * @package    course/format
 * @subpackage grid
 * @copyright  &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Imports */
require_once('../../../config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/course/format/grid/editimage_form.php');
require_once($CFG->libdir . '/gdlib.php');

/* Script settings */
define('GRID_ITEM_IMAGE_WIDTH', 210);
define('GRID_ITEM_IMAGE_HEIGHT', 140);

/* Page parameters */
$contextid = required_param('contextid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$id = optional_param('id', null, PARAM_INT);

/* No idea, copied this from an example. Sets form data options but I don't know what they all do exactly */
$formdata = new stdClass();
$formdata->userid = required_param('userid', PARAM_INT);
$formdata->offset = optional_param('offset', null, PARAM_INT);
$formdata->forcerefresh = optional_param('forcerefresh', null, PARAM_INT);
$formdata->mode = optional_param('mode', null, PARAM_ALPHA);

$url = new moodle_url('/course/format/grid/editimage.php', array(
            'contextid' => $contextid,
            'id' => $id,
            'offset' => $formdata->offset,
            'forcerefresh' => $formdata->forcerefresh,
            'userid' => $formdata->userid,
            'mode' => $formdata->mode));

/* Not exactly sure what this stuff does, but it seems fairly straightforward */
list($context, $course, $cm) = get_context_info_array($contextid);

require_login($course, true, $cm);
if (isguestuser()) {
    die();
}

$PAGE->set_url($url);
$PAGE->set_context($context);

/* Functional part. Create the form and display it, handle results, etc */
$options = array(
    'subdirs' => 0,
    'maxfiles' => 1,
    'accepted_types' => array('web_image'),
    'return_types' => FILE_INTERNAL);

$mform = new grid_image_form(null, array(
            'contextid' => $contextid,
            'userid' => $formdata->userid,
            'sectionid' => $sectionid,
            'options' => $options));

if ($mform->is_cancelled()) {
    // Someone has hit the 'cancel' button.
    redirect(new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $course->id));
} else if ($formdata = $mform->get_data()) { // Form has been submitted.
    $fs = get_file_storage();

    if ($newfilename = $mform->get_new_filename('icon_file')) {
        // We have a new file so can delete the old....
        $sectionicon = course_get_format($course)->grid_get_icon($course->id, $sectionid);
        if ($sectionicon) {
            if ($file = $fs->get_file($context->id, 'course', 'section', $sectionid, '/', $sectionicon->imagepath)) {
                $file->delete();
            }
        }

        // Resize the new image and save it...
        $created = time();
        $storedfile_record = array(
            'contextid' => $contextid,
            'component' => 'course',
            'filearea' => 'section',
            'itemid' => $sectionid,
            'filepath' => '/',
            'filename' => $newfilename,
            'timecreated' => $created,
            'timemodified' => $created);

        $temp_file = $mform->save_stored_file(
                'icon_file', $storedfile_record['contextid'], $storedfile_record['component'], $storedfile_record['filearea'],
                             $storedfile_record['itemid'], $storedfile_record['filepath'], 'temp.'.$storedfile_record['filename'],
                             true);

        try {
            $convert_success = true;
            // Ensure the right quality setting...
            $mime = $temp_file->get_mimetype();

            $storedfile_record['mimetype'] = $mime;

            if ($mime != 'image/gif') {
                $tmproot = make_temp_directory('gridformatimage');
                $tmpfilepath = $tmproot . '/' . $temp_file->get_contenthash();
                $temp_file->copy_content_to($tmpfilepath);

                $data = generate_image_thumbnail($tmpfilepath, GRID_ITEM_IMAGE_WIDTH, GRID_ITEM_IMAGE_HEIGHT);
                if (!empty($data)) {
                    $fs->create_file_from_string($storedfile_record, $data);
                } else {
                    $convert_success = false;
                }
                unlink($tmpfilepath);
            } else {
                $fr = $fs->convert_image($storedfile_record, $temp_file, GRID_ITEM_IMAGE_WIDTH, GRID_ITEM_IMAGE_HEIGHT, true, null);

                // Debugging...
                if (debugging('', DEBUG_DEVELOPER)) {
                    // Use 'print' function even though the documentation says you should not and yet everywhere does.
                    print_object($fr);
                }
            }
            $temp_file->delete();
            unset($temp_file);

            if ($convert_success == true) {
                $DB->set_field('format_grid_icon', 'imagepath', $newfilename, array('sectionid' => $sectionid));
            } else {
                print_error('imagecannotbeusedasanicon', 'format_grid', $CFG->wwwroot . "/course/view.php?id=" . $course->id);
            }
        } catch (Exception $e) {
            if (isset($temp_file)) {
                $temp_file->delete();
                unset($temp_file);
            }
            print('Grid Format Edit Image Exception:...');
            debugging($e->getMessage());
        }
        redirect($CFG->wwwroot . "/course/view.php?id=" . $course->id);
    }
}

/* Draw the form */
echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
