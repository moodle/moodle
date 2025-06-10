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

class backup_qtype_multichoicewiris_plugin extends backup_qtype_multichoice_plugin {

    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {
        // Call parent.
        $plugin = parent::define_question_plugin_structure();

        // Change type.
        $plugin->set_condition('../../qtype', 'multichoicewiris');

        // Add questionxml.
        $pluginwrapper = $plugin->get_child($this->get_recommended_name());
        $questionxml = new backup_nested_element('question_xml', array('id'), array('xml'));
        $pluginwrapper->add_child($questionxml);
        $questionxml->set_source_table('qtype_wq', array('question' => backup::VAR_PARENTID));

        return $plugin;
    }
}
