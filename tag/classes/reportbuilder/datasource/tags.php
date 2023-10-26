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

namespace core_tag\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_tag\reportbuilder\local\entities\{collection, tag, instance};

/**
 * Tags datasource
 *
 * @package     core_tag
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('tags', 'core_tag');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $collectionentity = new collection();

        $collectionalias = $collectionentity->get_table_alias('tag_coll');
        $this->set_main_table('tag_coll', $collectionalias);

        $this->add_entity($collectionentity);

        // Join tag entity to collection.
        $tagentity = new tag();
        $tagalias = $tagentity->get_table_alias('tag');
        $this->add_entity($tagentity
            ->add_join("LEFT JOIN {tag} {$tagalias} ON {$tagalias}.tagcollid = {$collectionalias}.id")
        );

        // Join instance entity to tag.
        $instanceentity = new instance();
        $instancealias = $instanceentity->get_table_alias('tag_instance');
        $this->add_entity($instanceentity
            ->add_joins($tagentity->get_joins())
            ->add_join("LEFT JOIN {tag_instance} {$instancealias} ON {$instancealias}.tagid = {$tagalias}.id")
        );

        // Join user entity to represent the tag author.
        $userentity = (new user())
            ->set_entity_title(new lang_string('tagauthor', 'core_tag'));
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($tagentity->get_joins())
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$tagalias}.userid")
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
            'collection:name',
            'tag:namewithlink',
            'tag:standard',
            'instance:context',
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'tag:name',
            'tag:standard',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'collection:name',
        ];
    }
}
