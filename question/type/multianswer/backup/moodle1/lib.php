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
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Multianswer (aka embedded, cloze) question type conversion handler
 */
class moodle1_qtype_multianswer_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'MULTIANSWERS/MULTIANSWER',
        );
    }

    /**
     * Appends the multianswer specific information to the question
     *
     * Note that there is an upgrade step 2008050800 that is not replayed here as I suppose there
     * was an error on restore and the backup file contains correct data. If I'm wrong on this
     * assumption then the parent of the embedded questions could be fixed on conversion in theory
     * (by using a temporary stash that keeps multianswer's id and its sequence) but the category
     * fix would be tricky in XML.
     */
    public function process_question(array $data, array $raw) {

        // Convert and write the answers first.
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // Convert and write the multianswer extra fields.
        foreach ($data['multianswers'] as $multianswers) {
            foreach ($multianswers as $multianswer) {
                $this->write_xml('multianswer', $multianswer, array('/multianswer/id'));
            }
        }
    }
}
