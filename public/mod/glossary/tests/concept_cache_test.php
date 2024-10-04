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

namespace mod_glossary;

/**
 * Concept fetching and caching tests.
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class concept_cache_test extends \advanced_testcase {
    /**
     * Test convect fetching.
     */
    public function test_concept_fetching(): void {
        global $CFG, $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $CFG->glossary_linkbydefault = 1;
        $CFG->glossary_linkentries = 0;

        // Create a test courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $site = $DB->get_record('course', array('id' => SITEID));

        // Create a glossary.
        $glossary1a = $this->getDataGenerator()->create_module('glossary',
            array('course' => $course1->id, 'mainglossary' => 1, 'usedynalink' => 1));
        $glossary1b = $this->getDataGenerator()->create_module('glossary',
            array('course' => $course1->id, 'mainglossary' => 1, 'usedynalink' => 1));
        $glossary1c = $this->getDataGenerator()->create_module('glossary',
            array('course' => $course1->id, 'mainglossary' => 1, 'usedynalink' => 0));
        $glossary2 = $this->getDataGenerator()->create_module('glossary',
            array('course' => $course2->id, 'mainglossary' => 1, 'usedynalink' => 1));
        $glossary3 = $this->getDataGenerator()->create_module('glossary',
            array('course' => $site->id, 'mainglossary' => 1, 'usedynalink' => 1, 'globalglossary' => 1));

        /** @var mod_glossary_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry1a1 = $generator->create_content($glossary1a, array('concept' => 'first', 'usedynalink' => 1), array('prvni', 'erste'));
        $entry1a2 = $generator->create_content($glossary1a, array('concept' => 'A&B', 'usedynalink' => 1));
        $entry1a3 = $generator->create_content($glossary1a, array('concept' => 'neee', 'usedynalink' => 0));
        $entry1b1 = $generator->create_content($glossary1b, array('concept' => 'second', 'usedynalink' => 1));
        $entry1c1 = $generator->create_content($glossary1c, array('concept' => 'third', 'usedynalink' => 1));
        $entry31 = $generator->create_content($glossary3, array('concept' => 'global', 'usedynalink' => 1), array('globalni'));

        $cat1 = $generator->create_category($glossary1a, array('name' => 'special'), array($entry1a1, $entry1a2));

        \mod_glossary\local\concept_cache::reset_caches();

        $concepts1 = \mod_glossary\local\concept_cache::get_concepts($course1->id);
        $this->assertCount(3, $concepts1[0]);
        $this->assertArrayHasKey($glossary1a->id, $concepts1[0]);
        $this->assertArrayHasKey($glossary1b->id, $concepts1[0]);
        $this->assertArrayHasKey($glossary3->id, $concepts1[0]);
        $this->assertCount(3, $concepts1[1]);
        $this->assertArrayHasKey($glossary1a->id, $concepts1[1]);
        $this->assertArrayHasKey($glossary1b->id, $concepts1[1]);
        $this->assertArrayHasKey($glossary3->id, $concepts1[1]);
        $this->assertCount(5, $concepts1[1][$glossary1a->id]);
        foreach($concepts1[1][$glossary1a->id] as $concept) {
            $this->assertSame(array('id', 'glossaryid', 'concept', 'casesensitive', 'category', 'fullmatch'), array_keys((array)$concept));
            if ($concept->concept === 'first') {
                $this->assertEquals($entry1a1->id, $concept->id);
                $this->assertEquals($glossary1a->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'prvni') {
                $this->assertEquals($entry1a1->id, $concept->id);
                $this->assertEquals($glossary1a->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'erste') {
                $this->assertEquals($entry1a1->id, $concept->id);
                $this->assertEquals($glossary1a->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'A&amp;B') {
                $this->assertEquals($entry1a2->id, $concept->id);
                $this->assertEquals($glossary1a->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'special') {
                $this->assertEquals($cat1->id, $concept->id);
                $this->assertEquals($glossary1a->id, $concept->glossaryid);
                $this->assertEquals(1, $concept->category);
            } else {
                $this->fail('Unexpected concept: ' . $concept->concept);
            }
        }
        $this->assertCount(1, $concepts1[1][$glossary1b->id]);
        foreach($concepts1[1][$glossary1b->id] as $concept) {
            $this->assertSame(array('id', 'glossaryid', 'concept', 'casesensitive', 'category', 'fullmatch'), array_keys((array)$concept));
            if ($concept->concept === 'second') {
                $this->assertEquals($entry1b1->id, $concept->id);
                $this->assertEquals($glossary1b->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else {
                $this->fail('Unexpected concept: ' . $concept->concept);
            }
        }
        $this->assertCount(2, $concepts1[1][$glossary3->id]);
        foreach($concepts1[1][$glossary3->id] as $concept) {
            $this->assertSame(array('id', 'glossaryid', 'concept', 'casesensitive', 'category', 'fullmatch'), array_keys((array)$concept));
            if ($concept->concept === 'global') {
                $this->assertEquals($entry31->id, $concept->id);
                $this->assertEquals($glossary3->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'globalni') {
                $this->assertEquals($entry31->id, $concept->id);
                $this->assertEquals($glossary3->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else {
                $this->fail('Unexpected concept: ' . $concept->concept);
            }
        }

        $concepts3 = \mod_glossary\local\concept_cache::get_concepts($site->id);
        $this->assertCount(1, $concepts3[0]);
        $this->assertArrayHasKey($glossary3->id, $concepts3[0]);
        $this->assertCount(1, $concepts3[1]);
        $this->assertArrayHasKey($glossary3->id, $concepts3[1]);
        foreach($concepts3[1][$glossary3->id] as $concept) {
            $this->assertSame(array('id', 'glossaryid', 'concept', 'casesensitive', 'category', 'fullmatch'), array_keys((array)$concept));
            if ($concept->concept === 'global') {
                $this->assertEquals($entry31->id, $concept->id);
                $this->assertEquals($glossary3->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else if ($concept->concept === 'globalni') {
                $this->assertEquals($entry31->id, $concept->id);
                $this->assertEquals($glossary3->id, $concept->glossaryid);
                $this->assertEquals(0, $concept->category);
            } else {
                $this->fail('Unexpected concept: ' . $concept->concept);
            }
        }

        $concepts2 = \mod_glossary\local\concept_cache::get_concepts($course2->id);
        $this->assertEquals($concepts3, $concepts2);

        // Test uservisible flag.
        set_config('enableavailability', 1);
        $glossary1d = $this->getDataGenerator()->create_module('glossary',
                array('course' => $course1->id, 'mainglossary' => 1, 'usedynalink' => 1,
                'availability' => json_encode(\core_availability\tree::get_root_json(
                        array(\availability_group\condition::get_json())))));
        $entry1d1 = $generator->create_content($glossary1d, array('concept' => 'membersonly', 'usedynalink' => 1));
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        \mod_glossary\local\concept_cache::reset_caches();
        $concepts1 = \mod_glossary\local\concept_cache::get_concepts($course1->id);
        $this->assertCount(4, $concepts1[0]);
        $this->assertCount(4, $concepts1[1]);
        $this->setUser($user);
        \course_modinfo::clear_instance_cache();
        \mod_glossary\local\concept_cache::reset_caches();
        $concepts1 = \mod_glossary\local\concept_cache::get_concepts($course1->id);
        $this->assertCount(3, $concepts1[0]);
        $this->assertCount(3, $concepts1[1]);
    }
}
