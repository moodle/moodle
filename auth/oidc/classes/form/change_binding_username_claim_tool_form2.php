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
 * Change binding username claim tool form 2.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\form;

use moodleform;

/**
 * Class change_binding_username_claim_tool_form2 represents the form on the change binding username claim tool page.
 */
class change_binding_username_claim_tool_form2 extends moodleform {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        $mform =& $this->_form;
        $data = $this->_customdata['data'];

        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(true, get_string('upload_usernames', 'auth_oidc'));

        $this->set_data($data);
    }
}
