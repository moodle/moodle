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

namespace tool_mergeusers\local\merger;

use mod_attendance\output\user_data;
use tool_mergeusers\local\merger\grade_grades_table_merger;
use tool_mergeusers\local\user_merger;

/**
 * Tests for grade_grades_table_merger class.
 *
 * @package    tool_mergeusers
 * @author     Daniel Tomé
 * @copyright  2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class grade_grades_table_merger_test extends \advanced_testcase {
    /** @var \stdClass */
    private $assigngrade;
    /** @var \stdClass */
    private $usertobedeleted;
    /** @var \stdClass */
    private $usertobemaintained;

    protected function setUp(): void {
        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category(['name' => 'Merge Users']);

        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'All about grade grades merge users',
            'enablecompletion' => true,
        ]);

        $this->usertobedeleted = $this->getDataGenerator()->create_user(["firstname" => "Deleted one"]);
        $this->usertobemaintained = $this->getDataGenerator()->create_user(["firstname" => "Maintained one"]);

        $this->getDataGenerator()->enrol_user($this->usertobedeleted->id, $course->id, "student");
        $this->getDataGenerator()->enrol_user($this->usertobemaintained->id, $course->id, "student");

        $this->assigngrade =
            $this->getDataGenerator()->create_grade_item(["courseid" => $course->id, "itemname" => "Assign One"]);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_grade_grades
     * @covers \tool_mergeusers\local\merger\grade_grades_table_merger::merge
     * @dataProvider grade_grades_table_merger_provider
     */
    public function test_keep_data_from_proper_user(
        ?int $usertobemaintainedfinalgrade,
        ?int $usertobedeletedfinalgrade,
        ?int $finalgrade,
        bool $datatokeepisfrommaintaineduser,
    ): void {
        global $DB;

        $usertobedeletedgrade = $this->create_grade_with_finalgrade(
            $this->usertobedeleted->id,
            $usertobedeletedfinalgrade,
        );
        $usertobemaintainedgrade = $this->create_grade_with_finalgrade(
            $this->usertobemaintained->id,
            $usertobemaintainedfinalgrade,
        );

        $mut = new user_merger();
        $mut->merge($this->usertobemaintained->id, $this->usertobedeleted->id);

        $resultantgrade = $DB->get_record(
            "grade_grades",
            [
                "itemid" => $this->assigngrade->id,
                "userid" => $this->usertobemaintained->id,
            ],
        );

        if ($datatokeepisfrommaintaineduser) {
            $gradeid = $usertobemaintainedgrade->id;
            $userid = $this->usertobemaintained->id;
            $missinggradeid = $usertobedeletedgrade->id;
        } else {
            $gradeid = $usertobedeletedgrade->id;
            $userid = $this->usertobemaintained->id;
            $missinggradeid = $usertobemaintainedgrade->id;
        }

        $this->assertEquals($gradeid, $resultantgrade->id);
        $this->assertEquals($userid, $resultantgrade->userid);
        $this->assertEquals($finalgrade, $resultantgrade->finalgrade);

        $this->assertFalse($DB->record_exists("grade_grades", ["id" => $missinggradeid]));
        $this->assertTrue($DB->record_exists("grade_grades", ["id" => $gradeid]));
    }

    public static function grade_grades_table_merger_provider(): array {
        return [
            // Values: grade from user to keep, grade from user to delete, final grade, keep data from user to keep.
            'keep data from user to keep when user to delete has no grades and user to keep has grades' => [7, null, 7, true],
            'keep data from user to keep when both users have grades' => [8, 7, 8, true],
            'keep data from user to delete when user to delete has grades and user to keep has no grades' => [null, 7, 7, false],
            'keep data from user to delete when both users have no grades' => [null, null, null, false],
        ];
    }

    private function create_grade_with_finalgrade(int $userid, int|null $finalgrade): \grade_grade {
        return $this->getDataGenerator()->create_grade_grade(["itemid" => $this->assigngrade->id, "userid" => $userid,
            "finalgrade" => $finalgrade]);
    }
}
