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
 * @subpackage essay
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Short answer question type conversion handler
 */
class moodle1_qtype_essay_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array();
    }

    /**
     * Appends the essay specific information to the question
     */
    public function process_question(array $data, array $raw) {
        // Data added on the upgrade step 2011031000.
        $this->write_xml('essay', array(
            'id'                     => $this->converter->get_nextid(),
            'responseformat'         => 'editor',
            'responsefieldlines'     => 15,
            'attachments'            => 0,
            'graderinfo'             => '',
            'graderinfoformat'       => FORMAT_HTML,
            'responsetemplate'       => '',
            'responsetemplateformat' => FORMAT_HTML
        ), array('/essay/id'));
    }
}
