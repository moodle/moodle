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
 * External message API
 *
 * @package    core_message
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/message/lib.php");

/**
 * Message external functions
 *
 * @package    core_message
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_message_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function send_instant_messages_parameters() {
        return new external_function_parameters(
            array(
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'touserid' => new external_value(PARAM_INT, 'id of the user to send the private message'),
                            'text' => new external_value(PARAM_RAW, 'the text of the message'),
                            'textformat' => new external_format_value('text', VALUE_DEFAULT, FORMAT_MOODLE),
                            'clientmsgid' => new external_value(PARAM_ALPHANUMEXT, 'your own client id for the message. If this id is provided, the fail message id will be returned to you', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Send private messages from the current USER to other users
     *
     * @param array $messages An array of message to send.
     * @return array
     * @since Moodle 2.2
     */
    public static function send_instant_messages($messages = array()) {
        global $CFG, $USER, $DB;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Ensure the current user is allowed to run this function
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:sendmessage', $context);

        $params = self::validate_parameters(self::send_instant_messages_parameters(), array('messages' => $messages));

        //retrieve all tousers of the messages
        $receivers = array();
        foreach($params['messages'] as $message) {
            $receivers[] = $message['touserid'];
        }
        list($sqluserids, $sqlparams) = $DB->get_in_or_equal($receivers, SQL_PARAMS_NAMED, 'userid_');
        $tousers = $DB->get_records_select("user", "id " . $sqluserids . " AND deleted = 0", $sqlparams);
        $blocklist   = array();
        $contactlist = array();
        $sqlparams['contactid'] = $USER->id;
        $rs = $DB->get_recordset_sql("SELECT *
                                        FROM {message_contacts}
                                       WHERE userid $sqluserids
                                             AND contactid = :contactid", $sqlparams);
        foreach ($rs as $record) {
            if ($record->blocked) {
                // $record->userid is blocking current user
                $blocklist[$record->userid] = true;
            } else {
                // $record->userid have current user as contact
                $contactlist[$record->userid] = true;
            }
        }
        $rs->close();

        $canreadallmessages = has_capability('moodle/site:readallmessages', $context);

        $resultmessages = array();
        foreach ($params['messages'] as $message) {
            $resultmsg = array(); //the infos about the success of the operation

            //we are going to do some checking
            //code should match /messages/index.php checks
            $success = true;

            //check the user exists
            if (empty($tousers[$message['touserid']])) {
                $success = false;
                $errormessage = get_string('touserdoesntexist', 'message', $message['touserid']);
            }

            //check that the touser is not blocking the current user
            if ($success and !empty($blocklist[$message['touserid']]) and !$canreadallmessages) {
                $success = false;
                $errormessage = get_string('userisblockingyou', 'message');
            }

            // Check if the user is a contact
            //TODO MDL-31118 performance improvement - edit the function so we can pass an array instead userid
            $blocknoncontacts = get_user_preferences('message_blocknoncontacts', NULL, $message['touserid']);
            // message_blocknoncontacts option is on and current user is not in contact list
            if ($success && empty($contactlist[$message['touserid']]) && !empty($blocknoncontacts)) {
                // The user isn't a contact and they have selected to block non contacts so this message won't be sent.
                $success = false;
                $errormessage = get_string('userisblockingyounoncontact', 'message',
                        fullname(core_user::get_user($message['touserid'])));
            }

            //now we can send the message (at least try)
            if ($success) {
                //TODO MDL-31118 performance improvement - edit the function so we can pass an array instead one touser object
                $success = message_post_message($USER, $tousers[$message['touserid']],
                        $message['text'], external_validate_format($message['textformat']));
            }

            //build the resultmsg
            if (isset($message['clientmsgid'])) {
                $resultmsg['clientmsgid'] = $message['clientmsgid'];
            }
            if ($success) {
                $resultmsg['msgid'] = $success;
            } else {
                // WARNINGS: for backward compatibility we return this errormessage.
                //          We should have thrown exceptions as these errors prevent results to be returned.
                // See http://docs.moodle.org/dev/Errors_handling_in_web_services#When_to_send_a_warning_on_the_server_side .
                $resultmsg['msgid'] = -1;
                $resultmsg['errormessage'] = $errormessage;
            }

            $resultmessages[] = $resultmsg;
        }

        return $resultmessages;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function send_instant_messages_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'msgid' => new external_value(PARAM_INT, 'test this to know if it succeeds:  id of the created message if it succeeded, -1 when failed'),
                    'clientmsgid' => new external_value(PARAM_ALPHANUMEXT, 'your own id for the message', VALUE_OPTIONAL),
                    'errormessage' => new external_value(PARAM_TEXT, 'error message - if it failed', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Create contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function create_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                ),
                'userid' => new external_value(PARAM_INT, 'The id of the user we are creating the contacts for, 0 for the
                    current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Create contacts.
     *
     * @param array $userids array of user IDs.
     * @param int $userid The id of the user we are creating the contacts for
     * @return external_description
     * @since Moodle 2.5
     */
    public static function create_contacts($userids, $userid = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $userid) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::create_contacts_parameters(), $params);

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_add_contact($id, 0, $userid)) {
                $warnings[] = array(
                    'item' => 'user',
                    'itemid' => $id,
                    'warningcode' => 'contactnotcreated',
                    'message' => 'The contact could not be created'
                );
            }
        }
        return $warnings;
    }

    /**
     * Create contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function create_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Delete contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                ),
                'userid' => new external_value(PARAM_INT, 'The id of the user we are deleting the contacts for, 0 for the
                    current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Delete contacts.
     *
     * @param array $userids array of user IDs.
     * @param int $userid The id of the user we are deleting the contacts for
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_contacts($userids, $userid = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $userid) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::delete_contacts_parameters(), $params);

        foreach ($params['userids'] as $id) {
            message_remove_contact($id, $userid);
        }

        return null;
    }

    /**
     * Delete contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function delete_contacts_returns() {
        return null;
    }

    /**
     * Block contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function block_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                ),
                'userid' => new external_value(PARAM_INT, 'The id of the user we are blocking the contacts for, 0 for the
                    current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Block contacts.
     *
     * @param array $userids array of user IDs.
     * @param int $userid The id of the user we are blocking the contacts for
     * @return external_description
     * @since Moodle 2.5
     */
    public static function block_contacts($userids, $userid = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $userid) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::block_contacts_parameters(), $params);

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_block_contact($id, $userid)) {
                $warnings[] = array(
                    'item' => 'user',
                    'itemid' => $id,
                    'warningcode' => 'contactnotblocked',
                    'message' => 'The contact could not be blocked'
                );
            }
        }
        return $warnings;
    }

    /**
     * Block contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function block_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Unblock contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function unblock_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                ),
                'userid' => new external_value(PARAM_INT, 'The id of the user we are unblocking the contacts for, 0 for the
                    current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Unblock contacts.
     *
     * @param array $userids array of user IDs.
     * @param int $userid The id of the user we are unblocking the contacts for
     * @return null
     * @since Moodle 2.5
     */
    public static function unblock_contacts($userids, $userid = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $userid) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::unblock_contacts_parameters(), $params);

        foreach ($params['userids'] as $id) {
            message_unblock_contact($id, $userid);
        }

        return null;
    }

    /**
     * Unblock contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function unblock_contacts_returns() {
        return null;
    }

    /**
     * Return the structure of a message area contact.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    private static function get_messagearea_contact_structure() {
        return new external_single_structure(
            array(
                'userid' => new external_value(PARAM_INT, 'The user\'s id'),
                'fullname' => new external_value(PARAM_NOTAGS, 'The user\'s name'),
                'profileimageurl' => new external_value(PARAM_URL, 'User picture URL'),
                'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL'),
                'ismessaging' => new external_value(PARAM_BOOL, 'If we are messaging the user'),
                'sentfromcurrentuser' => new external_value(PARAM_BOOL, 'Was the last message sent from the current user?'),
                'lastmessage' => new external_value(PARAM_NOTAGS, 'The user\'s last message'),
                'messageid' => new external_value(PARAM_INT, 'The unique search message id', VALUE_DEFAULT, null),
                'showonlinestatus' => new external_value(PARAM_BOOL, 'Show the user\'s online status?'),
                'isonline' => new external_value(PARAM_BOOL, 'The user\'s online status'),
                'isread' => new external_value(PARAM_BOOL, 'If the user has read the message'),
                'isblocked' => new external_value(PARAM_BOOL, 'If the user has been blocked'),
                'unreadcount' => new external_value(PARAM_INT, 'The number of unread messages in this conversation',
                    VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the structure of a message area message.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    private static function get_messagearea_message_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The id of the message'),
                'useridfrom' => new external_value(PARAM_INT, 'The id of the user who sent the message'),
                'useridto' => new external_value(PARAM_INT, 'The id of the user who received the message'),
                'text' => new external_value(PARAM_RAW, 'The text of the message'),
                'displayblocktime' => new external_value(PARAM_BOOL, 'Should we display the block time?'),
                'blocktime' => new external_value(PARAM_NOTAGS, 'The time to display above the message'),
                'position' => new external_value(PARAM_ALPHA, 'The position of the text'),
                'timesent' => new external_value(PARAM_NOTAGS, 'The time the message was sent'),
                'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the message'),
                'isread' => new external_value(PARAM_INT, 'Determines if the message was read or not'),
            )
        );
    }

    /**
     * Get messagearea search users in course parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_search_users_in_course_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who is performing the search'),
                'courseid' => new external_value(PARAM_INT, 'The id of the course'),
                'search' => new external_value(PARAM_RAW, 'The string being searched'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get messagearea search users in course results.
     *
     * @param int $userid The id of the user who is performing the search
     * @param int $courseid The id of the course
     * @param string $search The string being searched
     * @param int $limitfrom
     * @param int $limitnum
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_search_users_in_course($userid, $courseid, $search, $limitfrom = 0,
                                                                       $limitnum = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'courseid' => $courseid,
            'search' => $search,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        );
        self::validate_parameters(self::data_for_messagearea_search_users_in_course_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $userid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $users = \core_message\api::search_users_in_course($userid, $courseid, $search, $limitfrom, $limitnum);
        $results = new \core_message\output\messagearea\user_search_results($users);

        $renderer = $PAGE->get_renderer('core_message');
        return $results->export_for_template($renderer);
    }

    /**
     * Get messagearea search users in course returns.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_search_users_in_course_returns() {
        return new external_single_structure(
            array(
                'contacts' => new external_multiple_structure(
                    self::get_messagearea_contact_structure()
                ),
            )
        );
    }

    /**
     * Get messagearea search users parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_search_users_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who is performing the search'),
                'search' => new external_value(PARAM_RAW, 'The string being searched'),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get messagearea search users results.
     *
     * @param int $userid The id of the user who is performing the search
     * @param string $search The string being searched
     * @param int $limitnum
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_search_users($userid, $search, $limitnum = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'search' => $search,
            'limitnum' => $limitnum
        );
        self::validate_parameters(self::data_for_messagearea_search_users_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $userid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        list($contacts, $courses, $noncontacts) = \core_message\api::search_users($userid, $search, $limitnum);
        $search = new \core_message\output\messagearea\user_search_results($contacts, $courses, $noncontacts);

        $renderer = $PAGE->get_renderer('core_message');
        return $search->export_for_template($renderer);
    }

    /**
     * Get messagearea search users returns.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_search_users_returns() {
        return new external_single_structure(
            array(
                'contacts' => new external_multiple_structure(
                    self::get_messagearea_contact_structure()
                ),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The course id'),
                            'shortname' => new external_value(PARAM_TEXT, 'The course shortname'),
                            'fullname' => new external_value(PARAM_TEXT, 'The course fullname'),
                        )
                    )
                ),
                'noncontacts' => new external_multiple_structure(
                    self::get_messagearea_contact_structure()
                )
            )
        );
    }

    /**
     * Get messagearea search messages parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_search_messages_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who is performing the search'),
                'search' => new external_value(PARAM_RAW, 'The string being searched'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get messagearea search messages results.
     *
     * @param int $userid The id of the user who is performing the search
     * @param string $search The string being searched
     * @param int $limitfrom
     * @param int $limitnum
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_search_messages($userid, $search, $limitfrom = 0, $limitnum = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'search' => $search,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum

        );
        self::validate_parameters(self::data_for_messagearea_search_messages_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $userid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $messages = \core_message\api::search_messages($userid, $search, $limitfrom, $limitnum);
        $results = new \core_message\output\messagearea\message_search_results($messages);

        $renderer = $PAGE->get_renderer('core_message');
        return $results->export_for_template($renderer);
    }

    /**
     * Get messagearea search messages returns.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_search_messages_returns() {
        return new external_single_structure(
            array(
                'contacts' => new external_multiple_structure(
                    self::get_messagearea_contact_structure()
                )
            )
        );
    }

    /**
     * The messagearea conversations parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_conversations_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we are viewing conversations for'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get messagearea conversations.
     *
     * @param int $userid The id of the user who we are viewing conversations for
     * @param int $limitfrom
     * @param int $limitnum
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_conversations($userid, $limitfrom = 0, $limitnum = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        );
        self::validate_parameters(self::data_for_messagearea_conversations_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $userid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $conversations = \core_message\api::get_conversations($userid, $limitfrom, $limitnum);
        $conversations = new \core_message\output\messagearea\contacts(null, $conversations);

        $renderer = $PAGE->get_renderer('core_message');
        return $conversations->export_for_template($renderer);
    }

    /**
     * The messagearea conversations return structure.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_conversations_returns() {
        return new external_single_structure(
            array(
                'contacts' => new external_multiple_structure(
                    self::get_messagearea_contact_structure()
                )
            )
        );
    }

    /**
     * The messagearea contacts return parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_contacts_parameters() {
        return self::data_for_messagearea_conversations_parameters();
    }

    /**
     * Get messagearea contacts parameters.
     *
     * @param int $userid The id of the user who we are viewing conversations for
     * @param int $limitfrom
     * @param int $limitnum
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_contacts($userid, $limitfrom = 0, $limitnum = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        );
        self::validate_parameters(self::data_for_messagearea_contacts_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $userid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $contacts = \core_message\api::get_contacts($userid, $limitfrom, $limitnum);
        $contacts = new \core_message\output\messagearea\contacts(null, $contacts);

        $renderer = $PAGE->get_renderer('core_message');
        return $contacts->export_for_template($renderer);
    }

    /**
     * The messagearea contacts return structure.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_contacts_returns() {
        return self::data_for_messagearea_conversations_returns();
    }

    /**
     * The messagearea messages parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_messages_parameters() {
        return new external_function_parameters(
            array(
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'otheruserid' => new external_value(PARAM_INT, 'The other user\'s id'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0),
                'newest' => new external_value(PARAM_BOOL, 'Newest first?', VALUE_DEFAULT, false),
                'timefrom' => new external_value(PARAM_INT,
                    'The timestamp from which the messages were created', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get messagearea messages.
     *
     * @param int $currentuserid The current user's id
     * @param int $otheruserid The other user's id
     * @param int $limitfrom
     * @param int $limitnum
     * @param boolean $newest
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_messages($currentuserid, $otheruserid, $limitfrom = 0, $limitnum = 0,
                                                         $newest = false, $timefrom = 0) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'currentuserid' => $currentuserid,
            'otheruserid' => $otheruserid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
            'newest' => $newest,
            'timefrom' => $timefrom,
        );
        self::validate_parameters(self::data_for_messagearea_messages_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $currentuserid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        if ($newest) {
            $sort = 'timecreated DESC';
        } else {
            $sort = 'timecreated ASC';
        }

        // We need to enforce a one second delay on messages to avoid race conditions of current
        // messages still being sent.
        //
        // There is a chance that we could request messages before the current time's
        // second has elapsed and while other messages are being sent in that same second. In which
        // case those messages will be lost.
        //
        // Instead we ignore the current time in the result set to ensure that second is allowed to finish.
        if (!empty($timefrom)) {
            $timeto = time() - 1;
        } else {
            $timeto = 0;
        }

        // No requesting messages from the current time, as stated above.
        if ($timefrom == time()) {
            $messages = [];
        } else {
            $messages = \core_message\api::get_messages($currentuserid, $otheruserid, $limitfrom,
                                                        $limitnum, $sort, $timefrom, $timeto);
        }

        $messages = new \core_message\output\messagearea\messages($currentuserid, $otheruserid, $messages);

        $renderer = $PAGE->get_renderer('core_message');
        return $messages->export_for_template($renderer);
    }

    /**
     * The messagearea messages return structure.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_messages_returns() {
        return new external_single_structure(
            array(
                'iscurrentuser' => new external_value(PARAM_BOOL, 'Is the currently logged in user the user we are viewing
                    the messages on behalf of?'),
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'otheruserid' => new external_value(PARAM_INT, 'The other user\'s id'),
                'otheruserfullname' => new external_value(PARAM_NOTAGS, 'The other user\'s fullname'),
                'showonlinestatus' => new external_value(PARAM_BOOL, 'Show the user\'s online status?'),
                'isonline' => new external_value(PARAM_BOOL, 'The user\'s online status'),
                'messages' => new external_multiple_structure(
                    self::get_messagearea_message_structure()
                ),
                'isblocked' => new external_value(PARAM_BOOL, 'Is this user blocked by the current user?', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * The get most recent message return parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_get_most_recent_message_parameters() {
        return new external_function_parameters(
            array(
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'otheruserid' => new external_value(PARAM_INT, 'The other user\'s id'),
            )
        );
    }

    /**
     * Get the most recent message in a conversation.
     *
     * @param int $currentuserid The current user's id
     * @param int $otheruserid The other user's id
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_get_most_recent_message($currentuserid, $otheruserid) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'currentuserid' => $currentuserid,
            'otheruserid' => $otheruserid
        );
        self::validate_parameters(self::data_for_messagearea_get_most_recent_message_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $currentuserid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $message = \core_message\api::get_most_recent_message($currentuserid, $otheruserid);
        $message = new \core_message\output\messagearea\message($message);

        $renderer = $PAGE->get_renderer('core_message');
        return $message->export_for_template($renderer);
    }

    /**
     * The get most recent message return structure.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_get_most_recent_message_returns() {
        return self::get_messagearea_message_structure();
    }

    /**
     * The get profile parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_get_profile_parameters() {
        return new external_function_parameters(
            array(
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'otheruserid' => new external_value(PARAM_INT, 'The id of the user whose profile we want to view'),
            )
        );
    }

    /**
     * Get the profile information for a contact.
     *
     * @param int $currentuserid The current user's id
     * @param int $otheruserid The id of the user whose profile we are viewing
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_get_profile($currentuserid, $otheruserid) {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'currentuserid' => $currentuserid,
            'otheruserid' => $otheruserid
        );
        self::validate_parameters(self::data_for_messagearea_get_profile_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $currentuserid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $profile = \core_message\api::get_profile($currentuserid, $otheruserid);
        $profile = new \core_message\output\messagearea\profile($profile);

        $renderer = $PAGE->get_renderer('core_message');
        return $profile->export_for_template($renderer);
    }

    /**
     * The get profile return structure.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_get_profile_returns() {
        return new external_single_structure(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user whose profile we are viewing'),
                'email' => new external_value(core_user::get_property_type('email'), 'An email address'),
                'country' => new external_value(PARAM_TEXT, 'Home country of the user'),
                'city' => new external_value(core_user::get_property_type('city'), 'Home city of the user'),
                'fullname' => new external_value(PARAM_NOTAGS, 'The user\'s name'),
                'profileimageurl' => new external_value(PARAM_URL, 'User picture URL'),
                'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL'),
                'showonlinestatus' => new external_value(PARAM_BOOL, 'Show the user\'s online status?'),
                'isonline' => new external_value(PARAM_BOOL, 'The user\'s online status'),
                'isblocked' => new external_value(PARAM_BOOL, 'Is the user blocked?'),
                'iscontact' => new external_value(PARAM_BOOL, 'Is the user a contact?')
            )
        );
    }

    /**
     * Get contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_contacts_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get contacts.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_contacts() {
        global $CFG, $PAGE;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        require_once($CFG->dirroot . '/user/lib.php');

        list($online, $offline, $strangers) = message_get_contacts();
        $allcontacts = array('online' => $online, 'offline' => $offline, 'strangers' => $strangers);
        foreach ($allcontacts as $mode => $contacts) {
            foreach ($contacts as $key => $contact) {
                $newcontact = array(
                    'id' => $contact->id,
                    'fullname' => fullname($contact),
                    'unread' => $contact->messagecount
                );

                $userpicture = new user_picture($contact);
                $userpicture->size = 1; // Size f1.
                $newcontact['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
                $userpicture->size = 0; // Size f2.
                $newcontact['profileimageurlsmall'] = $userpicture->get_url($PAGE)->out(false);

                $allcontacts[$mode][$key] = $newcontact;
            }
        }
        return $allcontacts;
    }

    /**
     * Get contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_contacts_returns() {
        return new external_single_structure(
            array(
                'online' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'User ID'),
                            'fullname' => new external_value(PARAM_NOTAGS, 'User full name'),
                            'profileimageurl' => new external_value(PARAM_URL, 'User picture URL', VALUE_OPTIONAL),
                            'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL', VALUE_OPTIONAL),
                            'unread' => new external_value(PARAM_INT, 'Unread message count')
                        )
                    ),
                    'List of online contacts'
                ),
                'offline' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'User ID'),
                            'fullname' => new external_value(PARAM_NOTAGS, 'User full name'),
                            'profileimageurl' => new external_value(PARAM_URL, 'User picture URL', VALUE_OPTIONAL),
                            'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL', VALUE_OPTIONAL),
                            'unread' => new external_value(PARAM_INT, 'Unread message count')
                        )
                    ),
                    'List of offline contacts'
                ),
                'strangers' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'User ID'),
                            'fullname' => new external_value(PARAM_NOTAGS, 'User full name'),
                            'profileimageurl' => new external_value(PARAM_URL, 'User picture URL', VALUE_OPTIONAL),
                            'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL', VALUE_OPTIONAL),
                            'unread' => new external_value(PARAM_INT, 'Unread message count')
                        )
                    ),
                    'List of users that are not in the user\'s contact list but have sent a message'
                )
            )
        );
    }

    /**
     * Search contacts parameters description.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function search_contacts_parameters() {
        return new external_function_parameters(
            array(
                'searchtext' => new external_value(PARAM_CLEAN, 'String the user\'s fullname has to match to be found'),
                'onlymycourses' => new external_value(PARAM_BOOL, 'Limit search to the user\'s courses',
                    VALUE_DEFAULT, false)
            )
        );
    }

    /**
     * Search contacts.
     *
     * @param string $searchtext query string.
     * @param bool $onlymycourses limit the search to the user's courses only.
     * @return external_description
     * @since Moodle 2.5
     */
    public static function search_contacts($searchtext, $onlymycourses = false) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        require_once($CFG->libdir . '/enrollib.php');

        $params = array('searchtext' => $searchtext, 'onlymycourses' => $onlymycourses);
        $params = self::validate_parameters(self::search_contacts_parameters(), $params);

        // Extra validation, we do not allow empty queries.
        if ($params['searchtext'] === '') {
            throw new moodle_exception('querystringcannotbeempty');
        }

        $courseids = array();
        if ($params['onlymycourses']) {
            $mycourses = enrol_get_my_courses(array('id'));
            foreach ($mycourses as $mycourse) {
                $courseids[] = $mycourse->id;
            }
        } else {
            $courseids[] = SITEID;
        }

        // Retrieving the users matching the query.
        $users = message_search_users($courseids, $params['searchtext']);
        $results = array();
        foreach ($users as $user) {
            $results[$user->id] = $user;
        }

        // Reorganising information.
        foreach ($results as &$user) {
            $newuser = array(
                'id' => $user->id,
                'fullname' => fullname($user)
            );

            // Avoid undefined property notice as phone not specified.
            $user->phone1 = null;
            $user->phone2 = null;

            $userpicture = new user_picture($user);
            $userpicture->size = 1; // Size f1.
            $newuser['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
            $userpicture->size = 0; // Size f2.
            $newuser['profileimageurlsmall'] = $userpicture->get_url($PAGE)->out(false);

            $user = $newuser;
        }

        return $results;
    }

    /**
     * Search contacts return description.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function search_contacts_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'User ID'),
                    'fullname' => new external_value(PARAM_NOTAGS, 'User full name'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User picture URL', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL', VALUE_OPTIONAL)
                )
            ),
            'List of contacts'
        );
    }

    /**
     * Get messages parameters description.
     *
     * @return external_function_parameters
     * @since 2.8
     */
    public static function get_messages_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for any user', VALUE_REQUIRED),
                'useridfrom' => new external_value(
                    PARAM_INT, 'the user id who send the message, 0 for any user. -10 or -20 for no-reply or support user',
                    VALUE_DEFAULT, 0),
                'type' => new external_value(
                    PARAM_ALPHA, 'type of message to return, expected values are: notifications, conversations and both',
                    VALUE_DEFAULT, 'both'),
                'read' => new external_value(PARAM_BOOL, 'true for getting read messages, false for unread', VALUE_DEFAULT, true),
                'newestfirst' => new external_value(
                    PARAM_BOOL, 'true for ordering by newest first, false for oldest first',
                    VALUE_DEFAULT, true),
                'limitfrom' => new external_value(PARAM_INT, 'limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get messages function implementation.
     *
     * @since  2.8
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @param  int      $useridfrom     the user id who send the message. -10 or -20 for no-reply or support user
     * @param  string   $type           type of message to return, expected values: notifications, conversations and both
     * @param  bool     $read           true for retreiving read messages, false for unread
     * @param  bool     $newestfirst    true for ordering by newest first, false for oldest first
     * @param  int      $limitfrom      limit from
     * @param  int      $limitnum       limit num
     * @return external_description
     */
    public static function get_messages($useridto, $useridfrom = 0, $type = 'both', $read = true,
                                        $newestfirst = true, $limitfrom = 0, $limitnum = 0) {
        global $CFG, $USER;

        $warnings = array();

        $params = array(
            'useridto' => $useridto,
            'useridfrom' => $useridfrom,
            'type' => $type,
            'read' => $read,
            'newestfirst' => $newestfirst,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        );

        $params = self::validate_parameters(self::get_messages_parameters(), $params);

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];
        $useridfrom = $params['useridfrom'];
        $type = $params['type'];
        $read = $params['read'];
        $newestfirst = $params['newestfirst'];
        $limitfrom = $params['limitfrom'];
        $limitnum = $params['limitnum'];

        $allowedvalues = array('notifications', 'conversations', 'both');
        if (!in_array($type, $allowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for type parameter (value: ' . $type . '),' .
                'allowed values are: ' . implode(',', $allowedvalues));
        }

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            // If we are retreiving only conversations, and messaging is disabled, throw an exception.
            if ($type == "conversations") {
                throw new moodle_exception('disabled', 'message');
            }
            if ($type == "both") {
                $warning = array();
                $warning['item'] = 'message';
                $warning['itemid'] = $USER->id;
                $warning['warningcode'] = '1';
                $warning['message'] = 'Private messages (conversations) are not enabled in this site.
                    Only notifications will be returned';
                $warnings[] = $warning;
            }
        }

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        }

        if (!empty($useridfrom)) {
            // We use get_user here because the from user can be the noreply or support user.
            $userfrom = core_user::get_user($useridfrom, '*', MUST_EXIST);
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and $useridfrom != $USER->id and
             !has_capability('moodle/site:readallmessages', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        // Which type of messages to retrieve.
        $notifications = -1;
        if ($type != 'both') {
            $notifications = ($type == 'notifications') ? 1 : 0;
        }

        $orderdirection = $newestfirst ? 'DESC' : 'ASC';
        $sort = "mr.timecreated $orderdirection";

        if ($messages = message_get_messages($useridto, $useridfrom, $notifications, $read, $sort, $limitfrom, $limitnum)) {
            $canviewfullname = has_capability('moodle/site:viewfullnames', $context);

            // In some cases, we don't need to get the to/from user objects from the sql query.
            $userfromfullname = '';
            $usertofullname = '';

            // In this case, the useridto field is not empty, so we can get the user destinatary fullname from there.
            if (!empty($useridto)) {
                $usertofullname = fullname($userto, $canviewfullname);
                // The user from may or may not be filled.
                if (!empty($useridfrom)) {
                    $userfromfullname = fullname($userfrom, $canviewfullname);
                }
            } else {
                // If the useridto field is empty, the useridfrom must be filled.
                $userfromfullname = fullname($userfrom, $canviewfullname);
            }
            foreach ($messages as $mid => $message) {

                // Do not return deleted messages.
                if (($useridto == $USER->id and $message->timeusertodeleted) or
                        ($useridfrom == $USER->id and $message->timeuserfromdeleted)) {

                    unset($messages[$mid]);
                    continue;
                }

                // We need to get the user from the query.
                if (empty($userfromfullname)) {
                    // Check for non-reply and support users.
                    if (core_user::is_real_user($message->useridfrom)) {
                        $user = new stdClass();
                        $user = username_load_fields_from_object($user, $message, 'userfrom');
                        $message->userfromfullname = fullname($user, $canviewfullname);
                    } else {
                        $user = core_user::get_user($message->useridfrom);
                        $message->userfromfullname = fullname($user, $canviewfullname);
                    }
                } else {
                    $message->userfromfullname = $userfromfullname;
                }

                // We need to get the user from the query.
                if (empty($usertofullname)) {
                    $user = new stdClass();
                    $user = username_load_fields_from_object($user, $message, 'userto');
                    $message->usertofullname = fullname($user, $canviewfullname);
                } else {
                    $message->usertofullname = $usertofullname;
                }

                // This field is only available in the message_read table.
                if (!isset($message->timeread)) {
                    $message->timeread = 0;
                }

                $message->text = message_format_message_text($message);
                $messages[$mid] = (array) $message;
            }
        }

        $results = array(
            'messages' => $messages,
            'warnings' => $warnings
        );

        return $results;
    }

    /**
     * Get messages return description.
     *
     * @return external_single_structure
     * @since 2.8
     */
    public static function get_messages_returns() {
        return new external_single_structure(
            array(
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Message id'),
                            'useridfrom' => new external_value(PARAM_INT, 'User from id'),
                            'useridto' => new external_value(PARAM_INT, 'User to id'),
                            'subject' => new external_value(PARAM_TEXT, 'The message subject'),
                            'text' => new external_value(PARAM_RAW, 'The message text formated'),
                            'fullmessage' => new external_value(PARAM_RAW, 'The message'),
                            'fullmessageformat' => new external_format_value('fullmessage'),
                            'fullmessagehtml' => new external_value(PARAM_RAW, 'The message in html'),
                            'smallmessage' => new external_value(PARAM_RAW, 'The shorten message'),
                            'notification' => new external_value(PARAM_INT, 'Is a notification?'),
                            'contexturl' => new external_value(PARAM_RAW, 'Context URL'),
                            'contexturlname' => new external_value(PARAM_TEXT, 'Context URL link name'),
                            'timecreated' => new external_value(PARAM_INT, 'Time created'),
                            'timeread' => new external_value(PARAM_INT, 'Time read'),
                            'usertofullname' => new external_value(PARAM_TEXT, 'User to full name'),
                            'userfromfullname' => new external_value(PARAM_TEXT, 'User from full name')
                        ), 'message'
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Mark all notifications as read parameters description.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function mark_all_notifications_as_read_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for any user', VALUE_REQUIRED),
                'useridfrom' => new external_value(
                    PARAM_INT, 'the user id who send the message, 0 for any user. -10 or -20 for no-reply or support user',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Mark all notifications as read function.
     *
     * @since  3.2
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @param  int      $useridfrom     the user id who send the message. -10 or -20 for no-reply or support user
     * @return external_description
     */
    public static function mark_all_notifications_as_read($useridto, $useridfrom) {
        global $USER;

        $params = self::validate_parameters(
            self::mark_all_notifications_as_read_parameters(),
            array(
                'useridto' => $useridto,
                'useridfrom' => $useridfrom,
            )
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];
        $useridfrom = $params['useridfrom'];

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        }

        if (!empty($useridfrom)) {
            // We use get_user here because the from user can be the noreply or support user.
            $userfrom = core_user::get_user($useridfrom, '*', MUST_EXIST);
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and $useridfrom != $USER->id and
            // The deleteanymessage cap seems more reasonable here than readallmessages.
             !has_capability('moodle/site:deleteanymessage', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        \core_message\api::mark_all_read_for_user($useridto, $useridfrom, MESSAGE_TYPE_NOTIFICATION);

        return true;
    }

    /**
     * Mark all notifications as read return description.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function mark_all_notifications_as_read_returns() {
        return new external_value(PARAM_BOOL, 'True if the messages were marked read, false otherwise');
    }

    /**
     * Get unread conversations count parameters description.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_unread_conversations_count_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for any user', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get unread messages count function.
     *
     * @since  3.2
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @return external_description
     */
    public static function get_unread_conversations_count($useridto) {
        global $USER, $CFG;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = self::validate_parameters(
            self::get_unread_conversations_count_parameters(),
            array('useridto' => $useridto)
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        } else {
            $useridto = $USER->id;
        }

        // Check if the current user is the receiver or just a privileged user.
        if ($useridto != $USER->id and !has_capability('moodle/site:readallmessages', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        return \core_message\api::count_unread_conversations($userto);
    }

    /**
     * Get unread conversations count return description.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function get_unread_conversations_count_returns() {
        return new external_value(PARAM_INT, 'The count of unread messages for the user');
    }

    /**
     * Get blocked users parameters description.
     *
     * @return external_function_parameters
     * @since 2.9
     */
    public static function get_blocked_users_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT,
                                'the user whose blocked users we want to retrieve',
                                VALUE_REQUIRED),
            )
        );
    }

    /**
     * Retrieve a list of users blocked
     *
     * @param  int $userid the user whose blocked users we want to retrieve
     * @return external_description
     * @since 2.9
     */
    public static function get_blocked_users($userid) {
        global $CFG, $USER, $PAGE;

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        // Validate params.
        $params = array(
            'userid' => $userid
        );
        $params = self::validate_parameters(self::get_blocked_users_parameters(), $params);
        $userid = $params['userid'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $user = core_user::get_user($userid, '*', MUST_EXIST);
        core_user::require_active_user($user);

        // Check if we have permissions for retrieve the information.
        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $userid) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        // Now, we can get safely all the blocked users.
        $users = message_get_blocked_users($user);

        $blockedusers = array();
        foreach ($users as $user) {
            $newuser = array(
                'id' => $user->id,
                'fullname' => fullname($user),
            );

            $userpicture = new user_picture($user);
            $userpicture->size = 1; // Size f1.
            $newuser['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);

            $blockedusers[] = $newuser;
        }

        $results = array(
            'users' => $blockedusers,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Get blocked users return description.
     *
     * @return external_single_structure
     * @since 2.9
     */
    public static function get_blocked_users_returns() {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'User ID'),
                            'fullname' => new external_value(PARAM_NOTAGS, 'User full name'),
                            'profileimageurl' => new external_value(PARAM_URL, 'User picture URL', VALUE_OPTIONAL)
                        )
                    ),
                    'List of blocked users'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 2.9
     */
    public static function mark_message_read_parameters() {
        return new external_function_parameters(
            array(
                'messageid' => new external_value(PARAM_INT, 'id of the message (in the message table)'),
                'timeread' => new external_value(PARAM_INT, 'timestamp for when the message should be marked read',
                    VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Mark a single message as read, trigger message_viewed event
     *
     * @param  int $messageid id of the message (in the message table)
     * @param  int $timeread timestamp for when the message should be marked read
     * @return external_description
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @since 2.9
     */
    public static function mark_message_read($messageid, $timeread) {
        global $CFG, $DB, $USER;

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        // Validate params.
        $params = array(
            'messageid' => $messageid,
            'timeread' => $timeread
        );
        $params = self::validate_parameters(self::mark_message_read_parameters(), $params);

        if (empty($params['timeread'])) {
            $timeread = time();
        } else {
            $timeread = $params['timeread'];
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $message = $DB->get_record('message', array('id' => $params['messageid']), '*', MUST_EXIST);

        if ($message->useridto != $USER->id) {
            throw new invalid_parameter_exception('Invalid messageid, you don\'t have permissions to mark this message as read');
        }

        $messageid = message_mark_message_read($message, $timeread);

        $results = array(
            'messageid' => $messageid,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 2.9
     */
    public static function mark_message_read_returns() {
        return new external_single_structure(
            array(
                'messageid' => new external_value(PARAM_INT, 'the id of the message in the message_read table'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Mark all messages as read parameters description.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function mark_all_messages_as_read_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for any user', VALUE_REQUIRED),
                'useridfrom' => new external_value(
                    PARAM_INT, 'the user id who send the message, 0 for any user. -10 or -20 for no-reply or support user',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Mark all notifications as read function.
     *
     * @since  3.2
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @param  int      $useridfrom     the user id who send the message. -10 or -20 for no-reply or support user
     * @return external_description
     */
    public static function mark_all_messages_as_read($useridto, $useridfrom) {
        global $USER, $CFG;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = self::validate_parameters(
            self::mark_all_messages_as_read_parameters(),
            array(
                'useridto' => $useridto,
                'useridfrom' => $useridfrom,
            )
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];
        $useridfrom = $params['useridfrom'];

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        }

        if (!empty($useridfrom)) {
            // We use get_user here because the from user can be the noreply or support user.
            $userfrom = core_user::get_user($useridfrom, '*', MUST_EXIST);
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and $useridfrom != $USER->id and
            // The deleteanymessage cap seems more reasonable here than readallmessages.
             !has_capability('moodle/site:deleteanymessage', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        \core_message\api::mark_all_read_for_user($useridto, $useridfrom, MESSAGE_TYPE_MESSAGE);

        return true;
    }

    /**
     * Mark all notifications as read return description.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function mark_all_messages_as_read_returns() {
        return new external_value(PARAM_BOOL, 'True if the messages were marked read, false otherwise');
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function delete_conversation_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The user id of who we want to delete the conversation for'),
                'otheruserid' => new external_value(PARAM_INT, 'The user id of the other user in the conversation'),
            )
        );
    }

    /**
     * Deletes a conversation.
     *
     * @param int $userid The user id of who we want to delete the conversation for
     * @param int $otheruserid The user id of the other user in the conversation
     * @return array
     * @throws moodle_exception
     * @since 3.2
     */
    public static function delete_conversation($userid, $otheruserid) {
        global $CFG;

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        // Validate params.
        $params = array(
            'userid' => $userid,
            'otheruserid' => $otheruserid,
        );
        $params = self::validate_parameters(self::delete_conversation_parameters(), $params);

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        if (\core_message\api::can_delete_conversation($user->id)) {
            $status = \core_message\api::delete_conversation($user->id, $otheruserid);
        } else {
            throw new moodle_exception('You do not have permission to delete messages');
        }

        $results = array(
            'status' => $status,
            'warnings' => $warnings
        );

        return $results;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since 3.2
     */
    public static function delete_conversation_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if the conversation was deleted, false otherwise'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.1
     */
    public static function delete_message_parameters() {
        return new external_function_parameters(
            array(
                'messageid' => new external_value(PARAM_INT, 'The message id'),
                'userid' => new external_value(PARAM_INT, 'The user id of who we want to delete the message for'),
                'read' => new external_value(PARAM_BOOL, 'If is a message read', VALUE_DEFAULT, true)
            )
        );
    }

    /**
     * Deletes a message
     *
     * @param  int $messageid the message id
     * @param  int $userid the user id of who we want to delete the message for
     * @param  bool $read if is a message read (default to true)
     * @return external_description
     * @throws moodle_exception
     * @since 3.1
     */
    public static function delete_message($messageid, $userid, $read = true) {
        global $CFG, $DB;

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        // Validate params.
        $params = array(
            'messageid' => $messageid,
            'userid' => $userid,
            'read' => $read
        );
        $params = self::validate_parameters(self::delete_message_parameters(), $params);

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $messagestable = $params['read'] ? 'message_read' : 'message';
        $message = $DB->get_record($messagestable, array('id' => $params['messageid']), '*', MUST_EXIST);

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        $status = false;
        if (message_can_delete_message($message, $user->id)) {
            $status = message_delete_message($message, $user->id);;
        } else {
            throw new moodle_exception('You do not have permission to delete this message');
        }

        $results = array(
            'status' => $status,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.1
     */
    public static function delete_message_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if the message was deleted, false otherwise'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function message_processor_config_form_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_REQUIRED),
                'name' => new external_value(PARAM_TEXT, 'The name of the message processor'),
                'formvalues' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_TEXT, 'name of the form element', VALUE_REQUIRED),
                            'value' => new external_value(PARAM_RAW, 'value of the form element', VALUE_REQUIRED),
                        )
                    ),
                    'Config form values',
                    VALUE_REQUIRED
                ),
            )
        );
    }

    /**
     * Processes a message processor config form.
     *
     * @param  int $userid the user id
     * @param  string $name the name of the processor
     * @param  array $formvalues the form values
     * @return external_description
     * @throws moodle_exception
     * @since 3.2
     */
    public static function message_processor_config_form($userid, $name, $formvalues) {
        global $USER, $CFG;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = self::validate_parameters(
            self::message_processor_config_form_parameters(),
            array(
                'userid' => $userid,
                'name' => $name,
                'formvalues' => $formvalues,
            )
        );

        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        $processor = get_message_processor($name);
        $preferences = [];
        $form = new stdClass();

        foreach ($formvalues as $formvalue) {
            // Curly braces to ensure interpretation is consistent between
            // php 5 and php 7.
            $form->{$formvalue['name']} = $formvalue['value'];
        }

        $processor->process_form($form, $preferences);

        if (!empty($preferences)) {
            set_user_preferences($preferences, $userid);
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.2
     */
    public static function message_processor_config_form_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_message_processor_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user'),
                'name' => new external_value(PARAM_TEXT, 'The name of the message processor', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get a message processor.
     *
     * @param int $userid
     * @param string $name the name of the processor
     * @return external_description
     * @throws moodle_exception
     * @since 3.2
     */
    public static function get_message_processor($userid = 0, $name) {
        global $USER, $PAGE, $CFG;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = self::validate_parameters(
            self::get_message_processor_parameters(),
            array(
                'userid' => $userid,
                'name' => $name,
            )
        );

        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);
        self::validate_context(context_user::instance($params['userid']));

        $processor = get_message_processor($name);

        $processoroutput = new \core_message\output\processor($processor, $user);
        $renderer = $PAGE->get_renderer('core_message');

        return $processoroutput->export_for_template($renderer);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.2
     */
    public static function get_message_processor_returns() {
        return new external_function_parameters(
            array(
                'systemconfigured' => new external_value(PARAM_BOOL, 'Site configuration status'),
                'userconfigured' => new external_value(PARAM_BOOL, 'The user configuration status'),
            )
        );
    }

    /**
     * Check that the user has enough permission to retrieve message or notifications preferences.
     *
     * @param  int $userid the user id requesting the preferences
     * @return stdClass full user object
     * @throws moodle_exception
     * @since  Moodle 3.2
     */
    protected static function validate_preferences_permissions($userid) {
        global $USER;

        if (empty($userid)) {
            $user = $USER;
        } else {
            $user = core_user::get_user($userid, '*', MUST_EXIST);
            core_user::require_active_user($user);
        }

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        // Check access control.
        if ($user->id == $USER->id) {
            // Editing own message profile.
            require_capability('moodle/user:editownmessageprofile', $systemcontext);
        } else {
            // Teachers, parents, etc.
            $personalcontext = context_user::instance($user->id);
            require_capability('moodle/user:editmessageprofile', $personalcontext);
        }
        return $user;
    }

    /**
     * Returns a notification or message preference structure.
     *
     * @return external_single_structure the structure
     * @since  Moodle 3.2
     */
    protected static function get_preferences_structure() {
        return new external_single_structure(
            array(
                'userid' => new external_value(PARAM_INT, 'User id'),
                'disableall' => new external_value(PARAM_INT, 'Whether all the preferences are disabled'),
                'processors' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                            'name' => new external_value(PARAM_PLUGIN, 'Processor name'),
                            'hassettings' => new external_value(PARAM_BOOL, 'Whether has settings'),
                            'contextid' => new external_value(PARAM_INT, 'Context id'),
                            'userconfigured' => new external_value(PARAM_INT, 'Whether is configured by the user'),
                        )
                    ),
                    'Config form values'
                ),
                'components' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                            'notifications' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                                        'preferencekey' => new external_value(PARAM_ALPHANUMEXT, 'Preference key'),
                                        'processors' => new external_multiple_structure(
                                            new external_single_structure(
                                                array(
                                                    'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                                                    'name' => new external_value(PARAM_PLUGIN, 'Processor name'),
                                                    'locked' => new external_value(PARAM_BOOL, 'Is locked by admin?'),
                                                    'userconfigured' => new external_value(PARAM_INT, 'Is configured?'),
                                                    'loggedin' => new external_single_structure(
                                                        array(
                                                            'name' => new external_value(PARAM_NOTAGS, 'Name'),
                                                            'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                                                            'checked' => new external_value(PARAM_BOOL, 'Is checked?'),
                                                        )
                                                    ),
                                                    'loggedoff' => new external_single_structure(
                                                        array(
                                                            'name' => new external_value(PARAM_NOTAGS, 'Name'),
                                                            'displayname' => new external_value(PARAM_TEXT, 'Display name'),
                                                            'checked' => new external_value(PARAM_BOOL, 'Is checked?'),
                                                        )
                                                    ),
                                                )
                                            ),
                                            'Processors values for this notification'
                                        ),
                                    )
                                ),
                                'List of notificaitons for the component'
                            ),
                        )
                    ),
                    'Available components'
                ),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_user_notification_preferences_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get the notification preferences for a given user.
     *
     * @param int $userid id of the user, 0 for current user
     * @return external_description
     * @throws moodle_exception
     * @since 3.2
     */
    public static function get_user_notification_preferences($userid = 0) {
        global $PAGE;

        $params = self::validate_parameters(
            self::get_user_notification_preferences_parameters(),
            array(
                'userid' => $userid,
            )
        );
        $user = self::validate_preferences_permissions($params['userid']);

        $processors = get_message_processors();
        $providers = message_get_providers_for_user($user->id);
        $preferences = \core_message\api::get_all_message_preferences($processors, $providers, $user);
        $notificationlist = new \core_message\output\preferences\notification_list($processors, $providers, $preferences, $user);

        $renderer = $PAGE->get_renderer('core_message');

        $result = array(
            'warnings' => array(),
            'preferences' => $notificationlist->export_for_template($renderer)
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.2
     */
    public static function get_user_notification_preferences_returns() {
        return new external_function_parameters(
            array(
                'preferences' => self::get_preferences_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_user_message_preferences_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get the notification preferences for a given user.
     *
     * @param int $userid id of the user, 0 for current user
     * @return external_description
     * @throws moodle_exception
     * @since 3.2
     */
    public static function get_user_message_preferences($userid = 0) {
        global $PAGE;

        $params = self::validate_parameters(
            self::get_user_message_preferences_parameters(),
            array(
                'userid' => $userid,
            )
        );

        $user = self::validate_preferences_permissions($params['userid']);

        // Filter out enabled, available system_configured and user_configured processors only.
        $readyprocessors = array_filter(get_message_processors(), function($processor) {
            return $processor->enabled &&
                $processor->configured &&
                $processor->object->is_user_configured() &&
                // Filter out processors that don't have and message preferences to configure.
                $processor->object->has_message_preferences();
        });

        $providers = array_filter(message_get_providers_for_user($user->id), function($provider) {
            return $provider->component === 'moodle';
        });
        $preferences = \core_message\api::get_all_message_preferences($readyprocessors, $providers, $user);
        $notificationlistoutput = new \core_message\output\preferences\message_notification_list($readyprocessors,
            $providers, $preferences, $user);

        $renderer = $PAGE->get_renderer('core_message');

        $result = array(
            'warnings' => array(),
            'preferences' => $notificationlistoutput->export_for_template($renderer),
            'blocknoncontacts' => get_user_preferences('message_blocknoncontacts', '', $user->id) ? true : false,
        );
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.2
     */
    public static function get_user_message_preferences_returns() {
        return new external_function_parameters(
            array(
                'preferences' => self::get_preferences_structure(),
                'blocknoncontacts' => new external_value(PARAM_BOOL, 'Whether to block or not messages from non contacts'),
                'warnings' => new external_warnings(),
            )
        );
    }
}
