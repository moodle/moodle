<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace theme_snap\renderables;

use theme_snap\local;
use moodle_url;

/**
 * Featured categories renderable.
 *
 * @author    Bryan Cruz <bryan.cruz@openlms.net>
 * @copyright Copyright (c) 2024 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featured_categories implements \renderable, \templatable {

    use trait_exportable;

    /**
     * @var featured_category[];
     */
    public $cards = [];

    /**
     * @var string
     */
    public $heading;

    /**
     * @var null | moodle_url
     */
    public $browseallurl = null;

    /**
     * @var null | moodle_url
     */
    public $editurl = null;

    public function __construct() {
        global $PAGE, $DB;

        $config = get_config('theme_snap');

        // Featured categories title.
        if (!empty($config->fcat_heading)) {
            $this->heading = $config->fcat_heading;
        }

        if (!empty($config->fcat_browse_all)) {
            $url = new moodle_url('/course/');
            $this->browseallurl = $url;
        }

        if ($PAGE->user_is_editing()) {
            $url = new moodle_url('/admin/settings.php?section=themesettingsnap#themesnapfeaturedcategoriesandcourses');
            $this->editurl = $url;
        }

        // Build array of category ids to display.
        $ids = ["fcat_one", "fcat_two", "fcat_three", "fcat_four", "fcat_five", "fcat_six", "fcat_seven", "fcat_eight"];
        $categoryids = [];
        $config = get_config('theme_snap');
        foreach ($ids as $id) {
            if (!empty($config->$id)) {
                $categoryids[] = $config->$id;
            }
        }

        // Get DB records for category ids.
        if (count($categoryids)) {
            list ($categorysql, $params) = $DB->get_in_or_equal($categoryids);
            $sql = "SELECT * FROM {course_categories} WHERE id $categorysql";
            $categories = $DB->get_records_sql($sql, $params);
        } else {
            return;
        }

        // Order records to match order input.
        $orderedcategories = [];
        foreach ($categoryids as $categoryid) {
            if (!empty($categories[$categoryid])) {
                $orderedcategories[] = $categories[$categoryid];
            }
        }

        // Build featured category card renderables.
        $i = 0;
        foreach ($orderedcategories as $category) {
            $i ++;
            $url = new moodle_url('/course/index.php?categoryid=' .$category->id);
            $coverimageurl = local::category_coverimage_url($category->id, true);
            $coverimageurl = $coverimageurl ?: null;
            $this->cards[] = new featured_category($url, $coverimageurl, $category->name, $i);
        }
    }
}
