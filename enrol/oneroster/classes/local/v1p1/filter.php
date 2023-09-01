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
 * One Roster 1.1 Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\filter as filter_base;
use enrol_oneroster\local\interfaces\filter as filter_interface;
use InvalidArgumentException;

/**
 * A One Roster Filter for the 1.1 API.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter extends filter_base {
    /** @var string[] The current search filters */
    protected $filters = [];

    /** @var string The current logical operator */
    protected $logicaloperator = 'AND';

    /**
     * Add a filter.
     *
     * @param   string|null $field The field to filter on
     * @param   string|null $value The value to filter on
     * @param   string $predicate The filter search type
     * @return  filter_interface
     */
    public function add_filter(string $field, string $value, string $predicate = '='): filter_interface {
        if (count($this->filters) >= 2) {
            throw new \InvalidArgumentException("You may only specify two filters");
        }

        $this->filters[] = sprintf(
            "%s%s'%s'",
            $field,
            $predicate,
            $value
        );

        return $this;
    }
}
