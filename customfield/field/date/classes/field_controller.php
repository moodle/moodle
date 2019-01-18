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
 * Customfield date plugin
 *
 * @package   customfield_date
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_date;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package customfield_date
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller extends \core_customfield\field_controller {
    /**
     * Type of plugin data
     */
    const TYPE = 'date';

    /**
     * Validate the data from the config form.
     *
     * @param array $data
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, $files = array()) : array {
        $errors = array();

        // Make sure the start year is not greater than the end year.
        if (!empty($data['configdata']['mindate']) && !empty($data['configdata']['maxdate']) &&
                $data['configdata']['mindate'] > $data['configdata']['maxdate']) {
            $errors['configdata[mindate]'] = get_string('mindateaftermax', 'customfield_date');
        }

        return $errors;
    }

    /**
     * Add fields for editing a date field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $config = $this->get('configdata');

        // Add elements.
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_date'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement('advcheckbox', 'configdata[includetime]', get_string('includetime', 'customfield_date'));

        $mform->addElement('date_time_selector', 'configdata[mindate]', get_string('mindate', 'customfield_date'),
            ['optional' => true]);

        $mform->addElement('date_time_selector', 'configdata[maxdate]', get_string('maxdate', 'customfield_date'),
            ['optional' => true]);

        $mform->hideIf('configdata[maxdate][hour]', 'configdata[includetime]');
        $mform->hideIf('configdata[maxdate][minute]', 'configdata[includetime]');
        $mform->hideIf('configdata[mindate][hour]', 'configdata[includetime]');
        $mform->hideIf('configdata[mindate][minute]', 'configdata[includetime]');
    }
}
