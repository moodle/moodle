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
 * This file contains the form definition for discussion export.
 *
 * @package   mod_forum
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\form;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Export discussion form.
 *
 * @package   mod_forum
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class export_form extends \moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;
        $forum = $this->_customdata['forum'];

        $mform->addElement('hidden', 'export');
        $mform->setType('export', PARAM_BOOL);
        $mform->setDefault('export', true);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $forum->get_id());

        // Export formats.
        $formats = \core_plugin_manager::instance()->get_plugins_of_type('dataformat');
        $options = [];
        foreach ($formats as $format) {
            $options[$format->name] = $format->displayname;
        }
        $mform->addElement('select', 'format', 'Format', $options);
        $this->add_action_buttons(true, get_string('export', 'mod_forum'));
    }
}
