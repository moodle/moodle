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
 * Steps definitions related with the questionnaire activity.
 *
 * @package    mod_questionnaire
 * @category   test
 * @copyright  2016 Mike Churchward - The POET Group
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\When as When,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Gherkin\Node\PyStringNode as PyStringNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException;
;
/**
 * Questionnaire-related steps definitions.
 *
 * @package    mod_questionnaire
 * @category   test
 * @copyright  2016 Mike Churchward - The POET Group
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_questionnaire extends behat_base {

    /**
     * Adds a question to the questionnaire with the provided data.
     *
     * @Given /^I add a "([^"]*)" question and I fill the form with:$/
     *
     * @param string $questiontype The question type by text name to enter.
     * @param TableNode $fielddata
     */
    public function i_add_a_question_and_i_fill_the_form_with($questiontype, TableNode $fielddata) {
        $validtypes = array(
            '----- Page Break -----',
            'Check Boxes',
            'Date',
            'Dropdown Box',
            'Essay Box',
            'Label',
            'Numeric',
            'Radio Buttons',
            'Rate (scale 1..5)',
            'Text Box',
            'Yes/No');

        if (!in_array($questiontype, $validtypes)) {
            throw new ExpectationException('Invalid question type specified.', $this->getSession());
        }

        // We get option choices as CSV strings. If we have this, modify it for use in
        // multiline data.
        $rows = $fielddata->getRows();
        $hashrows = $fielddata->getRowsHash();
        $options = array();
        if (isset($hashrows['Possible answers'])) {
            $options = explode(',', $hashrows['Possible answers']);
            $rownum = -1;
            // Find the row that contained multiline data and add line breaks. Rows are two item arrays where the
            // first is an identifier and the second is the value.
            foreach ($rows as $key => $row) {
                if ($row[0] == 'Possible answers') {
                    $row[1] = str_replace(',', "\n", $row[1]);
                    $rows[$key] = $row;
                    break;
                }
            }
            $fielddata = new TableNode($rows);
        }

        $this->execute('behat_forms::i_set_the_field_to', array('id_type_id', $questiontype));
        $this->execute('behat_forms::press_button', 'Add selected question type');
        if (isset($hashrows['id_dependquestions_and_1'])) {
            $this->execute('behat_forms::press_button', 'id_adddependencies_and');
        }
        if (isset($hashrows['id_dependquestions_or_1'])) {
            $this->execute('behat_forms::press_button', 'id_adddependencies_or');
        }
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $fielddata);
        $this->execute('behat_forms::press_button', 'Save changes');
    }

    /**
     * Selects a radio button option in the named radio button group.
     *
     * @Given /^I click the "([^"]*)" radio button$/
     *
     * @param string $radiogroupname The "id" attribute of the radio button.
     */
    public function i_click_the_radio_button($radioid) {
        $session = $this->getSession();
        $page = $session->getPage();
        $radios = $page->findAll('xpath', '//input[@type="radio" and @id="'.$radioid.'"]');
        $radios[0]->click();
    }

    /**
     * Adds a questions and responses to the questionnaire with the provided name.
     *
     * @Given /^"([^"]*)" has questions and responses$/
     *
     * @param string $questionnairename The name of an existing questionnaire.
     */
    public function has_questions_and_responses($questionnairename) {
        global $DB;

        if (!$questionnaire = $DB->get_record('questionnaire', array('name' => $questionnairename), 'id,sid')) {
            throw new ExpectationException('Invalid questionnaire name specified.', $this->getSession());
        }

        if (!$DB->record_exists('questionnaire_survey', array('id' => $questionnaire->sid))) {
            throw new ExpectationException('Questionnaire survey does not exist.', $this->getSession());
        }

        $this->add_question_data($questionnaire->sid);
        $this->add_response_data($questionnaire->id, $questionnaire->sid);
    }

    /**
     * Adds a question data to the given survey id.
     *
     * @param int $sid The id field of an existing questionnaire_survey record.
     * @return null
     */
    private function add_question_data($sid) {
        $questiondata = array(
            array("id", "survey_id", "name", "type_id", "result_id", "length", "precise", "position", "content", "required",
                  "deleted", "dependquestion", "dependchoice"),
            array("1", $sid, "own car", "1", null, "0", "0", "1", "<p>Do you own a car?</p>", "y", "n", "0", "0"),
            array("2", $sid, "optional", "2", null, "20", "25", "3", "<p>What is the colour of your car?</p>", "y", "n", "121",
                  "0"),
            array("3", $sid, null, "99", null, "0", "0", "2", "break", "n", "n", "0", "0"),
            array("4", $sid, "optional2", "1", null, "0", "0", "5", "<p>Do you sometimes use public transport to go to work?</p>",
                  "y", "n", "0", "0"),
            array("5", $sid, null, "99", null, "0", "0", "4", "break", "n", "n", "0", "0"),
            array("6", $sid, "entertext", "2", null, "20", "10", "6", "<p>Enter no more than 10 characters.<br></p>", "n", "n", "0",
                  "0"),
            array("7", $sid, "q7", "5", null, "0", "0", "7", "<p>Check all that apply<br></p>", "n", "n", "0", "0"),
            array("8", $sid, "q8", "9", null, "0", "0", "8", "<p>Enter today's date<br></p>", "n", "n", "0", "0"),
            array("9", $sid, "q9", "6", null, "0", "0", "9", "<p>Choose One<br></p>", "n", "n", "0", "0"),
            array("10", $sid, "q10", "3", null, "5", "0", "10", "<p>Write an essay<br></p>", "n", "n", "0", "0"),
            array("11", $sid, "q11", "10", null, "10", "0", "11", "<p>Enter a number<br></p>", "n", "n", "0", "0"),
            array("12", $sid, "q12", "4", null, "1", "0", "13", "<p>Choose a colour<br></p>", "n", "n", "0", "0"),
            array("13", $sid, "q13", "8", null, "5", "1", "14", "<p>Rate this.<br></p>", "n", "n", "0", "0"),
            array("14", $sid, null, "99", null, "0", "0", "12", "break", "n", "y", "0", "0"),
            array("15", $sid, null, "99", null, "0", "0", "12", "break", "n", "n", "0", "0"),
            array("16", $sid, "Q1", "10", null, "3", "2", "15", "Enter a number<br><p><br></p>", "y", "n", "0", "0")
        );

        $choicedata = array(
            array("id", "question_id", "content", "value"),
            array("1", "7", "1", null),
            array("2", "7", "2", null),
            array("3", "7", "3", null),
            array("4", "7", "4", null),
            array("5", "7", "5", null),
            array("6", "9", "1", null),
            array("7", "9", "One", null),
            array("8", "9", "2", null),
            array("9", "9", "Two", null),
            array("10", "9", "3", null),
            array("11", "9", "Three", null),
            array("12", "12", "Red", null),
            array("13", "12", "Toyota", null),
            array("14", "12", "Bird", null),
            array("15", "12", "Blew", null),
            array("16", "13", "Good", null),
            array("17", "13", "Great", null),
            array("18", "13", "So-so", null),
            array("19", "13", "Lamp", null),
            array("20", "13", "Huh?", null),
            array("21", "7", "!other=Another number", null),
            array("22", "12", "!other=Something else", null)
        );

        $this->add_data($questiondata, 'questionnaire_question', 'questionmap');
        $this->add_data($choicedata, 'questionnaire_quest_choice', 'choicemap', array('questionmap' => 'question_id'));
    }

    /**
     * Adds response data to the given questionnaire and survey id.
     *
     * @param int $qid The id field of an existing questionnaire record.
     * @param int $sid The id field of an existing questionnaire_survey record.
     * @return null
     */
    private function add_response_data($qid, $sid) {
        $responses = array(
            array("id", "survey_id", "submitted", "complete", "grade", "userid"),
            array("1", $sid, "1419011935", "y", "0", "2"),
            array("2", $sid, "1449064371", "y", "0", "2"),
            array("3", $sid, "1449258520", "y", "0", "2"),
            array("4", $sid, "1452020444", "y", "0", "2"),
            array("5", $sid, "1452804783", "y", "0", "2"),
            array("6", $sid, "1452806547", "y", "0", "2"),
            array("7", $sid, "1465415731", "n", "0", "2")
        );

        $this->add_data($responses, 'questionnaire_response', 'responsemap');

        $attempts = array(
            array("id", "qid", "userid", "rid", "timemodified"),
            array("", $qid, "2", "1", "1419011935"),
            array("", $qid, "2", "2", "1449064371"),
            array("", $qid, "2", "3", "1449258520"),
            array("", $qid, "2", "4", "1452020444"),
            array("", $qid, "2", "5", "1452804783"),
            array("", $qid, "2", "6", "1452806547")
        );
        $this->add_data($attempts, 'questionnaire_attempts', '', array('responsemap' => 'rid'));

        $responsebool = array(
            array("id", "response_id", "question_id", "choice_id"),
            array("", "1", "1", "y"),
            array("", "1", "4", "n"),
            array("", "2", "1", "y"),
            array("", "2", "4", "n"),
            array("", "3", "1", "n"),
            array("", "3", "4", "y"),
            array("", "4", "1", "y"),
            array("", "4", "4", "n"),
            array("", "5", "1", "n"),
            array("", "5", "4", "n"),
            array("", "6", "1", "n"),
            array("", "6", "4", "n"),
            array("", "7", "1", "y"),
            array("", "7", "4", "y")
        );
        $this->add_data($responsebool, 'questionnaire_response_bool', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id'));

        $responsedate = array(
            array("id", "response_id", "question_id", "response"),
            array("", "1", "8", "2014-12-19"),
            array("", "2", "8", "2015-12-02"),
            array("", "3", "8", "2015-12-04"),
            array("", "4", "8", "2016-01-06"),
            array("", "5", "8", "2016-01-13"),
            array("", "6", "8", "2016-01-13")
        );
        $this->add_data($responsedate, 'questionnaire_response_date', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id'));

        $responseother = array(
            array("id", "response_id", "question_id", "choice_id", "response"),
            array("", "5", "7", "21", "Forty-four"),
            array("", "6", "12", "22", "Green"),
            array("", "7", "7", "21", "5")
        );
        $this->add_data($responseother, 'questionnaire_response_other', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id', 'choicemap' => 'choice_id'));

        $responserank = array(
            array("id", "response_id", "question_id", "choice_id", "rank"),
            array("", "1", "13", "16", "0"),
            array("", "1", "13", "17", "1"),
            array("", "1", "13", "18", "2"),
            array("", "1", "13", "19", "3"),
            array("", "1", "13", "20", "4"),
            array("", "2", "13", "16", "0"),
            array("", "2", "13", "17", "1"),
            array("", "2", "13", "18", "2"),
            array("", "2", "13", "19", "3"),
            array("", "2", "13", "20", "4"),
            array("", "3", "13", "16", "4"),
            array("", "3", "13", "17", "0"),
            array("", "3", "13", "18", "3"),
            array("", "3", "13", "19", "1"),
            array("", "3", "13", "20", "2"),
            array("", "4", "13", "16", "2"),
            array("", "4", "13", "17", "2"),
            array("", "4", "13", "18", "2"),
            array("", "4", "13", "19", "2"),
            array("", "4", "13", "20", "2"),
            array("", "5", "13", "16", "1"),
            array("", "5", "13", "17", "1"),
            array("", "5", "13", "18", "1"),
            array("", "5", "13", "19", "1"),
            array("", "5", "13", "20", "-1"),
            array("", "6", "13", "16", "2"),
            array("", "6", "13", "17", "3"),
            array("", "6", "13", "18", "-1"),
            array("", "6", "13", "19", "1"),
            array("", "6", "13", "20", "-1"),
            array("", "7", "13", "16", "-999"),
            array("", "7", "13", "17", "-999"),
            array("", "7", "13", "18", "-999"),
            array("", "7", "13", "19", "-999"),
            array("", "7", "13", "20", "-999")
        );
        $this->add_data($responserank, 'questionnaire_response_rank', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id', 'choicemap' => 'choice_id'));

        $respmultiple = array(
            array("id", "response_id", "question_id", "choice_id"),
            array("", "1", "7", "1"),
            array("", "1", "7", "3"),
            array("", "1", "7", "5"),
            array("", "2", "7", "4"),
            array("", "3", "7", "2"),
            array("", "3", "7", "4"),
            array("", "4", "7", "2"),
            array("", "4", "7", "4"),
            array("", "4", "7", "5"),
            array("", "5", "7", "2"),
            array("", "5", "7", "3"),
            array("", "5", "7", "21"),
            array("", "6", "7", "2"),
            array("", "6", "7", "5"),
            array("", "7", "7", "21")
        );
        $this->add_data($respmultiple, 'questionnaire_resp_multiple', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id', 'choicemap' => 'choice_id'));

        $respsingle = array(
            array("id", "response_id", "question_id", "choice_id"),
            array("", "1", "9", "7"),
            array("", "1", "12", "15"),
            array("", "2", "9", "7"),
            array("", "2", "12", "14"),
            array("", "3", "9", "11"),
            array("", "3", "12", "15"),
            array("", "4", "9", "6"),
            array("", "4", "12", "12"),
            array("", "5", "9", "6"),
            array("", "5", "12", "13"),
            array("", "6", "9", "7"),
            array("", "6", "12", "22")
        );
        $this->add_data($respsingle, 'questionnaire_resp_single', '',
            array('responsemap' => 'response_id', 'questionmap' => 'question_id', 'choicemap' => 'choice_id'));
    }

    /**
     * Helper function to insert record data, save mapping data and remap data where necessary.
     *
     * @param array $data Array of data record row arrays. The first row contains the field names.
     * @param string $datatable The name of the data table to insert records into.
     * @param string $mapvar The name of the object variable to store oldid / newid mappings (optional).
     * @param string $replvars Array of key/value pairs where key is the mapvar and value is the record field
     *                         to replace with mapped values.
     * @return null
     */
    private function add_data(array $data, $datatable, $mapvar = '', array $replvars = null) {
        global $DB;

        if ($replvars === null) {
            $replvars = array();
        }
        $fields = array_shift($data);
        foreach ($data as $row) {
            $record = new stdClass();
            foreach ($row as $key => $fieldvalue) {
                if ($fields[$key] == 'id') {
                    if (!empty($mapvar)) {
                        $oldid = $fieldvalue;
                    }
                } else if (($replvar = array_search($fields[$key], $replvars)) !== false) {
                    $record->{$fields[$key]} = $this->{$replvar}[$fieldvalue];
                } else {
                    $record->{$fields[$key]} = $fieldvalue;
                }
            }
            $newid = $DB->insert_record($datatable, $record);
            if (!empty($mapvar)) {
                $this->{$mapvar}[$oldid] = $newid;
            }
        }

    }
}
