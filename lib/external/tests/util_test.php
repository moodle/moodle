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

namespace core_external;

/**
 * Unit tests for core_external\util.
 *
 * @package     core_external
 * @category    test
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers      \core_external\util
 */
class util_test extends \advanced_testcase {
    /** @var \moodle_database The database connection */
    protected $db;

    /**
     * Store the global DB for restore between tests.
     */
    public function setUp(): void {
        global $DB;
        $this->db = $DB;
    }

    /**
     * Reset the global DB between tests.
     */
    public function tearDown(): void {
        global $DB;
        if ($this->db !== null) {
            $DB = $this->db;
        }
    }

    /**
     * Validate courses, but still return courses even if they fail validation.
     *
     * @covers \core_external\util::validate_courses
     */
    public function test_validate_courses_keepfails(): void {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $courseids = [$c1->id, $c2->id, $c3->id];

        $this->setUser($u1);
        [$courses, $warnings] = util::validate_courses($courseids, [], false, true);
        $this->assertCount(2, $warnings);
        $this->assertEquals($c2->id, $warnings[0]['itemid']);
        $this->assertEquals($c3->id, $warnings[1]['itemid']);
        $this->assertCount(3, $courses);
        $this->assertTrue($courses[$c1->id]->contextvalidated);
        $this->assertFalse($courses[$c2->id]->contextvalidated);
        $this->assertFalse($courses[$c3->id]->contextvalidated);
    }

    /**
     * Validate courses can re-use an array of prefetched courses.
     *
     * @covers \core_external\util::validate_courses
     */
    public function test_validate_courses_prefetch(): void {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $c4 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);

        $courseids = [$c1->id, $c2->id, $c3->id];
        $courses = [$c2->id => $c2, $c3->id => $c3, $c4->id => $c4];

        $this->setUser($u1);
        list($courses, $warnings) = util::validate_courses($courseids, $courses);
        $this->assertCount(2, $courses);
        $this->assertCount(1, $warnings);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertSame($c2, $courses[$c2->id]);
        $this->assertArrayNotHasKey($c3->id, $courses);
        // The extra course passed is not returned.
        $this->assertArrayNotHasKey($c4->id, $courses);
    }

    /**
     * Test the Validate courses standard functionality.
     *
     * @covers \core_external\util::validate_courses
     */
    public function test_validate_courses(): void {
        $this->resetAfterTest(true);

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $courseids = [$c1->id, $c2->id, $c3->id];

        $this->setAdminUser();
        [$courses, $warnings] = \external_util::validate_courses($courseids);
        $this->assertEmpty($warnings);
        $this->assertCount(3, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayHasKey($c2->id, $courses);
        $this->assertArrayHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
        $this->assertEquals($c2->id, $courses[$c2->id]->id);
        $this->assertEquals($c3->id, $courses[$c3->id]->id);

        $this->setUser($u1);
        [$courses, $warnings] = \external_util::validate_courses($courseids);
        $this->assertCount(2, $warnings);
        $this->assertEquals($c2->id, $warnings[0]['itemid']);
        $this->assertEquals($c3->id, $warnings[1]['itemid']);
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($c1->id, $courses);
        $this->assertArrayNotHasKey($c2->id, $courses);
        $this->assertArrayNotHasKey($c3->id, $courses);
        $this->assertEquals($c1->id, $courses[$c1->id]->id);
    }

    /**
     * Text util::get_area_files
     *
     * @covers \core_external\util::get_area_files
     */
    public function test_external_util_get_area_files(): void {
        global $CFG, $DB;

        $this->DB = $DB;
        $DB = $this->getMockBuilder('moodle_database')->getMock();

        $content = base64_encode("Let us create a nice simple file.");
        $timemodified = 102030405;
        $itemid = 42;
        $filesize = strlen($content);

        $DB->method('get_records_sql')->willReturn([
            (object) [
                'filename'      => 'example.txt',
                'filepath'      => '/',
                'mimetype'      => 'text/plain',
                'filesize'      => $filesize,
                'timemodified'  => $timemodified,
                'itemid'        => $itemid,
                'pathnamehash'  => sha1('/example.txt'),
            ],
        ]);

        $component = 'mod_foo';
        $filearea = 'area';
        $context = 12345;

        $expectedfiles[] = [
            'filename' => 'example.txt',
            'filepath' => '/',
            'fileurl' => "{$CFG->wwwroot}/webservice/pluginfile.php/{$context}/{$component}/{$filearea}/{$itemid}/example.txt",
            'timemodified' => $timemodified,
            'filesize' => $filesize,
            'mimetype' => 'text/plain',
            'isexternalfile' => false,
        ];
        // Get all the files for the area.
        $files = util::get_area_files($context, $component, $filearea, false);
        $this->assertEquals($expectedfiles, $files);

        $DB->method('get_in_or_equal')->willReturn([
            '= :mock1',
            ['mock1' => $itemid],
        ]);

        // Get just the file indicated by $itemid.
        $files = util::get_area_files($context, $component, $filearea, $itemid);
        $this->assertEquals($expectedfiles, $files);
    }
}
