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
 * Task to get the latest content types from the official H5P repository.
 *
 * @package    core
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

use core_h5p\factory;

defined('MOODLE_INTERNAL') || die();

/**
 * A task to get the latest content types from the official H5P repository.
 *
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class h5p_get_content_types_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('h5pgetcontenttypestask', 'admin');
    }

    /**
     * Get an \core_h5p\core instance.
     *
     * @return \core_h5p\core
     */
    public function get_core() {
        $factory = new factory();
        $core = $factory->get_core();
        return $core;
    }

    /**
     * Execute the task.
     */
    public function execute() {

        $core = $this->get_core();

        $result = $core->fetch_latest_content_types();

        if (!empty($result->error)) {
            mtrace($result->error);
        } else {
            $numtypesinstalled = count($result->typesinstalled);
            mtrace("{$numtypesinstalled} new content types installed");
        }
    }
}
