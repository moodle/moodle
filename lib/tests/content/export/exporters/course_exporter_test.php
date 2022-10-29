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

namespace core\content\export\exporters;

use advanced_testcase;
use context_course;
use context_module;
use ZipArchive;
use core\content\export\zipwriter;

/**
 * Unit tests for activity exporter.
 *
 * @package     core
 * @category    test
 * @copyright   2020 Simey Lameze <simey@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers      \core\content\export\exporters\course_exporter
 */
class course_exporter_test extends advanced_testcase {

    /**
     * The course_exporter should still export a module intro when no exportables are passed.
     */
    public function test_no_exportables_exported(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = context_course::instance($course->id);

        $intro = 'XX Some introduction should go here XX';
        $content = 'YY Some content should go here YY';
        $module = $generator->create_module('page', [
            'course' => $course->id,
            'intro' => $intro,
            'content' => $content,
        ]);
        $modcontext = context_module::instance($module->cmid);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        // Only the module index should be added.
        $archive = $this->get_mocked_zipwriter(['add_file_from_string']);
        $archive->expects($this->once())
            ->method('add_file_from_string')
            ->with(
                $modcontext,
                'index.html',
                $this->callback(function($html) use ($intro, $content): bool {
                    if (strpos($html, $intro) === false) {
                        return false;
                    }

                    if (strpos($html, $content) !== false) {
                        // The content as not exported.
                        return false;
                    }

                    return true;
                })
            );
        $archive->set_root_context($coursecontext);

        $coursecontroller = new course_exporter($modcontext->get_course_context(), $user, $archive);
        $coursecontroller->export_mod_content($modcontext, []);
    }

    /**
     * The course_exporter should still export exportables as well as module intro.
     */
    public function test_exportables_exported(): void {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = context_course::instance($course->id);

        $intro = 'XX Some introduction should go here XX';
        $content = 'YY Some content should go here YY';
        $module = $generator->create_module('page', [
            'course' => $course->id,
            'intro' => $intro,
            'content' => $content,
        ]);
        $modcontext = context_module::instance($module->cmid);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        // Only the module index should be added.
        $archive = $this->get_mocked_zipwriter(['add_file_from_string']);
        $archive->expects($this->once())
            ->method('add_file_from_string')
            ->with(
                $modcontext,
                'index.html',
                $this->callback(function($html) use ($intro, $content): bool {
                    if (strpos($html, $intro) === false) {
                        return false;
                    }

                    if (strpos($html, $content) === false) {
                        // Content was exported.
                        return false;
                    }

                    return true;
                })
            );
        $archive->set_root_context($coursecontext);

        $pagecontroller = new \mod_page\content\exporter($modcontext, "mod_page", $user, $archive);

        $coursecontroller = new course_exporter($modcontext->get_course_context(), $user, $archive);
        $coursecontroller->export_mod_content($modcontext, $pagecontroller->get_exportables());
    }

    /**
     * Get a mocked zipwriter instance, stubbing the supplieid classes.
     *
     * @param   string[] $methods
     * @return  zipwriter
     */
    protected function get_mocked_zipwriter(?array $methods = []): zipwriter {
        return $this->getMockBuilder(zipwriter::class)
            ->setConstructorArgs([$this->getMockBuilder(\ZipStream\ZipStream::class)->getmock()])
            ->onlyMethods($methods)
            ->getMock();
    }
}
