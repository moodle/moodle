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

namespace tool_mobile;

use html_writer;

/**
 * Allows plugins to add any elements to the footer.
 *
 * @package    tool_mobile
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(
        \core\hook\output\before_standard_head_html_generation $hook,
    ): void {
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

    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_footer_html_generation $hook
     */
    public static function before_standard_footer_html_generation(
        \core\hook\output\before_standard_footer_html_generation $hook,
    ): void {
        global $CFG;

        require_once(__DIR__ . '/../lib.php');

        if (empty($CFG->enablemobilewebservice)) {
            return;
        }

        $url = tool_mobile_create_app_download_url();
        if (empty($url)) {
            return;
        }
        $hook->add_html(
            html_writer::div(
                html_writer::link($url, get_string('getmoodleonyourmobile', 'tool_mobile'), ['class' => 'mobilelink']),
            ),
        );
    }

    /**
     * Callback to recover $SESSION->wantsurl.
     *
     * @param \core_user\hook\after_login_completed $hook
     */
    public static function after_login_completed(
        \core_user\hook\after_login_completed $hook,
    ): void {
        global $SESSION, $CFG;

        // Check if the user is doing a mobile app launch, if that's the case, ensure $SESSION->wantsurl is correctly set.
        if (!NO_MOODLE_COOKIES && !empty($_COOKIE['tool_mobile_launch'])) {
            if (empty($SESSION->wantsurl) || strpos($SESSION->wantsurl, '/tool/mobile/launch.php') === false) {
                $params = json_decode($_COOKIE['tool_mobile_launch'], true);
                $SESSION->wantsurl = (new \moodle_url("/$CFG->admin/tool/mobile/launch.php", $params))->out(false);
            }
        }
    }
}
