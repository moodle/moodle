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
 * Competency lib.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_competency\api;
use core_competency\plan;
use core_competency\url;
use core_competency\user_competency;
use core_competency\user_evidence;

/**
 * Hook when a comment is added.
 *
 * @param  stdClass $comment The comment.
 * @param  stdClass $params The parameters.
 * @return array
 */
function core_competency_comment_add($comment, $params) {
    global $USER;

    if (!get_config('core_competency', 'enabled')) {
        return;
    }

    if ($params->commentarea == 'user_competency') {
        $uc = new user_competency($params->itemid);

        // Message both the user and the reviewer, except when they are the author of the message.
        $recipients = array($uc->get_userid());
        if ($uc->get_reviewerid()) {
            $recipients[] = $uc->get_reviewerid();
        }
        $recipients = array_diff($recipients, array($comment->userid));
        if (empty($recipients)) {
            return;
        }

        // Get the sender.
        $user = $USER;
        if ($USER->id != $comment->userid) {
            $user = core_user::get_user($comment->userid);
        }
        $fullname = fullname($user);

        // Get the competency.
        $competency = $uc->get_competency();
        $competencyname = format_string($competency->get_shortname(), true, array('context' => $competency->get_context()));

        // We want to send a message for one plan, trying to find an active one first, or the last modified one.
        $plan = null;
        $plans = $uc->get_plans();
        foreach ($plans as $candidate) {
            if ($candidate->get_status() == plan::STATUS_ACTIVE) {
                $plan = $candidate;
                break;

            } else if (!empty($plan) && $plan->get_timemodified() < $candidate->get_timemodified()) {
                $plan = $candidate;

            } else if (empty($plan)) {
                $plan = $candidate;
            }
        }

        // Urls.
        // TODO MDL-52749 Replace the link to the plan with the user competency page.
        if (empty($plan)) {
            $urlname = get_string('userplans', 'core_competency');
            $url = url::plans($uc->get_userid());
        } else {
            $urlname = $competencyname;
            $url = url::user_competency_in_plan($uc->get_userid(), $uc->get_competencyid(), $plan->get_id());
        }

        // Construct the message content.
        $fullmessagehtml = get_string('usercommentedonacompetencyhtml', 'core_competency', array(
            'fullname' => $fullname,
            'competency' => $competencyname,
            'comment' => format_text($comment->content, $comment->format, array('context' => $params->context->id)),
            'url' => $url->out(true),
            'urlname' => $urlname,
        ));
        if ($comment->format == FORMAT_PLAIN || $comment->format == FORMAT_MOODLE) {
            $format = FORMAT_MOODLE;
            $fullmessage = get_string('usercommentedonacompetency', 'core_competency', array(
                'fullname' => $fullname,
                'competency' => $competencyname,
                'comment' => $comment->content,
                'url' => $url->out(false),
            ));
        } else {
            $format = FORMAT_HTML;
            $fullmessage = $fullmessagehtml;
        }

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'competencyusercompcomment';
        $message->notification = 1;
        $message->userfrom = core_user::get_noreply_user();
        $message->subject = get_string('usercommentedonacompetencysubject', 'core_competency', $fullname);
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = $format;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = get_string('usercommentedonacompetencysmall', 'core_competency', array(
            'fullname' => $fullname,
            'competency' => $competencyname,
        ));
        $message->contexturl = $url->out(false);
        $message->contexturlname = $urlname;

        // Message each recipient.
        foreach ($recipients as $recipient) {
            $msgcopy = clone($message);
            $msgcopy->userto = $recipient;
            message_send($msgcopy);
        }

    } else if ($params->commentarea == 'plan') {
        $plan = new plan($params->itemid);

        // Message both the user and the reviewer, except when they are the author of the message.
        $recipients = array($plan->get_userid());
        if ($plan->get_reviewerid()) {
            $recipients[] = $plan->get_reviewerid();
        }
        $recipients = array_diff($recipients, array($comment->userid));
        if (empty($recipients)) {
            return;
        }

        // Get the sender.
        $user = $USER;
        if ($USER->id != $comment->userid) {
            $user = core_user::get_user($comment->userid);
        }

        $fullname = fullname($user);
        $planname = format_string($plan->get_name(), true, array('context' => $plan->get_context()));
        $urlname = $planname;
        $url = url::plan($plan->get_id());

        // Construct the message content.
        $fullmessagehtml = get_string('usercommentedonaplanhtml', 'core_competency', array(
            'fullname' => $fullname,
            'plan' => $planname,
            'comment' => format_text($comment->content, $comment->format, array('context' => $params->context->id)),
            'url' => $url->out(true),
            'urlname' => $urlname,
        ));
        if ($comment->format == FORMAT_PLAIN || $comment->format == FORMAT_MOODLE) {
            $format = FORMAT_MOODLE;
            $fullmessage = get_string('usercommentedonaplan', 'core_competency', array(
                'fullname' => $fullname,
                'plan' => $planname,
                'comment' => $comment->content,
                'url' => $url->out(false),
            ));
        } else {
            $format = FORMAT_HTML;
            $fullmessage = $fullmessagehtml;
        }

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'competencyplancomment';
        $message->notification = 1;
        $message->userfrom = core_user::get_noreply_user();
        $message->subject = get_string('usercommentedonaplansubject', 'core_competency', $fullname);
        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = $format;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = get_string('usercommentedonaplansmall', 'core_competency', array(
            'fullname' => $fullname,
            'plan' => $planname,
        ));
        $message->contexturl = $url->out(false);
        $message->contexturlname = $urlname;

        // Message each recipient.
        foreach ($recipients as $recipient) {
            $msgcopy = clone($message);
            $msgcopy->userto = $recipient;
            message_send($msgcopy);
        }
    }
}

/**
 * Return the permissions of for the comments.
 *
 * @param  stdClass $params The parameters.
 * @return array
 */
function core_competency_comment_permissions($params) {
    if (!get_config('core_competency', 'enabled')) {
        return array('post' => false, 'view' => false);
    }

    if ($params->commentarea == 'user_competency') {
        $uc = new user_competency($params->itemid);
        if ($uc->can_read()) {
            return array('post' => $uc->can_comment(), 'view' => $uc->can_read_comments());
        }
    } else if ($params->commentarea == 'plan') {
        $plan = new plan($params->itemid);
        if ($plan->can_read()) {
            return array('post' => $plan->can_comment(), 'view' => $plan->can_read_comments());
        }
    }

    return array('post' => false, 'view' => false);
}

/**
 * Validates comments.
 *
 * @param  stdClass $params The parameters.
 * @return bool
 */
function core_competency_comment_validate($params) {
    if (!get_config('core_competency', 'enabled')) {
        return false;
    }

    if ($params->commentarea == 'user_competency') {
        if (!user_competency::record_exists($params->itemid)) {
            return false;
        }
        return true;
    } else if ($params->commentarea == 'plan') {
        if (!plan::record_exists($params->itemid)) {
            return false;
        }
        return true;
    }
    return false;
}

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function core_competency_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;

    if (!get_config('core_competency', 'enabled')) {
        return false;
    }

    $fs = get_file_storage();
    $file = null;

    $itemid = array_shift($args);
    $filename = array_shift($args);
    $filepath = $args ? '/' .implode('/', $args) . '/' : '/';

    if ($filearea == 'userevidence' && $context->contextlevel == CONTEXT_USER) {
        if (user_evidence::can_read_user($context->instanceid)) {
            $file = $fs->get_file($context->id, 'core_competency', $filearea, $itemid, $filepath, $filename);
        }
    }

    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload);
}
