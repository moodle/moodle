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
 * Search page for searching for images
 *
 * @package   mod_lightboxgallery
 * @copyright 2010 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/imageclass.php');

require_once($CFG->libdir.'/filelib.php');

$cid = required_param('id', PARAM_INT);
$g = optional_param('gallery', '0', PARAM_INT);
$search = optional_param('search', '', PARAM_CLEAN);

if ($g) {
    $gallery = $DB->get_record('lightboxgallery', array('id' => $g), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $gallery->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance("lightboxgallery", $gallery->id, $course->id, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    require_login($course, true, $cm);
} else {
    $course = $DB->get_record('course', array('id' => $cid), '*', MUST_EXIST);
    $context = context_course::instance($cid);
    require_login($course, true);
}


if (isset($gallery) && $gallery->ispublic) {
    $userid = (isloggedin() ? $USER->id : 0);
} else {
    $userid = $USER->id;
}

$context = context_module::instance($cm->id);

$params = array(
    'context' => $context,
    'other' => array(
        'searchterm' => $search,
        'lightboxgalleryid' => $gallery->id,
    ),
);
$event = \mod_lightboxgallery\event\gallery_searched::create($params);
$event->trigger();

$PAGE->set_url('/mod/lightboxgallery/search.php', array('id' => $cm->id, 'search' => $search));
$PAGE->set_title($gallery->name);
$PAGE->set_heading($course->shortname);
$PAGE->requires->css('/mod/lightboxgallery/assets/skins/sam/gallery-lightbox-skin.css');
$PAGE->requires->yui_module('moodle-mod_lightboxgallery-lightbox', 'M.mod_lightboxgallery.init');

echo $OUTPUT->header();

$options = array();
if ($instances = get_all_instances_in_course('lightboxgallery', $course)) {
    foreach ($instances as $instance) {
        $options[$instance->id] = $instance->name;
    }

    echo('<form action="search.php">');

    $table = new html_table;
    $table->width = '*';
    $table->align = array('left', 'left', 'left', 'left');
    $table->data[] = array(get_string('modulenameshort', 'lightboxgallery'), html_writer::select($options, 'gallery', $g),
                           '<input type="text" name="search" size="10" value="'.s($search, true).'" />' .
                           '<input type="hidden" name="id" value="'.$cid.'" />',
                           '<input type="submit" value="'.get_string('search').'" />');
    echo html_writer::table($table);
    echo html_writer::end_tag('form');
}

$fs = get_file_storage();

if ($g) {
    $options = array($g => $g);
}
list($insql, $inparams) = $DB->get_in_or_equal(array_keys($options));
$params = array_merge(array("%$search%"), $inparams);
$sql = "SELECT *
        FROM {lightboxgallery_image_meta}
        WHERE ".$DB->sql_like('description', '?', false)." AND gallery $insql";
if ($results = $DB->get_records_sql($sql, $params)) {
    echo $OUTPUT->box_start('generalbox lightbox-gallery clearfix autoresize');

    $hashes = array();
    $galleryrecords = array();

    foreach ($results as $result) {
        if (!isset($hashes[$result->image])) {
            $imgcm = get_coursemodule_from_instance("lightboxgallery", $result->gallery, $course->id, false, MUST_EXIST);

            if (!isset($galleryrecords[$result->gallery])) {
                $imggallery = $DB->get_record('lightboxgallery', array('id' => $result->gallery), '*', MUST_EXIST);
                $galleryrecords[$result->gallery] = $imggallery;
            } else {
                $imggallery = $galleryrecords[$result->gallery];
            }
            $imgcontext = context_module::instance($imgcm->id);

            if ($storedfile = $fs->get_file($imgcontext->id, 'mod_lightboxgallery', 'gallery_images', 0, '/', $result->image)) {
                $image = new lightboxgallery_image($storedfile, $imggallery, $imgcm);
                echo $image->get_image_display_html();
                $hashes[$result->image] = 1;
            }
        }
    }

    echo $OUTPUT->box_end();
} else {
    echo $OUTPUT->box(get_string('errornosearchresults', 'lightboxgallery'));
}

echo $OUTPUT->footer();
