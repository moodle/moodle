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

namespace core_badges\output;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Class manage_badge_action_bar_test
 *
 * Unit test for the badges tertiary navigation
 *
 * @coversDefaultClass \core_badges\output\manage_badge_action_bar
 * @package     core_badges
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_badge_action_bar_test extends \advanced_testcase {
    /**
     * Data provider for test_generate_badge_navigation
     *
     * @return array
     */
    public static function generate_badge_navigation_provider(): array {
        return [
            "Test tertiary nav as an editing teacher" => [
                "editingteacher", [
                    'Overview',
                    'Edit details',
                    'Criteria',
                    'Message',
                    'Recipients (0)',
                    'Endorsement',
                    'Related badges (0)',
                    'Alignments (0)'
                ],
            ],
            "Test tertiary nav as an non-editing teacher" => [
                "teacher", [
                    'Overview',
                    'Recipients (0)'
                ],
            ],
            "Test tertiary nav as an admin" => [
                "admin", [
                    'Overview',
                    'Edit details',
                    'Criteria',
                    'Message',
                    'Recipients (0)',
                    'Endorsement',
                    'Related badges (0)',
                    'Alignments (0)'
                ]
            ],
            "Test tertiary nav as a student" => [
                "student", [],
            ]
        ];
    }

    /**
     * Test the generate_badge_navigation function
     *
     * @dataProvider generate_badge_navigation_provider
     * @param string $role
     * @param array $expected
     * @covers ::generate_badge_navigation
     */
    public function test_generate_badge_navigation(string $role, array $expected): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        if ($role != 'admin') {
            $user = $this->getDataGenerator()->create_and_enrol($course, $role);
            $this->setUser($user);
        } else {
            $this->setAdminUser();
        }

        // Mock up a course badge.
        $now = time();
        $badge = new \stdClass();
        $badge->id = null;
        $badge->name = "Test badge course";
        $badge->description = "Testing badges course";
        $badge->type = BADGE_TYPE_COURSE;
        $badge->courseid = $course->id;
        $badge->timecreated = $now - 12;
        $badge->timemodified = $now - 12;
        $badge->usercreated = $teacher->id;
        $badge->usermodified = $teacher->id;
        $badge->issuername = "Test issuer";
        $badge->issuerurl = "http://issuer-url.domain.co.nz";
        $badge->issuercontact = "issuer@example.com";
        $badge->expiredate = null;
        $badge->expireperiod = null;
        $badge->messagesubject = "Test message subject for badge";
        $badge->message = "Test message body for badge";
        $badge->attachment = 1;
        $badge->notification = 0;
        $badge->status = BADGE_STATUS_ACTIVE;
        $badge->version = '1';
        $badge->language = 'en';
        $badge->imageauthorname = 'Image author';
        $badge->imageauthoremail = 'imageauthor@example.com';
        $badge->imageauthorurl = 'http://image-author-url.domain.co.nz';
        $badge->imagecaption = 'Caption';
        $coursebadgeid = $DB->insert_record('badge', $badge, true);
        $badge = new \core_badges\badge($coursebadgeid);

        $context = \context_course::instance($course->id);
        $page = new \moodle_page();
        $page->set_context($context);
        $actionbar = new manage_badge_action_bar($badge, $page);

        $rc = new \ReflectionClass(manage_badge_action_bar::class);
        $rcm = $rc->getMethod('generate_badge_navigation');
        $content = $rcm->invoke($actionbar);
        $this->assertEquals($expected, array_values($content));
    }
}
