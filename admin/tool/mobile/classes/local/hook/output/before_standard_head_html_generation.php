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

namespace tool_mobile\local\hook\output;

/**
 * Allows plugins to add any elements to the page <head> html tag
 *
 * @package    tool_mobile
 * @copyright  2023 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_head_html_generation {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function callback(\core\hook\output\before_standard_head_html_generation $hook): void {
        global $CFG, $PAGE;
        // Smart App Banners meta tag is only displayed if mobile services are enabled and configured.
        if (!empty($CFG->enablemobilewebservice)) {
            $mobilesettings = get_config('tool_mobile');
            if (!empty($mobilesettings->enablesmartappbanners)) {
                if (!empty($mobilesettings->iosappid)) {
                    $hook->add_html(
                        '<meta name="apple-itunes-app" content="app-id=' . s($mobilesettings->iosappid) . ', ' .
                        'app-argument=' . $PAGE->url->out() . '"/>'
                    );
                }

                if (!empty($mobilesettings->androidappid)) {
                    $mobilemanifesturl = "$CFG->wwwroot/$CFG->admin/tool/mobile/mobile.webmanifest.php";
                    $hook->add_html('<link rel="manifest" href="' . $mobilemanifesturl . '" />');
                }
            }
        }
    }
}
