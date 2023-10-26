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

namespace core_blog\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use core_blog\reportbuilder\local\entities\blog;
use core_tag\reportbuilder\local\entities\tag;

/**
 * Blogs datasource
 *
 * @package     core_blog
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blogs extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('blogs', 'core_blog');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $blogentity = new blog();

        $postalias = $blogentity->get_table_alias('post');
        $this->set_main_table('post', $postalias);
        $this->add_base_condition_simple("{$postalias}.module", 'blog');

        $this->add_entity($blogentity);

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_entity_title(new lang_string('blogtags', 'core_blog'))
            ->set_table_alias('tag', $blogentity->get_table_alias('tag'));
        $this->add_entity($tagentity
            ->add_joins($blogentity->get_tag_joins()));

        // Join the user entity to represent the blog author.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$postalias}.userid"));

        // Join the course entity for course blogs.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_join("LEFT JOIN {course} {$coursealias} ON {$coursealias}.id = {$postalias}.courseid"));

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entity($blogentity->get_entity_name());

        // Add specific tag entity elements.
        $this->add_columns_from_entity($tagentity->get_entity_name(), ['name', 'namewithlink']);
        $this->add_filter($tagentity->get_filter('name'));
        $this->add_condition($tagentity->get_condition('name'));

        $this->add_all_from_entity($userentity->get_entity_name());
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
            'course:fullname',
            'blog:title',
            'blog:timecreated',
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
            'blog:title',
            'blog:timecreated',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'blog:publishstate',
        ];
    }
}
