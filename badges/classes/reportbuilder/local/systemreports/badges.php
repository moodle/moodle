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

namespace core_badges\reportbuilder\local\systemreports;

use core\context\{course, system};
use core_badges\reportbuilder\local\entities\badge;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\{action, column};
use core_reportbuilder\system_report;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Badges system report class implementation
 *
 * @package    core_badges
 * @copyright  2023 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badges extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $badgeentity = new badge();
        $entityalias = $badgeentity->get_table_alias('badge');

        $this->set_main_table('badge', $entityalias);
        $this->add_entity($badgeentity);

        $paramtype = database::generate_param_name();
        $context = $this->get_context();
        if ($context instanceof system) {
            $type = BADGE_TYPE_SITE;
            $this->add_base_condition_sql("{$entityalias}.type = :$paramtype", [$paramtype => $type]);
        } else {
            $type = BADGE_TYPE_COURSE;
            $paramcourseid = database::generate_param_name();
            $this->add_base_condition_sql("{$entityalias}.type = :$paramtype AND {$entityalias}.courseid = :$paramcourseid",
                [$paramtype => $type, $paramcourseid => $context->instanceid]);
        }

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entityalias}.id, {$entityalias}.type, {$entityalias}.courseid, {$entityalias}.status");

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns($badgeentity);
        $this->add_filters();
        $this->add_actions();

        // Set initial sorting by name.
        $this->set_initial_sort_column('badge:namewithlink', SORT_ASC);

        // Set if report can be downloaded.
        $this->set_downloadable(false);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_any_capability([
            'moodle/badges:viewawarded',
            'moodle/badges:createbadge',
            'moodle/badges:awardbadge',
            'moodle/badges:configurecriteria',
            'moodle/badges:configuremessages',
            'moodle/badges:configuredetails',
            'moodle/badges:deletebadge'], $this->get_context());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     *
     * @param badge $badgeentity
     */
    public function add_columns(badge $badgeentity): void {
        $columns = [
            'badge:image',
            'badge:namewithlink',
            'badge:version',
            'badge:status',
            'badge:criteria',
        ];

        $this->add_columns_from_entities($columns);

        // Issued badges column.
        // TODO: Move this column to the entity when MDL-76392 is integrated.
        $tempbadgealias = database::generate_alias();
        $badgeentityalias = $badgeentity->get_table_alias('badge');
        $this->add_column((new column(
            'issued',
            new lang_string('awards', 'core_badges'),
            $badgeentity->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("(SELECT COUNT({$tempbadgealias}.userid)
                            FROM {badge_issued} {$tempbadgealias}
                      INNER JOIN {user} u
                              ON {$tempbadgealias}.userid = u.id
                           WHERE {$tempbadgealias}.badgeid = {$badgeentityalias}.id AND u.deleted = 0)", 'issued')
            ->set_is_sortable(true));

        // Remove title from image column.
        $this->get_column('badge:image')->set_title(null);

        // Change title from namewithlink column.
        $this->get_column('badge:namewithlink')->set_title(new lang_string('name'));
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'badge:name',
            'badge:version',
            'badge:status',
            'badge:expiry',
        ];
        $this->add_filters_from_entities($filters);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {
        // Activate badge.
        $this->add_action((new action(
            new moodle_url('/badges/action.php', [
                'id' => ':id',
                'activate' => true,
                'return' => ':return',
            ]),
            new pix_icon('t/show', '', 'core'),
            [],
            false,
            new lang_string('activate', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $badge = new \core_badges\badge($row->id);

            // Populate the return URL.
            $row->return = (new moodle_url('/badges/index.php',
                ['type' => $badge->type, 'id' => (int) $badge->courseid]))->out_as_local_url(false);

            return has_capability('moodle/badges:configuredetails', $badge->get_context()) &&
                $badge->has_criteria() &&
                ($row->status == BADGE_STATUS_INACTIVE || $row->status == BADGE_STATUS_INACTIVE_LOCKED);

        }));

        // Deactivate badge.
        $this->add_action((new action(
            new moodle_url('/badges/index.php', [
                'lock' => ':id',
                'sesskey' => sesskey(),
                'type' => ':type',
                'id' => ':courseid',
            ]),
            new pix_icon('t/hide', '', 'core'),
            [],
            false,
            new lang_string('deactivate', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $badge = new \core_badges\badge($row->id);
            return has_capability('moodle/badges:configuredetails', $badge->get_context()) &&
                $badge->has_criteria() &&
                $row->status != BADGE_STATUS_INACTIVE && $row->status != BADGE_STATUS_INACTIVE_LOCKED;
        }));

        // Award badge manually.
        $this->add_action((new action(
            new moodle_url('/badges/award.php', [
                'id' => ':id',
            ]),
            new pix_icon('t/award', '', 'core'),
            [],
            false,
            new lang_string('award', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $badge = new \core_badges\badge($row->id);
            return has_capability('moodle/badges:awardbadge', $badge->get_context()) &&
                $badge->has_manual_award_criteria() &&
                $badge->is_active();
        }));

        // Edit action.
        $this->add_action((new action(
            new moodle_url('/badges/edit.php', [
                'id' => ':id',
                'action' => 'badge',
            ]),
            new pix_icon('t/edit', '', 'core'),
            [],
            false,
            new lang_string('edit', 'core')
        ))->add_callback(static function(stdclass $row): bool {
            $context = self::get_badge_context((int)$row->type, (int)$row->courseid);
            return has_capability('moodle/badges:configuredetails', $context);

        }));

        // Duplicate action.
        $this->add_action((new action(
            new moodle_url('/badges/action.php', [
                'id' => ':id',
                'copy' => 1,
                'sesskey' => sesskey(),
            ]),
            new pix_icon('t/copy', '', 'core'),
            [],
            false,
            new lang_string('copy', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $context = self::get_badge_context((int)$row->type, (int)$row->courseid);
            return has_capability('moodle/badges:createbadge', $context);
        }));

        // Delete action.
        $this->add_action((new action(
            new moodle_url('/badges/index.php', [
                'delete' => ':id',
                'type' => ':type',
                'id' => ':courseid',
            ]),
            new pix_icon('t/delete', '', 'core'),
            ['class' => 'text-danger'],
            false,
            new lang_string('delete', 'core')
        ))->add_callback(static function(stdclass $row): bool {
            $context = self::get_badge_context((int)$row->type, (int)$row->courseid);
            return has_capability('moodle/badges:deletebadge', $context);
        }));
    }

    /**
     * Return badge context based on type and courseid
     *
     * @param int $type
     * @param int $courseid
     * @return \core\context
     * @throws \coding_exception
     */
    private static function get_badge_context(int $type, int $courseid): \core\context {
        switch ($type) {
            case BADGE_TYPE_SITE:
                return system::instance();
            case BADGE_TYPE_COURSE:
                return course::instance($courseid);
            default:
                throw new \coding_exception('Wrong context');
        }
    }

    /**
     * CSS classes to add to the row
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return ($row->status == BADGE_STATUS_INACTIVE_LOCKED || $row->status == BADGE_STATUS_INACTIVE) ? 'text-muted' : '';
    }
}
