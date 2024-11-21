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
 * Block XP user edit form.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

use core_form\dynamic_form;

/**
 * Block XP user edit form class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_xp extends dynamic_form {

    use dynamic_world_trait;

    /** @var string */
    protected $routename = 'report';

    /**
     * Get the state.
     *
     * This will throw an exception if the state does not already exist for the user.
     *
     * @return \block_xp\local\xp\state
     */
    protected function get_state() {
        $userid = $this->optional_param('userid', 0, PARAM_INT);
        return $this->get_world()->get_store()->get_state($userid);
    }

    public function process_dynamic_submission() {
        $state = $this->get_state(); // Acts as validation.
        $data = $this->get_data();
        $this->get_world()->get_store()->set($state->get_id(), $data->xp);
    }

    public function set_data_for_dynamic_submission(): void {
        $userid = $this->optional_param('userid', 0, PARAM_INT);
        $state = $this->get_state();
        $this->set_data([
            'userid' => $userid,
            'level' => $state->get_level()->get_level(),
            'xp' => $state->get_xp(),
        ]);
    }

    /**
     * Form definintion.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $mform->setDisableShortforms(true);

        if ($this->_ajaxformdata) {
            $mform->addElement('hidden', 'contextid', $this->get_world()->get_context()->id);
            $mform->setType('contextid', PARAM_INT);
        }

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('text', 'level', get_string('level', 'block_xp'));
        $mform->setType('level', PARAM_INT);
        $mform->hardFreeze('level');

        $mform->addElement('text', 'xp', get_string('total', 'block_xp'));
        $mform->setType('xp', PARAM_INT);

        if (!$this->_ajaxformdata) {
            $this->add_action_buttons();
        }
    }

    /**
     * Data validate.
     *
     * @param array $data The data submitted.
     * @param array $files The files submitted.
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = [];

        // Validating the XP points.
        $xp = (int) $data['xp'];
        if ($xp < 0) {
            $errors['xp'] = get_string('invalidxp', 'block_xp');
        }

        return $errors;
    }

}
