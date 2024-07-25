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

defined('MOODLE_INTERNAL') || die();


/**
 * Behat data generator class for core entities.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_generator extends behat_generator_base {

    protected function get_creatable_entities(): array {
        $entities = [
            'users' => [
                'singular' => 'user',
                'datagenerator' => 'user',
                'required' => ['username'],
            ],
            'categories' => [
                'singular' => 'category',
                'datagenerator' => 'category',
                'required' => ['idnumber'],
                'switchids' => ['category' => 'parent'],
            ],
            'courses' => [
                'singular' => 'course',
                'datagenerator' => 'course',
                'required' => ['shortname'],
                'switchids' => ['category' => 'category'],
            ],
            'groups' => [
                'singular' => 'group',
                'datagenerator' => 'group',
                'required' => ['idnumber', 'course'],
                'switchids' => ['course' => 'courseid'],
            ],
            'groupings' => [
                'singular' => 'grouping',
                'datagenerator' => 'grouping',
                'required' => ['idnumber', 'course'],
                'switchids' => ['course' => 'courseid'],
            ],
            'course enrolments' => [
                'singular' => 'course enrolment',
                'datagenerator' => 'enrol_user',
                'required' => ['user', 'course', 'role'],
                'switchids' => ['user' => 'userid', 'course' => 'courseid', 'role' => 'roleid'],
            ],
            'custom field categories' => [
                'singular' => 'custom field category',
                'datagenerator' => 'custom_field_category',
                'required' => ['name', 'component', 'area', 'itemid'],
                'switchids' => [],
            ],
            'custom fields' => [
                'singular' => 'custom field',
                'datagenerator' => 'custom_field',
                'required' => ['name', 'category', 'type', 'shortname'],
                'switchids' => [],
            ],
            'custom profile field categories' => [
                'singular' => 'custom profile field category',
                'datagenerator' => 'custom_profile_field_category',
                'required' => ['name'],
                'switchids' => [],
            ],
            'custom profile fields' => [
                'singular' => 'custom profile field',
                'datagenerator' => 'custom_profile_field',
                'required' => ['datatype', 'shortname', 'name'],
                'switchids' => [],
            ],
            'permission overrides' => [
                'singular' => 'permission override',
                'datagenerator' => 'permission_override',
                'required' => ['capability', 'permission', 'role', 'contextlevel', 'reference'],
                'switchids' => ['role' => 'roleid'],
            ],
            'system role assigns' => [
                'singular' => 'system role assignment',
                'datagenerator' => 'system_role_assign',
                'required' => ['user', 'role'],
                'switchids' => ['user' => 'userid', 'role' => 'roleid'],
            ],
            'role assigns' => [
                'singular' => 'role assignment',
                'datagenerator' => 'role_assign',
                'required' => ['user', 'role', 'contextlevel', 'reference'],
                'switchids' => ['user' => 'userid', 'role' => 'roleid'],
            ],
            'activities' => [
                'singular' => 'activity',
                'datagenerator' => 'activity',
                'required' => ['activity', 'course'],
                'switchids' => ['course' => 'course', 'gradecategory' => 'gradecat', 'grouping' => 'groupingid'],
            ],
            'blocks' => [
                'singular' => 'block',
                'datagenerator' => 'block_instance',
                'required' => ['blockname', 'contextlevel', 'reference'],
            ],
            'group members' => [
                'singular' => 'group member',
                'datagenerator' => 'group_member',
                'required' => ['user', 'group'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid'],
            ],
            'grouping groups' => [
                'singular' => 'grouping group',
                'datagenerator' => 'grouping_group',
                'required' => ['grouping', 'group'],
                'switchids' => ['grouping' => 'groupingid', 'group' => 'groupid'],
            ],
            'cohorts' => [
                'singular' => 'cohort',
                'datagenerator' => 'cohort',
                'required' => ['idnumber'],
            ],
            'cohort members' => [
                'singular' => 'cohort member',
                'datagenerator' => 'cohort_member',
                'required' => ['user', 'cohort'],
                'switchids' => ['user' => 'userid', 'cohort' => 'cohortid'],
            ],
            'roles' => [
                'singular' => 'role',
                'datagenerator' => 'role',
                'required' => ['shortname'],
            ],
            'role capabilities' => [
                'singular' => 'role capability',
                'datagenerator' => 'role_capability',
                'required' => ['role'],
                'switchids' => ['role' => 'roleid'],
            ],
            'grade categories' => [
                'singular' => 'grade category',
                'datagenerator' => 'grade_category',
                'required' => ['fullname', 'course'],
                'switchids' => ['course' => 'courseid', 'gradecategory' => 'parent'],
            ],
            'grade grades' => [
                'singular' => 'grade grade',
                'datagenerator' => 'grade_grade',
                'required' => ['gradeitem'],
                'switchids' => ['user' => 'userid', 'gradeitem' => 'itemid'],
            ],
            'grade items' => [
                'singular' => 'grade item',
                'datagenerator' => 'grade_item',
                'required' => ['course'],
                'switchids' => [
                    'scale' => 'scaleid',
                    'outcome' => 'outcomeid',
                    'course' => 'courseid',
                    'gradecategory' => 'categoryid',
                ],
            ],
            'grade outcomes' => [
                'singular' => 'grade outcome',
                'datagenerator' => 'grade_outcome',
                'required' => ['shortname', 'scale'],
                'switchids' => ['course' => 'courseid', 'gradecategory' => 'categoryid', 'scale' => 'scaleid'],
            ],
            'scales' => [
                'singular' => 'scale',
                'datagenerator' => 'scale',
                'required' => ['name', 'scale'],
                'switchids' => ['course' => 'courseid'],
            ],
            'question categories' => [
                'singular' => 'question category',
                'datagenerator' => 'question_category',
                'required' => ['name', 'contextlevel', 'reference'],
                'switchids' => ['questioncategory' => 'parent'],
            ],
            'questions' => [
                'singular' => 'question',
                'datagenerator' => 'question',
                'required' => ['qtype', 'questioncategory', 'name'],
                'switchids' => ['questioncategory' => 'category', 'user' => 'createdby'],
            ],
            'tags' => [
                'singular' => 'tag',
                'datagenerator' => 'tag',
                'required' => ['name'],
            ],
            'events' => [
                'singular' => 'event',
                'datagenerator' => 'event',
                'required' => ['name', 'eventtype'],
                'switchids' => [
                    'user' => 'userid',
                    'course' => 'courseid',
                    'category' => 'categoryid',
                ],
            ],
            'message contacts' => [
                'singular' => 'message contact',
                'datagenerator' => 'message_contacts',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'private messages' => [
                'singular' => 'private message',
                'datagenerator' => 'private_messages',
                'required' => ['user', 'contact', 'message'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'favourite conversations' => [
                'singular' => 'favourite conversation',
                'datagenerator' => 'favourite_conversations',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'group messages' => [
                'singular' => 'group message',
                'datagenerator' => 'group_messages',
                'required' => ['user', 'group', 'message'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid'],
            ],
            'muted group conversations' => [
                'singular' => 'muted group conversation',
                'datagenerator' => 'mute_group_conversations',
                'required' => ['user', 'group', 'course'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid', 'course' => 'courseid'],
            ],
            'muted private conversations' => [
                'singular' => 'muted private conversation',
                'datagenerator' => 'mute_private_conversations',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'language customisations' => [
                'singular' => 'language customisation',
                'datagenerator' => 'customlang',
                'required' => ['component', 'stringid', 'value'],
            ],
            'language packs' => [
                'singular' => 'language pack',
                'datagenerator' => 'langpack',
                'required' => ['language'],
            ],
            'analytics models' => [
                'singular' => 'analytics model',
                'datagenerator' => 'analytics_model',
                'required' => ['target', 'indicators', 'timesplitting', 'enabled'],
            ],
            'user preferences' => [
                'singular' => 'user preference',
                'datagenerator' => 'user_preferences',
                'required' => array('user', 'preference', 'value'),
                'switchids' => array('user' => 'userid'),
            ],
            'contentbank contents' => [
                'singular' => 'contentbank content',
                'datagenerator' => 'contentbank_content',
                'required' => array('contextlevel', 'reference', 'contenttype', 'user', 'contentname'),
                'switchids' => array('user' => 'userid')
            ],
            'user private files' => [
                'singular' => 'user private file',
                'datagenerator' => 'user_private_files',
                'required' => ['user', 'filepath'],
                'switchids' => ['user' => 'userid']
            ],
            'badge external backpacks' => [
                'singular' => 'badge external backpack',
                'datagenerator' => 'badge_external_backpack',
                'required' => ['backpackapiurl', 'backpackweburl', 'apiversion']
            ],
            'setup backpacks connected' => [
                'singular' => 'setup backpack connected',
                'datagenerator' => 'setup_backpack_connected',
                'required' => ['user', 'externalbackpack'],
                'switchids' => ['user' => 'userid', 'externalbackpack' => 'externalbackpackid']
            ],
            'last access times' => [
                'singular' => 'last access time',
                'datagenerator' => 'last_access_times',
                'required' => ['user', 'course', 'lastaccess'],
                'switchids' => ['user' => 'userid', 'course' => 'courseid'],
            ],
            'notifications' => [
                'singular' => 'notification',
                'datagenerator' => 'notification',
                'required' => ['subject', 'userfrom', 'userto'],
                'switchids' => ['userfrom' => 'userfromid', 'userto' => 'usertoid'],
            ],
            'stored progress bars' => [
                'singular' => 'stored progress bar',
                'datagenerator' => 'stored_progress_bar',
                'required' => ['idnumber'],
            ],
        ];

        return $entities;
    }

    /**
     * Get the grade item id using a name.
     *
     * @param string $name
     * @return int The grade item id
     */
    protected function get_gradeitem_id(string $name): int {
        global $DB;

        if (!$id = $DB->get_field('grade_items', 'id', ['itemname' => $name])) {
            throw new Exception('The specified grade item with name "' . $name . '" could not be found.');
        }

        return $id;
    }

    /**
     * Remove any empty custom fields, to avoid errors when creating the course.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_course($data) {
        foreach ($data as $fieldname => $value) {
            if ($value === '' && strpos($fieldname, 'customfield_') === 0) {
                unset($data[$fieldname]);
            }
        }
        return $data;
    }

    /**
     * If password is not set it uses the username.
     *
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
     * If contextlevel and reference are specified for cohort, transform them to the contextid.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_cohort($data) {
        if (isset($data['contextlevel'])) {
            if (!isset($data['reference'])) {
                throw new Exception('If field contextlevel is specified, field reference must also be present');
            }
            $context = $this->get_context($data['contextlevel'], $data['reference']);
            unset($data['contextlevel']);
            unset($data['reference']);
            $data['contextid'] = $context->id;
        }
        return $data;
    }

    /**
     * Preprocesses the creation of a grade item. Converts gradetype text to a number.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_grade_item($data) {
        global $CFG;
        require_once("$CFG->libdir/grade/constants.php");

        if (isset($data['gradetype'])) {
            $data['gradetype'] = constant("GRADE_TYPE_" . strtoupper($data['gradetype']));
        }

        if (!empty($data['category']) && !empty($data['courseid'])) {
            $cat = grade_category::fetch(array('fullname' => $data['category'], 'courseid' => $data['courseid']));
            if (!$cat) {
                throw new Exception('Could not resolve category with name "' . $data['category'] . '"');
            }
            unset($data['category']);
            $data['categoryid'] = $cat->id;
        }

        // We need to ensure that all these attributes coming from data are not-localised floats.
        $attrs = [
            'grademax',
            'grademin',
            'gradepass',
            'multfactor',
            'plusfactor',
            'aggregationcoef',
            'aggregationcoef2',
        ];
        foreach ($attrs as $attr) {
            if (array_key_exists($attr, $data)) {
                $data[$attr] = unformat_float($data[$attr]);
            }
        }

        return $data;
    }

    /**
     * Adapter to modules generator.
     *
     * @throws Exception Custom exception for test writers
     * @param array $data
     * @return void
     */
    protected function process_activity($data) {
        global $DB, $CFG;

        // The the_following_exists() method checks that the field exists.
        $activityname = $data['activity'];
        unset($data['activity']);

        // Convert scale name into scale id (negative number indicates using scale).
        if (isset($data['grade']) && strlen($data['grade']) && !is_number($data['grade'])) {
            $data['grade'] = - $this->get_scale_id($data['grade']);
            require_once("$CFG->libdir/grade/constants.php");

            if (!isset($data['gradetype'])) {
                $data['gradetype'] = GRADE_TYPE_SCALE;
            }
        }

        if (!array_key_exists('idnumber', $data)) {
            $data['idnumber'] = $data['name'];
            if (strlen($data['name']) > 100) {
                throw new Exception(
                    "Activity '{$activityname}' cannot be used as the default idnumber. " .
                    "The idnumber has a max length of 100 chars. " .
                    "Please manually specify an idnumber."
                );
            }
        }

        // We split $data in the activity $record and the course module $options.
        $cmoptions = array();
        $cmcolumns = $DB->get_columns('course_modules');
        foreach ($cmcolumns as $key => $value) {
            if (isset($data[$key])) {
                $cmoptions[$key] = $data[$key];
            }
        }

        $this->datagenerator->create_module($activityname, $data, $cmoptions);
    }

    /**
     * Add a block to a page.
     *
     * @param array $data should mostly match the fields of the block_instances table.
     *     The block type is specified by blockname.
     *     The parentcontextid is set from contextlevel and reference.
     *     Missing values are filled in by testing_block_generator::prepare_record.
     *     $data is passed to create_block as both $record and $options. Normally
     *     the keys are different, so this is a way to let people set values in either place.
     */
    protected function process_block_instance($data) {

        if (empty($data['blockname'])) {
            throw new Exception('\'blocks\' requires the field \'block\' type to be specified');
        }

        if (empty($data['contextlevel'])) {
            throw new Exception('\'blocks\' requires the field \'contextlevel\' to be specified');
        }

        if (!isset($data['reference'])) {
            throw new Exception('\'blocks\' requires the field \'reference\' to be specified');
        }

        $context = $this->get_context($data['contextlevel'], $data['reference']);
        $data['parentcontextid'] = $context->id;

        // Pass $data as both $record and $options. I think that is unlikely to
        // cause problems since the relevant key names are different.
        // $options is not used in most blocks I have seen, but where it is, it is necessary.
        $this->datagenerator->create_block($data['blockname'], $data, $data);
    }

    /**
     * Creates language customisation.
     *
     * @throws Exception
     * @throws dml_exception
     * @param array $data
     * @return void
     */
    protected function process_customlang($data) {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/customlang/locallib.php');
        require_once($CFG->libdir . '/adminlib.php');

        if (empty($data['component'])) {
            throw new Exception('\'customlang\' requires the field \'component\' type to be specified');
        }

        if (empty($data['stringid'])) {
            throw new Exception('\'customlang\' requires the field \'stringid\' to be specified');
        }

        if (!isset($data['value'])) {
            throw new Exception('\'customlang\' requires the field \'value\' to be specified');
        }

        $now = time();

        tool_customlang_utils::checkout($USER->lang);

        $record = $DB->get_record_sql("SELECT s.*
                                         FROM {tool_customlang} s
                                         JOIN {tool_customlang_components} c ON s.componentid = c.id
                                        WHERE c.name = ? AND s.lang = ? AND s.stringid = ?",
                array($data['component'], $USER->lang, $data['stringid']));

        if (empty($data['value']) && !is_null($record->local)) {
            $record->local = null;
            $record->modified = 1;
            $record->outdated = 0;
            $record->timecustomized = null;
            $DB->update_record('tool_customlang', $record);
            tool_customlang_utils::checkin($USER->lang);
        }

        if (!empty($data['value']) && $data['value'] != $record->local) {
            $record->local = $data['value'];
            $record->modified = 1;
            $record->outdated = 0;
            $record->timecustomized = $now;
            $DB->update_record('tool_customlang', $record);
            tool_customlang_utils::checkin($USER->lang);
        }
    }
    /**
     * Imports a langpack.
     *
     * @param array $data
     */
    protected function process_langpack($data) {
        $controller = new \tool_langimport\controller();
        $controller->install_languagepacks($data['language']);
        get_string_manager()->reset_caches();
    }

    /**
     * Adapter to enrol_user() data generator.
     *
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

        if (!isset($data['timestart'])) {
            $data['timestart'] = 0;
        }

        if (!isset($data['timeend'])) {
            $data['timeend'] = 0;
        }

        if (!isset($data['status'])) {
            $data['status'] = null;
        } else {
            $status = strtolower($data['status']);
            switch ($status) {
                case 'active':
                    $data['status'] = ENROL_USER_ACTIVE;
                    break;
                case 'suspended':
                    $data['status'] = ENROL_USER_SUSPENDED;
                    break;
            }
        }

        // If the provided course shortname is the site shortname we consider it a system role assign.
        if ($data['courseid'] == $SITE->id) {
            // Frontpage course assign.
            $context = context_course::instance($data['courseid']);
            role_assign($data['roleid'], $data['userid'], $context->id);

        } else {
            // Course assign.
            $this->datagenerator->enrol_user($data['userid'], $data['courseid'], $data['roleid'], $data['enrol'],
                    $data['timestart'], $data['timeend'], $data['status']);
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
     * Assign capabilities to a role.
     *
     * @param array $data
     */
    protected function process_role_capability($data): void {
        // We require the user to fill the role shortname.
        if (empty($data['roleid'])) {
            throw new Exception('\'role capability\' requires the field \'roleid\' to be specified');
        }

        $roleid = $data['roleid'];
        unset($data['roleid']);

        $this->datagenerator->create_role_capability($roleid, $data, \context_system::instance());
    }

    /**
     * Adds members to cohorts
     *
     * @param array $data
     * @return void
     */
    protected function process_cohort_member($data) {
        cohort_add_member($data['cohortid'], $data['userid']);
    }

    /**
     * Create a question category.
     *
     * @param array $data the row of data from the behat script.
     */
    protected function process_question_category($data) {
        global $DB;

        $context = $this->get_context($data['contextlevel'], $data['reference']);

        // The way this class works, we have already looked up the given parent category
        // name and found a matching category. However, it is possible, particularly
        // for the 'top' category, for there to be several categories with the
        // same name. So far one will have been picked at random, but we need
        // the one from the right context. So, if we have the wrong category, try again.
        // (Just fixing it here, rather than getting it right first time, is a bit
        // of a bodge, but in general this class assumes that names are unique,
        // and normally they are, so this was the easiest fix.)
        if (!empty($data['parent'])) {
            $foundparent = $DB->get_record('question_categories', ['id' => $data['parent']], '*', MUST_EXIST);
            if ($foundparent->contextid != $context->id) {
                $rightparentid = $DB->get_field('question_categories', 'id',
                        ['contextid' => $context->id, 'name' => $foundparent->name]);
                if (!$rightparentid) {
                    throw new Exception('The specified question category with name "' . $foundparent->name .
                            '" does not exist in context "' . $context->get_context_name() . '"."');
                }
                $data['parent'] = $rightparentid;
            }
        }

        $data['contextid'] = $context->id;
        /** @var core_question_generator $qgenerator */
        $qgenerator = $this->datagenerator->get_plugin_generator('core_question');
        $qgenerator->create_question_category($data);
    }

    /**
     * Create a question.
     *
     * Creating questions relies on the question/type/.../tests/helper.php mechanism.
     * We start with test_question_maker::get_question_form_data($data['qtype'], $data['template'])
     * and then overlay the values from any other fields of $data that are set.
     *
     * There is a special case that allows you to set qtype to 'missingtype'.
     * This creates an example of broken question, such as you might get if you
     * install a question type, create some questions of that type, and then
     * uninstall the question type (which is prevented through the UI but can
     * still happen). This special lets tests verify that these questions are
     * handled OK.
     *
     * @param array $data the row of data from the behat script.
     */
    protected function process_question($data) {
        global $DB;

        if (array_key_exists('questiontext', $data)) {
            $data['questiontext'] = array(
                    'text'   => $data['questiontext'],
                    'format' => FORMAT_HTML,
            );
        }

        if (array_key_exists('generalfeedback', $data)) {
            $data['generalfeedback'] = array(
                    'text'   => $data['generalfeedback'],
                    'format' => FORMAT_HTML,
            );
        }

        $which = null;
        if (!empty($data['template'])) {
            $which = $data['template'];
        }

        $missingtypespecialcase = false;
        if ($data['qtype'] === 'missingtype') {
            $data['qtype'] = 'essay'; // Actual type uses here does not matter. We just need any question.
            $missingtypespecialcase = true;
        }

        /** @var core_question_generator $qgenerator */
        $qgenerator = $this->datagenerator->get_plugin_generator('core_question');
        $questiondata = $qgenerator
            ->create_question($data['qtype'], $which, $data);

        if ($missingtypespecialcase) {
            $DB->set_field('question', 'qtype', 'unknownqtype', ['id' => $questiondata->id]);
        }
    }

    /**
     * Adds user to contacts
     *
     * @param array $data
     * @return void
     */
    protected function process_message_contacts($data) {
        \core_message\api::add_contact($data['userid'], $data['contactid']);
    }

    /**
     * Send a new message from user to contact in a private conversation
     *
     * @param array $data
     * @return void
     */
    protected function process_private_messages(array $data) {
        if (empty($data['format'])) {
            $data['format'] = 'FORMAT_PLAIN';
        }

        if (!$conversationid = \core_message\api::get_conversation_between_users([$data['userid'], $data['contactid']])) {
            $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                    [$data['userid'], $data['contactid']]
            );
            $conversationid = $conversation->id;
        }
        \core_message\api::send_message_to_conversation(
                $data['userid'],
                $conversationid,
                $data['message'],
                constant($data['format'])
        );
    }

    /**
     * Send a new message from user to a group conversation
     *
     * @param array $data
     * @return void
     */
    protected function process_group_messages(array $data) {
        global $DB;

        if (empty($data['format'])) {
            $data['format'] = 'FORMAT_PLAIN';
        }

        $group = $DB->get_record('groups', ['id' => $data['groupid']]);
        $coursecontext = context_course::instance($group->courseid);
        if (!$conversation = \core_message\api::get_conversation_by_area('core_group', 'groups', $data['groupid'],
                $coursecontext->id)) {
            $members = $DB->get_records_menu('groups_members', ['groupid' => $data['groupid']], '', 'userid, id');
            $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
                    array_keys($members),
                    $group->name,
                    \core_message\api::MESSAGE_CONVERSATION_ENABLED,
                    'core_group',
                    'groups',
                    $group->id,
                    $coursecontext->id);
        }
        \core_message\api::send_message_to_conversation(
                $data['userid'],
                $conversation->id,
                $data['message'],
                constant($data['format'])
        );
    }

    /**
     * Mark a private conversation as favourite for user
     *
     * @param array $data
     * @return void
     */
    protected function process_favourite_conversations(array $data) {
        if (!$conversationid = \core_message\api::get_conversation_between_users([$data['userid'], $data['contactid']])) {
            $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                    [$data['userid'], $data['contactid']]
            );
            $conversationid = $conversation->id;
        }
        \core_message\api::set_favourite_conversation($conversationid, $data['userid']);
    }

    /**
     * Mute an existing group conversation for user
     *
     * @param array $data
     * @return void
     */
    protected function process_mute_group_conversations(array $data) {
        if (groups_is_member($data['groupid'], $data['userid'])) {
            $context = context_course::instance($data['courseid']);
            $conversation = \core_message\api::get_conversation_by_area(
                    'core_group',
                    'groups',
                    $data['groupid'],
                    $context->id
            );
            if ($conversation) {
                \core_message\api::mute_conversation($data['userid'], $conversation->id);
            }
        }
    }

    /**
     * Mute a private conversation for user
     *
     * @param array $data
     * @return void
     */
    protected function process_mute_private_conversations(array $data) {
        if (!$conversationid = \core_message\api::get_conversation_between_users([$data['userid'], $data['contactid']])) {
            $conversation = \core_message\api::create_conversation(
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                    [$data['userid'], $data['contactid']]
            );
            $conversationid = $conversation->id;
        }
        \core_message\api::mute_conversation($data['userid'], $conversationid);
    }

    /**
     * Transform indicators string into array.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_analytics_model($data) {
        $data['indicators'] = explode(',', $data['indicators']);
        return $data;
    }

    /**
     * Creates an analytics model
     *
     * @param array $data target
     * @return void
     */
    protected function process_analytics_model($data) {
        \core_analytics\manager::create_declared_model($data);
    }

    /**
     * Set a preference value for user
     *
     * @param array $data
     * @return void
     */
    protected function process_user_preferences(array $data) {
        set_user_preference($data['preference'], $data['value'], $data['userid']);
    }

    /**
     * Create content in the given context's content bank.
     *
     * @param array $data
     * @return void
     */
    protected function process_contentbank_content(array $data) {
        global $CFG;

        if (empty($data['contextlevel'])) {
            throw new Exception('contentbank_content requires the field contextlevel to be specified');
        }

        if (!isset($data['reference'])) {
            throw new Exception('contentbank_content requires the field reference to be specified');
        }

        if (empty($data['contenttype'])) {
            throw new Exception('contentbank_content requires the field contenttype to be specified');
        }

        $contenttypeclass = "\\".$data['contenttype']."\\contenttype";
        if (class_exists($contenttypeclass)) {
            $context = $this->get_context($data['contextlevel'], $data['reference']);
            $contenttype = new $contenttypeclass($context);
            $record = new stdClass();
            $record->usercreated = $data['userid'];
            $record->name = $data['contentname'];
            if (isset($data['visibility'])) {
                $record->visibility = $data['visibility'];
            }
            $content = $contenttype->create_content($record);

            if (!empty($data['filepath'])) {
                $filename = basename($data['filepath']);
                $fs = get_file_storage();
                $filerecord = [
                    'component' => 'contentbank',
                    'filearea' => 'public',
                    'contextid' => $context->id,
                    'userid' => $data['userid'],
                    'itemid' => $content->get_id(),
                    'filename' => $filename,
                    'filepath' => '/',
                ];
                $fs->create_file_from_pathname($filerecord, $CFG->dirroot . $data['filepath']);
            }
        } else {
            throw new Exception('The specified "' . $data['contenttype'] . '" contenttype does not exist');
        }
    }

    /**
     * Create content in the given user's private files.
     *
     * @param array $data
     * @return void
     */
    protected function process_user_private_files(array $data) {
        global $CFG;

        $userid = $data['userid'];
        $fs = get_file_storage();
        $filepath = "{$CFG->dirroot}/{$data['filepath']}";

        if (!file_exists($filepath)) {
            throw new coding_exception("File '{$filepath}' does not exist");
        }
        $filerecord = [
            'userid' => $userid,
            'contextid' => context_user::instance($userid)->id,
            'component' => 'user',
            'filearea' => 'private',
            'itemid' => 0,
            'filepath'  => '/',
            'filename'  => basename($filepath),
        ];
        $fs->create_file_from_pathname($filerecord, $filepath);
    }

    /**
     * Create a exetrnal backpack.
     *
     * @param array $data
     */
    protected function process_badge_external_backpack(array $data) {
        global $DB;
        $DB->insert_record('badge_external_backpack', $data, true);
    }

    /**
     * Setup a backpack connected for user.
     *
     * @param array $data
     * @throws dml_exception
     */
    protected function process_setup_backpack_connected(array $data) {
        global $DB;

        if (empty($data['userid'])) {
            throw new Exception('\'setup backpack connected\' requires the field \'user\' to be specified');
        }
        if (empty($data['externalbackpackid'])) {
            throw new Exception('\'setup backpack connected\' requires the field \'externalbackpack\' to be specified');
        }
        // Dummy badge_backpack_oauth2 data.
        $timenow = time();
        $backpackoauth2 = new stdClass();
        $backpackoauth2->usermodified = $data['userid'];
        $backpackoauth2->timecreated = $timenow;
        $backpackoauth2->timemodified = $timenow;
        $backpackoauth2->userid = $data['userid'];
        $backpackoauth2->issuerid = 1;
        $backpackoauth2->externalbackpackid = $data['externalbackpackid'];
        $backpackoauth2->token = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $backpackoauth2->refreshtoken = '0123456789abcdefghijk';
        $backpackoauth2->expires = $timenow + 3600;
        $backpackoauth2->scope = 'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.create';
        $backpackoauth2->scope .= ' https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.readonly offline_access';
        $DB->insert_record('badge_backpack_oauth2', $backpackoauth2);

        // Dummy badge_backpack data.
        $backpack = new stdClass();
        $backpack->userid = $data['userid'];
        $backpack->email = 'student@behat.moodle';
        $backpack->backpackuid = 0;
        $backpack->autosync = 0;
        $backpack->password = '';
        $backpack->externalbackpackid = $data['externalbackpackid'];
        $DB->insert_record('badge_backpack', $backpack);
    }

    /**
     * Creates notifications to specific user.
     *
     * @param array $data
     * @return void
     */
    protected function process_notification(array $data) {
        global $DB;

        $notification = new stdClass();
        $notification->useridfrom = $data['userfromid'];
        $notification->useridto = $data['usertoid'];
        $notification->subject = $data['subject'];
        $notification->fullmessage = $data['subject'] . ' description';
        $notification->smallmessage = $data['subject'] . ' description';
        $notification->fullmessagehtml = $data['subject'] . ' description';

        if ($data['timecreated'] !== 'null') {
            $notification->timecreated = $data['timecreated'];
        }

        if ($data['timeread'] !== 'null') {
            $notification->timeread = $data['timeread'];
        }

        if (!empty($data)) {
            $popupnotification = new stdClass();
            $popupnotification->notificationid = $DB->insert_record('notifications', $notification);
            $DB->insert_record('message_popup_notifications', $popupnotification);
        }

    }

    /**
     * Creates user last access data within given courses.
     *
     * @param array $data
     * @return void
     */
    protected function process_last_access_times(array $data) {
        global $DB;

        if (!isset($data['userid'])) {
            throw new Exception('\'last acces times\' requires the field \'user\' to be specified');
        }

        if (!isset($data['courseid'])) {
            throw new Exception('\'last acces times\' requires the field \'course\' to be specified');
        }

        if (!isset($data['lastaccess'])) {
            throw new Exception('\'last acces times\' requires the field \'lastaccess\' to be specified');
        }

        $userdata = [];
        $userdata['old'] = $DB->get_record('user', ['id' => $data['userid']], 'firstaccess, lastaccess, lastlogin, currentlogin');
        $userdata['new'] = [
            'firstaccess' => $userdata['old']->firstaccess,
            'lastaccess' => $userdata['old']->lastaccess,
            'lastlogin' => $userdata['old']->lastlogin,
            'currentlogin' => $userdata['old']->currentlogin,
        ];

        // Check for lastaccess data for this course.
        $lastaccessdata = [
            'userid' => $data['userid'],
            'courseid' => $data['courseid'],
        ];

        $lastaccessid = $DB->get_field('user_lastaccess', 'id', $lastaccessdata);

        $dbdata = (object) $lastaccessdata;
        $dbdata->timeaccess = $data['lastaccess'];

        // Set the course last access time.
        if ($lastaccessid) {
            $dbdata->id = $lastaccessid;
            $DB->update_record('user_lastaccess', $dbdata);
        } else {
            $DB->insert_record('user_lastaccess', $dbdata);
        }

        // Store changes to other user access times as needed.

        // Update first access if this is the user's first login, or this access is earlier than their current first access.
        if (empty($userdata['new']['firstaccess']) ||
                $userdata['new']['firstaccess'] > $data['lastaccess']) {
            $userdata['new']['firstaccess'] = $data['lastaccess'];
        }

        // Update last access if it is the user's most recent access.
        if (empty($userdata['new']['lastaccess']) ||
                $userdata['new']['lastaccess'] < $data['lastaccess']) {
            $userdata['new']['lastaccess'] = $data['lastaccess'];
        }

        // Update last and current login if it is the user's most recent access.
        if (empty($userdata['new']['lastlogin']) ||
                $userdata['new']['lastlogin'] < $data['lastaccess']) {
            $userdata['new']['lastlogin'] = $data['lastaccess'];
            $userdata['new']['currentlogin'] = $data['lastaccess'];
        }

        $updatedata = [];

        if ($userdata['new']['firstaccess'] != $userdata['old']->firstaccess) {
            $updatedata['firstaccess'] = $userdata['new']['firstaccess'];
        }

        if ($userdata['new']['lastaccess'] != $userdata['old']->lastaccess) {
            $updatedata['lastaccess'] = $userdata['new']['lastaccess'];
        }

        if ($userdata['new']['lastlogin'] != $userdata['old']->lastlogin) {
            $updatedata['lastlogin'] = $userdata['new']['lastlogin'];
        }

        if ($userdata['new']['currentlogin'] != $userdata['old']->currentlogin) {
            $updatedata['currentlogin'] = $userdata['new']['currentlogin'];
        }

        // Only update user access data if there have been any changes.
        if (!empty($updatedata)) {
            $updatedata['id'] = $data['userid'];
            $updatedata = (object) $updatedata;
            $DB->update_record('user', $updatedata);
        }
    }
}
