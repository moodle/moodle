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
 * Calculates the disk usage
 *
 * @package   theme_moove
 * @copyright 2020 Willian Mano - http://conecti.me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\task;

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

use cache;

/**
 * Task to calculates the disk usage
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class diskusage extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     *
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('calculatediskusagetask', 'theme_moove');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;

        $cache = cache::make('theme_moove', 'admininfos');

        $totalusage = get_directory_size($CFG->dataroot);
        $totalusagereadable = number_format(ceil($totalusage / 1048576));

        $cache->set('totalusagereadable', $totalusagereadable);

        return true;
    }
}
