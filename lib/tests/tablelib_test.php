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

/**
 * Test tablelib.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Test some of tablelib.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tablelib_testcase extends basic_testcase {
    protected function generate_columns($cols) {
        $columns = array();
        foreach (range(0, $cols - 1) as $j) {
            array_push($columns, 'column' . $j);
        }
        return $columns;
    }

    protected function generate_headers($cols) {
        $columns = array();
        foreach (range(0, $cols - 1) as $j) {
            array_push($columns, 'Column ' . $j);
        }
        return $columns;
    }

    protected function generate_data($rows, $cols) {
        $data = array();

        foreach (range(0, $rows - 1) as $i) {
            $row = array();
            foreach (range(0, $cols - 1) as $j) {
                $val =  'row ' . $i . ' col ' . $j;
                $row['column' . $j] = $val;
            }
            array_push($data, $row);
        }
        return $data;
    }

    protected function run_table_test($columns, $headers, $sortable, $collapsible, $suppress, $nosorting, $data, $pagesize) {
        $table = new flexible_table('tablelib_test');

        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->define_baseurl('/invalid.php');

        $table->sortable($sortable);
        $table->collapsible($collapsible);
        foreach ($suppress as $column) {
            $table->column_suppress($column);
        }

        foreach ($nosorting as $column) {
            $table->no_sorting($column);
        }


        $table->setup();
        $table->pagesize($pagesize, count($data));
        foreach ($data as $row) {
            $table->add_data_keyed($row);
        }
        $table->finish_output();
    }

    public function test_empty_table() {
        $this->expectOutputRegex('/' . get_string('nothingtodisplay') . '/');
        $this->run_table_test(
            array('column1', 'column2'),       // Columns.
            array('Column 1', 'Column 2'),     // Headers.
            true,                              // Sortable.
            false,                             // Collapsible.
            array(),                           // Suppress columns.
            array(),                           // No sorting.
            array(),                           // Data.
            10                                 // Page size.
        );
    }

    public function test_has_next_pagination() {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for pagination controls containing 1.*2</a>.*Next</a>
        $this->expectOutputRegex('/1.*2<\/a>.*' . get_string('next') . '<\/a>/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_has_hide() {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for 'hide' links in the column headers
        $this->expectOutputRegex('/' . get_string('hide') . '/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            true,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_has_not_hide() {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Make sure there are no 'hide' links in the headers.

        ob_start();
        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotContains(get_string('hide'), $output);
    }

    public function test_has_sort() {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for pagination controls containing 1.*2</a>.*Next</a>
        $this->expectOutputRegex('/' . get_string('sortby') . '/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_has_not_sort() {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Make sure there are no 'Sort by' links in the headers.

        ob_start();
        $this->run_table_test(
            $columns,
            $headers,
            false,
            false,
            array(),
            array(),
            $data,
            10
        );
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotContains(get_string('sortby'), $output);
    }

    public function test_has_not_next_pagination() {

        $data = $this->generate_data(10, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Make sure there are no 'Next' links in the pagination.

        ob_start();
        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );

        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotContains(get_string('next'), $output);
    }

    public function test_1_col() {

        $data = $this->generate_data(100, 1);
        $columns = $this->generate_columns(1);
        $headers = $this->generate_headers(1);

        $this->expectOutputRegex('/row 0 col 0/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_empty_rows() {

        $data = $this->generate_data(1, 5);
        $columns = $this->generate_columns(5);
        $headers = $this->generate_headers(5);

        // Test that we have at least 5 columns generated for each empty row.
        $this->expectOutputRegex('/emptyrow.*r9_c4/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_5_cols() {

        $data = $this->generate_data(100, 5);
        $columns = $this->generate_columns(5);
        $headers = $this->generate_headers(5);

        $this->expectOutputRegex('/row 0 col 0/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }

    public function test_50_cols() {

        $data = $this->generate_data(100, 50);
        $columns = $this->generate_columns(50);
        $headers = $this->generate_headers(50);

        $this->expectOutputRegex('/row 0 col 0/');

        $this->run_table_test(
            $columns,
            $headers,
            true,
            false,
            array(),
            array(),
            $data,
            10
        );
    }
}
