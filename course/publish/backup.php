<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/*
 * @package    course
 * @subpackage publish
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page display the publication backup form
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot.'/admin/registration/lib.php');
require_once($CFG->dirroot.'/course/publish/lib.php');
require_once($CFG->dirroot.'/lib/filelib.php');


//retrieve initial page parameters
$id = optional_param('id', 0, PARAM_INT);
$hubcourseid = optional_param('hubcourseid', 0, PARAM_INT);
$huburl = optional_param('huburl', '', PARAM_URL);
$hubname = optional_param('hubname', '', PARAM_TEXT);

//some permissions and parameters checking
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_login($course);
if (!has_capability('moodle/course:publish', get_context_instance(CONTEXT_COURSE, $id))
        or !confirm_sesskey()) {
    throw new moodle_exception('nopermission');
}
if (empty($huburl) or empty($hubcourseid)) {
        throw new moodle_exception('missingparameter');
}

//page settings
$PAGE->set_url('/course/publish/backup.php');
$PAGE->set_pagelayout('course');
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);

//BEGIN backup processing
$backupid = optional_param('backup', false, PARAM_ALPHANUM);
if (!($bc = backup_ui::load_controller($backupid))) {
    $bc = new backup_controller(backup::TYPE_1COURSE, $id, backup::FORMAT_MOODLE,
                    backup::INTERACTIVE_YES, backup::MODE_HUB, $USER->id);
}
$backup = new backup_ui($bc,
        array('id' => $id, 'hubcourseid' => $hubcourseid, 'huburl' => $huburl, 'hubname' => $hubname));
$backup->process();
if ($backup->get_stage() == backup_ui::STAGE_FINAL) {
    $backup->execute();
} else {
    $backup->save_controller();
}

if ($backup->get_stage() !== backup_ui::STAGE_COMPLETE) {
    $renderer = $PAGE->get_renderer('core', 'backup');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourseon', 'hub', !empty($hubname)?$hubname:$huburl), 3, 'main');
    if ($backup->enforce_changed_dependencies()) {
        echo $renderer->dependency_notification(get_string('dependenciesenforced', 'backup'));
    }
    echo $renderer->progress_bar($backup->get_progress_bar());
    echo $backup->display();
    echo $OUTPUT->footer();
    die();
}

//$backupfile = $backup->get_stage_results();
$backupfile = $bc->get_results();
$backupfile = $backupfile['backup_destination'];
//END backup processing

//retrieve the token to call the hub
$registrationmanager = new registration_manager();
$registeredhub = $registrationmanager->get_registeredhub($huburl);

//send backup file to the hub
$curl = new curl();
$params = array();
$params['filetype'] = HUB_BACKUP_FILE_TYPE;
$params['courseid'] = $hubcourseid;
$params['file'] = $backupfile;
$params['token'] = $registeredhub->token;
$curl->post($huburl . "/local/hub/webservice/upload.php", $params);

//delete the temp backup file from user_tohub aera
$backupfile->delete();

//redirect to the index publication page
redirect(new moodle_url('/course/publish/index.php',
                array('sesskey' => sesskey(), 'id' => $id, 
                    'published' => true, 'huburl' => $huburl, 'hubname' => $hubname)));
?>
