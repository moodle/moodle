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
 * Instance.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rule;

/**
 * Instance.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class static_instance implements instance {

    /** @var object The record. */
    protected $record;
    /** @var \context The context. */
    protected $context;
    /** @var \context The childcontext. */
    protected $childcontext;

    /**
     * Constructor.
     *
     * @param object $record The record.
     */
    public function __construct($record) {
        $this->record = $record;
    }

    public function get_id(): int {
        return $this->record->id;
    }

    public function get_context(): \context {
        if (!isset($this->context)) {
            $this->context = \context::instance_by_id($this->record->contextid);
        }
        return $this->context;
    }

    public function get_child_context(): ?\context {
        if (!$this->record->childcontextid) {
            return null;
        }
        if (!isset($this->childcontext)) {
            $this->childcontext = \context::instance_by_id($this->record->childcontextid);
        }
        return $this->childcontext;
    }

    public function get_points(): int {
        return $this->record->points;
    }

    public function get_type_name(): string {
        return $this->record->type;
    }

    public function get_filter_name(): string {
        return $this->record->filter;
    }

    public function get_filter_config(): object {
        return (object) [
            'courseid' => $this->record->filtercourseid,
            'cmid' => $this->record->filtercmid,
            'int1' => $this->record->filterint1,
            'char1' => $this->record->filterchar1,
        ];
    }

}
