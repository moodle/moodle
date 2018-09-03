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
 * Class site_unregistration_form
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\hub;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * This form display a unregistration form.
 *
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class site_unregistration_form extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;
        $mform->addElement('header', 'site', get_string('unregister', 'hub'));

        $unregisterlabel = get_string('unregister', 'hub');
        $mform->addElement('advcheckbox', 'unpublishalladvertisedcourses', '',
            ' ' . get_string('unpublishalladvertisedcourses', 'hub'));
        $mform->setType('unpublishalladvertisedcourses', PARAM_INT);
        $mform->addElement('advcheckbox', 'unpublishalluploadedcourses', '',
            ' ' . get_string('unpublishalluploadedcourses', 'hub'));
        $mform->setType('unpublishalluploadedcourses', PARAM_INT);

        $mform->addElement('hidden', 'unregistration', 1);
        $mform->setType('unregistration', PARAM_INT);

        $mform->addElement('static', 'explanation', '', get_string('unregisterexplained', 'hub', $CFG->wwwroot));

        $this->add_action_buttons(true, $unregisterlabel);
    }
}
