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
use stdClass;
use core\lang_string;
use core\output\html_writer;
use core_reportbuilder\local\aggregation\{groupconcat, groupconcatdistinct};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, number, tags};
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
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'tag',
            'tag_instance',
        ];
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
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $tagalias = $this->get_table_alias('tag');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tagalias}.rawname, {$tagalias}.name")
            ->set_is_sortable(true)
            ->add_callback(static function($rawname, stdClass $tag): string {
                if ($rawname === null) {
                    return '';
                }
                return core_tag_tag::make_display_name($tag);
            });

        // Name with badge.
        $columns[] = (new column(
            'namewithbadge',
            new lang_string('namewithbadge', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tagalias}.rawname, {$tagalias}.name, {$tagalias}.flag, {$tagalias}.isstandard")
            ->set_is_sortable(true)
            ->set_aggregation_options(groupconcat::get_class_name(), ['separator' => ' '])
            ->set_aggregation_options(groupconcatdistinct::get_class_name(), ['separator' => ' '])
            ->add_callback(static function($rawname, stdClass $tag): string {
                if ($rawname === null) {
                    return '';
                }

                $displayname = core_tag_tag::make_display_name($tag);
                if ($tag->flag > 0) {
                    $displayname = html_writer::span($displayname, 'flagged-tag');
                }

                $class = 'badge bg-info text-white';
                if ($tag->isstandard) {
                    $class .= ' standardtag';
                }

                return html_writer::span($displayname, $class);
            });

        // Name with link.
        $columns[] = (new column(
            'namewithlink',
            new lang_string('namewithlink', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
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
        $columns[] = (new column(
            'description',
            new lang_string('tagdescription', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$tagalias}.description, {$tagalias}.descriptionformat, {$tagalias}.id")
            ->set_is_sortable(true)
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
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Flag count.
        $columns[] = (new column(
            'flagcount',
            new lang_string('flagcount', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$tagalias}.flag")
            ->set_is_sortable(true);

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
    protected function get_available_filters(): array {
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

        // Flag count.
        $filters[] = (new filter(
            number::class,
            'flagcount',
            new lang_string('flagcount', 'core_tag'),
            $this->get_entity_name(),
            "{$tagalias}.flag"
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

    /**
     * Return joins necessary for retrieving tags
     *
     * @param string $component
     * @param string $itemtype
     * @param string $itemidfield
     * @return string[]
     */
    public function get_tag_joins(string $component, string $itemtype, string $itemidfield): array {
        return $this->get_tag_joins_for_entity($component, $itemtype, $itemidfield);
    }
}
