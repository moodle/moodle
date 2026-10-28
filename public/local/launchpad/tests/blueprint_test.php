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

namespace local_launchpad;

/**
 * Blueprint shape tests (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\blueprint
 */
final class blueprint_test extends \basic_testcase {

    public function test_track_categories_are_exactly_five_tracks(): void {
        $cats = blueprint::track_categories();
        $this->assertCount(5, $cats);
        $this->assertArrayNotHasKey('launchpad-orientation', $cats);
        $this->assertSame('AI & Automation', $cats['launchpad-ai']);
        $this->assertSame('Full Stack Development', $cats['launchpad-fullstack']);
        $this->assertSame('Product Management', $cats['launchpad-pm']);
        $this->assertSame('Digital Marketing', $cats['launchpad-marketing']);
        $this->assertSame('Startup Building', $cats['launchpad-startup']);
    }

    public function test_phase_sections_are_four_named_phases(): void {
        $phases = blueprint::phase_sections();
        $this->assertSame([
            1 => 'Foundations',
            2 => 'Guided project',
            3 => 'Industry simulation',
            4 => 'Demo day and certification',
        ], $phases);
    }

    public function test_courses_are_orientation_under_root_plus_five_tracks(): void {
        $courses = blueprint::courses('2026-Pilot');
        $this->assertCount(6, $courses);
        $this->assertSame('orientation', $courses[0]['kind']);
        $this->assertSame(blueprint::ROOT_IDNUMBER, $courses[0]['category_idnumber']);
        $this->assertStringContainsString('Cohort 2026-Pilot', $courses[0]['fullname']);

        $trackcats = [];
        foreach (array_slice($courses, 1) as $course) {
            $this->assertSame('track', $course['kind']);
            $trackcats[] = $course['category_idnumber'];
            $this->assertStringStartsWith('lp-', $course['idnumber']);
        }
        $this->assertSame(array_keys(blueprint::track_categories()), $trackcats);

        $shortnames = array_column($courses, 'shortname');
        $this->assertCount(6, array_unique($shortnames));
        $idnumbers = array_column($courses, 'idnumber');
        $this->assertCount(6, array_unique($idnumbers));
    }

    public function test_orientation_activities_stay_in_general_section(): void {
        $acts = blueprint::activities_for('orientation');
        foreach ($acts as $act) {
            $this->assertSame(0, $act['section']);
        }
    }

    public function test_track_activities_use_page_quiz_assign_only(): void {
        $acts = blueprint::activities_for('track');
        $modules = array_unique(array_column($acts, 'modulename'));
        sort($modules);
        $this->assertSame(['assign', 'page', 'quiz'], $modules);
        $this->assertNotContains('resource', $modules, 'No video stubs without recordings.');
        $sections = array_unique(array_column($acts, 'section'));
        $this->assertContains(0, $sections);
        $this->assertContains(1, $sections);
        $this->assertContains(4, $sections);
    }

    public function test_cohort_slug_sanitizes(): void {
        $this->assertSame('2026-pilot', blueprint::cohort_slug('2026-Pilot'));
    }

    public function test_cohort_slug_rejects_empty_result_without_fallback(): void {
        $this->expectException(cohort_exception::class);
        blueprint::cohort_slug('@@@');
    }
}
