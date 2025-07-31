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

namespace core\navigation;

use moodle_page;

/**
 * Class used to populate site admin navigation for ajax.
 *
 * @package   core
 * @category  navigation
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_navigation_ajax extends settings_navigation {
    /**
     * Constructs the navigation for use in an AJAX request
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page &$page) {
        $this->page = $page;
        $this->cache = new navigation_cache(self::CACHE_NAME);
        $this->children = new navigation_node_collection();
        $this->initialise();
    }

    /**
     * Initialise the site admin navigation.
     */
    public function initialise() {
        if ($this->initialised || during_initial_install()) {
            return false;
        }
        $this->context = $this->page->context;
        $this->load_administration_settings();

        // Check if local plugins is adding node to site admin.
        $this->load_local_plugin_settings();

        $this->initialised = true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(settings_navigation_ajax::class, \settings_navigation_ajax::class);
