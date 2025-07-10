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
use core\lang_string;
use core_reportbuilder\local\helpers\database;
use MoodleQuickForm;

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

    /** @var int Category is any value */
    public const ANY_VALUE = -1;

    /** @var int Category is equal to */
    public const EQUAL_TO = 0;

    /** @var int Category is not equal to */
    public const NOT_EQUAL_TO = 1;

    /**
     * Returns an array of comparison operators
     *
     * @return array
     */
    private function get_operators(): array {
        $operators = [
            self::ANY_VALUE => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::EQUAL_TO => new lang_string('filterisequalto', 'core_reportbuilder'),
            self::NOT_EQUAL_TO => new lang_string('filterisnotequalto', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $operatorlabel = get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header());
        $mform->addElement('select', "{$this->name}_operator", $operatorlabel, $this->get_operators())
            ->setHiddenLabel(true);

        // See MDL-74627: in order to set the default value to "No selection" we need to prepend an empty value.
        $requiredcapabilities = $this->filter->get_options()['requiredcapabilities'] ?? '';
        $categories = [0 => ''] + core_course_category::make_categories_list($requiredcapabilities);

        $valuelabel = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());
        $mform->addElement('autocomplete', "{$this->name}_value", $valuelabel, $categories)->setHiddenLabel(true);
        $mform->hideIf("{$this->name}_value", "{$this->name}_operator", 'eq', self::ANY_VALUE);

        $mform->addElement('advcheckbox', "{$this->name}_subcategories", get_string('includesubcategories'));
        $mform->hideIf("{$this->name}_subcategories", "{$this->name}_operator", 'eq', self::ANY_VALUE);
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

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);
        $category = (int) ($values["{$this->name}_value"] ?? 0);
        $subcategories = !empty($values["{$this->name}_subcategories"]);

        // Invalid or inactive filter.
        if ($operator === self::ANY_VALUE || $category === 0) {
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

        // If specified "Not equal to", then negate the entire clause.
        if ($operator === self::NOT_EQUAL_TO) {
            $sql = "NOT ({$sql})";
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
            "{$this->name}_operator" => self::EQUAL_TO,
            "{$this->name}_value" => 1,
        ];
    }
}
