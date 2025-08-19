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

use core\context\{coursecat, system};
use core_customfield_generator;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for cohorts datasource
 *
 * @package     core_cohort
 * @covers      \core_cohort\reportbuilder\datasource\cohorts
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cohorts_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        // Test subject.
        $contextsystem = system::instance();
        $cohortone = $this->getDataGenerator()->create_cohort([
            'contextid' => $contextsystem->id,
            'name' => 'Legends',
            'idnumber' => 'C101',
            'description' => 'Cohort for the legends',
        ]);

        $category = $this->getDataGenerator()->create_category();
        $contextcategory = coursecat::instance($category->id);
        $cohorttwo = $this->getDataGenerator()->create_cohort([
            'contextid' => $contextcategory->id,
            'name' => 'Category cohort',
            'description' => 'This is my category cohort',
        ]);

        // Non-visible cohort (excluded by default).
        $cohortnonvisible = $this->getDataGenerator()->create_cohort([
            'contextid' => $contextsystem->id,
            'name' => 'Non-visible',
            'visible' => false,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Cohorts', 'source' => cohorts::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are name, context, idnumber, description. Sorted by name.
        $this->assertEquals([
            [$cohorttwo->name, $contextcategory->get_context_name(false), $cohorttwo->idnumber,
                format_text($cohorttwo->description)],
            [$cohortone->name, $contextsystem->get_context_name(false), $cohortone->idnumber,
                format_text($cohortone->description)],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('allowcohortthemes', true);

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $fieldcategory = $generator->create_category(['component' => 'core_cohort', 'area' => 'cohort']);
        $field = $generator->create_field(['categoryid' => $fieldcategory->get('id'), 'shortname' => 'hi']);

        // Test subject.
        $cohortone = $this->getDataGenerator()->create_cohort([
            'name' => 'Cohort 1',
            'theme' => 'boost',
            'customfield_hi' => 'Hello',
        ]);
        $cohorttwo = $this->getDataGenerator()->create_cohort(['name' => 'Cohort 2']);

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Lionel', 'lastname' => 'Richards']);
        cohort_add_member($cohortone->id, $user->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Cohorts', 'source' => cohorts::class, 'default' => 0]);

        // Cohort.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name',
            'sortenabled' => true]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:visible']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:timemodified']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:theme']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:customfield_hi']);

        // Cohort member.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort_member:timeadded']);

        // User.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        [, $visible, $timecreated, $timemodified, $component, $theme, $custom, $timeadded, $fullname] =
            array_values($content[0]);
        $this->assertEquals('Yes', $visible);
        $this->assertNotEmpty($timecreated);
        $this->assertNotEmpty($timemodified);
        $this->assertEquals('Created manually', $component);
        $this->assertEquals('Boost', $theme);
        $this->assertEquals('Hello', $custom);
        $this->assertNotEmpty($timeadded);
        $this->assertEquals(fullname($user), $fullname);

        [, $visible, $timecreated, $timemodified, $component, $theme, $custom, $timeadded, $fullname] =
            array_values($content[1]);
        $this->assertEquals('Yes', $visible);
        $this->assertNotEmpty($timecreated);
        $this->assertNotEmpty($timemodified);
        $this->assertEquals('Created manually', $component);
        $this->assertEquals('Do not force', $theme);
        $this->assertEmpty($custom);
        $this->assertEmpty($timeadded);
        $this->assertEmpty($fullname);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Cohort.
            'Filter cohort' => ['cohort:cohortselect', [
                'cohort:cohortselect_values' => [-1],
            ], false],
            'Filter context' => ['cohort:context', [
                'cohort:context_operator' => select::EQUAL_TO,
                'cohort:context_value' => system::instance()->id,
            ], true],
            'Filter context (no match)' => ['cohort:context', [
                'cohort:context_operator' => select::NOT_EQUAL_TO,
                'cohort:context_value' => system::instance()->id,
            ], false],
            'Filter name' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'Legends',
            ], true],
            'Filter name (no match)' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'Dancing',
            ], false],
            'Filter idnumber' => ['cohort:idnumber', [
                'cohort:idnumber_operator' => text::IS_EQUAL_TO,
                'cohort:idnumber_value' => 'C101',
            ], true],
            'Filter idnumber (no match)' => ['cohort:idnumber', [
                'cohort:idnumber_operator' => text::IS_EQUAL_TO,
                'cohort:idnumber_value' => 'C102',
            ], false],
            'Filter time created' => ['cohort:timecreated', [
                'cohort:timecreated_operator' => date::DATE_RANGE,
                'cohort:timecreated_from' => 1622502000,
            ], true],
            'Filter time created (no match)' => ['cohort:timecreated', [
                'cohort:timecreated_operator' => date::DATE_RANGE,
                'cohort:timecreated_to' => 1622502000,
            ], false],
            'Filter description' => ['cohort:description', [
                'cohort:description_operator' => text::CONTAINS,
                'cohort:description_value' => 'legends',
            ], true],
            'Filter description (no match)' => ['cohort:description', [
                'cohort:description_operator' => text::IS_EMPTY,
            ], false],
            'Filter theme' => ['cohort:theme', [
                'cohort:theme_operator' => select::EQUAL_TO,
                'cohort:theme_value' => 'boost',
            ], true],
            'Filter theme (no match)' => ['cohort:theme', [
                'cohort:theme_operator' => select::EQUAL_TO,
                'cohort:theme_value' => '',
            ], false],
            'Filter visible' => ['cohort:visible', [
                'cohort:visible_operator' => boolean_select::CHECKED,
            ], true],
            'Filter visible (no match)' => ['cohort:visible', [
                'cohort:visible_operator' => boolean_select::NOT_CHECKED,
            ], false],

            // Cohort member.
            'Filter time added' => ['cohort_member:timeadded', [
                'cohort_member:timeadded_operator' => date::DATE_RANGE,
                'cohort_member:timeadded_from' => 1622502000,
            ], true],
            'Filter time added (no match)' => ['cohort_member:timeadded', [
                'cohort_member:timeadded_operator' => date::DATE_RANGE,
                'cohort_member:timeadded_to' => 1622502000,
            ], false],

            // User.
            'Filter user' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'lionel',
            ], true],
            'Filter user (no match)' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'rick',
            ], false],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(string $filtername, array $filtervalues, bool $expectmatch): void {
        $this->resetAfterTest();

        set_config('allowcohortthemes', true);

        // Test subject.
        $cohort = $this->getDataGenerator()->create_cohort([
            'name' => 'Legends',
            'idnumber' => 'C101',
            'description' => 'Cohort for the legends',
            'theme' => 'boost',
        ]);

        $user = $this->getDataGenerator()->create_user(['username' => 'lionel']);
        cohort_add_member($cohort->id, $user->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Cohorts', 'source' => cohorts::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('Legends', reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test datasource
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort->id, $user->id);

        $this->datasource_stress_test_columns(cohorts::class);
        $this->datasource_stress_test_columns_aggregation(cohorts::class);
        $this->datasource_stress_test_conditions(cohorts::class, 'cohort:name');
    }
}
