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

namespace mod_label\local\views;

use core\navigation\views\secondary as core_secondary;
use settings_navigation;
use navigation_node;

/**
 * Class secondary_navigation_view.
 *
 * Custom implementation for a plugin.
 *
 * @package     mod_label
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary extends core_secondary {

    /**
     * Custom module construct for label
     *
     * @param settings_navigation $settingsnav The settings navigation object related to the module page
     * @param navigation_node|null $rootnode The node where the module navigation nodes should be added into as children.
     *                                       If not explicitly defined, the nodes will be added to the secondary root
     *                                       node by default.
     */
    protected function load_module_navigation(settings_navigation $settingsnav, ?navigation_node $rootnode = null): void {
        parent::load_module_navigation($settingsnav, $rootnode);
        $rootnode = $rootnode ?? $this;
        $node = $rootnode->find('modulepage', null);
        if ($node) {
            // Label does not have a view and redirects to the course page. Change text to indicate this.
            $node->text = get_string('course');
        }
    }
}
