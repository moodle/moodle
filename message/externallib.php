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

defined('MOODLE_INTERNAL') || die();

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
     * @since Moodle 3.6
     */
    public static function send_messages_to_conversation_parameters() {
        return new external_function_parameters(
            array(
                'conversationid' => new external_value(PARAM_INT, 'id of the conversation'),
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'text' => new external_value(PARAM_RAW, 'the text of the message'),
                            'textformat' => new external_format_value('text', VALUE_DEFAULT, FORMAT_MOODLE),
                        )
                    )
                )
            )
        );
    }

    /**
     * Send messages from the current USER to a conversation.
     *
     * This conversation may be any type of conversation, individual or group.
     *
     * @param int $conversationid the id of the conversation to which the messages will be sent.
     * @param array $messages An array of message to send.
     * @return array the array of messages which were sent (created).
     * @since Moodle 3.6
     */
    public static function send_messages_to_conversation(int $conversationid, array $messages = []) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::send_messages_to_conversation_parameters(), [
            'conversationid' => $conversationid,
            'messages' => $messages
        ]);

        // Validate messages content before posting them.
        foreach ($params['messages'] as $message) {
            // Check message length.
            if (strlen($message['text']) > \core_message\api::MESSAGE_MAX_LENGTH) {
                throw new moodle_exception('errormessagetoolong', 'message');
            }
        }

        $messages = [];
        foreach ($params['messages'] as $message) {
            $createdmessage = \core_message\api::send_message_to_conversation($USER->id, $params['conversationid'], $message['text'],
                $message['textformat']);
            $createdmessage->text = message_format_message_text((object) [
                'smallmessage' => $createdmessage->text,
                'fullmessageformat' => external_validate_format($message['textformat']),
                'fullmessagetrust' => $createdmessage->fullmessagetrust
            ]);
            $messages[] = $createdmessage;
        }

        return $messages;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.6
     */
    public static function send_messages_to_conversation_returns() {
        return new external_multiple_structure(
            self::get_conversation_message_structure()
        );
    }


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

        // Ensure the current user is allowed to delete message for everyone.
        $candeletemessagesforallusers = has_capability('moodle/site:deleteanymessage', $context);

        $params = self::validate_parameters(self::send_instant_messages_parameters(), array('messages' => $messages));

        //retrieve all tousers of the messages
        $receivers = array();
        foreach($params['messages'] as $message) {
            $receivers[] = $message['touserid'];
        }
        list($sqluserids, $sqlparams) = $DB->get_in_or_equal($receivers);
        $tousers = $DB->get_records_select("user", "id " . $sqluserids . " AND deleted = 0", $sqlparams);

        $resultmessages = array();
        $messageids = array();
        foreach ($params['messages'] as $message) {
            $resultmsg = array(); //the infos about the success of the operation

            // We are going to do some checking.
            // Code should match /messages/index.php checks.
            $success = true;

            // Check the user exists.
            if (empty($tousers[$message['touserid']])) {
                $success = false;
                $errormessage = get_string('touserdoesntexist', 'message', $message['touserid']);
            }

            // Check message length.
            if ($success && strlen($message['text']) > \core_message\api::MESSAGE_MAX_LENGTH) {
                $success = false;
                $errormessage = get_string('errormessagetoolong', 'message');
            }

            // TODO MDL-31118 performance improvement - edit the function so we can pass an array instead userid
            // Check if the recipient can be messaged by the sender.
            if ($success && !\core_message\api::can_send_message($tousers[$message['touserid']]->id, $USER->id)) {
                $success = false;
                $errormessage = get_string('usercantbemessaged', 'message', fullname(\core_user::get_user($message['touserid'])));
            }

            // Now we can send the message (at least try).
            if ($success) {
                // TODO MDL-31118 performance improvement - edit the function so we can pass an array instead one touser object.
                $success = message_post_message($USER, $tousers[$message['touserid']],
                        $message['text'], external_validate_format($message['textformat']));
            }

            // Build the resultmsg.
            if (isset($message['clientmsgid'])) {
                $resultmsg['clientmsgid'] = $message['clientmsgid'];
            }
            if ($success) {
                $resultmsg['msgid'] = $success;
                $resultmsg['timecreated'] = time();
                $resultmsg['candeletemessagesforallusers'] = $candeletemessagesforallusers;
                $messageids[] = $success;
            } else {
                // WARNINGS: for backward compatibility we return this errormessage.
                //          We should have thrown exceptions as these errors prevent results to be returned.
                // See http://docs.moodle.org/dev/Errors_handling_in_web_services#When_to_send_a_warning_on_the_server_side .
                $resultmsg['msgid'] = -1;
                if (!isset($errormessage)) { // Nobody has set a message error or thrown an exception, let's set it.
                    $errormessage = get_string('messageundeliveredbynotificationsettings', 'error');
                }
                $resultmsg['errormessage'] = $errormessage;
            }

            $resultmessages[] = $resultmsg;
        }

        if (!empty($messageids)) {
            $messagerecords = $DB->get_records_list(
                'messages',
                'id',
                $messageids,
                '',
                'id, conversationid, smallmessage, fullmessageformat, fullmessagetrust');
            $resultmessages = array_map(function($resultmessage) use ($messagerecords, $USER) {
                $id = $resultmessage['msgid'];
                $resultmessage['conversationid'] = isset($messagerecords[$id]) ? $messagerecords[$id]->conversationid : null;
                $resultmessage['useridfrom'] = $USER->id;
                $resultmessage['text'] = message_format_message_text((object) [
                    'smallmessage' => $messagerecords[$id]->smallmessage,
                    'fullmessageformat' => external_validate_format($messagerecords[$id]->fullmessageformat),
                    'fullmessagetrust' => $messagerecords[$id]->fullmessagetrust
                ]);
                return $resultmessage;
            }, $resultmessages);
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
                    'errormessage' => new external_value(PARAM_TEXT, 'error message - if it failed', VALUE_OPTIONAL),
                    'text' => new external_value(PARAM_RAW, 'The text of the message', VALUE_OPTIONAL),
                    'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the message', VALUE_OPTIONAL),
                    'conversationid' => new external_value(PARAM_INT, 'The conversation id for this message', VALUE_OPTIONAL),
                    'useridfrom' => new external_value(PARAM_INT, 'The user id who sent the message', VALUE_OPTIONAL),
                    'candeletemessagesforallusers' => new external_value(PARAM_BOOL,
                        'If the user can delete messages in the conversation for all users', VALUE_DEFAULT, false),
                )
            )
        );
    }

    /**
     * Create contacts parameters description.
     *
     * @deprecated since Moodle 3.6
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
     * @deprecated since Moodle 3.6
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

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::create_contacts_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_add_contact($id, 0, $params['userid'])) {
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
     * @deprecated since Moodle 3.6
     * @return external_description
     * @since Moodle 2.5
     */
    public static function create_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function create_contacts_is_deprecated() {
        return true;
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

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::delete_contacts_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        foreach ($params['userids'] as $id) {
            \core_message\api::remove_contact($params['userid'], $id);
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
     * Mute conversations parameters description.
     *
     * @return external_function_parameters
     */
    public static function mute_conversations_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user who is blocking'),
                'conversationids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of the conversation', VALUE_REQUIRED)
                ),
            ]
        );
    }

    /**
     * Mutes conversations.
     *
     * @param int $userid The id of the user who is blocking
     * @param array $conversationids The list of conversations being muted
     * @return external_description
     */
    public static function mute_conversations(int $userid, array $conversationids) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'conversationids' => $conversationids];
        $params = self::validate_parameters(self::mute_conversations_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        foreach ($params['conversationids'] as $conversationid) {
            if (!\core_message\api::is_conversation_muted($params['userid'], $conversationid)) {
                \core_message\api::mute_conversation($params['userid'], $conversationid);
            }
        }

        return [];
    }

    /**
     * Mute conversations return description.
     *
     * @return external_description
     */
    public static function mute_conversations_returns() {
        return new external_warnings();
    }

    /**
     * Unmute conversations parameters description.
     *
     * @return external_function_parameters
     */
    public static function unmute_conversations_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user who is unblocking'),
                'conversationids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of the conversation', VALUE_REQUIRED)
                ),
            ]
        );
    }

    /**
     * Unmute conversations.
     *
     * @param int $userid The id of the user who is unblocking
     * @param array $conversationids The list of conversations being muted
     */
    public static function unmute_conversations(int $userid, array $conversationids) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'conversationids' => $conversationids];
        $params = self::validate_parameters(self::unmute_conversations_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        foreach ($params['conversationids'] as $conversationid) {
            \core_message\api::unmute_conversation($params['userid'], $conversationid);
        }

        return [];
    }

    /**
     * Unmute conversations return description.
     *
     * @return external_description
     */
    public static function unmute_conversations_returns() {
        return new external_warnings();
    }

    /**
     * Block user parameters description.
     *
     * @return external_function_parameters
     */
    public static function block_user_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user who is blocking'),
                'blockeduserid' => new external_value(PARAM_INT, 'The id of the user being blocked'),
            ]
        );
    }

    /**
     * Blocks a user.
     *
     * @param int $userid The id of the user who is blocking
     * @param int $blockeduserid The id of the user being blocked
     * @return external_description
     */
    public static function block_user(int $userid, int $blockeduserid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'blockeduserid' => $blockeduserid];
        $params = self::validate_parameters(self::block_user_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        // If the blocking is going to be useless then don't do it.
        if (\core_message\api::can_send_message($userid, $blockeduserid, true)) {
            return [];
        }

        if (!\core_message\api::is_blocked($params['userid'], $params['blockeduserid'])) {
            \core_message\api::block_user($params['userid'], $params['blockeduserid']);
        }

        return [];
    }

    /**
     * Block user return description.
     *
     * @return external_description
     */
    public static function block_user_returns() {
        return new external_warnings();
    }

    /**
     * Unblock user parameters description.
     *
     * @return external_function_parameters
     */
    public static function unblock_user_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user who is unblocking'),
                'unblockeduserid' => new external_value(PARAM_INT, 'The id of the user being unblocked'),
            ]
        );
    }

    /**
     * Unblock user.
     *
     * @param int $userid The id of the user who is unblocking
     * @param int $unblockeduserid The id of the user being unblocked
     */
    public static function unblock_user(int $userid, int $unblockeduserid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'unblockeduserid' => $unblockeduserid];
        $params = self::validate_parameters(self::unblock_user_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        \core_message\api::unblock_user($params['userid'], $params['unblockeduserid']);

        return [];
    }

    /**
     * Unblock user return description.
     *
     * @return external_description
     */
    public static function unblock_user_returns() {
        return new external_warnings();
    }

    /**
     * Block contacts parameters description.
     *
     * @deprecated since Moodle 3.6
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
     * @deprecated since Moodle 3.6
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

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::block_contacts_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_block_contact($id, $params['userid'])) {
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
     * @deprecated since Moodle 3.6
     * @return external_description
     * @since Moodle 2.5
     */
    public static function block_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function block_contacts_is_deprecated() {
        return true;
    }

    /**
     * Unblock contacts parameters description.
     *
     * @deprecated since Moodle 3.6
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

        $params = array('userids' => $userids, 'userid' => $userid);
        $params = self::validate_parameters(self::unblock_contacts_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        foreach ($params['userids'] as $id) {
            message_unblock_contact($id, $params['userid']);
        }

        return null;
    }

    /**
     * Unblock contacts return description.
     *
     * @deprecated since Moodle 3.6
     * @return external_description
     * @since Moodle 2.5
     */
    public static function unblock_contacts_returns() {
        return null;
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function unblock_contacts_is_deprecated() {
        return true;
    }

    /**
     * Returns contact requests parameters description.
     *
     * @return external_function_parameters
     */
    public static function get_contact_requests_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user we want the requests for'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Handles returning the contact requests for a user.
     *
     * This also includes the user data necessary to display information
     * about the user.
     *
     * It will not include blocked users.
     *
     * @param int $userid The id of the user we want to get the contact requests for
     * @param int $limitfrom
     * @param int $limitnum
     */
    public static function get_contact_requests(int $userid, int $limitfrom = 0, int $limitnum = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = [
            'userid' => $userid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        ];
        $params = self::validate_parameters(self::get_contact_requests_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        return \core_message\api::get_contact_requests($params['userid'], $params['limitfrom'], $params['limitnum']);
    }

    /**
     * Returns the contact requests return description.
     *
     * @return external_description
     */
    public static function get_contact_requests_returns() {
        return new external_multiple_structure(
            self::get_conversation_member_structure()
        );
    }

    /**
     * Returns the number of contact requests the user has received parameters description.
     *
     * @return external_function_parameters
     */
    public static function get_received_contact_requests_count_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user we want to return the number of ' .
                    'received contact requests for', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Returns the number of contact requests the user has received.
     *
     * @param int $userid The ID of the user we want to return the number of received contact requests for
     * @return external_value
     */
    public static function get_received_contact_requests_count(int $userid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = [
            'userid' => $userid,
        ];
        $params = self::validate_parameters(self::get_received_contact_requests_count_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        return \core_message\api::get_received_contact_requests_count($params['userid']);
    }

    /**
     * Returns the number of contact requests the user has received return description.
     *
     * @return external_value
     */
    public static function get_received_contact_requests_count_returns() {
        return new external_value(PARAM_INT, 'The number of received contact requests');
    }

    /**
     * Returns get conversation members parameters description.
     *
     * @return external_function_parameters
     */
    public static function get_conversation_members_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user we are performing this action on behalf of'),
                'conversationid' => new external_value(PARAM_INT, 'The id of the conversation'),
                'includecontactrequests' => new external_value(PARAM_BOOL, 'Do we want to include contact requests?',
                    VALUE_DEFAULT, false),
                'includeprivacyinfo' => new external_value(PARAM_BOOL, 'Do we want to include privacy info?',
                    VALUE_DEFAULT, false),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Returns a list of conversation members.
     *
     * @param int $userid The user we are returning the conversation members for, used by helper::get_member_info.
     * @param int $conversationid The id of the conversation
     * @param bool $includecontactrequests Do we want to include contact requests with this data?
     * @param bool $includeprivacyinfo Do we want to include privacy info?
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function get_conversation_members(int $userid, int $conversationid, bool $includecontactrequests = false,
                                                    bool $includeprivacyinfo = false, int $limitfrom = 0, int $limitnum = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = [
            'userid' => $userid,
            'conversationid' => $conversationid,
            'includecontactrequests' => $includecontactrequests,
            'includeprivacyinfo' => $includeprivacyinfo,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        ];
        $params = self::validate_parameters(self::get_conversation_members_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        // The user needs to be a part of the conversation before querying who the members are.
        if (!\core_message\api::is_user_in_conversation($params['userid'], $params['conversationid'])) {
            throw new moodle_exception('You are not a member of this conversation.');
        }

        return \core_message\api::get_conversation_members($params['userid'], $params['conversationid'], $params['includecontactrequests'],
            $params['includeprivacyinfo'], $params['limitfrom'], $params['limitnum']);
    }

    /**
     * Returns the get conversation members return description.
     *
     * @return external_description
     */
    public static function get_conversation_members_returns() {
        return new external_multiple_structure(
            self::get_conversation_member_structure()
        );
    }

    /**
     * Creates a contact request parameters description.
     *
     * @return external_function_parameters
     */
    public static function create_contact_request_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user making the request'),
                'requesteduserid' => new external_value(PARAM_INT, 'The id of the user being requested')
            ]
        );
    }

    /**
     * Creates a contact request.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     */
    public static function create_contact_request(int $userid, int $requesteduserid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'requesteduserid' => $requesteduserid];
        $params = self::validate_parameters(self::create_contact_request_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['userid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        $result = [
            'warnings' => []
        ];

        if (!\core_message\api::can_create_contact($params['userid'], $params['requesteduserid'])) {
            $result['warnings'][] = [
                'item' => 'user',
                'itemid' => $params['requesteduserid'],
                'warningcode' => 'cannotcreatecontactrequest',
                'message' => 'You are unable to create a contact request for this user'
            ];
        } else {
            if ($requests = \core_message\api::get_contact_requests_between_users($params['userid'], $params['requesteduserid'])) {
                // There should only ever be one but just in case there are multiple then we can return the first.
                $result['request'] = array_shift($requests);
            } else {
                $result['request'] = \core_message\api::create_contact_request($params['userid'], $params['requesteduserid']);
            }
        }

        return $result;
    }

    /**
     * Creates a contact request return description.
     *
     * @return external_description
     */
    public static function create_contact_request_returns() {
        return new external_single_structure(
            array(
                'request' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'Message id'),
                        'userid' => new external_value(PARAM_INT, 'User from id'),
                        'requesteduserid' => new external_value(PARAM_INT, 'User to id'),
                        'timecreated' => new external_value(PARAM_INT, 'Time created'),
                    ),
                    'request record',
                    VALUE_OPTIONAL
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Confirm a contact request parameters description.
     *
     * @return external_function_parameters
     */
    public static function confirm_contact_request_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user making the request'),
                'requesteduserid' => new external_value(PARAM_INT, 'The id of the user being requested')
            ]
        );
    }

    /**
     * Confirm a contact request.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     */
    public static function confirm_contact_request(int $userid, int $requesteduserid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'requesteduserid' => $requesteduserid];
        $params = self::validate_parameters(self::confirm_contact_request_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['requesteduserid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        \core_message\api::confirm_contact_request($params['userid'], $params['requesteduserid']);

        return [];
    }

    /**
     * Confirm a contact request return description.
     *
     * @return external_description
     */
    public static function confirm_contact_request_returns() {
        return new external_warnings();
    }

    /**
     * Declines a contact request parameters description.
     *
     * @return external_function_parameters
     */
    public static function decline_contact_request_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user making the request'),
                'requesteduserid' => new external_value(PARAM_INT, 'The id of the user being requested')
            ]
        );
    }

    /**
     * Declines a contact request.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     */
    public static function decline_contact_request(int $userid, int $requesteduserid) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $params = ['userid' => $userid, 'requesteduserid' => $requesteduserid];
        $params = self::validate_parameters(self::decline_contact_request_parameters(), $params);

        $capability = 'moodle/site:manageallmessaging';
        if (($USER->id != $params['requesteduserid']) && !has_capability($capability, $context)) {
            throw new required_capability_exception($context, $capability, 'nopermissions', '');
        }

        \core_message\api::decline_contact_request($params['userid'], $params['requesteduserid']);

        return [];
    }

    /**
     * Declines a contact request return description.
     *
     * @return external_description
     */
    public static function decline_contact_request_returns() {
        return new external_warnings();
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
                'lastmessagedate' => new external_value(PARAM_INT, 'Timestamp for last message', VALUE_DEFAULT, null),
                'messageid' => new external_value(PARAM_INT, 'The unique search message id', VALUE_DEFAULT, null),
                'showonlinestatus' => new external_value(PARAM_BOOL, 'Show the user\'s online status?'),
                'isonline' => new external_value(PARAM_BOOL, 'The user\'s online status'),
                'isread' => new external_value(PARAM_BOOL, 'If the user has read the message'),
                'isblocked' => new external_value(PARAM_BOOL, 'If the user has been blocked'),
                'unreadcount' => new external_value(PARAM_INT, 'The number of unread messages in this conversation',
                    VALUE_DEFAULT, null),
                'conversationid' => new external_value(PARAM_INT, 'The id of the conversation', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the structure of a conversation.
     *
     * @return external_single_structure
     * @since Moodle 3.6
     */
    private static function get_conversation_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The conversation id'),
                'name' => new external_value(PARAM_RAW, 'The conversation name, if set', VALUE_DEFAULT, null),
                'subname' => new external_value(PARAM_RAW, 'A subtitle for the conversation name, if set', VALUE_DEFAULT, null),
                'imageurl' => new external_value(PARAM_URL, 'A link to the conversation picture, if set', VALUE_DEFAULT, null),
                'type' => new external_value(PARAM_INT, 'The type of the conversation (1=individual,2=group,3=self)'),
                'membercount' => new external_value(PARAM_INT, 'Total number of conversation members'),
                'ismuted' => new external_value(PARAM_BOOL, 'If the user muted this conversation'),
                'isfavourite' => new external_value(PARAM_BOOL, 'If the user marked this conversation as a favourite'),
                'isread' => new external_value(PARAM_BOOL, 'If the user has read all messages in the conversation'),
                'unreadcount' => new external_value(PARAM_INT, 'The number of unread messages in this conversation',
                    VALUE_DEFAULT, null),
                'members' => new external_multiple_structure(
                    self::get_conversation_member_structure()
                ),
                'messages' => new external_multiple_structure(
                    self::get_conversation_message_structure()
                ),
                'candeletemessagesforallusers' => new external_value(PARAM_BOOL,
                    'If the user can delete messages in the conversation for all users', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Return the structure of a conversation member.
     *
     * @return external_single_structure
     * @since Moodle 3.6
     */
    private static function get_conversation_member_structure() {
        $result = [
            'id' => new external_value(PARAM_INT, 'The user id'),
            'fullname' => new external_value(PARAM_NOTAGS, 'The user\'s name'),
            'profileurl' => new external_value(PARAM_URL, 'The link to the user\'s profile page'),
            'profileimageurl' => new external_value(PARAM_URL, 'User picture URL'),
            'profileimageurlsmall' => new external_value(PARAM_URL, 'Small user picture URL'),
            'isonline' => new external_value(PARAM_BOOL, 'The user\'s online status'),
            'showonlinestatus' => new external_value(PARAM_BOOL, 'Show the user\'s online status?'),
            'isblocked' => new external_value(PARAM_BOOL, 'If the user has been blocked'),
            'iscontact' => new external_value(PARAM_BOOL, 'Is the user a contact?'),
            'isdeleted' => new external_value(PARAM_BOOL, 'Is the user deleted?'),
            'canmessageevenifblocked' => new external_value(PARAM_BOOL,
                'If the user can still message even if they get blocked'),
            'canmessage' => new external_value(PARAM_BOOL, 'If the user can be messaged'),
            'requirescontact' => new external_value(PARAM_BOOL, 'If the user requires to be contacts'),
        ];

        $result['contactrequests'] = new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'The id of the contact request'),
                    'userid' => new external_value(PARAM_INT, 'The id of the user who created the contact request'),
                    'requesteduserid' => new external_value(PARAM_INT, 'The id of the user confirming the request'),
                    'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the contact request'),
                ]
            ), 'The contact requests', VALUE_OPTIONAL
        );

        $result['conversations'] = new external_multiple_structure(new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Conversations id'),
                'type' => new external_value(PARAM_INT, 'Conversation type: private or public'),
                'name' => new external_value(PARAM_RAW, 'Multilang compatible conversation name'. VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the conversation'),
            ), 'information about conversation', VALUE_OPTIONAL),
            'Conversations between users', VALUE_OPTIONAL
        );

        return new external_single_structure(
            $result
        );
    }

    /**
     * Return the structure of a message area message.
     *
     * @return external_single_structure
     * @since Moodle 3.6
     */
    private static function get_conversation_message_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The id of the message'),
                'useridfrom' => new external_value(PARAM_INT, 'The id of the user who sent the message'),
                'text' => new external_value(PARAM_RAW, 'The text of the message'),
                'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the message'),
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
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_search_users_in_course_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $users = \core_message\api::search_users_in_course(
            $params['userid'],
            $params['courseid'],
            $params['search'],
            $params['limitfrom'],
            $params['limitnum']
        );
        $results = new \core_message\output\messagearea\user_search_results($users);

        $renderer = $PAGE->get_renderer('core_message');
        return $results->export_for_template($renderer);
    }

    /**
     * Get messagearea search users in course returns.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_search_users_in_course_is_deprecated() {
        return true;
    }

    /**
     * Get messagearea search users parameters.
     *
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_search_users_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        list($contacts, $courses, $noncontacts) = \core_message\api::search_users(
            $params['userid'],
            $params['search'],
            $params['limitnum']
        );

        $search = new \core_message\output\messagearea\user_search_results($contacts, $courses, $noncontacts);

        $renderer = $PAGE->get_renderer('core_message');
        return $search->export_for_template($renderer);
    }

    /**
     * Get messagearea search users returns.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_search_users_is_deprecated() {
        return true;
    }

    /**
     * Get messagearea message search users parameters.
     *
     * @return external_function_parameters
     * @since 3.6
     */
    public static function message_search_users_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who is performing the search'),
                'search' => new external_value(PARAM_RAW, 'The string being searched'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get search users results.
     *
     * @param int $userid The id of the user who is performing the search
     * @param string $search The string being searched
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     * @throws moodle_exception
     * @since 3.6
     */
    public static function message_search_users($userid, $search, $limitfrom = 0, $limitnum = 0) {
        global $USER;

        $systemcontext = context_system::instance();

        $params = array(
            'userid' => $userid,
            'search' => $search,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum
        );
        $params = self::validate_parameters(self::message_search_users_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        list($contacts, $noncontacts) = \core_message\api::message_search_users(
            $params['userid'],
            $params['search'],
            $params['limitfrom'],
            $params['limitnum']);

        return array('contacts' => $contacts, 'noncontacts' => $noncontacts);
    }

    /**
     * Get messagearea message search users returns.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function message_search_users_returns() {
        return new external_single_structure(
            array(
                'contacts' => new external_multiple_structure(
                    self::get_conversation_member_structure()
                ),
                'noncontacts' => new external_multiple_structure(
                    self::get_conversation_member_structure()
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
        global $CFG, $USER;

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
        $params = self::validate_parameters(self::data_for_messagearea_search_messages_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $messages = \core_message\api::search_messages(
            $params['userid'],
            $params['search'],
            $params['limitfrom'],
            $params['limitnum']
        );

        $data = new \stdClass();
        $data->contacts = [];
        foreach ($messages as $message) {
            $contact = new \stdClass();
            $contact->userid = $message->userid;
            $contact->fullname = $message->fullname;
            $contact->profileimageurl = $message->profileimageurl;
            $contact->profileimageurlsmall = $message->profileimageurlsmall;
            $contact->messageid = $message->messageid;
            $contact->ismessaging = $message->ismessaging;
            $contact->sentfromcurrentuser = false;
            if ($message->lastmessage) {
                if ($message->userid !== $message->useridfrom) {
                    $contact->sentfromcurrentuser = true;
                }
                $contact->lastmessage = shorten_text($message->lastmessage, 60);
            } else {
                $contact->lastmessage = null;
            }
            $contact->lastmessagedate = $message->lastmessagedate;
            $contact->showonlinestatus = is_null($message->isonline) ? false : true;
            $contact->isonline = $message->isonline;
            $contact->isblocked = $message->isblocked;
            $contact->isread = $message->isread;
            $contact->unreadcount = $message->unreadcount;
            $contact->conversationid = $message->conversationid;

            $data->contacts[] = $contact;
        }

        return $data;
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
     * Get conversations parameters.
     *
     * @return external_function_parameters
     * @since 3.6
     */
    public static function get_conversations_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we are viewing conversations for'),
                'limitfrom' => new external_value(PARAM_INT, 'The offset to start at', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number of conversations to this', VALUE_DEFAULT, 0),
                'type' => new external_value(PARAM_INT, 'Filter by type', VALUE_DEFAULT, null),
                'favourites' => new external_value(PARAM_BOOL, 'Whether to restrict the results to contain NO favourite
                conversations (false), ONLY favourite conversation (true), or ignore any restriction altogether (null)',
                    VALUE_DEFAULT, null),
                'mergeself' => new external_value(PARAM_BOOL, 'Whether to include self-conversations (true) or ONLY private
                    conversations (false) when private conversations are requested.',
                    VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Get the list of conversations for the user.
     *
     * @param int $userid The id of the user who is performing the search
     * @param int $limitfrom
     * @param int $limitnum
     * @param int|null $type
     * @param bool|null $favourites
     * @param bool $mergeself whether to include self-conversations (true) or ONLY private conversations (false)
     *             when private conversations are requested.
     * @return stdClass
     * @throws \moodle_exception if the messaging feature is disabled on the site.
     * @since 3.2
     */
    public static function get_conversations($userid, $limitfrom = 0, $limitnum = 0, int $type = null, bool $favourites = null,
            bool $mergeself = false) {
        global $CFG, $USER;

        // All the standard BL checks.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = array(
            'userid' => $userid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
            'type' => $type,
            'favourites' => $favourites,
            'mergeself' => $mergeself
        );
        $params = self::validate_parameters(self::get_conversations_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $conversations = \core_message\api::get_conversations(
            $params['userid'],
            $params['limitfrom'],
            $params['limitnum'],
            $params['type'],
            $params['favourites'],
            $params['mergeself']
        );

        return (object) ['conversations' => $conversations];
    }

    /**
     * Get conversations returns.
     *
     * @return external_single_structure
     * @since 3.6
     */
    public static function get_conversations_returns() {
        return new external_single_structure(
            [
                'conversations' => new external_multiple_structure(
                    self::get_conversation_structure(true)
                )
            ]
        );
    }

    /**
     * Get conversation parameters.
     *
     * @return external_function_parameters
     */
    public static function get_conversation_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we are viewing conversations for'),
                'conversationid' => new external_value(PARAM_INT, 'The id of the conversation to fetch'),
                'includecontactrequests' => new external_value(PARAM_BOOL, 'Include contact requests in the members'),
                'includeprivacyinfo' => new external_value(PARAM_BOOL, 'Include privacy info in the members'),
                'memberlimit' => new external_value(PARAM_INT, 'Limit for number of members', VALUE_DEFAULT, 0),
                'memberoffset' => new external_value(PARAM_INT, 'Offset for member list', VALUE_DEFAULT, 0),
                'messagelimit' => new external_value(PARAM_INT, 'Limit for number of messages', VALUE_DEFAULT, 100),
                'messageoffset' => new external_value(PARAM_INT, 'Offset for messages list', VALUE_DEFAULT, 0),
                'newestmessagesfirst' => new external_value(PARAM_BOOL, 'Order messages by newest first', VALUE_DEFAULT, true)
            )
        );
    }

    /**
     * Get a single conversation.
     *
     * @param int $userid The user id to get the conversation for
     * @param int $conversationid The id of the conversation to fetch
     * @param bool $includecontactrequests Should contact requests be included between members
     * @param bool $includeprivacyinfo Should privacy info be included between members
     * @param int $memberlimit Limit number of members to load
     * @param int $memberoffset Offset members by this amount
     * @param int $messagelimit Limit number of messages to load
     * @param int $messageoffset Offset the messages
     * @param bool $newestmessagesfirst Order messages by newest first
     * @return stdClass
     * @throws \moodle_exception if the messaging feature is disabled on the site.
     */
    public static function get_conversation(
        int $userid,
        int $conversationid,
        bool $includecontactrequests = false,
        bool $includeprivacyinfo = false,
        int $memberlimit = 0,
        int $memberoffset = 0,
        int $messagelimit = 0,
        int $messageoffset = 0,
        bool $newestmessagesfirst = true
    ) {
        global $CFG, $DB, $USER;

        // All the standard BL checks.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = [
            'userid' => $userid,
            'conversationid' => $conversationid,
            'includecontactrequests' => $includecontactrequests,
            'includeprivacyinfo' => $includeprivacyinfo,
            'memberlimit' => $memberlimit,
            'memberoffset' => $memberoffset,
            'messagelimit' => $messagelimit,
            'messageoffset' => $messageoffset,
            'newestmessagesfirst' => $newestmessagesfirst
        ];
        self::validate_parameters(self::get_conversation_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        $conversation = \core_message\api::get_conversation(
            $params['userid'],
            $params['conversationid'],
            $params['includecontactrequests'],
            $params['includeprivacyinfo'],
            $params['memberlimit'],
            $params['memberoffset'],
            $params['messagelimit'],
            $params['messageoffset'],
            $params['newestmessagesfirst']
        );

        if ($conversation) {
            return $conversation;
        } else {
            // We have to throw an exception here because the external functions annoyingly
            // don't accept null to be returned for a single structure.
            throw new \moodle_exception('errorconversationdoesnotexist', 'message');
        }
    }

    /**
     * Get conversation returns.
     *
     * @return external_single_structure
     */
    public static function get_conversation_returns() {
        return self::get_conversation_structure();
    }

    /**
     * Get conversation parameters.
     *
     * @return external_function_parameters
     */
    public static function get_conversation_between_users_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we are viewing conversations for'),
                'otheruserid' => new external_value(PARAM_INT, 'The other user id'),
                'includecontactrequests' => new external_value(PARAM_BOOL, 'Include contact requests in the members'),
                'includeprivacyinfo' => new external_value(PARAM_BOOL, 'Include privacy info in the members'),
                'memberlimit' => new external_value(PARAM_INT, 'Limit for number of members', VALUE_DEFAULT, 0),
                'memberoffset' => new external_value(PARAM_INT, 'Offset for member list', VALUE_DEFAULT, 0),
                'messagelimit' => new external_value(PARAM_INT, 'Limit for number of messages', VALUE_DEFAULT, 100),
                'messageoffset' => new external_value(PARAM_INT, 'Offset for messages list', VALUE_DEFAULT, 0),
                'newestmessagesfirst' => new external_value(PARAM_BOOL, 'Order messages by newest first', VALUE_DEFAULT, true)
            )
        );
    }

    /**
     * Get a single conversation between users.
     *
     * @param int $userid The user id to get the conversation for
     * @param int $otheruserid The other user id
     * @param bool $includecontactrequests Should contact requests be included between members
     * @param bool $includeprivacyinfo Should privacy info be included between members
     * @param int $memberlimit Limit number of members to load
     * @param int $memberoffset Offset members by this amount
     * @param int $messagelimit Limit number of messages to load
     * @param int $messageoffset Offset the messages
     * @param bool $newestmessagesfirst Order messages by newest first
     * @return stdClass
     * @throws \moodle_exception if the messaging feature is disabled on the site.
     */
    public static function get_conversation_between_users(
        int $userid,
        int $otheruserid,
        bool $includecontactrequests = false,
        bool $includeprivacyinfo = false,
        int $memberlimit = 0,
        int $memberoffset = 0,
        int $messagelimit = 0,
        int $messageoffset = 0,
        bool $newestmessagesfirst = true
    ) {
        global $CFG, $DB, $USER;

        // All the standard BL checks.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = [
            'userid' => $userid,
            'otheruserid' => $otheruserid,
            'includecontactrequests' => $includecontactrequests,
            'includeprivacyinfo' => $includeprivacyinfo,
            'memberlimit' => $memberlimit,
            'memberoffset' => $memberoffset,
            'messagelimit' => $messagelimit,
            'messageoffset' => $messageoffset,
            'newestmessagesfirst' => $newestmessagesfirst
        ];
        self::validate_parameters(self::get_conversation_between_users_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        $conversationid = \core_message\api::get_conversation_between_users([$params['userid'], $params['otheruserid']]);
        $conversation = null;

        if ($conversationid) {
            $conversation = \core_message\api::get_conversation(
                $params['userid'],
                $conversationid,
                $params['includecontactrequests'],
                $params['includeprivacyinfo'],
                $params['memberlimit'],
                $params['memberoffset'],
                $params['messagelimit'],
                $params['messageoffset'],
                $params['newestmessagesfirst']
            );
        }

        if ($conversation) {
            return $conversation;
        } else {
            // We have to throw an exception here because the external functions annoyingly
            // don't accept null to be returned for a single structure.
            throw new \moodle_exception('errorconversationdoesnotexist', 'message');
        }
    }

    /**
     * Get conversation returns.
     *
     * @return external_single_structure
     */
    public static function get_conversation_between_users_returns() {
        return self::get_conversation_structure(true);
    }

    /**
     * Get self-conversation parameters.
     *
     * @return external_function_parameters
     */
    public static function get_self_conversation_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we are viewing self-conversations for'),
                'messagelimit' => new external_value(PARAM_INT, 'Limit for number of messages', VALUE_DEFAULT, 100),
                'messageoffset' => new external_value(PARAM_INT, 'Offset for messages list', VALUE_DEFAULT, 0),
                'newestmessagesfirst' => new external_value(PARAM_BOOL, 'Order messages by newest first', VALUE_DEFAULT, true)
            )
        );
    }

    /**
     * Get a single self-conversation.
     *
     * @param int $userid The user id to get the self-conversation for
     * @param int $messagelimit Limit number of messages to load
     * @param int $messageoffset Offset the messages
     * @param bool $newestmessagesfirst Order messages by newest first
     * @return stdClass
     * @throws \moodle_exception if the messaging feature is disabled on the site.
     * @since Moodle 3.7
     */
    public static function get_self_conversation(
        int $userid,
        int $messagelimit = 0,
        int $messageoffset = 0,
        bool $newestmessagesfirst = true
    ) {
        global $CFG;

        // All the standard BL checks.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = [
            'userid' => $userid,
            'messagelimit' => $messagelimit,
            'messageoffset' => $messageoffset,
            'newestmessagesfirst' => $newestmessagesfirst
        ];
        self::validate_parameters(self::get_self_conversation_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        $conversation = \core_message\api::get_self_conversation($params['userid']);

        if ($conversation) {
            $conversation = \core_message\api::get_conversation(
                $params['userid'],
                $conversation->id,
                false,
                false,
                0,
                0,
                $params['messagelimit'],
                $params['messageoffset'],
                $params['newestmessagesfirst']
            );
        }

        if ($conversation) {
            return $conversation;
        } else {
            // We have to throw an exception here because the external functions annoyingly
            // don't accept null to be returned for a single structure.
            throw new \moodle_exception('errorconversationdoesnotexist', 'message');
        }
    }

    /**
     * Get conversation returns.
     *
     * @return external_single_structure
     */
    public static function get_self_conversation_returns() {
        return self::get_conversation_structure();
    }

    /**
     * The messagearea conversations parameters.
     *
     * @deprecated since 3.6
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
     * NOTE FOR FINAL DEPRECATION:
     * When removing this method, please also consider removal of get_conversations_legacy_formatter()
     * from the \core_message\helper class. This helper method was used solely to format the new get_conversations() return data
     * into the old format used here, and in message/index.php. If we no longer need either of these, then that method can be
     * removed.
     *
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_conversations_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $conversations = \core_message\api::get_conversations($params['userid'], $params['limitfrom'], $params['limitnum']);

        // Format the conversations in the legacy style, as the get_conversations method has since been changed.
        $conversations = \core_message\helper::get_conversations_legacy_formatter($conversations);

        $conversations = new \core_message\output\messagearea\contacts(null, $conversations);

        $renderer = $PAGE->get_renderer('core_message');
        return $conversations->export_for_template($renderer);
    }

    /**
     * The messagearea conversations return structure.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_conversations_is_deprecated() {
        return true;
    }

    /**
     * The messagearea contacts return parameters.
     *
     * @deprecated since 3.6
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_contacts_parameters() {
        return self::data_for_messagearea_conversations_parameters();
    }

    /**
     * Get messagearea contacts parameters.
     *
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_contacts_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $contacts = \core_message\api::get_contacts($params['userid'], $params['limitfrom'], $params['limitnum']);
        $contacts = new \core_message\output\messagearea\contacts(null, $contacts);

        $renderer = $PAGE->get_renderer('core_message');
        return $contacts->export_for_template($renderer);
    }

    /**
     * The messagearea contacts return structure.
     *
     * @deprecated since 3.6
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_contacts_returns() {
        return self::data_for_messagearea_conversations_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_contacts_is_deprecated() {
        return true;
    }

    /**
     * The messagearea messages parameters.
     *
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_messages_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['currentuserid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        if ($params['newest']) {
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
        if (!empty($params['timefrom'])) {
            $timeto = time() - 1;
        } else {
            $timeto = 0;
        }

        // No requesting messages from the current time, as stated above.
        if ($params['timefrom'] == time()) {
            $messages = [];
        } else {
            $messages = \core_message\api::get_messages($params['currentuserid'], $params['otheruserid'], $params['limitfrom'],
                                                        $params['limitnum'], $sort, $params['timefrom'], $timeto);
        }

        $messages = new \core_message\output\messagearea\messages($params['currentuserid'], $params['otheruserid'], $messages);

        $renderer = $PAGE->get_renderer('core_message');
        return $messages->export_for_template($renderer);
    }

    /**
     * The messagearea messages return structure.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_messages_is_deprecated() {
        return true;
    }

    /**
     * The conversation messages parameters.
     *
     * @return external_function_parameters
     * @since 3.6
     */
    public static function get_conversation_messages_parameters() {
        return new external_function_parameters(
            array(
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'convid' => new external_value(PARAM_INT, 'The conversation id'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0),
                'newest' => new external_value(PARAM_BOOL, 'Newest first?', VALUE_DEFAULT, false),
                'timefrom' => new external_value(PARAM_INT,
                    'The timestamp from which the messages were created', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get conversation messages.
     *
     * @param  int $currentuserid The current user's id.
     * @param  int $convid The conversation id.
     * @param  int $limitfrom Return a subset of records, starting at this point (optional).
     * @param  int $limitnum Return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @param  bool $newest True for getting first newest messages, false otherwise.
     * @param  int  $timefrom The time from the conversation messages to get.
     * @return array The messages and members who have sent some of these messages.
     * @throws moodle_exception
     * @since 3.6
     */
    public static function get_conversation_messages(int $currentuserid, int $convid, int $limitfrom = 0, int $limitnum = 0,
                                                         bool $newest = false, int $timefrom = 0) {
        global $CFG, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'currentuserid' => $currentuserid,
            'convid' => $convid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
            'newest' => $newest,
            'timefrom' => $timefrom,
        );
        $params = self::validate_parameters(self::get_conversation_messages_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['currentuserid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        // Check that the user belongs to the conversation.
        if (!\core_message\api::is_user_in_conversation($params['currentuserid'], $params['convid'])) {
            throw new moodle_exception('User is not part of conversation.');
        }

        $sort = $newest ? 'timecreated DESC' : 'timecreated ASC';

        // We need to enforce a one second delay on messages to avoid race conditions of current
        // messages still being sent.
        //
        // There is a chance that we could request messages before the current time's
        // second has elapsed and while other messages are being sent in that same second. In which
        // case those messages will be lost.
        //
        // Instead we ignore the current time in the result set to ensure that second is allowed to finish.
        $timeto = empty($params['timefrom']) ? 0 : time() - 1;

        // No requesting messages from the current time, as stated above.
        if ($params['timefrom'] == time()) {
            $messages = [];
        } else {
            $messages = \core_message\api::get_conversation_messages(
                $params['currentuserid'],
                $params['convid'],
                $params['limitfrom'],
                $params['limitnum'],
                $sort,
                $params['timefrom'],
                $timeto);
        }

        return $messages;
    }

    /**
     * The messagearea messages return structure.
     *
     * @return external_single_structure
     * @since 3.6
     */
    public static function get_conversation_messages_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The conversation id'),
                'members' => new external_multiple_structure(
                    self::get_conversation_member_structure()
                ),
                'messages' => new external_multiple_structure(
                    self::get_conversation_message_structure()
                ),
            )
        );
    }

    /**
     * The user contacts return parameters.
     *
     * @return external_function_parameters
     */
    public static function get_user_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of the user who we retrieving the contacts for'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get user contacts.
     *
     * @param int $userid The id of the user who we are viewing conversations for
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     * @throws moodle_exception
     */
    public static function get_user_contacts(int $userid, int $limitfrom = 0, int $limitnum = 0) {
        global $CFG, $USER;

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
        $params = self::validate_parameters(self::get_user_contacts_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        return \core_message\api::get_user_contacts($params['userid'], $params['limitfrom'], $params['limitnum']);
    }

    /**
     * The user contacts return structure.
     *
     * @return external_multiple_structure
     */
    public static function get_user_contacts_returns() {
        return new external_multiple_structure(
            self::get_conversation_member_structure()
        );
    }

    /**
     * The get most recent message return parameters.
     *
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_get_most_recent_message_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['currentuserid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $message = \core_message\api::get_most_recent_message($params['currentuserid'], $params['otheruserid']);
        $message = new \core_message\output\messagearea\message($message);

        $renderer = $PAGE->get_renderer('core_message');
        return $message->export_for_template($renderer);
    }

    /**
     * The get most recent message return structure.
     *
     * @deprecated since 3.6
     * @return external_single_structure
     * @since 3.2
     */
    public static function data_for_messagearea_get_most_recent_message_returns() {
        return self::get_messagearea_message_structure();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_get_most_recent_message_is_deprecated() {
        return true;
    }

    /**
     * The get profile parameters.
     *
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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
        $params = self::validate_parameters(self::data_for_messagearea_get_profile_parameters(), $params);
        self::validate_context($systemcontext);

        if (($USER->id != $params['currentuserid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        $profile = \core_message\api::get_profile($params['currentuserid'], $params['otheruserid']);
        $profile = new \core_message\output\messagearea\profile($profile);

        $renderer = $PAGE->get_renderer('core_message');
        return $profile->export_for_template($renderer);
    }

    /**
     * The get profile return structure.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function data_for_messagearea_get_profile_is_deprecated() {
        return true;
    }

    /**
     * Get contacts parameters description.
     *
     * @deprecated since 3.6
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_contacts_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get contacts.
     *
     * @deprecated since 3.6
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_contacts() {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        require_once($CFG->dirroot . '/user/lib.php');

        $allcontacts = array('online' => [], 'offline' => [], 'strangers' => []);
        $contacts = \core_message\api::get_contacts_with_unread_message_count($USER->id);
        foreach ($contacts as $contact) {
            // Set the mode.
            $mode = 'offline';
            if (\core_message\helper::is_online($contact->lastaccess)) {
                $mode = 'online';
            }

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

            $allcontacts[$mode][$contact->id] = $newcontact;
        }

        $strangers = \core_message\api::get_non_contacts_with_unread_message_count($USER->id);
        foreach ($strangers as $contact) {
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

            $allcontacts['strangers'][$contact->id] = $newcontact;
        }

        // Add noreply user and support user to the list, if they don't exist.
        $supportuser = core_user::get_support_user();
        if (!isset($strangers[$supportuser->id]) && !$supportuser->deleted) {
            $supportuser->messagecount = message_count_unread_messages($USER, $supportuser);
            if ($supportuser->messagecount > 0) {
                $supportuser->fullname = fullname($supportuser);
                $supportuser->unread = $supportuser->messagecount;
                $allcontacts['strangers'][$supportuser->id] = $supportuser;
            }
        }

        $noreplyuser = core_user::get_noreply_user();
        if (!isset($strangers[$noreplyuser->id]) && !$noreplyuser->deleted) {
            $noreplyuser->messagecount = message_count_unread_messages($USER, $noreplyuser);
            if ($noreplyuser->messagecount > 0) {
                $noreplyuser->fullname = fullname($noreplyuser);
                $noreplyuser->unread = $noreplyuser->messagecount;
                $allcontacts['strangers'][$noreplyuser->id] = $noreplyuser;
            }
        }

        return $allcontacts;
    }

    /**
     * Get contacts return description.
     *
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_contacts_is_deprecated() {
        return true;
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
                if (!$message->notification) {
                    if (($useridto == $USER->id and $message->timeusertodeleted) or
                        ($useridfrom == $USER->id and $message->timeuserfromdeleted)) {
                        unset($messages[$mid]);
                        continue;
                    }
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
                            'userfromfullname' => new external_value(PARAM_TEXT, 'User from full name'),
                            'component' => new external_value(PARAM_TEXT, 'The component that generated the notification',
                                VALUE_OPTIONAL),
                            'eventtype' => new external_value(PARAM_TEXT, 'The type of notification', VALUE_OPTIONAL),
                            'customdata' => new external_value(PARAM_RAW, 'Custom data to be passed to the message processor.
                                The data here is serialised using json_encode().', VALUE_OPTIONAL),
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

        \core_message\api::mark_all_notifications_as_read($useridto, $useridfrom);

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
        $users = \core_message\api::get_blocked_users($user->id);

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
                'messageid' => new external_value(PARAM_INT, 'id of the message in the messages table'),
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

        $sql = "SELECT m.*, mcm.userid as useridto
                  FROM {messages} m
            INNER JOIN {message_conversations} mc
                    ON m.conversationid = mc.id
            INNER JOIN {message_conversation_members} mcm
                    ON mcm.conversationid = mc.id
             LEFT JOIN {message_user_actions} mua
                    ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                 WHERE mua.id is NULL
                   AND mcm.userid != m.useridfrom
                   AND m.id = ?";
        $messageparams = [];
        $messageparams[] = $USER->id;
        $messageparams[] = \core_message\api::MESSAGE_ACTION_READ;
        $messageparams[] = $params['messageid'];
        $message = $DB->get_record_sql($sql, $messageparams, MUST_EXIST);

        if ($message->useridto != $USER->id) {
            throw new invalid_parameter_exception('Invalid messageid, you don\'t have permissions to mark this message as read');
        }

        \core_message\api::mark_message_as_read($USER->id, $message, $timeread);

        $results = array(
            'messageid' => $message->id,
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
                'messageid' => new external_value(PARAM_INT, 'the id of the message in the messages table'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function mark_notification_read_parameters() {
        return new external_function_parameters(
            array(
                'notificationid' => new external_value(PARAM_INT, 'id of the notification'),
                'timeread' => new external_value(PARAM_INT, 'timestamp for when the notification should be marked read',
                    VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Mark a single notification as read.
     *
     * This will trigger a 'notification_viewed' event.
     *
     * @param int $notificationid id of the notification
     * @param int $timeread timestamp for when the notification should be marked read
     * @return external_description
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function mark_notification_read($notificationid, $timeread) {
        global $CFG, $DB, $USER;

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        // Validate params.
        $params = array(
            'notificationid' => $notificationid,
            'timeread' => $timeread
        );
        $params = self::validate_parameters(self::mark_notification_read_parameters(), $params);

        if (empty($params['timeread'])) {
            $timeread = time();
        } else {
            $timeread = $params['timeread'];
        }

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $notification = $DB->get_record('notifications', ['id' => $params['notificationid']], '*', MUST_EXIST);

        if ($notification->useridto != $USER->id) {
            throw new invalid_parameter_exception('Invalid notificationid, you don\'t have permissions to mark this ' .
                'notification as read');
        }

        \core_message\api::mark_notification_as_read($notification, $timeread);

        $results = array(
            'notificationid' => $notification->id,
            'warnings' => $warnings
        );

        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function mark_notification_read_returns() {
        return new external_single_structure(
            array(
                'notificationid' => new external_value(PARAM_INT, 'id of the notification'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Mark all messages as read parameters description.
     *
     * @deprecated since 3.6
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
     * Mark all messages as read function.
     *
     * @deprecated since 3.6
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @param  int      $useridfrom     the user id who send the message. -10 or -20 for no-reply or support user
     * @return external_description
     * @since  3.2
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

        if ($useridfrom) {
            if ($conversationid = \core_message\api::get_conversation_between_users([$useridto, $useridfrom])) {
                \core_message\api::mark_all_messages_as_read($useridto, $conversationid);
            }
        } else {
            \core_message\api::mark_all_messages_as_read($useridto);
        }

        return true;
    }

    /**
     * Mark all messages as read return description.
     *
     * @deprecated since 3.6
     * @return external_single_structure
     * @since 3.2
     */
    public static function mark_all_messages_as_read_returns() {
        return new external_value(PARAM_BOOL, 'True if the messages were marked read, false otherwise');
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function mark_all_messages_as_read_is_deprecated() {
        return true;
    }

    /**
     * Mark all conversation messages as read parameters description.
     *
     * @return external_function_parameters
     * @since 3.6
     */
    public static function mark_all_conversation_messages_as_read_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The user id who who we are marking the messages as read for'),
                'conversationid' =>
                    new external_value(PARAM_INT, 'The conversation id who who we are marking the messages as read for')
            )
        );
    }

    /**
     * Mark all conversation messages as read function.
     *
     * @param int $userid The user id of who we want to delete the conversation for
     * @param int $conversationid The id of the conversations
     * @since 3.6
     */
    public static function mark_all_conversation_messages_as_read(int $userid, int $conversationid) {
        global $CFG;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $params = array(
            'userid' => $userid,
            'conversationid' => $conversationid,
        );
        $params = self::validate_parameters(self::mark_all_conversation_messages_as_read_parameters(), $params);

        $context = context_system::instance();
        self::validate_context($context);

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        if (\core_message\api::can_mark_all_messages_as_read($params['userid'], $params['conversationid'])) {
            \core_message\api::mark_all_messages_as_read($params['userid'], $params['conversationid']);
        } else {
            throw new moodle_exception('accessdenied', 'admin');
        }
    }

    /**
     * Mark all conversation messages as read return description.
     *
     * @return external_warnings
     * @since 3.6
     */
    public static function mark_all_conversation_messages_as_read_returns() {
        return null;
    }

    /**
     * Returns description of method parameters.
     *
     * @deprecated since 3.6
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
     * @deprecated since 3.6
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

        if (!$conversationid = \core_message\api::get_conversation_between_users([$params['userid'], $params['otheruserid']])) {
            return [];
        }

        if (\core_message\api::can_delete_conversation($user->id, $conversationid)) {
            \core_message\api::delete_conversation_by_id($user->id, $conversationid);
            $status = true;
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
     * @deprecated since 3.6
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
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function delete_conversation_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since 3.6
     */
    public static function delete_conversations_by_id_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The user id of who we want to delete the conversation for'),
                'conversationids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The id of the conversation'),
                    'List of conversation IDs'
                ),
            )
        );
    }

    /**
     * Deletes a conversation.
     *
     * @param int $userid The user id of who we want to delete the conversation for
     * @param int[] $conversationids The ids of the conversations
     * @return array
     * @throws moodle_exception
     * @since 3.6
     */
    public static function delete_conversations_by_id($userid, array $conversationids) {
        global $CFG;

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate params.
        $params = [
            'userid' => $userid,
            'conversationids' => $conversationids,
        ];
        $params = self::validate_parameters(self::delete_conversations_by_id_parameters(), $params);

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        foreach ($params['conversationids'] as $conversationid) {
            if (\core_message\api::can_delete_conversation($user->id, $conversationid)) {
                \core_message\api::delete_conversation_by_id($user->id, $conversationid);
            } else {
                throw new moodle_exception("You do not have permission to delete the conversation '$conversationid'");
            }
        }

        return [];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since 3.6
     */
    public static function delete_conversations_by_id_returns() {
        return new external_warnings();
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
        global $CFG;

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

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        if (\core_message\api::can_delete_message($user->id, $params['messageid'])) {
            $status = \core_message\api::delete_message($user->id, $params['messageid']);
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

        $params = self::validate_parameters(
            self::message_processor_config_form_parameters(),
            array(
                'userid' => $userid,
                'name' => $name,
                'formvalues' => $formvalues,
            )
        );

        $user = self::validate_preferences_permissions($params['userid']);

        $processor = get_message_processor($params['name']);
        $preferences = [];
        $form = new stdClass();

        foreach ($params['formvalues'] as $formvalue) {
            // Curly braces to ensure interpretation is consistent between
            // php 5 and php 7.
            $form->{$formvalue['name']} = $formvalue['value'];
        }

        $processor->process_form($form, $preferences);

        if (!empty($preferences)) {
            set_user_preferences($preferences, $params['userid']);
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

        $processor = get_message_processor($params['name']);

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
                                                    'lockedmessage' => new external_value(PARAM_TEXT,
                                                        'Text to display if locked', VALUE_OPTIONAL),
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
        global $CFG, $PAGE;

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

        $entertosend = get_user_preferences('message_entertosend', $CFG->messagingdefaultpressenter, $user);

        $result = array(
            'warnings' => array(),
            'preferences' => $notificationlistoutput->export_for_template($renderer),
            'blocknoncontacts' => \core_message\api::get_user_privacy_messaging_preference($user->id),
            'entertosend' => $entertosend
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
                'blocknoncontacts' => new external_value(PARAM_INT, 'Privacy messaging setting to define who can message you'),
                'entertosend' => new external_value(PARAM_BOOL, 'User preference for using enter to send messages'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters for the favourite_conversations() method.
     *
     * @return external_function_parameters
     */
    public static function set_favourite_conversations_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0),
                'conversations' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of the conversation', VALUE_DEFAULT, 0)
                )
            )
        );
    }

    /**
     * Favourite a conversation, or list of conversations for a user.
     *
     * @param int $userid the id of the user, or 0 for the current user.
     * @param array $conversationids the list of conversations ids to favourite.
     * @return array
     * @throws moodle_exception if messaging is disabled or if the user cannot perform the action.
     */
    public static function set_favourite_conversations(int $userid, array $conversationids) {
        global $CFG, $USER;

        // All the business logic checks that really shouldn't be in here.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }
        $params = [
            'userid' => $userid,
            'conversations' => $conversationids
        ];
        $params = self::validate_parameters(self::set_favourite_conversations_parameters(), $params);
        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        foreach ($params['conversations'] as $conversationid) {
            \core_message\api::set_favourite_conversation($conversationid, $params['userid']);
        }

        return [];
    }

    /**
     * Return a description of the returns for the create_user_favourite_conversations() method.
     *
     * @return external_description
     */
    public static function set_favourite_conversations_returns() {
        return new external_warnings();
    }

    /**
     * Returns description of method parameters for unfavourite_conversations() method.
     *
     * @return external_function_parameters
     */
    public static function unset_favourite_conversations_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0),
                'conversations' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of the conversation', VALUE_DEFAULT, 0)
                )
            )
        );
    }

    /**
     * Unfavourite a conversation, or list of conversations for a user.
     *
     * @param int $userid the id of the user, or 0 for the current user.
     * @param array $conversationids the list of conversations ids unset as favourites.
     * @return array
     * @throws moodle_exception if messaging is disabled or if the user cannot perform the action.
     */
    public static function unset_favourite_conversations(int $userid, array $conversationids) {
        global $CFG, $USER;

        // All the business logic checks that really shouldn't be in here.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }
        $params = [
            'userid' => $userid,
            'conversations' => $conversationids
        ];
        $params = self::validate_parameters(self::unset_favourite_conversations_parameters(), $params);
        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        foreach ($params['conversations'] as $conversationid) {
            \core_message\api::unset_favourite_conversation($conversationid, $params['userid']);
        }

        return [];
    }

    /**
     * Unset favourite conversations return description.
     *
     * @return external_description
     */
    public static function unset_favourite_conversations_returns() {
        return new external_warnings();
    }

    /**
     * Returns description of method parameters for get_member_info() method.
     *
     * @return external_function_parameters
     */
    public static function get_member_info_parameters() {
        return new external_function_parameters(
            array(
                'referenceuserid' => new external_value(PARAM_INT, 'id of the user'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'id of members to get')
                ),
                'includecontactrequests' => new external_value(PARAM_BOOL, 'include contact requests in response', VALUE_DEFAULT, false),
                'includeprivacyinfo' => new external_value(PARAM_BOOL, 'include privacy info in response', VALUE_DEFAULT, false)
            )
        );
    }

    /**
     * Returns conversation member info for the supplied users, relative to the supplied referenceuserid.
     *
     * This is the basic structure used when returning members, and includes information about the relationship between each member
     * and the referenceuser, such as a whether the referenceuser has marked the member as a contact, or has blocked them.
     *
     * @param int $referenceuserid the id of the user which check contact and blocked status.
     * @param array $userids
     * @return array the array of objects containing member info.
     * @throws moodle_exception if messaging is disabled or if the user cannot perform the action.
     */
    public static function get_member_info(
        int $referenceuserid,
        array $userids,
        bool $includecontactrequests = false,
        bool $includeprivacyinfo = false
    ) {
        global $CFG, $USER;

        // All the business logic checks that really shouldn't be in here.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }
        $params = [
            'referenceuserid' => $referenceuserid,
            'userids' => $userids,
            'includecontactrequests' => $includecontactrequests,
            'includeprivacyinfo' => $includeprivacyinfo
        ];
        $params = self::validate_parameters(self::get_member_info_parameters(), $params);
        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $referenceuserid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        return \core_message\helper::get_member_info(
            $params['referenceuserid'],
            $params['userids'],
            $params['includecontactrequests'],
            $params['includeprivacyinfo']
        );
    }

    /**
     * Get member info return description.
     *
     * @return external_description
     */
    public static function get_member_info_returns() {
        return new external_multiple_structure(
            self::get_conversation_member_structure()
        );
    }

    /**
     * Returns description of method parameters for get_conversation_counts() method.
     *
     * @return external_function_parameters
     */
    public static function get_conversation_counts_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Returns an array of conversation counts for the various types of conversations, including favourites.
     *
     * Return format:
     * [
     *     'favourites' => 0,
     *     'types' => [
     *          \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
     *          \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0
     *      ]
     * ]
     *
     * @param int $userid the id of the user whose counts we are fetching.
     * @return array the array of conversation counts, indexed by type.
     * @throws moodle_exception if the current user cannot perform this action.
     */
    public static function get_conversation_counts(int $userid) {
        global $CFG, $USER;

        // All the business logic checks that really shouldn't be in here.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $params = ['userid' => $userid];
        $params = self::validate_parameters(self::get_conversation_counts_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        return \core_message\api::get_conversation_counts($params['userid']);
    }

    /**
     * Get conversation counts return description.
     *
     * @return external_description
     */
    public static function get_conversation_counts_returns() {
        return new external_single_structure(
            [
                'favourites' => new external_value(PARAM_INT, 'Total number of favourite conversations'),
                'types' => new external_single_structure(
                    [
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => new external_value(PARAM_INT,
                            'Total number of individual conversations'),
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => new external_value(PARAM_INT,
                            'Total number of group conversations'),
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => new external_value(PARAM_INT,
                            'Total number of self conversations'),
                    ]
                ),
            ]
        );
    }

    /**
     * Returns description of method parameters for get_unread_conversation_counts() method.
     *
     * @return external_function_parameters
     */
    public static function get_unread_conversation_counts_parameters() {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Returns an array of unread conversation counts for the various types of conversations, including favourites.
     *
     * Return format:
     * [
     *     'favourites' => 0,
     *     'types' => [
     *          \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
     *          \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0
     *      ]
     * ]
     *
     * @param int $userid the id of the user whose counts we are fetching.
     * @return array the array of unread conversation counts, indexed by type.
     * @throws moodle_exception if the current user cannot perform this action.
     */
    public static function get_unread_conversation_counts(int $userid) {
        global $CFG, $USER;

        // All the business logic checks that really shouldn't be in here.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $params = ['userid' => $userid];
        $params = self::validate_parameters(self::get_unread_conversation_counts_parameters(), $params);

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        if (($USER->id != $params['userid']) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception('You do not have permission to perform this action.');
        }

        return \core_message\api::get_unread_conversation_counts($params['userid']);
    }

    /**
     * Get unread conversation counts return description.
     *
     * @return external_description
     */
    public static function get_unread_conversation_counts_returns() {
        return new external_single_structure(
            [
                'favourites' => new external_value(PARAM_INT, 'Total number of unread favourite conversations'),
                'types' => new external_single_structure(
                    [
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => new external_value(PARAM_INT,
                            'Total number of unread individual conversations'),
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => new external_value(PARAM_INT,
                            'Total number of unread group conversations'),
                        \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => new external_value(PARAM_INT,
                            'Total number of unread self conversations'),
                    ]
                ),
            ]
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since 3.7
     */
    public static function delete_message_for_all_users_parameters() {
        return new external_function_parameters(
            array(
                'messageid' => new external_value(PARAM_INT, 'The message id'),
                'userid' => new external_value(PARAM_INT, 'The user id of who we want to delete the message for all users')
            )
        );
    }
    /**
     * Deletes a message for all users
     *
     * @param  int $messageid the message id
     * @param  int $userid the user id of who we want to delete the message for all users
     * @return external_description
     * @throws moodle_exception
     * @since 3.7
     */
    public static function delete_message_for_all_users(int $messageid, int $userid) {
        global $CFG;

        // Check if private messaging between users is allowed.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        // Validate params.
        $params = array(
            'messageid' => $messageid,
            'userid' => $userid
        );
        $params = self::validate_parameters(self::delete_message_for_all_users_parameters(), $params);

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        // Checks if a user can delete a message for all users.
        if (core_message\api::can_delete_message_for_all_users($user->id, $params['messageid'])) {
            \core_message\api::delete_message_for_all_users($params['messageid']);
        } else {
            throw new moodle_exception('You do not have permission to delete this message for everyone.');
        }

        return [];
    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since 3.7
     */
    public static function delete_message_for_all_users_returns() {
        return new external_warnings();
    }
}
