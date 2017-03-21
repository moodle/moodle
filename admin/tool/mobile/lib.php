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
 * Lib functions, mostly callbacks.
 *
 * @package    tool_mobile
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Callback to add head elements.
 *
 * @return str valid html head content
 * @since  Moodle 3.3
 */
function tool_mobile_before_standard_html_head() {
    global $CFG, $PAGE;
    $output = '';
    // Smart App Banners meta tag is only displayed if mobile services are enabled and configured.
    if (!empty($CFG->enablemobilewebservice)) {
        $mobilesettings = get_config('tool_mobile');
        if (!empty($mobilesettings->enablesmartappbanners)) {
            if (!empty($mobilesettings->iosappid)) {
                $output .= '<meta name="apple-itunes-app" content="app-id=' . s($mobilesettings->iosappid) . ', ';
                $output .= 'app-argument=' . $PAGE->url->out() . '"/>';
            }

            if (!empty($mobilesettings->androidappid)) {
                $mobilemanifesturl = "$CFG->wwwroot/$CFG->admin/tool/mobile/mobile.webmanifest.php";
                $output .= '<link rel="manifest" href="'.$mobilemanifesturl.'" />';
            }
        }
    }
    return $output;
}
