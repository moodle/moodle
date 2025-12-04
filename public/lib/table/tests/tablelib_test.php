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

namespace core_table;

use testable_flexible_table;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/tests/fixtures/testable_flexible_table.php');

/**
 * Test some of tablelib.
 *
 * @package    core_table
 * @category   test
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \flexible_table
 */
final class tablelib_test extends \advanced_testcase {
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

    /**
     * Create a table with properties as passed in params, add data and output html.
     *
     * @param string[] $columns The columns of the table.
     * @param string[] $headers The header of the table.
     * @param bool $sortable Sorting of the table.
     * @param bool $collapsible Is table collapsible.
     * @param string[] $suppress Suppress columns.
     * @param string[] $nosorting No sorting.
     * @param (array|object)[] $data The data of the table.
     * @param int $pagesize Page size of the table
     * @param string $caption Caption of the table.
     * @param array $captionattribute The attribute of the caption.
     */
    protected function run_table_test($columns, $headers, $sortable, $collapsible, $suppress, $nosorting, $data,
        $pagesize, $caption = '', $captionattribute = []) {
        $table = $this->create_and_setup_table($columns, $headers, $sortable, $collapsible, $suppress, $nosorting,
            $caption, $captionattribute);
        $table->pagesize($pagesize, count($data));
        foreach ($data as $row) {
            $table->add_data_keyed($row);
        }
        $table->finish_output();
    }

    /**
     * Create a table with properties as passed in params.
     *
     * @param string[] $columns The columns of the table.
     * @param string[] $headers The header of the table.
     * @param bool $sortable Sorting of the table.
     * @param bool $collapsible Is table collapsible.
     * @param string[] $suppress Suppress columns.
     * @param string[] $nosorting No sorting.
     * @param string $caption Caption of the table.
     * @param array $captionattribute The attribute of the caption.
     * @return flexible_table
     */
    protected function create_and_setup_table($columns, $headers, $sortable, $collapsible, $suppress, $nosorting,
        $caption = '', $captionattribute = '') {
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
        if ($caption) {
            $table->set_caption($caption, $captionattribute);
        }

        $table->setup();
        return $table;
    }

    public function test_empty_table(): void {
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

    public function test_has_next_pagination(): void {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for pagination controls containing 'page-link"\saria-label="Next"'.
        $this->expectOutputRegex('/Next page/');

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

    public function test_has_hide(): void {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for 'hide' links in the column headers.
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

    public function test_has_not_hide(): void {

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
        $this->assertStringNotContainsString(get_string('hide'), $output);
    }

    public function test_has_sort(): void {

        $data = $this->generate_data(11, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);

        // Search for pagination controls containing '1.*2</a>.*Next</a>'.
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

    public function test_has_not_sort(): void {

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
        $this->assertStringNotContainsString(get_string('sortby'), $output);
    }

    public function test_has_not_next_pagination(): void {

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
        $this->assertStringNotContainsString(get_string('next'), $output);
    }

    public function test_1_col(): void {

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

    public function test_empty_rows(): void {

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

    public function test_5_cols(): void {

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

    public function test_50_cols(): void {

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

    /**
     * Data provider for test_fullname_column
     *
     * @return array
     */
    public static function fullname_column_provider(): array {
        return [
            ['language'],
            ['alternatename lastname'],
            ['firstname lastnamephonetic'],
        ];
    }

    /**
     * Test fullname column observes configured alternate fullname format configuration
     *
     * @param string $format
     * @return void
     *
     * @dataProvider fullname_column_provider
     */
    public function test_fullname_column(string $format): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('alternativefullnameformat', $format);

        $user = $this->getDataGenerator()->create_user();

        $table = $this->create_and_setup_table(['fullname'], [], true, false, [], []);
        $this->assertStringContainsString(fullname($user, true), $table->format_row($user)['fullname']);
    }

    /**
     * Test fullname column ignores fullname format configuration for a user with viewfullnames capability prohibited
     *
     * @param string $format
     * @return void
     *
     * @dataProvider fullname_column_provider
     */
    public function test_fullname_column_prohibit_viewfullnames(string $format): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        set_config('alternativefullnameformat', $format);

        $currentuser = $this->getDataGenerator()->create_user();
        $this->setUser($currentuser);

        // Prohibit the viewfullnames from the default user role.
        $userrole = $DB->get_record('role', ['id' => $CFG->defaultuserroleid]);
        role_change_permission($userrole->id, \context_system::instance(), 'moodle/site:viewfullnames', CAP_PROHIBIT);

        $user = $this->getDataGenerator()->create_user();

        $table = $this->create_and_setup_table(['fullname'], [], true, false, [], []);
        $this->assertStringContainsString(fullname($user, false), $table->format_row($user)['fullname']);
    }

    public function test_get_row_html(): void {
        $data = $this->generate_data(1, 5);
        $columns = $this->generate_columns(5);
        $headers = $this->generate_headers(5);
        $data = array_keys(array_flip($data[0]));

        $table = new flexible_table('tablelib_test');
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->define_baseurl('/invalid.php');

        $row = $table->get_row_html($data);
        $this->assertMatchesRegularExpression('/row 0 col 0/', $row);
        $this->assertMatchesRegularExpression('/<tr class=""/', $row);
        $this->assertMatchesRegularExpression('/<td class="cell c0"/', $row);
    }

    public function test_persistent_table(): void {
        global $SESSION;

        $data = $this->generate_data(5, 5);
        $columns = $this->generate_columns(5);
        $headers = $this->generate_headers(5);

        // Testing without persistence first to verify that the results are different.
        $table1 = new flexible_table('tablelib_test');
        $table1->define_columns($columns);
        $table1->define_headers($headers);
        $table1->define_baseurl('/invalid.php');

        $table1->sortable(true);
        $table1->collapsible(true);

        $table1->is_persistent(false);
        $_GET['thide'] = 'column0';
        $_GET['tsort'] = 'column1';
        $_GET['tifirst'] = 'A';
        $_GET['tilast'] = 'Z';

        foreach ($data as $row) {
            $table1->add_data_keyed($row);
        }
        $table1->setup();

        // Clear session data between each new table.
        unset($SESSION->flextable);

        $table2 = new flexible_table('tablelib_test');
        $table2->define_columns($columns);
        $table2->define_headers($headers);
        $table2->define_baseurl('/invalid.php');

        $table2->sortable(true);
        $table2->collapsible(true);

        $table2->is_persistent(false);
        unset($_GET);

        foreach ($data as $row) {
            $table2->add_data_keyed($row);
        }
        $table2->setup();

        $this->assertNotEquals($table1, $table2);

        unset($SESSION->flextable);

        // Now testing with persistence to check that the tables are the same.
        $table3 = new flexible_table('tablelib_test');
        $table3->define_columns($columns);
        $table3->define_headers($headers);
        $table3->define_baseurl('/invalid.php');

        $table3->sortable(true);
        $table3->collapsible(true);

        $table3->is_persistent(true);
        $_GET['thide'] = 'column0';
        $_GET['tsort'] = 'column1';
        $_GET['tifirst'] = 'A';
        $_GET['tilast'] = 'Z';

        foreach ($data as $row) {
            $table3->add_data_keyed($row);
        }
        $table3->setup();

        unset($SESSION->flextable);

        $table4 = new flexible_table('tablelib_test');
        $table4->define_columns($columns);
        $table4->define_headers($headers);
        $table4->define_baseurl('/invalid.php');

        $table4->sortable(true);
        $table4->collapsible(true);

        $table4->is_persistent(true);
        unset($_GET);

        foreach ($data as $row) {
            $table4->add_data_keyed($row);
        }
        $table4->setup();

        $this->assertEquals($table3, $table4);

        unset($SESSION->flextable);

        // Finally, another test with no persistence, but without clearing the session data.
        $table5 = new flexible_table('tablelib_test');
        $table5->define_columns($columns);
        $table5->define_headers($headers);
        $table5->define_baseurl('/invalid.php');

        $table5->sortable(true);
        $table5->collapsible(true);

        $table5->is_persistent(true);
        $_GET['thide'] = 'column0';
        $_GET['tsort'] = 'column1';
        $_GET['tifirst'] = 'A';
        $_GET['tilast'] = 'Z';

        foreach ($data as $row) {
            $table5->add_data_keyed($row);
        }
        $table5->setup();

        $table6 = new flexible_table('tablelib_test');
        $table6->define_columns($columns);
        $table6->define_headers($headers);
        $table6->define_baseurl('/invalid.php');

        $table6->sortable(true);
        $table6->collapsible(true);

        $table6->is_persistent(true);
        unset($_GET);

        foreach ($data as $row) {
            $table6->add_data_keyed($row);
        }
        $table6->setup();

        $this->assertEquals($table5, $table6);
    }

    /**
     * Helper method for preparing tables instances in {@link self::test_can_be_reset()}.
     *
     * @param string $tableid
     * @return testable_flexible_table
     */
    protected function prepare_table_for_reset_test($tableid) {
        global $SESSION;

        unset($SESSION->flextable[$tableid]);

        $data = $this->generate_data(25, 3);
        $columns = array('column0', 'column1', 'column2');
        $headers = $this->generate_headers(3);

        $table = new testable_flexible_table($tableid);
        $table->define_baseurl('/invalid.php');
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->collapsible(true);
        $table->is_persistent(false);

        return $table;
    }

    public function test_can_be_reset(): void {
        // Table in its default state (as if seen for the first time), nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->setup();
        $this->assertFalse($table->can_be_reset());

        // Table in its default state with default sorting defined, nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $table->setup();
        $this->assertFalse($table->can_be_reset());

        // Table explicitly sorted by the default column & direction, nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $_GET['tsort'] = 'column1';
        $_GET['tdir'] = SORT_DESC;
        $table->setup();
        unset($_GET['tsort']);
        unset($_GET['tdir']);
        $this->assertFalse($table->can_be_reset());

        // Table explicitly sorted twice by the default column & direction, nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $_GET['tsort'] = 'column1';
        $_GET['tdir'] = SORT_DESC;
        $table->setup();
        $table->setup(); // Set up again to simulate the second page request.
        unset($_GET['tsort']);
        unset($_GET['tdir']);
        $this->assertFalse($table->can_be_reset());

        // Table sorted by other than default column, can be reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $_GET['tsort'] = 'column2';
        $table->setup();
        unset($_GET['tsort']);
        $this->assertTrue($table->can_be_reset());

        // Table sorted by other than default direction, can be reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $_GET['tsort'] = 'column1';
        $_GET['tdir'] = SORT_ASC;
        $table->setup();
        unset($_GET['tsort']);
        unset($_GET['tdir']);
        $this->assertTrue($table->can_be_reset());

        // Table sorted by the default column after another sorting previously selected.
        // This leads to different ORDER BY than just having a single sort defined, can be reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $table->sortable(true, 'column1', SORT_DESC);
        $_GET['tsort'] = 'column0';
        $table->setup();
        $_GET['tsort'] = 'column1';
        $table->setup();
        unset($_GET['tsort']);
        $this->assertTrue($table->can_be_reset());

        // Table having some column collapsed, can be reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $_GET['thide'] = 'column2';
        $table->setup();
        unset($_GET['thide']);
        $this->assertTrue($table->can_be_reset());

        // Table having some column explicitly expanded, nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $_GET['tshow'] = 'column2';
        $table->setup();
        unset($_GET['tshow']);
        $this->assertFalse($table->can_be_reset());

        // Table after expanding a collapsed column, nothing to reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $_GET['thide'] = 'column0';
        $table->setup();
        $_GET['tshow'] = 'column0';
        $table->setup();
        unset($_GET['thide']);
        unset($_GET['tshow']);
        $this->assertFalse($table->can_be_reset());

        // Table with some name filtering enabled, can be reset.
        $table = $this->prepare_table_for_reset_test(uniqid('tablelib_test_'));
        $_GET['tifirst'] = 'A';
        $table->setup();
        unset($_GET['tifirst']);
        $this->assertTrue($table->can_be_reset());
    }

    /**
     * Test export in CSV format
     */
    public function test_table_export(): void {
        $table = new flexible_table('tablelib_test_export');
        $table->define_baseurl('/invalid.php');
        $table->define_columns(['c1', 'c2', 'c3']);
        $table->define_headers(['Col1', 'Col2', 'Col3']);

        ob_start();
        $table->is_downloadable(true);
        $table->is_downloading('csv');

        $table->setup();
        $table->add_data(['column0' => 'a', 'column1' => 'b', 'column2' => 'c']);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals("Col1,Col2,Col3\na,b,c\n", substr($output, 3));
    }

    /**
     * Test the initials functionality.
     *
     * @dataProvider initials_provider
     * @param string|null $getvalue
     * @param string|null $setvalue
     * @param string|null $finalvalue
     */
    public function test_initials_first_set(?string $getvalue, ?string $setvalue, ?string $finalvalue): void {
        global $_GET;

        $this->resetAfterTest(true);

        $table = new flexible_table('tablelib_test');

        $user = $this->getDataGenerator()->create_user();

        $table->define_columns(['fullname']);
        $table->define_headers(['Fullname']);
        $table->define_baseurl('/invalid.php');
        $table->initialbars(true);

        if ($getvalue !== null) {
            $_GET['tifirst'] = $getvalue;
        }

        if ($setvalue !== null) {
            $table->set_first_initial($setvalue);
        }

        $table->setup();

        $this->assertEquals($finalvalue, $table->get_initial_first());
    }

    /**
     * Test the initials functionality.
     *
     * @dataProvider initials_provider
     * @param string|null $getvalue
     * @param string|null $setvalue
     * @param string|null $finalvalue
     */
    public function test_initials_last_set(?string $getvalue, ?string $setvalue, ?string $finalvalue): void {
        global $_GET;

        $this->resetAfterTest(true);

        $table = new flexible_table('tablelib_test');

        $user = $this->getDataGenerator()->create_user();

        $table->define_columns(['fullname']);
        $table->define_headers(['Fullname']);
        $table->define_baseurl('/invalid.php');
        $table->initialbars(true);

        if ($getvalue !== null) {
            $_GET['tilast'] = $getvalue;
        }

        if ($setvalue !== null) {
            $table->set_last_initial($setvalue);
        }

        $table->setup();

        $this->assertEquals($finalvalue, $table->get_initial_last());
    }

    /**
     * Data for testing initials providers.
     *
     * @return array
     */
    public static function initials_provider(): array {
        return [
            [null, null, null],
            ['A', null, 'A'],
            ['Z', null, 'Z'],
            [null, 'A', 'A'],
            [null, 'Z', 'Z'],
            ['A', 'Z', 'Z'],
            ['Z', 'A', 'A'],
        ];
    }

    /**
     * Data test for set and render caption for table.
     */
    public function test_set_and_render_caption_for_table(): void {
        $data = $this->generate_data(10, 2);
        $columns = $this->generate_columns(2);
        $headers = $this->generate_headers(2);
        $caption = 'Caption for table';
        $captionattribute = ['class' => 'inline'];
        $this->run_table_test(
            $columns,
            $headers,
            // Sortable.
            true,
            // Collapsible.
            false,
            // Suppress columns.
            [],
            // No sorting.
            [],
            // Data.
            $data,
            // Page size.
            10,
            // Caption for table.
            $caption,
            // Caption attribute.
            $captionattribute,
        );
        $this->expectOutputRegex('/' . '<caption class="inline">' . $caption . '<\/caption>' . '/');
    }

    /**
     * Test formulas are escaped in exported tables.
     */
    public function test_table_exports_escaped_formulas(): void {
        $table = new flexible_table('tablelib_test_export');
        $table->define_baseurl('/invalid.php');
        $table->define_columns(['c0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6']);

        ob_start();
        $table->is_downloadable(true);
        $table->is_downloading('csv');

        $table->setup();
        $table->add_data([
            'column0' => "  =SUM(1+1)", // With spaces.
            'column1' => "=SUM(1+1)",
            'column2' => "=1+1",
            'column3' => "+1+1",
            'column4' => "-1+1",
            'column5' => "@A1",
            'column6' => "-", // Single dash (should not be escaped).
        ]);

        $output = ob_get_contents();
        ob_end_clean();

        $matchregex = "/\"?'  =SUM\(1\+1\)\"?,'=SUM\(1\+1\),'=1\+1,'\+1\+1,'-1\+1,'@A1,-/";
        $this->assertMatchesRegularExpression($matchregex, $output);
    }
}
