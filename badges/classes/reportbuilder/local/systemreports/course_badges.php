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

use core\context\system;
use core_badges\reportbuilder\local\entities\badge;
use core_badges\reportbuilder\local\entities\badge_issued;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\system_report;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Course badges system report class implementation
 *
 * @package    core_badges
 * @copyright  2023 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since 4.5. Use badges system_report instead.
 * @todo       MDL-82383 This class will be deleted in Moodle 6.0.
 */
class course_badges extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     *
     * @deprecated since 4.5. Use badges system_report instead.
     */
    #[\core\attribute\deprecated('course_badges::initialise', since: '4.5', mdl: 'MDL-82503')]
    protected function initialise(): void {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
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

         $this->add_base_condition_sql("({$entityalias}.status = " . BADGE_STATUS_ACTIVE .
            " OR {$entityalias}.status = " . BADGE_STATUS_ACTIVE_LOCKED . ")");

        $badgeissuedentity = new badge_issued();
        $badgeissuedalias = $badgeissuedentity->get_table_alias('badge_issued');
        $this->add_entity($badgeissuedentity
            ->add_join("LEFT JOIN {badge_issued} {$badgeissuedalias}
                ON {$entityalias}.id = {$badgeissuedalias}.badgeid AND {$badgeissuedalias}.userid = ".$USER->id)
        );

        $this->add_base_fields("{$badgeissuedalias}.uniquehash");

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();

        $this->set_initial_sort_column('badge:name', SORT_ASC);
        $this->set_default_no_results_notice(new lang_string('nomatchingbadges', 'core_badges'));

        // Set if report can be downloaded.
        $this->set_downloadable(false);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     * @deprecated since 4.5. Use badges system_report instead.
     */
    #[\core\attribute\deprecated('course_badges::can_view', since: '4.5', mdl: 'MDL-82503')]
    protected function can_view(): bool {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
        return has_capability('moodle/badges:viewbadges', $this->get_context());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     *
     * @param string $badgeissuedalias
     * @deprecated since 4.5. Use badges system_report instead.
     */
    #[\core\attribute\deprecated('course_badges::add_columns', since: '4.5', mdl: 'MDL-82503')]
    protected function add_columns(): void {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
        $badgeissuedalias = $this->get_entity('badge_issued')->get_table_alias('badge_issued');

        $this->add_columns_from_entities([
            'badge:image',
            'badge:name',
            'badge:description',
            'badge:criteria',
            'badge_issued:issued',
        ]);

        $this->get_column('badge_issued:issued')
            ->set_title(new lang_string('awardedtoyou', 'core_badges'))
            ->add_fields("{$badgeissuedalias}.uniquehash")
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
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     *
     * @deprecated since 4.5. Use badges system_report instead.
     */
    #[\core\attribute\deprecated('course_badges::add_filters', since: '4.5', mdl: 'MDL-82503')]
    protected function add_filters(): void {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
        $this->add_filters_from_entities([
            'badge:name',
            'badge_issued:issued',
        ]);
    }
}
