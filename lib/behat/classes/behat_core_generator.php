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
        return [
            'users' => [
                'datagenerator' => 'user',
                'required' => ['username'],
            ],
            'categories' => [
                'datagenerator' => 'category',
                'required' => ['idnumber'],
                'switchids' => ['category' => 'parent'],
            ],
            'courses' => [
                'datagenerator' => 'course',
                'required' => ['shortname'],
                'switchids' => ['category' => 'category'],
            ],
            'groups' => [
                'datagenerator' => 'group',
                'required' => ['idnumber', 'course'],
                'switchids' => ['course' => 'courseid'],
            ],
            'groupings' => [
                'datagenerator' => 'grouping',
                'required' => ['idnumber', 'course'],
                'switchids' => ['course' => 'courseid'],
            ],
            'course enrolments' => [
                'datagenerator' => 'enrol_user',
                'required' => ['user', 'course', 'role'],
                'switchids' => ['user' => 'userid', 'course' => 'courseid', 'role' => 'roleid'],
            ],
            'custom field categories' => [
                'datagenerator' => 'custom_field_category',
                'required' => ['name', 'component', 'area', 'itemid'],
                'switchids' => [],
            ],
            'custom fields' => [
                'datagenerator' => 'custom_field',
                'required' => ['name', 'category', 'type', 'shortname'],
                'switchids' => [],
            ],
            'permission overrides' => [
                'datagenerator' => 'permission_override',
                'required' => ['capability', 'permission', 'role', 'contextlevel', 'reference'],
                'switchids' => ['role' => 'roleid'],
            ],
            'system role assigns' => [
                'datagenerator' => 'system_role_assign',
                'required' => ['user', 'role'],
                'switchids' => ['user' => 'userid', 'role' => 'roleid'],
            ],
            'role assigns' => [
                'datagenerator' => 'role_assign',
                'required' => ['user', 'role', 'contextlevel', 'reference'],
                'switchids' => ['user' => 'userid', 'role' => 'roleid'],
            ],
            'activities' => [
                'datagenerator' => 'activity',
                'required' => ['activity', 'idnumber', 'course'],
                'switchids' => ['course' => 'course', 'gradecategory' => 'gradecat', 'grouping' => 'groupingid'],
            ],
            'blocks' => [
                'datagenerator' => 'block_instance',
                'required' => ['blockname', 'contextlevel', 'reference'],
            ],
            'group members' => [
                'datagenerator' => 'group_member',
                'required' => ['user', 'group'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid'],
            ],
            'grouping groups' => [
                'datagenerator' => 'grouping_group',
                'required' => ['grouping', 'group'],
                'switchids' => ['grouping' => 'groupingid', 'group' => 'groupid'],
            ],
            'cohorts' => [
                'datagenerator' => 'cohort',
                'required' => ['idnumber'],
            ],
            'cohort members' => [
                'datagenerator' => 'cohort_member',
                'required' => ['user', 'cohort'],
                'switchids' => ['user' => 'userid', 'cohort' => 'cohortid'],
            ],
            'roles' => [
                'datagenerator' => 'role',
                'required' => ['shortname'],
            ],
            'grade categories' => [
                'datagenerator' => 'grade_category',
                'required' => ['fullname', 'course'],
                'switchids' => ['course' => 'courseid', 'gradecategory' => 'parent'],
            ],
            'grade items' => [
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
                'datagenerator' => 'grade_outcome',
                'required' => ['shortname', 'scale'],
                'switchids' => ['course' => 'courseid', 'gradecategory' => 'categoryid', 'scale' => 'scaleid'],
            ],
            'scales' => [
                'datagenerator' => 'scale',
                'required' => ['name', 'scale'],
                'switchids' => ['course' => 'courseid'],
            ],
            'question categories' => [
                'datagenerator' => 'question_category',
                'required' => ['name', 'contextlevel', 'reference'],
                'switchids' => ['questioncategory' => 'parent'],
            ],
            'questions' => [
                'datagenerator' => 'question',
                'required' => ['qtype', 'questioncategory', 'name'],
                'switchids' => ['questioncategory' => 'category', 'user' => 'createdby'],
            ],
            'tags' => [
                'datagenerator' => 'tag',
                'required' => ['name'],
            ],
            'events' => [
                'datagenerator' => 'event',
                'required' => ['name', 'eventtype'],
                'switchids' => [
                    'user' => 'userid',
                    'course' => 'courseid',
                    'category' => 'categoryid',
                ],
            ],
            'message contacts' => [
                'datagenerator' => 'message_contacts',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'private messages' => [
                'datagenerator' => 'private_messages',
                'required' => ['user', 'contact', 'message'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'favourite conversations' => [
                'datagenerator' => 'favourite_conversations',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'group messages' => [
                'datagenerator' => 'group_messages',
                'required' => ['user', 'group', 'message'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid'],
            ],
            'muted group conversations' => [
                'datagenerator' => 'mute_group_conversations',
                'required' => ['user', 'group', 'course'],
                'switchids' => ['user' => 'userid', 'group' => 'groupid', 'course' => 'courseid'],
            ],
            'muted private conversations' => [
                'datagenerator' => 'mute_private_conversations',
                'required' => ['user', 'contact'],
                'switchids' => ['user' => 'userid', 'contact' => 'contactid'],
            ],
            'language customisations' => [
                'datagenerator' => 'customlang',
                'required' => ['component', 'stringid', 'value'],
            ],
            'analytics model' => [
                'datagenerator' => 'analytics_model',
                'required' => ['target', 'indicators', 'timesplitting', 'enabled'],
            ],
            'user preferences' => [
                'datagenerator' => 'user_preferences',
                'required' => array('user', 'preference', 'value'),
                'switchids' => array('user' => 'userid')
            ],
        ];
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
        $this->datagenerator->get_plugin_generator('core_question')->create_question_category($data);
    }

    /**
     * Create a question.
     *
     * Creating questions relies on the question/type/.../tests/helper.php mechanism.
     * We start with test_question_maker::get_question_form_data($data['qtype'], $data['template'])
     * and then overlay the values from any other fields of $data that are set.
     *
     * @param array $data the row of data from the behat script.
     */
    protected function process_question($data) {
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

        $this->datagenerator->get_plugin_generator('core_question')->create_question($data['qtype'], $which, $data);
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
     * @param target $data
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
}
