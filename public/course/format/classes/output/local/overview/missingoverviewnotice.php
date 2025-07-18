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

namespace core_courseformat\output\local\overview;

use core\output\action_link;
use core\output\named_templatable;
use core\output\renderable;
use core\output\notification;
use core\plugin_manager;
use core\url;
use core_courseformat\local\overview\overviewfactory;
use stdClass;

/**
 * Class missingoverviewnotice
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class missingoverviewnotice implements renderable, named_templatable {
    /**
     * Constructor.
     *
     * @param stdClass $course The course object.
     * @param string $modname The module name.
     */
    public function __construct(
        /** @var stdClass $course the course object  */
        private stdClass $course,
        /** @var string $modname the activity module name  */
        private string $modname,
    ) {
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): stdClass {
        if (!overviewfactory::activity_has_overview_integration($this->modname)) {
            return $this->export_legacy_overview($output);
        }
        // The notice is not needed for plugins with overview class.
        return (object) [];
    }

    /**
     * Exports the legacy overview for a given module.
     *
     * This export only applies to modules that do not have an overview integration.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    private function export_legacy_overview(
        \renderer_base $output,
    ): stdClass {
        $legacyoverview = '/mod/' . $this->modname . '/index.php';
        $name = plugin_manager::instance()->plugin_name($this->modname);

        $link = new action_link(
            url: new url($legacyoverview, ['id' => $this->course->id]),
            text: get_string('overview_modname', 'core_course', $name),
        );

        $notification = new notification(
            message: get_string('overview_missing_notice', 'core_course', $output->render($link)),
            messagetype: notification::NOTIFY_INFO,
            closebutton: false,
            title: get_string('overview_missing_title', 'core_course', $name),
            titleicon: 'i/circleinfo',
        );

        return (object) [
            'name' => $name,
            'shortname' => $this->modname,
            'notification' => $notification->export_for_template($output),
            'missingoverview' => true,
        ];
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core_courseformat/local/overview/missingoverviewnotice';
    }
}
