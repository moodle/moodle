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
 * User report filter
 *
 * This filter expects field SQL referring to a user ID (e.g. "{$tableuser}.id")
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends base {

    /** @var int Filter for any user */
    public const USER_ANY = 0;

    /** @var int Filter for current user */
    public const USER_CURRENT = 1;

    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::USER_ANY => new lang_string('userany', 'core_reportbuilder'),
            self::USER_CURRENT => new lang_string('usercurrent', 'core_reportbuilder'),
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
        $mform->setDefault("{$this->name}_operator", self::USER_ANY);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        global $USER;

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $operator = $values["{$this->name}_operator"] ?? self::USER_ANY;
        switch ($operator) {
            case self::USER_CURRENT:
                $paramuserid = database::generate_param_name();
                $sql = "{$fieldsql} = :{$paramuserid}";
                $params[$paramuserid] = $USER->id;
            break;
            default:
                // Invalid or inactive filter.
                return ['', []];
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
            "{$this->name}_operator" => self::USER_CURRENT,
        ];
    }
}
