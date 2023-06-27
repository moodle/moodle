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
 * Upload a zip of custom lang php files.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\form;

use tool_customlang\local\importer;

/**
 * Upload a zip/php of custom lang php files.
 *
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'settingsheader', get_string('import', 'tool_customlang'));

        $mform->addElement('hidden', 'lng');
        $mform->setType('lng', PARAM_LANG);
        $mform->setDefault('lng', $this->_customdata['lng']);

        $filemanageroptions = array(
            'accepted_types' => array('.php', '.zip'),
            'maxbytes' => 0,
            'maxfiles' => 1,
            'subdirs' => 0
        );

        $mform->addElement('filepicker', 'pack', get_string('langpack', 'tool_customlang'),
            null, $filemanageroptions);
        $mform->addRule('pack', null, 'required');

        $modes = [
            importer::IMPORTALL => get_string('import_all', 'tool_customlang'),
            importer::IMPORTUPDATE => get_string('import_update', 'tool_customlang'),
            importer::IMPORTNEW => get_string('import_new', 'tool_customlang'),
        ];
        $mform->addElement('select', 'importmode', get_string('import_mode', 'tool_customlang'), $modes);

        $mform->addElement('submit', 'importcustomstrings', get_string('importfile', 'tool_customlang'));
    }
}
