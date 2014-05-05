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
 * Data generators for acceptance testing.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Exception\PendingException as PendingException;

/**
 * Class to set up quickly a Given environment.
 *
 * Acceptance tests are block-boxed, so this steps definitions should only
 * be used to set up the test environment as we are not replicating user steps.
 *
 * All data generators should be in lib/testing/generator/*, shared between phpunit
 * and behat and they should be called from here, if possible using the standard
 * 'create_$elementname($options)' and if it's not possible (data generators arguments will not be
 * always the same) or the element is not suitable to be a data generator, create a
 * 'process_$elementname($options)' method and use the data generator from there if possible.
 *
 * @todo      If the available elements list grows too much this class must be split into smaller pieces
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_data_generators extends behat_base {

    /**
     * @var testing_data_generator
     */
    protected $datagenerator;

    /**
     * Each element specifies:
     * - The data generator sufix used.
     * - The required fields.
     * - The mapping between other elements references and database field names.
     * @var array
     */
    protected static $elements = array(
        'users' => array(
            'datagenerator' => 'user',
            'required' => array('username')
        ),
        'categories' => array(
            'datagenerator' => 'category',
            'required' => array('idnumber'),
            'switchids' => array('category' => 'parent')
        ),
        'courses' => array(
            'datagenerator' => 'course',
            'required' => array('shortname'),
            'switchids' => array('category' => 'category')
        ),
        'groups' => array(
            'datagenerator' => 'group',
            'required' => array('idnumber', 'course'),
            'switchids' => array('course' => 'courseid')
        ),
        'groupings' => array(
            'datagenerator' => 'grouping',
            'required' => array('idnumber', 'course'),
            'switchids' => array('course' => 'courseid')
        ),
        'course enrolments' => array(
            'datagenerator' => 'enrol_user',
            'required' => array('user', 'course', 'role'),
            'switchids' => array('user' => 'userid', 'course' => 'courseid', 'role' => 'roleid')

        ),
        'permission overrides' => array(
            'datagenerator' => 'permission_override',
            'required' => array('capability', 'permission', 'role', 'contextlevel', 'reference'),
            'switchids' => array('role' => 'roleid')
        ),
        'system role assigns' => array(
            'datagenerator' => 'system_role_assign',
            'required' => array('user', 'role'),
            'switchids' => array('user' => 'userid', 'role' => 'roleid')
        ),
        'role assigns' => array(
            'datagenerator' => 'role_assign',
            'required' => array('user', 'role', 'contextlevel', 'reference'),
            'switchids' => array('user' => 'userid', 'role' => 'roleid')
        ),
        'activities' => array(
            'datagenerator' => 'activity',
            'required' => array('activity', 'idnumber', 'course'),
            'switchids' => array('course' => 'course')
        ),
        'group members' => array(
            'datagenerator' => 'group_member',
            'required' => array('user', 'group'),
            'switchids' => array('user' => 'userid', 'group' => 'groupid')
        ),
        'grouping groups' => array(
            'datagenerator' => 'grouping_group',
            'required' => array('grouping', 'group'),
            'switchids' => array('grouping' => 'groupingid', 'group' => 'groupid')
        ),
        'cohorts' => array(
            'datagenerator' => 'cohort',
            'required' => array('idnumber')
        ),
        'roles' => array(
            'datagenerator' => 'role',
            'required' => array('shortname')
        )
    );

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    public function the_following_exist($elementname, TableNode $data) {

        // Now that we need them require the data generators.
        require_once(__DIR__ . '/../../testing/generator/lib.php');

        if (empty(self::$elements[$elementname])) {
            throw new PendingException($elementname . ' data generator is not implemented');
        }

        $this->datagenerator = testing_util::get_data_generator();

        $elementdatagenerator = self::$elements[$elementname]['datagenerator'];
        $requiredfields = self::$elements[$elementname]['required'];
        if (!empty(self::$elements[$elementname]['switchids'])) {
            $switchids = self::$elements[$elementname]['switchids'];
        }

        foreach ($data->getHash() as $elementdata) {

            // Check if all the required fields are there.
            foreach ($requiredfields as $requiredfield) {
                if (!isset($elementdata[$requiredfield])) {
                    throw new Exception($elementname . ' requires the field ' . $requiredfield . ' to be specified');
                }
            }

            // Switch from human-friendly references to ids.
            if (isset($switchids)) {
                foreach ($switchids as $element => $field) {
                    $methodname = 'get_' . $element . '_id';

                    // Not all the switch fields are required, default vars will be assigned by data generators.
                    if (isset($elementdata[$element])) {
                        // Temp $id var to avoid problems when $element == $field.
                        $id = $this->{$methodname}($elementdata[$element]);
                        unset($elementdata[$element]);
                        $elementdata[$field] = $id;
                    }
                }
            }

            // Preprocess the entities that requires a special treatment.
            if (method_exists($this, 'preprocess_' . $elementdatagenerator)) {
                $elementdata = $this->{'preprocess_' . $elementdatagenerator}($elementdata);
            }

            // Creates element.
            $methodname = 'create_' . $elementdatagenerator;
            if (method_exists($this->datagenerator, $methodname)) {
                // Using data generators directly.
                $this->datagenerator->{$methodname}($elementdata);

            } else if (method_exists($this, 'process_' . $elementdatagenerator)) {
                // Using an alternative to the direct data generator call.
                $this->{'process_' . $elementdatagenerator}($elementdata);
            } else {
                throw new PendingException($elementname . ' data generator is not implemented');
            }
        }

    }

    /**
     * If password is not set it uses the username.
     * @param array $data
     * @return array
     */
    protected function preprocess_user($data) {
        if (!isset($data['password'])) {
            $data['password'] = $data['username'];
        }
        return $data;
    }

    /**
     * Adapter to modules generator
     * @throws Exception Custom exception for test writers
     * @param array $data
     * @return void
     */
    protected function process_activity($data) {
        global $DB;

        // The the_following_exists() method checks that the field exists.
        $activityname = $data['activity'];
        unset($data['activity']);

        // We split $data in the activity $record and the course module $options.
        $cmoptions = array();
        $cmcolumns = $DB->get_columns('course_modules');
        foreach ($cmcolumns as $key => $value) {
            if (isset($data[$key])) {
                $cmoptions[$key] = $data[$key];
            }
        }

        // Custom exception.
        try {
            $this->datagenerator->create_module($activityname, $data, $cmoptions);
        } catch (coding_exception $e) {
            throw new Exception('\'' . $activityname . '\' activity can not be added using this step,' .
                ' use the step \'I add a "ACTIVITY_OR_RESOURCE_NAME_STRING" to section "SECTION_NUMBER"\' instead');
        }
    }

    /**
     * Adapter to enrol_user() data generator.
     * @throws Exception
     * @param array $data
     * @return void
     */
    protected function process_enrol_user($data) {
        global $SITE;

        if (empty($data['roleid'])) {
            throw new Exception('\'course enrolments\' requires the field \'role\' to be specified');
        }

        if (!isset($data['userid'])) {
            throw new Exception('\'course enrolments\' requires the field \'user\' to be specified');
        }

        if (!isset($data['courseid'])) {
            throw new Exception('\'course enrolments\' requires the field \'course\' to be specified');
        }

        if (!isset($data['enrol'])) {
            $data['enrol'] = 'manual';
        }

        // If the provided course shortname is the site shortname we consider it a system role assign.
        if ($data['courseid'] == $SITE->id) {
            // Frontpage course assign.
            $context = context_course::instance($data['courseid']);
            role_assign($data['roleid'], $data['userid'], $context->id);

        } else {
            // Course assign.
            $this->datagenerator->enrol_user($data['userid'], $data['courseid'], $data['roleid'], $data['enrol']);
        }

    }

    /**
     * Allows/denies a capability at the specified context
     *
     * @throws Exception
     * @param array $data
     * @return void
     */
    protected function process_permission_override($data) {

        // Will throw an exception if it does not exist.
        $context = $this->get_context($data['contextlevel'], $data['reference']);

        switch ($data['permission']) {
            case get_string('allow', 'role'):
                $permission = CAP_ALLOW;
                break;
            case get_string('prevent', 'role'):
                $permission = CAP_PREVENT;
                break;
            case get_string('prohibit', 'role'):
                $permission = CAP_PROHIBIT;
                break;
            default:
                throw new Exception('The \'' . $data['permission'] . '\' permission does not exist');
                break;
        }

        if (is_null(get_capability_info($data['capability']))) {
            throw new Exception('The \'' . $data['capability'] . '\' capability does not exist');
        }

        role_change_permission($data['roleid'], $context, $data['capability'], $permission);
    }

    /**
     * Assigns a role to a user at system context
     *
     * Used by "system role assigns" can be deleted when
     * system role assign will be deprecated in favour of
     * "role assigns"
     *
     * @throws Exception
     * @param array $data
     * @return void
     */
    protected function process_system_role_assign($data) {

        if (empty($data['roleid'])) {
            throw new Exception('\'system role assigns\' requires the field \'role\' to be specified');
        }

        if (!isset($data['userid'])) {
            throw new Exception('\'system role assigns\' requires the field \'user\' to be specified');
        }

        $context = context_system::instance();

        $this->datagenerator->role_assign($data['roleid'], $data['userid'], $context->id);
    }

    /**
     * Assigns a role to a user at the specified context
     *
     * @throws Exception
     * @param array $data
     * @return void
     */
    protected function process_role_assign($data) {

        if (empty($data['roleid'])) {
            throw new Exception('\'role assigns\' requires the field \'role\' to be specified');
        }

        if (!isset($data['userid'])) {
            throw new Exception('\'role assigns\' requires the field \'user\' to be specified');
        }

        if (empty($data['contextlevel'])) {
            throw new Exception('\'role assigns\' requires the field \'contextlevel\' to be specified');
        }

        if (!isset($data['reference'])) {
            throw new Exception('\'role assigns\' requires the field \'reference\' to be specified');
        }

        // Getting the context id.
        $context = $this->get_context($data['contextlevel'], $data['reference']);

        $this->datagenerator->role_assign($data['roleid'], $data['userid'], $context->id);
    }

    /**
     * Creates a role.
     *
     * @param array $data
     * @return void
     */
    protected function process_role($data) {

        // We require the user to fill the role shortname.
        if (empty($data['shortname'])) {
            throw new Exception('\'role\' requires the field \'shortname\' to be specified');
        }

        $this->datagenerator->create_role($data);
    }

    /**
     * Gets the user id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_user_id($username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', array('username' => $username))) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the role id from it's shortname.
     * @throws Exception
     * @param string $roleshortname
     * @return int
     */
    protected function get_role_id($roleshortname) {
        global $DB;

        if (!$id = $DB->get_field('role', 'id', array('shortname' => $roleshortname))) {
            throw new Exception('The specified role with shortname "' . $roleshortname . '" does not exist');
        }

        return $id;
    }

    /**
     * Gets the category id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_category_id($idnumber) {
        global $DB;

        // If no category was specified use the data generator one.
        if ($idnumber == false) {
            return null;
        }

        if (!$id = $DB->get_field('course_categories', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified category with idnumber "' . $idnumber . '" does not exist');
        }

        return $id;
    }

    /**
     * Gets the course id from it's shortname.
     * @throws Exception
     * @param string $shortname
     * @return int
     */
    protected function get_course_id($shortname) {
        global $DB;

        if (!$id = $DB->get_field('course', 'id', array('shortname' => $shortname))) {
            throw new Exception('The specified course with shortname "' . $shortname . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the group id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_group_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('groups', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified group with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the grouping id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_grouping_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('groupings', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified grouping with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the internal context id from the context reference.
     *
     * The context reference changes depending on the context
     * level, it can be the system, a user, a category, a course or
     * a module.
     *
     * @throws Exception
     * @param string $levelname The context level string introduced by the test writer
     * @param string $contextref The context reference introduced by the test writer
     * @return context
     */
    protected function get_context($levelname, $contextref) {
        global $DB;

        // Getting context levels and names (we will be using the English ones as it is the test site language).
        $contextlevels = context_helper::get_all_levels();
        $contextnames = array();
        foreach ($contextlevels as $level => $classname) {
            $contextnames[context_helper::get_level_name($level)] = $level;
        }

        if (empty($contextnames[$levelname])) {
            throw new Exception('The specified "' . $levelname . '" context level does not exist');
        }
        $contextlevel = $contextnames[$levelname];

        // Return it, we don't need to look for other internal ids.
        if ($contextlevel == CONTEXT_SYSTEM) {
            return context_system::instance();
        }

        switch ($contextlevel) {

            case CONTEXT_USER:
                $instanceid = $DB->get_field('user', 'id', array('username' => $contextref));
                break;

            case CONTEXT_COURSECAT:
                $instanceid = $DB->get_field('course_categories', 'id', array('idnumber' => $contextref));
                break;

            case CONTEXT_COURSE:
                $instanceid = $DB->get_field('course', 'id', array('shortname' => $contextref));
                break;

            case CONTEXT_MODULE:
                $instanceid = $DB->get_field('course_modules', 'id', array('idnumber' => $contextref));
                break;

            default:
                break;
        }

        $contextclass = $contextlevels[$contextlevel];
        if (!$context = $contextclass::instance($instanceid, IGNORE_MISSING)) {
            throw new Exception('The specified "' . $contextref . '" context reference does not exist');
        }

        return $context;
    }

}
