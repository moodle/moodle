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
 * Form class for mybackpack.php
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace core_badges\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');

use html_writer;
use moodleform;

/**
 * Form to select backpack collections.
 *
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collections extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;
        $email = $this->_customdata['email'];
        $backpackweburl = $this->_customdata['backpackweburl'];
        $selected = $this->_customdata['selected'];

        if (isset($this->_customdata['groups'])) {
            $groups = $this->_customdata['groups'];
            $nogroups = null;
        } else {
            $groups = null;
            $nogroups = $this->_customdata['nogroups'];
        }

        $backpack = get_backpack_settings($USER->id);
        $sitebackpack = badges_get_site_backpack($backpack->backpackid);

        $mform->addElement('header', 'backpackheader', get_string('backpackconnection', 'badges'));
        $mform->addHelpButton('backpackheader', 'backpackconnection', 'badges');
        $mform->addElement('static', 'url', get_string('url'), $backpackweburl);

        $status = html_writer::tag('span', get_string('connected', 'badges'), array('class' => 'connected'));
        $mform->addElement('static', 'status', get_string('status'), $status);
        $mform->addElement('static', 'email', get_string('email'), $email);
        $mform->addHelpButton('email', 'backpackemail', 'badges');
        $mform->addElement('submit', 'disconnect', get_string('disconnect', 'badges'));

        $mform->addElement('header', 'collectionheader', get_string('backpackimport', 'badges'));
        $mform->addHelpButton('collectionheader', 'backpackimport', 'badges');

        $hasgroups = false;
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $count = 0;
                // Handle attributes based on backpack's supported version.
                if ($sitebackpack->apiversion == OPEN_BADGES_V2) {
                    // OpenBadges v2 data attributes.
                    if (empty($group->published)) {
                        // Only public collections.
                        continue;
                    }

                    // Get the number of badges associated with this collection from the assertions array returned.
                    $count = count($group->assertions);
                } else {
                    // OpenBadges v1 data attributes.
                    $group->entityId = $group->groupId;

                    // Get the number of badges associated with this collection. In that case, the number is returned directly.
                    $count = $group->badges;
                }

                if (!$hasgroups) {
                    $mform->addElement('static', 'selectgroup', '', get_string('selectgroup_start', 'badges'));
                }
                $hasgroups = true;
                $name = $group->name . ' (' . $count . ')';
                $mform->addElement(
                    'advcheckbox',
                    'group[' . $group->entityId . ']',
                    null,
                    $name,
                    array('group' => 1),
                    array(false, $group->entityId)
                );
                if (in_array($group->entityId, $selected)) {
                    $mform->setDefault('group[' . $group->entityId . ']', $group->entityId);
                }
            }
            $mform->addElement('static', 'selectgroup', '', get_string('selectgroup_end', 'badges', $backpackweburl));
        }
        if (!$hasgroups) {
            $mform->addElement('static', 'selectgroup', '', $nogroups);
        }

        $this->add_action_buttons();
    }
}
