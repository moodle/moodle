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
 * Image editing page
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 NetSpot Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/edit/base.class.php');
require_once(dirname(__FILE__).'/imageclass.php');

global $DB;

$id = required_param('id', PARAM_INT);
$image = required_param('image', PARAM_PATH);
$tab = optional_param('tab', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);

$cm      = get_coursemodule_from_id('lightboxgallery', $id, 0, false, MUST_EXIST);
$course  = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$gallery = $DB->get_record('lightboxgallery', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course->id);

$context = context_module::instance($cm->id);
require_capability('mod/lightboxgallery:edit', $context);

$PAGE->set_cm($cm);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/lightboxgallery/imageedit.php', array('id' => $cm->id, 'image' => $image, 'tab' => $tab, 'page' => $page));
$PAGE->set_title($gallery->name);
$PAGE->set_heading($course->shortname);
$buttonurl = new moodle_url('/mod/lightboxgallery/view.php', array('id' => $id, 'editing' => 1, 'page' => $page));
$PAGE->set_button($OUTPUT->single_button($buttonurl, get_string('backtogallery', 'lightboxgallery')));

$edittypes = lightboxgallery_edit_types();

$tabs = array();
foreach ($edittypes as $type => $name) {
    $editurl = new moodle_url('/mod/lightboxgallery/imageedit.php',
                                array('id' => $cm->id, 'image' => $image, 'page' => $page, 'tab' => $type));
    $tabs[] = new tabObject($type, $editurl, $name);
}

if (!in_array($tab, array_keys($edittypes))) {
    $types = array_keys($edittypes);
    if (isset($types[0])) {
        $tab = $types[0];
    } else {
        notice(get_string('allpluginsdisabled', 'lightboxgallery'), "view.php?id=$id&page=$page");
    }
}

require($CFG->dirroot.'/mod/lightboxgallery/edit/'.$tab.'/'.$tab.'.class.php');
$editclass = 'edit_'.$tab;
$editinstance = new $editclass($gallery, $cm, $image, $tab);

$fs = get_file_storage();
if (!$storedfile = $fs->get_file($context->id, 'mod_lightboxgallery', 'gallery_images', '0', '/', $image)) {
    print_error(get_string('errornofile', 'lightboxgallery', $image));
}

if ($editinstance->processing() && confirm_sesskey()) {
    $params = array(
        'context' => $context,
        'other' => array(
            'imagename' => $image,
            'tab' => $tab
        ),
    );
    $event = \mod_lightboxgallery\event\image_updated::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('lightboxgallery', $gallery);
    $event->trigger();

    $editinstance->process_form();
    redirect($CFG->wwwroot.'/mod/lightboxgallery/imageedit.php?id='.$cm->id.'&image='.$editinstance->image.'&tab='.$tab);
}

$image = new lightboxgallery_image($storedfile, $gallery, $cm);

$table = new html_table();
$table->width = '*';

if ($editinstance->showthumb) {
    $table->attributes = array('style' => 'margin-left:auto;margin-right:auto;');
    $table->align = array('center', 'center');
    $table->size = array('*', '*');
    $table->data[] = array('<img src="'.$image->get_thumbnail_url().
                            '" alt="" /><br /><span title="'.$image->get_image_caption().'">'.
                            $image->get_image_caption().'</span>', $editinstance->output($image->get_image_caption()));
} else {
    $table->align = array('center');
    $table->size = array('*');
    $table->data[] = array($editinstance->output($image->get_image_caption()));
}

echo $OUTPUT->header();

print_tabs(array($tabs), $tab);

echo html_writer::table($table);

echo $OUTPUT->footer();
