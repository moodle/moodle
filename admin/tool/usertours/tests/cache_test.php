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

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/helper_trait.php');

/**
 * Tests for cache.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \tool_usertours\cache
 */
final class cache_test extends \advanced_testcase {
    // There are shared helpers for these tests in the helper trait.
    use \tool_usertours_helper_trait;

    /**
     * Test that get_enabled_tourdata does not return disabled tours.
     */
    public function test_get_enabled_tourdata_disabled(): void {
        $this->resetAfterTest();

        $tour = $this->helper_create_tour((object)['enabled' => false]);
        $this->helper_create_step((object) ['tourid' => $tour->get_id()]);

        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEmpty($matches);
    }

    /**
     * Test that get_enabled_tourdata does not return an enabled but empty tour.
     */
    public function test_get_enabled_tourdata_enabled_no_steps(): void {
        $this->resetAfterTest();

        $this->helper_create_tour();

        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEmpty($matches);
    }

    /**
     * Test that get_enabled_tourdata returns a tour with steps.
     */
    public function test_get_enabled_tourdata_enabled(): void {
        $this->resetAfterTest();

        // Create two tours. Only the second has steps.
        $this->helper_create_tour();
        $tour2 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour2->get_id()]);

        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertNotEmpty($matches);
        $this->assertCount(1, $matches);

        $match = array_shift($matches);
        $this->assertEquals($tour2->get_id(), $match->id);
    }

    /**
     * Test that get_enabled_tourdata returns tours in the correct sortorder
     */
    public function test_get_enabled_tourdata_enabled_sortorder(): void {
        $this->resetAfterTest();

        $tour1 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $tour2 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour2->get_id()]);

        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertNotEmpty($matches);
        $this->assertCount(2, $matches);

        $match = array_shift($matches);
        $this->assertEquals($tour1->get_id(), $match->id);
        $match = array_shift($matches);
        $this->assertEquals($tour2->get_id(), $match->id);
    }

    /**
     * Test that caching prevents additional DB reads.
     */
    public function test_get_enabled_tourdata_single_fetch(): void {
        global $DB;

        $this->resetAfterTest();

        $tour1 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $tour2 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour2->get_id()]);

        // Only one read for the first call.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // No subsequent reads for any further calls.
        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);
    }

    /**
     * Data provider for get_matching_tourdata.
     *
     * @return  array
     */
    public static function get_matching_tourdata_provider(): array {
        $tourconfigs = [
            (object) [
                'name' => 'my_exact_1',
                'pathmatch' => '/my/view.php',
            ],
            (object) [
                'name' => 'my_failed_regex',
                'pathmatch' => '/my/*.php',
            ],
            (object) [
                'name' => 'my_glob_1',
                'pathmatch' => '/my/%',
            ],
            (object) [
                'name' => 'my_glob_2',
                'pathmatch' => '/my/%',
            ],
            (object) [
                'name' => 'frontpage_only',
                'pathmatch' => 'FRONTPAGE',
            ],
            (object) [
                'name' => 'frontpage_match',
                'pathmatch' => '/?%',
            ],
        ];

        return [
            'Matches expected glob' => [
                $tourconfigs,
                '/my/index.php',
                ['my_glob_1', 'my_glob_2'],
            ],
            'Matches expected glob and exact' => [
                $tourconfigs,
                '/my/view.php',
                ['my_exact_1', 'my_glob_1', 'my_glob_2'],
            ],
            'Special constant FRONTPAGE must match front page only' => [
                $tourconfigs,
                '/',
                ['frontpage_only'],
            ],
            'Standard frontpage URL matches both the special constant, and a correctly formed pathmatch' => [
                $tourconfigs,
                '/?redirect=0',
                ['frontpage_only', 'frontpage_match'],
            ],
        ];
    }

    /**
     * Tests for the get_matching_tourdata function.
     *
     * @dataProvider    get_matching_tourdata_provider
     * @param   array   $tourconfigs    The configuration for the tours to create
     * @param   string  $targetmatch    The match to be tested
     * @param   array   $expected       An array containing the ordered names of the expected tours
     */
    public function test_get_matching_tourdata($tourconfigs, $targetmatch, $expected): void {
        $this->resetAfterTest();
        foreach ($tourconfigs as $tourconfig) {
            $tour = $this->helper_create_tour($tourconfig);
            $this->helper_create_step((object) ['tourid' => $tour->get_id()]);
        }

        $matches = \tool_usertours\cache::get_matching_tourdata(new \moodle_url($targetmatch));
        $this->assertCount(count($expected), $matches);

        for ($i = 0; $i < count($matches); $i++) {
            $match = array_shift($matches);
            $this->assertEquals($expected[$i], $match->name);
        }
    }

    /**
     * Test that notify_tour_change clears the cache.
     */
    public function test_notify_tour_change(): void {
        global $DB;

        $this->resetAfterTest();

        $tour1 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $tour2 = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour2->get_id()]);

        // Only one read for the first call.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // No subsequent reads for any further calls.
        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // Reset.
        \tool_usertours\cache::notify_tour_change();

        // An additional DB read now.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_enabled_tourdata();
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);
    }

    /**
     * Test that get_stepdata returns an empty array when no steps were found.
     */
    public function test_get_stepdata_no_steps(): void {
        $this->resetAfterTest();

        $tour = $this->helper_create_tour((object)['enabled' => false]);

        $data = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    /**
     * Test that get_stepdata returns an empty array when no steps were found.
     */
    public function test_get_stepdata_correct_tour(): void {
        $this->resetAfterTest();

        $tour1 = $this->helper_create_tour((object)['enabled' => false]);
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $this->helper_create_step((object) ['tourid' => $tour1->get_id()]);
        $tour2 = $this->helper_create_tour((object)['enabled' => false]);

        $data = \tool_usertours\cache::get_stepdata($tour1->get_id());
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        $data = \tool_usertours\cache::get_stepdata($tour2->get_id());
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    /**
     * Test that get_stepdata returns an array containing multiple steps in
     * the same order.
     *
     * This is very difficult to determine because the act of changing the
     * order will likely change the DB natural sorting.
     */
    public function test_get_stepdata_ordered_steps(): void {
        $this->resetAfterTest();

        $tour = $this->helper_create_tour((object)['enabled' => false]);
        $steps = [];
        $steps[] = $this->helper_create_step((object) ['tourid' => $tour->get_id()]);
        $steps[] = $this->helper_create_step((object) ['tourid' => $tour->get_id()]);
        $steps[] = $this->helper_create_step((object) ['tourid' => $tour->get_id()]);
        $steps[] = $this->helper_create_step((object) ['tourid' => $tour->get_id()]);
        $steps[0]->set_sortorder(10)->persist();

        $data = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertIsArray($data);
        $this->assertCount(4, $data);

        // Re-order the steps.
        usort($steps, function ($a, $b) {
            return ($a->get_sortorder() < $b->get_sortorder()) ? -1 : 1;
        });

        for ($i = 0; $i < count($data); $i++) {
            $step = array_shift($data);
            $this->assertEquals($steps[$i]->get_id(), $step->id);
        }
    }

    /**
     * Test that caching prevents additional DB reads.
     */
    public function test_get_stepdata_single_fetch(): void {
        global $DB;

        $this->resetAfterTest();

        $tour = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour->get_id()]);

        // Only one read for the first call.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // No subsequent reads for any further calls.
        $matches = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);
    }

    /**
     * Test that notify_step_change clears the cache.
     */
    public function test_notify_step_change(): void {
        global $DB;

        $this->resetAfterTest();

        $tour = $this->helper_create_tour();
        $this->helper_create_step((object) ['tourid' => $tour->get_id()]);

        // Only one read for the first call.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // No subsequent reads for any further calls.
        $matches = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        // Reset.
        \tool_usertours\cache::notify_step_change($tour->get_id());

        // An additional DB read now.
        $startreads = $DB->perf_get_reads();
        $matches = \tool_usertours\cache::get_stepdata($tour->get_id());
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);
    }
}
