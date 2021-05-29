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
require_once($CFG->dirroot . '/local/iomad/lib/iomad.php');

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
        if (!$path = $DB->get_record('iomad_learningpath', array('id' => $params['pathid']))) {
            throw new invalid_parameter_exception("Learning Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Check state
        if (($params['state'] != 0) && ($params['state'] != 1)) {
            throw new invalid_parameter_exception("State can only be 0 or 1. Value was $state");
        }

        // Set the new state.
        $path->active = $params['state'];
        $DB->update_record('iomad_learningpath', $path);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getprospectivecourses_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of (target) learning path'),
                'filter' => new external_value(PARAM_TEXT, 'Filter course list returned', VALUE_DEFAULT, ''),
                'category' => new external_value(PARAM_INT, 'Show only courses in this category (and children)', VALUE_DEFAULT, 0),
                'program' => new external_value(PARAM_INT, 'Show only courses assigned to this program license', VALUE_DEFAULT, 0),
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
                    'image' => new external_value(PARAM_URL, 'Course image'),
                )
            )
        );
    }

    /**
     * Get list of possible courses
     * @param int $pathid
     * @param int $filter
     * @param int $category (id) (0 = show all)
     * @param array $excludeids
     * @throws invalid_parameter_exception
     */
    public static function getprospectivecourses($pathid, $filter = '', $category = 0, $program = 0) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getprospectivecourses_parameters(),
            ['pathid' => $pathid, 'filter' => $filter, 'category' => $category, 'program' => $program]);

        // Find learning path and company
        $path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']], '*', MUST_EXIST);
        $company = $DB->get_record('company', ['id' => $path->company]);
        $companyid = $company->id;

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Set up the company path object.
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);

        // Update the path licenseid.
        $companypaths->assign_license_to_plan($pathid, $program);

        // Get full list of prospective courses
        $courses = $companypaths->get_prospective_courses($params['pathid'], $params['filter'], $params['category'], $params['program']);

        // Just the bits we need
        $pcs = [];
        foreach ($courses as $course) {
            $pcs[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'image' => $course->image,
            ];
        }

        return $pcs;
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
                'groupid' => new external_value(PARAM_INT, 'ID of group. If 0 just add to lowest numbered group', VALUE_DEFAULT, 0),
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
     * $param int $groupid (0 = add to lowest group)
     * @throws invalid_parameter_exception
     */
    public static function addcourses($pathid, $courseids, $groupid = 0) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::addcourses_parameters(), ['pathid' => $pathid, 'courseids' => $courseids, 'groupid' => $groupid]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Add courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
        $companypaths->add_courses($params['pathid'], $params['courseids'], $params['groupid']);

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

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Remove courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
        $companypaths->remove_courses($params['pathid'], $params['courseids']);

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
                'groupid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path group (0 = return all)', VALUE_DEFAULT, 0),
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
                    'groupid' => new external_value(PARAM_INT, 'Group ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'Course fullname'),
                    'shortname' => new external_value(PARAM_TEXT, 'Course shortname'),
                    'image' => new external_value(PARAM_URL, 'Course image'),
                )
            )
        );
    }

    /**
     * Get list of courses in learning path
     * @param int $pathid
     * @param int $groupid
     * @throws invalid_parameter_exception
     */
    public static function getcourses($pathid, $groupid = 0) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getcourses_parameters(), ['pathid' => $pathid, 'groupid' => $groupid]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Get full list of courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
        $courses = $companypaths->get_courselist($params['pathid'], $params['groupid']);

        // Just the bits we need
        $ccs = [];
        foreach ($courses as $course) {
            $ccs[] = [
                'id' => $course->courseid,
                'groupid' => $params['groupid'],
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'image' => $course->image,
            ];
        }

        return $ccs;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function ordercourses_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                            'groupid' => new external_value(PARAM_INT, 'id of group'),
                        )
                    )
                ),
            )
        );
    }

    /**
     * Returns description of method result
     * @return external_description
     */
    public static function ordercourses_returns() {
        return new external_value(PARAM_BOOL, 'True if courses ordered correctly');
    }

    /**
     * Find course in list of (path) courses
     * @param array $courses
     * @param int $courseid
     * @return object or bool
     */
    protected static function find_course($courses, $courseid) {
        foreach ($courses as $course) {
            if ($course->course == $courseid) {
                return $course;
            }
        }

        return false;
    }

    /**
     * Order courses in learning path
     * (Valid) new course ids will simply be added in that position
     * Missing ones get deleted
     * @param int $pathid
     * @param array $courses of arrays {int courseid, int groupid}
     * @throws invalid_parameter_exception
     */
    public static function ordercourses($pathid, $courses) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::ordercourses_parameters(), ['pathid' => $pathid, 'courses' => $courses]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);

        // Find any new ones and add them
        // Also make a list of courseids for delete phase.
        $courseids = [];
        foreach ($params['courses'] as $course) {
            $courseids[] = $course['courseid'];
            if (!$DB->record_exists('iomad_learningpathcourse', ['path' => $params['pathid'], 'course' => $course['courseid']])) {
                $companypaths->add_courses($path->id, [$course['courseid']], $course['groupid']);
            }
        }

        // Find any missing ones and delete them
        $oldcourses = $DB->get_records('iomad_learningpathcourse', ['path' => $params['pathid']]);
        foreach ($oldcourses as $oldcourse) {
            if (!in_array($oldcourse->course, $courseids)) {
                $companypaths->remove_courses($path->id, [$oldcourse->course]);
            }
        }

        // Work through courses.
        $sequence = 1;
        foreach ($params['courses'] as $course) {
            $oldcourse = $DB->get_record('iomad_learningpathcourse', ['path' => $params['pathid'], 'course' => $course['courseid']], '*', MUST_EXIST);

            // Update sequence.
            $oldcourse->groupid = $course['groupid'];
            $oldcourse->sequence = $sequence;
            $sequence++;
            $DB->update_record('iomad_learningpathcourse', $oldcourse);
        }

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function deletepath_parameters() {
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
    public static function deletepath_returns() {
        return new external_value(PARAM_BOOL, 'True if courses added correctly');
    }

    /**
     * Delete learning path
     * @param int $pathid
     * @throws invalid_parameter_exception
     */
    public static function deletepath($pathid) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::deletepath_parameters(), ['pathid' => $pathid]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Get full list of prospective courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);

        // Delete path
        $companypaths->deletepath($params['pathid']);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function copypath_parameters() {
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
    public static function copypath_returns() {
        return new external_value(PARAM_BOOL, 'True if path copied correctly');
    }

    /**
     * Copy learning path
     * @param int $pathid
     * @throws invalid_parameter_exception
     */
    public static function copypath($pathid) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::copypath_parameters(), ['pathid' => $pathid]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Get full list of prospective courses
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);

        // Copy path
        $companypaths->copypath($params['pathid']);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getprospectiveusers_parameters() {
        return new external_function_parameters(
            array(
                'companyid' => new external_value(PARAM_INT, 'ID of Iomad Company'),
                'pathid' => new external_value(PARAM_INT, 'ID learning path'),
                'filter' => new external_value(PARAM_TEXT, 'Filter user list returned', VALUE_DEFAULT, ''),
                'profilefieldid' => new external_value(PARAM_INT, 'Filter by user profilefield', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns description of method result
     * @return external_description
     */
    public static function getprospectiveusers_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'User ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'User fullname'),
                    'email' => new external_value(PARAM_TEXT, 'User email'),
                )
            )
        );
    }

    /**
     * Get list of possible users
     * @param int $companyid
     * @param int $pathid
     * @param int $filter
     * @throws invalid_parameter_exception
     */
    public static function getprospectiveusers($companyid, $pathid, $filter, $profilefieldid) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getprospectiveusers_parameters(),
            ['companyid' => $companyid, 'pathid' => $pathid, 'filter' => $filter, 'profilefieldid' => $profilefieldid]);

        // Find/validate company
        if (!$company = $DB->get_record('company', ['id' => $params['companyid']])) {
            throw new invalid_parameter_exception("Company with id = {$params['companyid']} does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Get lists of users
        $companypaths = new local_iomad_learningpath\companypaths($params['companyid'], $context);
        $users = $companypaths->get_prospective_users($params['pathid'], $params['filter'], $params['profilefieldid']);

        return $users;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function addusers_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
                'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'User ID'), 'List of user IDs to add'),
            )
        );
    }

    /**
     * Returns description of method result
     * @return external_description
     */
    public static function addusers_returns() {
        return new external_value(PARAM_BOOL, 'True if users added correctly');
    }

    /**
     * Add users to learning path
     * @param int $pathid
     * @param array $userids
     * @throws invalid_parameter_exception
     */
    public static function addusers($pathid, $userids) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::addusers_parameters(), ['pathid' => $pathid, 'userids' => $userids]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Add users
        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
        $companypaths->add_users($params['pathid'], $params['userids']);

        return true;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function getusers_parameters() {
        return new external_function_parameters(
            array(
                'companyid' => new external_value(PARAM_INT, 'ID of Iomad Company'),
                'pathid' => new external_value(PARAM_INT, 'ID learning path'),
            )
        );
    }

    /**
     * Returns description of method result
     * @return external_description
     */
    public static function getusers_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'User ID'),
                    'fullname' => new external_value(PARAM_TEXT, 'User fullname'),
                    'email' => new external_value(PARAM_TEXT, 'User email'),
                )
            )
        );
    }

    /**
     * Get list of path users
     * @param int $companyid
     * @param int $pathid
     * @throws invalid_parameter_exception
     */
    public static function getusers($companyid, $pathid) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::getprospectiveusers_parameters(), ['companyid' => $companyid, 'pathid' => $pathid]);

        // Find/validate company
        if (!$company = $DB->get_record('company', ['id' => $params['companyid']])) {
            throw new invalid_parameter_exception("Company with id = {$params['companyid']} does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        // Get lists of users
        $companypaths = new local_iomad_learningpath\companypaths($params['companyid'], $context);
        $allusers = $companypaths->get_users($params['pathid']);

        // massage list
        $users = [];
        foreach ($allusers as $alluser) {
            $user = new stdClass;
            $user->id = $alluser->id;
            $user->fullname = $alluser->fullname;
            $user->email = $alluser->email;
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function removeusers_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Iomad Learning Path'),
                'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'User IDs'), 'List of course IDs to remove'),
            )
        );
    }

    /**
     * Returns description of method result
     * @return external_description
     */
    public static function removeusers_returns() {
        return new external_value(PARAM_BOOL, 'True if users removed correctly');
    }

    /**
     * Remove users from learning path
     * @param int $pathid
     * @param array $userids
     * @throws invalid_parameter_exception
     */
    public static function removeusers($pathid, $userids) {
        global $DB;

        // Validate params
        $params = self::validate_parameters(self::removeusers_parameters(), ['pathid' => $pathid, 'userids' => $userids]);

        // get path
        if (!$path = $DB->get_record('iomad_learningpath', ['id' => $params['pathid']])) {
            throw new invalid_parameter_exception("Path with id = $pathid does not exist");
        }

        // Find/validate company
        $companyid = $path->company;
        if (!$company = $DB->get_record('company', ['id' => $companyid])) {
            throw new invalid_parameter_exception("Company with id = $companyid does not exist");
        }

        // Security
        $context = context_system::instance();
        self::validate_context($context);
        iomad::require_capability('local/iomad_learningpath:manage', $context, $companyid);

        $companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
        $companypaths->delete_users($params['pathid'], $params['userids']);

        return true;
    }

}
