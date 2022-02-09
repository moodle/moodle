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
 * Helper functions and callbacks.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/comment/lib.php');

/**
 * Validate comment parameter before perform other comments actions.
 *
 * @param stdClass $commentparam
 * {
 * context     => context the context object
 * courseid    => int course id
 * cm          => stdClass course module object
 * commentarea => string comment area
 * itemid      => int itemid
 * }
 * @return boolean
 */
function qbank_comment_comment_validate($commentparam): bool {
    if ($commentparam->commentarea != 'question' && $commentparam->component != 'qbank_comment') {
        throw new comment_exception('invalidcommentarea');
    }
    return true;
}

/**
 * Running additional permission check on plugins.
 *
 * @param stdClass $args
 * @return array
 */
function qbank_comment_comment_permissions($args): array {
    return ['post' => true, 'view' => true];
}

/**
 * Validate comment data before displaying comments.
 *
 * @param array $comments
 * @param stdClass $args
 * @return array $comments
 */
function qbank_comment_comment_display($comments, $args): array {
    if ($args->commentarea != 'question' && $args->component != 'qbank_comment') {
        throw new comment_exception('core_question');
    }
    return $comments;
}

/**
 * Comment content for callbacks.
 *
 * @param question_definition $question
 * @param int $courseid
 * @return string
 */
function qbank_comment_preview_display($question, $courseid): string {
    global $CFG, $PAGE;
    if (question_has_capability_on($question, 'comment') && $CFG->usecomments
            && core\plugininfo\qbank::is_plugin_enabled('qbank_comment')) {
        \comment::init($PAGE);
        $args = new \stdClass;
        $args->contextid = 1; // Static data to bypass comment sql as context is not needed.
        $args->courseid  = $courseid;
        $args->area      = 'question';
        $args->itemid    = $question->id;
        $args->component = 'qbank_comment';
        $args->notoggle  = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $args->linktext = get_string('commentheader', 'qbank_comment');
        $comment = new \comment($args);
        $comment->set_view_permission(true);
        $comment->set_fullwidth();
        return $comment->output();
    } else {
        return '';
    }
}

/**
 * Question comment fragment callback.
 *
 * @param array $args
 * @return string rendered output
 */
function qbank_comment_output_fragment_question_comment($args): string {
    global $USER, $PAGE, $CFG, $DB;
    $displaydata = [];
    require_once($CFG->dirroot . '/question/engine/bank.php');
    $question = question_bank::load_question($args['questionid']);
    $quba = question_engine::make_questions_usage_by_activity(
            'core_question_preview', context_user::instance($USER->id));

    // Just in case of any regression, it should not break the modal, just show the comments.
    if (class_exists('\\qbank_previewquestion\\question_preview_options')) {
        $options = new \qbank_previewquestion\question_preview_options($question);
        $quba->set_preferred_behaviour($options->behaviour);
        $slot = $quba->add_question($question, $options->maxmark);
        $quba->start_question($slot, $options->variant);
        $transaction = $DB->start_delegated_transaction();
        question_engine::save_questions_usage_by_activity($quba);
        $transaction->allow_commit();
        $displaydata['question'] = $quba->render_question($slot, $options, '1');
    }
    $displaydata['comment'] = qbank_comment_preview_display($question, $args['courseid']);
    $displaydata['commenstdisabled'] = false;
    if (empty($displaydata['comment']) && !$CFG->usecomments) {
        $displaydata['commenstdisabled'] = true;
    }

    return $PAGE->get_renderer('qbank_comment')->render_comment_fragment($displaydata);
}
