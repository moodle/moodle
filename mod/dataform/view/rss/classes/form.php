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
 * @package dataformview
 * @subpackage rss
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_rss_form extends mod_dataform\pluginbase\dataformviewform {

    /**
     *
     */
    protected function definition_view_specific() {
        // View template.
        $this->definition_view_template();

        // Item (entry) template.
        $this->definition_entry_template();

        // Feed template.
        $this->definition_feed_template();

        // Submission.
        $this->definition_view_submission();
    }

    /**
     *
     */
    protected function definition_feed_template() {
        $view = $this->_view;
        $df = $view->get_df();
        $editoroptions = $view->editoroptions;
        $paramtext = !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN;

        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'feedtemplatehdr', get_string('feedtemplate', 'dataformview_rss'));
        $mform->addHelpButton('feedtemplatehdr', 'feedtemplate', 'dataformview_rss');

        // Feed view (param5).
        $options = array('' => get_string('choosedots'));
        if ($viewsmenu = $df->view_manager->views_menu) {
            $options = $options + $viewsmenu;
        }
        $mform->addElement('select', 'param5', get_string('feedview', 'dataformview_rss'), $options);
        $mform->addHelpButton('param5', 'feedview', 'dataformview_rss');

        // Feed title (param4).
        $mform->addElement('text', 'param4', get_string('feedtitle', 'dataformview_rss'), array('size' => 64));
        $mform->setType('param4', $paramtext);
        $mform->addHelpButton('param4', 'feedtitle', 'dataformview_rss');

        // Feed description (param6).
        $mform->addElement('editor', 'param6_editor', get_string('feeddescription', 'dataformview_rss'), null, $editoroptions);
        $mform->addHelpButton('param6_editor', 'feeddescription', 'dataformview_rss');
        $this->add_patterns_selectors('param6_editor', array('view'));
    }

    /**
     *
     */
    protected function definition_entry_template() {
        $view = $this->_view;
        $df = $view->get_df();
        $editoroptions = $view->editoroptions;
        $paramtext = !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN;

        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'entrytemplatehdr', get_string('itemtemplate', 'dataformview_rss'));
        $mform->addHelpButton('entrytemplatehdr', 'itemtemplate', 'dataformview_rss');

        // Item view (param3).
        $options = array('' => get_string('choosedots'));
        if ($viewsmenu = $df->view_manager->views_menu) {
            $options = $options + $viewsmenu;
        }
        $mform->addElement('select', 'param3', get_string('itemview', 'dataformview_rss'), $options);
        $mform->addHelpButton('param3', 'itemview', 'dataformview_rss');

        // Item title (param1).
        $mform->addElement('text', 'param1', get_string('itemtitle', 'dataformview_rss'), array('size' => 64));
        $mform->setType('param1', $paramtext);
        $mform->addHelpButton('param1', 'itemtitle', 'dataformview_rss');

        // Item description (param2).
        $mform->addElement('editor', 'param2_editor', get_string('itemdescription', 'dataformview_rss'), null, $editoroptions);
        $mform->addHelpButton('param2_editor', 'itemdescription', 'dataformview_rss');
        $this->add_patterns_selectors('param2_editor', array('field'));
    }

}
