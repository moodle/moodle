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

namespace core_admin\setting\page;

use core_admin\admin_search;

/**
 * Question behaviour management admin page.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manageqbehaviours extends \core_admin\setting\tree\externalpage {
    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        parent::__construct(
            'manageqbehaviours',
            get_string('manageqbehaviours', 'admin'),
            new \moodle_url('/admin/qbehaviours.php')
        );
    }

    /**
     * Search question behaviours for the specified string
     *
     * @param string $query The string to search for in question behaviours
     * @return array
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        require_once($CFG->dirroot . '/question/engine/lib.php');
        foreach (\core_component::get_plugin_list('qbehaviour') as $behaviour => $notused) {
            if (
                strpos(
                    \core_text::strtolower(\question_engine::get_behaviour_name($behaviour)),
                    $query
                ) !== false
            ) {
                $type = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                $found = true;
                break;
            }
        }
        if ($found) {
            $result = new \stdClass();
            $result->page     = $this;
            $result->settings = [];
            $result->searchmatchtype = $type;
            return [$this->name => $result];
        } else {
            return [];
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manageqbehaviours::class, \admin_page_manageqbehaviours::class);
