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
 * Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/wds_sportsgrades/classes/search.php');

/**
 * Sports Grades block class.
 */
class block_wds_sportsgrades extends block_base {

    /**
     * Initialize the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_wds_sportsgrades');
    }

    /**
     * Block has its own configuration screen.
     */
    public function has_config() {
        return true;
    }

    /**
     * Allow instances in multiple areas.
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Return contents of the block.
     */
    public function get_content() {
        global $CFG, $USER, $OUTPUT;

        // If we already ahve content show it.
        if ($this->content !== null) {
            return $this->content;
        }

        // Initialize content.
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Set these to false for now.
        $manageraccess = false;
        $mentoraccess = false;

        // Make sure we can manage stuff.
        if (has_capability('block/wds_sportsgrades:manageaccess', context_system::instance())) {
            $manageraccess = true;
        }

        // Make sure we can use the tool.
        if (has_capability('block/wds_sportsgrades:viewgrades', context_system::instance())) {
            $mentoraccess = true;
        }

        // Check if user has access.
        if (!$mentoraccess && !$manageraccess) {
            return;
        }

        // Set this up for later.
        $listitems = '';

        // Build out the list of items for mentors.
        $mentoritems = [
            [
                'text' => get_string('search_page_link', 'block_wds_sportsgrades'),
                'url' => new moodle_url('/blocks/wds_sportsgrades/view.php'),
                'icontype' => 'fontawesome',
                'icon' => 'fa-square-root-variable'
            ],
        ];

        // Build out manager items.
        $manageritems = [
            [
                'text' => get_string('manageaccess', 'block_wds_sportsgrades'),
                'url' => new moodle_url('/blocks/wds_sportsgrades/admin.php'),
                'icontype' => 'fontawesome',
                'icon' => 'fa-users-gear'
            ],
            [
                'text' => get_string('assignmentors', 'block_wds_sportsgrades'),
                'url' => new moodle_url('/blocks/wds_sportsgrades/assign_mentors.php'),
                'icontype' => 'fontawesome',
                'icon' => 'fa-chalkboard-user'
            ],
        ];

        // Create a button to access the search page.
        if ($mentoraccess) {

            // Loop through the mentor items.
            foreach ($mentoritems as $item) {
                if ($item['icontype'] === 'fontawesome') {
                    $icon=html_writer::tag('i','',
                        ['class' => 'wds icon fa '.$item['icon'],'aria-hidden' => 'true']
                    );
                } else {
                    $icon=$item['icon'];
                }

                // Create the links.
                $link = html_writer::link($item['url'], $icon . $item['text'], ['class' => 'wds sportsgrades menu-link']);

                // Add the links to list items.
                $listitems .= html_writer::tag('li', $link, ['class' => 'wds menu-item']);
            }
        }

        // Create a button to access the management page.
        if ($manageraccess) {

            // Loop through the manager items.
            foreach ($manageritems as $item) {
                if ($item['icontype'] === 'fontawesome') {
                    $icon=html_writer::tag('i','',
                        ['class' => 'wds icon fa '.$item['icon'],'aria-hidden' => 'true']
                    );
                } else {
                    $icon=$item['icon'];
                }

                // Create the links.
                $link = html_writer::link($item['url'], $icon . $item['text'], ['class' => 'wds sportsgrades menu-link']);

                // Add the links to list items.
                $listitems .= html_writer::tag('li', $link, ['class' => 'wds menu-item']);
            }
        }

        // Return all the links.
        $this->content->text = html_writer::tag('ul', $listitems, ['class' => 'wds sportsgrades menu-list']);

        return $this->content;
    }

    /**
     * Specify which pages types this block can be displayed on.
     */
    public function applicable_formats() {
        return [
            'site' => true,
            'my' => true,
            'admin' => true,
        ];
    }
}
