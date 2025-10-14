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

namespace core_badges\reportbuilder\local\filters;

use advanced_testcase;
use award_criteria;
use core\lang_string;
use core_badges_generator;
use core_reportbuilder\local\report\filter;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Unit tests for badge criteria report filter
 *
 * @package     core_badges
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(criteria::class)]
final class criteria_test extends advanced_testcase {
    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public static function get_sql_filter_simple_provider(): array {
        return [
            [criteria::ANY_VALUE, null, ['badgeone', 'badgetwo', 'badgethree']],
            [criteria::EQUAL_TO, BADGE_CRITERIA_TYPE_MANUAL, ['badgeone']],
            [criteria::EQUAL_TO, BADGE_CRITERIA_TYPE_COURSE, ['badgetwo', 'badgethree']],
            [criteria::EQUAL_TO, BADGE_CRITERIA_TYPE_COHORT, []],
            [criteria::NOT_EQUAL_TO, BADGE_CRITERIA_TYPE_MANUAL, ['badgetwo', 'badgethree']],
            [criteria::NOT_EQUAL_TO, BADGE_CRITERIA_TYPE_COURSE, ['badgeone']],
            [criteria::NOT_EQUAL_TO, BADGE_CRITERIA_TYPE_COHORT, ['badgeone', 'badgetwo', 'badgethree']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param int|null $value
     * @param string[] $expectmatch
     */
    #[DataProvider('get_sql_filter_simple_provider')]
    public function test_get_sql_filter_simple(int $operator, ?int $value, array $expectmatch): void {
        global $DB;

        $this->resetAfterTest();

        $courseid = $this->getDataGenerator()->create_course()->id;
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        // First badge has two criteria: manual by role; email profile field.
        $badgeone = $generator->create_badge(['name' => 'badgeone']);
        award_criteria::build(['badgeid' => $badgeone->id, 'criteriatype' => BADGE_CRITERIA_TYPE_MANUAL])
            ->save(["role_{$managerroleid}" => $managerroleid]);
        award_criteria::build(['badgeid' => $badgeone->id, 'criteriatype' => BADGE_CRITERIA_TYPE_PROFILE])
            ->save(['profilefield_email' => 'email']);

        // Second badge has two criteria: completion of course; email profile field.
        $badgetwo = $generator->create_badge(['name' => 'badgetwo']);
        award_criteria::build(['badgeid' => $badgetwo->id, 'criteriatype' => BADGE_CRITERIA_TYPE_COURSE])
            ->save(["course_{$courseid}" => $courseid]);
        award_criteria::build(['badgeid' => $badgetwo->id, 'criteriatype' => BADGE_CRITERIA_TYPE_PROFILE])
            ->save(['profilefield_email' => 'email']);

        // Third badge has one criteria: completion of course.
        $badgethree = $generator->create_badge(['name' => 'badgethree']);
        award_criteria::build(['badgeid' => $badgethree->id, 'criteriatype' => BADGE_CRITERIA_TYPE_COURSE])
            ->save(["course_{$courseid}" => $courseid]);

        $filter = new filter(
            criteria::class,
            'test',
            new lang_string('bcriteria', 'core_badges'),
            'testentity',
            'b.id',
        );

        // Create instance of our filter, passing given operator/value.
        [$select, $params] = criteria::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
        ]);

        $badgenames = $DB->get_fieldset_sql('SELECT b.name FROM {badge} b WHERE ' . ($select ?: '1=1'), $params);
        $this->assertEqualsCanonicalizing($expectmatch, $badgenames);
    }
}
