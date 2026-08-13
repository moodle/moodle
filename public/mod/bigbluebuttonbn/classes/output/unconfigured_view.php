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

namespace mod_bigbluebuttonbn\output;

use mod_bigbluebuttonbn\instance;
use renderable;
use renderer_base;
use templatable;

/**
 * Renderable for the neutral state page shown when the BBB server is not yet configured.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2026 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unconfigured_view implements renderable, templatable {
    /**
     * The BBB instance being viewed.
     *
     * @var instance
     */
    private readonly instance $instance;

    /**
     * Constructor.
     *
     * @param instance $instance The BBB instance being viewed.
     */
    public function __construct(instance $instance) {
        $this->instance = $instance;
    }

    /**
     * Export template context data.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $course = $this->instance->get_course();

        $data = [
            'heading' => get_string('unconfigured_view_heading', 'mod_bigbluebuttonbn'),
        ];

        if (has_capability('moodle/site:config', \context_system::instance())) {
            $settingsurl = new \moodle_url('/admin/settings.php', ['section' => 'modsettingbigbluebuttonbn']);
            $data['message'] = get_string('unconfigured_view_admin', 'mod_bigbluebuttonbn');
            $data['settingsurl'] = $settingsurl->out(false);
            $data['settingslinktext'] = get_string('unconfigured_view_settings_link', 'mod_bigbluebuttonbn');
        } else if (has_capability('moodle/course:manageactivities', \context_course::instance($course->id))) {
            $data['message'] = get_string('unconfigured_view_teacher', 'mod_bigbluebuttonbn');
        } else {
            $data['message'] = get_string('unconfigured_view_student', 'mod_bigbluebuttonbn');
        }

        return $data;
    }
}
