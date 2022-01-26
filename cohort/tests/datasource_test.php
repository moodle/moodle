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

use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\manager;
use core_reportbuilder\local\filters\user;
use core_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for cohorts datasource
 *
 * @package     core_cohort
 * @covers      \core_cohort\reportbuilder\datasource\cohorts
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datasource_test extends core_reportbuilder_testcase {

    /**
     * Test cohorts datasource
     */
    public function test_cohorts_datasource(): void {
        $this->resetAfterTest();

        // Test subject.
        $cohort = $this->getDataGenerator()->create_cohort([
            'name' => 'Legends',
            'idnumber' => 'C101',
            'description' => 'Cohort for the legends',
        ]);

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Lionel', 'lastname' => 'Richards']);
        cohort_add_member($cohort->id, $user->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Cohorts', 'source' => cohorts::class]);

        // Add user fullname column to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $contentrow = array_values(reset($content));
        $this->assertEquals([
            'System', // Context.
            'Legends', // Name.
            'C101', // ID number.
            '<div class="text_to_html">Cohort for the legends</div>', // Description.
            'Lionel Richards', // User.
        ], $contentrow);
    }

    /**
     * Data provider for {@see test_cohorts_datasource_user_select}
     *
     * @return array[]
     */
    public function cohorts_datasource_user_select_provider(): array {
        return [
            ['user01', 'Cohort01'],
            ['user02', 'Cohort02'],
        ];
    }

    /**
     * Test cohorts datasource, while adding the user select condition
     *
     * @param string $username
     * @param string $expectedcohort
     *
     * @dataProvider cohorts_datasource_user_select_provider
     */
    public function test_cohorts_datasource_user_select(string $username, string $expectedcohort): void {
        $this->resetAfterTest();

        // First cohort/user member.
        $cohort01 = $this->getDataGenerator()->create_cohort(['name' => 'Cohort01']);
        $user01 = $this->getDataGenerator()->create_user(['username' => 'user01']);
        cohort_add_member($cohort01->id, $user01->id);

        // Second cohort/user member.
        $cohort02 = $this->getDataGenerator()->create_cohort(['name' => 'Cohort02']);
        $user02 = $this->getDataGenerator()->create_user(['username' => 'user02']);
        cohort_add_member($cohort02->id, $user02->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'User cohorts', 'source' => cohorts::class, 'default' => 0]);

        // Add cohort name and user fullname columns.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

        // Add condition to limit report data to current user.
        $condition = $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:userselect']);
        manager::get_report_from_persistent($report)->set_condition_values([
            $condition->get('uniqueidentifier') . '_operator' => user::USER_CURRENT,
        ]);

        // Switch user, request report.
        $currentuser = core_user::get_user_by_username($username);
        $this->setUser($currentuser);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $contentrow = array_values(reset($content));
        $this->assertEquals([$expectedcohort, $username], $contentrow);
    }
}
