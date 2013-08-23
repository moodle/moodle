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

defined('MOODLE_INTERNAL') || die();

/**
 * Automated unit testing. This tests the 'make large course' backend,
 * using the 'XS' option so that it completes quickly.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_generator_maketestcourse_testcase extends advanced_testcase {
    /**
     * Creates a small test course and checks all the components have been put in place.
     */
    public function test_make_xs_course() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the XS course.
        $backend = new tool_generator_backend('TOOL_MAKELARGECOURSE_XS', 0, false, false);
        $courseid = $backend->make();

        // Get course details.
        $course = get_course($courseid);
        $context = context_course::instance($courseid);
        $modinfo = get_fast_modinfo($course);

        // Check sections (just section 0 plus one other).
        $this->assertEquals(2, count($modinfo->get_section_info_all()));

        // Check user is enrolled.
        $users = get_enrolled_users($context);
        $this->assertEquals(1, count($users));
        $this->assertEquals('tool_generator_000001', reset($users)->username);

        // Check there's a page on the course.
        $pages = $modinfo->get_instances_of('page');
        $this->assertEquals(1, count($pages));

        // Check there are small files.
        $resources = $modinfo->get_instances_of('resource');
        $ok = false;
        foreach ($resources as $resource) {
            if ($resource->sectionnum == 0) {
                // The one in section 0 is the 'small files' resource.
                $ok = true;
                break;
            }
        }
        $this->assertTrue($ok);

        // Check it contains 2 files (the default txt and a dat file).
        $fs = get_file_storage();
        $resourcecontext = context_module::instance($resource->id);
        $files = $fs->get_area_files($resourcecontext->id, 'mod_resource', 'content', false, 'filename', false);
        $files = array_values($files);
        $this->assertEquals(2, count($files));
        $this->assertEquals('resource1.txt', $files[0]->get_filename());
        $this->assertEquals('smallfile0.dat', $files[1]->get_filename());

        // Check there's a single 'big' file (it's actually only 8KB).
        $ok = false;
        foreach ($resources as $resource) {
            if ($resource->sectionnum == 1) {
                $ok = true;
                break;
            }
        }
        $this->assertTrue($ok);

        // Check it contains 2 files.
        $resourcecontext = context_module::instance($resource->id);
        $files = $fs->get_area_files($resourcecontext->id, 'mod_resource', 'content', false, 'filename', false);
        $files = array_values($files);
        $this->assertEquals(2, count($files));
        $this->assertEquals('bigfile0.dat', $files[0]->get_filename());
        $this->assertEquals('resource2.txt', $files[1]->get_filename());

        // Get forum and count the number of posts on it.
        $forums = $modinfo->get_instances_of('forum');
        $forum = reset($forums);
        $posts = $DB->count_records_sql("
                SELECT
                    COUNT(1)
                FROM
                    {forum_posts} fp
                    JOIN {forum_discussions} fd ON fd.id = fp.discussion
                WHERE
                    fd.forum = ?", array($forum->instance));
        $this->assertEquals(2, $posts);
    }
}
