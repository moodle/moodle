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
 * Library functions for messaging
 *
 * @package   core_message
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('MESSAGE_SHORTLENGTH', 300);

define('MESSAGE_HISTORY_ALL', 1);

define('MESSAGE_SEARCH_MAX_RESULTS', 200);

define('MESSAGE_TYPE_NOTIFICATION', 'notification');
define('MESSAGE_TYPE_MESSAGE', 'message');

/**
 * Define contants for messaging default settings population. For unambiguity of
 * plugin developer intentions we use 4-bit value (LSB numbering):
 * bit 0 - whether to send message (MESSAGE_DEFAULT_ENABLED)
 * bit 1 - Deprecated: whether to send message (MESSAGE_DEFAULT_LOGGEDOFF). Used to mean only when the user is logged off.
 * bit 2..3 - messaging permission (MESSAGE_DISALLOWED|MESSAGE_PERMITTED|MESSAGE_FORCED)
 *
 * MESSAGE_PERMITTED_MASK contains the mask we use to distinguish permission setting.
 */

 /**
  * @deprecated since Moodle 4.0. Use MESSAGE_DEFAULT_ENABLED instead.
  * @todo Remove on MDL-73284.
  */
define('MESSAGE_DEFAULT_LOGGEDIN', 0x01); // 0001

 /**
  * @deprecated since Moodle 4.0 MDL-73284. Use MESSAGE_DEFAULT_ENABLED instead.
  * @todo Remove on MDL-73284.
  */
define('MESSAGE_DEFAULT_LOGGEDOFF', 0x02); // 0010

define('MESSAGE_DEFAULT_ENABLED', 0x01); // 0001.

define('MESSAGE_DISALLOWED', 0x04); // 0100.
define('MESSAGE_PERMITTED', 0x08); // 1000.
define('MESSAGE_FORCED', 0x0c); // 1100.

define('MESSAGE_PERMITTED_MASK', 0x0c); // 1100.

/**
 * Set default value for default outputs permitted setting
 * @deprecated since Moodle 4.0 MDL-73284.
 * @todo Remove on MDL-73284.
 */
define('MESSAGE_DEFAULT_PERMITTED', 'permitted');

/**
 * Set default values for polling.
 */
define('MESSAGE_DEFAULT_MIN_POLL_IN_SECONDS', 10);
define('MESSAGE_DEFAULT_MAX_POLL_IN_SECONDS', 2 * MINSECS);
define('MESSAGE_DEFAULT_TIMEOUT_POLL_IN_SECONDS', 5 * MINSECS);

/**
 * To get only read, unread or both messages or notifications.
 */
define('MESSAGE_GET_UNREAD', 0);
define('MESSAGE_GET_READ', 1);
define('MESSAGE_GET_READ_AND_UNREAD', 2);

/**
 * Returns the count of unread messages for user. Either from a specific user or from all users.
 *
 * @deprecated since 3.10
 * TODO: MDL-69643
 * @param object $user1 the first user. Defaults to $USER
 * @param object $user2 the second user. If null this function will count all of user 1's unread messages.
 * @return int the count of $user1's unread messages
 */
function message_count_unread_messages($user1=null, $user2=null) {
    global $USER, $DB;

    debugging('message_count_unread_messages is deprecated and no longer used',
        DEBUG_DEVELOPER);

    if (empty($user1)) {
        $user1 = $USER;
    }

    $sql = "SELECT COUNT(m.id)
              FROM {messages} m
        INNER JOIN {message_conversations} mc
                ON mc.id = m.conversationid
        INNER JOIN {message_conversation_members} mcm
                ON mcm.conversationid = mc.id
         LEFT JOIN {message_user_actions} mua
                ON (mua.messageid = m.id AND mua.userid = ? AND (mua.action = ? OR mua.action = ?))
             WHERE mua.id is NULL
               AND mcm.userid = ?";
    $params = [$user1->id, \core_message\api::MESSAGE_ACTION_DELETED, \core_message\api::MESSAGE_ACTION_READ, $user1->id];

    if (!empty($user2)) {
        $sql .= " AND m.useridfrom = ?";
        $params[] = $user2->id;
    } else {
        $sql .= " AND m.useridfrom <> ?";
        $params[] = $user1->id;
    }

    return $DB->count_records_sql($sql, $params);
}

/**
 * Try to guess how to convert the message to html.
 *
 * @access private
 *
 * @param stdClass $message
 * @param bool $forcetexttohtml
 * @return string html fragment
 */
function message_format_message_text($message, $forcetexttohtml = false) {
    // Note: this is a very nasty hack that tries to work around the weird messaging rules and design.

    $options = new stdClass();
    $options->para = false;
    $options->blanktarget = true;
    $options->trusted = isset($message->fullmessagetrust) ? $message->fullmessagetrust : false;

    $format = $message->fullmessageformat;

    if (strval($message->smallmessage) !== '') {
        if (!empty($message->notification)) {
            if (strval($message->fullmessagehtml) !== '' or strval($message->fullmessage) !== '') {
                $format = FORMAT_PLAIN;
            }
        }
        $messagetext = $message->smallmessage;

    } else if ($message->fullmessageformat == FORMAT_HTML) {
        if (strval($message->fullmessagehtml) !== '') {
            $messagetext = $message->fullmessagehtml;
        } else {
            $messagetext = $message->fullmessage;
            $format = FORMAT_MOODLE;
        }

    } else {
        if (strval($message->fullmessage) !== '') {
            $messagetext = $message->fullmessage;
        } else {
            $messagetext = $message->fullmessagehtml;
            $format = FORMAT_HTML;
        }
    }

    if ($forcetexttohtml) {
        // This is a crazy hack, why not set proper format when creating the notifications?
        if ($format === FORMAT_PLAIN) {
            $format = FORMAT_MOODLE;
        }
    }
    return format_text($messagetext, $format, $options);
}

/**
 * Search through course users.
 *
 * If $courseids contains the site course then this function searches
 * through all undeleted and confirmed users.
 *
 * @param int|array $courseids Course ID or array of course IDs.
 * @param string $searchtext the text to search for.
 * @param string $sort the column name to order by.
 * @param string|array $exceptions comma separated list or array of user IDs to exclude.
 * @return array An array of {@link $USER} records.
 */
function message_search_users($courseids, $searchtext, $sort='', $exceptions='') {
    global $CFG, $USER, $DB;

    // Basic validation to ensure that the parameter $courseids is not an empty array or an empty value.
    if (!$courseids) {
        $courseids = array(SITEID);
    }

    // Allow an integer to be passed.
    if (!is_array($courseids)) {
        $courseids = array($courseids);
    }

    $fullname = $DB->sql_fullname();
    $userfieldsapi = \core_user\fields::for_userpic();
    $ufields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;

    if (!empty($sort)) {
        $order = ' ORDER BY '. $sort;
    } else {
        $order = '';
    }

    $params = array(
        'userid' => $USER->id,
        'userid2' => $USER->id,
        'query' => "%$searchtext%"
    );

    if (empty($exceptions)) {
        $exceptions = array();
    } else if (!empty($exceptions) && is_string($exceptions)) {
        $exceptions = explode(',', $exceptions);
    }

    // Ignore self and guest account.
    $exceptions[] = $USER->id;
    $exceptions[] = $CFG->siteguest;

    // Exclude exceptions from the search result.
    list($except, $params_except) = $DB->get_in_or_equal($exceptions, SQL_PARAMS_NAMED, 'param', false);
    $except = ' AND u.id ' . $except;
    $params = array_merge($params_except, $params);

    if (in_array(SITEID, $courseids)) {
        // Search on site level.
        return $DB->get_records_sql("SELECT $ufields, mc.id as contactlistid, mub.id as userblockedid
                                       FROM {user} u
                                       LEFT JOIN {message_contacts} mc
                                            ON mc.contactid = u.id AND mc.userid = :userid
                                       LEFT JOIN {message_users_blocked} mub
                                            ON mub.userid = :userid2 AND mub.blockeduserid = u.id
                                      WHERE u.deleted = '0' AND u.confirmed = '1'
                                            AND (".$DB->sql_like($fullname, ':query', false).")
                                            $except
                                     $order", $params);
    } else {
        // Search in courses.

        // Getting the context IDs or each course.
        $contextids = array();
        foreach ($courseids as $courseid) {
            $context = context_course::instance($courseid);
            $contextids = array_merge($contextids, $context->get_parent_context_ids(true));
        }
        list($contextwhere, $contextparams) = $DB->get_in_or_equal(array_unique($contextids), SQL_PARAMS_NAMED, 'context');
        $params = array_merge($params, $contextparams);

        // Everyone who has a role assignment in this course or higher.
        // TODO: add enabled enrolment join here (skodak)
        $users = $DB->get_records_sql("SELECT DISTINCT $ufields, mc.id as contactlistid, mub.id as userblockedid
                                         FROM {user} u
                                         JOIN {role_assignments} ra ON ra.userid = u.id
                                         LEFT JOIN {message_contacts} mc
                                              ON mc.contactid = u.id AND mc.userid = :userid
                                         LEFT JOIN {message_users_blocked} mub
                                              ON mub.userid = :userid2 AND mub.blockeduserid = u.id
                                        WHERE u.deleted = '0' AND u.confirmed = '1'
                                              AND (".$DB->sql_like($fullname, ':query', false).")
                                              AND ra.contextid $contextwhere
                                              $except
                                       $order", $params);

        return $users;
    }
}

/**
 * Format a message for display in the message history
 *
 * @param object $message the message object
 * @param string $format optional date format
 * @param string $keywords keywords to highlight
 * @param string $class CSS class to apply to the div around the message
 * @return string the formatted message
 */
function message_format_message($message, $format='', $keywords='', $class='other') {

    static $dateformat;

    //if we haven't previously set the date format or they've supplied a new one
    if ( empty($dateformat) || (!empty($format) && $dateformat != $format) ) {
        if ($format) {
            $dateformat = $format;
        } else {
            $dateformat = get_string('strftimedatetimeshort');
        }
    }
    $time = userdate($message->timecreated, $dateformat);

    $messagetext = message_format_message_text($message, false);

    if ($keywords) {
        $messagetext = highlight($keywords, $messagetext);
    }

    $messagetext .= message_format_contexturl($message);

    $messagetext = clean_text($messagetext, FORMAT_HTML);

    return <<<TEMPLATE
<div class='message $class'>
    <a name="m{$message->id}"></a>
    <span class="message-meta"><span class="time">$time</span></span>: <span class="text">$messagetext</span>
</div>
TEMPLATE;
}

/**
 * Format a the context url and context url name of a message for display
 *
 * @param object $message the message object
 * @return string the formatted string
 */
function message_format_contexturl($message) {
    $s = null;

    if (!empty($message->contexturl)) {
        $displaytext = null;
        if (!empty($message->contexturlname)) {
            $displaytext= $message->contexturlname;
        } else {
            $displaytext= $message->contexturl;
        }
        $s .= html_writer::start_tag('div',array('class' => 'messagecontext'));
            $s .= get_string('view').': '.html_writer::tag('a', $displaytext, array('href' => $message->contexturl));
        $s .= html_writer::end_tag('div');
    }

    return $s;
}

/**
 * Send a message from one user to another. Will be delivered according to the message recipients messaging preferences
 *
 * @param object $userfrom the message sender
 * @param object $userto the message recipient
 * @param string $message the message
 * @param int $format message format such as FORMAT_PLAIN or FORMAT_HTML
 * @return int|false the ID of the new message or false
 */
function message_post_message($userfrom, $userto, $message, $format) {
    global $PAGE;

    $eventdata = new \core\message\message();
    $eventdata->courseid         = 1;
    $eventdata->component        = 'moodle';
    $eventdata->name             = 'instantmessage';
    $eventdata->userfrom         = $userfrom;
    $eventdata->userto           = $userto;

    //using string manager directly so that strings in the message will be in the message recipients language rather than the senders
    $eventdata->subject          = get_string_manager()->get_string('unreadnewmessage', 'message', fullname($userfrom), $userto->lang);

    if ($format == FORMAT_HTML) {
        $eventdata->fullmessagehtml  = $message;
        //some message processors may revert to sending plain text even if html is supplied
        //so we keep both plain and html versions if we're intending to send html
        $eventdata->fullmessage = html_to_text($eventdata->fullmessagehtml);
    } else {
        $eventdata->fullmessage      = $message;
        $eventdata->fullmessagehtml  = '';
    }

    $eventdata->fullmessageformat = $format;
    $eventdata->smallmessage     = $message;//store the message unfiltered. Clean up on output.
    $eventdata->timecreated     = time();
    $eventdata->notification    = 0;
    // User image.
    $userpicture = new user_picture($userfrom);
    $userpicture->size = 1; // Use f1 size.
    $userpicture->includetoken = $userto->id; // Generate an out-of-session token for the user receiving the message.
    $eventdata->customdata = [
        'notificationiconurl' => $userpicture->get_url($PAGE)->out(false),
        'actionbuttons' => [
            'send' => get_string_manager()->get_string('send', 'message', null, $eventdata->userto->lang),
        ],
        'placeholders' => [
            'send' => get_string_manager()->get_string('writeamessage', 'message', null, $eventdata->userto->lang),
        ],
    ];
    return message_send($eventdata);
}

/**
 * Get all message processors, validate corresponding plugin existance and
 * system configuration
 *
 * @param bool $ready only return ready-to-use processors
 * @param bool $reset Reset list of message processors (used in unit tests)
 * @param bool $resetonly Just reset, then exit
 * @return mixed $processors array of objects containing information on message processors
 */
function get_message_processors($ready = false, $reset = false, $resetonly = false) {
    global $DB, $CFG;

    static $processors;
    if ($reset) {
        $processors = array();

        if ($resetonly) {
            return $processors;
        }
    }

    if (empty($processors)) {
        // Get all processors, ensure the name column is the first so it will be the array key
        $processors = $DB->get_records('message_processors', null, 'name DESC', 'name, id, enabled');
        foreach ($processors as &$processor){
            $processor = \core_message\api::get_processed_processor_object($processor);
        }
    }
    if ($ready) {
        // Filter out enabled and system_configured processors
        $readyprocessors = $processors;
        foreach ($readyprocessors as $readyprocessor) {
            if (!($readyprocessor->enabled && $readyprocessor->configured)) {
                unset($readyprocessors[$readyprocessor->name]);
            }
        }
        return $readyprocessors;
    }

    return $processors;
}

/**
 * Get all message providers, validate their plugin existance and
 * system configuration
 *
 * @return mixed $processors array of objects containing information on message processors
 */
function get_message_providers() {
    global $CFG, $DB;

    $pluginman = core_plugin_manager::instance();

    $providers = $DB->get_records('message_providers', null, 'name');

    // Remove all the providers whose plugins are disabled or don't exist
    foreach ($providers as $providerid => $provider) {
        $plugin = $pluginman->get_plugin_info($provider->component);
        if ($plugin) {
            if ($plugin->get_status() === core_plugin_manager::PLUGIN_STATUS_MISSING) {
                unset($providers[$providerid]);   // Plugins does not exist
                continue;
            }
            if ($plugin->is_enabled() === false) {
                unset($providers[$providerid]);   // Plugin disabled
                continue;
            }
        }
    }
    return $providers;
}

/**
 * Get an instance of the message_output class for one of the output plugins.
 * @param string $type the message output type. E.g. 'email' or 'jabber'.
 * @return message_output message_output the requested class.
 */
function get_message_processor($type) {
    global $CFG;

    // Note, we cannot use the get_message_processors function here, becaues this
    // code is called during install after installing each messaging plugin, and
    // get_message_processors caches the list of installed plugins.

    $processorfile = $CFG->dirroot . "/message/output/{$type}/message_output_{$type}.php";
    if (!is_readable($processorfile)) {
        throw new coding_exception('Unknown message processor type ' . $type);
    }

    include_once($processorfile);

    $processclass = 'message_output_' . $type;
    if (!class_exists($processclass)) {
        throw new coding_exception('Message processor ' . $type .
                ' does not define the right class');
    }

    return new $processclass();
}

/**
 * Get messaging outputs default (site) preferences
 *
 * @return object $processors object containing information on message processors
 */
function get_message_output_default_preferences() {
    return get_config('message');
}

/**
 * Translate message default settings from binary value to the array of string
 * representing the settings to be stored. Also validate the provided value and
 * use default if it is malformed.
 * @todo Remove usage of MESSAGE_DEFAULT_LOGGEDOFF on MDL-73284.
 *
 * @param  int    $plugindefault Default setting suggested by plugin
 * @param  string $processorname The name of processor
 * @return array  $settings array of strings in the order: $locked, $enabled.
 */
function translate_message_default_setting($plugindefault, $processorname) {

    // Define the default setting.
    $processor = get_message_processor($processorname);
    $default = $processor->get_default_messaging_settings();

    // Validate the value. It should not exceed the maximum size
    if (!is_int($plugindefault) || ($plugindefault > 0x0f)) {
        debugging(get_string('errortranslatingdefault', 'message'));
        $plugindefault = $default;
    }
    // Use plugin default setting of 'permitted' is 0
    if (!($plugindefault & MESSAGE_PERMITTED_MASK)) {
        $plugindefault = $default;
    }

    $locked = false;
    $enabled = false;

    $permitted = $plugindefault & MESSAGE_PERMITTED_MASK;
    switch ($permitted) {
        case MESSAGE_FORCED:
            $locked = true;
            $enabled = true;
            break;
        case MESSAGE_DISALLOWED:
            $locked = true;
            $enabled = false;
            break;
        default:
            $locked = false;
            // It's equivalent to logged in.
            $enabled = $plugindefault & MESSAGE_DEFAULT_ENABLED == MESSAGE_DEFAULT_ENABLED;

            // MESSAGE_DEFAULT_LOGGEDOFF is deprecated but we're checking it just in case.
            $loggedoff = $plugindefault & MESSAGE_DEFAULT_LOGGEDOFF == MESSAGE_DEFAULT_LOGGEDOFF;
            $enabled = $enabled || $loggedoff;
            break;
    }

    return array($locked, $enabled);
}

/**
 * Return a list of page types
 *
 * @param string $pagetype current page type
 * @param context|null $parentcontext Block's parent context
 * @param context|null $currentcontext Current context of block
 * @return array
 */
function message_page_type_list(string $pagetype, ?context $parentcontext, ?context $currentcontext): array {
    return [
        'message-*' => get_string('page-message-x', 'message'),
    ];
}

/**
 * Get messages sent or/and received by the specified users.
 * Please note that this function return deleted messages too. Besides, only individual conversation messages
 * are returned to maintain backwards compatibility.
 *
 * @param  int      $useridto       the user id who received the message
 * @param  int      $useridfrom     the user id who sent the message. -10 or -20 for no-reply or support user
 * @param  int      $notifications  1 for retrieving notifications, 0 for messages, -1 for both
 * @param  int      $read           Either MESSAGE_GET_READ, MESSAGE_GET_UNREAD or MESSAGE_GET_READ_AND_UNREAD.
 * @param  string   $sort           the column name to order by including optionally direction
 * @param  int      $limitfrom      limit from
 * @param  int      $limitnum       limit num
 * @return external_description
 * @since  2.8
 */
function message_get_messages($useridto, $useridfrom = 0, $notifications = -1, $read = MESSAGE_GET_READ,
                                $sort = 'mr.timecreated DESC', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    if (is_bool($read)) {
        // Backwards compatibility, this parameter was a bool before 4.0.
        $read = (int) $read;
    }

    // If the 'useridto' value is empty then we are going to retrieve messages sent by the useridfrom to any user.
    $userfieldsapi = \core_user\fields::for_name();
    if (empty($useridto)) {
        $userfields = $userfieldsapi->get_sql('u', false, 'userto', '', false)->selects;
        $messageuseridtosql = 'u.id as useridto';
    } else {
        $userfields = $userfieldsapi->get_sql('u', false, 'userfrom', '', false)->selects;
        $messageuseridtosql = "$useridto as useridto";
    }

    // Create the SQL we will be using.
    $messagesql = "SELECT mr.*, $userfields, 0 as notification, '' as contexturl, '' as contexturlname,
                          mua.timecreated as timeusertodeleted, mua2.timecreated as timeread,
                          mua3.timecreated as timeuserfromdeleted, $messageuseridtosql
                     FROM {messages} mr
               INNER JOIN {message_conversations} mc
                       ON mc.id = mr.conversationid
               INNER JOIN {message_conversation_members} mcm
                       ON mcm.conversationid = mc.id ";

    $notificationsql = "SELECT mr.*, $userfields, 1 as notification
                          FROM {notifications} mr ";

    $messagejoinsql = "LEFT JOIN {message_user_actions} mua
                              ON (mua.messageid = mr.id AND mua.userid = mcm.userid AND mua.action = ?)
                       LEFT JOIN {message_user_actions} mua2
                              ON (mua2.messageid = mr.id AND mua2.userid = mcm.userid AND mua2.action = ?)
                       LEFT JOIN {message_user_actions} mua3
                              ON (mua3.messageid = mr.id AND mua3.userid = mr.useridfrom AND mua3.action = ?)";
    $messagejoinparams = [\core_message\api::MESSAGE_ACTION_DELETED, \core_message\api::MESSAGE_ACTION_READ,
        \core_message\api::MESSAGE_ACTION_DELETED];
    $notificationsparams = [];

    // If the 'useridto' value is empty then we are going to retrieve messages sent by the useridfrom to any user.
    if (empty($useridto)) {
        // Create the messaging query and params.
        $messagesql .= "INNER JOIN {user} u
                                ON u.id = mcm.userid
                                $messagejoinsql
                             WHERE mr.useridfrom = ?
                               AND mr.useridfrom != mcm.userid
                               AND u.deleted = 0
                               AND mc.type = ? ";
        $messageparams = array_merge($messagejoinparams, [$useridfrom, \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL]);

        // Create the notifications query and params.
        $notificationsql .= "INNER JOIN {user} u
                                     ON u.id = mr.useridto
                                  WHERE mr.useridfrom = ?
                                    AND u.deleted = 0 ";
        $notificationsparams[] = $useridfrom;
    } else {
        // Create the messaging query and params.
        // Left join because useridfrom may be -10 or -20 (no-reply and support users).
        $messagesql .= "LEFT JOIN {user} u
                               ON u.id = mr.useridfrom
                               $messagejoinsql
                            WHERE mcm.userid = ?
                              AND mr.useridfrom != mcm.userid
                              AND u.deleted = 0
                              AND mc.type = ? ";
        $messageparams = array_merge($messagejoinparams, [$useridto, \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL]);

        // If we're dealing with messages only and both useridto and useridfrom are set,
        // try to get a conversation between the users. Break early if we can't find one.
        if (!empty($useridfrom) && $notifications == 0) {
            $messagesql .= " AND mr.useridfrom = ? ";
            $messageparams[] = $useridfrom;

            // There should be an individual conversation between the users. If not, we can return early.
            $conversationid = \core_message\api::get_conversation_between_users([$useridto, $useridfrom]);
            if (empty($conversationid)) {
                return [];
            }
            $messagesql .= " AND mc.id = ? ";
            $messageparams[] = $conversationid;
        }

        // Create the notifications query and params.
        // Left join because useridfrom may be -10 or -20 (no-reply and support users).
        $notificationsql .= "LEFT JOIN {user} u
                                    ON (u.id = mr.useridfrom AND u.deleted = 0)
                                 WHERE mr.useridto = ? ";
        $notificationsparams[] = $useridto;
        if (!empty($useridfrom)) {
            $notificationsql .= " AND mr.useridfrom = ? ";
            $notificationsparams[] = $useridfrom;
        }
    }
    if ($read === MESSAGE_GET_READ) {
        $notificationsql .= "AND mr.timeread IS NOT NULL ";
    } else if ($read === MESSAGE_GET_UNREAD) {
        $notificationsql .= "AND mr.timeread IS NULL ";
    }
    $messagesql .= "ORDER BY $sort";
    $notificationsql .= "ORDER BY $sort";

    // Handle messages if needed.
    if ($notifications === -1 || $notifications === 0) {
        $messages = $DB->get_records_sql($messagesql, $messageparams, $limitfrom, $limitnum);
        if ($read !== MESSAGE_GET_READ_AND_UNREAD) {
            // Get rid of the messages that have either been read or not read depending on the value of $read.
            $messages = array_filter($messages, function ($message) use ($read) {
                if ($read === MESSAGE_GET_READ) {
                    return !is_null($message->timeread);
                }

                return is_null($message->timeread);
            });
        }
    }

    // All.
    if ($notifications === -1) {
        return array_merge($messages, $DB->get_records_sql($notificationsql, $notificationsparams, $limitfrom, $limitnum));
    } else if ($notifications === 1) { // Just notifications.
        return $DB->get_records_sql($notificationsql, $notificationsparams, $limitfrom, $limitnum);
    }

    // Just messages.
    return $messages;
}

/**
 * Handles displaying processor settings in a fragment.
 *
 * @param array $args
 * @return bool|string
 * @throws moodle_exception
 */
function message_output_fragment_processor_settings($args = []) {
    global $PAGE;

    if (!isset($args['type'])) {
        throw new moodle_exception('Must provide a processor type');
    }

    if (!isset($args['userid'])) {
        throw new moodle_exception('Must provide a userid');
    }

    $type = $args['type'];
    $userid = $args['userid'];

    $user = core_user::get_user($userid, '*', MUST_EXIST);
    $processor = get_message_processor($type);
    $providers = message_get_providers_for_user($userid);
    $processorwrapper = new stdClass();
    $processorwrapper->object = $processor;
    $preferences = \core_message\api::get_all_message_preferences([$processorwrapper], $providers, $user);

    $processoroutput = new \core_message\output\preferences\processor($processor, $preferences, $user, $type);
    $renderer = $PAGE->get_renderer('core', 'message');

    return $renderer->render_from_template('core_message/preferences_processor', $processoroutput->export_for_template($renderer));
}

/**
 * Checks if current user is allowed to edit messaging preferences of another user
 *
 * @param stdClass $user user whose preferences we are updating
 * @return bool
 */
function core_message_can_edit_message_profile($user) {
    global $USER;
    if ($user->id == $USER->id) {
        return has_capability('moodle/user:editownmessageprofile', context_system::instance());
    } else {
        $personalcontext = context_user::instance($user->id);
        if (!has_capability('moodle/user:editmessageprofile', $personalcontext)) {
            return false;
        }
        if (isguestuser($user)) {
            return false;
        }
        // No editing of admins by non-admins.
        if (is_siteadmin($user) and !is_siteadmin($USER)) {
            return false;
        }
        return true;
    }
}

/**
 * Implements callback user_preferences, lists preferences that users are allowed to update directly
 *
 * Used in {@see core_user::fill_preferences_cache()}, see also {@see useredit_update_user_preference()}
 *
 * @return array
 */
function core_message_user_preferences() {
    $preferences = [];
    $preferences['message_blocknoncontacts'] = array(
        'type' => PARAM_INT,
        'null' => NULL_NOT_ALLOWED,
        'default' => 0,
        'choices' => array(
            \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS,
            \core_message\api::MESSAGE_PRIVACY_COURSEMEMBER,
            \core_message\api::MESSAGE_PRIVACY_SITE
        ),
        'cleancallback' => function ($value) {
            global $CFG;

            // When site-wide messaging between users is disabled, MESSAGE_PRIVACY_SITE should be converted.
            if (empty($CFG->messagingallusers) && $value === \core_message\api::MESSAGE_PRIVACY_SITE) {
                return \core_message\api::MESSAGE_PRIVACY_COURSEMEMBER;
            }
            return $value;
        }
    );
    $preferences['message_entertosend'] = array(
        'type' => PARAM_BOOL,
        'null' => NULL_NOT_ALLOWED,
        'default' => false
    );
    $preferences['/^message_provider_([\w\d_]*)_enabled$/'] = array('isregex' => true, 'type' => PARAM_NOTAGS,
        'null' => NULL_NOT_ALLOWED, 'default' => 'none',
        'permissioncallback' => function ($user, $preferencename) {
            global $CFG;
            require_once($CFG->libdir.'/messagelib.php');
            if (core_message_can_edit_message_profile($user) &&
                    preg_match('/^message_provider_([\w\d_]*)_enabled$/', $preferencename, $matches)) {
                $providers = message_get_providers_for_user($user->id);
                foreach ($providers as $provider) {
                    if ($matches[1] === $provider->component . '_' . $provider->name) {
                       return true;
                    }
                }
            }
            return false;
        },
        'cleancallback' => function ($value, $preferencename) {
            if ($value === 'none' || empty($value)) {
                return 'none';
            }
            $parts = explode('/,/', $value);
            $processors = array_keys(get_message_processors());
            array_filter($parts, function($v) use ($processors) {return in_array($v, $processors);});
            return $parts ? join(',', $parts) : 'none';
        });
    return $preferences;
}

/**
 * Render the message drawer to be included in the top of the body of each page.
 *
 * @return string HTML
 */
function core_message_standard_after_main_region_html() {
    return \core_message\helper::render_messaging_widget(true, null, null);
}
