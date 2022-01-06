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
 * @category  backup
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pdfannotator\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper as request_helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

/**
 * Description of provider
 *
 * @author Admin
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * This function implements the \core_privacy\local\metadata\provider interface.
     *
     * It describes what kind of data is stored by the pdfannotator, including:
     *
     * 1. Items stored in a Moodle subsystem - for example files, and ratings
     * 2. Items stored in the Moodle database
     * 3. User preferences stored site-wide within Moodle for the pdfannotator
     * 4. Data being exported to an external location
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {

        // 1. Indicating that you store content in a Moodle subsystem.
        // 1.1 Files uploaded by users are saved.
        $collection->add_subsystem_link(
                'core_files', [], 'privacy:metadata:core_files'
        );

        // 2. Describing data stored in database tables.
        // 2.1 A user's annotations in the pdf are stored.
        $collection->add_database_table(
                'pdfannotator_annotations', [
            'userid' => 'privacy:metadata:pdfannotator_annotations:userid',
            'id' => 'privacy:metadata:pdfannotator_annotations:annotationid',
                ], 'privacy:metadata:pdfannotator_annotations'
        );
        // 2.2 A user's comments are stored.
        $collection->add_database_table(
                'pdfannotator_comments', [
            'userid' => 'privacy:metadata:pdfannotator_comments:userid',
            'annotationid' => 'privacy:metadata:pdfannotator_comments:annotationid',
            'content' => 'privacy:metadata:pdfannotator_comments:content',
                ], 'privacy:metadata:pdfannotator_comments'
        );
        // 2.3 Users can report other users' comments as inappropriate. These reports stored.
        $collection->add_database_table(
                'pdfannotator_reports', [
            'commentid' => 'privacy:metadata:pdfannotator_reports:commentid',
            'message' => 'privacy:metadata:pdfannotator_reports:message',
            'userid' => 'privacy:metadata:pdfannotator_reports:userid',
                ], 'privacy:metadata:pdfannotator_reports'
        );
        // 2.4 A user's subscriptions are stored.
        $collection->add_database_table(
                'pdfannotator_subscriptions', [
            'annotationid' => 'privacy:metadata:pdfannotator_subscriptions:annotationid',
            'userid' => 'privacy:metadata:pdfannotator_subscriptions:userid',
                ], 'privacy:metadata:pdfannotator_subscriptions'
        );
        // 2.5 Votes are stored.
        $collection->add_database_table(
                'pdfannotator_votes', [
            'commentid' => 'privacy:metadata:pdfannotator_votes:commentid',
            'userid' => 'privacy:metadata:pdfannotator_votes:userid',
                ], 'privacy:metadata:pdfannotator_votes'
        );

        // 3. There are no site-wide user preferences stored at present.
        // 4. No data is exported to an external location at present.

        return $collection;
    }

    /**
     * This function implements the core_privacy\local\request\plugin\provider interface.
     * It retursn a list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {

        $contextlist = new \core_privacy\local\request\contextlist();

        $params = [
            'modname' => 'pdfannotator',
            'contextlevel' => CONTEXT_MODULE,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid5' => $userid,
            'userid6' => $userid,
        ];

        $sql = "SELECT DISTINCT c.id
                FROM {context} c
                INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                INNER JOIN {pdfannotator} p ON p.id = cm.instance
                LEFT JOIN {pdfannotator_annotations} a ON a.pdfannotatorid = p.id
                LEFT JOIN {pdfannotator_subscriptions} s ON s.annotationid = a.id
                LEFT JOIN {pdfannotator_comments} k ON k.annotationid = a.id
                LEFT JOIN {pdfannotator_reports} r ON r.commentid = k.id
                LEFT JOIN {pdfannotator_votes} v ON v.commentid = k.id
                    WHERE (
                    a.userid        = :userid1 OR
                    s.userid        = :userid2 OR
                    k.userid        = :userid3 OR
                    r.userid        = :userid5 OR
                    v.userid        = :userid6
                    )
                ";

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {

        global $DB, $CFG;

        require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

        if (empty($contextlist)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    c.id AS contextid,
                    cm.id AS cmid,
                    p.id AS id,
                    p.name AS pdfannotatorname
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {pdfannotator} p ON p.id = cm.instance
                  WHERE (
                       c.id {$contextsql}
                   )
        ";

        // Keep a mapping of pdfannotatorid to contextid.
        $mappings = [];

        $pdfannotators = $DB->get_recordset_sql($sql, $contextparams);
        foreach ($pdfannotators as $pdfannotator) {

            $mappings[$pdfannotator->id] = $pdfannotator->contextid;

            $context = \context::instance_by_id($mappings[$pdfannotator->id]);

            // Get all questions asked by this user.
            $sql1 = "SELECT c.content, c.timecreated, c.visibility
                    FROM {pdfannotator_comments} c
                    WHERE c.isquestion = 1 AND c.userid = :userid AND c.pdfannotatorid = :pdfannotator";
            $myquestions = $DB->get_records_sql($sql1, array('userid' => $userid, 'pdfannotator' => $pdfannotator->id));

            foreach ($myquestions as $myquestion) {
                $myquestion->timecreated = pdfannotator_get_user_datetime($myquestion->timecreated);
            }

            // Get all non-question comments by this user, together with the question.
            $sql2 = "SELECT c.content, c.timecreated, c.visibility, q.content as questioncontent "
                    . "FROM {pdfannotator_comments} c "
                    . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
                    . "JOIN {pdfannotator_comments} q ON q.annotationid = c.annotationid "
                    . "WHERE q.isquestion = :question AND c.isquestion = :normalcomment AND c.userid = :userid AND a.pdfannotatorid = :pdfannotator";
            $mycomments = $DB->get_records_sql($sql2, array('question' => 1, 'normalcomment' => 0, 'userid' => $userid, 'pdfannotator' => $pdfannotator->id));

            foreach ($mycomments as $mycomment) {
                $mycomment->timecreated = pdfannotator_get_user_datetime($mycomment->timecreated);
            }

            // Get all subscriptions of this user (exluding their own questions which they're automatically subscribed to).
            $sql3 = "SELECT c.content
                    FROM {pdfannotator_subscriptions} s JOIN {pdfannotator_annotations} a ON s.annotationid = a.id JOIN {pdfannotator_comments} c ON c.annotationid = a.id
                    WHERE c.isquestion = 1 AND s.userid = :userid AND a.pdfannotatorid = :pdfannotator AND NOT a.userid = :u";
            $mysubscriptions = $DB->get_records_sql($sql3, array('userid' => $userid, 'pdfannotator' => $pdfannotator->id, 'u' => $userid));

            // Get all comments this user voted for in this annotator.
            $sql4 = "SELECT c.content
                    FROM {pdfannotator_comments} c JOIN {pdfannotator_votes} v on v.commentid = c.id
                    WHERE v.userid = :userid AND c.pdfannotatorid = :pdfannotator";
            $myvotes = $DB->get_records_sql($sql4, array('userid' => $userid, 'pdfannotator' => $pdfannotator->id));

            // Get all reports this user wrote.
            $sql6 = "SELECT r.message
                    FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON c.id = r.commentid
                    WHERE r.userid = :userid AND r.pdfannotatorid = :pdfannotator";
            $myreportmessages = $DB->get_records_sql($sql6, array('userid' => $userid, 'pdfannotator' => $pdfannotator->id));

            // Get all drawings and textboxes this user made in this annotator.
            $sql7 = "SELECT a.data, a.timecreated
                    FROM {pdfannotator_annotations} a JOIN {pdfannotator_annotationtypes} t ON a.annotationtypeid = t.id
                    WHERE t.name IN (:type1, :type2) AND a.userid = :userid AND a.pdfannotatorid = :pdfannotator";
            $mydrawingsandtextboxes = $DB->get_records_sql($sql7, array('type1' => 'drawing', 'type2' => 'textbox', 'userid' => $userid, 'pdfannotator' => $pdfannotator->id));

            foreach ($mydrawingsandtextboxes as $mydrawingortextbox) {
                $mydrawingortextbox->timecreated = pdfannotator_get_user_datetime($mydrawingortextbox->timecreated);
            }

            $pdfannotator->myquestions = $myquestions;
            $pdfannotator->mycomments = $mycomments;
            $pdfannotator->mysubscriptions = $mysubscriptions;
            $pdfannotator->myvotes = $myvotes;
            $pdfannotator->myreportmessages = $myreportmessages;
            $pdfannotator->mydrawingsandtextboxes = $mydrawingsandtextboxes;

            writer::with_context($context)->export_data([], $pdfannotator);
        }
        $pdfannotators->close();
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $instanceid = $context->instanceid;

        $cm = get_coursemodule_from_id('pdfannotator', $instanceid);
        if (!$cm) {
            return;
        }

        // 1. Delete all reports of comments in this annotator.
        $DB->delete_records('pdfannotator_reports', ['pdfannotatorid' => $instanceid]);

        // 2. Delete all votes in this annotator.
        $sql = "SELECT v.id FROM {pdfannotator_votes} v WHERE v.commentid IN "
                . "(SELECT c.id FROM {pdfannotator_comments} c "
                . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id WHERE a.pdfannotatorid = ?)";
        $votes = $DB->get_records_sql($sql, array($instanceid));
        foreach ($votes as $vote) {
            $DB->delete_records('pdfannotator_votes', array("id" => $vote->id));
        }

        // 3. Delete all subscriptions in this annotator.
        $sql = "SELECT s.id FROM {pdfannotator_subscriptions} s "
                . "WHERE s.annotationid IN (SELECT a.id FROM {pdfannotator_annotations} a WHERE a.pdfannotatorid = ?)";
        $subscriptions = $DB->get_records_sql($sql, array($instanceid));
        foreach ($subscriptions as $subscription) {
            $DB->delete_records('pdfannotator_subscriptions', array("id" => $subscription->id));
        }

        // 4. Delete all comments in this annotator.
        $sql = "SELECT c.id FROM {pdfannotator_comments} c WHERE c.annotationid IN (SELECT a.id FROM {pdfannotator_annotations} a WHERE a.pdfannotatorid = ?)";
        $comments = $DB->get_records_sql($sql, array($instanceid));
        foreach ($comments as $comment) {
            $DB->delete_records('pdfannotator_comments', array("id" => $comment->id));
        }

        // 5. Delete all annotations in this annotator.
        $annotations = $DB->get_fieldset_select('pdfannotator_annotations', 'id', "pdfannotatorid = ?", array($instanceid));
        foreach ($annotations as $annotationid) {
            $DB->delete_records('pdfannotator_annotations', array("id" => $annotationid));
        }
    }

    /**
     *
     * @global type $DB
     * @param \mod_pdfannotator\privacy\approved_contextlist $contextlist
     * @return type
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {

        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {

            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);

            // 1. Delete all reports this user made in this annotator.
            $DB->delete_records('pdfannotator_reports', ['pdfannotatorid' => $instanceid, 'userid' => $userid]);

            // 2. Delete all votes this user made in this annotator.
            $sql = "SELECT v.id FROM {pdfannotator_votes} v WHERE v.userid = ? AND v.commentid IN (SELECT c.id FROM {pdfannotator_comments} c WHERE c.pdfannotatorid = ?)";
            $votes = $DB->get_records_sql($sql , array($userid, $instanceid));
            foreach ($votes as $vote) {
                $DB->delete_records('pdfannotator_votes', array("id" => $vote->id));
            }

            // 3. Delete all subscriptions this user made in this annotator.
            $sql = "SELECT s.id FROM {pdfannotator_subscriptions} s WHERE s.userid = ? AND s.annotationid IN "
                    . "(SELECT a.id FROM {pdfannotator_annotations} a WHERE a.pdfannotatorid = ?)";
            $subscriptions = $DB->get_records_sql($sql, array($userid, $instanceid));
            foreach ($subscriptions as $subscription) {
                $DB->delete_records('pdfannotator_subscriptions', array("id" => $subscription->id));
            }

            // 4. Select all comments this user made in this annotator.
            $comments = $DB->get_records_sql("SELECT c.* FROM {pdfannotator_comments} c WHERE c.pdfannotatorid = ? AND c.userid = ?", array($instanceid, $userid));
            foreach ($comments as $comment) {

                // Delete question comments, their underlying annotation as well as all answers and subscriptions.
                if ($comment->isquestion) {
                    self::delete_annotation($comment->annotationid);
                    continue;
                }
                // Empty or delete all other comments.
                self::empty_or_delete_comment($comment);
            }

            // 5. Select the IDs of all annotations that were made by this user in this annotator. Then call the function to delete the annotation and any adjacent comments.
            $annotations = $DB->get_fieldset_select('pdfannotator_annotations', 'id', "pdfannotatorid = ? AND userid = ?", array($instanceid, $userid));
            foreach ($annotations as $annotationid) {
                self::delete_annotation($annotationid);
            }
        }
    }

    // Status quo:
    // Deleting the initial or final comment of a 'thread' will remove it from the comments table.
    // Deleting any other comment will merely set the field isdeleted of the comments table to 1, so that the comment will be displayed as deleted within the 'thread'.

    /**
     * Function deletes an annotation and all comments and subscriptions attached to it.
     *
     * @global \mod_pdfannotator\privacy\type $DB
     * @param type $annotationid
     */
    public static function delete_annotation($annotationid) {

        global $DB;

        // 1. Get all comments on this annotation and prepare them for deletion.
        // 1.1 Retrieve comments from DB.
        $comments = $DB->get_records('pdfannotator_comments', array("annotationid" => $annotationid));

        foreach ($comments as $comment) {

            // 1.2 Delete any votes for these comments.
            $DB->delete_records('pdfannotator_votes', array("commentid" => $comment->id));

        }

        // 1.3 Now delete all comments.
        $DB->delete_records('pdfannotator_comments', array("annotationid" => $annotationid));

        // 2. Delete subscriptions to the question.
        $DB->delete_records('pdfannotator_subscriptions', array('annotationid' => $annotationid));

        // 3. Delete the annotation itself.
        $DB->delete_records('pdfannotator_annotations', array("id" => $annotationid));
    }

    public static function empty_or_delete_comment($comment) {

        global $DB;

        $select = "annotationid = ? AND timecreated > ? AND isdeleted = ?";
        $wasanswered = $DB->record_exists_select('pdfannotator_comments', $select, array($comment->annotationid, $comment->timecreated, 0));

        // If the comment was answered, empty it and mark it as deleted for a special display.
        if ($wasanswered) {
            $DB->update_record('pdfannotator_comments', array("id" => $comment->id, "content" => "", "isdeleted" => 1));
            // If not, just delete it.
        } else {

            // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely.
            $sql = "SELECT id, isdeleted from {pdfannotator_comments} WHERE annotationid = ? AND isquestion = ? AND timecreated < ? ORDER BY id DESC";
            $params = array($comment->annotationid, 0, $comment->timecreated);

            $predecessors = $DB->get_records_sql($sql, $params);

            foreach ($predecessors as $predecessor) {
                if ($predecessor->isdeleted) {
                    $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                } else {
                    break;
                }
            }

            // Now delete the selected comment.
            $DB->delete_records('pdfannotator_comments', array("id" => $comment->id));
        }
    }

}