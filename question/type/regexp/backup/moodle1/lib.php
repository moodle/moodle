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
 * Regexp question conversion handler.
 *
 * @package    qtype_regexp
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Joseph Rézeau
 * @copyright  2011 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Regexp question conversion handler.
 * @copyright  2011 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle1_qtype_regexp_handler extends moodle1_qtype_handler {

    /**
     * Returns the question subpaths.
     * @return array
     */
    public function get_question_subpaths() {
        return [
            'ANSWERS/ANSWER',
            'REGEXP',
        ];
    }

    /**
     * Appends the regexp specific information to the question.
     * @param array $data
     * @param array $raw
     */
    public function process_question(array $data, array $raw) {

        // Convert and write the answers first.
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // Convert and write the regexp extra fields.
        foreach ($data['regexp'] as $regexp) {
            $regexp['id'] = $this->converter->get_nextid();
            $this->write_xml('regexp', $regexp, ['/regexp/id']);
        }
    }
}
