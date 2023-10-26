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

namespace core_badges\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use core_badges\reportbuilder\local\entities\{badge, badge_issued};

/**
 * Badges datasource
 *
 * @package     core_badges
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badges extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('badges', 'core_badges');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $badgeentity = new badge();
        $badgealias = $badgeentity->get_table_alias('badge');

        $this->set_main_table('badge', $badgealias);

        $this->add_entity($badgeentity);

        // Join the badge issued entity to the badge entity.
        $badgeissuedentity = new badge_issued();
        $badgeissuedalias = $badgeissuedentity->get_table_alias('badge_issued');

        $this->add_entity($badgeissuedentity
            ->add_join("LEFT JOIN {badge_issued} {$badgeissuedalias}
                ON {$badgeissuedalias}.badgeid = {$badgealias}.id")
        );

        // Join the user entity to the badge issued entity.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        $this->add_entity($userentity
            ->add_joins($badgeissuedentity->get_joins())
            ->add_join("LEFT JOIN {user} {$useralias}
                ON {$useralias}.id = {$badgeissuedalias}.userid")
            ->set_entity_title(new lang_string('recipient', 'core_badges'))
        );

        // Join the course entity to the badge entity, coalescing courseid with the siteid for site badges.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_join("LEFT JOIN {course} {$coursealias}
                ON {$coursealias}.id = COALESCE({$badgealias}.courseid, 1)")
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'badge:name',
            'badge:description',
            'user:fullname',
            'badge_issued:issued',
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'badge:name',
            'user:fullname',
            'badge_issued:issued',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'badge:type',
            'badge:name',
        ];
    }

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return array|int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'badge:name' => SORT_ASC,
            'user:fullname' => SORT_ASC,
            'badge_issued:issued' => SORT_ASC,
        ];
    }
}
