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
 * This file contains the forms to set the allocated marker for selected submissions.
 *
 * @package   mod_assign
 * @copyright 2013 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/assign/feedback/file/locallib.php');
/**
 * Set allocated marker form.
 *
 * @package   mod_assign
 * @copyright 2013 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_batch_set_allocatedmarker_form extends moodleform {
    /**
     * Define this form - called by the parent constructor
     * @throws moodle_exception
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;
        $params = $this->_customdata;

        $mform->addElement('header', 'general', get_string('batchsetallocatedmarker', 'assign', $params['userscount']));
        $mform->addElement('static', 'userslist', get_string('selectedusers', 'assign'), $params['usershtml']);

        $options = $params['markers'];

        $requiredmarkercount = (!empty($params['markercount'])) ? $params['markercount'] : 1;
        $optionalmarkercount = (!empty($params['optionalmarkercount'])) ? $params['optionalmarkercount'] : 0;
        $totalmarkercount = $requiredmarkercount + $optionalmarkercount;
        $markerids = array_keys($options);

        // If we do not have enough markers to meet the minimum required markers, throw an exception with a meaningful message.
        if (count($markerids) < $requiredmarkercount) {
            throw new \core\exception\moodle_exception('invalidmarkerallocation:notenoughmarkers', 'assign', '', [
                'markers' => count($markerids),
                'requested' => $requiredmarkercount,
            ]);
        }

        // If multi marking is enabled and we have existing grades, warn the user about potential updates.
        if (!empty($params['agreedgrades'])) {
            $notification = $OUTPUT->notification(get_string('changeallocatedmarkersaftergrade', 'mod_assign'), 'warning', false);
            $mform->addElement('html', $notification);
        }

        $manageoptional = !empty($params['context']) && has_capability('mod/assign:manageoptionalallocations', $params['context']);
        $options = ['' => get_string('choosemarker', 'assign')] + $options;

        for ($i = 1; $i <= $totalmarkercount; $i++) {
            $group = [];
            if ($i > $requiredmarkercount) {
                // Add checkbox for optional markers.
                $checkbox = $mform->createElement('advcheckbox', "allocatedmarkerenabled[$i]", '', get_string('enable'));
                if (!$manageoptional) {
                    $checkbox->freeze();
                }
                $group[] = $checkbox;
                $mform->disabledIf("allocatedmarker[$i]", "allocatedmarkerenabled[$i]", 'eq', 0);
            }
            $name = get_string('allocatedmarker', 'assign') . ' ' . $i;
            $group[] = $mform->createElement('select', "allocatedmarker[$i]", $name, $options);
            $mform->addGroup($group, "allocatedmarkergroup[$i]", $name, null, false);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'setbatchmarkingallocation');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'selectedusers');
        $mform->setType('selectedusers', PARAM_SEQUENCE);
        $this->add_action_buttons(true, get_string('savechanges'));

    }

}

