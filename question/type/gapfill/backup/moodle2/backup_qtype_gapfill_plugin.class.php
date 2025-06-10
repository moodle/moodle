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
 * Gapfill question type backup
 *
 * @package    qtype_gapfill
 * @subpackage backup-moodle2
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * Provides steps to perform one complete backup of a gapfill question instance
 *
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_gapfill_plugin extends backup_qtype_plugin {

    /**
     * returns the name of the plugin/question type
     *
     * @return string
     */
    protected static function qtype_name() {
        return 'gapfill';
    }
    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {
        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'gapfill');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        /* This qtype uses standard question_answers, add them here
         to the tree before any other information that will use them */
         $this->add_question_question_answers($pluginwrapper);

        // Now create the qtype own structures.
        $gapfill = new backup_nested_element('gapfill', array('id'), array(
            'answerdisplay', 'delimitchars', 'casesensitive', 'noduplicates', 'disableregex',
            'fixedgapsize', 'optionsaftertext', 'letterhints', 'singleuse', 'correctfedback', 'correctfeddbackformat',
            'partiallycorrectfeedback', 'partiallycorrectfeedbackformat', 'incorrectfeedback', 'incorrectfeedbackformat'));

        $gapsettings = new backup_nested_element('gapsettings');
        $gapsetting = new backup_nested_element('gapsetting', array('id'), array('questionid', 'itemid',
                'gaptext', 'correctfeedback', 'incorrectfeedback'));

        // Now the own qtype tree.
        $pluginwrapper->add_child($gapfill);

        $pluginwrapper->add_child($gapsettings);
        $gapsettings->add_child($gapsetting);

        // Set source to populate the data.
        $gapfill->set_source_table('question_gapfill',
                array('question' => backup::VAR_PARENTID));
         // Set source to populate the data.
        $gapsetting->set_source_table('question_gapfill_settings',
                array('question' => backup::VAR_PARENTID));

        // Don't need to annotate ids nor files.

        return $plugin;
    }

    /**
     * Returns one array with filearea => mappingname elements for the qtype
     *
     * files to be processed both in backup and restore.
     */
    public static function get_qtype_fileareas() {
        return array(
            'correctfeedback' => 'question_created',
            'partiallycorrectfeedback' => 'question_created',
            'incorrectfeedback' => 'question_created');
    }
}
