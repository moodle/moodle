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

namespace core_comment\reportbuilder\local\systemreports;

use context_system;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\action;
use core_comment\reportbuilder\local\entities\comment;

/**
 * Comments system report
 *
 * @package     core_comment
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comments extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        $commententity = new comment();
        $commentalias = $commententity->get_table_alias('comments');

        $this->set_main_table('comments', $commentalias);
        $this->add_entity($commententity);

        // Base fields required for action callbacks and checkbox toggle.
        $this->add_base_fields("{$commentalias}.id");
        $this->set_checkbox_toggleall(static function(stdClass $row): array {
            return [$row->id, get_string('select')];
        });

        // Join the user entity to the comment userid (author).
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$commentalias}.userid"));

        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        $this->set_downloadable(true, get_string('comments'));
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/comment:delete', context_system::instance());
    }

    /**
     * Add columns to the report
     */
    protected function add_columns(): void {
        $this->add_columns_from_entities([
            'user:fullnamewithlink',
            'comment:content',
            'comment:contexturl',
            'comment:timecreated',
        ]);

        // Default sorting.
        $this->set_initial_sort_column('comment:timecreated', SORT_DESC);
    }

    /**
     * Add filters to the report
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'user:fullname',
            'comment:content',
            'comment:timecreated',
        ]);
    }

    /**
     * Add actions to report
     */
    protected function add_actions(): void {
        $this->add_action(new action(
            new moodle_url('#'),
            new pix_icon('t/delete', ''),
            ['data-action' => 'comment-delete', 'data-comment-id' => ':id'],
            false,
            new lang_string('delete')
        ));
    }
}
