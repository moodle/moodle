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

use lang_string;
use MoodleQuickForm;
use core_reportbuilder\local\helpers\database;

/**
 * Boolean report filter
 *
 * This filter accepts an expression that evaluates to 1 or 0, either a simple field such as "u.suspended", or a more complex
 * expression such as "CASE WHEN <EXPRESSION> THEN 1 ELSE 0 END"
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boolean_select extends base {

    /** @var int Any value */
    public const ANY_VALUE = 0;

    /** @var int Checked */
    public const CHECKED = 1;

    /** @var int Not checked */
    public const NOT_CHECKED = 2;

    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::ANY_VALUE => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::CHECKED => new lang_string('yes'),
            self::NOT_CHECKED => new lang_string('no'),
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

        $mform->setType("{$this->name}_operator", PARAM_INT);
        $mform->setDefault("{$this->name}_operator", self::ANY_VALUE);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $paramname = database::generate_param_name();

        $operator = $values["{$this->name}_operator"] ?? self::ANY_VALUE;
        switch ($operator) {
            case self::CHECKED:
                $fieldsql .= " = :{$paramname}";
                $params[$paramname] = 1;
                break;
            case self::NOT_CHECKED:
                $fieldsql .= " = :{$paramname}";
                $params[$paramname] = 0;
                break;
            default:
                // Invalid or inactive filter.
                return ['', []];
        }

        return [$fieldsql, $params];
    }
}
