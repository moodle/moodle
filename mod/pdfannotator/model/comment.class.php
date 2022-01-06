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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @authors   Rabea de Groot, Anna Heynkes and Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/pdfannotator/lib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once('model/annotation.class.php');
require_once('model/pdfannotator.php');

class pdfannotator_comment {

    /**
     * This method inserts a new record into mdl_pdfannotator_comments and returns its id
     *
     * @global type $DB
     * @global type $USER
     * @param type $documentid specifies the pdf
     * @param type $annotationid specifies the annotation (usually a highlight) to be commented
     * @param type $content the text or comment itself
     */
    public static function create($documentid, $annotationid, $content, $visibility, $isquestion, $cm, $context) {

        global $DB, $USER, $CFG;

        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        // Create a new record in 'pdfannotator_comments'.
        $datarecord = new stdClass();
        $datarecord->pdfannotatorid = $documentid;
        $datarecord->annotationid = $annotationid;
        $datarecord->userid = $USER->id;
        $datarecord->content = $content;
        $datarecord->timecreated = time(); // Moodle method: DateTime::getTimestamp();
        $datarecord->timemodified = $datarecord->timecreated;
        $datarecord->visibility = $visibility;
        $datarecord->isquestion = $isquestion;
        $anno = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid]);
        if ($anno) {
            // Create a new record in the table named 'comments' and return its id, which is created by autoincrement.
            $commentuuid = $DB->insert_record('pdfannotator_comments', $datarecord, $returnid = true);

            $datarecord->uuid = $commentuuid;
            self::set_username($datarecord);

            $datarecord->content = format_text($datarecord->content, $format = FORMAT_MOODLE, $options = ['para' => false]);
            $datarecord->timecreated = pdfannotator_optional_timeago($datarecord->timecreated);
            $datarecord->timemodified = pdfannotator_optional_timeago($datarecord->timemodified);
            $datarecord->usevotes = pdfannotator_instance::use_votes($documentid);
            $datarecord->votes = 0;
            $datarecord->ishidden = false;
            $datarecord->isdeleted = false;
            $datarecord->solved = false;

            $anonymous = $visibility == 'anonymous' ? true : false;
            $modulename = format_string($cm->name, true);
            if ($isquestion == 0) {
                // Notify subscribed users.
                $comment = new stdClass();
                $comment->answeruser = $visibility == 'public' ? fullname($USER) : 'Anonymous';
                $comment->content = $content;
                $comment->question = pdfannotator_annotation::get_question($annotationid);
                $page = pdfannotator_annotation::get_pageid($annotationid);
                $comment->urltoanswer = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' .
                        $cm->id . '&page=' . $page . '&annoid=' . $annotationid . '&commid=' . $commentuuid;

                $messagetext = new stdClass();
                $module = get_string('modulename', 'pdfannotator');
                $messagetext->text = pdfannotator_format_notification_message_text($course, $cm, $context, $module, $modulename, $comment, 'newanswer');
                $messagetext->url = $comment->urltoanswer;
                $recipients = self::get_subscribed_users($annotationid);
                foreach ($recipients as $recipient) {
                    if ($recipient != $USER->id) {
                        $messagetext->html = pdfannotator_format_notification_message_html($course, $cm, $context, $module, $modulename, $comment, 'newanswer', $recipient);
                        $messageid = pdfannotator_notify_manager($recipient, $course, $cm, 'newanswer', $messagetext, $anonymous);
                    }
                }
            } else if ($visibility != 'private') {
                self::insert_subscription($annotationid, $context);

                // Notify all users, that there is a new question.
                $recipients = get_enrolled_users($context, 'mod/pdfannotator:recievenewquestionnotifications');

                $question = new stdClass();
                $question->answeruser = $visibility == 'public' ? fullname($USER) : 'Anonymous';
                $question->content = $content;

                $page = $DB->get_field('pdfannotator_annotations', 'page', array('id' => $annotationid), $strictness = MUST_EXIST);
                $question->urltoanswer = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '&page=' . $page . '&annoid=' . $annotationid . '&commid=' . $commentuuid;

                $messagetext = new stdClass();
                $messagetext->text = pdfannotator_format_notification_message_text($course, $cm, $context, get_string('modulename', 'pdfannotator'), $modulename, $question, 'newquestion');
                $messagetext->url = $question->urltoanswer;
                foreach ($recipients as $recipient) {
                    if (!pdfannotator_can_see_comment($datarecord, $context) ){
                        continue;
                    }
                    if ($recipient->id == $USER->id) {
                        continue;
                    }
                    $messagetext->html = pdfannotator_format_notification_message_html($course, $cm, $context, get_string('modulename', 'pdfannotator'), $modulename, $question, 'newquestion', $recipient->id);
                    $messageid = pdfannotator_notify_manager($recipient, $course, $cm, 'newquestion', $messagetext, $anonymous);
                }

            }

            return $datarecord;
        } else {
            return false;
        }
    }

    /**
     * This method returns an array of all comment objects belonging to the specified annotation.
     *
     * @global type $DB
     * @param type $documentid
     * @param type $highlightid
     * @param $context
     * @return \stdClass
     */
    public static function read($documentid, $annotationid, $context) {

        global $DB, $USER;

        // Get the ids and text content of all comments attached to this annotation/highlight.
        $sql = "SELECT c.id, c.content, c.userid, c.visibility, c.isquestion, c.isdeleted, c.ishidden, c.timecreated, c.timemodified, c.modifiedby, c.solved, c.annotationid, SUM(vote) AS votes "
                . "FROM {pdfannotator_comments} c LEFT JOIN {pdfannotator_votes} v"
                . " ON c.id=v.commentid WHERE annotationid = ?"
                . " GROUP BY c.id, c.content, c.userid, c.visibility, c.isquestion, c.isdeleted, c.ishidden, c.timecreated, c.timemodified, c.modifiedby, c.solved, c.annotationid"
                . " ORDER BY c.timecreated";
        $a = array();
        $a[] = $annotationid;
        $comments = $DB->get_records_sql($sql, $a); // Records taken from table 'comments' as an array of objects.
        $usevotes = pdfannotator_instance::use_votes($documentid);

        $annotation = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid], $fields = 'timecreated, timemodified, modifiedby', $strictness = MUST_EXIST);

        $result = array();
        foreach ($comments as $data) {
            $comment = new stdClass();

            $comment->userid = $data->userid; // Author of comment.
            $comment->visibility = $data->visibility;
            $comment->isquestion = $data->isquestion;
            $comment->annotationid = $annotationid;
            $comment->annotation = $annotationid;
            if ( !pdfannotator_can_see_comment($comment, $context)) {
                continue;
            }

            $comment->timecreated = pdfannotator_optional_timeago($data->timecreated);
            // If the comment was edited.
            if ($data->timecreated != $data->timemodified) {
                $comment->edited = true;
                $comment->timemodified = pdfannotator_optional_timeago($data->timemodified);
                $comment->modifiedby = $data->modifiedby;
            }
            // If the annotation was moved (after the last edit).
            if ($comment->isquestion && !empty($annotation->timemodified) && ($annotation->timemodified > $data->timemodified)) {
                $comment->edited = true;
                $comment->timemodified = pdfannotator_optional_timeago($annotation->timemodified);
                $comment->modifiedby = $annotation->modifiedby;
            }

            $comment->isdeleted = $data->isdeleted;
            $comment->uuid = $data->id;

            if ($data->ishidden) {
                $comment->ishidden = 1;
            } else {
                $comment->ishidden = 0;
            }

            if ($data->isdeleted) {
                $comment->content = get_string('deletedComment', 'pdfannotator');
            } else {
                $comment->content = $data->content;
                $comment->content = format_text($data->content, $format = FORMAT_MOODLE, $options = ['para' => false]);
            }

            self::set_username($comment);
            $comment->solved = $data->solved;
            $comment->votes = $data->votes;
            $comment->isvoted = self::is_voted($data->id);
            $comment->usevotes = $usevotes;
            $comment->issubscribed = self::is_subscribed($annotationid);
            // Add the comment to the list.
            $result[] = $comment;
        }
        return $result;
    }

    /**
     * Function sets the username to be passed to JavaScript according to comment visibility
     *
     * @param type $comment
     */
    public static function set_username($comment) {
        global $USER;
        switch ($comment->visibility) {
            case 'public':
            case 'private':
            case 'protected':
                if ($comment->userid === $USER->id) {
                    $comment->username = get_string('me', 'pdfannotator');
                } else {
                    $comment->username = pdfannotator_get_username($comment->userid);
                }
                break;
            case 'anonymous':
                $comment->username = get_string('anonymous', 'pdfannotator');
                break;
            case 'deleted':
                $comment->username = '';
                break;
            default:
                $comment->username = '';
        }
    }

    /**
     * Function serves to hide a comment from participants' view while keeping it visibile for managers/teachers/etc.
     *
     * @global type $DB
     * @global type $USER
     * @return type
     */
    public static function hide_comment($commentid, $cmid) {

        global $DB, $USER;
        $success = 0;

        // 1. Is there a comment to hide? Retrieve comment from db (return false if it doesn't exist).
        $comment = $DB->get_record('pdfannotator_comments', array('id' => $commentid), '*', $strictness = IGNORE_MISSING);
        if (!$comment) {
            echo json_encode(['status' => 'error']);
            return;
        }
        // 2. If so, is the user allowed to hide comments?
        $context = context_module::instance($cmid);
        if (!has_capability('mod/pdfannotator:hidecomments', $context)) {
            echo json_encode(['status' => 'error']);
            return;
        }

        // To delete or not to delete, that is the question.
        $annotationid = $comment->annotationid;

        $select = "annotationid = ? AND timecreated > ? AND isdeleted = ?";
        $wasanswered = $DB->record_exists_select('pdfannotator_comments', $select, [$annotationid, $comment->timecreated, 0]);

        $tobedeletedaswell = [];
        $hideannotation = 0;

        if (!$wasanswered) {
            // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely.
            $sql = "SELECT id, isdeleted, isquestion from {pdfannotator_comments} "
                    . "WHERE annotationid = ? AND timecreated < ? ORDER BY id DESC";
            $params = array($annotationid, $comment->timecreated);
            $predecessors = $DB->get_records_sql($sql, $params);

            foreach ($predecessors as $predecessor) {
                if ($predecessor->isdeleted != 0) {
                    $workingfine = $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                    if ($workingfine != 0) {
                        $tobedeletedaswell[] = $predecessor->id;
                        if ($predecessor->isquestion) {
                                $hideannotation = 1; // $annotationid;
                        }
                    }
                } else {
                    break;
                }
            }

        }

        $success = $DB->update_record('pdfannotator_comments', array("id" => $commentid, "ishidden" => 1), $bulk = false);

        if ($success == 1) {
            return ['status' => 'success', 'hideannotation' => $hideannotation, 'wasanswered' => $wasanswered, 'followups' => $tobedeletedaswell];
        } else {
            return ['status' => 'error'];
        }

    }
    /**
     * Function serves to redisplay a comment that had been hidden from normal participants' view.
     *
     * @param int $commentid
     * @param int $cmid
     * @return array
     */
    public static function redisplay_comment($commentid, $cmid) {

        global $DB;

        $success = $DB->update_record('pdfannotator_comments', array("id" => $commentid, "ishidden" => 0), $bulk = false);

        if ($success == 1) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error'];
        }

    }
    /**
     * Deletes a comment.
     * If the comment is answered, it will be displayed as deleted comment.
     */
    public static function delete_comment($commentid, $cmid) {
        global $DB, $USER;
        $success = 0;
        // Retrieve comment from db (return false if it doesn't exist).
        $comment = $DB->get_record('pdfannotator_comments', array('id' => $commentid), '*', $strictness = IGNORE_MISSING);

        if (!$comment) {
            echo json_encode(['status' => 'error']);
            return;
        }
        $context = context_module::instance($cmid);
        // Check capabilities.
        if (!has_capability('mod/pdfannotator:deleteany', $context) &&
                !(has_capability('mod/pdfannotator:deleteown', $context) &&($comment->userid == $USER->id))) {
            echo json_encode(['status' => 'error']);
            return;
        }

        // To delete or not to delete, that is the question.
        $annotationid = $comment->annotationid;

        $select = "annotationid = ? AND timecreated > ? AND isdeleted = ?";
        $wasanswered = $DB->record_exists_select('pdfannotator_comments', $select, [$annotationid, $comment->timecreated, 0]);

        $tobedeletedaswell = [];
        $deleteannotation = 0;

        if ($wasanswered) { // If the comment was answered, mark it as deleted for a special display.
            $params = array("id" => $commentid, "isdeleted" => 1);
            $success = $DB->update_record('pdfannotator_comments', $params, $bulk = false);
        } else { // If not, just delete it.
            // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely.
            $sql = "SELECT id, isdeleted, isquestion from {pdfannotator_comments} "
                    . "WHERE annotationid = ? AND timecreated < ? ORDER BY id DESC";
            $params = array($annotationid, $comment->timecreated);
            $predecessors = $DB->get_records_sql($sql, $params);

            foreach ($predecessors as $predecessor) {
                if ($predecessor->isdeleted != 0) {
                    $workingfine = $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                    if ($workingfine != 0) {
                        $tobedeletedaswell[] = $predecessor->id;
                        if ($predecessor->isquestion) {
                            pdfannotator_annotation::delete($annotationid, $cmid, true);
                            $deleteannotation = $annotationid;
                        }
                    }
                } else {
                    break;
                }
            }

            // If the comment is a question and has no answers, delete the annotion.
            if ($comment->isquestion) {
                pdfannotator_annotation::delete($annotationid, $cmid, true);
                $deleteannotation = $annotationid;
            }

            $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid));
        }
        // Delete votes to the comment.
        $DB->delete_records('pdfannotator_votes', array("commentid" => $commentid));

        if ($success == 1) {
            return ['status' => 'success', 'wasanswered' => $wasanswered, 'followups' => $tobedeletedaswell,
                'deleteannotation' => $deleteannotation, 'isquestion' => $comment->isquestion];
        } else {
            return ['status' => 'error'];
        }
    }

    public static function update($commentid, $content, $editanypost) {
        global $DB, $USER;
        $comment = $DB->get_record('pdfannotator_comments', ['id' => $commentid]);
        if ($comment && ( $comment->userid == $USER->id || $editanypost) && $comment->isdeleted == 0) {
            $comment->content = $content;
            $comment->timemodified = time();
            $comment->modifiedby = $USER->id;
            $time = pdfannotator_optional_timeago($comment->timemodified);
            $success = $DB->update_record('pdfannotator_comments', $comment);
        } else {
            $success = false;
        }

        if ($success) {
            $content = format_text($content, $format = FORMAT_MOODLE, $options = ['para' => false]);
            $result = array('status' => 'success', 'timemodified' => $time, 'newContent' => $content);
            if ($comment->userid != $USER->id) {
                $result['modifiedby'] = pdfannotator_get_username($USER->id);
            }
            return $result;
        } else {
            return ['status' => 'error'];
        }
    }

    /**
     * Inserts a vote into the db.
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return boolean
     */
    public static function insert_vote($documentid, $commentid) {

        global $DB;
        global $USER;

        // Check if voting is allowed in this pdfannotator and if comment was already voted.
        if (!(pdfannotator_instance::use_votes($documentid)) || (self::is_voted($commentid))) {
            return false;
        }

        // Check comment's existence.
        if (!$DB->record_exists('pdfannotator_comments', array('id' => $commentid))) {
            return false;
        }

        // Create a new record, insert it in the table named 'votes' and return its id, which is created by autoincrement.
        $datarecord = new stdClass();
        $datarecord->commentid = $commentid;
        $datarecord->userid = $USER->id;

        $DB->insert_record('pdfannotator_votes', $datarecord, $returnid = true);
        $countvotes = self::get_number_of_votes($commentid);
        return $countvotes;
    }

    /**
     * Inserts a subscription into the DB.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return boolean
     */
    public static function insert_subscription($annotationid, $context) {
        global $DB, $USER;

        // Check if subscription already exists.
        if ($DB->record_exists('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id))) {
            return false;
        }
        
        $comment = $DB->get_record('pdfannotator_comments', array('annotationid' => $annotationid, 'isquestion' => '1'));
        if (!pdfannotator_can_see_comment($comment, $context)) {
            return false;
        }

        $datarecord = new stdClass();
        $datarecord->annotationid = $annotationid;
        $datarecord->userid = $USER->id;


        $subscriptionid = $DB->insert_record('pdfannotator_subscriptions', $datarecord, $returnid = true);
        return $subscriptionid;
    }

    /**
     * Deletes a subscription.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return string
     */
    public static function delete_subscription($annotationid) {
        global $DB, $USER;
        $count = $DB->count_records('pdfannotator_comments', array('annotationid' => $annotationid, 'isquestion' => 0));
        $success = $DB->delete_records('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id));
        if (!empty($success)) {
            return $count;
        }
        return false;
    }

    /**
     * Marks a comment as solved. A question will be closed (or opened) and a answer will be marked as correct.
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return boolean
     */
    public static function mark_solved($commentid, $context) {
        global $DB, $USER;
        $comment = $DB->get_record('pdfannotator_comments', ['id' => $commentid]);
        if ($comment->isquestion) {
            // If comment is question, check if the user is allowed to close all questions or if he is the questions author.
            $closeanyquestion = has_capability('mod/pdfannotator:closeanyquestion', $context);
            $closeownquestion = has_capability('mod/pdfannotator:closequestion', $context);
            if (!$closeanyquestion && !($closeownquestion && ($comment->userid === $USER->id ))) {
                return false;
            }
        } else {
            // If the comment is an answer.
            if (!has_capability('mod/pdfannotator:markcorrectanswer', $context)) {
                return false;
            }
        }
        if ($comment->solved != 0) {
            $comment->solved = 0;
        } else {
            $comment->solved = $USER->id;
        }
        $success = $DB->update_record('pdfannotator_comments', $comment);
        return $success;
    }

    public static function is_solved($commentid) {
        global $DB;
        return $DB->record_exists('pdfannotator_comments', ['commentid' => $commentid, 'solved' => 1]);
    }

    /**
     * Returns if the user already voted a comment.
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return type
     */
    public static function is_voted($commentid) {
        global $DB, $USER;
        return $DB->record_exists('pdfannotator_votes', array('commentid' => $commentid, 'userid' => $USER->id));
    }

    /**
     * Returns the number of votes a comment got.
     * @global type $DB
     * @param type $commentid
     * @return type
     */
    public static function get_number_of_votes($commentid) {
        global $DB;
        return $DB->count_records('pdfannotator_votes', array('commentid' => $commentid));
    }

    /**
     * Returns if the user is subscribed to a question.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return type
     */
    public static function is_subscribed($annotationid) {
        global $DB, $USER;
        return $DB->record_exists('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id));
    }

    /**
     * Returns all subscribed users to a question.
     * @global type $DB
     * @param type $annotationid
     * @return arry of userids as strings
     */
    public static function get_subscribed_users($annotationid) {
        global $DB;
        $select = 'annotationid = ?';
        return $DB->get_fieldset_select('pdfannotator_subscriptions', 'userid', $select, array($annotationid));
    }

    public static function get_questions($documentid, $pagenumber, $context) {
        global $DB, $USER;
        $displayhidden = has_capability('mod/pdfannotator:seehiddencomments', $context);
        // Get all questions of a page with a subselect, where all ids of annotations of one page are selected.
        $sql = "SELECT c.* FROM {pdfannotator_comments} c WHERE isquestion = 1 AND annotationid IN "
                . "(SELECT id FROM {pdfannotator_annotations} a WHERE a.page = :page AND a.pdfannotatorid = :docid)";
        $questions = $DB->get_records_sql($sql, array('page' => $pagenumber, 'docid' => $documentid));
        $ret = [];
        foreach ($questions as $question) {
            // Private Comments are only displayed for the author.

            if ( !pdfannotator_can_see_comment($question, $context) ) {
                continue;
            }

            $question->answercount = pdfannotator_count_answers($question->annotationid, $context);
            $question->page = $pagenumber;

            if ($question->isdeleted == 1) {
                $question->content = '<em>'.get_string('deletedComment', 'pdfannotator').'</em>';
            }
            if ($question->ishidden == 1 && !$displayhidden) {
                $question->content = get_string('hiddenComment', 'pdfannotator');
            }
            $ret[] = $question;
        }

        return $ret;
    }

    public static function get_all_questions($documentid, $context) {
        global $DB;
        // Get all questions of a page with a subselect, where all ids of annotations of one page are selected.
        $sql = "SELECT c.*, a.page FROM {pdfannotator_comments} c "
                . "JOIN (SELECT * FROM {pdfannotator_annotations} WHERE pdfannotatorid = :docid) a "
                . "ON a.id = c.annotationid WHERE isquestion = 1";
        $questions = $DB->get_records_sql($sql, array('docid' => $documentid));

        $ret = [];
        foreach ($questions as $question) {
            if ( !pdfannotator_can_see_comment($question, $context) ) {
                continue;
            }
            $ret[$question->page][] = $question;
        }
        return $ret;
    }

    /**
     * Get all questions in an annotator where a comment contains the pattern
     * @global type $DB
     * @param type $documentid
     * @param type $pattern
     */
    public static function get_questions_search($documentid, $pattern, $context) {
        global $DB;
        $ret = [];
        $i = 0;
        $displayhidden = has_capability('mod/pdfannotator:seehiddencomments', $context);
        $sql = "SELECT c.*, a.page FROM {pdfannotator_comments} c "
                . "JOIN {pdfannotator_annotations} a ON a.id = c.annotationid "
                . "WHERE isquestion = 1 AND c.pdfannotatorid = :docid AND "
                . "annotationid IN "
               . "(SELECT annotationid FROM {pdfannotator_comments} "
                     . "WHERE " . $DB->sql_like('content', ':pattern', false) . " AND isdeleted = 0) "
                . "ORDER BY a.page, c.id";

        $params = ['docid' => $documentid, 'pattern' => '%' . $pattern . '%'];
        $questions = $DB->get_records_sql($sql, $params);

        foreach ($questions as $question) {
            if (!pdfannotator_can_see_comment($question, $context)) {
                continue;
            }

            $question->answercount = pdfannotator_count_answers($question->annotationid, $context);
            if ($question->isdeleted == 1) {
                $question->content = '<em>'.get_string('deletedComment', 'pdfannotator').'</em>';
            }
            if ($question->ishidden == 1 && !$displayhidden) {
                $question->content = get_string('hiddenComment', 'pdfannotator');
            }
            $ret[$i] = $question;   // Without this array the order by page would get lost, because js sorts by id.
            $i++;
        }
        return $ret;
    }

}
