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
 * Form to allow user to set their default home page
 *
 * @package     core_user
 * @copyright   2019 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\form;

use lang_string;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form class
 *
 * @copyright   2019 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class defaulthomepage_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $options = [HOMEPAGE_SITE => new lang_string('home')];
        if (!empty($CFG->enabledashboard)) {
            $options[HOMEPAGE_MY] = new lang_string('mymoodle', 'admin');
        }
        $options[HOMEPAGE_MYCOURSES] = new lang_string('mycourses', 'admin');

        $mform->addElement('select', 'defaulthomepage', get_string('defaulthomepageuser'), $options);
        $mform->addHelpButton('defaulthomepage', 'defaulthomepageuser');
        $mform->setDefault('defaulthomepage', get_default_home_page());

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
