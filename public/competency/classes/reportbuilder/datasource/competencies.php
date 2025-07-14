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

namespace core_competency\reportbuilder\datasource;

use core\reportbuilder\local\entities\context;
use core_cohort\reportbuilder\local\entities\cohort;
use core_competency\reportbuilder\local\entities\{competency, framework, usercompetency};
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\helpers\database;

/**
 * Competencies datasource
 *
 * @package     core_competency
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competencies extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('competencies', 'core_competency');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $frameworkentity = new framework();
        $frameworkalias = $frameworkentity->get_table_alias('competency_framework');

        $contextentity = new context();
        $contextalias = $contextentity->get_table_alias('context');

        $this->set_main_table('competency_framework', $frameworkalias);

        // Join context entity (unconditionally, as table also used by both framework/competency entities).
        $this->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$frameworkalias}.contextid");
        $this->add_entity($contextentity);

        $this->add_entity($frameworkentity
            ->set_table_join_alias('context', $contextalias)
        );

        // Join competency entity.
        $competencyentity = new competency();
        $competencyalias = $competencyentity->get_table_alias('competency');
        $this->add_entity($competencyentity
            ->set_table_join_alias('context', $contextalias)
            ->add_join("LEFT JOIN {competency} {$competencyalias}
                ON {$competencyalias}.competencyframeworkid = {$frameworkalias}.id")
        );

        // Join user competency entity.
        $usercompetencyentity = new usercompetency();
        $usercompetencyalias = $usercompetencyentity->get_table_alias('competency_usercomp');
        $this->add_entity($usercompetencyentity
            ->add_joins($competencyentity->get_joins())
            ->add_join("LEFT JOIN {competency_usercomp} {$usercompetencyalias}
                ON {$usercompetencyalias}.competencyid = {$competencyalias}.id")
        );

        // Join user entity.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($usercompetencyentity->get_joins())
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$usercompetencyalias}.userid")
        );

        // Join cohort entity.
        $cohortentity = new cohort();
        $cohortalias = $cohortentity->get_table_alias('cohort');
        $cohortmemberalias = database::generate_alias();
        $this->add_entity($cohortentity
            ->add_joins($userentity->get_joins())
            ->add_joins([
                "LEFT JOIN {cohort_members} {$cohortmemberalias} ON {$cohortmemberalias}.userid = {$useralias}.id",
                "LEFT JOIN {cohort} {$cohortalias} ON {$cohortalias}.id = {$cohortmemberalias}.cohortid",
            ])
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities([
            $contextentity->get_entity_name(),
            $frameworkentity->get_entity_name(),
            $competencyentity->get_entity_name(),
            $usercompetencyentity->get_entity_name(),
            $userentity->get_entity_name(),
        ]);

        $this->add_all_from_entity(
            $cohortentity->get_entity_name(),
            ['name', 'idnumber', 'description', 'customfield*'],
            ['cohortselect', 'name', 'idnumber', 'customfield*'],
            ['cohortselect', 'name', 'idnumber', 'customfield*'],
        );
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'framework:name',
            'competency:name',
            'user:fullname',
            'usercompetency:proficient',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'framework:name' => SORT_ASC,
            'competency:name' => SORT_ASC,
            'user:fullname' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'competency:name',
            'user:fullname',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'framework:visible',
        ];
    }

    /**
     * Return the condition values that will be added to the report upon creation
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return [
            'framework:visible_operator' => boolean_select::CHECKED,
        ];
    }
}
