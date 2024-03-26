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

namespace qbank_columnsortorder;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;
use context_course;
use core_question\local\bank\column_base;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use moodle_url;

global $CFG;
require_once($CFG->dirroot . '/question/tests/fixtures/testable_core_question_column.php');
require_once($CFG->dirroot . '/question/classes/external.php');

/**
 * Test class for columnsortorder feature.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qbank_columnsortorder\column_manager
 */
class column_manager_test extends advanced_testcase {

    /**
     * Generate a course and return a question bank view for the course context.
     *
     * @return view
     */
    protected static function get_question_bank(): view {
        $course = self::getDataGenerator()->create_course();
        $questionbank = new view(
            new question_edit_contexts(context_course::instance($course->id)),
            new moodle_url('/'),
            $course
        );
        return $questionbank;
    }

    /**
     * Return an array of visible columns for the question bank.
     *
     * @return array
     */
    protected static function get_columns(): array {
        $questionbank = self::get_question_bank();
        $columns = [];
        foreach ($questionbank->get_visiblecolumns() as $column) {
            $columns[] = $column->get_column_id();
        }
        return $columns;
    }

    /**
     * Provide examples for testing each column setting function, with test data and data format.
     *
     * @return array[]
     */
    public static function settings_provider(): array {
        return [
            'Test set_column_order' => [
                'setting' => 'enabledcol',
                'function' => 'set_column_order',
                'datamethod' => [__CLASS__, 'get_columns'],
                'csv' => true,
            ],
            'Test set_hidden_columns' => [
                'setting' => 'hiddencols',
                'function' => 'set_hidden_columns',
                'datamethod' => [__CLASS__, 'get_columns'],
                'csv' => true,
            ],
            'Test set_column_size' => [
                'setting' => 'colsize',
                'function' => 'set_column_size',
                'datamethod' => 'random_string',
                'csv' => false,
            ],
        ];
    }

    /**
     * Retrieve data using the specified method.
     * This function is used to retrieve data from various data methods defined within this class.
     *
     * @param array|string $datamethod This can be either a function name or an array containing the class and method name.
     * @return array|string The retrieved data as an array or string, depending on the data method used.
     */
    protected function get_data_from_datamethod(array|string $datamethod): array|string {
        return call_user_func($datamethod);
    }


    /**
     * Test setting config settings
     *
     * @dataProvider settings_provider
     * @param string $setting The name of the setting being saved
     * @param string $function The name of the function being called
     * @param array|string $datamethod The property of the test class to pass to the function.
     * @param bool $csv True of the data is stored as a comma-separated list.
     * @return void
     */
    public function test_settings(
        string $setting,
        string $function,
        array|string $datamethod,
        bool $csv,
    ): void {
        $data = $this->get_data_from_datamethod($datamethod);
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $this->assertFalse(get_config('qbank_columnsortorder', $setting));
        $this->assertEmpty(get_user_preferences('qbank_columnsortorder_' . $setting));
        column_manager::{$function}($data, true);
        $expected = $csv ? implode(',', $data) : $data;
        $this->assertEquals($expected, get_config('qbank_columnsortorder', $setting));
        $this->assertEmpty(get_user_preferences('qbank_columnsortorder_' . $setting));
    }

    /**
     * Test passing null clears the corresponding config setting.
     *
     * @dataProvider settings_provider
     * @param string $setting The name of the setting being saved
     * @param string $function The name of the function being called
     * @param array|string $datamethod The property of the test class to pass to the function.
     * @param bool $csv True of the data is stored as a comma-separated list.
     * @return void
     */
    public function test_reset_settings(
        string $setting,
        string $function,
        array|string $datamethod,
        bool $csv,
    ): void {
        $data = $this->get_data_from_datamethod($datamethod);
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $initial = $csv ? implode(',', $data) : $data;
        set_config($setting, $initial, 'qbank_columnsortorder');
        $this->assertEquals($initial, get_config('qbank_columnsortorder', $setting));
        column_manager::{$function}(null, true);
        $this->assertFalse(get_config('qbank_columnsortorder', $setting));
    }

    /**
     * Test setting user preferences
     *
     * @dataProvider settings_provider
     * @param string $setting The name of the setting being saved
     * @param string $function The name of the function being called
     * @param array|string $datamethod The property of the test class to pass to the function.
     * @param bool $csv True of the data is stored as a comma-separated list.
     * @return void
     */
    public function test_settings_user(
        string $setting,
        string $function,
        array|string $datamethod,
        bool $csv,
    ): void {
        $this->resetAfterTest(true);
        $data = $this->get_data_from_datamethod($datamethod);
        $this->assertFalse(get_config('qbank_columnsortorder', $setting));
        $this->assertEmpty(get_user_preferences('qbank_columnsortorder_' . $setting));
        column_manager::{$function}($data);
        $expected = $csv ? implode(',', $data) : $data;
        $this->assertFalse(get_config('qbank_columnsortorder', $setting));
        $this->assertEquals($expected, get_user_preferences('qbank_columnsortorder_' . $setting));
    }

    /**
     * Test passing null clears the corresponding user preference.
     *
     * @dataProvider settings_provider
     * @param string $setting The name of the setting being saved
     * @param string $function The name of the function being called
     * @param array|string $datamethod The property of the test class to pass to the function.
     * @param bool $csv True of the data is stored as a comma-separated list.
     * @return void
     */
    public function test_reset_user_settings(
        string $setting,
        string $function,
        array|string $datamethod,
        bool $csv,
    ): void {
        $data = $this->get_data_from_datamethod($datamethod);
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $initial = $csv ? implode(',', $data) : $data;
        set_user_preference('qbank_columnsortorder_' . $setting, $initial);
        $this->assertEquals($initial, get_user_preferences('qbank_columnsortorder_' . $setting));
        column_manager::{$function}(null);
        $this->assertEmpty(get_user_preferences('qbank_columnsortorder_' . $setting));
    }

    /**
     * Test function get_columns in helper class, that proper data is returned.
     *
     * @covers ::get_columns
     */
    public function test_getcolumns_function(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $columnmanager = new column_manager(true);
        $questionlistcolumns = $columnmanager->get_columns();
        $this->assertIsArray($questionlistcolumns);
        foreach ($questionlistcolumns as $columnnobject) {
            $this->assertObjectHasProperty('class', $columnnobject);
            $this->assertObjectHasProperty('name', $columnnobject);
            $this->assertObjectHasProperty('colname', $columnnobject);
        }
    }

    /**
     * The get_sorted_columns method should return the provided columns sorted according to enabledcol setting.
     *
     * @return void
     */
    public function test_get_sorted_columns(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        $neworder = $columns;
        shuffle($neworder);
        set_config('enabledcol', implode(',', $neworder), 'qbank_columnsortorder');

        $columnmanager = new column_manager(true);
        $columnstosort = [];
        foreach ($columns as $column) {
            $columnstosort[$column] = $column;
        }

        $sortedcolumns = $columnmanager->get_sorted_columns($columnstosort);

        $expectedorder = ['core_question\local\bank\checkbox_column' . column_base::ID_SEPARATOR . 'checkbox_column' => 0];
        foreach ($neworder as $columnid) {
            $expectedorder[$columnid] = $columnid;
        }
        $this->assertSame($expectedorder, $sortedcolumns);
    }

    /**
     * Test disabled columns are removed from enabledcol setting and added to disabledcol setting.
     *
     * @return void
     */
    public function test_disable_columns(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        // Set up enabledcol with all plugins.
        set_config('enabledcol', implode(',', $columns), 'qbank_columnsortorder');
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        $columnmanager = new column_manager(true);
        $this->assertFalse(get_config('qbank_columnsortorder', 'disabledcol'));

        // Disable a random plugin.
        $plugincolumns = array_filter($columns, fn($column) => str_starts_with($column, 'qbank_'));
        $randomcolumn = $plugincolumns[array_rand($plugincolumns, 1)];
        $randomplugin = explode('\\', $randomcolumn)[0];
        $columnmanager->disable_columns($randomplugin);

        // The enabledcol setting should now contain all columns except the disabled plugin.
        $expectedconfig = array_filter($columns, fn($column) => !str_starts_with($column, $randomplugin));
        sort($expectedconfig);
        $newconfig = explode(',', get_config('qbank_columnsortorder', 'enabledcol'));
        sort($newconfig);
        $this->assertEquals($expectedconfig, $newconfig);
        $this->assertNotContains($randomcolumn, $newconfig);
        // The disabledcol setting should only contain columns from the disabled plugin.
        $disabledconfig = explode(',', get_config('qbank_columnsortorder', 'disabledcol'));
        array_walk($disabledconfig, fn($column) => $this->assertStringStartsWith($randomplugin, $column));
    }

    /**
     * Test enabling and disabling columns through event observers
     *
     * @covers \qbank_columnsortorder\event\plugin_observer
     */
    public function test_plugin_enabled_disabled_observers(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        $columnmanager = new column_manager(true);
        $neworder = $columnmanager->get_sorted_columns($columns);
        shuffle($neworder);
        $columnmanager::set_column_order($neworder, true);
        // Get the list of enabled columns, excluding core columns (we can't disable those).
        $currentconfig = get_config('qbank_columnsortorder', 'enabledcol');
        $currentconfig = array_filter(explode(',', $currentconfig), fn($class) => !str_starts_with($class, 'core'));
        // Pick a column at random and get its plugin name.
        $randomcolumnid = $currentconfig[array_rand($currentconfig, 1)];
        [$randomcolumnclass] = explode(column_base::ID_SEPARATOR, $randomcolumnid, 2);
        [$randomplugintodisable] = explode('\\', $randomcolumnclass);
        $olddisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        \core\event\qbank_plugin_disabled::create_for_plugin($randomplugintodisable)->trigger();
        $newdisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        $this->assertNotEquals($olddisabledconfig, $newdisabledconfig);
        \core\event\qbank_plugin_enabled::create_for_plugin($randomplugintodisable)->trigger();
        $newdisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        $this->assertEmpty($newdisabledconfig);
        $enabledconfig = get_config('qbank_columnsortorder', 'enabledcol');
        $contains = strpos($enabledconfig, $randomplugintodisable);
        $this->assertNotFalse($contains);
        $this->assertIsInt($contains);
    }

    /**
     * Test enabled columns are removed from disabledcol setting and added to enabledcol setting.
     *
     * @return void
     */
    public function test_enable_columns() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        // Set up disablecol with columns from 2 random plugins, and enabledcol with all other columns.
        $plugincolumns = array_filter($columns, fn($column) => str_starts_with($column, 'qbank_'));
        $plugins = array_unique(array_map(fn($column) => explode('\\', $column)[0], $plugincolumns));
        $randomplugins = array_rand($plugins, 2);
        $randomplugin1 = $plugins[$randomplugins[0]];
        $randomplugin2 = $plugins[$randomplugins[1]];

        $disabledcols = array_filter(
            $columns,
            fn($column) => str_starts_with($column, $randomplugin1) || str_starts_with($column, $randomplugin2)
        );
        $enabledcols = array_diff($columns, $disabledcols);

        set_config('enabledcol', implode(',', $enabledcols), 'qbank_columnsortorder');
        set_config('disabledcol', implode(',', $disabledcols), 'qbank_columnsortorder');

        // Enable one of the disabled plugins.
        $columnmanager = new column_manager(true);
        $columnmanager->enable_columns($randomplugin1);
        // The enabledcol setting should now contain all columns except the remaining disabled plugin.
        $expectedenabled = array_filter($columns, fn($column) => !str_starts_with($column, $randomplugin2));
        $expecteddisabled = array_filter($disabledcols, fn($column) => str_starts_with($column, $randomplugin2));
        sort($expectedenabled);
        sort($expecteddisabled);
        $newenabled = explode(',', get_config('qbank_columnsortorder', 'enabledcol'));
        sort($newenabled);
        $this->assertEquals($expectedenabled, $newenabled);
        $this->assertNotContains(reset($expecteddisabled), $newenabled);
        // The disabledcol setting should only contain columns from the remaining disabled plugin.
        $newdisabled = explode(',', get_config('qbank_columnsortorder', 'disabledcol'));
        array_walk($newdisabled, fn($column) => $this->assertStringStartsWith($randomplugin2, $column));
    }

    /**
     * Test that get_disabled_columns returns names of all the columns in the disabledcol setting
     *
     * @return void
     */
    public function test_get_disabled_columns(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $questionbank = $this->get_question_bank();
        $columns = $this->get_columns($questionbank);
        // Set up disablecol with columns from 2 random plugins, and enabledcol with all other columns.
        $plugincolumns = array_filter($columns, fn($column) => str_starts_with($column, 'qbank_'));
        $randomcolumn = $plugincolumns[array_rand($plugincolumns, 1)];
        $randomplugin = explode('\\', $randomcolumn)[0];

        $disabledcols = array_filter($columns, fn($column) => str_starts_with($column, $randomplugin));

        set_config('disabledcol', implode(',', $disabledcols), 'qbank_columnsortorder');

        $columnmanager = new column_manager(true);
        $expecteddisablednames = [];
        foreach ($disabledcols as $disabledcolid) {
            [$columnclass, $columnname] = explode(column_base::ID_SEPARATOR, $disabledcolid, 2);
            $columnobject = $columnclass::from_column_name($questionbank, $columnname);
            $expecteddisablednames[$disabledcolid] = (object) [
                'disabledname' => $columnobject->get_title(),
            ];
        }
        $disablednames = $columnmanager->get_disabled_columns();
        $this->assertEquals($expecteddisablednames, $disablednames);
    }
}
