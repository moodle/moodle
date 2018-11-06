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
 * Class containing data for the Recently accessed courses block.
 *
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recentlyaccessedcourses\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Class containing data for Recently accessed courses block.
 *
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $nocoursesurl = $output->image_url('courses', 'block_recentlyaccessedcourses')->out();

        return [
            'userid' => $USER->id,
            'nocoursesimgurl' => $nocoursesurl
        ];
    }
}
