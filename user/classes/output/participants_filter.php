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
 * Class for rendering user filters on the course participants page.
 *
 * @package    core_user
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_user\output;

use context_course;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Class for rendering user filters on the course participants page.
 *
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_filter implements renderable, templatable {

    /** @var context_course $context The context where the filters are being rendered. */
    protected $context;

    /** @var string $tableregionid The table to be updated by this filter */
    protected $tableregionid;

    /**
     * Participants filter constructor.
     *
     * @param context_course $context The context where the filters are being rendered.
     * @param string $tableregionid The table to be updated by this filter
     */
    public function __construct(context_course $context, string $tableregionid) {
        $this->context = $context;
        $this->tableregionid = $tableregionid;
    }

    /**
     * Get data for all filter types.
     *
     * @return array
     */
    protected function get_filtertypes(): array {
        $filtertypes = [];

        if ($filtertype = $this->get_enrolmentstatus_filter()) {
            $filtertypes[] = $filtertype;
        }

        if ($filtertype = $this->get_roles_filter()) {
            $filtertypes[] = $filtertype;
        }

        return $filtertypes;
    }

    /**
     * Get data for the enrolment status filter.
     *
     * @return stdClass|null
     */
    protected function get_enrolmentstatus_filter(): ?stdClass {
        if (!has_capability('moodle/course:enrolreview', $this->context)) {
            return null;
        }

        return $this->get_filter_object(
            'status',
            get_string('participationstatus', 'core_enrol'),
            false,
            true,
            null,
            [
                (object) [
                    'value' => ENROL_USER_ACTIVE,
                    'title' => get_string('active'),
                ],
                (object) [
                    'value' => ENROL_USER_SUSPENDED,
                    'title'  => get_string('inactive'),
                ],
            ]
        );
    }

    /**
     * Get data for the roles filter.
     *
     * @return stdClass|null
     */
    protected function get_roles_filter(): ?stdClass {
        $roles = [];
        $roles += [-1 => get_string('noroles', 'role')];
        $roles += get_viewable_roles($this->context);

        if (has_capability('moodle/role:assign', $this->context)) {
            $roles += get_assignable_roles($this->context, ROLENAME_ALIAS);
        }

        return $this->get_filter_object(
            'roles',
            get_string('roles', 'core_role'),
            false,
            true,
            null,
            array_map(function($id, $title) {
                return (object) [
                    'value' => $id,
                    'title' => $title,
                ];
            }, array_keys($roles), array_values($roles))
        );
    }

    /**
     * Export the renderer data in a mustache template friendly format.
     *
     * @param renderer_base $output Unused.
     * @return stdClass Data in a format compatible with a mustache template.
     */
    public function export_for_template(renderer_base $output): stdClass {
        return (object) [
            'tableregionid' => $this->tableregionid,
            'courseid' => $this->context->instanceid,
            'filtertypes' => $this->get_filtertypes(),
        ];

        return $data;
    }

    /**
     * Get a standardised filter object.
     *
     * @param string $name
     * @param string $title
     * @param bool $custom
     * @param bool $multiple
     * @param string|null $filterclass
     * @param array $values
     * @return stdClass|null
     */
    protected function get_filter_object(
        string $name,
        string $title,
        bool $custom,
        bool $multiple,
        ?string $filterclass,
        array $values
    ): ?stdClass {
        if (empty($values)) {
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
        ];
    }
}
