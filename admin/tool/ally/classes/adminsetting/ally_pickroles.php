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
 * Admin setting for picking roles available for ally configuration.
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @package   tool_ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\adminsetting;

/**
 * Class admin_setting_ally_pickroles
 *
 * @package   tool_ally
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ally_pickroles extends \admin_setting_pickroles {

    /**
     * @inheritdoc
     */
    public function load_choices() {
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = get_all_roles()) {
            $roles = array_filter($roles, 'self::filter_student_archetype');
            $this->choices = role_fix_names($roles, null, ROLENAME_ORIGINAL, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Static callback function for filtering students from role list.
     * @param  \stdClass $role
     * @return bool
     */
    private static function filter_student_archetype($role) {
        return $role->archetype !== 'student';
    }
}
