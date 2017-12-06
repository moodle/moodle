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
 * @package block_dataformnotification
 * @copyright 2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_dataformnotification_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        $dataformid = $this->block->dataformid;
        $ruleformhelper = '\mod_dataform\helper\ruleform';
        $notificationformhelper = '\mod_dataform\helper\notificationform';
        $filterformhelper = '\mod_dataform\helper\filterform';

        // Common elements.
        $ruleformhelper::general_definition($mform, $dataformid, 'config_');

        // Events.
        $mform->addElement('header', 'eventshdr', get_string('event', 'dataform'));
        $mform->setExpanded('eventshdr');

        $events = $this->block->get_applicable_events();
        $select = &$mform->addElement('select', 'config_events', get_string('events', 'dataform'), $events);
        $select->setMultiple(true);
        $mform->addRule('config_events', null, 'required', null, 'client');

        // Notification.
        $notificationformhelper::notification_definition($mform, $dataformid, 'config_');

        // Filter.
        $mform->addElement('header', 'filterhdr', get_string('filter', 'dataform'));
        $mform->setExpanded('filterhdr');

        // Filter selector.
        $filterformhelper::filter_selection_definition($mform, $dataformid, 'config_');

        // Custom search.
        $config = $this->block->config;
        $customsearch = !empty($config->customsearch) ? $config->customsearch : null;
        $filterformhelper::custom_search_definition($mform, $dataformid, $customsearch);
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Content.
            if ($data->config_contentview) {
                $data->config_contenttext = null;
            }

            // Custom search.
            $filterformhelper = '\mod_dataform\helper\filterform';
            if ($customsearch = $filterformhelper::get_custom_search_from_form($data, $this->block->dataformid)) {
                $data->config_customsearch = $customsearch;
            } else {
                $data->config_customsearch = null;
            }
        }

        return $data;
    }

    /**
     *
     */
    public function validation($data, $files) {
        if ($errors = parent::validation($data, $files)) {
            return $errors;
        }

        $notificationformhelper = '\mod_dataform\helper\notificationform';
        if ($errors = $notificationformhelper::general_validation($data, $files, 'config_')) {
            return $errors;
        }

        if ($errors = $notificationformhelper::notification_validation($data, $files, 'config_')) {
            return $errors;
        }

    }
}
