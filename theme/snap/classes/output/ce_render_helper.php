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
 * Snap custom elements renderer.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

use theme_snap\local;

/**
 * Snap custom elements renderer class.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ce_render_helper {

    /**
     * @var ce_render_helper
     */
    private static $instance;

    /**
     * ce_render_helper constructor.
     */
    private function __construct() {
    }

    /**
     * Singleton instance getter.
     * @return ce_render_helper
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new ce_render_helper();
        }
        return self::$instance;
    }

    /**
     * @param string $feedkey
     * @param string $title
     * @param $emptymessage
     * @param bool $virtualpaging
     * @param bool $showreload
     * @param int $courseid
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_feed_web_component($feedkey, $title, $emptymessage, $virtualpaging = false, $showreload = true,
        $courseid = 0, $location = '' ) {
        global $CFG;
        $pagesize = get_config('theme_snap', 'advancedfeedsperpage');
        $pagesize = !empty($pagesize) ? $pagesize : 3;
        $maxlifetime = get_config('theme_snap', 'advancedfeedslifetime');
        $maxlifetime = is_number($maxlifetime) ? $maxlifetime : 30 * MINSECS;
        $sesskey = sesskey();

        $viewmoremsg = get_string('advancedfeed_viewmore', 'theme_snap');
        $reloadmsg = get_string('advancedfeed_reload', 'theme_snap');
        $loadingfeed = get_string('loadingfeed', 'theme_snap');

        $initialvalue = '';
        if ((defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING)
            // There is no easy way to have e2e testing when requesting services is asynchronous,
            // so for testing purposes, we'll populate the component data when the page is being rendered.
            || !empty($CFG->theme_snap_prepopulate_advanced_feeds)
        ) {
            $initialvalue = htmlspecialchars(json_encode(local::get_feed($feedkey, 0, $pagesize)),
                        ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
            $initialvalue = "initial-value=\"{$initialvalue}\"";
        }

        $courseidatt = '';
        if (!empty($courseid)) {
            $courseidatt = "course-id=\"{$courseid}\"";
        }
        $locationid = 'sidebar-menu';

        return <<<HTML
<snap-feed elem-id="snap-{$locationid}-feed-{$feedkey}"
           title="{$title}"
           feed-id="{$feedkey}"
           show-reload="{$showreload}"
           sess-key="{$sesskey}"
           page-size="{$pagesize}"
           virtual-paging="{$virtualpaging}"
           empty-message="{$emptymessage}"
           view-more-message="{$viewmoremsg}"
           reload-message="{$reloadmsg}"
           {$initialvalue}
           www-root="{$CFG->wwwroot}"
           max-life-time="$maxlifetime"
           {$courseidatt}
           loading-feed="{$loadingfeed}"

></snap-feed>
HTML;
    }
}
