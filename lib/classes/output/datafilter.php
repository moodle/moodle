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

namespace core\output;

use context;
use renderable;
use stdClass;
use templatable;

/**
 * The filter renderable class.
 *
 * @package    core
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class datafilter implements renderable, templatable {

    /** @var int None of the following match */
    public const JOINTYPE_NONE = 0;

    /** @var int Any of the following match */
    public const JOINTYPE_ANY = 1;

    /** @var int All of the following match */
    public const JOINTYPE_ALL = 2;

    /** @var context $context The context where the filters are being rendered. */
    protected $context;

    /** @var string $tableregionid Container of the table to be updated by this filter, is used to retrieve the table */
    protected $tableregionid;

    /** @var stdClass $course The course shown */
    protected $course;

    /**
     * Filter constructor.
     *
     * @param context $context The context where the filters are being rendered
     * @param string|null $tableregionid Container of the table which will be updated by this filter
     */
    public function __construct(context $context, ?string $tableregionid = null) {
        $this->context = $context;
        $this->tableregionid = $tableregionid;

        if ($context instanceof \context_course) {
            $this->course = get_course($context->instanceid);
        }
    }

    /**
     * Get data for all filter types.
     *
     * @return array
     */
    abstract protected function get_filtertypes(): array;

    /**
     * Get a standardised filter object.
     *
     * @param string $name
     * @param string $title
     * @param bool $custom
     * @param bool $multiple
     * @param string|null $filterclass
     * @param array $values
     * @param bool $allowempty
     * @return stdClass|null
     */
    protected function get_filter_object(
        string $name,
        string $title,
        bool $custom,
        bool $multiple,
        ?string $filterclass,
        array $values,
        bool $allowempty = false,
        ?stdClass $filteroptions = null,
        bool $required = false,
        array $joinlist = [self::JOINTYPE_NONE, self::JOINTYPE_ANY, self::JOINTYPE_ALL]
    ): ?stdClass {

        if (!$allowempty && empty($values)) {
            // Do not show empty filters.
            return null;
        }

        return (object) [
            'name' => $name,
            'title' => $title,
            'allowcustom' => $custom,
            'allowmultiple' => $multiple,
            'filtertypeclass' => $filterclass,
            'values' => $values,
            'filteroptions' => $filteroptions,
            'required' => $required,
            'joinlist' => json_encode($joinlist)
        ];
    }
}
