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

namespace core_notes\reportbuilder\local\entities;

use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->dirroot}/notes/lib.php");

/**
 * Note entity
 *
 * @package     core_notes
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class note extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'post',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('note', 'core_notes');
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

        $postalias = $this->get_table_alias('post');

        // Content.
        $contentfieldsql = "{$postalias}.content";
        if ($DB->get_dbfamily() === 'oracle') {
            $contentfieldsql = $DB->sql_order_by_text($contentfieldsql, 1024);
        }
        $columns[] = (new column(
            'content',
            new lang_string('content', 'core_notes'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($contentfieldsql, 'content')
            ->add_field("{$postalias}.format")
            ->add_callback(static function(?string $content, stdClass $note): string {
                if ($content === null) {
                    return '';
                }
                return format_text($content, $note->format);
            });

        // Publish state.
        $columns[] = (new column(
            'publishstate',
            new lang_string('publishstate', 'core_notes'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$postalias}.publishstate")
            ->set_is_sortable(true)
            ->add_callback(static function(string $publishstate): string {
                $states = [
                    NOTES_STATE_SITE => new lang_string('sitenotes', 'core_notes'),
                    NOTES_STATE_PUBLIC => new lang_string('coursenotes', 'core_notes'),
                    NOTES_STATE_DRAFT => new lang_string('personalnotes', 'core_notes'),
                ];

                return (string) ($states[$publishstate] ?? $publishstate);
            });

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.created")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.lastmodified")
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
        global $DB;

        $postalias = $this->get_table_alias('post');

        // Content.
        $filters[] = (new filter(
            text::class,
            'content',
            new lang_string('content', 'core_notes'),
            $this->get_entity_name(),
            $DB->sql_cast_to_char("{$postalias}.content")
        ))
            ->add_joins($this->get_joins());

        // Publish state.
        $filters[] = (new filter(
            select::class,
            'publishstate',
            new lang_string('publishstate', 'core_notes'),
            $this->get_entity_name(),
            "{$postalias}.publishstate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                NOTES_STATE_SITE => new lang_string('sitenotes', 'core_notes'),
                NOTES_STATE_PUBLIC => new lang_string('coursenotes', 'core_notes'),
                NOTES_STATE_DRAFT => new lang_string('personalnotes', 'core_notes'),
            ]);

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.created"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.lastmodified"
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
