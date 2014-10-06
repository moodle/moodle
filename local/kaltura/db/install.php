<?php


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
 * Kaltura Post installation and migration code.
 *
 * @package    local
 * @subpackage kaltura
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/locallib.php');

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}



function xmldb_local_kaltura_install() {

    // Copy plug configuration data
    migrate_configuration_data();

    // Create new Kaltura video resource/presentations from old resource types
    migrate_resource_data();


    // Create new Kaltura video assignment from old assignment type and update all
    // user data pertaining to that assignment
    migrate_assignment_data();

    return true;
}

/**
 * This function migrates old video assignment type data and creates new Kaltura
 * video assignment modules from the old data.  Updates the course modules table
 * to refer to the newly created video assignment module.  Updates the
 * grade_items table to refer to the newly created assignment module.  If grades
 * are found in the grade_grades table, create a new video assignment submission
 * record using the data from the old video assignment submission record and
 * remove the old assignment submission data.  Lastly the old assignment record
 * is remove.
 */
function migrate_assignment_data() {
    global $DB;

    $dbman                  = $DB->get_manager();
    $assign_table_exists    = false;
    $module_exists          = false;
    $rebuild_courses        = array();

    $table = new xmldb_table('assignment');
    if ($dbman->table_exists($table)) {
        $assign_table_exists = true;
    }

    if ($module = $DB->get_record('modules', array('name'=> 'kalvidassign'))) {
        $module_exists = true;
    }

    if ($assign_table_exists && $module_exists) {
        
        try {
            //Check of the kalvidassign module exists and retrieve all old assignments
            $module_table_exists = kalvidassign_exists($dbman);
    
            $params = array('assignmenttype' => 'kaltura');
            $old_assignments = $DB->get_records('assignment', $params);
    
            if (!empty($old_assignments)) {
    
                foreach ($old_assignments as $assignment) {
    
                    $courseid                   = $assignment->course;
                    $rebuild_courses[$courseid] = $courseid;
                    $kalvidassign_obj           = create_new_kalvidassign($assignment);
                    $kalvidassign_id            = add_new_kalvidassign($kalvidassign_obj);
    
                    if ($kalvidassign_id) {
    
                        // Update calendar event
                        update_calendar_event($kalvidassign_id, $assignment->id);
    
                        // update course modules record for the video assignment by
                        // setting it to point to the new kalvidassign instance
                        $old_assignment_cm = get_coursemodule_from_instance('assignment', $assignment->id);
    
                        if (empty($old_assignment_cm)) {
                            continue;
                        }
    
                        $cm           = new stdClass();
                        $cm->id       = $old_assignment_cm->id;
                        $cm->module   = $module->id;
                        $cm->instance = $kalvidassign_id;
    
                        // Replace the old assignment type reference with a reference to the new
                        // assignment module
                        if ($DB->update_record('course_modules', $cm)) {
    
                            $param = array('itemtype'     => 'mod',
                                           'itemmodule'   => 'assignment',
                                           'iteminstance' => $assignment->id);

                            $grade_item = $DB->get_record('grade_items', $param);
    
                            // If this assignment has a grade item then update the references to point to the new
                            // assignment module
                            if (!empty($grade_item)) {
    
                                // Now update the grade_items record
                                $grade_item->itemmodule   = 'kalvidassign';
                                $grade_item->iteminstance = $kalvidassign_id;
    
                                if ($DB->update_record('grade_items', $grade_item)) {
    
                                    $param = array('itemid' => $grade_item->id);
                                    $grade_grades = $DB->get_records('grade_grades', $param, 'id,userid');
    
                                    if (!empty($grade_grades)) {
    
                                        foreach ($grade_grades as $grade_grade) {
    
                                            $param = array('assignment' => $assignment->id,
                                                           'userid' => $grade_grade->userid);
                                            $assign_sub = $DB->get_record('assignment_submissions', $param);
    
                                            if (!empty($assign_sub) && !empty($assign_sub->data1)) {
    
                                                // Create new user assignment submission
                                                create_new_kalvidassign_submission($kalvidassign_id, $assign_sub);
    
                                                // Remove old submission
                                                $param = array('id' => $assign_sub->id);
                                                $DB->delete_records('assignment_submissions',$param);
    
                                            } // end of if submission exists
    
                                        } // end of foreach loop grade_grades
    
                                    } // end of if empty grade_grades
    
                                } // end of if update grade item failed
    
                            } // end of if grade item exists
    
                            // Delete old assignment record
                            $param = array('id' => $assignment->id);
                            $DB->delete_records('assignment', $param);
    
                        }
    
                    }
    
                }
            }
        } catch (Exception $exp) {
            add_to_log(SITEID, 'local_kaltura', 'Data migration error', '', $exp->getMessage());
        }
    
    }
    
    foreach ($rebuild_courses as $courseid) {
        rebuild_course_cache($courseid);
    }

}

/**
 * Updates the calendar event entry to refer to the new video assignment
 * instance
 *
 * @param int - Id of new assignment instance
 * @param int - Id of old assignment instance
 */
function update_calendar_event($new_assignment_id, $old_assignment_id) {
    global $DB;

    $param = array('modulename' => 'assignment',
                   'instance' => $old_assignment_id);
    $event = $DB->get_record('event', $param);

    if (!empty($event)) {
        $event->modulename = 'kalvidassign';
        $event->instance = $new_assignment_id;

        $DB->update_record('event', $event);
    }
}

/**
 * This function migrates old resource data from the resource_old table and
 * creates new kaltura video resource/presentations modules with the old data.
 * Updates the course modules table to point to the new video
 * resource/presentation modules.  Removes old resource data from the
 * resource_old table as well as from the resource table
 *
 */
function migrate_resource_data() {
    global $CFG, $DB;

    $dbman               = $DB->get_manager();
    $module_table_exists = false;
    $kalvidpres_exists   = false;
    $resource_old_exists = false;

    // Check if the mdl_resource_old table exists and has any entries.  If so then we may have old resource activites to upgrade
    $table = new xmldb_table('resource_old');
    if ($dbman->table_exists($table)) {
        $resource_old_exists = true;
    }

    // If resource old exists run through the upgrade steps to migrate the data into individual modules
    if ($resource_old_exists) {

        // Check of the kalvidres module exists and include it's lib.php
        $module_table_exists = kalvidres_exists($dbman);

        if ($module_table_exists) {
            require_once($CFG->dirroot.'/mod/kalvidres/lib.php');
        }

        // Check of the kalvidpres module exists and include it's lib.php
        $module_table_exists = kalvidpres_exists($dbman);

        if ($module_table_exists) {
            require_once($CFG->dirroot.'/mod/kalvidpres/lib.php');
        }

        $params = array('migrated' => 0,
                       'vr' => 'kalturavideo',
                       'vp' => 'kalturaswfdoc');
        $sql = "SELECT *
                FROM {resource_old}
                WHERE migrated = :migrated
                AND (type = :vr OR type = :vp)";

        $kaltura_old_resources = $DB->get_records_sql($sql, $params);

        // If old resources have been found then we must convert them into new plugins
        if (!empty($kaltura_old_resources)) {

            foreach ($kaltura_old_resources as $old_resource) {

                // If a kalture video resoruce is found
                if (0 == strcmp('kalturavideo', $old_resource->type)) {

                    // Get module information for the kaltura video resource and add a new instance
                    if (!$module = $DB->get_record('modules', array('name'=> 'kalvidres'))) {
                        continue;
                    }

                    // Create an instance of the Kaltura video resource
                    $kalvidres_obj     = create_new_kalvidres($old_resource);
                    $kalvidres_inst_id = kalvidres_add_instance($kalvidres_obj);

                    // If add instance was successful
                    if ($kalvidres_inst_id) {

                        // update course modules record for the video resourse by setting it to point to the new kalvidres instance
                        $cm = new stdClass();
                        $cm->id       = $old_resource->cmid;
                        $cm->module   = $module->id;
                        $cm->instance = $kalvidres_inst_id;

                        // If update successful remove references to the obsolete video resource
                        if (!is_null($old_resource->cmid) && $DB->update_record('course_modules', $cm)) {

                            // Remove old instance of the resource/kalturavideo module from the resource table
                            $param = array('id' => $old_resource->id);
                            $DB->delete_records('resource', $param);

                            // Remove the record of the old instance from the resource_old table
                            $DB->delete_records('resource_old', $param);

                        } else { // Remove instance from module table

                            $param = array('id' => $kalvidres_inst_id);
                            $DB->delete_records('kalvidres', $param);
                        }
                    }

                } else if (0 == strcmp('kalturaswfdoc', $old_resource->type)) {
                    // If Kaltura video presentation is found

                    // Get module information for the kaltura video presention and add a new instance
                    if (!$module = $DB->get_record('modules', array('name'=> 'kalvidpres'))) {
                        continue;
                    }

                    // Create an instance of the Kaltura video presentation
                    $kalvidpres_obj     = create_new_kalvidpres($old_resource);
                    $kalvidpres_inst_id = kalvidpres_add_instance($kalvidpres_obj);

                    // If add instance was successful
                    if ($kalvidpres_inst_id) {

                        // update course modules record for the video presentation by setting it to point to the new kalvidres instance
                        $cm = new stdClass();
                        $cm->id       = $old_resource->cmid;
                        $cm->module   = $module->id;
                        $cm->instance = $kalvidpres_inst_id;

                        // If update successful remove references to the obsolete video resource
                        if ($DB->update_record('course_modules', $cm)) {

                            // Remove old instance of the resource/kalturaswfdoc module from the resource table
                            $param = array('id' => $old_resource->id);
                            $DB->delete_records('resource', $param);

                            // Remove the record of the old instance from the resource_old table
                            $DB->delete_records('resource_old', $param);

                        } else { // Remove instance from module table

                            $param = array('id' => $kalvidpres_inst_id);
                            $DB->delete_records('kalvidres', $param);
                        }
                    }
                }
            }
        }
    }
}

/**
 * Update Kaltura 1.9 configuration settings to 2.1 spec settings.
 * This function also removes all of the old 1.9 player configurations
 */
function migrate_configuration_data() {
    global $DB;

    $param    = array('plugin' => 'block_kaltura');
    $records  = $DB->get_records('config_plugins', $param);
    $name_map = configuration_data_mapping();

    if (empty($records)) {
        return true;
    }

    foreach ($records as $record) {
        switch ($record->name) {
            case 'kaltura_conn_server':
            case 'kaltura_uri':
            case 'kaltura_login':
            case 'kaltura_password':
            case 'kaltura_secret':
            case 'kaltura_adminsecret':
            case 'kaltura_partner_id':

                $name = $record->name;

                if (array_key_exists($name , $name_map)) {

                    $record->plugin = 'local_kaltura';
                    $record->name = $name_map[$name ];

                    $DB->update_record('config_plugins', $record);
                }

                break;
            default:

                $param = array ('id' => $record->id);
                $DB->delete_records('config_plugins', $param);
                break;
        }

    }

}

/**
 * Constructs and returns an array of Moodle 1.9 kaltura configuration name
 * mappings where the key is the 1.9 configuraiton name and the value is the 2.1
 * configuration name
 *
 * @param - none
 * @return array - array key 1.9 names, value 2.1 names
 */
function configuration_data_mapping() {
    return array('kaltura_conn_server' => 'conn_server',
                 'kaltura_uri'         => 'uri',
                 'kaltura_login'       => 'login',
                 'kaltura_password'    => 'password',
                 'kaltura_secret'      => 'secret',
                 'kaltura_adminsecret' => 'adminsecret',
                 'kaltura_partner_id'  => 'partner_id');
}

/**
 * Adds a new instance of the kaltura video assignment
 *
 * @param object - a kaltura video assignment instance object
 * @return int - id of the newly inserted record or false
 */
function add_new_kalvidassign($kalvidassign) {
    global $DB;

    $id = $DB->insert_record('kalvidassign', $kalvidassign);

    return $id;
}

/**
 * Construct a kaltura video assignmentobject using parameters from a Moodle 1.9
 * kaltura video assignment type
 *
 * @param object - kaltura video assignment object (ver: Moodle 1.9)
 * @return object - kaltura video assignment object (var: Moodle 2.1)
 */
function create_new_kalvidassign($old_assignment) {

    $kalvidassign = new stdClass();

    $kalvidassign->course           = $old_assignment->course;
    $kalvidassign->name             = $old_assignment->name;
    $kalvidassign->intro            = $old_assignment->intro;
    $kalvidassign->introformat      = $old_assignment->introformat;
    $kalvidassign->timeavailable    = $old_assignment->timeavailable;
    $kalvidassign->timedue          = $old_assignment->timedue;
    $kalvidassign->preventlate      = $old_assignment->preventlate;
    $kalvidassign->resubmit         = $old_assignment->resubmit;
    $kalvidassign->emailteachers    = $old_assignment->emailteachers;
    $kalvidassign->grade            = $old_assignment->grade;
    $kalvidassign->timecreated      = $old_assignment->timemodified;

    return $kalvidassign;
}

/**
 * Adds a new instance of the kaltura video assignment submission
 *
 * @param int - Id of the kaltura video assignment the submission is for
 * @param object - old video assignment submission object
 *
 * @return int - id of the newly inserted record or false
 */
function create_new_kalvidassign_submission($kalvidassign_id, $old_assign_sub) {
    global $DB;

    $kalvidassign_sub                       = new stdClass();
    $kalvidassign_sub->vidassignid          = $kalvidassign_id;
    $kalvidassign_sub->userid               = $old_assign_sub->userid;
    $kalvidassign_sub->entry_id             = $old_assign_sub->data1;
    $kalvidassign_sub->grade                = $old_assign_sub->grade;
    $kalvidassign_sub->submissioncomment    = $old_assign_sub->submissioncomment;
    $kalvidassign_sub->format               = $old_assign_sub->format;
    $kalvidassign_sub->teacher              = $old_assign_sub->teacher;
    $kalvidassign_sub->mailed               = $old_assign_sub->mailed;
    $kalvidassign_sub->timemarked           = $old_assign_sub->timemarked;
    $kalvidassign_sub->timecreated          = $old_assign_sub->timecreated;
    $kalvidassign_sub->timemodified         = $old_assign_sub->timemodified;

    $id = $DB->insert_record('kalvidassign_submission', $kalvidassign_sub);

    return $id;
}

/**
 * Construct a kaltura video resource object using parameters from a Moodle 1.9
 * kaltura video resource
 *
 * @param object - kaltura video resource object (ver: Moodle 1.9)
 * @return object - kaltura video resource object (var: Moodle 2.1)
 */
function create_new_kalvidres($old_resource) {

    $kalvidres = new stdClass();

    $kalvidres->course       = $old_resource->course;
    $kalvidres->name         = $old_resource->name;
    $kalvidres->intro        = $old_resource->intro;
    $kalvidres->introformat  = $old_resource->introformat;
    $kalvidres->entry_id     = $old_resource->alltext;
    $kalvidres->video_title  = $old_resource->name;
    $kalvidres->uiconf_id    = KALTURA_PLAYER_PLAYERREGULARDARK;
    $kalvidres->widescreen   = 0;
    $kalvidres->height       = 365;
    $kalvidres->width        = 400;

    return $kalvidres;
}

/**
 * Construct a kaltura video presentation object using parameters from a Moodle
 * 1.9 kaltura video resource
 *
 * @param object - kaltura video presentation object (ver: Moodle 1.9)
 * @return object - kaltura video presentation object (var: Moodle 2.1)
 */
function create_new_kalvidpres($old_resource) {

    $kalvidpres = new stdClass();

    $kalvidpres->course         = $old_resource->course;
    $kalvidpres->name           = $old_resource->name;
    $kalvidpres->intro          = $old_resource->intro;
    $kalvidpres->introformat    = $old_resource->introformat;
    $kalvidpres->entry_id       = $old_resource->alltext;
    $kalvidpres->video_entry_id = $old_resource->alltext;
    $kalvidpres->doc_entry_id   = $old_resource->alltext;
    $kalvidpres->video_title    = $old_resource->name;
    $kalvidpres->uiconf_id      = KALTURA_PLAYER_PLAYERVIDEOPRESENTATION;
    $kalvidpres->widescreen     = 0;
    $kalvidpres->height         = 365;
    $kalvidpres->width          = 400;

    return $kalvidpres;
}


/**
 * Check if the Kaltura video assignment table schema exists
 *
 * @para object - db manager
 * @return bool - true if exists, else false
 */
function kalvidassign_exists($dbman) {
    global $CFG;

    // Check of the Kaltura video resource plugin exists
    $table = new xmldb_table('kalvidassign');

    if (!$dbman->table_exists($table)) {
        return false;
    }

    if (!file_exists($CFG->dirroot.'/mod/kalvidassign/lib.php')) {
        return false;
    }

    return true;
}

/**
 * Check if the Kaltura video resource table schema exists
 *
 * @para object - db manager
 * @return bool - true if exists, else false
 */
function kalvidres_exists($dbman) {
    global $CFG;

    // Check of the Kaltura video resource plugin exists
    $table = new xmldb_table('kalvidres');

    if (!$dbman->table_exists($table)) {
        return false;
    }

    if (!file_exists($CFG->dirroot.'/mod/kalvidres/lib.php')) {
        return false;
    }

    return true;
}

/**
 * Check if the Kaltura video presentation table schema exists
 *
 * @para object - db manager
 * @return bool - true if exists, else false
 */
function kalvidpres_exists($dbman) {
    global $CFG;

    // Check of the Kaltura video pres plugin exists
    $table = new xmldb_table('kalvidpres');

    if (!$dbman->table_exists($table)) {
        return false;
    }

    if (!file_exists($CFG->dirroot.'/mod/kalvidpres/lib.php')) {
        return false;
    }

    return true;
}
