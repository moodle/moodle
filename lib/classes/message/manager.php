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
 * New messaging manager class.
 *
 * @package   core_message
 * @since     Moodle 2.8
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

namespace core\message;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used for various messaging related stuff.
 *
 * Note: Do NOT use directly in your code, it is intended to be used from core code only.
 *
 * @access private
 *
 * @package   core_message
 * @since     Moodle 2.8
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
class manager {
    /** @var array buffer of pending messages */
    protected static $buffer = array();

    /** @var array buffer of pending messages to conversations */
    protected static $convmessagebuffer = array();

    /**
     * Used for calling processors, and generating event data when sending a message to a conversation.
     *
     * This is ONLY used for messages of type 'message' (notification=0), and is responsible for:
     *
     * 1. generation of per-user event data (to pass to processors)
     * 2. generation of the processors for each recipient member of the conversation
     * 3. calling said processors for each member, passing in the per-user (local) eventdata.
     * 4. generation of an appropriate event for the message send, depending on the conversation type
     *   - messages to individual conversations generate a 'message_sent' event (as per legacy send_message())
     *   - messages to group conversations generate a 'group_message_sent' event.
     *
     * @param message $eventdata
     * @param \stdClass $savemessage
     * @return int
     */
    public static function send_message_to_conversation(message $eventdata, \stdClass $savemessage): int {
        global $DB, $CFG, $SITE;

        if (empty($eventdata->convid)) {
            throw new \moodle_exception("Message is not being sent to a conversation. Please check event data.");
        }

        // Fetch default (site) preferences.
        $defaultpreferences = get_message_output_default_preferences();
        $preferencebase = $eventdata->component.'_'.$eventdata->name;

        // Because we're dealing with multiple recipients, we need to send a localised (per-user) version of the eventdata to each
        // processor, because of things like the language-specific subject. We're going to modify this, for each recipient member.
        // Any time we're modifying the event data here, we should be using the localised version.
        // This localised version is based on the generic event data, but we should keep that object intact, so we clone it.
        $localisedeventdata = clone $eventdata;

        // Get user records for all members of the conversation.
        // We must fetch distinct users, because it's possible for a user to message themselves via bulk user actions.
        // In such cases, there will be 2 records referring to the same user.
        $sql = "SELECT u.*, mca.id as ismuted
                  FROM {user} u
             LEFT JOIN {message_conversation_actions} mca
                    ON mca.userid = u.id AND mca.conversationid = ? AND mca.action = ?
                 WHERE u.id IN (
                          SELECT mcm.userid FROM {message_conversation_members} mcm
                           WHERE mcm.conversationid = ?
                 )";
        $members = $DB->get_records_sql($sql, [$eventdata->convid, \core_message\api::CONVERSATION_ACTION_MUTED,
            $eventdata->convid]);
        if (empty($members)) {
            throw new \moodle_exception("Conversation has no members or does not exist.");
        }

        if (!is_object($localisedeventdata->userfrom)) {
            $localisedeventdata->userfrom = $members[$localisedeventdata->userfrom];
        }

        // This should now hold only the other users (recipients).
        unset($members[$localisedeventdata->userfrom->id]);
        $otherusers = $members;

        // Get conversation type and name. We'll use this to determine which message subject to generate, depending on type.
        $conv = $DB->get_record('message_conversations', ['id' => $eventdata->convid], 'id, type, name');

        // For now Self conversations are not processed because users are aware of the messages sent by themselves, so we
        // can return early.
        if ($conv->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF) {
            return $savemessage->id;
        }
        $localisedeventdata->conversationtype = $conv->type;

        // We treat individual conversations the same as any direct message with 'userfrom' and 'userto' specified.
        // We know the other user, so set the 'userto' field so that the event code will get access to this field.
        // If this was a legacy caller (eventdata->userto is set), then use that instead, as we want to use the fields specified
        // in that object instead of using one fetched from the DB.
        $legacymessage = false;
        if ($conv->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
            if (isset($eventdata->userto)) {
                $legacymessage = true;
            } else {
                $otheruser = reset($otherusers);
                $eventdata->userto = $otheruser;
            }
        }

        // Fetch enabled processors.
        // If we are dealing with a message some processors may want to handle it regardless of user and site settings.
        $processors = array_filter(get_message_processors(false), function($processor) {
            if ($processor->object->force_process_messages()) {
                return true;
            }

            return ($processor->enabled && $processor->configured);
        });

        // For each member of the conversation, other than the sender:
        // 1. Set recipient specific event data (language specific, user prefs, etc)
        // 2. Generate recipient specific processor list
        // 3. Call send_message() to pass the message to processors and generate the relevant per-user events.
        $eventprocmaps = []; // Init the event/processors buffer.
        foreach ($otherusers as $recipient) {
            // If this message was a legacy (1:1) message, then we use the userto.
            if ($legacymessage) {
                $ismuted = $recipient->ismuted;

                $recipient = $eventdata->userto;
                $recipient->ismuted = $ismuted;
            }

            $usertoisrealuser = (\core_user::is_real_user($recipient->id) != false);

            // Using string manager directly so that strings in the message will be in the message recipients language rather than
            // the sender's.
            if ($conv->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
                $localisedeventdata->subject = get_string_manager()->get_string('unreadnewmessage', 'message',
                    fullname($localisedeventdata->userfrom), $recipient->lang);
            } else if ($conv->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP) {
                $stringdata = (object) ['name' => fullname($localisedeventdata->userfrom), 'conversationname' => $conv->name];
                $localisedeventdata->subject = get_string_manager()->get_string('unreadnewgroupconversationmessage', 'message',
                    $stringdata, $recipient->lang);
            }

            // Spoof the userto based on the current member id.
            $localisedeventdata->userto = $recipient;
            // Check if the notification is including images that will need a user token to be displayed outside Moodle.
            if (!empty($localisedeventdata->customdata)) {
                $customdata = json_decode($localisedeventdata->customdata);
                if (is_object($customdata) && !empty($customdata->notificationiconurl)) {
                    $customdata->tokenpluginfile = get_user_key('core_files', $localisedeventdata->userto->id);
                    $localisedeventdata->customdata = $customdata; // Message class will JSON encode again.
                }
            }

            $s = new \stdClass();
            $s->sitename = format_string($SITE->shortname, true, array('context' => \context_course::instance(SITEID)));
            $s->url = $CFG->wwwroot.'/message/index.php?id='.$eventdata->userfrom->id;
            $emailtagline = get_string_manager()->get_string('emailtagline', 'message', $s, $recipient->lang);

            $localisedeventdata->fullmessage = $eventdata->fullmessage;
            $localisedeventdata->fullmessagehtml = $eventdata->fullmessagehtml;
            if (!empty($localisedeventdata->fullmessage)) {
                // Prevent unclosed HTML elements.
                $localisedeventdata->fullmessage =
                    \core_message\helper::prevent_unclosed_html_tags($localisedeventdata->fullmessage, true);

                $localisedeventdata->fullmessage .= "\n\n---------------------------------------------------------------------\n"
                    . $emailtagline;
            }
            if (!empty($localisedeventdata->fullmessagehtml)) {
                // Prevent unclosed HTML elements.
                $localisedeventdata->fullmessagehtml =
                    \core_message\helper::prevent_unclosed_html_tags($localisedeventdata->fullmessagehtml, true);

                $localisedeventdata->fullmessagehtml .=
                    "<br><br>---------------------------------------------------------------------<br>" . $emailtagline;
            }

            // If recipient is internal user (noreply user), and emailstop is set then don't send any msg.
            if (!$usertoisrealuser && !empty($recipient->emailstop)) {
                debugging('Attempt to send msg to internal (noreply) user', DEBUG_NORMAL);
                return false;
            }

            // Fill in the array of processors to be used based on default and user preferences.
            // Do not process muted conversations.
            $processorlist = [];
            if (!$recipient->ismuted) {
                foreach ($processors as $processor) {
                    // Skip adding processors for internal user, if processor doesn't support sending message to internal user.
                    if (!$usertoisrealuser && !$processor->object->can_send_to_any_users()) {
                        continue;
                    }

                    // First find out permissions.
                    $defaultlockedpreference = $processor->name . '_provider_' . $preferencebase . '_locked';
                    $locked = false;
                    if (isset($defaultpreferences->{$defaultlockedpreference})) {
                        $locked = $defaultpreferences->{$defaultlockedpreference};
                    } else {
                        // MDL-25114 They supplied an $eventdata->component $eventdata->name combination which doesn't
                        // exist in the message_provider table (thus there is no default settings for them).
                        $preferrormsg = "Could not load preference $defaultlockedpreference.
                     Make sure the component and name you supplied to message_send() are valid.";
                        throw new \coding_exception($preferrormsg);
                    }

                    $enabledpreference = 'message_provider_'.$preferencebase . '_enabled';
                    $forced = false;
                    if ($locked && isset($defaultpreferences->{$enabledpreference})) {
                        $forced = $defaultpreferences->{$enabledpreference};
                    }

                    // Find out if user has configured this output.
                    // Some processors cannot function without settings from the user.
                    $userisconfigured = $processor->object->is_user_configured($recipient);

                    // DEBUG: notify if we are forcing unconfigured output.
                    if ($forced && !$userisconfigured) {
                        debugging('Attempt to force message delivery to user who has "' . $processor->name .
                            '" output unconfigured', DEBUG_NORMAL);
                    }

                    // Populate the list of processors we will be using.
                    if (!$eventdata->notification && $processor->object->force_process_messages()) {
                        $processorlist[] = $processor->name;
                    } else if ($forced && $userisconfigured) {
                        // An admin is forcing users to use this message processor. Use this processor unconditionally.
                        $processorlist[] = $processor->name;
                    } else if (!$locked && $userisconfigured && !$recipient->emailstop) {
                        // User has not disabled notifications.
                        // See if user set any notification preferences, otherwise use site default ones.
                        $userpreferencename = 'message_provider_' . $preferencebase . '_enabled';
                        if ($userpreference = get_user_preferences($userpreferencename, null, $recipient)) {
                            if (in_array($processor->name, explode(',', $userpreference))) {
                                $processorlist[] = $processor->name;
                            }
                        } else if (isset($defaultpreferences->{$userpreferencename})) {
                            if (in_array($processor->name, explode(',', $defaultpreferences->{$userpreferencename}))) {
                                $processorlist[] = $processor->name;
                            }
                        }
                    }
                }
            }
            // Batch up the localised event data and processor list for all users into a local buffer.
            $eventprocmaps[] = [clone($localisedeventdata), $processorlist];
        }
        // Then pass it off as one item of work, to be processed by send_conversation_message_to_processors(), which will
        // handle all transaction buffering logic.
        self::send_conversation_message_to_processors($eventprocmaps, $eventdata, $savemessage);

        return $savemessage->id;
    }

    /**
     * Takes a list of localised event data, and tries to send them to their respective member's message processors.
     *
     * Input format:
     *  [CONVID => [$localisedeventdata, $savemessage, $processorlist], ].
     *
     * @param array $eventprocmaps the array of localised event data and processors for each member of the conversation.
     * @param message $eventdata the original conversation message eventdata
     * @param \stdClass $savemessage the saved message record.
     * @throws \coding_exception
     */
    protected static function send_conversation_message_to_processors(array $eventprocmaps, message $eventdata,
                                                                      \stdClass $savemessage) {
        global $DB;

        // We cannot communicate with external systems in DB transactions,
        // buffer the messages if necessary.
        if ($DB->is_transaction_started()) {
            // Buffer this group conversation message and it's record.
            self::$convmessagebuffer[] = [$eventprocmaps, $eventdata, $savemessage];
            return;
        }

        // Send each localised version of the event data to each member's respective processors.
        foreach ($eventprocmaps as $eventprocmap) {
            $eventdata = $eventprocmap[0];
            $processorlist = $eventprocmap[1];
            self::call_processors($eventdata, $processorlist);
        }

        // Trigger event for sending a message or notification - we need to do this before marking as read!
        self::trigger_message_events($eventdata, $savemessage);
    }

    /**
     * Do the message sending.
     *
     * NOTE: to be used from message_send() only.
     *
     * @param \core\message\message $eventdata fully prepared event data for processors
     * @param \stdClass $savemessage the message saved in 'message' table
     * @param array $processorlist list of processors for target user
     * @return int $messageid the id from 'messages' (false is not returned)
     */
    public static function send_message(message $eventdata, \stdClass $savemessage, array $processorlist) {
        global $CFG;

        require_once($CFG->dirroot.'/message/lib.php'); // This is most probably already included from messagelib.php file.

        if (empty($processorlist)) {
            // Trigger event for sending a message or notification - we need to do this before marking as read!
            self::trigger_message_events($eventdata, $savemessage);

            if ($eventdata->notification) {
                // If they have deselected all processors and it's a notification mark it read. The user doesn't want to be
                // bothered.
                $savemessage->timeread = null;
                \core_message\api::mark_notification_as_read($savemessage);
            } else if (empty($CFG->messaging)) {
                // If it's a message and messaging is disabled mark it read.
                \core_message\api::mark_message_as_read($eventdata->userto->id, $savemessage);
            }

            return $savemessage->id;
        }

        // Let the manager do the sending or buffering when db transaction in progress.
        return self::send_message_to_processors($eventdata, $savemessage, $processorlist);
    }

    /**
     * Send message to message processors.
     *
     * @param \stdClass|\core\message\message $eventdata
     * @param \stdClass $savemessage
     * @param array $processorlist
     * @throws \moodle_exception
     * @return int $messageid
     */
    protected static function send_message_to_processors($eventdata, \stdClass $savemessage, array
    $processorlist) {
        global $CFG, $DB;

        // We cannot communicate with external systems in DB transactions,
        // buffer the messages if necessary.
        if ($DB->is_transaction_started()) {
            // We need to clone all objects so that devs may not modify it from outside later.
            $eventdata = clone($eventdata);
            $eventdata->userto = clone($eventdata->userto);
            $eventdata->userfrom = clone($eventdata->userfrom);

            // Conserve some memory the same was as $USER setup does.
            unset($eventdata->userto->description);
            unset($eventdata->userfrom->description);

            self::$buffer[] = array($eventdata, $savemessage, $processorlist);
            return $savemessage->id;
        }

        // Send the message to processors.
        if (!self::call_processors($eventdata, $processorlist)) {
            throw new \moodle_exception("Message was not sent.");
        }

        // Trigger event for sending a message or notification - we need to do this before marking as read!
        self::trigger_message_events($eventdata, $savemessage);

        if (!$eventdata->notification && empty($CFG->messaging)) {
            // If it's a message and messaging is disabled mark it read.
            \core_message\api::mark_message_as_read($eventdata->userto->id, $savemessage);
        }

        return $savemessage->id;
    }

    /**
     * Notification from DML layer.
     *
     * Note: to be used from DML layer only.
     */
    public static function database_transaction_commited() {
        if (!self::$buffer && !self::$convmessagebuffer) {
            return;
        }
        self::process_buffer();
    }

    /**
     * Notification from DML layer.
     *
     * Note: to be used from DML layer only.
     */
    public static function database_transaction_rolledback() {
        self::$buffer = array();
        self::$convmessagebuffer = array();
    }

    /**
     * Sent out any buffered messages if necessary.
     */
    protected static function process_buffer() {
        // Reset the buffers first in case we get exception from processor.
        $messages = self::$buffer;
        self::$buffer = array();
        $convmessages = self::$convmessagebuffer;
        self::$convmessagebuffer = array();

        foreach ($messages as $message) {
            list($eventdata, $savemessage, $processorlist) = $message;
            self::send_message_to_processors($eventdata, $savemessage, $processorlist);
        }

        foreach ($convmessages as $convmessage) {
            list($eventprocmap, $eventdata, $savemessage) = $convmessage;
            self::send_conversation_message_to_processors($eventprocmap, $eventdata, $savemessage);
        }
    }

    /**
     * Trigger an appropriate message creation event, based on the supplied $eventdata and $savemessage.
     *
     * @param message $eventdata the eventdata for the message.
     * @param \stdClass $savemessage the message record.
     * @throws \coding_exception
     */
    protected static function trigger_message_events(message $eventdata, \stdClass $savemessage) {
        global $DB;
        if ($eventdata->notification) {
            \core\event\notification_sent::create_from_ids(
                $eventdata->userfrom->id,
                $eventdata->userto->id,
                $savemessage->id,
                $eventdata->courseid
            )->trigger();
        } else { // Must be a message.
            // If the message is a group conversation, then trigger the 'group_message_sent' event.
            if ($eventdata->convid) {
                $conv = $DB->get_record('message_conversations', ['id' => $eventdata->convid], 'id, type');
                if ($conv->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP) {
                    \core\event\group_message_sent::create_from_ids(
                        $eventdata->userfrom->id,
                        $eventdata->convid,
                        $savemessage->id,
                        $eventdata->courseid
                    )->trigger();
                    return;
                }
                // Individual type conversations fall through to the default 'message_sent' event.
            }
            \core\event\message_sent::create_from_ids(
                $eventdata->userfrom->id,
                $eventdata->userto->id,
                $savemessage->id,
                $eventdata->courseid
            )->trigger();
        }
    }

    /**
     * For each processor, call it's send_message() method.
     *
     * @param message $eventdata the message object.
     * @param array $processorlist the list of processors for a single user.
     * @return bool false if error calling message processor
     */
    protected static function call_processors(message $eventdata, array $processorlist) {
        // Allow plugins to change the message/notification data before sending it.
        $pluginsfunction = get_plugins_with_function('pre_processor_message_send');
        $sendmsgsuccessful = true;
        foreach ($processorlist as $procname) {
            // Let new messaging class add custom content based on the processor.
            $proceventdata = ($eventdata instanceof message) ? $eventdata->get_eventobject_for_processor($procname) : $eventdata;

            if ($pluginsfunction) {
                foreach ($pluginsfunction as $plugintype => $plugins) {
                    foreach ($plugins as $pluginfunction) {
                        $pluginfunction($procname, $proceventdata);
                    }
                }
            }

            $stdproc = new \stdClass();
            $stdproc->name = $procname;
            $processor = \core_message\api::get_processed_processor_object($stdproc);
            if (!$processor->object->send_message($proceventdata)) {
                debugging('Error calling message processor ' . $procname);
                $sendmsgsuccessful = false;
            }
        }
        return $sendmsgsuccessful;
    }
}
