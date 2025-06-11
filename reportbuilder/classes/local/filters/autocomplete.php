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
 * Autocomplete report filter
 *
 * @package     core_reportbuilder
 * @copyright   2022 Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autocomplete extends base {

    /**
     * Return the options for the filter as an array, to be used to populate the select input field
     *
     * @return array
     */
    protected function get_select_options(): array {
        return (array) $this->filter->get_options();
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $operatorlabel = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());
        $values = [0 => ''] + $this->get_select_options();
        $options = ['multiple' => true];

        $mform->addElement('autocomplete', $this->name . '_values', $operatorlabel, $values, $options)
            ->setHiddenLabel(true);
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

        $invalues = $values["{$this->name}_values"] ?? [];
        if (empty($invalues)) {
            return ['', []];
        }

        [$insql, $inparams] = $DB->get_in_or_equal($invalues, SQL_PARAMS_NAMED, database::generate_param_name('_'));

        return ["{$fieldsql} $insql", array_merge($params, $inparams)];
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
