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
 * Simple slider block for Moodle
 *
 * If You like my plugin please send a small donation https://paypal.me/limsko Thanks!
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_login();

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/filterlib.php');
require_once('manage_images_table.php');
require_once('lib.php');

$sliderid = required_param('sliderid', PARAM_INT);
$id = optional_param('id', null, PARAM_INT);
$courseid = optional_param('course', null, PARAM_INT);
$baseurl = new moodle_url('/blocks/slider/manage_images.php', array('view' => 'manage', 'sliderid' => $sliderid));
if ($courseid) {
    if ($course = get_course($courseid)) {
        $PAGE->set_course($course);
    }
}
$PAGE->navbar->add(get_string('manage_slides', 'block_slider'), $baseurl);

require_once('manage_images_form.php');
/* require_once($CFG->libdir . '/gdlib.php'); todo: consider adding function of thumbnail generation. */

$context = context_block::instance($sliderid);
require_capability('block/slider:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($baseurl);

$mform = new add_slider_image(new moodle_url('/blocks/slider/manage_images.php'), array('sliderid' => $sliderid, 'id' => $id));
if ($mform->is_cancelled()) {
    redirect($baseurl);
} else if ($fromform = $mform->get_data()) {

    $dtable = 'slider_slides';
    $data = new StdClass();
    $data->sliderid = $fromform->sliderid;
    $data->slide_title = $fromform->slide_title;
    $data->slide_desc = $fromform->slide_desc;
    $data->slide_order = $fromform->slide_order;
    $data->slide_link = $fromform->slide_link;

    // Editing or adding new slide.
    if (!empty($fromform->id) and $data->id = $DB->get_field($dtable, 'id', array('id' => $fromform->id))) {
        $editing = 1;
        $DB->update_record($dtable, $data);
    } else {
        $editing = 0;
        $id = $DB->insert_record($dtable, $data);
    }

    // Adding new slide photo.
    $content = $mform->get_file_content('slide_image');
    $name = $mform->get_new_filename('slide_image');
    if ($content && $name) {
        // First delete old image.
        if ($editing === 1) {
            block_slider_delete_image($data->sliderid, $id);
        }
        $filename = strtolower($name);
        $fs = get_file_storage();
        $fileinfo = array(
                'contextid' => $context->id,
                'component' => 'block_slider',
                'filearea' => 'slider_slides',
                'itemid' => $id,
                'filepath' => '/',
                'filename' => $filename);
        $fs->create_file_from_string($fileinfo, $content);

        /* todo: Thumbnail generation. */
        $data->id = $id;
        $data->slide_image = $filename;
        $id = $DB->update_record($dtable, $data);
    }

    if ($id) {
        redirect($baseurl, get_string('saved', 'block_slider'));
    }

} else {

    echo $OUTPUT->header();

    // Display Slider ID is Filter is enabled.
    if (filter_is_enabled('slider')) {
        echo html_writer::tag('p', get_string('slider_id_for_filter', 'block_slider', $sliderid), ['class' => 'lead']);
    }

    if (!$id) {
        // Slides table.
        $table = new manage_images('slider_table');
        $table->set_sql('*', "{slider_slides}", 'sliderid=?', array($sliderid));
        $table->define_baseurl($baseurl);
        $table->no_sorting('manage');
        $table->no_sorting('slide_image');
        $table->collapsible(false);
        $table->out(40, true);
        echo html_writer::empty_tag('hr');
    }

    if ($id) {
        // Editing slide.
        $toform = $DB->get_record('slider_slides', array('id' => $id));
        $mform->set_data($toform);
    }
    $mform->display();

    slider_donation_link();

    echo $OUTPUT->footer();
}
