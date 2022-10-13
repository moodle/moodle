<?php

/**
 * Version details
 *
 * @package    local_message
 * @author  Albohtori
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//defined('MOODLE_INTERNAL') || die();

use local_message\manager;


function local_message_before_footer()
{
    global $USER;


    $manager = new manager();
    $messages = $manager->get_messages($USER->id);
    foreach ($messages as $message) {
        $type = \core\output\notification::NOTIFY_INFO;
        if ($message->messagetype === '0') {
            $type = \core\output\notification::NOTIFY_WARNING;
        } else if ($message->messagetype === '1') {
            $type = \core\output\notification::NOTIFY_SUCCESS;
        } else if ($message->messagetype === '2') {
            $type = \core\output\notification::NOTIFY_ERROR;
        }


        \core\notification::add($message->messagetext, $type);


        $manager->mark_message_read($message->id, $USER->id);

    }
}
