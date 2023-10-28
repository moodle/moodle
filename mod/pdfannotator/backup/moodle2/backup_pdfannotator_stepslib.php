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
 * Define all the backup steps that will be used by the backup_pdfannotator_activity_task
 *
 * Moodle creates backups of courses or their parts by executing a so called backup plan.
 * The backup plan consists of a set of backup tasks and finally each backup task consists of one or more backup steps.
 * This file provides all the backup steps classes.
 *
 * See https://docs.moodle.org/dev/Backup_API and https://docs.moodle.org/dev/Backup_2.0_for_developers for more information.
 *
 * @package   mod_pdfannotator
 * @category  backup
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete pdfannotator structure for backup, with file and id annotations
 */
class backup_pdfannotator_activity_structure_step extends backup_activity_structure_step {

    /**
     * There are three main things that the method must do:
     * 1. Create a set of backup_nested_element instances that describe the required data of your plugin
     * 2. Connect these instances into a hierarchy using their add_child() method
     * 3. Set data sources for the elements, using their methods like set_source_table() or set_source_sql()
     * The method must return the root backup_nested_element instance processed by the prepare_activity_structure()
     * method (which just wraps your structures with a common envelope).
     *
     * TODO Adjust after final db structure has been determined
     *
     */
    protected function define_structure() {

        // 1. To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // 2. Define each element separately.
        $pdfannotator = new backup_nested_element('pdfannotator', array('id'), array(
            'name', 'intro', 'introformat', 'usevotes', 'useprint', 'useprintcomments', 'use_studenttextbox', 'use_studentdrawing',
            'useprivatecomments', 'useprotectedcomments', 'timecreated', 'timemodified'));

            $annotations = new backup_nested_element('annotations');
            $annotation = new backup_nested_element('annotation', array('id'), array('page', 'userid', 'annotationtypeid',
                'data', 'timecreated', 'timemodified', 'modifiedby'));

                $subscriptions = new backup_nested_element('subscriptions');
                $subscription = new backup_nested_element('subscription', array('id'), array('userid'));

                $comments = new backup_nested_element('comments');
                $c = array('pdfannotatorid', 'userid', 'content', 'timecreated', 'timemodified', 'modifiedby', 'visibility',
                    'isquestion', 'isdeleted', 'ishidden', 'solved');
                $comment = new backup_nested_element('comment', array('id'), $c);

                    $votes = new backup_nested_element('votes');
                    $vote = new backup_nested_element('vote', array('id'), array('userid', 'annotationid'));

                    $reports = new backup_nested_element('reports');
                    $report = new backup_nested_element('report', array('id'), array('courseid', 'pdfannotatorid', 'message',
                        'userid', 'timecreated', 'seen'));

        // 3. Build the tree (mind the right order!)
        $pdfannotator->add_child($annotations);
            $annotations->add_child($annotation);

                $annotation->add_child($subscriptions);
                    $subscriptions->add_child($subscription);

                $annotation->add_child($comments);
                    $comments->add_child($comment);

                        $comment->add_child($votes);
                            $votes->add_child($vote);

                        $comment->add_child($reports);
                            $reports->add_child($report);

        // 4. Define db sources
        // backup::VAR_ACTIVITYID is the 'course module id'.
        $pdfannotator->set_source_table('pdfannotator', array('id' => backup::VAR_ACTIVITYID));

        if ($userinfo) {
            // Add all annotations specific to this annotator instance.
            $annotation->set_source_sql('SELECT a.* FROM {pdfannotator_annotations} a '
                                        . 'JOIN {pdfannotator_comments} c ON a.id = c.annotationid '
                                        . "WHERE a.pdfannotatorid = ? AND c.isquestion = 1 AND "
                                        . "(c.visibility = 'public' OR c.visibility = 'anonymous') ",
                                        array('pdfannotatorid' => backup::VAR_PARENTID));

                // Add any subscriptions to this annotation.
                $subscription->set_source_table('pdfannotator_subscriptions', array('annotationid' => backup::VAR_PARENTID));

                // Add any comments of this annotation.
                $comment->set_source_table('pdfannotator_comments', array('annotationid' => backup::VAR_PARENTID));

                    // Add any votes for this comment.
                    $vote->set_source_table('pdfannotator_votes', array('commentid' => backup::VAR_PARENTID));

                    // Add any reports of this comment.
                    $report->set_source_table('pdfannotator_reports', array('commentid' => backup::VAR_PARENTID));
        }

        // 5. Define id annotations (some attributes are foreign keys).
        $annotation->annotate_ids('user', 'userid');
        $subscription->annotate_ids('user', 'userid');
        $comment->annotate_ids('user', 'userid');
        $comment->annotate_ids('pdfannotator', 'pdfannotatorid');
        $vote->annotate_ids('user', 'userid');
        $report->annotate_ids('user', 'userid');
        $report->annotate_ids('pdfannotator', 'pdfannotatorid');

        // 6. Define file annotations (vgl. resource activity).
        $pdfannotator->annotate_files('mod_pdfannotator', 'intro', null); // This file area does not have an itemid.
        $pdfannotator->annotate_files('mod_pdfannotator', 'content', null); // See above.
        $comment->annotate_files('mod_pdfannotator', 'post', 'id');

        // 7. Return the root element (pdfannotator), wrapped into standard activity structure.
        return $this->prepare_activity_structure($pdfannotator);
    }
}
