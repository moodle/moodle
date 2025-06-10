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
 * Macmillan Learning External Web Service
 *
 * @package    localml
 * @copyright  2017 Macmillan Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * References:
 *     https://docs.moodle.org/dev/Adding_a_web_service_to_a_plugin
 *     https://docs.moodle.org/dev/Web_services_API
 *     https://docs.moodle.org/dev/External_functions_API
 *     https://github.com/moodlehq/moodle-local_wstemplate
 *
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . "/externallib.php");
class local_ml_external extends external_api {


    // Delete module.
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function delete_module_parameters() {

        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course module id', VALUE_DEFAULT, 0)
            )
        );
    }


    // Delete a module.
    // @param $cmid int - coursemodule id.
    public static function delete_module($cmid) {

        global $USER;
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::delete_module_parameters(),
                array('cmid' => $cmid));
        $data = array('cmid' => $params['cmid']);
        $deletedmodule = course_delete_module($data['cmid']);
        return $deletedmodule;

    }


    // Not sure if these are required.
    public static function delete_module_returns() {
           return new external_value(PARAM_TEXT, 'Return is just text as well for now.');
    }


    // Create External Tool.
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function create_external_tool_parameters() {

        return new external_function_parameters(
                array(
                    'add' => new external_value(PARAM_TEXT, 'What to add', VALUE_DEFAULT, 'lti'),
                    'course' => new external_value(PARAM_INT, 'Course id', VALUE_DEFAULT, 3),
                    'name' => new external_value(PARAM_TEXT,
                                                 'Name of the tool link',
                                                 VALUE_DEFAULT,
                                                 'Some tool link by Ken from the plugin'),
                    'toolurl' => new external_value(PARAM_TEXT,
                                                    'Url of the external tool.',
                                                    VALUE_DEFAULT,
                                                    'https://dev-lmslink.bfwpub.com/index.php'),
                    'resourcekey' => new external_value(PARAM_TEXT, 'Resource, aka consumer key.', VALUE_DEFAULT, 'moodlekey'),
                    'password' => new external_value(PARAM_TEXT, 'Password, aka shared secret.', VALUE_DEFAULT, 'moodlesecret'),
                    'coursemodule' => new external_value(PARAM_INT, 'Id of course module', VALUE_DEFAULT, 5),
                    'modulename' => new external_value(PARAM_TEXT, 'Module name', VALUE_DEFAULT, 'lti'),
                    'section' => new external_value(PARAM_INT, 'Section id', VALUE_DEFAULT, 15),
                    'visible' => new external_value(PARAM_INT, 'Visibility', VALUE_DEFAULT, 1),
                    'visibleoncoursepage' => new external_value(PARAM_INT, 'Visibility on course page', VALUE_DEFAULT, 1),
                    'gradecat' => new external_value(PARAM_INT, 'Grade category', VALUE_DEFAULT, 2),
                    'launchcontainer' => new external_value(PARAM_INT, 'The launch container type', VALUE_DEFAULT, 1),
                    'showtitlelaunch' => new external_value(PARAM_INT, 'Show title launch whatever that is', VALUE_DEFAULT, 1),
                    'icon' => new external_value(PARAM_TEXT,
                                                 'Maybe the icon url',
                                                 VALUE_DEFAULT,
                                                 'http://tinyurl.com/yasf4vnw'),
                    'description' => new external_value(PARAM_TEXT, 'Activity description', VALUE_DEFAULT, ''),
                    'grade' => new external_value(PARAM_FLOAT, 'Points possible', VALUE_DEFAULT, 100)
                )
        );

    }

    // Create an lti link.
    public static function create_external_tool($add,
                                                $course,
                                                $name,
                                                $toolurl,
                                                $resourcekey,
                                                $password,
                                                $coursemodule,
                                                $modulename,
                                                $section,
                                                $visible,
                                                $visibleoncoursepage,
                                                $gradecat,
                                                $launchcontainer,
                                                $showtitlelaunch,
                                                $icon,
                                                $description,
                                                $grade) {

        global $CFG;

        // Needed for create_module.
        require_once($CFG->dirroot . '/course/lib.php');

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::create_external_tool_parameters(),
                array(  'add' => $add,
                        'course' => $course,
                        'name' => $name,
                        'toolurl' => $toolurl,
                        'resourcekey' => $resourcekey,
                        'password' => $password,
                        'coursemodule' => $coursemodule,
                        'modulename' => $modulename,
                        'section' => $section,
                        'visible' => $visible,
                        'visibleoncoursepage' => $visibleoncoursepage,
                        'gradecat' => $gradecat,
                        'launchcontainer' => $launchcontainer,
                        'showtitlelaunch' => $showtitlelaunch,
                        'icon' => $icon,
                        'description' => $description,
                        'grade' => $grade
                        ));
        
        $moduleinfo = new StdClass();
        // These are required by create_module.
        $moduleinfo->modulename = $params['modulename'];
        $moduleinfo->course = $params['course'];
        $moduleinfo->section = $params['section'];
        $moduleinfo->visible = $params['visible'];
        // Moodle 3.3 seems to also require visibleoncoursepage. 
        $moduleinfo->visibleoncoursepage = $params['visibleoncoursepage'];
        $moduleinfo->introeditor = array(
                'text' => $params['description'],
                'format' => 1,
                'itemid' => '23423424342');  // This will be the description from PX ultimately.  No idea what the itemid is
        // These are not required by create_module but are presumably needed for add_moduleinfo (which is in course/modlib.php).
        $moduleinfo->name = $params['name'];
        $moduleinfo->toolurl = $params['toolurl'];
        $moduleinfo->resourcekey = $params['resourcekey'];
        $moduleinfo->password = $params['password'];
        $moduleinfo->showtitlelaunch = $params['showtitlelaunch'];
        $moduleinfo->icon = $params['icon'];
        $moduleinfo->gradecat = $params['gradecat'];
        // In the UI, if this option is not checked, we can't fill out the grade info.
        // So this seems to be a prerequisite for establishing max points.
        if (!empty($params['grade'])) {
            $moduleinfo->instructorchoiceacceptgrades = '1';
            $moduleinfo->grade = $params['grade'];
        } else {
            $moduleinfo->instructorchoiceacceptgrades = '0';
        }
        
        // This in turn calls add_moduleinfo.
        $moduleinfo = create_module($moduleinfo);
        return json_encode($moduleinfo);

    }

    public static function create_external_tool_returns() {
           return new external_value(PARAM_TEXT, 'Return is just text as well for now.');
    }



    // Update External Tool.
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function update_external_tool_parameters() {

        return new external_function_parameters(
                array(
                    'course' => new external_value(PARAM_INT, 'Course id', VALUE_DEFAULT, 3),
                    'name' => new external_value(PARAM_TEXT,
                                                 'Name of the tool link',
                                                 VALUE_DEFAULT,
                                                 'Some tool link by Ken from the plugin'),
                    'toolurl' => new external_value(PARAM_TEXT,
                                                    'Url of the external tool.',
                                                    VALUE_DEFAULT,
                                                    'https://dev-lmslink.bfwpub.com/index.php'),
                    'resourcekey' => new external_value(PARAM_TEXT, 'Resource, aka consumer key.', VALUE_DEFAULT, 'moodlekey'),
                    'password' => new external_value(PARAM_TEXT, 'Password, aka shared secret.', VALUE_DEFAULT, 'moodlesecret'),
                    'coursemodule' => new external_value(PARAM_INT, 'Id of course module', VALUE_DEFAULT, 5),
                    'modulename' => new external_value(PARAM_TEXT, 'Module name', VALUE_DEFAULT, 'lti'),
                    'section' => new external_value(PARAM_INT, 'Section id', VALUE_DEFAULT, 15),
                    'visible' => new external_value(PARAM_INT, 'Visibility', VALUE_DEFAULT, 1),
                    'visibleoncoursepage' => new external_value(PARAM_INT, 'Visibility on course page', VALUE_DEFAULT, 1),
                    'gradecat' => new external_value(PARAM_INT, 'Grade category', VALUE_DEFAULT, 2),
                    'launchcontainer' => new external_value(PARAM_INT, 'The launch container type', VALUE_DEFAULT, 1),
                    'showtitlelaunch' => new external_value(PARAM_INT, 'Show title launch whatever that is', VALUE_DEFAULT, 1),
                    'icon' => new external_value(PARAM_TEXT,
                                                 'Maybe the icon url',
                                                 VALUE_DEFAULT,
                                                 'http://tinyurl.com/yasf4vnw'),
                    'description' => new external_value(PARAM_TEXT, 'Activity description', VALUE_DEFAULT, ''),
                    'grade' => new external_value(PARAM_FLOAT, 'Points possible', VALUE_DEFAULT, 100)
                )
        );

    }

    // Update an lti link.
    public static function update_external_tool($course,
                                                $name,
                                                $toolurl,
                                                $resourcekey,
                                                $password,
                                                $coursemodule,
                                                $modulename,
                                                $section,
                                                $visible,
                                                $visibleoncoursepage,
                                                $gradecat,
                                                $launchcontainer,
                                                $showtitlelaunch,
                                                $icon,
                                                $description,
                                                $grade) {

        global $CFG;

        // Needed for create_module.
        require_once($CFG->dirroot . '/course/lib.php');

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::update_external_tool_parameters(),
                array(  'course' => $course,
                        'name' => $name,
                        'toolurl' => $toolurl,
                        'resourcekey' => $resourcekey,
                        'password' => $password,
                        'coursemodule' => $coursemodule,
                        'modulename' => $modulename,
                        'section' => $section,
                        'visible' => $visible,
                        'visibleoncoursepage' => $visibleoncoursepage,
                        'gradecat' => $gradecat,
                        'launchcontainer' => $launchcontainer,
                        'showtitlelaunch' => $showtitlelaunch,
                        'icon' => $icon,
                        'description' => $description,
                        'grade' => $grade
                        ));

        $moduleinfo = new StdClass();
        // These are required by create_module.
        $moduleinfo->coursemodule = $params['coursemodule'];
        $moduleinfo->modulename = $params['modulename'];
        $moduleinfo->course = $params['course'];
        $moduleinfo->section = $params['section'];
        $moduleinfo->visible = $params['visible'];
        // Moodle 3.3 seems to also require visibleoncoursepage.
        $moduleinfo->visibleoncoursepage = $params['visibleoncoursepage'];
        $moduleinfo->introeditor = array(
                'text' => $params['description'],
                'format' => 1,
                'itemid' => '23423424342');  // This will be the description from PX ultimately.  No idea what the itemid is
        // These are not required by create_module but are presumably needed for add_moduleinfo (which is in course/modlib.php).
        $moduleinfo->name = $params['name'];
        $moduleinfo->toolurl = $params['toolurl'];
        $moduleinfo->resourcekey = $params['resourcekey'];
        $moduleinfo->password = $params['password'];
        $moduleinfo->showtitlelaunch = $params['showtitlelaunch'];
        $moduleinfo->icon = $params['icon'];
        $moduleinfo->gradecat = $params['gradecat'];
        // In the UI, if this option is not checked, we can't fill out the grade info.
        // So this seems to be a prerequisite for establishing max points.
        if (!empty($params['grade'])) {
            $moduleinfo->instructorchoiceacceptgrades = '1';
            $moduleinfo->grade = $params['grade'];
        } else {
            $moduleinfo->instructorchoiceacceptgrades = '0';
        }
        // This in turn calls add_moduleinfo.
        $moduleinfo = update_module($moduleinfo);
        return json_encode($moduleinfo);

    }

    public static function update_external_tool_returns() {
           return new external_value(PARAM_TEXT, 'Return is just text as well for now.');
    }



    // Delete Assignment (Instance).
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function delete_assignment_parameters() {

        return new external_function_parameters(
                array(
                    'id' => new external_value(PARAM_INT, 'Id of the assignment instance.', VALUE_DEFAULT, 0)
                )
        );
    }


    // Delete Assignment.
    // @return string assignment instance id.
    public static function delete_assignment($id) {

        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/lib.php');

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::delete_assignment_parameters(), array('id' => $id));
        $response = assign_delete_instance($params['id']);
        return $response;

    }


    // Not sure if these are required.
    public static function delete_assignment_returns() {
           return new external_value(PARAM_TEXT, 'Return is just text as well for now.');
    }


    // Create Assignment.
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function create_assignment_parameters() {

        return new external_function_parameters(
                array(
                    'name' => new external_value(PARAM_TEXT, 'The assignment name.', VALUE_DEFAULT, 'Macmillan assignment'),
                    'timemodified' => new external_value(PARAM_INT, 'The time modified.', VALUE_DEFAULT, 1462907708),
                    'course' => new external_value(PARAM_INT, 'The id of the course.', VALUE_DEFAULT, 1),
                    'intro' => new external_value(PARAM_TEXT, 'The introduction of the assignment.', VALUE_DEFAULT, 'Introduction'),
                    'introformat' => new external_value(PARAM_INT, 'The introduction format of the assignment.', VALUE_DEFAULT, 1),
                    'alwaysshowdescription' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 1),
                    'nosubmissions' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'submissiondrafts' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'requiresubmissionstatement' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'sendnotifications' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'sendlatenotifications' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'sendstudentnotifications' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 1),
                    'duedate' => new external_value(PARAM_INT, 'The due date.', VALUE_DEFAULT, 1462907708),
                    'cutoffdate' => new external_value(PARAM_INT, 'The cutoff date.', VALUE_DEFAULT, 1462907708),
                    'allowsubmissionsfromdate' => new external_value(PARAM_INT,
                                                                     'Date from which to allow submissions.',
                                                                     VALUE_DEFAULT,
                                                                     1462907708),
                    'grade' => new external_value(PARAM_INT, 'The grade', VALUE_DEFAULT, 0),
                    'completionsubmit' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'teamsubmission' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'requireallteammemberssubmit' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'blindmarking' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'attemptreopenmethod' => new external_value(PARAM_TEXT, 'something', VALUE_DEFAULT, 'none'),
                    'markingworkflow' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'markingallocation' => new external_value(PARAM_INT, 'something', VALUE_DEFAULT, 0),
                    'coursemodule' => new external_value(PARAM_INT,
                                                         'The id of the coursemodule.  By default we can use 1.',
                                                         VALUE_DEFAULT,
                                                         1)
                )
        );

    }

     // Returns assignment instance id.
     // @return string assignment instance id.
    public static function create_assignment($name,
                                             $timemodified,
                                             $course,
                                             $intro,
                                             $introformat,
                                             $alwaysshowdescription,
                                             $nosubmissions,
                                             $submissiondrafts,
                                             $requiresubmissionstatement,
                                             $sendnotifications,
                                             $sendlatenotifications,
                                             $sendstudentnotifications,
                                             $duedate,
                                             $cutoffdate,
                                             $allowsubmissionsfromdate,
                                             $grade,
                                             $completionsubmit,
                                             $teamsubmission,
                                             $requireallteammemberssubmit,
                                             $blindmarking,
                                             $attemptreopenmethod,
                                             $markingworkflow,
                                             $markingallocation,
                                             $coursemodule) {

        global $USER;
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/lib.php');

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::create_assignment_parameters(),
                array(  'name' => $name,
                        'timemodified' => $timemodified,
                        'course' => $course,
                        'intro' => $intro,
                        'introformat' => $introformat,
                        'alwaysshowdescription' => $alwaysshowdescription,
                        'nosubmissions' => $nosubmissions,
                        'submissiondrafts' => $submissiondrafts,
                        'requiresubmissionstatement' => $requiresubmissionstatement,
                        'sendnotifications' => $sendnotifications,
                        'sendlatenotifications' => $sendlatenotifications,
                        'sendstudentnotifications' => $sendstudentnotifications,
                        'duedate' => $duedate,
                        'cutoffdate' => $duedate,
                        'allowsubmissionsfromdate' => $allowsubmissionsfromdate,
                        'grade' => $grade,
                        'completionsubmit' => $completionsubmit,
                        'teamsubmission' => $teamsubmission,
                        'requireallteammemberssubmit' => $requireallteammemberssubmit,
                        'blindmarking' => $blindmarking,
                        'attemptreopenmethod' => $attemptreopenmethod,
                        'markingworkflow' => $markingworkflow,
                        'markingallocation' => $markingallocation,
                        'coursemodule' => $coursemodule
                        ));

        $data = new StdClass();
        // Only name, timemodified, course, intro and duedate seem to be required.  Grade might be required for gradebook column.
        $data->name = $params['name'];
        $data->timemodified = $params['timemodified'];
        $data->course = $params['course'];
        $data->intro = $params['intro'];
        $data->introformat = $params['introformat'];
        $data->alwaysshowdescription = $params['alwaysshowdescription'];
        $data->nosubmissions = $params['nosubmissions'];
        $data->submissiondrafts = $params['submissiondrafts'];
        $data->requiresubmissionstatement = $params['requiresubmissionstatement'];
        $data->sendnotifications = $params['sendnotifications'];
        $data->sendlatenotifications = $params['sendlatenotifications'];
        $data->sendstudentnotifications = $params['sendstudentnotifications'];
        $data->duedate = $params['duedate'];
        $data->cutoffdate = $params['cutoffdate'];
        $data->allowsubmissionsfromdate = $params['allowsubmissionsfromdate'];
        $data->grade = $params['grade'];
        $data->completionsubmit = $params['completionsubmit'];
        $data->teamsubmission = $params['teamsubmission'];
        $data->requireallteammemberssubmit = $params['requireallteammemberssubmit'];
        $data->blindmarking = $params['blindmarking'];
        $data->attemptreopenmethod = $params['attemptreopenmethod'];
        $data->markingworkflow = $params['markingworkflow'];
        $data->markingallocation = $params['markingallocation'];
        $data->coursemodule = $params['coursemodule'];

        // Context validation.
        // OPTIONAL but in most web service it should present.
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        $assignment = assign_add_instance($data, null);
        return $assignment;

    }

    // Returns description of method result value.
    // @return external_description.
    public static function create_assignment_returns() {
        return new external_value(PARAM_TEXT, 'Return is just text as well for now.');
    }


    // Get course LTI.
    // Returns description of method parameters.
    // @return external_function_parameters.
    public static function get_course_lti_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'The course ID'))
        );
    }

    // Returns welcome message.
    // @return string welcome message.
    public static function get_course_lti($courseid) {
        global $USER;
        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::get_course_sections_parameters(),
            array('courseid' => $courseid));
        return self::retrieve_course_lti_from_db($courseid);
    }

    private static function retrieve_course_lti_from_db($courseid) {
        global $DB;

         $sql = <<< EOT
         SELECT
             l.id,
             l.name,
             l.timecreated,
             l.timemodified,
             l.course,
             t.baseurl,
             t.tooldomain
         FROM
             mdl_lti l
             INNER JOIN mdl_lti_types t ON
                 t.id = l.typeid
         WHERE l.course = ?;
EOT;
        $results = $DB->get_records_sql($sql, array($courseid));
        // I don't really want the index, though.
        $results = array_values($results);
        return $results;
    } // retrieve_course_sections_from_db

    // Returns description of method result value.
    // @return external_description.
    public static function get_course_lti_returns() {
        return new external_multiple_structure(
            new external_single_structure( // An object; the thing that will be listed.
                array( // Use an array to define the object's structure.
                    'id' => new external_value(PARAM_INT, 'lti id'), // Primary key.
                    'name' => new external_value(PARAM_TEXT, 'lti name'),
                    'timecreated' => new external_value(PARAM_INT, 'timestamp of time created'),
                    'timemodified' => new external_value(PARAM_INT, 'timestamp of time last modified'),
                    'course' => new external_value(PARAM_INT, 'id of course'),
                    'baseurl' => new external_value(PARAM_TEXT, 'lti base url'),
                    'tooldomain' => new external_value(PARAM_TEXT, 'lti tool domain'),
                )
            )
        );
    } // get_course_lti_returns

    // Update grade item title.
    // Returns description of update_grade_item_name method parameters.
    // @return external_function_parameters.
    public static function update_grade_item_name_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'The course ID'),
                'ltiid' => new external_value(PARAM_INT, 'The LTI ID'),
                'name' => new external_value(PARAM_TEXT, 'The new name'),
            )
        );
    } // update_grade_item_name_parameters

    /**
     * Updates the grade item name
     * @param integer $courseid
     * @param integer $ltiid
     * @param string $title
     * @return string Success message
     */
    public static function update_grade_item_name($courseid, $ltiid, $name) {
        global $USER;
        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::update_grade_item_name_parameters(),
            array(
                'courseid' => $courseid,
                'ltiid' => $ltiid,
                'name' => $name,
            )
        );
        /*
         //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);
        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
        throw new moodle_exception('cannotviewprofile');
        }
        */
        self::update_grade_item_name_by_db($courseid, $ltiid, $name);
        return 0;
    } // update_grade_item_name


    /**
     * Updates the LTI and grade item rows in the DB.
     * @param integer $courseid
     * @param integer $ltiid
     * @param string $name
     * @throws Exception
     */
    private static function update_grade_item_name_by_db($courseid, $ltiid, $name) {
         global $DB;

         // Update the grade item row, best effort.
         $params = array(
             'courseid' => $courseid,
             'itemtype' => 'mod',
             'itemmodule' => 'lti',
             'iteminstance' => $ltiid,
         );
         $gradeitem = $DB->get_record('grade_items', $params, 'id');
        if (empty($gradeitem->id)) {
             throw new Exception("Cannot get grade item for course ID {$courseid} LTI ID: {$ltiid}");
        }

         $params = new stdClass();
         $params->id = $gradeitem->id;
         $params->itemname = $name;
         $DB->update_record('grade_items', $params);
    } // update_grade_item_name_by_db


    // Returns description of method result value.
    // @return external_description.
    public static function update_grade_item_name_returns() {
         // There is precedent that in methods like this, upon success, 0 is retuned.
         // (There is precedent for other convensions, too, (like empty body) but that's another matter.)
         // If there's an error, Moodle takes care of that and returns a JSON error structure.
         return new external_value(PARAM_INT, '0=Success');
    } // update_grade_item_name_returns

}

/* End of file externallib.php */
