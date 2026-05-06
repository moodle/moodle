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

namespace core_admin\setting\page;

use core_admin\admin_search;

/**
 * Module manage page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managemods extends \core_admin\setting\tree\externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managemodules', get_string('modsettings', 'admin'), "$CFG->wwwroot/$CFG->admin/modules.php");
    }

    /**
     * Try to find the specified module
     *
     * @param string $query The module to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($modules = $DB->get_records('modules')) {
            foreach ($modules as $module) {
                if (!file_exists("$CFG->dirroot/mod/$module->name/lib.php")) {
                    continue;
                }
                if (strpos($module->name, $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                    $found = true;
                    break;
                }
                $strmodulename = get_string('modulename', $module->name);
                if (strpos(\core_text::strtolower($strmodulename), $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new \stdClass();
            $result->page     = $this;
            $result->settings = array();
            $result->searchmatchtype = $type;
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managemods::class, \admin_page_managemods::class);
