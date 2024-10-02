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
use local_iomad\reportbuilder\local\entities\{company};

/**
 * Local IOMAD datasource
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class companies extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('company', 'block_iomad_company_admin');
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
            'company:code',
            'company:address',
            'company:city',
            'company:region',
            'company:postcode',
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
            'company:code',
            'company:address',
            'company:city',
            'company:region',
            'company:postcode',
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
            'company:code' => SORT_ASC,
            'company:address' => SORT_ASC,
            'company:city' => SORT_ASC,
            'company:region' => SORT_ASC,
            'company:postcode' => SORT_ASC,
        ];
    }
}
