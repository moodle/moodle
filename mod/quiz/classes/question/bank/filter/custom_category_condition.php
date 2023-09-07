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

namespace mod_quiz\question\bank\filter;

use qbank_managecategories\helper;

/**
 * A custom filter condition for quiz to select question categories.
 *
 * This is required as quiz will only use ready questions and the count should show according to that.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_category_condition extends \qbank_managecategories\category_condition {

    public function get_initial_values() {
        $catmenu = custom_category_condition_helper::question_category_options($this->contexts, true, 0, true, -1, false);
        $values = [];
        foreach ($catmenu as $menu) {
            foreach ($menu as $catlist) {
                foreach ($catlist as $key => $value) {
                    $values[] = (object) [
                        // Remove contextid from value.
                        'value' => strpos($key, ',') === false ? $key : substr($key, 0, strpos($key, ',')),
                        'title' => $value,
                        'selected' => ($key === $this->cat),
                    ];
                }
            }
        }
        return $values;
    }
}
