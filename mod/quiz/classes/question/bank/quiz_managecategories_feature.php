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

namespace mod_quiz\question\bank;

use core_question\local\bank\view;
use mod_quiz\question\bank\filter\custom_category_condition;

/**
 * Class quiz_managecategories_feature
 *
 * Overrides the default categories feature with a custom category condition.
 *
 * @package    mod_quiz
 * @copyright  2022 Catalyst IT EU Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_managecategories_feature extends \qbank_managecategories\plugin_feature {

    public function get_question_filters(?view $qbank = null): array {
        return [
            new custom_category_condition($qbank),
        ];
    }
}
