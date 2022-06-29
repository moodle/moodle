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

namespace core_cohort\reportbuilder\datasource;

use core_cohort\local\entities\cohort;
use core_cohort\local\entities\cohort_member;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;

/**
 * Cohorts datasource
 *
 * @package     core_cohort
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohorts extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('cohorts', 'core_cohort');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $cohortentity = new cohort();
        $cohorttablealias = $cohortentity->get_table_alias('cohort');

        $this->set_main_table('cohort', $cohorttablealias);

        $this->add_entity($cohortentity);

        // Join the cohort member entity to the cohort entity.
        $cohortmemberentity = new cohort_member();
        $cohortmembertablealias = $cohortmemberentity->get_table_alias('cohort_members');

        $cohortmemberjoin = "LEFT JOIN {cohort_members} {$cohortmembertablealias}
                               ON {$cohortmembertablealias}.cohortid = {$cohorttablealias}.id";

        $this->add_entity($cohortmemberentity->add_join($cohortmemberjoin));

        // Join the user entity to the cohort member entity.
        $userentity = new user();
        $usertablealias = $userentity->get_table_alias('user');

        $userjoin = "LEFT JOIN {user} {$usertablealias}
                       ON {$usertablealias}.id = {$cohortmembertablealias}.userid";

        $this->add_entity($userentity->add_joins([$cohortmemberjoin, $userjoin]));

        // Add all columns from entities to be available in custom reports.
        $this->add_columns_from_entity($cohortentity->get_entity_name());
        $this->add_columns_from_entity($cohortmemberentity->get_entity_name());
        $this->add_columns_from_entity($userentity->get_entity_name());

        // Add all filters from entities to be available in custom reports.
        $this->add_filters_from_entity($cohortentity->get_entity_name());
        $this->add_filters_from_entity($cohortmemberentity->get_entity_name());
        $this->add_filters_from_entity($userentity->get_entity_name());

        // Add all conditions from entities to be available in custom reports.
        $this->add_conditions_from_entity($cohortentity->get_entity_name());
        $this->add_conditions_from_entity($cohortmemberentity->get_entity_name());
        $this->add_conditions_from_entity($userentity->get_entity_name());
    }

    /**
     * Return the columns that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'cohort:context',
            'cohort:name',
            'cohort:idnumber',
            'cohort:description',
        ];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return ['cohort:context', 'cohort:name'];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [];
    }
}
