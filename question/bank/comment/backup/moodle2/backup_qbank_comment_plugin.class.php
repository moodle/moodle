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
 * Provides the information to backup question comments.
 *
 * @package    qbank_comment
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qbank_comment_plugin extends \backup_qbank_plugin {

    /**
     * Returns the comment information to attach to question element.
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element();

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        $comments = new backup_nested_element('comments');

        $comment = new backup_nested_element('comment', ['id'], ['component', 'commentarea', 'itemid', 'contextid',
                'content', 'format', 'userid', 'timecreated']);

        $pluginwrapper->add_child($comments);
        $comments->add_child($comment);

        $comment->set_source_sql(
            "SELECT c.*
               FROM {comments} c
              WHERE c.contextid = :contextid
                AND c.component = 'qbank_comment'
                AND c.commentarea = 'question'
                AND c.itemid = :itemid",
            [
                'contextid' => backup_helper::is_sqlparam(context_system::instance()->id),
                'itemid' => backup::VAR_PARENTID,
            ]
        );

        $comment->annotate_ids('user', 'userid');

        return $plugin;
    }
}
