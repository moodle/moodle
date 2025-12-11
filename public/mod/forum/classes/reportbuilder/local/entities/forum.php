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

namespace mod_forum\reportbuilder\local\entities;

use core\{context, context_helper};
use core\lang_string;
use core_course\reportbuilder\local\entities\course_module_base;
use core_reportbuilder\local\filters\{date, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Forum entity
 *
 * @package     mod_forum
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum extends course_module_base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return array_merge(
            parent::get_default_tables(),
            [
                'forum',
            ],
        );
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('forum', 'mod_forum');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        [
            'context' => $contextalias,
            'forum' => $forumalias,
        ] = $this->get_table_aliases();

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$forumalias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $name, stdClass $forum): string {
                if ($name === null || $forum->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $forum);
                $context = context::instance_by_id($forum->ctxid);

                return format_string($name, true, ['context' => $context]);
            });

        // Description.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$forumalias}.intro, {$forumalias}.introformat")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $intro, stdClass $forum): string {
                if ($intro === null || $forum->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $forum);
                $context = context::instance_by_id($forum->ctxid);

                $intro = file_rewrite_pluginfile_urls($intro, 'pluginfile.php', $context->id, 'mod_forum', 'intro', null);

                return format_text($intro, $forum->introformat, ['context' => $context]);
            });

        // Type.
        $columns[] = (new column(
            'type',
            new lang_string('forumtype', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$forumalias}.type")
            ->set_is_sortable(true)
            ->set_callback(static function (?string $type): string {
                global $CFG;
                require_once("{$CFG->dirroot}/mod/forum/lib.php");

                $types = forum_get_forum_types_all();
                if ($type === null || !array_key_exists($type, $types)) {
                    return '';
                }
                return $types[$type];
            });

        // Due date.
        $columns[] = (new column(
            'duedate',
            new lang_string('duedate', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$forumalias}.duedate")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Cut-off date.
        $columns[] = (new column(
            'cutoffdate',
            new lang_string('cutoffdate', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$forumalias}.cutoffdate")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $forumalias = $this->get_table_alias('forum');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$forumalias}.name",
        ))
            ->add_joins($this->get_joins());

        // Description.
        $filters[] = (new filter(
            text::class,
            'description',
            new lang_string('description'),
            $this->get_entity_name(),
            "{$forumalias}.intro",
        ))
            ->add_joins($this->get_joins());

        // Type.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('forumtype', 'mod_forum'),
            $this->get_entity_name(),
            "{$forumalias}.type",
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function (): array {
                global $CFG;
                require_once("{$CFG->dirroot}/mod/forum/lib.php");

                return forum_get_forum_types_all();
            });

        // Due date.
        $filters[] = (new filter(
            date::class,
            'duedate',
            new lang_string('duedate', 'mod_forum'),
            $this->get_entity_name(),
            "{$forumalias}.duedate",
        ))
            ->add_joins($this->get_joins());

        // Cut-off date.
        $filters[] = (new filter(
            date::class,
            'cutoffdate',
            new lang_string('cutoffdate', 'mod_forum'),
            $this->get_entity_name(),
            "{$forumalias}.cutoffdate",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }

    /**
     * Return context joins
     *
     * @return string[]
     *
     * @deprecated since Moodle 5.2 - please do not use this function any more, {@see get_course_modules_joins}
     */
    #[\core\attribute\deprecated('::get_course_modules_joins', since: '5.2', mdl: 'MDL-86699')]
    public function get_context_joins(): array {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        [
            'forum' => $forumalias,
        ] = $this->get_table_aliases();

        return $this->get_course_modules_joins('forum', "{$forumalias}.id");
    }
}
