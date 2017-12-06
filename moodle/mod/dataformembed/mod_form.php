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
 * @package    mod_dataformembed
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_dataformembed_mod_form extends moodleform_mod {

    public function definition() {
        global $DB, $SITE, $CFG, $PAGE;
        $mform = $this->_form;

        // Buttons.
        // -------------------------------------------------------------------------------
        $this->add_action_buttons();

        // Fields for editing HTML block title and contents.
        // --------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Intro.
        $this->standard_intro_elements(false, get_string('description'));

        // Dataforms menu.
        $options = array(0 => get_string('choosedots'));
        $courseids = array($SITE->id, $this->current->course);
        list($insql, $params) = $DB->get_in_or_equal($courseids);
        if ($dataforms = $DB->get_records_select_menu('dataform', " course $insql ", $params, 'name', 'id,name')) {
            foreach ($dataforms as $key => $value) {
                $dataforms[$key] = strip_tags(format_string($value, true));
            }
            $options = $options + $dataforms;
        }
        $mform->addElement('select', 'dataform', get_string('selectdataform', 'dataformembed'), $options);

        // Views menu.
        $options = array(0 => get_string('choosedots'));
        $mform->addElement('select', "view", get_string('selectview', 'dataformembed'), $options);
        $mform->disabledIf("view", "dataform", 'eq', 0);

        // Filters menu.
        $options = array(0 => get_string('choosedots'));
        $mform->addElement('select', 'filterid', get_string('selectfilter', 'dataformembed'), $options);
        $mform->disabledIf('filterid', "dataform", 'eq', 0);

        // Embed.
        $mform->addElement('selectyesno', "embed", get_string('embed', 'dataform'));

        // Style.
        $mform->addElement('text', 'style', get_string('style', 'dataformembed'), array('size' => '64'));
        $mform->setType('style', PARAM_TEXT);
        $mform->disabledIf('style', 'embed', 'eq', 0);

        $this->standard_coursemodule_elements();

        // Buttons.
        // -------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    /**
     *
     */
    public function definition_after_data() {
        global $DB;

        if ($selectedarr = $this->_form->getElement('dataform')->getSelected()) {
            $dataformid = reset($selectedarr);
        } else {
            $dataformid = 0;
        }

        if ($selectedarr = $this->_form->getElement('view')->getSelected()) {
            $viewid = reset($selectedarr);
        } else {
            $viewid = 0;
        }

        if ($dataformid) {
            if ($views = $DB->get_records_menu('dataform_views', array('dataid' => $dataformid), 'name', 'id,name')) {
                $configview = &$this->_form->getElement('view');
                foreach ($views as $key => $value) {
                    $configview->addOption(strip_tags(format_string($value, true)), $key);
                }
            }

            if ($filters = $DB->get_records_menu('dataform_filters', array('dataid' => $dataformid), 'name', 'id,name')) {
                $configfilter = &$this->_form->getElement('filterid');
                foreach ($filters as $key => $value) {
                    $configfilter->addOption(strip_tags(format_string($value, true)), $key);
                }
            }
        }
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $data = (array) $data;
        parent::data_preprocessing($data);

        // Set filterid from filter.
        $data['filterid'] = !empty($data['filter']) ? $data['filter'] : 0;
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     *
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Get filter from filterid.
        $data->filter = empty($data->filterid) ? 0 : $data->filterid;

        return $data;
    }

    /**
     *
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $errors = array();

        if (!empty($data['dataform']) and empty($data['view'])) {
            $errors['view'] = get_string('missingview', 'dataformembed');
        }

        return $errors;
    }
}
