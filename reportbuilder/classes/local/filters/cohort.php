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

use MoodleQuickForm;
use core_reportbuilder\local\helpers\database;

/**
 * Cohort selector filter class implementation
 *
 * @package     core_reportbuilder
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort extends base {

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $mform->addElement(
            'cohort',
            "{$this->name}_values",
            get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header()),
            [
                'multiple' => true,
            ],
        )->setHiddenLabel(true);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        global $DB;

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $cohortids = $values["{$this->name}_values"] ?? [];
        if (empty($cohortids)) {
            return ['', []];
        }

        [$cohortselect, $cohortparams] = $DB->get_in_or_equal(
            $cohortids,
            SQL_PARAMS_NAMED,
            database::generate_param_name('_'),
        );

        return ["{$fieldsql} $cohortselect", array_merge($params, $cohortparams)];
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_values" => [1],
        ];
    }
}
