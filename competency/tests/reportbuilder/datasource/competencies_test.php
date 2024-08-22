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

use core_competency_generator;
use core_competency\user_competency;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for comptencies datasource
 *
 * @package     core_competency
 * @covers      \core_competency\reportbuilder\datasource\competencies
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class competencies_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        /** @var core_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_competency');

        // First framework has two competencies.
        $framework = $generator->create_framework(['shortname' => 'Zoology']);
        $generator->create_competency(['competencyframeworkid' => $framework->get('id'), 'shortname' => 'Zebras']);
        $competency = $generator->create_competency([
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'Aardvarks',
        ]);

        // Framework two has no competencies.
        $generator->create_framework(['shortname' => 'Algebra']);

        // Assign user to second competency.
        $generator->create_user_competency([
            'competencyid' => $competency->get('id'),
            'userid' => $user->id,
            'proficiency' => true,
            'grade' => 1,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Competencies', 'source' => competencies::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are framework name, competency name, user fullname, proficient. Sorted by first three.
        $this->assertEquals([
            ['Algebra', '', '', ''],
            ['Zoology', 'Aardvarks', fullname($user), 'Yes'],
            ['Zoology', 'Zebras', '', ''],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $cohort = $this->getDataGenerator()->create_cohort(['name' => 'My cohort']);
        cohort_add_member($cohort->id, $user->id);

        $scale = $this->getDataGenerator()->create_scale(['name' => 'My scale', 'scale' => 'A,B,C,D']);

        /** @var core_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $generator->create_framework(['description' => 'So cool', 'idnumber' => 'FRM101', 'scaleid' => $scale->id]);
        $competency = $generator->create_competency(['competencyframeworkid' => $framework->get('id'), 'idnumber' => 'COM101']);
        $generator->create_user_competency([
            'competencyid' => $competency->get('id'),
            'userid' => $user->id,
            'proficiency' => true,
            'grade' => 3,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Competencies', 'source' => competencies::class, 'default' => 0]);

        // Framework.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:description']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:idnumber']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:scale']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:visible']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:timemodified']);

        // Competency.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'competency:idnumber']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'competency:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'competency:timemodified']);

        // User competency.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'usercompetency:status']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'usercompetency:rating']);

        // Cohort.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        [
            $frameworkdescription,
            $frameworkidnumber,
            $frameworkscale,
            $frameworkvisible,
            $frameworktimecreated,
            $frameworktimemodified,
            $competencyidnumber,
            $competencytimecreated,
            $competencytimemodified,
            $usercompetencystatus,
            $usercompetencyrating,
            $cohortname,
        ] = array_values($content[0]);

        $this->assertEquals('So cool', $frameworkdescription);
        $this->assertEquals('FRM101', $frameworkidnumber);
        $this->assertEquals('My scale', $frameworkscale);
        $this->assertEquals('Yes', $frameworkvisible);
        $this->assertNotEmpty($frameworktimecreated);
        $this->assertNotEmpty($frameworktimemodified);
        $this->assertEquals('COM101', $competencyidnumber);
        $this->assertNotEmpty($competencytimecreated);
        $this->assertNotEmpty($competencytimemodified);
        $this->assertEquals('Idle', $usercompetencystatus);
        $this->assertEquals('C', $usercompetencyrating);
        $this->assertEquals('My cohort', $cohortname);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Framework.
            'Framework name' => ['framework:name', [
                'framework:name_operator' => text::IS_EQUAL_TO,
                'framework:name_value' => 'How much I care',
            ], true],
            'Framework name (no match)' => ['framework:name', [
                'framework:name_operator' => text::IS_EQUAL_TO,
                'framework:name_value' => 'Something else',
            ], false],
            'Framework idnumber' => ['framework:idnumber', [
                'framework:idnumber_operator' => text::IS_EQUAL_TO,
                'framework:idnumber_value' => 'FRM101',
            ], true],
            'Framework idnumber (no match)' => ['framework:idnumber', [
                'framework:idnumber_operator' => text::IS_EQUAL_TO,
                'framework:idnumber_value' => 'FRM102',
            ], false],
            'Framework scale' => ['framework:scale', [
                'framework:scale_operator' => select::EQUAL_TO,
                'framework:scale_value' => '<SCALEID>',
            ], true],
            'Framework scale (no match)' => ['framework:scale', [
                'framework:scale_operator' => select::EQUAL_TO,
                'framework:scale_value' => -1,
            ], false],
            'Framework visible' => ['framework:visible', [
                'framework:visible_operator' => boolean_select::CHECKED,
            ], true],
            'Framework visible (no match)' => ['framework:visible', [
                'framework:visible_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Framework time created' => ['framework:timecreated', [
                'framework:timecreated_operator' => date::DATE_RANGE,
                'framework:timecreated_from' => 1622502000,
            ], true],
            'Framework time created (no match)' => ['framework:timecreated', [
                'framework:timecreated_operator' => date::DATE_RANGE,
                'framework:timecreated_to' => 1622502000,
            ], false],
            'Framework time modified' => ['framework:timemodified', [
                'framework:timemodified_operator' => date::DATE_RANGE,
                'framework:timemodified_from' => 1622502000,
            ], true],
            'Framework time modified (no match)' => ['framework:timemodified', [
                'framework:timemodified_operator' => date::DATE_RANGE,
                'framework:timemodified_to' => 1622502000,
            ], false],

            // Competency.
            'Competency name' => ['competency:name', [
                'competency:name_operator' => text::IS_EQUAL_TO,
                'competency:name_value' => 'My framework',
            ], true],
            'Competency name (no match)' => ['competency:name', [
                'competency:name_operator' => text::IS_EQUAL_TO,
                'competency:name_value' => 'Something else',
            ], false],
            'Competency idnumber' => ['competency:idnumber', [
                'competency:idnumber_operator' => text::IS_EQUAL_TO,
                'competency:idnumber_value' => 'COM101',
            ], true],
            'Competency idnumber (no match)' => ['competency:idnumber', [
                'competency:idnumber_operator' => text::IS_EQUAL_TO,
                'competency:idnumber_value' => 'COM102',
            ], false],
            'Competency time created' => ['competency:timecreated', [
                'competency:timecreated_operator' => date::DATE_RANGE,
                'competency:timecreated_from' => 1622502000,
            ], true],
            'Competency time created (no match)' => ['competency:timecreated', [
                'competency:timecreated_operator' => date::DATE_RANGE,
                'competency:timecreated_to' => 1622502000,
            ], false],
            'Competency time modified' => ['competency:timemodified', [
                'competency:timemodified_operator' => date::DATE_RANGE,
                'competency:timemodified_from' => 1622502000,
            ], true],
            'Competency time modified (no match)' => ['competency:timemodified', [
                'competency:timemodified_operator' => date::DATE_RANGE,
                'competency:timemodified_to' => 1622502000,
            ], false],

            // User competency.
            'User competency status' => ['usercompetency:status', [
                'usercompetency:status_operator' => SELECT::EQUAL_TO,
                'usercompetency:status_value' => user_competency::STATUS_IDLE,
            ], true],
            'User competency status (no match)' => ['usercompetency:status', [
                'usercompetency:status_operator' => SELECT::EQUAL_TO,
                'usercompetency:status_value' => user_competency::STATUS_WAITING_FOR_REVIEW,
            ], false],
            'User competency proficient' => ['usercompetency:proficient', [
                'usercompetency:proficient_operator' => boolean_select::CHECKED,
            ], true],
            'User competency proficient (no match)' => ['usercompetency:proficient', [
                'usercompetency:proficient_operator' => boolean_select::NOT_CHECKED,
            ], false],

            // User.
            'User username' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'testuser',
            ], true],
            'User username (no match)' => ['user:username', [
                'user:username_operator' => text::IS_NOT_EQUAL_TO,
                'user:username_value' => 'testuser',
            ], false],

            // Cohort.
            'Cohort name' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'My cohort',
            ], true],
            'Cohort name (no match)' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'Another cohort',
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
    public function test_datasource_filters(
        string $filtername,
        array $filtervalues,
        bool $expectmatch
    ): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['username' => 'testuser']);
        $cohort = $this->getDataGenerator()->create_cohort(['name' => 'My cohort']);
        cohort_add_member($cohort->id, $user->id);

        $scale = $this->getDataGenerator()->create_scale([]);

        /** @var core_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $generator->create_framework([
            'shortname' => 'How much I care',
            'description' => 'Sometimes I feel my heart will overflow',
            'idnumber' => 'FRM101',
            'scaleid' => $scale->id,
        ]);
        $competency = $generator->create_competency([
            'competencyframeworkid' => $framework->get('id'),
            'shortname' => 'My framework',
            'idnumber' => 'COM101',
        ]);
        $generator->create_user_competency([
            'competencyid' => $competency->get('id'),
            'userid' => $user->id,
            'proficiency' => true,
            'grade' => 3,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Competencies', 'source' => competencies::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'framework:idnumber']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content(
            reportid: $report->get('id'),
            filtervalues: str_replace('<SCALEID>', $scale->id, $filtervalues),
        );

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('FRM101', reset($content[0]));
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

        $user = $this->getDataGenerator()->create_user();

        /** @var core_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_competency');

        $framework = $generator->create_framework();
        $competency = $generator->create_competency(['competencyframeworkid' => $framework->get('id')]);
        $generator->create_user_competency(['competencyid' => $competency->get('id'), 'userid' => $user->id]);

        $this->datasource_stress_test_columns(competencies::class);
        $this->datasource_stress_test_columns_aggregation(competencies::class);
        $this->datasource_stress_test_conditions(competencies::class, 'framework:idnumber');
    }
}
