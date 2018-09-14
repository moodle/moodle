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
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once('../managers/validate_profile_action.php');
require_once("../classes/mdl_forms/UserImageForm.php");

$courseid  = required_param('courseid', PARAM_INT);
require_login($courseid, false);
$blockid   = required_param('instanceid', PARAM_INT);
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->update_user_profile_image_)) {
 redirect(new moodle_url('/'));
}
$show_html_elements_update_user_profile_image = true;
$filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                             'subdirs'        => 0,
                             'maxfiles'       => 1,
                             'accepted_types' => 'web_image');

$ases_user_id =  required_param('ases_user_id', PARAM_INT);
//echo $ases_user_id ;
//die;
$url_return =  required_param('url_return', PARAM_TEXT);

$contextcourse = context_course::instance($courseid);
$contextblock  = context_block::instance($blockid);
$url           = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid,
    'ases_user_id' => $ases_user_id,
    'url_return' => $url_return
));
/** Creando el formulario  */
$user_image_edit_form = new user_image_edit_form($url);

//Form processing and displaying is done here
if ($user_image_edit_form->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($url_return);
} else if ($image_data = $user_image_edit_form->get_data()) {
    file_save_draft_area_files($image_data->imagefile, $contextblock->id, 'block_ases', 'profile_image',
    $ases_user_id, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 50));
    redirect($url_return);
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
  //Set default data (if any)
  $user_image_edit_form->set_data($toform);
  //displays the form
  $user_image_edit_form->display(null);
};