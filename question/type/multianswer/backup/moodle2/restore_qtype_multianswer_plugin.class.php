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
 * restore plugin class that provides the necessary information
 * needed to restore one multianswer qtype plugin
 *
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_multianswer_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {
        $paths = array();

        // This qtype uses question_answers, add them
        $this->add_question_question_answers($paths);

        // Add own qtype stuff
        $elename = 'multianswer';
        $elepath = $this->get_pathfor('/multianswer');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Process the qtype/multianswer element
     */
    public function process_multianswer($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its
        // question_multianswer too
        if ($questioncreated) {
            // Adjust some columns
            $data->question = $newquestionid;
            // Note: multianswer->sequence is a list of question->id values. We aren't
            // recoding them here (because some questions can be missing yet). Instead
            // we'll perform the recode in the {@link after_execute} method of the plugin
            // that gets executed once all questions have been created
            // Insert record
            $newitemid = $DB->insert_record('question_multianswer', $data);
            // Create mapping (need it for after_execute recode of sequence)
            $this->set_mapping('question_multianswer', $oldid, $newitemid);
        }
    }

    /**
     * This method is executed once the whole restore_structure_step
     * this step is part of ({@link restore_create_categories_and_questions})
     * has ended processing the whole xml structure. Its name is:
     * "after_execute_" + connectionpoint ("question")
     *
     * For multianswer qtype we use it to restore the sequence column,
     * containing one list of question ids
     */
    public function after_execute_question() {
        global $DB;
        // Now that all the questions have been restored, let's process
        // the created question_multianswer sequences (list of question ids)
        $rs = $DB->get_recordset_sql("
                SELECT qma.id, qma.sequence
                  FROM {question_multianswer} qma
                  JOIN {backup_ids_temp} bi ON bi.newitemid = qma.question
                 WHERE bi.backupid = ?
                   AND bi.itemname = 'question_created'",
                array($this->get_restoreid()));
        foreach ($rs as $rec) {
            $sequencearr = explode(',', $rec->sequence);
            foreach ($sequencearr as $key => $question) {
                $sequencearr[$key] = $this->get_mappingid('question', $question);
            }
            $sequence = implode(',', $sequencearr);
            $DB->set_field('question_multianswer', 'sequence', $sequence,
                    array('id' => $rec->id));
        }
        $rs->close();
    }

    public function recode_response($questionid, $sequencenumber, array $response) {
        global $DB;

        $qtypes = $DB->get_records_menu('question', array('parent' => $questionid),
                '', 'id, qtype');

        $sequence = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $questionid));

        $fakestep = new question_attempt_step_read_only($response);

        foreach (explode(',', $sequence) as $key => $subqid) {
            $i = $key + 1;

            $substep = new question_attempt_step_subquestion_adapter($fakestep, 'sub' . $i . '_');
            $recodedresponse = $this->step->questions_recode_response_data($qtypes[$subqid],
                    $subqid, $sequencenumber, $substep->get_all_data());

            foreach ($recodedresponse as $name => $value) {
                $response[$substep->add_prefix($name)] = $value;
            }
        }

        return $response;
    }

    /**
     * Given one question_states record, return the answer
     * recoded pointing to all the restored stuff for multianswer questions
     *
     * answer is one comma separated list of hypen separated pairs
     * containing sequence (pointing to questions sequence in question_multianswer)
     * and mixed answers. We'll delegate
     * the recoding of answers to the proper qtype
     */
    public function recode_legacy_state_answer($state) {
        global $DB;
        $answer = $state->answer;
        $resultarr = array();
        // Get sequence of questions
        $sequence = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $state->question));
        $sequencearr = explode(',', $sequence);
        // Let's process each pair
        foreach (explode(',', $answer) as $pair) {
            $pairarr = explode('-', $pair);
            $sequenceid = $pairarr[0];
            $subanswer = $pairarr[1];
            // Calculate the questionid based on sequenceid
            // Note it is already one *new* questionid that doesn't need mapping
            $questionid = $sequencearr[$sequenceid-1];
            // Fetch qtype of the question (needed for delegation)
            $questionqtype = $DB->get_field('question', 'qtype', array('id' => $questionid));
            // Delegate subanswer recode to proper qtype, faking one question_states record
            $substate = new stdClass();
            $substate->question = $questionid;
            $substate->answer = $subanswer;
            $newanswer = $this->step->restore_recode_legacy_answer($substate, $questionqtype);
            $resultarr[] = implode('-', array($sequenceid, $newanswer));
        }
        return implode(',', $resultarr);
    }

}
