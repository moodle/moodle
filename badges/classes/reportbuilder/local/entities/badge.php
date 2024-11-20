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

namespace core_badges\reportbuilder\local\entities;

use context_course;
use context_helper;
use context_system;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, select, text};
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\{column, filter};

defined('MOODLE_INTERNAL') or die;

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Badge entity
 *
 * @package     core_badges
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'badge',
            'context',
            'tag_instance',
            'tag',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('badgedetails', 'core_badges');
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

        $badgealias = $this->get_table_alias('badge');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$badgealias}.name")
            ->set_is_sortable(true);

        // Name with link.
        $columns[] = (new column(
            'namewithlink',
            new lang_string('namewithlink', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$badgealias}.name, {$badgealias}.id")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $value, stdClass $row): string {
                if (!$row->id) {
                    return '';
                }

                $url = new moodle_url('/badges/overview.php', ['id' => $row->id]);
                return html_writer::link($url, $row->name);
            });

        // Description (note, this column contains plaintext so requires no post-processing).
        $descriptionfieldsql = "{$badgealias}.description";
        if ($DB->get_dbfamily() === 'oracle') {
            $descriptionfieldsql = $DB->sql_order_by_text($descriptionfieldsql, 1024);
        }
        $columns[] = (new column(
            'description',
            new lang_string('description', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description');

        // Criteria.
        $columns[] = (new column(
            'criteria',
            new lang_string('bcriteria', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$badgealias}.id")
            ->set_disabled_aggregation_all()
            ->add_callback(static function($badgeid): string {
                global $PAGE;
                if (!$badgeid) {
                    return '';
                }
                $badge = new \core_badges\badge($badgeid);

                $renderer = $PAGE->get_renderer('core_badges');
                return $renderer->print_badge_criteria($badge, 'short');
            });

        // Image.
        $columns[] = (new column(
            'image',
            new lang_string('badgeimage', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join("LEFT JOIN {context} {$contextalias}
                    ON {$contextalias}.contextlevel = " . CONTEXT_COURSE . "
                   AND {$contextalias}.instanceid = {$badgealias}.courseid")
            ->add_fields("{$badgealias}.id, {$badgealias}.type, {$badgealias}.courseid")
            ->add_field($DB->sql_cast_to_char("{$badgealias}.imagecaption"), 'imagecaption')
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->add_callback(static function($value, stdClass $badge): string {
                if ($badge->id === null) {
                    return '';
                }
                if ($badge->type == BADGE_TYPE_SITE) {
                    $context = context_system::instance();
                } else {
                    context_helper::preload_from_record($badge);
                    $context = context_course::instance($badge->courseid);
                }

                $badgeimage = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f2');
                return html_writer::img($badgeimage, $badge->imagecaption);
            });

        // Language.
        $columns[] = (new column(
            'language',
            new lang_string('language'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$badgealias}.language")
            ->set_is_sortable(true)
            ->add_callback(static function($language): string {
                $languages = get_string_manager()->get_list_of_languages();
                return (string) ($languages[$language] ?? $language);
            });

        // Version.
        $columns[] = (new column(
            'version',
            new lang_string('version', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$badgealias}.version")
            ->set_is_sortable(true);

        // Status.
        $columns[] = (new column(
            'status',
            new lang_string('status', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$badgealias}.status")
            ->set_is_sortable(true)
            ->add_callback(static function($status): string {
                if ($status === null) {
                    return '';
                }

                return get_string("badgestatus_{$status}", 'core_badges');
            });

        // Expiry date/period.
        $columns[] = (new column(
            'expiry',
            new lang_string('expirydate', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$badgealias}.expiredate, {$badgealias}.expireperiod, {$badgealias}.id")
            ->set_is_sortable(true, ["{$badgealias}.expiredate", "{$badgealias}.expireperiod"])
            ->set_disabled_aggregation_all()
            ->add_callback(static function(?int $expiredate, stdClass $badge): string {
                if (!$badge->id) {
                    return '';
                } else if ($expiredate) {
                    return userdate($expiredate);
                } else if ($badge->expireperiod) {
                    return format_time($badge->expireperiod);
                } else {
                    return get_string('never', 'core_badges');
                }
            });

        // Image author details.
        foreach (['imageauthorname', 'imageauthoremail', 'imageauthorurl'] as $imageauthorfield) {
            $columns[] = (new column(
                $imageauthorfield,
                new lang_string($imageauthorfield, 'core_badges'),
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->set_type(column::TYPE_TEXT)
                ->add_field("{$badgealias}.{$imageauthorfield}")
                ->set_is_sortable(true);
        }

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        global $DB;

        $badgealias = $this->get_table_alias('badge');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$badgealias}.name"
        ))
            ->add_joins($this->get_joins());

        // Version.
        $filters[] = (new filter(
            text::class,
            'version',
            new lang_string('version', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.version"
        ))
            ->add_joins($this->get_joins());

        // Status.
        $filters[] = (new filter(
            select::class,
            'status',
            new lang_string('status', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.status"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                BADGE_STATUS_INACTIVE => new lang_string('badgestatus_0', 'core_badges'),
                BADGE_STATUS_ACTIVE => new lang_string('badgestatus_1', 'core_badges'),
                BADGE_STATUS_INACTIVE_LOCKED => new lang_string('badgestatus_2', 'core_badges'),
                BADGE_STATUS_ACTIVE_LOCKED => new lang_string('badgestatus_3', 'core_badges'),
                BADGE_STATUS_ARCHIVED => new lang_string('badgestatus_4', 'core_badges'),
            ]);

        // Expiry date/period.
        $paramtime = database::generate_param_name();
        $filters[] = (new filter(
            date::class,
            'expiry',
            new lang_string('expirydate', 'core_badges'),
            $this->get_entity_name(),
            "CASE WHEN {$badgealias}.expiredate IS NULL AND {$badgealias}.expireperiod IS NULL
                  THEN " . SQL_INT_MAX . "
                  ELSE COALESCE({$badgealias}.expiredate, {$badgealias}.expireperiod + :{$paramtime})
             END",
            [$paramtime => time()]
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
                date::DATE_NEXT,
                date::DATE_PAST,
                date::DATE_FUTURE,
            ]);

        // Type.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('type', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.type"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                BADGE_TYPE_SITE => new lang_string('site'),
                BADGE_TYPE_COURSE => new lang_string('course'),
            ]);

        return $filters;
    }

    /**
     * Return joins necessary for retrieving tags
     *
     * @return string[]
     */
    public function get_tag_joins(): array {
        return $this->get_tag_joins_for_entity('core_badges', 'badge', $this->get_table_alias('badge') . '.id');
    }
}
