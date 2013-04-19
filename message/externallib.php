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
                            'textformat' => new external_format_value('text', VALUE_DEFAULT),
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
        require_once($CFG->dirroot . "/message/lib.php");

        //check if messaging is enabled
        if (!$CFG->messaging) {
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
                $errormessage = get_string('userisblockingyounoncontact', 'message');
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
     * @since 2.5
     */
    public static function create_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                )
            )
        );
    }

    /**
     * Create contacts.
     *
     * @param array $userids array of user IDs.
     * @return external_description
     * @since 2.5
     */
    public static function create_contacts($userids) {
        $params = array('userids' => $userids);
        $params = self::validate_parameters(self::create_contacts_parameters(), $params);

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_add_contact($id)) {
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
     * @since 2.5
     */
    public static function create_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Delete contacts parameters description.
     *
     * @return external_function_parameters
     * @since 2.5
     */
    public static function delete_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                )
            )
        );
    }

    /**
     * Delete contacts.
     *
     * @param array $userids array of user IDs.
     * @return null
     * @since 2.5
     */
    public static function delete_contacts($userids) {
        $params = array('userids' => $userids);
        $params = self::validate_parameters(self::delete_contacts_parameters(), $params);

        foreach ($params['userids'] as $id) {
            message_remove_contact($id);
        }

        return null;
    }

    /**
     * Delete contacts return description.
     *
     * @return external_description
     * @since 2.5
     */
    public static function delete_contacts_returns() {
        return null;
    }

    /**
     * Block contacts parameters description.
     *
     * @return external_function_parameters
     * @since 2.5
     */
    public static function block_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                )
            )
        );
    }

    /**
     * Block contacts.
     *
     * @param array $userids array of user IDs.
     * @return external_description
     * @since 2.5
     */
    public static function block_contacts($userids) {
        $params = array('userids' => $userids);
        $params = self::validate_parameters(self::block_contacts_parameters(), $params);

        $warnings = array();
        foreach ($params['userids'] as $id) {
            if (!message_block_contact($id)) {
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
     * @since 2.5
     */
    public static function block_contacts_returns() {
        return new external_warnings();
    }

    /**
     * Unblock contacts parameters description.
     *
     * @return external_function_parameters
     * @since 2.5
     */
    public static function unblock_contacts_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User ID'),
                    'List of user IDs'
                )
            )
        );
    }

    /**
     * Unblock contacts.
     *
     * @param array $userids array of user IDs.
     * @return null
     * @since 2.5
     */
    public static function unblock_contacts($userids) {
        $params = array('userids' => $userids);
        $params = self::validate_parameters(self::unblock_contacts_parameters(), $params);

        foreach ($params['userids'] as $id) {
            message_unblock_contact($id);
        }

        return null;
    }

    /**
     * Unblock contacts return description.
     *
     * @return external_description
     * @since 2.5
     */
    public static function unblock_contacts_returns() {
        return null;
    }

    /**
     * Get contacts parameters description.
     *
     * @return external_function_parameters
     * @since 2.5
     */
    public static function get_contacts_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get contacts.
     *
     * @param array $userids array of user IDs.
     * @return external_description
     * @since 2.5
     */
    public static function get_contacts() {
        global $CFG;
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

                // Try to get the user picture, but sometimes this method can return null.
                $userdetails = user_get_user_details($contact, null, array('profileimageurl', 'profileimageurlsmall'));
                if (!empty($userdetails)) {
                    $newcontact['profileimageurl'] = $userdetails['profileimageurl'];
                    $newcontact['profileimageurlsmall'] = $userdetails['profileimageurlsmall'];
                }

                $allcontacts[$mode][$key] = $newcontact;
            }
        }
        return $allcontacts;
    }

    /**
     * Get contacts return description.
     *
     * @return external_description
     * @since 2.5
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
     * @since 2.5
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
     * @since 2.5
     */
    public static function search_contacts($searchtext, $onlymycourses = false) {
        global $CFG, $USER;
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

            // Try to get the user picture, but sometimes this method can return null.
            $userdetails = user_get_user_details($user, null, array('profileimageurl', 'profileimageurlsmall'));
            if (!empty($userdetails)) {
                $newuser['profileimageurl'] = $userdetails['profileimageurl'];
                $newuser['profileimageurlsmall'] = $userdetails['profileimageurlsmall'];
            }

            $user = $newuser;
        }

        return $results;
    }

    /**
     * Search contacts return description.
     *
     * @return external_description
     * @since 2.5
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
}

/**
 * Deprecated message external functions
 *
 * @package    core_message
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.1
 * @deprecated Moodle 2.2 MDL-29106 - Please do not use this class any more.
 * @see core_notes_external
 */
class moodle_message_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_message_external::send_instant_messages_parameters()
     */
    public static function send_instantmessages_parameters() {
        return core_message_external::send_instant_messages_parameters();
    }

    /**
     * Send private messages from the current USER to other users
     *
     * @param array $messages An array of message to send.
     * @return array
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_message_external::send_instant_messages()
     */
    public static function send_instantmessages($messages = array()) {
        return core_message_external::send_instant_messages($messages);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_message_external::send_instant_messages_returns()
     */
    public static function send_instantmessages_returns() {
        return core_message_external::send_instant_messages_returns();
    }

}
