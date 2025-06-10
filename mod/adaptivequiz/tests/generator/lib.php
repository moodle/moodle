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
 * Generator for the module.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2024 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_adaptivequiz_generator extends testing_module_generator {

    /**
     * Creates new module instance.
     *
     * For params description see the parent's method.
     *
     * @param array|stdClass|null $record
     * @param array|null $options
     * @return stdClass
     * @throws coding_exception
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;

        require_once($CFG->dirroot .'/mod/adaptivequiz/locallib.php');

        $record = (object)(array)$record;

        if (!isset($record->questionpool) && !isset($record->questionpoolnamed)) {
            throw new coding_exception('either \'questionpool\' or \'questionpoolnamed\' property must be specified when '.
                'generating an adaptive quiz instance');
        }

        // Named question pool takes precedence over the 'questionpool' setting.
        if (isset($record->questionpoolnamed)) {
            if (is_string($record->questionpoolnamed)) {
                $record->questionpoolnamed = [$record->questionpoolnamed];
            }

            $record->questionpool = $this->get_question_category_id_list_by_names($record->questionpoolnamed);
            unset($record->questionpoolnamed);
        }

        $defaultsettings = [
            'introformat' => FORMAT_MOODLE,
            'attempts' => 0,
            'grademethod' => ADAPTIVEQUIZ_GRADEHIGHEST,
            'password' => '',
            'attemptfeedback' => '',
            'attemptfeedbackformat' => FORMAT_MOODLE,
            'attemptonlast' => 0,
            'highestlevel' => 111,
            'lowestlevel' => 1,
            'minimumquestions' => 1,
            'maximumquestions' => 111,
            'standarderror' => 1.1,
            'startinglevel' => 11,
            'timecreated' => time(),
            'timemodified' => time(),
        ];

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, $options);
    }

    /**
     * Fetches a list of id for the given names of question categories.
     *
     * @param string[] $names
     * @return int[]
     */
    private function get_question_category_id_list_by_names(array $names): array {
        global $DB;

        [$namesql, $nameparams] = $DB->get_in_or_equal($names);

        return $DB->get_fieldset_select('question_categories', 'id', "name $namesql", $nameparams);
    }
}
