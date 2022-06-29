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
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qbank_customfields_plugin extends restore_qbank_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     *
     * @return restore_path_element[] The restore path element array.
     */
    protected function define_question_plugin_structure(): array {
        return [new restore_path_element('customfield', $this->get_pathfor('/customfields/customfield'))];
    }

    /**
     * Process the question custom field element.
     *
     * @param array $data The custom field data to restore.
     */
    public function process_customfield(array $data) {
        global $DB;

        $newquestion = $this->get_new_parentid('question');

        if (!empty($data->contextid) && $newcontextid = $this->get_mappingid('context', $data->contextid)) {
            $fieldcontextid = $newcontextid;
        } else {
            // Get the category, so we can then later get the context.
            $categoryid = $this->get_new_parentid('question_category');
            if (empty($this->cachedcategory) || $this->cachedcategory->id != $categoryid) {
                $this->cachedcategory = $DB->get_record('question_categories', ['id' => $categoryid]);
            }
            $fieldcontextid = $this->cachedcategory->contextid;
        }

        $data['newquestion'] = $newquestion;
        $data['fieldcontextid'] = $fieldcontextid;

        $customfieldhandler = qbank_customfields\customfield\question_handler::create();
        $customfieldhandler->restore_instance_data_from_backup($this->task, $data);
    }
}
