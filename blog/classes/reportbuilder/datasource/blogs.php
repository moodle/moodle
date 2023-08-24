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
use core_files\reportbuilder\local\entities\file;
use core_comment\reportbuilder\local\entities\comment;
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

        // Join the files entity.
        $fileentity = (new file())
            ->set_entity_title(new lang_string('blogattachment', 'core_blog'));
        $filesalias = $fileentity->get_table_alias('files');
        $this->add_entity($fileentity
            ->add_join("LEFT JOIN {files} {$filesalias}
                ON {$filesalias}.contextid = " . SYSCONTEXTID . "
               AND {$filesalias}.component = 'blog'
               AND {$filesalias}.filearea = 'attachment'
               AND {$filesalias}.itemid = {$postalias}.id
               AND {$filesalias}.filename != '.'"));

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_entity_title(new lang_string('blogtags', 'core_blog'))
            ->set_table_alias('tag', $blogentity->get_table_alias('tag'));
        $this->add_entity($tagentity
            ->add_joins($blogentity->get_tag_joins()));

        // Join the user entity to represent the blog author.
        $authorentity = (new user())
            ->set_entity_title(new lang_string('author', 'core_blog'));
        $authoralias = $authorentity->get_table_alias('user');
        $this->add_entity($authorentity
            ->add_join("LEFT JOIN {user} {$authoralias} ON {$authoralias}.id = {$postalias}.userid"));

        // Join the course entity for course blogs.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_join("LEFT JOIN {course} {$coursealias} ON {$coursealias}.id = {$postalias}.courseid"));

        // Join the comment entity (ensure differing alias from that used by course entity).
        $commententity = (new comment())
            ->set_table_alias('comments', 'bcmt');
        $this->add_entity($commententity
            ->add_join("LEFT JOIN {comments} bcmt ON bcmt.component = 'blog' AND bcmt.itemid = {$postalias}.id"));

        // Join the user entity to represent the comment author. Override table aliases to avoid clash with first instance.
        $commenterentity = (new user())
            ->set_entity_name('commenter')
            ->set_entity_title(new lang_string('commenter', 'core_comment'))
            ->set_table_aliases([
                'user' => 'cu',
                'context' => 'cuctx',
            ]);
        $this->add_entity($commenterentity
            ->add_joins($commententity->get_joins())
            ->add_join("LEFT JOIN {user} cu ON cu.id = bcmt.userid"));

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entity($blogentity->get_entity_name());

        // Add specific file/tag entity elements.
        $this->add_columns_from_entity($fileentity->get_entity_name(), ['name', 'size', 'type', 'timecreated']);
        $this->add_filters_from_entity($fileentity->get_entity_name(), ['name', 'size', 'timecreated']);
        $this->add_conditions_from_entity($fileentity->get_entity_name(), ['name', 'size', 'timecreated']);

        $this->add_columns_from_entity($tagentity->get_entity_name(), ['name', 'namewithlink']);
        $this->add_filter($tagentity->get_filter('name'));
        $this->add_condition($tagentity->get_condition('name'));

        $this->add_all_from_entity($authorentity->get_entity_name());
        $this->add_all_from_entity($courseentity->get_entity_name());

        // Add specific comment entity elements.
        $this->add_columns_from_entity($commententity->get_entity_name(), ['content', 'timecreated']);
        $this->add_filter($commententity->get_filter('timecreated'));
        $this->add_condition($commententity->get_filter('timecreated'));

        $this->add_all_from_entity($commenterentity->get_entity_name());
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
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'user:fullname' => SORT_ASC,
            'blog:timecreated' => SORT_ASC,
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
