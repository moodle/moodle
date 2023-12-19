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

use core_badges\reportbuilder\local\entities\badge_issued;
use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;
use lang_string;
use moodle_url;
use pix_icon;

/**
 * Badge recipients system report class implementation
 *
 * @package    core_badges
 * @copyright  2023 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recipients extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $badgeissuedentity = new badge_issued();
        $entityalias = $badgeissuedentity->get_table_alias('badge_issued');

        $this->set_main_table('badge_issued', $entityalias);
        $this->add_entity($badgeissuedentity);

        $userentity = new \core_reportbuilder\local\entities\user();
        $entityuseralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($userentity->get_joins())
            ->add_join("JOIN {user} {$entityuseralias}
                ON {$entityuseralias}.id = {$entityalias}.userid")
        );

        $this->add_base_condition_simple('badgeid', $this->get_parameter('badgeid', 0, PARAM_INT));

        $this->add_base_fields("{$entityalias}.uniquehash");

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        // Set if report can be downloaded.
        $this->set_downloadable(false);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/badges:viewawarded', $this->get_context());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     */
    public function add_columns(): void {
        $columns = [
            'user:fullnamewithlink',
            'badge_issued:issued',
        ];

        $this->add_columns_from_entities($columns);
        $this->set_initial_sort_column('badge_issued:issued', SORT_DESC);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'user:fullname',
            'badge_issued:issued',
        ];

        $this->add_filters_from_entities($filters);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":uniquehash" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {
        $this->add_action((new action(
            new moodle_url('/badges/badge.php', [
                'hash' => ':uniquehash',
            ]),
            new pix_icon('i/search', '', 'core'),
            [],
            false,
            new lang_string('viewbadge', 'badges')
        )));
    }
}
