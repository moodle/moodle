<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Admin setting that allows a user to pick appropriate roles for something.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pickroles extends \core_admin\setting\setting\configmulticheckbox {
    /** @var array Array of capabilities which identify roles */
    private $types;

    /**
     * Constructor for the pick roles setting.
     *
     * @param string $name Name of config variable
     * @param string $visiblename Display name
     * @param string $description Description
     * @param array $types Array of archetypes which identify
     *              roles that will be enabled by default.
     */
    public function __construct($name, $visiblename, $description, $types) {
        parent::__construct($name, $visiblename, $description, null, null);
        $this->types = $types;
    }

    #[\Override]
    public function load_choices() {
        global $CFG, $DB;
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = get_all_roles()) {
            $this->choices = role_fix_names($roles, null, ROLENAME_ORIGINAL, true);
            return true;
        } else {
            return false;
        }
    }

    #[\Override]
    public function get_defaultsetting() {
        global $CFG;

        if (during_initial_install()) {
            return null;
        }
        $result = [];
        foreach ($this->types as $archetype) {
            if ($caproles = get_archetype_roles($archetype)) {
                foreach ($caproles as $caprole) {
                    $result[$caprole->id] = 1;
                }
            }
        }
        return $result;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(pickroles::class, \admin_setting_pickroles::class);
