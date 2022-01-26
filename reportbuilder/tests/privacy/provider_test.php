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

namespace core_reportbuilder\privacy;

use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\database_table;
use core_privacy\local\metadata\types\user_preference;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\local\models\audience;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\schedule;

/**
 * Unit tests for privacy provider
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\privacy\provider
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Test provider metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('core_reportbuilder');
        $metadata = provider::get_metadata($collection)->get_collection();

        $this->assertCount(6, $metadata);

        $this->assertInstanceOf(database_table::class, $metadata[0]);
        $this->assertEquals(report::TABLE, $metadata[0]->get_name());

        $this->assertInstanceOf(database_table::class, $metadata[1]);
        $this->assertEquals(column::TABLE, $metadata[1]->get_name());

        $this->assertInstanceOf(database_table::class, $metadata[2]);
        $this->assertEquals(filter::TABLE, $metadata[2]->get_name());

        $this->assertInstanceOf(database_table::class, $metadata[3]);
        $this->assertEquals(audience::TABLE, $metadata[3]->get_name());

        $this->assertInstanceOf(database_table::class, $metadata[4]);
        $this->assertEquals(schedule::TABLE, $metadata[4]->get_name());

        $this->assertInstanceOf(user_preference::class, $metadata[5]);
    }

    /**
     * Test to check export_user_preferences.
     */
    public function test_export_user_preferences(): void {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        // Create report and set some filters for the user.
        $report1 = manager::create_report_persistent((object) [
            'type' => 1,
            'source' => 'class',
        ]);
        $filtervalues1 = [
            'task_log:name_operator' => 0,
            'task_log:name_value' => 'My task logs',
        ];
        user_filter_manager::set($report1->get('id'), $filtervalues1);

        // Add a filter for user2.
        $filtervalues1user2 = [
            'task_log:name_operator' => 0,
            'task_log:name_value' => 'My task logs user2',
        ];
        user_filter_manager::set($report1->get('id'), $filtervalues1user2, (int)$user2->id);

        // Create a second report and set some filters for the user.
        $report2 = manager::create_report_persistent((object) [
            'type' => 1,
            'source' => 'class',
        ]);
        $filtervalues2 = [
            'config_change:setting_operator' => 0,
            'config_change:setting_value' => str_repeat('A', 3000),
        ];
        user_filter_manager::set($report2->get('id'), $filtervalues2);

        // Switch to admin user (so we can validate preferences of our test user are still exported).
        $this->setAdminUser();

        // Export user preferences.
        provider::export_user_preferences((int)$user1->id);
        $writer = writer::with_context(context_system::instance());
        $prefs = $writer->get_user_preferences('core_reportbuilder');

        // Check that user preferences only contain the 2 preferences from user1.
        $this->assertCount(2, (array)$prefs);

        // Check that exported user preferences for report1 are correct.
        $report1key = 'reportbuilder-report-' . $report1->get('id');
        $this->assertEquals(json_encode($filtervalues1, JSON_PRETTY_PRINT), $prefs->$report1key->value);

        // Check that exported user preferences for report2 are correct.
        $report2key = 'reportbuilder-report-' . $report2->get('id');
        $this->assertEquals(json_encode($filtervalues2, JSON_PRETTY_PRINT), $prefs->$report2key->value);
    }
}
