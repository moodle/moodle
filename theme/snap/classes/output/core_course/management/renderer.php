<?php
// This file is part of The Bootstrap Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output\core_course\management;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/course/classes/management_renderer.php");

use core_course_category;
use core_course_list_element;

/**
 * Main renderer for the course management pages.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_course_management_renderer {
    /**
     * @inheritdoc
     */
    public function course_listitem(core_course_category $category, core_course_list_element $course, $selectedcourse) {
        return $this->decorate_link_with_detail_hash(parent::course_listitem($category, $course, $selectedcourse));
    }

    /**
     * @inheritdoc
     */
    public function search_listitem(core_course_list_element $course, $selectedcourse) {
        return $this->decorate_link_with_detail_hash(parent::search_listitem($course, $selectedcourse));
    }

    /**
     * @param $html
     * @return string
     */
    private function decorate_link_with_detail_hash(string $html) : string {
        $needle = 'class="float-start coursename aalink" href="';
        if (strpos($html, $needle) === false) {
            $needle = 'class="text-break col ps-0 mb-2 coursename aalink" href="';
        }
        $hrefstart = strpos($html, $needle) + strlen($needle);
        $hrefclose = strpos($html, '"', $hrefstart);

        $newurl = substr($html, $hrefstart, $hrefclose - $hrefstart) . '#course-detail-title';

        $hashedhtml = substr_replace($html, $newurl, $hrefstart, $hrefclose - $hrefstart);

        return $hashedhtml;
    }
}
