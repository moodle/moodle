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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class CustomMenuHelper {

    /**
     * Setup menu.
     *
     * @param \global_navigation $nav
     *
     * @return void
     */
    public function setup($nav) {
        global $PAGE;

        try {
            $mynode = $PAGE->navigation->find('myprofile', \navigation_node::TYPE_ROOTNODE);
            $mynode->collapse = true;
            $mynode->make_inactive();

            $context = \context_system::instance();
            if (isloggedin()
                && !empty(SettingsHelper::get_setting('ltitoolurl'))
                && has_capability('local/intellidata:viewlti', $context)) {

                $name = SettingsHelper::get_lti_title();
                $url = new \moodle_url('/local/intellidata/lti.php');
                $nav->add($name, $url);
                $icon = new \pix_icon('i/area_chart', '', 'local_intellidata');
                $node = $mynode->add($name, $url, 0, null, 'intellidata_lti', $icon);
                $node->showinflatnavigation = true;

                // Add LTI item.
                $this->add_item($name, $url);
            }
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }
    }

    /**
     * Add item.
     *
     * @param string $name
     * @param \moodle_url $url
     *
     * @return void
     */
    private function add_item($name, $url) {
        global $CFG;
        $url = $name . "|" . $url->out(false);
        if (SettingsHelper::get_setting('custommenuitem') && isset($CFG->custommenuitems)) {
            if (!$this->item_exists($url)) {
                $CFG->custommenuitems .= "\n" . $url;
            }
        } else if ($this->item_exists($url)) {
            $CFG->custommenuitems = str_replace([$url, "\n" . $url], "", $CFG->custommenuitems);
        }
    }

    /**
     * Item exists.
     *
     * @param string $url
     *
     * @return void
     */
    private function item_exists($url) {
        global $CFG;

        return strpos($CFG->custommenuitems, $url) !== false;
    }
}
