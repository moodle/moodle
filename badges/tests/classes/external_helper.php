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

namespace core_badges\tests;

/**
 * Helper trait for external function tests.
 *
 * @package    core_badges
 * @copyright  2025 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait external_helper {

    /**
     * Asserts that a badge class returned by an external function matches the given data.
     *
     * @param array $expected Expected badge data.
     * @param array $actual Actual badge class data returned by the external function.
     * @param bool $canconfiguredetails True if user has capability "moodle/badges:configuredetails".
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    private function assert_badge_class(array $expected, array $actual, bool $canconfiguredetails): void {
        $this->assertEquals('BadgeClass', $actual['type']);
        $badgeurl = new \moodle_url('/badges/badgeclass.php', ['id' => $expected['id']]);
        $this->assertEquals($badgeurl->out(false), $actual['id']);
        $this->assertEquals($expected['issuername'], $actual['issuer']);
        $this->assertEquals($expected['name'], $actual['name']);
        $this->assertEquals($expected['badgeurl'], $actual['image']);
        $this->assertEquals($expected['description'], $actual['description']);
        $this->assertEquals($expected['issuerurl'], $actual['hostedUrl']);
        $this->assertEquals($expected['coursefullname'], $actual['coursefullname'] ?? null);
        if ($canconfiguredetails) {
            $this->assertEquals($expected['courseid'], $actual['courseid'] ?? null);
        } else {
            $this->assertArrayNotHasKey('courseid', $actual);
        }

        $alignments = $expected['alignment'];
        if (!$canconfiguredetails) {
            foreach ($alignments as $index => $alignment) {
                $alignments[$index] = [
                    'id' => $alignment['id'],
                    'badgeid' => $alignment['badgeid'],
                    'targetName' => $alignment['targetName'],
                    'targetUrl' => $alignment['targetUrl'],
                ];
            }
        }
        $this->assertEquals($alignments, $actual['alignment'] ?? []);
    }

    /**
     * Asserts that an issued badge returned by an external function matches the given data.
     *
     * @param array $expected Expected badge data.
     * @param array $actual Actual badge data returned by the external function.
     * @param bool $isrecipient True if user is the badge recipient.
     * @param bool $canconfiguredetails True if user has capability "moodle/badges:configuredetails".
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    protected function assert_issued_badge(array $expected, array $actual, bool $isrecipient, bool $canconfiguredetails): void {
        $this->assertEquals($expected['id'], $actual['id']);
        $this->assertEquals($expected['name'], $actual['name']);
        $this->assertEquals($expected['type'], $actual['type']);
        $this->assertEquals($expected['description'], $actual['description']);
        $this->assertEquals($expected['issuername'], $actual['issuername']);
        $this->assertEquals($expected['issuerurl'], $actual['issuerurl']);
        $this->assertEquals($expected['issuercontact'], $actual['issuercontact']);
        $this->assertEquals($expected['uniquehash'], $actual['uniquehash']);
        $this->assertEquals($expected['dateissued'], $actual['dateissued']);
        $this->assertEquals($expected['dateexpire'], $actual['dateexpire']);
        $this->assertEquals($expected['version'], $actual['version']);
        $this->assertEquals($expected['language'], $actual['language']);
        $this->assertEquals($expected['imagecaption'], $actual['imagecaption']);
        $this->assertEquals($expected['badgeurl'], $actual['badgeurl']);
        $this->assertEquals($expected['recipientid'], $actual['recipientid']);
        $this->assertEquals($expected['recipientfullname'], $actual['recipientfullname']);
        $this->assertEquals($expected['coursefullname'], $actual['coursefullname'] ?? null);
        $this->assertEquals($expected['endorsement'] ?? null, $actual['endorsement'] ?? null);

        if ($isrecipient || $canconfiguredetails) {
            $this->assertTimeCurrent($expected['timecreated']);
            $this->assertTimeCurrent($expected['timemodified']);
            $this->assertEquals($expected['usercreated'], $actual['usercreated']);
            $this->assertEquals($expected['usermodified'], $actual['usermodified']);
            $this->assertEquals($expected['expiredate'], $actual['expiredate']);
            $this->assertEquals($expected['expireperiod'], $actual['expireperiod']);
            $this->assertEquals($expected['courseid'], $actual['courseid']);
            $this->assertEquals($expected['message'], $actual['message']);
            $this->assertEquals($expected['messagesubject'], $actual['messagesubject']);
            $this->assertEquals($expected['attachment'], $actual['attachment']);
            $this->assertEquals($expected['notification'], $actual['notification']);
            $this->assertEquals($expected['nextcron'], $actual['nextcron']);
            $this->assertEquals($expected['status'], $actual['status']);
            $this->assertEquals($expected['issuedid'], $actual['issuedid']);
            $this->assertEquals($expected['visible'], $actual['visible']);
            $this->assertEquals($expected['email'], $actual['email']);
        } else {
            $this->assertEquals(0, $actual['timecreated']);
            $this->assertEquals(0, $actual['timemodified']);
            $this->assertArrayNotHasKey('usercreated', $actual);
            $this->assertArrayNotHasKey('usermodified', $actual);
            $this->assertArrayNotHasKey('expiredate', $actual);
            $this->assertArrayNotHasKey('expireperiod', $actual);
            $this->assertArrayNotHasKey('courseid', $actual);
            $this->assertArrayNotHasKey('message', $actual);
            $this->assertArrayNotHasKey('messagesubject', $actual);
            $this->assertEquals(1, $actual['attachment']);
            $this->assertEquals(1, $actual['notification']);
            $this->assertArrayNotHasKey('nextcron', $actual);
            $this->assertEquals(0, $actual['status']);
            $this->assertArrayNotHasKey('issuedid', $actual);
            $this->assertEquals(0, $actual['visible']);
            $this->assertArrayNotHasKey('email', $actual);
        }

        $alignments = $expected['alignment'];
        if (!$canconfiguredetails) {
            foreach ($alignments as $index => $alignment) {
                $alignments[$index] = [
                    'id' => $alignment['id'],
                    'badgeid' => $alignment['badgeid'],
                    'targetName' => $alignment['targetName'],
                    'targetUrl' => $alignment['targetUrl'],
                ];
            }
        }
        $this->assertEquals($alignments, $actual['alignment']);

        $relatedbadges = $expected['relatedbadges'];
        if (!$canconfiguredetails) {
            foreach ($relatedbadges as $index => $relatedbadge) {
                $relatedbadges[$index] = [
                    'id' => $relatedbadge['id'],
                    'name' => $relatedbadge['name'],
                ];
            }
        }
        $this->assertEquals($relatedbadges, $actual['relatedbadges']);
    }

    /**
     * Creates and returns test data for external functions.
     *
     * The test data includes:
     * - A teacher.
     * - A student.
     * - A site badge, with an endorsment and 2 alignments, issued to the student by the teacher.
     * - A course badge, related to the site badge, issued to the student by the teacher.
     *
     * @return array
     */
    private function prepare_test_data(): array {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();

        // Setup test data.
        $course = $generator->create_course();

        // Create users and enrolments.
        $student = $generator->create_and_enrol($course);
        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $badgegenerator = $generator->get_plugin_generator('core_badges');

        $systemcontext = \context_system::instance();
        $coursecontext = \context_course::instance($course->id);

        // Create a site badge.
        $now = time();
        $sitebadge = $badgegenerator->create_badge([
            'name' => "Test badge site",
            'description' => "Testing badges site",
            'timecreated' => $now,
            'timemodified' => $now,
            'usercreated' => (int) $teacher->id,
            'usermodified' => (int) $teacher->id,
            'expiredate' => $now + YEARSECS,
            'expireperiod' => YEARSECS,
            'type' => BADGE_TYPE_SITE,
        ]);

        $sitebadge->issue($student->id, true);
        $siteissuedbadge = $DB->get_record('badge_issued', [ 'badgeid' => $sitebadge->id ]);

        // Change issued time to ensure badges are fetched in a consistent order.
        $siteissuedbadge->dateissued = $now - 1;
        $DB->update_record('badge_issued', $siteissuedbadge);

        $sitebadgedata = [
            ...(array) $sitebadge,
            'issuedid' => (int) $siteissuedbadge->id,
            'uniquehash' => $siteissuedbadge->uniquehash,
            'dateissued' => (int) $siteissuedbadge->dateissued,
            'dateexpire' => $siteissuedbadge->dateexpire,
            'visible' => (int) $siteissuedbadge->visible,
            'badgeurl' => \moodle_url::make_webservice_pluginfile_url($systemcontext->id, 'badges', 'badgeimage',
                                                                      $sitebadge->id, '/', 'f3')->out(false),
            'recipientid' => $student->id,
            'recipientfullname' => fullname($student),
            'email' => $student->email,
            'coursefullname' => null,
            'endorsement' => null,
            'alignment' => [],
            'relatedbadges' => [],
        ];

        // Add an endorsement for the site badge.
        $endorsement              = new \stdClass();
        $endorsement->badgeid     = $sitebadge->id;
        $endorsement->issuername  = 'Issuer name';
        $endorsement->issuerurl   = 'http://endorsement-issuer-url.domain.co.nz';
        $endorsement->issueremail = 'endorsementissuer@example.com';
        $endorsement->claimid     = 'http://claim-url.domain.co.nz';
        $endorsement->claimcomment = 'Claim comment';
        $endorsement->dateissued  = $now;
        $endorsement->id          = $sitebadge->save_endorsement($endorsement);
        $sitebadgedata['endorsement'] = (array) $endorsement;

        // Add 2 alignments to the site badge.
        $alignment = new \stdClass();
        $alignment->badgeid = $sitebadge->id;
        $alignment->targetname = 'Alignment 1';
        $alignment->targeturl = 'http://a1-target-url.domain.co.nz';
        $alignment->targetdescription = 'A1 target description';
        $alignment->targetframework = 'A1 framework';
        $alignment->targetcode = 'A1 code';
        $alignment->id = $sitebadge->save_alignment($alignment);
        $sitebadgedata['alignment'][] = [
            'id' => $alignment->id,
            'badgeid' => $alignment->badgeid,
            'targetName' => $alignment->targetname,
            'targetUrl' => $alignment->targeturl,
            'targetDescription' => $alignment->targetdescription,
            'targetFramework' => $alignment->targetframework,
            'targetCode' => $alignment->targetcode,
        ];

        $alignment = new \stdClass();
        $alignment->badgeid = $sitebadge->id;
        $alignment->targetname = 'Alignment 2';
        $alignment->targeturl = 'http://a2-target-url.domain.co.nz';
        $alignment->targetdescription = 'A2 target description';
        $alignment->targetframework = 'A2 framework';
        $alignment->targetcode = 'A2 code';
        $alignment->id = $sitebadge->save_alignment($alignment);
        $sitebadgedata['alignment'][] = [
            'id' => $alignment->id,
            'badgeid' => $alignment->badgeid,
            'targetName' => $alignment->targetname,
            'targetUrl' => $alignment->targeturl,
            'targetDescription' => $alignment->targetdescription,
            'targetFramework' => $alignment->targetframework,
            'targetCode' => $alignment->targetcode,
        ];

        // Create a course badge.
        $coursebadge = $badgegenerator->create_badge([
            'name' => "Test badge course",
            'description' => "Testing badges course",
            'timecreated' => $now,
            'timemodified' => $now,
            'usercreated' => (int) $teacher->id,
            'usermodified' => (int) $teacher->id,
            'expiredate' => $now + YEARSECS,
            'expireperiod' => YEARSECS,
            'type' => BADGE_TYPE_COURSE,
            'courseid' => (int) $course->id,
        ]);
        $coursebadge->issue($student->id, true);

        $courseissuedbadge = $DB->get_record('badge_issued', [ 'badgeid' => $coursebadge->id ]);
        $coursebadgedata = [
            ...(array) $coursebadge,
            'issuedid' => (int) $courseissuedbadge->id,
            'uniquehash' => $courseissuedbadge->uniquehash,
            'dateissued' => (int) $courseissuedbadge->dateissued,
            'dateexpire' => $courseissuedbadge->dateexpire,
            'visible' => (int) $courseissuedbadge->visible,
            'badgeurl' => \moodle_url::make_webservice_pluginfile_url($coursecontext->id, 'badges', 'badgeimage',
                                                                      $coursebadge->id, '/', 'f3')->out(false),
            'recipientid' => $student->id,
            'recipientfullname' => fullname($student),
            'email' => $student->email,
            'coursefullname' => \core_external\util::format_string($course->fullname, $coursecontext),
            'endorsement' => null,
            'alignment' => [],
            'relatedbadges' => [],
        ];

        // Add the course badge to the site badge.
        $sitebadge->add_related_badges([$coursebadge->id]);
        $sitebadgedata['relatedbadges'][] = [
            'id'   => (int) $coursebadge->id,
            'name' => $coursebadge->name,
            'version' => $coursebadge->version,
            'language' => $coursebadge->language,
            'type' => $coursebadge->type,
        ];
        $coursebadgedata['relatedbadges'][] = [
            'id'   => (int) $sitebadge->id,
            'name' => $sitebadge->name,
            'version' => $sitebadge->version,
            'language' => $sitebadge->language,
            'type' => $sitebadge->type,
        ];

        return [
            'coursebadge' => $coursebadgedata,
            'sitebadge' => $sitebadgedata,
            'student' => $student,
            'teacher' => $teacher,
        ];
    }
}
