<?PHP
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
 * This file contains the moodle hooks for the submission comments plugin
 *
 * @package   assignsubmission_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 *
 * Callback method for data validation---- required method for AJAXmoodle based comment API
 *
 * @param stdClass $options
 * @return bool
 */
function assignsubmission_comments_comment_validate(stdClass $options) {
    global $USER, $CFG, $DB;

    if ($options->commentarea != 'submission_comments' &&
            $options->commentarea != 'submission_comments_upgrade') {
        throw new comment_exception('invalidcommentarea');
    }
    if (!$submission = $DB->get_record('assign_submission', array('id'=>$options->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    $context = $options->context;

    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    $assignment = new assign($context, null, null);

    if ($assignment->get_instance()->id != $submission->assignment) {
        throw new comment_exception('invalidcontext');
    }
    if (!has_capability('mod/assign:grade', $context)) {
        if (!has_capability('mod/assign:submit', $context)) {
            throw new comment_exception('nopermissiontocomment');
        } else if ($assignment->get_instance()->teamsubmission) {
            $group = $assignment->get_submission_group($USER->id);
            $groupid = 0;
            if ($group) {
                $groupid = $group->id;
            }
            if ($groupid != $submission->groupid) {
                throw new comment_exception('nopermissiontocomment');
            }
        } else if ($submission->userid != $USER->id) {
            throw new comment_exception('nopermissiontocomment');
        }
    }

    return true;
}

/**
 * Permission control method for submission plugin ---- required method for AJAXmoodle based comment API
 *
 * @param stdClass $options
 * @return array
 */
function assignsubmission_comments_comment_permissions(stdClass $options) {
    global $USER, $CFG, $DB;

    if ($options->commentarea != 'submission_comments' &&
            $options->commentarea != 'submission_comments_upgrade') {
        throw new comment_exception('invalidcommentarea');
    }
    if (!$submission = $DB->get_record('assign_submission', array('id'=>$options->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    $context = $options->context;

    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    $assignment = new assign($context, null, null);

    if ($assignment->get_instance()->id != $submission->assignment) {
        throw new comment_exception('invalidcontext');
    }
    if (!has_capability('mod/assign:grade', $context)) {
        if (!has_capability('mod/assign:submit', $context)) {
            return array('post' => false, 'view' => false);
        } else if ($assignment->get_instance()->teamsubmission) {
            $group = $assignment->get_submission_group($USER->id);
            $groupid = 0;
            if ($group) {
                $groupid = $group->id;
            }
            if ($groupid != $submission->groupid) {
                return array('post' => false, 'view' => false);
            }
        } else if ($submission->userid != $USER->id) {
            return array('post' => false, 'view' => false);
        }
    }

    return array('post' => true, 'view' => true);
}

/**
 * Callback to force the userid for all comments to be the userid of the submission and NOT the global $USER->id. This
 * is required by the upgrade code. Note the comment area is used to identify upgrades.
 *
 * @param stdClass $comment
 * @param stdClass $param
 */
function assignsubmission_comments_comment_add(stdClass $comment, stdClass $param) {

    global $DB;
    if ($comment->commentarea == 'submission_comments_upgrade') {
        $submissionid = $comment->itemid;
        $submission = $DB->get_record('assign_submission', array('id' => $submissionid));

        $comment->userid = $submission->userid;
        $comment->commentarea = 'submission_comments';
    }
}

