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
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, number, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Forum post entity
 *
 * @package     mod_forum
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'forum_posts',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('post', 'mod_forum');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        [
            'context' => $contextalias,
            'forum_posts' => $postalias,
        ] = $this->get_table_aliases();

        // Subject.
        $columns[] = (new column(
            'subject',
            new lang_string('subject', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$postalias}.subject")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $subject, stdClass $post): string {
                if ($subject === null || $post->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $post);
                $context = context::instance_by_id($post->ctxid);

                return format_string($subject, true, ['context' => $context]);
            });

        // Message.
        $columns[] = (new column(
            'message',
            new lang_string('message', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$postalias}.message, {$postalias}.messageformat, {$postalias}.messagetrust, {$postalias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $message, stdClass $post): string {
                if ($message === null || $post->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $post);
                $context = context::instance_by_id($post->ctxid);

                $message = file_rewrite_pluginfile_urls($message, 'pluginfile.php', $context->id, 'mod_forum', 'post', $post->id);

                return format_text($message, $post->messageformat, [
                    'context' => $context,
                    'trusted' => $post->messagetrust,
                ]);
            });

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.created")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.modified")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Word count.
        $columns[] = (new column(
            'wordcount',
            new lang_string('wordcount', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$postalias}.wordcount")
            ->set_is_sortable(true);

        // Character count.
        $columns[] = (new column(
            'charcount',
            new lang_string('charactercount', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$postalias}.charcount")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $postalias = $this->get_table_alias('forum_posts');

        // Subject.
        $filters[] = (new filter(
            text::class,
            'subject',
            new lang_string('subject', 'mod_forum'),
            $this->get_entity_name(),
            "{$postalias}.subject",
        ))
            ->add_joins($this->get_joins());

        // Message.
        $filters[] = (new filter(
            text::class,
            'message',
            new lang_string('message', 'mod_forum'),
            $this->get_entity_name(),
            "{$postalias}.message",
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.created",
        ))
            ->add_joins($this->get_joins());

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.modified",
        ))
            ->add_joins($this->get_joins());

        // Word count.
        $filters[] = (new filter(
            number::class,
            'wordcount',
            new lang_string('wordcount', 'mod_forum'),
            $this->get_entity_name(),
            "{$postalias}.wordcount",
        ))
            ->add_joins($this->get_joins());

        // Character count.
        $filters[] = (new filter(
            number::class,
            'charcount',
            new lang_string('charactercount', 'mod_forum'),
            $this->get_entity_name(),
            "{$postalias}.charcount",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
