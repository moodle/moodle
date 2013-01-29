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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Restore plugin class that provides the necessary information
 * needed to restore one match qtype plugin.
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_match_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // Add own qtype stuff.
        $elename = 'matchoptions';
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/matchoptions');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'match';
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/matches/match');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Process the qtype/matchoptions element
     */
    public function process_matchoptions($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_match_options too.
        if ($questioncreated) {
            // Fill in some field that were added in 2.1, and so which may be missing
            // from backups made in older versions of Moodle.
            if (!isset($data->correctfeedback)) {
                $data->correctfeedback = '';
                $data->correctfeedbackformat = FORMAT_HTML;
            }
            if (!isset($data->partiallycorrectfeedback)) {
                $data->partiallycorrectfeedback = '';
                $data->partiallycorrectfeedbackformat = FORMAT_HTML;
            }
            if (!isset($data->incorrectfeedback)) {
                $data->incorrectfeedback = '';
                $data->incorrectfeedbackformat = FORMAT_HTML;
            }
            if (!isset($data->shownumcorrect)) {
                $data->shownumcorrect = 0;
            }

            // Adjust some columns.
            $data->questionid = $newquestionid;
            $newitemid = $DB->insert_record('qtype_match_options', $data);
            $this->set_mapping('qtype_match_options', $oldid, $newitemid);
        }
    }

    /**
     * Process the qtype/matches/match element
     */
    public function process_match($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        if ($questioncreated) {
            // If the question has been created by restore, we need to create its
            // qtype_match_subquestions too.

            // Adjust some columns.
            $data->questionid = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('qtype_match_subquestions', $data);
            // Create mapping (there are files and states based on this).
            $this->set_mapping('qtype_match_subquestions', $oldid, $newitemid);
            if (isset($data->code)) {
                $this->set_mapping('qtype_match_subquestion_codes', $data->code, $newitemid);
            }

        } else {
            // Match questions require mapping of qtype_match_subquestions, because
            // they are used by question_states->answer.

            // Look for matching subquestion (by questionid, questiontext and answertext).
            $sub = $DB->get_record_select('qtype_match_subquestions', 'questionid = ? AND ' .
                    $DB->sql_compare_text('questiontext') . ' = ' .
                    $DB->sql_compare_text('?').' AND answertext = ?',
                            array($newquestionid, $data->questiontext, $data->answertext),
                            'id', IGNORE_MULTIPLE);

            // Not able to find the answer, let's try cleaning the answertext
            // of all the match subquestions in DB as slower fallback. MDL-36683 / MDL-30018.
            if (!$sub) {
                $potentialsubs = $DB->get_records('qtype_match_subquestions',
                        array('questionid' => $newquestionid), '', 'id, questiontext, answertext');
                foreach ($potentialsubs as $potentialsub) {
                    // Clean in the same way than {@link xml_writer::xml_safe_utf8()}.
                    $cleanquestion = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is',
                            '', $potentialsub->questiontext); // Clean CTRL chars.
                    $cleanquestion = preg_replace("/\r\n|\r/", "\n", $cleanquestion); // Normalize line ending.

                    $cleananswer = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is',
                            '', $potentialsub->answertext); // Clean CTRL chars.
                    $cleananswer = preg_replace("/\r\n|\r/", "\n", $cleananswer); // Normalize line ending.

                    if ($cleanquestion === $data->questiontext && $cleananswer == $data->answertext) {
                        $sub = $potentialsub;
                    }
                }
            }

            // Found one. Let's create the mapping.
            if ($sub) {
                $this->set_mapping('qtype_match_subquestions', $oldid, $sub->id);
            } else {
                throw new restore_step_exception('error_qtype_match_subquestion_missing_in_db', $data);
            }
        }
    }

    public function recode_response($questionid, $sequencenumber, array $response) {
        if (array_key_exists('_stemorder', $response)) {
            $response['_stemorder'] = $this->recode_match_sub_order($response['_stemorder']);
        }
        if (array_key_exists('_choiceorder', $response)) {
            $response['_choiceorder'] = $this->recode_match_sub_order($response['_choiceorder']);
        }
        return $response;
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for match questions.
     *
     * answer is one comma separated list of hypen separated pairs
     * containing question_match_sub->id and question_match_sub->code, which
     * has been remapped to be qtype_match_subquestions->id, since code no longer exists.
     */
    public function recode_legacy_state_answer($state) {
        $answer = $state->answer;
        $resultarr = array();
        foreach (explode(',', $answer) as $pair) {
            $pairarr = explode('-', $pair);
            $id = $pairarr[0];
            $code = $pairarr[1];
            $newid = $this->get_mappingid('qtype_match_subquestions', $id);
            if ($code) {
                $newcode = $this->get_mappingid('qtype_match_subquestion_codes', $code);
            } else {
                $newcode = $code;
            }
            $resultarr[] = $newid . '-' . $newcode;
        }
        return implode(',', $resultarr);
    }

    /**
     * Recode the choice order as stored in the response.
     * @param string $order the original order.
     * @return string the recoded order.
     */
    protected function recode_match_sub_order($order) {
        $neworder = array();
        foreach (explode(',', $order) as $id) {
            if ($newid = $this->get_mappingid('qtype_match_subquestions', $id)) {
                $neworder[] = $newid;
            }
        }
        return implode(',', $neworder);
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder.
     */
    public static function define_decode_contents() {

        $contents = array();

        $contents[] = new restore_decode_content('qtype_match_subquestions',
                array('questiontext'), 'qtype_match_subquestions');

        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        $contents[] = new restore_decode_content('qtype_match_options', $fields, 'qtype_match_options');

        return $contents;
    }
}
