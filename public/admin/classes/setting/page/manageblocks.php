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

use core_admin\admin_search;

/**
 * Blocks manage page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_manageblocks extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageblocks', get_string('blocksettings', 'admin'), "$CFG->wwwroot/$CFG->admin/blocks.php");
    }

    /**
     * Search for a specific block
     *
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($blocks = $DB->get_records('block')) {
            foreach ($blocks as $block) {
                if (!file_exists("$CFG->dirroot/blocks/$block->name/")) {
                    continue;
                }
                if (strpos($block->name, $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                    $found = true;
                    break;
                }
                $strblockname = get_string('pluginname', 'block_'.$block->name);
                if (strpos(core_text::strtolower($strblockname), $query) !== false) {
                    $type = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            $result->searchmatchtype = $type;
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}
