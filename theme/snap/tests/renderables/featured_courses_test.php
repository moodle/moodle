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

namespace theme_snap\tests\renderables;
use theme_snap\renderables\featured_courses;

/**
 * Test course card service.
 * @package   theme_snap
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featured_courses_test extends \advanced_testcase {
    public function test_featured_courses_empty() {
        $fc = new featured_courses();
        $this->assertEmpty($fc->cards);
    }
    public function test_featured_courses() {
        global $USER, $PAGE;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $course1 = $dg->create_course(['fullname' => 'Spanish 101']);
        $course2 = $dg->create_course(['fullname' => 'Biology 101']);
        $course3 = $dg->create_course(['fullname' => 'Physics 101']);
        $course4 = $dg->create_course(['fullname' => 'English 101']);
        set_config('fc_one', $course1->id, 'theme_snap');
        set_config('fc_two', $course2->id, 'theme_snap');
        set_config('fc_three', $course3->id, 'theme_snap');
        set_config('fc_four', $course4->id, 'theme_snap');
        $fc = new featured_courses();
        $this->assertNotEmpty($fc->cards);
        $this->assertEquals($course1->fullname, $fc->cards[0]->title);
        $this->assertEquals($course2->fullname, $fc->cards[1]->title);
        $this->assertEquals($course3->fullname, $fc->cards[2]->title);
        $this->assertEquals($course4->fullname, $fc->cards[3]->title);

        // Test browse all button url empty by default.
        $this->assertEmpty($fc->browseallurl);

        // Enable browse all button and test for url not empty.
        set_config('fc_browse_all', '1', 'theme_snap');
        $fc = new featured_courses();
        $this->assertNotEmpty($fc->browseallurl);

        // Test featured courses title empty if unset.
        set_config('fc_heading', '', 'theme_snap');
        $fc = new featured_courses();
        $this->assertEmpty($fc->heading);

        // Add a title and test for it.
        $fcheading = 'Loads of lovely courses';
        set_config('fc_heading', $fcheading, 'theme_snap');
        $fc = new featured_courses();
        $this->assertEquals($fcheading, $fc->heading);

        // Test edit URL empty when page not in edit mode.
        $this->assertEmpty($fc->editurl);

        // Test edit URL not empty when page in edit mode.
        $this->setAdminUser();
        $USER->editing = true;
        $PAGE->set_context(\context_system::instance());
        $fc = new featured_courses();
        $this->assertNotEmpty($fc->editurl);
    }
}
