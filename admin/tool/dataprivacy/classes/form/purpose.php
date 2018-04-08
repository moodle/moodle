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
 * This file contains the form add/update a data purpose.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\form;
defined('MOODLE_INTERNAL') || die();

use core\form\persistent;

/**
 * Data purpose form.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose extends persistent {

    /**
     * @var The persistent class.
     */
    protected static $persistentclass = 'tool_dataprivacy\\purpose';

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="100"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'server');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'server');

        $mform->addElement('editor', 'description', get_string('description'), null, ['autosave' => false]);
        $mform->setType('description', PARAM_CLEANHTML);

        $number = $mform->createElement('text', 'retentionperiodnumber', null, ['size' => 8]);
        $unitoptions = [
            'Y' => get_string('years'),
            'M' => strtolower(get_string('months')),
            'D' => strtolower(get_string('days'))
        ];
        $unit = $mform->createElement('select', 'retentionperiodunit', '', $unitoptions);
        $mform->addGroup(['number' => $number, 'unit' => $unit], 'retentionperiod',
            get_string('retentionperiod', 'tool_dataprivacy'), null, false);
        $mform->setType('retentionperiodnumber', PARAM_INT);

        $this->_form->addElement('advcheckbox', 'protected', get_string('protected', 'tool_dataprivacy'),
            get_string('protectedlabel', 'tool_dataprivacy'));

        if (!empty($this->_customdata['showbuttons'])) {
            if (!$this->get_persistent()->get('id')) {
                $savetext = get_string('add');
            } else {
                $savetext = get_string('savechanges');
            }
            $this->add_action_buttons(true, $savetext);
        }
    }

    /**
     * Converts fields.
     *
     * @param \stdClass $data
     * @return \stdClass
     */
    protected static function convert_fields(\stdClass $data) {
        $data = parent::convert_fields($data);

        // A single value.
        $data->retentionperiod = 'P' . $data->retentionperiodnumber . $data->retentionperiodunit;
        unset($data->retentionperiodnumber);
        unset($data->retentionperiodunit);
        return $data;
    }

    /**
     * Get the default data.
     *
     * @return \stdClass
     */
    protected function get_default_data() {
        $data = parent::get_default_data();

        // Convert the single properties into number and unit.
        $strlen = strlen($data->retentionperiod);
        $data->retentionperiodnumber = substr($data->retentionperiod, 1, $strlen - 2);
        $data->retentionperiodunit = substr($data->retentionperiod, $strlen - 1);
        unset($data->retentionperiod);

        return $data;
    }
}
