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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use core_course_category;
use MoodleQuickForm;
use core_reportbuilder\local\helpers\database;

/**
 * Course category report filter
 *
 * The following optional array property can be passed to the {@see \core_reportbuilder\local\report\filter::set_options} method
 * when defining this filter, to define the capabilities passed to {@see \core_course_category::make_categories_list}
 *
 * ['requiredcapabilities' => '...']
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends base {

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $label = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());

        // See MDL-74627: in order to set the default value to "No selection" we need to prepend an empty value.
        $requiredcapabilities = $this->filter->get_options()['requiredcapabilities'] ?? '';
        $categories = [0 => ''] + core_course_category::make_categories_list($requiredcapabilities);

        $mform->addElement('autocomplete', "{$this->name}_value", $label, $categories)->setHiddenLabel(true);
        $mform->addElement('advcheckbox', "{$this->name}_subcategories", get_string('viewallsubcategories'));
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        global $DB;

        [$fieldsql, $params] = $this->filter->get_field_sql_and_params();

        $category = (int) ($values["{$this->name}_value"] ?? 0);
        $subcategories = !empty($values["{$this->name}_subcategories"]);

        // Invalid or inactive filter.
        if (empty($category)) {
            return ['', []];
        }

        // Initial matching on selected category.
        $paramcategory = database::generate_param_name();
        $params[$paramcategory] = $category;
        $sql = "{$fieldsql} = :{$paramcategory}";

        // Sub-category matching on path of selected category.
        if ($subcategories) {

            // We need to re-use the original filter SQL here, while ensuring parameter uniqueness is preserved.
            [$fieldsql, $params1] = $this->filter->get_field_sql_and_params(1);
            $params = array_merge($params, $params1);

            $paramcategorypath = database::generate_param_name();
            $params[$paramcategorypath] = "%/{$category}/%";
            $sql .= " OR {$fieldsql} IN (
                SELECT id
                  FROM {course_categories}
                 WHERE " . $DB->sql_like('path', ":{$paramcategorypath}") . "
            )";
        }

        return [$sql, $params];
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_value" => 1,
        ];
    }
}
