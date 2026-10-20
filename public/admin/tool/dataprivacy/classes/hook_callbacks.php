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

namespace tool_dataprivacy;

use html_writer;
use moodle_url;

/**
 * Hook callbacks for tool_dataprivacy.
 *
 * @package    tool_dataprivacy
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Add the privacy summary to the footer.
     *
     * @param \core\hook\output\before_standard_footer_html_generation $hook
     */
    public static function standard_footer_html(\core\hook\output\before_standard_footer_html_generation $hook): void {
        // A returned 0 means that the setting was set and disabled, false means that there is no value for the provided setting.
        $showsummary = get_config('tool_dataprivacy', 'showdataretentionsummary');
        if ($showsummary === false) {
            // This means that no value is stored in db. We use the default value in this case.
            $showsummary = true;
        }

        if ($showsummary) {
            $url = new moodle_url('/admin/tool/dataprivacy/summary.php');
            $hook->add_html(
                html_writer::div(
                    html_writer::link($url, get_string('dataretentionsummary', 'tool_dataprivacy')),
                    'tool_dataprivacy',
                ),
            );
        }
    }

    /**
     * Provide confirmation text when deleting users.
     *
     * @param \core_user\hook\before_deletion_confirmation_html_generation $hook
     */
    public static function user_deletion_confirmation_text(
        \core_user\hook\before_deletion_confirmation_html_generation $hook
    ): void {
        global $OUTPUT;

        if (!get_config('tool_dataprivacy', 'automaticdeletionrequests')) {
            return;
        }

        $newwindowicon = $OUTPUT->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ms-1']);
        $helplink = html_writer::link(
            get_docs_url('Data_privacy'),
            get_string('morehelp') . $newwindowicon,
            ['target' => '_blank'],
        );

        $hook->add_html(
            html_writer::tag('p', get_string('automaticdeletionrequestswarning', 'tool_dataprivacy')) .
            html_writer::tag('p', $helplink)
        );
    }
}
