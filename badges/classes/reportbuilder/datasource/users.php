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
use core_reportbuilder\local\helpers\database;
use core_badges\reportbuilder\local\entities\{badge, badge_issued};
use core_tag\reportbuilder\local\entities\tag;

/**
 * User badges datasource
 *
 * @package     core_badges
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('userbadges', 'core_badges');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $CFG;

        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        $this->set_main_table('user', $useralias);
        $this->add_entity($userentity);

        $paramguest = database::generate_param_name();
        $this->add_base_condition_sql("{$useralias}.id != :{$paramguest} AND {$useralias}.deleted = 0", [
            $paramguest => $CFG->siteguest,
        ]);

        // Join the badge issued entity to the user entity.
        $badgeissuedentity = new badge_issued();
        $badgeissuedalias = $badgeissuedentity->get_table_alias('badge_issued');
        $this->add_entity($badgeissuedentity
            ->add_join("LEFT JOIN {badge_issued} {$badgeissuedalias} ON {$badgeissuedalias}.userid = {$useralias}.id"));

        $badgeentity = new badge();
        $badgealias = $badgeentity->get_table_alias('badge');
        $this->add_entity($badgeentity
            ->add_joins($badgeissuedentity->get_joins())
            ->add_join("LEFT JOIN {badge} {$badgealias} ON {$badgealias}.id = {$badgeissuedalias}.badgeid"));

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_table_alias('tag', $badgeentity->get_table_alias('tag'))
            ->set_entity_title(new lang_string('badgetags', 'core_badges'));
        $this->add_entity($tagentity
            ->add_joins($badgeentity->get_joins())
            ->add_joins($badgeentity->get_tag_joins()));

        // Join the course entity to the badge entity, coalescing courseid with the siteid for site badges.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_joins($badgeentity->get_joins())
            ->add_join("LEFT JOIN {course} {$coursealias} ON {$coursealias}.id =
                CASE WHEN {$badgealias}.id IS NULL THEN 0 ELSE COALESCE({$badgealias}.courseid, 1) END"));

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entity($userentity->get_entity_name());
        $this->add_all_from_entity($badgeissuedentity->get_entity_name());
        $this->add_all_from_entity($badgeentity->get_entity_name());

        // Add specific tag entity elements.
        $this->add_columns_from_entity($tagentity->get_entity_name(), ['name', 'namewithlink']);
        $this->add_filter($tagentity->get_filter('name'));
        $this->add_condition($tagentity->get_condition('name'));

        $this->add_all_from_entity($courseentity->get_entity_name());
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'user:fullname',
            'badge:name',
            'badge:description',
            'badge_issued:issued',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'user:fullname' => SORT_ASC,
            'badge:name' => SORT_ASC,
            'badge_issued:issued' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'user:fullname',
            'badge:name',
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
}
