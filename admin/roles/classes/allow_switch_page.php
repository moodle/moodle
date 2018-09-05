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
 * Role witch matrix.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Subclass of role_allow_role_page for the Allow switches tab.
 */
class core_role_allow_switch_page extends core_role_allow_role_page {
    protected $allowedtargetroles;

    public function __construct() {
        parent::__construct('role_allow_switch', 'allowswitch');
    }

    protected function load_required_roles() {
        global $DB;
        parent::load_required_roles();
        $this->allowedtargetroles = $DB->get_records_menu('role', null, 'id');
    }

    protected function set_allow($fromroleid, $targetroleid) {
        core_role_set_switch_allowed($fromroleid, $targetroleid);
    }

    protected function is_allowed_target($targetroleid) {
        return isset($this->allowedtargetroles[$targetroleid]);
    }

    protected function get_cell_tooltip($fromrole, $targetrole) {
        $a = new stdClass;
        $a->fromrole = $fromrole->localname;
        $a->targetrole = $targetrole->localname;
        return get_string('allowroletoswitch', 'core_role', $a);
    }

    public function get_intro_text() {
        return get_string('configallowswitch', 'core_admin');
    }
}
