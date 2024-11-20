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

namespace local_iomad\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use local_iomad\reportbuilder\local\entities\{company, department, companyusers};

/**
 * Local IOMAD datasource
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class company_users extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('companyusers', 'block_iomad_company_admin');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $companyentity = new company();
        $companyalias = $companyentity->get_table_alias('company');

        $this->set_main_table('company', $companyalias);

        $this->add_entity($companyentity);

        // Get the tables and aliases
        $companyusersentity = new companyusers();
        $companyusersalias = $companyusersentity->get_table_alias('companyusers');
        $departmententity = new department();
        $departmentalias = $departmententity->get_table_alias('department');
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        $this->add_entity($companyusersentity
            ->add_join("JOIN {company_users} {$companyusersalias}
                ON {$companyusersalias}.companyid = {$companyalias}.id")
        );

        // Join the department entity to the company entity.

        $this->add_entity($departmententity
            ->add_join("JOIN {department} {$departmentalias}
                ON ({$departmentalias}.company = {$companyalias}.id
                    AND {$departmentalias}.id = {$companyusersalias}.departmentid)")
        );

        // Join the user entity to the company issued entity.

        $this->add_entity($userentity
            ->add_joins($companyusersentity->get_joins())
            ->add_join("JOIN {user} {$useralias}
                ON {$useralias}.id = {$companyusersalias}.userid")
            ->set_entity_title(new lang_string('user'))
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'company:name',
            'user:fullname',
            'department:name',
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'company:name',
            'department:name',
            'user:fullname',
            'user:email',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'company:name',
            'department:name',
            'user:fullname',
            'user:email',
        ];
    }

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return array|int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'company:name' => SORT_ASC,
            'department:name' => SORT_ASC,
            'user:fullname' => SORT_ASC,
            'user:email' => SORT_ASC,
        ];
    }
}
