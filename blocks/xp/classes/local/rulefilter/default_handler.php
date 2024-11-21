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

/**
 * Handler.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rulefilter;

use block_xp\local\rulefilter\rulefilter;

/**
 * Handler.
 *
 * The filter handler, legend says it is sometimes referred to as Alfred.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_handler implements handler {

    /** @var (rulefilter|false)[] The filters cache. */
    protected $filters = [];
    /** @var array The filter names. */
    protected $filternames;
    /** @var array The filters priority, indexed by name. */
    protected $filterspriority;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->filterspriority = $this->make_filters_list_with_priority();
        $this->filternames = array_keys($this->filterspriority);
    }

    /**
     * Get a filter.
     *
     * @param string $name The filter name.
     * @return rulefilter|null
     */
    public function get_filter(string $name): ?rulefilter {
        if (!isset($this->filters[$name])) {
            $this->filters[$name] = $this->load_filter($name) ?? false;
        }
        return $this->filters[$name] ?: null;
    }

    /**
     * Get the filter's name.
     *
     * @param rulefilter $filter The filter instance.
     */
    public function get_filter_name(rulefilter $filter): string {
        return str_replace('block_xp\\local\\rulefilter\\', '', get_class($filter));
    }

    /**
     * Get filter priority.
     *
     * @param rulefilter $filter The filter.
     * @return int
     */
    public function get_filter_priority(rulefilter $filter): int {
        return $this->get_filter_priority_from_name($this->get_filter_name($filter));
    }

    /**
     * Get filter priority by name.
     *
     * @param string $name The name.
     * @return int
     */
    public function get_filter_priority_from_name(string $name): int {
        return array_key_exists($name, $this->filterspriority) ? $this->filterspriority[$name] : 0;
    }

    /**
     * Get the filters.
     *
     * @return rulefilter[] Indexed by name.
     */
    public function get_filters(): array {
        return array_filter(
            array_reduce($this->filternames, function($carry, $name) {
                $carry[$name] = $this->get_filter($name);
                return $carry;
            }, []),
        );
    }

    /**
     * Load a filter.
     *
     * @param string $name The name.
     * @return rulefilter|null
     */
    protected function load_filter($name) {
        $class = "block_xp\\local\\rulefilter\\$name";
        $instance = null;
        if (class_exists($class)) {
            $instance = new $class();
            if (!$instance instanceof rulefilter) {
                $instance = null;
            }
        }
        return $instance;
    }

    /**
     * Make the filters list with priority.
     *
     * @return array
     */
    protected function make_filters_list_with_priority(): array {
        return ['any' => 0];
    }

}
