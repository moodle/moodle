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
use core_badges\reportbuilder\local\entities\{badge, badge_issued};
use core\lang_string;
use core\output\{html_writer, pix_icon};
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;
use moodle_url;
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

    /** @var int $badgeid The ID of the current badge row */
    private int $badgeid;

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        global $USER;

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

        if (!$this->can_view_draft_badges()) {
            $this->add_base_condition_sql("({$entityalias}.status = " . BADGE_STATUS_ACTIVE .
            " OR {$entityalias}.status = " . BADGE_STATUS_ACTIVE_LOCKED . ")");
        }

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entityalias}.id, {$entityalias}.type, {$entityalias}.courseid, {$entityalias}.status");

        $badgeissuedentity = new badge_issued();
        $badgeissuedalias = $badgeissuedentity->get_table_alias('badge_issued');
        $this->add_entity($badgeissuedentity
            ->add_join("LEFT JOIN {badge_issued} {$badgeissuedalias}
                               ON {$entityalias}.id = {$badgeissuedalias}.badgeid"));

        // Join the badge issued entity again, for current user.
        $badgeissuedselfentity = (new badge_issued())
            ->set_entity_name('badge_issued_self');
        $badgeissuedselfalias = $badgeissuedselfentity->get_table_alias('badge_issued');
        $this->add_entity($badgeissuedselfentity
            ->add_join("LEFT JOIN {badge_issued} {$badgeissuedselfalias}
                               ON {$entityalias}.id = {$badgeissuedselfalias}.badgeid
                              AND {$badgeissuedselfalias}.userid = {$USER->id}"));

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        $this->set_default_no_results_notice(new lang_string('nomatchingbadges', 'core_badges'));

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
            'moodle/badges:viewbadges',
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
     */
    protected function add_columns(): void {
        $columns = [
            'badge:namewithimagelink',
            'badge:status',
            'badge:criteria',
        ];

        $canviewdraftbadges = $this->can_view_draft_badges();
        if (!$canviewdraftbadges) {
            // Remove status and recipients column.
            unset($columns[1]);
        }
        $this->add_columns_from_entities($columns);

        // Change title of the `namewithimagelink` column to 'Name'.
        $this->get_column('badge:namewithimagelink')->set_title(new lang_string('name'));

        // Recipients column.
        $this->add_column_from_entity('badge_issued:visible')
            ->set_title(new lang_string('awards', 'core_badges'))
            ->set_is_available($canviewdraftbadges)
            ->set_aggregation('count', [
                'callback' => function (int $count): string {
                    if (!has_capability('moodle/badges:viewawarded', $this->get_context())) {
                        return (string) $count;
                    }

                    return html_writer::link(new moodle_url('/badges/recipients.php', ['id' => $this->badgeid]), $count);
                },
            ]);

        // Add the date the badge was issued at the end of the report.
        $badgeissuedselfalias = $this->get_entity('badge_issued_self')->get_table_alias('badge_issued');
        $this->add_column_from_entity('badge_issued_self:issued')
            ->set_title(new lang_string('awardedtoyou', 'core_badges'))
            ->add_fields("{$badgeissuedselfalias}.uniquehash")
            ->set_callback(static function(?int $value, stdClass $row) {
                global $OUTPUT;

                if (!$value) {
                    return '';
                }
                $format = get_string('strftimedatefullshort', 'core_langconfig');
                $date = $value ? userdate($value, $format) : '';
                $badgeurl = new moodle_url('/badges/badge.php', ['hash' => $row->uniquehash]);
                $icon = new pix_icon('i/valid', get_string('dateearned', 'badges', $date));
                return $OUTPUT->action_icon($badgeurl, $icon, null, null, true);
            });

        $this->set_initial_sort_column('badge:namewithimagelink', SORT_ASC);
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
            'badge_issued:issued',
        ];
        if (!$this->can_view_draft_badges()) {
            // Remove version and status filters.
            unset($filters[1]);
            unset($filters[2]);
        }
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
            new moodle_url('#'),
            new pix_icon('t/show', '', 'core'),
            [
                'data-action' => 'enablebadge',
                'data-badgeid' => ':id',
                'data-badgename' => ':badgename',
                'data-courseid' => ':courseid',
            ],
            false,
            new lang_string('activate', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $badge = new \core_badges\badge($row->id);
            $row->badgename = $badge->name;

            return has_capability('moodle/badges:configurecriteria', $badge->get_context()) &&
                $badge->has_criteria() &&
                ($row->status == BADGE_STATUS_INACTIVE || $row->status == BADGE_STATUS_INACTIVE_LOCKED);

        }));

        // Deactivate badge.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/hide', '', 'core'),
            [
                'data-action' => 'disablebadge',
                'data-badgeid' => ':id',
                'data-badgename' => ':badgename',
                'data-courseid' => ':courseid',
            ],
            false,
            new lang_string('deactivate', 'badges')
        ))->add_callback(static function(stdclass $row): bool {
            $badge = new \core_badges\badge($row->id);
            $row->badgename = $badge->name;
            return has_capability('moodle/badges:configurecriteria', $badge->get_context()) &&
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
     * Check whether the user can view unpublished badges.
     *
     * @return bool True if the user can edit badges, false otherwise.
     */
    private function can_view_draft_badges(): bool {
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
     * Store the ID of the badge within each row
     *
     * @param stdClass $row
     */
    public function row_callback(stdClass $row): void {
        $this->badgeid = (int) $row->id;
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
