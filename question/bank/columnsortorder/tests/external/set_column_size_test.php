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

namespace qbank_columnsortorder\external;

use qbank_columnsortorder\column_manager;
use qbank_columnsortorder\tests\external_function_testcase;

// phpcs:disable moodle.PHPUnit.TestCaseNames.Missing
// This class inherits its test methods from the parent class.

/**
 * Unit tests for qbank_columnsortorder external API.
 *
 * @package qbank_columnsortorder
 * @copyright 2023 Catalyst IT Europe Ltd.
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qbank_columnsortorder\external\set_column_size
 */
class set_column_size_test extends external_function_testcase {
    /**
     * @var string Fully-qualified external function class to test.
     */
    protected $testclass = '\qbank_columnsortorder\external\set_column_size';

    /**
     * @var string The name of the setting used to store the data.
     */
    protected $setting = 'colsize';

    /**
     * @var bool Whether the data is stored as a comma-separated list.
     */
    protected $csv = false;

    /**
     * Generate a list of random column sizes.
     *
     * @return array
     */
    protected function generate_test_data(): string {
        $columnsortorder = new column_manager();
        $questionlistcolumns = $columnsortorder->get_columns();
        $columnsizes = [];
        foreach ($questionlistcolumns as $columnnobject) {
            $columnsizes[] = (object)[
                'column' => $columnnobject->name,
                'width' => rand(1, 100) . 'px',
            ];
        }
        return json_encode($columnsizes);
    }
}
