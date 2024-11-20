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
 * Restore plugin class that provides the necessary information needed to restore comments for questions.
 *
 * @package    qbank_comment
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qbank_comment_plugin extends restore_qbank_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {
        return [
            new restore_path_element('comment', $this->get_pathfor('/comments/comment'))
        ];
    }

    /**
     * Process the question comments element.
     *
     * @param array $data The comment data to restore.
     */
    public function process_comment($data) {
        global $DB, $CFG;

        $data = (object)$data;

        $newquestionid = $this->get_new_parentid('question');
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));
        if (!$questioncreated) {
            // This question already exists in the question bank. Nothing for us to do.
            return;
        }

        if ($CFG->usecomments) {
            $data->itemid = $newquestionid;
            $DB->insert_record('comments', $data);
        }
    }
}
