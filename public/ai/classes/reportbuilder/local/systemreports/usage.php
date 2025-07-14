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

namespace core_ai\reportbuilder\local\systemreports;

use core_reportbuilder\system_report;
use core_ai\reportbuilder\local\entities\ai_action_register;
use core\reportbuilder\local\entities\context;
use core_reportbuilder\local\entities\user;

/**
 * AI usage system report.
 *
 * @package    core_ai
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usage extends system_report {

    #[\Override]
    protected function initialise(): void {
        $entitymain = new ai_action_register();
        $entitymainalias = $entitymain->get_table_alias('ai_action_register');

        $this->set_main_table('ai_action_register', $entitymainalias);
        $this->add_entity($entitymain);

        // Join the 'user' entity to our main entity.
        $entityuser = new user();
        $entituseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entituseralias} ON {$entituseralias}.id = {$entitymainalias}.userid"
        ));

        // Join the 'context' entity to our main entity.
        $entitycontext = new context();
        $entitycontextalias = $entitycontext->get_table_alias('context');
        $this->add_entity($entitycontext->add_join(
            "LEFT JOIN {context} {$entitycontextalias} ON {$entitycontextalias}.id = {$entitymainalias}.contextid"
        ));

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();

        // Set if report can be downloaded.
        $this->set_downloadable(true, get_string('aiusage', 'core_ai'));
    }

    #[\Override]
    protected function can_view(): bool {
        return has_capability('moodle/ai:viewaiusagereport', $this->get_context());
    }

    #[\Override]
    public static function get_name(): string {
        return get_string('aiusage', 'core_ai');
    }

    /**
     * Adds the columns we want to display in the report.
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier.
     */
    public function add_columns(): void {
        $this->add_columns_from_entities([
            'ai_action_register:provider',
            'ai_action_register:actionname',
            'ai_action_register:timecreated',
            'ai_action_register:prompttokens',
            'ai_action_register:completiontokens',
            'ai_action_register:success',
            'context:name',
            'user:fullnamewithlink',
        ]);

        // It's possible to set a default initial sort direction for one column.
        $this->set_initial_sort_column('ai_action_register:timecreated', SORT_DESC);
    }

    /**
     * Adds the filters we want to display in the report.
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier.
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'ai_action_register:actionname',
            'ai_action_register:provider',
            'ai_action_register:timecreated',
            'ai_action_register:prompttokens',
            'ai_action_register:completiontokens',
            'ai_action_register:success',
            'context:level',
            'user:fullname',
        ]);
    }
}
