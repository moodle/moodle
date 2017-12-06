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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module test data generator class
 *
 * @package    moodlecore
 * @subpackage question
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_generator extends component_generator_base {

    /**
     * @var number of created instances
     */
    protected $categorycount = 0;

    public function reset() {
        $this->categorycount = 0;
    }

    /**
     * Create a new question category.
     * @param array|stdClass $record
     * @return stdClass question_categories record.
     */
    public function create_question_category($record = null) {
        global $DB;

        $this->categorycount++;

        $defaults = array(
            'name'       => 'Test question category ' . $this->categorycount,
            'contextid'  => context_system::instance()->id,
            'info'       => '',
            'infoformat' => FORMAT_HTML,
            'stamp'      => make_unique_id_code(),
            'parent'     => 0,
            'sortorder'  => 999,
        );

        $record = $this->datagenerator->combine_defaults_and_record($defaults, $record);
        $record['id'] = $DB->insert_record('question_categories', $record);
        return (object) $record;
    }

    /**
     * Create a new question. The question is initialised using one of the
     * examples from the appropriate {@link question_test_helper} subclass.
     * Then, any files you want to change from the value in the base example you
     * can override using $overrides.
     * @param string $qtype the question type to create an example of.
     * @param string $which as for the corresponding argument of
     *      {@link question_test_helper::get_question_form_data}. null for the default one.
     * @param array|stdClass $overrides any fields that should be different from the base example.
     */
    public function create_question($qtype, $which = null, $overrides = null) {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

        $fromform = test_question_maker::get_question_form_data($qtype, $which);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record(
                (array) $fromform, $overrides);

        $question = new stdClass();
        $question->category  = $fromform->category;
        $question->qtype     = $qtype;
        $question->createdby = 0;
        return question_bank::get_qtype($qtype)->save_question($question, $fromform);
    }
}
