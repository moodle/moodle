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
 * This file contains the defaults form.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\form;
defined('MOODLE_INTERNAL') || die();

use \tool_dataprivacy\output\data_registry_page;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Context levels defaults form.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class defaults extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;
        $mform->setDisableShortforms();

        $notification = $OUTPUT->notification(get_string('defaultsinfo', 'tool_dataprivacy'),
            \core\output\notification::NOTIFY_INFO);
        $mform->addElement('html', $notification);

        foreach ($this->_customdata['levels'] as $level => $classname) {

            $mform->addElement('header', $classname . '-header',
                get_string('contextlevelname' . $level, 'tool_dataprivacy'));

            list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);

            $includeinherit = true;
            if ($level == CONTEXT_SYSTEM) {
                $includeinherit = false;
            }

            $categoryoptions = data_registry_page::category_options($this->_customdata['categories'], false, $includeinherit);
            $purposeoptions = data_registry_page::category_options($this->_customdata['purposes'], false, $includeinherit);

            $mform->addElement('select', $categoryvar, get_string('category', 'tool_dataprivacy'), $categoryoptions);
            $mform->addHelpButton($categoryvar, 'categorydefault', 'tool_dataprivacy');
            $mform->setType($categoryvar, PARAM_INT);

            $mform->addElement('select', $purposevar, get_string('purpose', 'tool_dataprivacy'), $purposeoptions);
            $mform->addHelpButton($purposevar, 'purposedefault', 'tool_dataprivacy');
            $mform->setType($purposevar, PARAM_INT);
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
