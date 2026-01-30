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

namespace mod_subsection;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Tests for course_integrity_check() function to prevent nested subsections.
 *
 * This test class verifies that the course_integrity_check() function correctly
 * prevents nested subsections (subsection modules inside subsection sections)
 * and ensures the course has a valid data structure after the check.
 *
 * @package    mod_subsection
 * @author     Andreas Wagner (mebis-lp)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::course_integrity_check
 */
final class course_integrity_check_test extends \advanced_testcase {

    /**
     * Set up test environment.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Get nested subsection modules using section reference.
     *
     * This finds subsection modules where cm.section references a subsection-section.
     *
     * @param int $courseid Optional course ID to filter by
     * @return array Array of nested subsection records
     * @throws \dml_exception
     */
    public function get_nested_subsections_by_section(int $courseid = 0): array {
        global $DB;

        $sql = "SELECT cm.id AS cmid, cm.course, cm.instance, cm.section,
                       parentsec.id AS parentsecid
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = 'subsection'
                  JOIN {subsection} sub ON cm.instance = sub.id
                  JOIN {course} c ON c.id = cm.course
                  JOIN {course_sections} parentsec ON parentsec.course = c.id
                       AND parentsec.component = 'mod_subsection'
                 WHERE cm.section = parentsec.id";

        $params = [];
        if ($courseid > 0) {
            $sql .= " AND cm.course = :courseid";
            $params['courseid'] = $courseid;
        }

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get nested subsection modules using sequence (Fall 2).
     *
     * This finds subsection modules where the cmid is in the sequence of a subsection-section.
     * This is slower but catches cases where cm.section might be inconsistent.
     *
     * @param int $courseid Optional course ID to filter by
     * @return array Array of nested subsection records
     * @throws \dml_exception
     */
    public function get_nested_subsections_by_sequence(int $courseid = 0): array {
        global $DB;

        $sql = "SELECT cm.id AS cmid, cm.course, cm.instance, cm.section,
                       parentsec.id AS parentsecid
                  FROM {course_modules} cm
                  JOIN {modules} m ON cm.module = m.id AND m.name = 'subsection'
                  JOIN {subsection} sub ON cm.instance = sub.id
                  JOIN {course} c ON c.id = cm.course
                  JOIN {course_sections} parentsec ON parentsec.course = c.id
                       AND parentsec.component = 'mod_subsection'
                 WHERE CONCAT(',', parentsec.sequence, ',') LIKE CONCAT('%,',cm.id,',%')";

        $params = [];
        if ($courseid > 0) {
            $sql .= " AND cm.course = :courseid";
            $params['courseid'] = $courseid;
        }
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Helper method to verify that no nested subsections exist in the course.
     *
     * A nested subsection occurs when a subsection module (course_modules entry
     * with module type 'subsection') is contained in a subsection section
     * (course_sections entry with component = 'mod_subsection').
     *
     * @param int $courseid The course ID to check.
     * @return void
     */
    private function assert_no_nested_subsections(int $courseid): void {

        $nestedsubsections = $this->get_nested_subsections_by_section($courseid);
        $this->assertEmpty(
            $nestedsubsections,
            'Found nested subsections: subsection module(s) inside subsection section(s)'
        );

        $nestedsubsections = $this->get_nested_subsections_by_sequence($courseid);
        $this->assertEmpty(
            $nestedsubsections,
            'Found nested subsections: subsection module(s) inside subsection section(s)'
        );
    }

    /**
     * Helper method to verify that the course data structure is consistent.
     *
     * Checks:
     * 1. All modules in sequences exist in course_modules.
     * 2. All course_modules.section values point to existing sections.
     * 3. All modules are in their section's sequence.
     *
     * @param int $courseid The course ID to check.
     * @return void
     */
    private function assert_course_structure_valid(int $courseid): void {
        global $DB;

        $sections = $DB->get_records('course_sections', ['course' => $courseid]);
        $modules = $DB->get_records('course_modules', ['course' => $courseid], '', 'id, section');

        foreach ($sections as $section) {
            if (empty($section->sequence)) {
                continue;
            }

            $cmids = explode(',', $section->sequence);
            foreach ($cmids as $cmid) {
                // Check that each module in sequence exists.
                $this->assertArrayHasKey(
                    (int) $cmid,
                    $modules,
                    "Module {$cmid} in sequence of section {$section->id} does not exist"
                );

                // Check that module points back to this section.
                $this->assertEquals(
                    $section->id,
                    $modules[(int) $cmid]->section,
                    "Module {$cmid} points to section {$modules[(int)$cmid]->section} but is in sequence of section {$section->id}"
                );
            }
        }

        // Check that all modules are in some sequence.
        foreach ($modules as $module) {
            $this->assertArrayHasKey(
                $module->section,
                $sections,
                "Module {$module->id} points to non-existent section {$module->section}"
            );

            $sequence = $sections[$module->section]->sequence ?? '';
            $cmids = !empty($sequence) ? explode(',', $sequence) : [];
            $this->assertContains(
                (string) $module->id,
                $cmids,
                "Module {$module->id} is not in sequence of its section {$module->section}"
            );
        }
    }

    /**
     * Test scenario 1: Subsection module appears in two sequences (normal section + subsection section).
     *
     * This test verifies that when a subsection module is incorrectly placed in both
     * a normal parent section and a subsection section (duplicate in sequences),
     * course_integrity_check() removes it from the subsection section and keeps it
     * in the parent section.
     */
    public function test_prevents_nested_subsection_duplicate_in_sequences(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create a subsection module in section 1.
        $subsection = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Create a subsection module in section 1.
        $subsection2 = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get the subsection's delegated section (component = 'mod_subsection').
        $subsectionsection2 = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsection2->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Verify initial state is correct.
        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $parentsection->sequence . ',');
        $this->assertEmpty($subsectionsection2->sequence);

        // Corrupt the data: Add subsection module to subsection2 section's sequence.
        $subsectionsection2->sequence = (string) $subsection->cmid;
        $DB->update_record('course_sections', $subsectionsection2);

        // Run integrity check.
        $messages = course_integrity_check($course->id);

        // Verify that messages indicate the fix was applied.
        $this->assertNotEmpty($messages);
        $this->assertTrue(
            (bool) preg_grep('/must be removed from sequence of subsection section/', $messages),
            'Expected message about removing subsection module from subsection section. Got: ' . implode(', ', $messages)
        );

        // Reload sections from DB.
        $parentsection = $DB->get_record('course_sections', ['id' => $parentsection->id]);
        $subsectionsection2 = $DB->get_record('course_sections', ['id' => $subsectionsection2->id]);
        $cm = $DB->get_record('course_modules', ['id' => $subsection->cmid]);

        // Verify: Subsection module should remain in parent section, not in subsection section.
        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $parentsection->sequence . ',');
        $this->assertStringNotContainsString(',' . $subsection->cmid, ',' . $subsectionsection2->sequence . ',');
        $this->assertEquals($parentsection->id, $cm->section);

        // Verify no nested subsections and valid structure.
        $this->assert_no_nested_subsections($course->id);
        $this->assert_course_structure_valid($course->id);
    }

    /**
     * Test scenario 2: Orphaned subsection module where $mod->section points to existing subsection section.
     *
     * This test verifies that when an orphaned subsection module (not in any sequence)
     * has its course_modules.section pointing to a subsection section,
     * course_integrity_check() places it in a non-delegated section instead.
     */
    public function test_prevents_nested_subsection_orphan_pointing_to_subsection(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create two subsection modules in section 1.
        $subsectiona = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );
        $subsectionb = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get subsection A's delegated section.
        $subsectionsectiona = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsectiona->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Corrupt the data:
        // 1. Remove subsection B from parent section's sequence (make it orphaned).
        $parentsection->sequence = (string) $subsectiona->cmid;
        $DB->update_record('course_sections', $parentsection);

        // 2. Point subsection B's course_modules.section to subsection A's section (would create nesting).
        $DB->update_record('course_modules', (object) [
            'id' => $subsectionb->cmid,
            'section' => $subsectionsectiona->id,
        ]);

        // Run integrity check with fullcheck=true (required for orphan detection).
        $messages = course_integrity_check($course->id, null, null, true);

        // Verify that messages were generated.
        $this->assertNotEmpty($messages);

        // Reload data from DB.
        $cm = $DB->get_record('course_modules', ['id' => $subsectionb->cmid]);
        $subsectionsectiona = $DB->get_record('course_sections', ['id' => $subsectionsectiona->id]);

        // Verify: Orphaned subsection module should NOT be in subsection section.
        $this->assertStringNotContainsString(',' . $subsectionb->cmid . ',', ',' . $subsectionsectiona->sequence . ',');

        // Verify: It should be placed in a non-delegated section.
        $nondelegatedsection = $DB->get_record('course_sections', ['id' => $cm->section]);
        $this->assertNull($nondelegatedsection->component);

        // Verify no nested subsections and valid structure.
        $this->assert_no_nested_subsections($course->id);
        $this->assert_course_structure_valid($course->id);
    }

    /**
     * Test scenario 3: Orphaned subsection module where $mod->section points to non-existent section.
     *
     * This test verifies that when an orphaned subsection module has its
     * course_modules.section pointing to a non-existent section,
     * course_integrity_check() places it in a non-delegated section.
     */
    public function test_prevents_nested_subsection_orphan_invalid_section(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create a subsection module in section 1.
        $subsection = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Corrupt the data:
        // 1. Remove subsection from parent section's sequence (make it orphaned).
        $parentsection->sequence = '';
        $DB->update_record('course_sections', $parentsection);

        // 2. Point course_modules.section to a non-existent section ID.
        $DB->update_record('course_modules', (object) [
            'id' => $subsection->cmid,
            'section' => 99999,
        ]);

        // Run integrity check with fullcheck=true.
        $messages = course_integrity_check($course->id, null, null, true);

        // Verify that messages were generated.
        $this->assertNotEmpty($messages);
        $this->assertTrue(
            (bool) preg_grep('/is missing from sequence of section/', $messages),
            'Expected message about missing module from sequence. Got: ' . implode(', ', $messages)
        );

        // Reload data from DB.
        $cm = $DB->get_record('course_modules', ['id' => $subsection->cmid]);

        // Verify: Module should be placed in a non-delegated section.
        $targetsection = $DB->get_record('course_sections', ['id' => $cm->section]);
        $this->assertNull($targetsection->component);

        // Verify: Module should be in the target section's sequence.
        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $targetsection->sequence . ',');

        // Verify no nested subsections and valid structure.
        $this->assert_no_nested_subsections($course->id);
        $this->assert_course_structure_valid($course->id);
    }

    /**
     * Test scenario 4: Subsection section has lowest section number and orphaned subsection module exists.
     *
     * This test verifies that when a subsection section has the lowest section number
     * (e.g., section=-100 or section=0), orphaned subsection modules are NOT placed
     * into this subsection section, but into a non-delegated section instead.
     *
     * This is critical because course_integrity_check() uses reset($nondelegatedsections)
     * to find a fallback section, and if the sections are sorted incorrectly or a
     * subsection section has a very low section number, it could be selected erroneously.
     */
    public function test_prevents_nested_subsection_when_subsection_has_lowest_section_number(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create two subsection modules in section 1.
        $subsectiona = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );
        $subsectionb = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get subsection A's delegated section.
        $subsectionsectiona = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsectiona->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Get section 0.
        $section0 = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 0,
        ]);

        // Corrupt the data:
        // 1. Set subsection A's section number to -100 (lowest possible, will be first in sorted order).
        $subsectionsectiona->section = -100;
        $DB->update_record('course_sections', $subsectionsectiona);

        // 2. Remove subsection B from parent section's sequence (make it orphaned).
        $parentsection->sequence = (string) $subsectiona->cmid;
        $DB->update_record('course_sections', $parentsection);

        // 3. Point subsection B's course_modules.section to a non-existent section (orphaned).
        $DB->update_record('course_modules', (object) [
            'id' => $subsectionb->cmid,
            'section' => 99999,
        ]);

        // Run integrity check with fullcheck=true (required for orphan detection).
        $messages = course_integrity_check($course->id, null, null, true);

        // Verify that messages were generated.
        $this->assertNotEmpty($messages);

        // Reload data from DB.
        $cm = $DB->get_record('course_modules', ['id' => $subsectionb->cmid]);
        $subsectionsectiona = $DB->get_record('course_sections', ['id' => $subsectionsectiona->id]);

        // Verify: Orphaned subsection module should NOT be in subsection section (even with section=-100).
        $this->assertStringNotContainsString(
            ',' . $subsectionb->cmid . ',',
            ',' . $subsectionsectiona->sequence . ',',
            'Subsection module was incorrectly placed in subsection section with lowest section number'
        );

        // Verify: It should NOT point to the subsection section.
        $this->assertNotEquals(
            $subsectionsectiona->id,
            $cm->section,
            'Subsection module section reference points to subsection section'
        );

        // Verify: It should be placed in a non-delegated section.
        $nondelegatedsection = $DB->get_record('course_sections', ['id' => $cm->section]);
        $this->assertNull(
            $nondelegatedsection->component,
            'Orphaned subsection module was not placed in a non-delegated section'
        );

        // Verify no nested subsections and valid structure.
        $this->assert_no_nested_subsections($course->id);
        $this->assert_course_structure_valid($course->id);
    }

    /**
     * This is a copy of unmodified course_integrity_check() to test that it will create
     * nested subsections in certain scenarios (Should be removed once the bug is fixed).
     *
     * @param $courseid
     * @param $rawmods
     * @param $sections
     * @param $fullcheck
     * @param $checkonly
     * @return array|true
     * @throws \dml_exception
     */
    protected function course_integrity_check($courseid, $rawmods = null, $sections = null, $fullcheck = false,
        $checkonly = false) {
        global $DB;
        $messages = array();
        if ($sections === null) {
            $sections = $DB->get_records('course_sections', array('course' => $courseid), 'section', 'id,section,sequence');
        }
        if ($fullcheck) {
            // Retrieve all records from course_modules regardless of module type visibility.
            $rawmods = $DB->get_records('course_modules', array('course' => $courseid), 'id', 'id,section');
        }
        if ($rawmods === null) {
            $rawmods = get_course_mods($courseid);
        }
        if (!$fullcheck && (empty($sections) || empty($rawmods))) {
            // If either of the arrays is empty, no modules are displayed anyway.
            return true;
        }
        $debuggingprefix = 'Failed integrity check for course [' . $courseid . ']. ';

        // First make sure that each module id appears in section sequences only once.
        // If it appears in several section sequences the last section wins.
        // If it appears twice in one section sequence, the first occurence wins.
        $modsection = array();
        foreach ($sections as $sectionid => $section) {
            $sections[$sectionid]->newsequence = $section->sequence;
            if (!empty($section->sequence)) {
                $sequence = explode(",", $section->sequence);
                $sequenceunique = array_unique($sequence);
                if (count($sequenceunique) != count($sequence)) {
                    // Some course module id appears in this section sequence more than once.
                    ksort($sequenceunique); // Preserve initial order of modules.
                    $sequence = array_values($sequenceunique);
                    $sections[$sectionid]->newsequence = join(',', $sequence);
                    $messages[] = $debuggingprefix . 'Sequence for course section [' .
                        $sectionid . '] is "' . $sections[$sectionid]->sequence . '", must be "' .
                        $sections[$sectionid]->newsequence . '"';
                }
                foreach ($sequence as $cmid) {
                    if (array_key_exists($cmid, $modsection) && isset($rawmods[$cmid])) {
                        // Some course module id appears to be in more than one section's sequences.
                        $wrongsectionid = $modsection[$cmid];
                        $sections[$wrongsectionid]->newsequence =
                            trim(preg_replace("/,$cmid,/", ',', ',' . $sections[$wrongsectionid]->newsequence . ','), ',');
                        $messages[] =
                            $debuggingprefix . 'Course module [' . $cmid . '] must be removed from sequence of section [' .
                            $wrongsectionid . '] because it is also present in sequence of section [' . $sectionid . ']';
                    }
                    $modsection[$cmid] = $sectionid;
                }
            }
        }

        // Add orphaned modules to their sections if they exist or to section 0 otherwise.
        if ($fullcheck) {
            foreach ($rawmods as $cmid => $mod) {
                if (!isset($modsection[$cmid])) {
                    // This is a module that is not mentioned in course_section.sequence at all.
                    // Add it to the section $mod->section or to the last available section.
                    if ($mod->section && isset($sections[$mod->section])) {
                        $modsection[$cmid] = $mod->section;
                    } else {
                        $firstsection = reset($sections);
                        $modsection[$cmid] = $firstsection->id;
                    }
                    $sections[$modsection[$cmid]]->newsequence =
                        trim($sections[$modsection[$cmid]]->newsequence . ',' . $cmid, ',');
                    $messages[] = $debuggingprefix . 'Course module [' . $cmid . '] is missing from sequence of section [' .
                        $modsection[$cmid] . ']';
                }
            }
            foreach ($modsection as $cmid => $sectionid) {
                if (!isset($rawmods[$cmid])) {
                    // Section $sectionid refers to module id that does not exist.
                    $sections[$sectionid]->newsequence =
                        trim(preg_replace("/,$cmid,/", ',', ',' . $sections[$sectionid]->newsequence . ','), ',');
                    $messages[] = $debuggingprefix . 'Course module [' . $cmid .
                        '] does not exist but is present in the sequence of section [' . $sectionid . ']';
                }
            }
        }

        // Update changed sections.
        if (!$checkonly && !empty($messages)) {
            foreach ($sections as $sectionid => $section) {
                if ($section->newsequence !== $section->sequence) {
                    $DB->update_record('course_sections', array('id' => $sectionid, 'sequence' => $section->newsequence));
                }
            }
        }

        // Now make sure that all modules point to the correct sections.
        foreach ($rawmods as $cmid => $mod) {
            if (isset($modsection[$cmid]) && $modsection[$cmid] != $mod->section) {
                if (!$checkonly) {
                    $DB->update_record('course_modules', array('id' => $cmid, 'section' => $modsection[$cmid]));
                }
                $messages[] = $debuggingprefix . 'Course module [' . $cmid .
                    '] points to section [' . $mod->section . '] instead of [' . $modsection[$cmid] . ']';
            }
        }

        return $messages;
    }

    /**
     * Test scenario 5: Confirms that nested subsections WOULD occur with original implementation.
     *
     * This test demonstrates the problem that exists when:
     * - A subsection section has the lowest section number (e.g., section=-100)
     * - A subsection module is orphaned (not in any sequence, points to non-existent section)
     * - The original course_integrity_check() uses reset($sections) which returns
     *   the first section in the sorted array (the one with lowest section number)
     *
     * Without the fix, the orphaned subsection module would be placed into the
     * subsection section, creating an invalid nested subsection structure.
     *
     * This test uses the ORIGINAL algorithm behavior to confirm the issue.
     */
    public function test_confirms_nested_subsection_would_occur_with_original_implementation(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create two subsection modules in section 1.
        $subsectiona = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );
        $subsectionb = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get subsection A's delegated section.
        $subsectionsectiona = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsectiona->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Corrupt the data:
        // 1. Set subsection A's section number to -100 (lowest possible).
        $subsectionsectiona->section = -100;
        $DB->update_record('course_sections', $subsectionsectiona);

        // 2. Remove subsection B from parent section's sequence (make it orphaned).
        $parentsection->sequence = (string) $subsectiona->cmid;
        $DB->update_record('course_sections', $parentsection);

        // 3. Point subsection B's course_modules.section to a non-existent section.
        $DB->update_record('course_modules', (object) [
            'id' => $subsectionb->cmid,
            'section' => 99999,
        ]);

        // Simulate the ORIGINAL algorithm behavior:
        $this->course_integrity_check($course->id, null, null, true);

        $nested = $this->get_nested_subsections_by_section($course->id);
        $this->assertCount(1, $nested);
        $this->assertEquals($subsectionb->cmid, array_shift($nested)->cmid);

        $nested = $this->get_nested_subsections_by_sequence($course->id);
        $this->assertCount(1, $nested);
        $this->assertEquals($subsectionb->cmid, array_shift($nested)->cmid);
    }

    /**
     * Test scenario 6: Confirms that nested subsections WOULD NOT occur with new implementation.
     *
     * This test demonstrates the problem that exists when:
     * - A subsection section has the lowest section number (e.g., section=-100)
     * - A subsection module is orphaned (not in any sequence, points to non-existent section)
     * - The original course_integrity_check() uses reset($sections) which returns
     *   the first section in the sorted array (the one with lowest section number)
     *
     * Without the fix, the orphaned subsection module would be placed into the
     * subsection section, creating an invalid nested subsection structure.
     */
    public function test_confirms_nested_subsection_would_not_occur_with_new_implementation(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create two subsection modules in section 1.
        $subsectiona = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );
        $subsectionb = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get subsection A's delegated section.
        $subsectionsectiona = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsectiona->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Corrupt the data:
        // 1. Set subsection A's section number to -100 (lowest possible).
        $subsectionsectiona->section = -100;
        $DB->update_record('course_sections', $subsectionsectiona);

        // 2. Remove subsection B from parent section's sequence (make it orphaned).
        $parentsection->sequence = (string) $subsectiona->cmid;
        $DB->update_record('course_sections', $parentsection);

        // 3. Point subsection B's course_modules.section to a non-existent section.
        $DB->update_record('course_modules', (object) [
            'id' => $subsectionb->cmid,
            'section' => 99999,
        ]);

        // Simulate the ORIGINAL algorithm behavior:
        course_integrity_check($course->id, null, null, true);

        $nested = $this->get_nested_subsections_by_section($course->id);
        $this->assertCount(0, $nested);

        $nested = $this->get_nested_subsections_by_sequence($course->id);
        $this->assertCount(0, $nested);
    }

    /**
     * Test scenario 7: Confirms that nested subsections WOULD occur with original implementation
     * when subsection module appears in both normal section and subsection section sequences
     * (Last Section Wins behavior).
     *
     * This test demonstrates the problem that exists when:
     * - A subsection module is in the sequence of a normal parent section
     * - The same subsection module is also in the sequence of a subsection section
     * - The subsection section has a higher section number (processed later)
     * - The original course_integrity_check() uses "last section wins" logic
     *
     * Without the fix, the subsection module would be moved to the subsection section,
     * creating an invalid nested subsection structure.
     */
    public function test_confirms_nested_subsection_would_occur_with_original_last_section_wins(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create a subsection module in section 1.
        $subsection = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get the subsection's delegated section (component = 'mod_subsection').
        $subsectionsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsection->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Verify initial state is correct.
        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $parentsection->sequence . ',');
        $this->assertEmpty($subsectionsection->sequence);

        // Corrupt the data: Add subsection module to its own subsection section's sequence (duplicate).
        // The subsection section has a higher section number, so it will be processed LATER
        // and "win" in the "last section wins" logic.
        $this->assertGreaterThan(
            $parentsection->section,
            $subsectionsection->section,
            'Subsection section should have higher section number than parent section'
        );

        $subsectionsection->sequence = (string) $subsection->cmid;
        $DB->update_record('course_sections', $subsectionsection);

        // Run the ORIGINAL integrity check (without the fix).
        $this->course_integrity_check($course->id);

        // Verify that a nested subsection was created (the bug).
        $nested = $this->get_nested_subsections_by_section($course->id);
        $this->assertCount(
            1,
            $nested,
            'Original implementation should create nested subsection due to "last section wins" behavior'
        );
        $this->assertEquals($subsection->cmid, array_shift($nested)->cmid);

        $nested = $this->get_nested_subsections_by_sequence($course->id);
        $this->assertCount(
            1,
            $nested,
            'Original implementation should create nested subsection in sequence'
        );
        $this->assertEquals($subsection->cmid, array_shift($nested)->cmid);
    }

    /**
     * Test scenario 8: Confirms that nested subsections WOULD NOT occur with new implementation
     * when subsection module appears in both normal section and subsection section sequences
     * (Last Section Wins behavior is overridden for subsection modules).
     *
     * This test demonstrates that the fix prevents nested subsections when:
     * - A subsection module is in the sequence of a normal parent section
     * - The same subsection module is also in the sequence of a subsection section
     * - The subsection section has a higher section number (would be processed later)
     * - The new course_integrity_check() detects this and removes it from subsection section
     */
    public function test_confirms_nested_subsection_would_not_occur_with_new_last_section_wins(): void {
        global $DB;

        // Create course with sections.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        // Create a subsection module in section 1.
        $subsection = $this->getDataGenerator()->create_module(
            'subsection',
            (object) ['course' => $course->id, 'section' => 1]
        );

        // Get the subsection's delegated section (component = 'mod_subsection').
        $subsectionsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'component' => 'mod_subsection',
            'itemid' => $subsection->id,
        ]);

        // Get the parent section (section 1).
        $parentsection = $DB->get_record('course_sections', [
            'course' => $course->id,
            'section' => 1,
        ]);

        // Verify initial state is correct.
        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $parentsection->sequence . ',');
        $this->assertEmpty($subsectionsection->sequence);

        // Corrupt the data: Add subsection module to its own subsection section's sequence (duplicate).
        // The subsection section has a higher section number, so it would be processed LATER.
        $this->assertGreaterThan(
            $parentsection->section,
            $subsectionsection->section,
            'Subsection section should have higher section number than parent section'
        );

        $subsectionsection->sequence = (string) $subsection->cmid;
        $DB->update_record('course_sections', $subsectionsection);

        // Run the NEW integrity check (with the fix).
        $messages = course_integrity_check($course->id);

        // Verify that messages indicate the fix was applied.
        $this->assertNotEmpty($messages);
        $this->assertTrue(
            (bool) preg_grep('/must be removed from sequence of subsection section/', $messages),
            'Expected message about removing subsection module from subsection section. Got: ' . implode(', ', $messages)
        );

        // Verify that NO nested subsection was created.
        $nested = $this->get_nested_subsections_by_section($course->id);
        $this->assertCount(
            0,
            $nested,
            'New implementation should NOT create nested subsection'
        );

        $nested = $this->get_nested_subsections_by_sequence($course->id);
        $this->assertCount(
            0,
            $nested,
            'New implementation should NOT create nested subsection in sequence'
        );

        // Verify the subsection module is in the parent section.
        $parentsection = $DB->get_record('course_sections', ['id' => $parentsection->id]);
        $subsectionsection = $DB->get_record('course_sections', ['id' => $subsectionsection->id]);
        $cm = $DB->get_record('course_modules', ['id' => $subsection->cmid]);

        $this->assertStringContainsString(',' . $subsection->cmid . ',', ',' . $parentsection->sequence . ',');
        $this->assertStringNotContainsString(',' . $subsection->cmid . ',', ',' . $subsectionsection->sequence . ',');
        $this->assertEquals($parentsection->id, $cm->section);

        // Verify course structure is valid.
        $this->assert_no_nested_subsections($course->id);
        $this->assert_course_structure_valid($course->id);
    }
}
