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

namespace mod_feedback\navigation\views;

use core\navigation\views\secondary as core_secondary;
use settings_navigation;
use navigation_node;

/**
 * Custom secondary navigation class
 *
 * A custom construct of secondary nav for feedback. This rearranges the nodes for the secondary
 *
 * @package     mod_feedback
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary extends core_secondary {
    protected function get_default_module_mapping(): array {
        $basenodes = parent::get_default_module_mapping();
        $basenodes[self::TYPE_CUSTOM] += [
            'templatenode' => 12,
            'mapcourse' => 13,
            'feedbackanalysis' => 14,
            'responses' => 15,
            'nonrespondents' => 15.1
        ];

        return $basenodes;
    }

    /**
     * Custom module construct for feedback
     *
     * @param settings_navigation $settingsnav The settings navigation object related to the module page
     * @param navigation_node|null $rootnode The node where the module navigation nodes should be added into as children.
     *                                       If not explicitly defined, the nodes will be added to the secondary root
     *                                       node by default.
     */
    protected function load_module_navigation(settings_navigation $settingsnav, ?navigation_node $rootnode = null): void {
        $rootnode = $rootnode ?? $this;
        $mainnode = $settingsnav->find('modulesettings', self::TYPE_SETTING);
        $nodes = $this->get_default_module_mapping();

        if ($mainnode) {
            $url = new \moodle_url('/mod/' . $this->page->activityname . '/view.php', ['id' => $this->page->cm->id]);
            $setactive = $url->compare($this->page->url, URL_MATCH_BASE);
            $node = $rootnode->add(get_string('modulename', 'feedback'), $url, null, null, 'modulepage');
            if ($setactive) {
                $node->make_active();
            }

            // Add the initial nodes.
            $nodesordered = $this->get_leaf_nodes($mainnode, $nodes);
            $this->add_ordered_nodes($nodesordered, $rootnode);

            // Reorder the existing nodes in settings so the active node scan can pick it up.
            $existingnode = $settingsnav->find('questionnode', self::TYPE_CUSTOM);
            if ($existingnode) {
                $node->add_node($existingnode);
                $nodes[self::TYPE_CUSTOM] += ['questionnode' => 3];
            }
            // We have finished inserting the initial structure.
            // Populate the menu with the rest of the nodes available.
            $this->load_remaining_nodes($mainnode, $nodes, $rootnode);
        }
    }
}
