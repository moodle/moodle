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

namespace mod_forum\reportbuilder\datasource;

use core_course\reportbuilder\local\entities\{course_category, course_module};
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use mod_forum\reportbuilder\local\entities\{forum, discussion, post};

/**
 * Forums datasource
 *
 * @package     mod_forum
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forums extends datasource {
    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('forumposts', 'mod_forum');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $forumentity = new forum();

        [
            'context' => $contextalias,
            'course_modules' => $coursemodulesalias,
            'forum' => $forumalias,
        ] = $forumentity->get_table_aliases();

        $this->set_main_table('forum', $forumalias);
        $this->add_entity($forumentity
            ->add_joins($forumentity->get_course_modules_joins('forum', "{$forumalias}.id")));

        // Join the course entity.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_join("LEFT JOIN {course} {$coursealias} ON {$coursealias}.id = {$forumalias}.course"));

        // Join the course category entity.
        $coursecatentity = new course_category();
        $coursecatalias = $coursecatentity->get_table_alias('course_categories');
        $this->add_entity($coursecatentity
            ->add_joins($courseentity->get_joins())
            ->add_join("LEFT JOIN {course_categories} {$coursecatalias} ON {$coursecatalias}.id = {$coursealias}.category"));

        // Join the course module entity.
        $coursemodentity = (new course_module())
            ->set_table_alias('course_modules', $coursemodulesalias);
        $this->add_entity($coursemodentity
            ->add_joins($forumentity->get_joins()));

        // Join the discussion entity.
        $discussionentity = (new discussion())
            ->set_table_alias('context', $contextalias);
        $discussionalias = $discussionentity->get_table_alias('forum_discussions');
        $this->add_entity($discussionentity
            ->add_joins($forumentity->get_joins())
            ->add_join("LEFT JOIN {forum_discussions} {$discussionalias} ON {$discussionalias}.forum = {$forumalias}.id"));

        // Join the post entity.
        $postentity = (new post())
            ->set_table_alias('context', $contextalias);
        $postalias = $postentity->get_table_alias('forum_posts');
        $this->add_entity($postentity
            ->add_joins($discussionentity->get_joins())
            ->add_join("LEFT JOIN {forum_posts} {$postalias} ON {$postalias}.discussion = {$discussionalias}.id"));

        // Join the user entity.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($postentity->get_joins())
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$postalias}.userid"));

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entity($coursecatentity->get_entity_name());
        $this->add_all_from_entity($courseentity->get_entity_name());
        $this->add_all_from_entity($coursemodentity->get_entity_name());
        $this->add_all_from_entity($forumentity->get_entity_name());
        $this->add_all_from_entity($discussionentity->get_entity_name());
        $this->add_all_from_entity($postentity->get_entity_name());
        $this->add_all_from_entity($userentity->get_entity_name());
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'course:fullname',
            'forum:name',
            'discussion:name',
            'post:timecreated',
            'post:message',
            'user:fullname',
        ];
    }

    /**
     * Return the default sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'course:fullname' => SORT_ASC,
            'forum:name' => SORT_ASC,
            'discussion:name' => SORT_ASC,
            'post:timecreated' => SORT_DESC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'course:courseselector',
            'forum:name',
            'discussion:name',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [];
    }
}
