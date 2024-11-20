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
 * Lib file of jupyternotebook
 *
 * @package   mod_jupyternotebook
 * @copyright 2021 DNE - Ministere de l'Education Nationale 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function jupyternotebook_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function jupyternotebook_get_view_actions() {
    return array('view');
}


/**
 * Add jupyternotebook instance.
 * @param object $data
 * @param object $mform
 * @return int new url instance id
 */
function jupyternotebook_add_instance($data) {
    global $DB;

    $data->timemodified = time();
    $data->id = $DB->insert_record('jupyternotebook', $data);

    $cmid = $data->coursemodule;
    $draftitemid = $data->files;
    $context = context_module::instance($cmid);

    if ($draftitemid) {
        $options = array('subdirs' => true, 'embed' => false);
        file_save_draft_area_files($draftitemid, $context->id, 'mod_jupyternotebook', 'content', 0, $options);
    }
    return $data->id;
}


/**
 * Update url instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function jupyternotebook_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('jupyternotebook', $data);

    $cmid = $data->coursemodule;
    $draftitemid = $data->files;
    $context = context_module::instance($cmid);
    if ($draftitemid) {
        $options = array('subdirs' => true, 'embed' => false);
        file_save_draft_area_files($draftitemid, $context->id, 'mod_jupyternotebook', 'content', 0, $options);
    }

    return true;
}

/**
 * Delete url instance.
 * @param int $id
 * @return bool true
 */
function jupyternotebook_delete_instance($id) {
    global $DB;
    if (!$jupyternotebook = $DB->get_record('jupyternotebook', array('id'=>$id))) {
        return false;
    }

    $DB->delete_records('jupyternotebook', array('id'=>$id));

    return true;
}

/**
 * Mark that the jupyternotebook has been view
 * @param stdClass $jupyter The jupyternotebook record
 * @param stdClass $course The course record
 * @param stdClass $cm Course module object
 * @param stdClass $context Context instance  
 */
function jupyternotebook_view($jupyter, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $jupyter->id
    );

    $event = \mod_url\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('jupyternotebook', $jupyter);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}


/**
 * Return the notebook url
 * @param stdClass $jupertermod The jupyternotebook record
 * @param stdClass $user User record
 * @return string The notebook url  
 */
function jupyternotebook_get_url($jupertermod, $user)
{
    $pnid = jupyternotebook_get_personalnotebookid($user);

    return $jupertermod->serverurl.'/'.$jupertermod->jpcourseid.'/'.$jupertermod->jpnotebookid.'/'.$pnid;
}


/**
 * Return personal notebook id
 * @param stdClass $user User record
 * @return int The personal notebook id  
 */
function jupyternotebook_get_personalnotebookid($user)
{
    global $DB;

    if(($data = $DB->get_record('jp_notebook', array('userid' => $user->id)))){
        return $data->personalnotebookid;
    }

    $data = new stdClass();
    $data->userid = $user->id;

    $hashprefix = 'mag';
    $hash = sha1($user->email);
    $hash = substr($hash, 0, 31-strlen($hashprefix));
    $data->personalnotebookid = $hashprefix.$hash;

    $DB->insert_record('jp_notebook', $data);

    return $data->personalnotebookid;

}

function jupyternotebook_cm_info_view(cm_info $cm) {
    global $DB, $CFG, $USER;

    require_once($CFG->dirroot.'/lib/resourcelib.php');

    $mod_jupyter = $DB->get_record('jupyternotebook', array('id'=>$cm->instance));
    $context = context_module::instance($mod_jupyter->id);
    $content = '';
    if ($mod_jupyter->showdescription && trim($mod_jupyter->intro) != '') {
        $options = array('noclean'=>true, 'para'=>false, 'filter'=>true, 'context'=>$context, 'overflowdiv'=>false);
        $content .= html_writer::tag('p', trim(format_text($mod_jupyter->intro, $mod_jupyter->introformat, $options, null)));
    }
    if ($mod_jupyter->displayoptions == RESOURCELIB_DISPLAY_EMBED) {
        $content .= html_writer::tag('iframe', '', array('src' => jupyternotebook_get_url($mod_jupyter, $USER), 'height' => $mod_jupyter->iframeheight, 'width' => '100%'));
    }

    $cm->set_content($content);
}
