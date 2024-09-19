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

namespace tool_policy;

use core\hook\output\before_standard_footer_html_generation;
use core\hook\output\before_standard_top_of_body_html_generation;
use html_writer;
use moodle_url;

/**
 * Allows the plugin to add any elements to the footer.
 *
 * @package    tool_policy
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Add the guest consent form to the top of the body.
     *
     * @param before_standard_top_of_body_html_generation $hook
     */
    public static function before_standard_top_of_body_html_generation(before_standard_top_of_body_html_generation $hook): void {
        global $CFG, $PAGE, $USER;

        if (empty($CFG->sitepolicyhandler)) {
            return;
        }

        if ($CFG->sitepolicyhandler !== 'tool_policy') {
            return;
        }

        if (!empty($USER->policyagreed)) {
            return;
        }

        if (!isguestuser() && isloggedin()) {
            return;
        }

        $output = $PAGE->get_renderer('tool_policy');
        try {
            $page = new \tool_policy\output\guestconsent();
            $hook->add_html($output->render($page));
        } catch (\dml_read_exception $e) {
            // During upgrades, the new plugin code with new SQL could be in place but the DB not upgraded yet.
            return;
        }
    }

    /**
     * Add the user policy settings link to the footer.
     *
     * @param before_standard_footer_html_generation $hook
     */
    public static function before_standard_footer_html_generation(before_standard_footer_html_generation $hook): void {
        global $CFG, $PAGE;

        if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy') {
            return;
        }

        $policies = api::get_current_versions_ids();
        if (!empty($policies)) {
            $url = new moodle_url('/admin/tool/policy/viewall.php', ['returnurl' => $PAGE->url]);
            $hook->add_html(
                html_writer::div(
                    html_writer::link($url, get_string('userpolicysettings', 'tool_policy')),
                    'policiesfooter',
                ),
            );
        }
    }
}
