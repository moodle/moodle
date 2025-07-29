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

namespace core;

use advanced_testcase;
use cache;
use cm_info;
use coding_exception;
use context_course;
use context_module;
use course_modinfo;
use core_courseformat\formatactions;
use moodle_exception;
use moodle_url;
use Exception;

/**
 * Unit tests for lib/modinfolib.php.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class modinfolib_test extends advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        parent::setUpBeforeClass();
    }
    public function test_matching_cacherev(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course([
            'format' => 'topics',
            'numsections' => 3,
        ], [
            'createsections' => true,
        ]);

        // Make sure the cacherev is set.
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan(0, $cacherev);
        $prevcacherev = $cacherev;

        // Reset course cache and make sure cacherev is bumped up but cache is empty.
        rebuild_course_cache($course->id, true);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get_versioned($course->id, $prevcacherev));
        $prevcacherev = $cacherev;

        // Build course cache. Cacherev should not change but cache is now not empty. Make sure cacherev is the same everywhere.
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Little trick to check that cache is not rebuilt druing the next step.
        // Substitute the value in MUC and later check that it is still there.
        $cache->acquire_lock($course->id);
        $cache->set_versioned($course->id, $cacherev, (object)array_merge((array)$cachedvalue, ['secretfield' => 1]));
        $cache->release_lock($course->id);

        // Clear static cache and call get_fast_modinfo() again (pretend we are in another request). Cache should not be rebuilt.
        course_modinfo::clear_instance_cache();
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertEquals($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertNotEmpty($cachedvalue->secretfield);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Rebuild course cache. Cacherev must be incremented everywhere.
        rebuild_course_cache($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $modinfo = get_fast_modinfo($course->id);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Update cacherev in DB and make sure the cache will be rebuilt on the next call to get_fast_modinfo().
        increment_revision_number('course', 'cacherev', 'id = ?', [$course->id]);
        // We need to clear static cache for course_modinfo instances too.
        course_modinfo::clear_instance_cache();
        $modinfo = get_fast_modinfo($course->id);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Reset cache for all courses and make sure this course cache is reset.
        rebuild_course_cache(0, true);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get_versioned($course->id, $cacherev));
        // Rebuild again.
        $modinfo = get_fast_modinfo($course->id);
        $cachedvalue = $cache->get_versioned($course->id, $cacherev);
        $this->assertNotEmpty($cachedvalue);
        $this->assertEquals($cacherev, $cachedvalue->cacherev);
        $this->assertEquals($cacherev, $modinfo->get_course()->cacherev);
        $prevcacherev = $cacherev;

        // Purge all caches and make sure cacherev is increased and data from MUC erased.
        purge_all_caches();
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($prevcacherev, $cacherev);
        $this->assertEmpty($cache->get($course->id));
    }

    /**
     * The cacherev is updated when we rebuild course cache, but there are scenarios where an
     * existing course object with old cacherev might be reused within the same request after
     * clearing the cache. In that case, we need to check that the new data is loaded and it
     * does not reuse the old cached data with old cacherev.
     *
     * @covers ::rebuild_course_cache()
     */
    public function test_cache_clear_wrong_cacherev(): void {
        global $DB;

        $this->resetAfterTest();
        $originalcourse = $this->getDataGenerator()->create_course();
        $course = $DB->get_record('course', ['id' => $originalcourse->id]);
        $page = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'frog']
        );
        $oldmodinfo = get_fast_modinfo($course);
        $this->assertEquals('frog', $oldmodinfo->get_cm($page->cmid)->name);

        // Change page name and rebuild cache.
        $DB->set_field('page', 'name', 'Frog', ['id' => $page->id]);
        rebuild_course_cache($course->id, true);

        // Get modinfo using original course object which has old cacherev.
        $newmodinfo = get_fast_modinfo($course);
        $this->assertEquals('Frog', $newmodinfo->get_cm($page->cmid)->name);
    }

    /**
     * When cacherev is updated for a course, it is supposed to update in the $COURSE and $SITE
     * globals automatically. Check this is working.
     *
     * @covers ::rebuild_course_cache()
     */
    public function test_cacherev_update_in_globals(): void {
        global $DB, $COURSE, $SITE;

        $this->resetAfterTest();

        // Create a course and get modinfo.
        $originalcourse = $this->getDataGenerator()->create_course();
        $oldmodinfo = get_fast_modinfo($originalcourse->id);

        // Store (two clones of) the course in COURSE and SITE globals.
        $COURSE = get_course($originalcourse->id);
        $SITE = get_course($originalcourse->id);

        // Note original cacherev.
        $originalcacherev = $oldmodinfo->get_course()->cacherev;
        $this->assertEquals($COURSE->cacherev, $originalcacherev);
        $this->assertEquals($SITE->cacherev, $originalcacherev);

        // Clear the cache and check cacherev updated.
        rebuild_course_cache($originalcourse->id, true);

        $newcourse = $DB->get_record('course', ['id' => $originalcourse->id]);
        $this->assertGreaterThan($originalcacherev, $newcourse->cacherev);

        // Check that the in-memory $COURSE and $SITE have updated.
        $this->assertEquals($newcourse->cacherev, $COURSE->cacherev);
        $this->assertEquals($newcourse->cacherev, $SITE->cacherev);
    }

    public function test_is_user_access_restricted_by_capability(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course and a mod_assign instance.
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Create and enrol a student.
        $coursecontext = context_course::instance($course->id);
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $student = $this->getDataGenerator()->create_user();
        role_assign($studentrole->id, $student->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolinstance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        $enrolplugin->enrol_user($enrolinstance, $student->id);
        $this->setUser($student);

        // Make sure student can see the module.
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());

        // Prohibit student to view mod_assign for the course.
        role_change_permission($studentrole->id, $coursecontext, 'mod/assign:view', CAP_PROHIBIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());

        // Restore permission to student to view mod_assign for the course.
        role_change_permission($studentrole->id, $coursecontext, 'mod/assign:view', CAP_INHERIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());

        // Prohibit student to view mod_assign for the particular module.
        role_change_permission($studentrole->id, context_module::instance($cm->id), 'mod/assign:view', CAP_PROHIBIT);
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());

        // Check calling get_fast_modinfo() for different user.
        $this->setAdminUser();
        $cm = get_fast_modinfo($course->id)->instances['assign'][$assign->id];
        $this->assertTrue($cm->uservisible);
        $this->assertFalse($cm->is_user_access_restricted_by_capability());
        $cm = get_fast_modinfo($course->id, $student->id)->instances['assign'][$assign->id];
        $this->assertFalse($cm->uservisible);
        $this->assertTrue($cm->is_user_access_restricted_by_capability());
    }
}
