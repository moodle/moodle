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

class restore_qtype_multichoicewiris_plugin extends restore_qtype_multichoice_plugin {

    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'multichoice';
        $xmlname = 'qtype_wq_multichoicewiris';

        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/multichoice');
        $xmlpath = $this->get_pathfor('/question_xml');

        $paths[] = new restore_path_element($elename, $elepath);
        $paths[] = new restore_path_element($xmlname, $xmlpath);

        return $paths; // And we return the interesting paths.
    }

    public static function convert_backup_to_questiondata(array $backupdata): \stdClass {

        // Moodle abstract implementation for this function assumes that the qtype plugin options are stored in the
        // ['plugin_qtype_{qtypename}_question']['{qtypename}'] array, so we need map the options from the base qtype.
        if (isset($backupdata['plugin_qtype_multichoicewiris_question']['multichoice'])) {
            $backupdata['plugin_qtype_multichoicewiris_question']['multichoicewiris'] = $backupdata['plugin_qtype_multichoicewiris_question']['multichoice'];
        }

        // Convert the backup data to question data.
        $questiondata = parent::convert_backup_to_questiondata($backupdata);

        // Include Wiris question XML if it exists.
        if (isset($backupdata['plugin_qtype_multichoicewiris_question']['question_xml'][0]['xml'])) {
            $questiondata->options->wirisquestion = $backupdata['plugin_qtype_multichoicewiris_question']['question_xml'][0]['xml'];
        }

        return $questiondata;
    }

    protected function define_excluded_identity_hash_fields(): array {
        // Only truefalsewiris uses wirisoptions. Exclude them for other qtypes.
        return array_merge(
            parent::define_excluded_identity_hash_fields(),
            [
                '/options/wirisoptions'
            ]
        );
    }

    public function process_qtype_wq_multichoicewiris($data) {
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


    protected function decode_html_entities($xml) {
        $htmlentitiestable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, 'UTF-8');
        $xmlentitiestable = get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT, 'UTF-8');
        $entitiestable = array_diff($htmlentitiestable, $xmlentitiestable);
        $decodetable = array_flip($entitiestable);
        $xml = str_replace(array_keys($decodetable), array_values($decodetable), $xml);
        return $xml;
    }
}
