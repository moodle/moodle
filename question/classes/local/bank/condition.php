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

namespace core_question\local\bank;

use core\output\datafilter;

/**
 * An abstract class for filtering/searching questions.
 *
 * @package    core_question
 * @copyright  2013 Ray Morris
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class condition {

    /** @var int The default filter type */
    const JOINTYPE_DEFAULT = datafilter::JOINTYPE_ANY;

    /** @var ?array Filter properties for this condition. */
    public ?array $filter;

    /** @var string SQL fragment to add to the where clause. */
    protected string $where = '';

    /** @var array query param used in where. */
    protected array $params = [];

    /**
     * Return title of the condition
     *
     * @return string title of the condition
     */
    abstract public function get_title();

    /**
     * Return filter class associated with this condition
     *
     * @return string filter class
     */
    abstract public function get_filter_class();

    /**
     * Extract the required filter from the provided question bank view.
     *
     * This will look for the filter matching {@see get_condition_key()}
     *
     * @param view|null $qbank
     */
    public function __construct(?view $qbank = null) {
        if (is_null($qbank)) {
            return;
        }
        $this->filter = static::get_filter_from_list($qbank->get_pagevars('filter'));
        // Build where and params.
        [$this->where, $this->params] = $this->filter ? static::build_query_from_filter($this->filter) : ['', []];
    }

    /**
     * Whether customisation is allowed.
     *
     * @return bool
     */
    public function allow_custom() {
        return true;
    }

    /**
     * Whether multiple values are allowed .
     *
     * @return bool
     */
    public function allow_multiple() {
        return true;
    }

    /**
     * Initial values of the condition
     *
     * @return array
     */
    public function get_initial_values() {
        return [];
    }

    /**
     * Extra data specific to this condition.
     *
     * @return \stdClass
     */
    public function get_filteroptions(): \stdClass {
        return (object)[];
    }

    /**
     * Whether empty value is allowed
     *
     * @return bool
     */
    public function allow_empty() {
        return true;
    }

    /**
     * Whether this filter is required - if so it cannot be removed from the list of filters.
     *
     * @return bool
     */
    public function is_required(): bool {
        return false;
    }

    /**
     * Return this condition class
     *
     * @return string
     */
    public function get_condition_class() {
        return get_class($this);
    }

    /**
     * Each condition will need a unique key to be identified and sequenced by the api.
     * Use a unique string for the condition identifier, use string directly, dont need to use language pack.
     * Using language pack might break the filter object for multilingual support.
     *
     * @return string
     */
    public static function get_condition_key() {
        return '';
    }

    /**
     * Return an SQL fragment to be ANDed into the WHERE clause to filter which questions are shown.
     *
     * @return string SQL fragment. Must use named parameters.
     */
    public function where() {
        return $this->where;
    }

    /**
     * Return parameters to be bound to the above WHERE clause fragment.
     * @return array parameter name => value.
     */
    public function params() {
        return $this->params;
    }

    /**
     * Display GUI for selecting criteria for this condition. Displayed when Show More is open.
     *
     * Compare display_options(), which displays always, whether Show More is open or not.
     * @return bool|string HTML form fragment
     * @deprecated since Moodle 4.0 MDL-72321 - please do not use this function any more.
     * @todo Final deprecation on Moodle 4.1 MDL-72572
     */
    public function display_options_adv() {
        debugging('Function display_options_adv() is deprecated, please use filtering objects', DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Display GUI for selecting criteria for this condition. Displayed always, whether Show More is open or not.
     *
     * Compare display_options_adv(), which displays when Show More is open.
     * @return bool|string HTML form fragment
     * @deprecated since Moodle 4.0 MDL-72321 - please do not use this function any more.
     * @todo Final deprecation on Moodle 4.1 MDL-72572
     */
    public function display_options() {
        debugging('Function display_options() is deprecated, please use filtering objects', DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Get the list of available joins for the filter.
     *
     * @return array
     */
    public function get_join_list(): array {
        return [
            datafilter::JOINTYPE_NONE,
            datafilter::JOINTYPE_ANY,
            datafilter::JOINTYPE_ALL,
        ];
    }

    /**
     * Given an array of filters, pick the entry that matches the condition key and return it.
     *
     * @param array $filters Array of filters, keyed by condition.
     * @return ?array The filter that matches this condition
     */
    public static function get_filter_from_list(array $filters): ?array {
        return $filters[static::get_condition_key()] ?? null;
    }

    /**
     * Build query from filter value
     *
     * @param array $filter filter properties
     * @return array where sql and params
     */
    public static function build_query_from_filter(array $filter): array {
        return ['', []];
    }
}
