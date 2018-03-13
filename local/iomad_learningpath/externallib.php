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
 * Web service declarations
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class local_iomad_learningpath_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function activate_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Learning Path'),
                'state' => new external_value(PARAM_INT, 'Active (1) / deactivate (0)'),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function activate_returns() {
        return new external_value(PARAM_BOOL, 'True if active state set correctly');
    }

    /**
     * Activate / Deactivate learning path
     * @param int $pathid 
     * @param int $state
     * @throws invalid_parameter_exception
     */
    public static function activate($pathid, $state) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::activate_parameters(), ['pathid' => $pathid, 'state' => $state]);

        // Find the learning path.
        if (!$path = $DB->get_record('local_iomad_learningpath', array('id' => $params['pathid']))) {
            throw new invalid_parameter_exception("Learning Path with id = $pathid does not exist");
        }

        // Check state
        if (($params['state'] != 0) && ($params['state'] != 1)) {
            throw new invalid_parameter_exception("State can only be 0 or 1. Value was $state");
        }
      
        // Set the new state.
        $path->active = $params['state'];
        $DB->update_record('local_iomad_learningpath', $path);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getprospectivecourses_parameters() {
        return new external_function_parameters(
            array(
                'companyid' => new external_value(PARAM_INT, 'ID of Iomad Company'),
                'filter' => new external_value(PARAM_TEXT, 'Filter course list returned', VALUE_DEFAULT, ''),
                'excludeids' => new external_multiple_structure(new external_value(PARAM_INT, 'Course ID'), 'List of course IDs to exclude', VALUE_DEFAULT, array()),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function getprospectivecourses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Course ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course fullname'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
                )
            )
        );
    }

    /**
     * Get list of possible courses
     * @param int $companyid 
     * @param int $filter
     * @param array $excludeids
     * @throws invalid_parameter_exception
     */
    public static function getprospectivecourses($companyid, $filter = '', $excludeids = array() ) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getprospectivecourses_parameters(), ['companyid' => $companyid, 'filter' => $filter, 'excludeids' => $excludeids]);

        // Find/validate company
        if (!$company = $DB->get_record('company', ['id' => $params['companyid']])) {
            throw new invalid_parameter_exception("Company with id = {$params['companyid']} does not exist");
        }

        // Get full list of prospective courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, context_system::instance());
        $allcourses = $companypaths->get_prospective_courses();

        // If filter, check there is a match
        $courses = [];
        foreach ($allcourses as $allcourse) {
            if ($params['filter'] && (stripos($allcourse->fullname, $params['filter']) === false)) {
                continue;
            }
            $courses[] = [
                'id' => $allcourse->id,
                'fullname' => $allcourse->fullname,
                'shortname' => $allcourse->shortname,
            ]; 
        }

        return $courses;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function addcourses_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'Course ID'), 'List of course IDs to add'),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function addcourses_returns() {
        return new external_value(PARAM_BOOL, 'True if courses added correctly');
    }

    /**
     * Add courses to learning path
     * @param int $pathid
     * @param array $courseids
     * @throws invalid_parameter_exception
     */
    public static function addcourses($pathid, $courseids) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::addcourses_parameters(), ['pathid' => $pathid, 'courseids' => $courseids]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Get full list of prospective courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, context_system::instance());
        $allcourses = $companypaths->get_prospective_courses();

        // Get existing list
        $count = $DB->count_records('iomad_learningpathcourse', ['path' => $params['pathid']]);

        // Work through courses.
        foreach ($params['courseids'] as $courseid) {
            if (!array_key_exists($courseid, $allcourses)) {
                throw new invalid_parameter_exception("Course with id=$courseid is not one of company courses");
            }

            // If course already in the list then just skip it
            if ($course = $DB->get_record('iomad_learningpathcourse', ['path' => $params['pathid'], 'course' => $courseid])) {
                continue;
            }

            // Add at the end
            $count++;
            $course = new stdClass;
            $course->path = $params['pathid'];
            $course->course = $courseid;
            $course->sequence = $count;
            $DB->insert_record('iomad_learningpathcourse', $course);
        }

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function removecourses_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'Course ID'), 'List of course IDs to remove'),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function removecourses_returns() {
        return new external_value(PARAM_BOOL, 'True if courses removed correctly');
    }

    /**
     * Remove courses from learning path
     * @param int $pathid
     * @param array $courseids
     * @throws invalid_parameter_exception
     */
    public static function removecourses($pathid, $courseids) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::removecourses_parameters(), ['pathid' => $pathid, 'courseids' => $courseids]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Work through courses.
        foreach ($params['courseids'] as $courseid) {
            $DB->delete_records('iomad_learningpathcourse', ['path' => $params['pathid'], 'course' => $courseid]);
        }

        // Fix the sequence
        $companypaths = new local_iomad_learningpath\companypaths($companyid, context_system::instance());
        $companypaths->fix_sequence($params['pathid']);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getcourses_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function getcourses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Course ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course fullname'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
                )
            )
        );
    }

    /**
     * Get list of courses in learning path
     * @param int $pathid 
     * @throws invalid_parameter_exception
     */
    public static function getcourses($pathid) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getcourses_parameters(), ['pathid' => $pathidid]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Get full list of courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, context_system::instance());
        $courses = $companypaths->get_courselist($params['pathid']);

        return $courses;
    }
}
