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

class restore_qtype_shortanswerwiris_plugin extends restore_qtype_shortanswer_plugin {

    /**
     * Returns the paths to be handled by the plugin at question levelm
     */
    protected function define_question_plugin_structure() {
        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'shortanswer';
        $shortanswerwiriselement = 'shortanswerwiris';
        $xmlname = 'qtype_wq_shortanswerwiris';
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/shortanswer');
        $shortanswerwirispath = $this->get_pathfor('/shortanswerwiris');
        $xmlpath = $this->get_pathfor('/question_xml');

        $paths[] = new restore_path_element($elename, $elepath);
        $paths[] = new restore_path_element($shortanswerwiriselement, $shortanswerwirispath);
        $paths[] = new restore_path_element($xmlname, $xmlpath);
        return $paths; // And we return the interesting paths.
    }

    public function process_qtype_wq_shortanswerwiris($data) {
        global $DB;

        $data = (object)$data;
        $data->xml = $this->decode_html_entities($data->xml);
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to fill
        // qtype_wq tables too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('qtype_wq', $data);
            // Create mapping.
            $this->set_mapping('qtype_wq', $oldid, $newitemid);
        }

    }

    /**
     * Recode answer identifiers from get_matching_answer cache.
     */
    public function recode_response($questionid, $sequencenumber, array $response) {
        if (array_key_exists('_matching_answer', $response)) {
            $response['_matching_answer'] = $this->get_mappingid('question_answer', $response['_matching_answer']);
        }
        return $response;
    }

    protected function decode_html_entities($xml) {
        $htmlentitiestable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, 'UTF-8');
        $xmlentitiestable = get_html_translation_table(HTML_SPECIALCHARS , ENT_COMPAT, 'UTF-8');
        $entitiestable = array_diff($htmlentitiestable, $xmlentitiestable);
        $decodetable = array_flip($entitiestable);
        $xml = str_replace(array_keys($decodetable), array_values($decodetable), $xml);
        return $xml;
    }

    public function process_shortanswerwiris($data) {
               $this->really_process_extra_question_fields($data);
    }

}
