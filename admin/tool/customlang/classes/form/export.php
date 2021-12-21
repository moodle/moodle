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
 * Creates Formular for customlang file export
 *
 * @package    tool_customlang
 * @copyright  2020 Thomas Wedekind <Thomas.Wedekind@univie.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_customlang\form;

use tool_customlang_utils;

/**
 * Formular for customlang file export
 *
 * @copyright  2020 Thomas Wedekind <Thomas.Wedekind@univie.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export extends \moodleform {

    /**
     * Add elements to form
     */
    public function definition() {
        $lng = $this->_customdata['lng'];
        $mform = $this->_form;

        $langdir = tool_customlang_utils::get_localpack_location($lng);

        // The export button only appears if a local lang is present.
        if (!check_dir_exists($langdir) || !count(glob("$langdir/*"))) {
            print_error('nolocallang', 'tool_customlang');
        }

        $langfiles = scandir($langdir);
        $fileoptions = [];
        foreach ($langfiles as $file) {
            if (substr($file, 0, 1) != '.') {
                $fileoptions[$file] = $file;
            }
        }

        $mform->addElement('hidden', 'lng', $lng);
        $mform->setType('lng', PARAM_LANG);

        $select = $mform->addElement('select', 'files', get_string('exportfilter', 'tool_customlang'), $fileoptions);
        $select->setMultiple(true);
        $mform->addRule('files', get_string('required'), 'required', null, 'client');
        $mform->setDefault('files', $fileoptions);

        $this->add_action_buttons(true, get_string('export', 'tool_customlang'));
    }
}
