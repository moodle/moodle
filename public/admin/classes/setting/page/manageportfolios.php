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

class admin_page_manageportfolios extends admin_externalpage {

/**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageportfolios', get_string('manageportfolios', 'portfolio'),
                "$CFG->wwwroot/$CFG->admin/portfolio.php");
    }

    /**
     * Searches page for the specified string.
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        $portfolios = core_component::get_plugin_list('portfolio');
        foreach ($portfolios as $p => $dir) {
            if (strpos($p, $query) !== false) {
                $type = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                $found = true;
                break;
            }
        }
        if (!$found) {
            foreach (portfolio_instances(false, false) as $instance) {
                $title = $instance->get('name');
                if (strpos(core_text::strtolower($title), $query) !== false) {
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
