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
 * Public API for tool_installaddon.
 *
 * @package     tool_installaddon
 * @copyright   2026 Safat Shahin <safat.shahin@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_course\local\entity\activity_chooser_footer;

/**
 * Build activity chooser footer content for Marketplace.
 *
 * @param int $courseid The selected course id.
 * @param int $sectionid The selected section id.
 * @return activity_chooser_footer
 */
function tool_installaddon_custom_chooser_footer(int $courseid, int $sectionid): activity_chooser_footer {
    global $OUTPUT;

    $installer = tool_installaddon_installer::instance();
    $marketplaceurl = $installer->get_marketplace_url();

    $renderedfooter = $OUTPUT->render_from_template('tool_installaddon/chooser_footer', [
        'url' => $marketplaceurl->out(false),
    ]);

    return new activity_chooser_footer(
        'tool_installaddon/footer',
        $renderedfooter
    );
}
