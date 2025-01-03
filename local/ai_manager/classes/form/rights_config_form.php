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

namespace local_ai_manager\form;

use local_ai_manager\local\userinfo;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Rights config form.
 *
 * This form handles the user locking/unlocking, assigning of roles etc. on the rights config page.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rights_config_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
        $mform = &$this->_form;

        $mform->addElement('hidden', 'tenant', $tenant->get_identifier());
        $mform->setType('tenant', PARAM_ALPHANUM);

        $mform->addElement('hidden', 'userids', '', ['id' => 'rights-table-userids']);
        $mform->setType('userids', PARAM_TEXT);

        $roleelementsarray = [];
        $roleelementsarray[] = $mform->createElement('select', 'role', '', [
                userinfo::ROLE_BASIC => get_string(userinfo::get_role_as_string(userinfo::ROLE_BASIC), 'local_ai_manager'),
                userinfo::ROLE_EXTENDED => get_string(userinfo::get_role_as_string(userinfo::ROLE_EXTENDED), 'local_ai_manager'),
                userinfo::ROLE_UNLIMITED => get_string(userinfo::get_role_as_string(userinfo::ROLE_UNLIMITED), 'local_ai_manager'),
                userinfo::ROLE_DEFAULT => get_string('defaultrole', 'local_ai_manager'),
        ]);
        $roleelementsarray[] = $mform->createElement('submit', 'changerole', get_string('assignrole', 'local_ai_manager'));
        $mform->addGroup($roleelementsarray, 'buttonarrayrole', '', [' '], false);

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'lockusers', get_string('lockuser', 'local_ai_manager'));
        $buttonarray[] = $mform->createElement('submit', 'unlockusers', get_string('unlockuser', 'local_ai_manager'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }
}
