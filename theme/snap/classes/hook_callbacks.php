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
 * @package   theme_snap
 * @author    Jonathan Garcia Gomez <jonathan.garcia@openlms.net>
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_snap;

class hook_callbacks {

    /**
     * Add alerts and redirects to the footer from event actions.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     * @return void
     * @throws \coding_exception
     */
    public static function before_footer_html_generation(\core\hook\output\before_footer_html_generation $hook): void {
        global $CFG, $PAGE;

        if ($PAGE->theme->name !== 'snap' || empty(get_config('theme_snap', 'advancedfeedsenable'))) {
            return;
        }

        $paths = [];
        $paths['theme_snap/snapce'] = [
            $CFG->wwwroot . '/pluginfile.php/' . $PAGE->context->id . '/theme_snap/vendorjs/snap-custom-elements/snap-ce'
        ];

        $PAGE->requires->js_call_amd('theme_snap/wcloader', 'init', [
            'componentPaths' => json_encode($paths)
        ]);
    }
}
