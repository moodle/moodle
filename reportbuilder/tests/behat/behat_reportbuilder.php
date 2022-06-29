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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use core_reportbuilder\local\aggregation\groupconcatdistinct;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\column;

/**
 * Behat step definitions for Report builder
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_reportbuilder extends behat_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | type   | identifier  | description          |
     * | Editor | Report name | Custom report editor |
     * | View   | Report name | Custom report view   |
     *
     * @param string $type
     * @param string $identifier
     * @return moodle_url
     * @throws Exception for unrecognised report or page type
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        if (!$report = report::get_record(['name' => $identifier])) {
            throw new Exception("Unknown report '{$identifier}'");
        }

        switch ($type) {
            case 'Editor':
                return new moodle_url('/reportbuilder/edit.php', ['id' => $report->get('id')]);

            case 'View':
                return new moodle_url('/reportbuilder/view.php', ['id' => $report->get('id')]);

            default:
                throw new Exception("Unrecognised reportbuilder page type '{$type}'");
        }
    }

    /**
     * Return the list of partial named selectors
     *
     * @return behat_component_named_selector[]
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector('Filter', [
                ".//*[@data-region='filters-form']//*[@data-filter-for=%locator%]",
            ]),
            new behat_component_named_selector('Condition', [
                ".//*[@data-region='conditions-form']//*[@data-condition-name=%locator%]",
            ]),
        ];
    }

    /**
     * Set aggregation for given column in report editor (proxied so we can skip if aggregation type not available)
     *
     * @When I set the :column column aggregation to :aggregation
     *
     * @param string $column
     * @param string $aggregation
     *
     * @throws \Moodle\BehatExtension\Exception\SkippedException
     */
    public function i_set_the_column_aggregation_to(string $column, string $aggregation): void {

        // Skip if aggregation type unavailable.
        $aggregationgroupconcatdistinct = (string) groupconcatdistinct::get_name();
        if ($aggregation === $aggregationgroupconcatdistinct && !groupconcatdistinct::compatible(column::TYPE_TEXT)) {
            throw new \Moodle\BehatExtension\Exception\SkippedException("{$aggregationgroupconcatdistinct} not available");
        }

        $editlabel = get_string('aggregatecolumn', 'core_reportbuilder', $column);
        $this->execute('behat_forms::i_set_the_field_to', [$this->escape($editlabel), $this->escape($aggregation)]);
    }

    /**
     * Press a given action from the action menu in a given report row
     *
     * @When I press :action action in the :row report row
     *
     * @param string $action
     * @param string $row
     */
    public function i_press_action_in_the_report_row(string $action, string $row): void {
        $this->execute('behat_action_menu::i_open_the_action_menu_in', [$this->escape($row), 'table_row']);
        $this->execute('behat_action_menu::i_choose_in_the_open_action_menu', [$this->escape($action)]);
    }
}
