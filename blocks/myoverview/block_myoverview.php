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
 * Contains the class for the My overview block.
 *
 * @package    block_myoverview
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * My overview block class.
 *
 * @package    block_myoverview
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myoverview extends block_base {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_myoverview');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }
        $group = get_user_preferences('block_myoverview_user_grouping_preference');
        $sort = get_user_preferences('block_myoverview_user_sort_preference');
        $view = get_user_preferences('block_myoverview_user_view_preference');
        $paging = get_user_preferences('block_myoverview_user_paging_preference');
        $customfieldvalue = get_user_preferences('block_myoverview_user_grouping_customfieldvalue_preference');

        $renderable = new \block_myoverview\output\main($group, $sort, $view, $paging, $customfieldvalue);
        $renderer = $this->page->get_renderer('block_myoverview');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my' => true);
    }

    /**
     * Allow the block to have a configuration page.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = get_config('block_myoverview');

        // Get the customfield values (if any).
        if ($configs->displaygroupingcustomfield) {
            $group = get_user_preferences('block_myoverview_user_grouping_preference');
            $sort = get_user_preferences('block_myoverview_user_sort_preference');
            $view = get_user_preferences('block_myoverview_user_view_preference');
            $paging = get_user_preferences('block_myoverview_user_paging_preference');
            $customfieldvalue = get_user_preferences('block_myoverview_user_grouping_customfieldvalue_preference');

            $renderable = new \block_myoverview\output\main($group, $sort, $view, $paging, $customfieldvalue);
            $customfieldsexport = $renderable->get_customfield_values_for_export();
            if (!empty($customfieldsexport)) {
                $configs->customfieldsexport = json_encode($customfieldsexport);
            }
        }

        return (object) [
            'instance' => new stdClass(),
            'plugin' => $configs,
        ];
    }
}

