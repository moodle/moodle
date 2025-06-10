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
global $CFG;
require_once($CFG->dirroot . '/question/type/wq/quizzes/quizzes.php');
require_once($CFG->dirroot . '/question/type/wq/lib.php');

class moodle1_qtype_truefalsewiris_handler extends moodle1_qtype_truefalse_handler {

    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'TRUEFALSE',
            'TRUEFALSEWIRIS',
            'TRUEFALSEWIRIS/WIRISOPTIONS'
        );
    }

    public function process_question(array $data, array $raw) {
        parent::process_question($data, $raw);

        $data['actualtruefalsewiris']['id'] = $this->converter->get_nextid();

        $wirisprogram = '<question><wirisCasSession>';
        $wirisprogram .= htmlspecialchars(wrsqz_mathml_decode($data['truefalsewiris'][0]['wirisprogram']), ENT_COMPAT, "utf-8");
        $wirisprogram .= '</wirisCasSession>';

        if (isset($data['truefalsewiris'][0]['wirisoptions']) && count($data['truefalsewiris'][0]['wirisoptions']) > 0) {
            $wirisprogram .= '<localData>';
            $wirisprogram .= $this->wrsqz_get_cas_for_computations($data);
            $wirisprogram .= $this->wrsqz_hidden_initial_cas_value($data);
            $wirisprogram .= '</localData>';
        }

        $wirisprogram .= '</question>';

        $wirisoverrideanswer = $data['truefalsewiris'][0]['wirisoverrideanswer'];

        $data['actualtruefalsewiris']['xml'] = $wirisprogram;
        if (isset($wirisoverrideanswer)) {
            $data['actualtruefalsewiris']['options'] = $wirisoverrideanswer;
        }

        $this->write_xml('question_xml', $data['actualtruefalsewiris'], array('/question_xml/id'));
    }

    protected function wrsqz_get_cas_for_computations($data) {
        $wrap = com_wiris_system_CallWrapper::getInstance();

        $wirisquestion = '';
        if (isset($data['truefalsewiris'][0]['wirisoptions'][0]['wiriscasforcomputations'])) {
            if ($data['truefalsewiris'][0]['wirisoptions'][0]['wiriscasforcomputations'] == 1) {
                // @codingStandardsIgnoreStart
                $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
                $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_ADD;
                // @codingStandardsIgnoreEnd
                $wirisquestion .= '</data>';
            } else if ($data['truefalsewiris'][0]['wirisoptions'][0]['wiriscasforcomputations'] == 2) {
                // @codingStandardsIgnoreStart
                $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
                $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_REPLACE;
                // @codingStandardsIgnoreEnd
                $wirisquestion .= '</data>';
            }
        } else {
            // @codingStandardsIgnoreStart
            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
            $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_FALSE;
            // @codingStandardsIgnoreEnd
            $wirisquestion .= '</data>';
        }
        return $wirisquestion;
    }

    protected function wrsqz_hidden_initial_cas_value($data) {
        $wrap = com_wiris_system_CallWrapper::getInstance();

        $wirisquestion = '';
        if (isset($data['truefalsewiris'][0]['wirisoptions'][0]['hiddeninitialcasvalue'])) {
            // @codingStandardsIgnoreLine
            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_CAS_INITIAL_SESSION . '">';
            $initialcasvalue = $data['truefalsewiris'][0]['wirisoptions'][0]['hiddeninitialcasvalue'];
            $wirisquestion .= htmlspecialchars(wrsqz_mathml_decode(trim($initialcasvalue)), ENT_COMPAT, "utf-8");
            $wirisquestion .= '</data>';
        }

        return $wirisquestion;
    }

}
