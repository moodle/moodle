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

namespace core_tag\reportbuilder\local\entities;

use context_system;
use core_tag_tag;
use html_writer;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, tags};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Tag entity
 *
 * @package     core_tag
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['tag' => 't'];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('tag', 'core_tag');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $tagalias = $this->get_table_alias('tag');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tagalias}.rawname, {$tagalias}.name")
            ->set_is_sortable(true)
            ->add_callback(static function($rawname, stdClass $tag): string {
                if ($rawname === null) {
                    return '';
                }
                return core_tag_tag::make_display_name($tag);
            });

        // Name with link.
        $columns[] = (new column(
            'namewithlink',
            new lang_string('namewithlink', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tagalias}.rawname, {$tagalias}.name, {$tagalias}.tagcollid")
            ->set_is_sortable(true)
            ->add_callback(static function($rawname, stdClass $tag): string {
                if ($rawname === null) {
                    return '';
                }
                return html_writer::link(core_tag_tag::make_url($tag->tagcollid, $tag->rawname),
                    core_tag_tag::make_display_name($tag));
            });

        // Description.
        $descriptionfieldsql = "{$tagalias}.description";
        if ($DB->get_dbfamily() === 'oracle') {
            $descriptionfieldsql = $DB->sql_order_by_text($descriptionfieldsql, 1024);
        }
        $columns[] = (new column(
            'description',
            new lang_string('tagdescription', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description')
            ->add_fields("{$tagalias}.descriptionformat, {$tagalias}.id")
            ->add_callback(static function(?string $description, stdClass $tag): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($description === null) {
                    return '';
                }

                $context = context_system::instance();
                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'tag',
                    'description', $tag->id);

                return format_text($description, $tag->descriptionformat, ['context' => $context->id]);
            });

        // Standard.
        $columns[] = (new column(
            'standard',
            new lang_string('standardtag', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$tagalias}.isstandard")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Flagged.
        $columns[] = (new column(
            'flagged',
            new lang_string('flagged', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("CASE WHEN {$tagalias}.flag > 0 THEN 1 ELSE {$tagalias}.flag END", 'flag')
            ->set_is_sortable(true, ["{$tagalias}.flag"])
            ->add_callback([format::class, 'boolean_as_text']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tagalias}.timemodified")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $tagalias = $this->get_table_alias('tag');

        // Name.
        $filters[] = (new filter(
            tags::class,
            'name',
            new lang_string('name', 'core_tag'),
            $this->get_entity_name(),
            "{$tagalias}.id"
        ))
            ->add_joins($this->get_joins());

        // Standard.
        $filters[] = (new filter(
            boolean_select::class,
            'standard',
            new lang_string('standardtag', 'core_tag'),
            $this->get_entity_name(),
            "{$tagalias}.isstandard"
        ))
            ->add_joins($this->get_joins());

        // Flagged.
        $filters[] = (new filter(
            boolean_select::class,
            'flagged',
            new lang_string('flagged', 'core_tag'),
            $this->get_entity_name(),
            "CASE WHEN {$tagalias}.flag > 0 THEN 1 ELSE {$tagalias}.flag END"
        ))
            ->add_joins($this->get_joins());

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tagalias}.timemodified"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        return $filters;
    }
}
