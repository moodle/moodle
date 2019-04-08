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
 * External backpack form
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\form;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Backpack form class.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_backpack extends \moodleform {

    /**
     * Create the form.
     *
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $backpack = false;

        if (isset($this->_customdata['externalbackpack'])) {
            $backpack = $this->_customdata['externalbackpack'];
        } else {
            throw new \coding_exception('backpack is required.');
        }

        $url = $backpack->backpackapiurl;

        $mform->addElement('static', 'backpackapiurlinfo', get_string('backpackapiurl', 'core_badges'), $url);

        $mform->addElement('hidden', 'backpackapiurl', $url);
        $mform->setType('backpackapiurl', PARAM_URL);

        $url = $backpack->backpackweburl;
        $mform->addElement('static', 'backpackweburlinfo', get_string('backpackweburl', 'core_badges'), $url);
        $mform->addElement('hidden', 'backpackweburl', $url);
        $mform->setType('backpackweburl', PARAM_URL);

        $options = badges_get_badge_api_versions();
        $label = $options[$backpack->apiversion];
        $mform->addElement('static', 'apiversioninfo', get_string('apiversion', 'core_badges'), $label);
        $mform->addElement('hidden', 'apiversion', $backpack->apiversion);
        $mform->setType('apiversion', PARAM_INTEGER);

        $mform->addElement('hidden', 'id', $backpack->id);
        $mform->setType('id', PARAM_INTEGER);

        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHA);

        $issuername = $CFG->badges_defaultissuername;
        $mform->addElement('static', 'issuerinfo', get_string('defaultissuername', 'core_badges'), $issuername);

        $issuercontact = $CFG->badges_defaultissuercontact;
        $mform->addElement('static', 'issuerinfo', get_string('defaultissuercontact', 'core_badges'), $issuercontact);

        $mform->addElement('passwordunmask', 'password', get_string('defaultissuerpassword', 'core_badges'));
        $mform->setType('password', PARAM_RAW);
        $mform->addHelpButton('password', 'defaultissuerpassword', 'badges');
        $mform->hideIf('password', 'apiversion', 'eq', 1);

        $this->set_data($backpack);

        // Disable short forms.
        $mform->setDisableShortforms();

        $this->add_action_buttons();
    }

}
