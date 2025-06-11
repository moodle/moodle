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
use theme_snap\renderables\featured_categories;

/**
 * Test category card service.
 * @package   theme_snap
 * @author    Bryan Cruz
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featured_categories_test extends \advanced_testcase {
    public function test_featured_categories_empty() {
        $fcat = new featured_categories();
        $this->assertEmpty($fcat->cards);
    }
    public function test_featured_categories() {
        global $USER, $PAGE;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $category1 = $dg->create_category(['name' => 'Science']);
        $category2 = $dg->create_category(['name' => 'Arts']);
        $category3 = $dg->create_category(['name' => 'Mathematics']);
        $category4 = $dg->create_category(['name' => 'Languages']);
        set_config('fcat_one', $category1->id, 'theme_snap');
        set_config('fcat_two', $category2->id, 'theme_snap');
        set_config('fcat_three', $category3->id, 'theme_snap');
        set_config('fcat_four', $category4->id, 'theme_snap');
        $fcat = new featured_categories();
        $this->assertNotEmpty($fcat->cards);
        $this->assertEquals($category1->name, $fcat->cards[0]->title);
        $this->assertEquals($category2->name, $fcat->cards[1]->title);
        $this->assertEquals($category3->name, $fcat->cards[2]->title);
        $this->assertEquals($category4->name, $fcat->cards[3]->title);

        // Test browse all button url empty by default.
        $this->assertEmpty($fcat->browseallurl);

        // Enable browse all button and test for url not empty.
        set_config('fcat_browse_all', '1', 'theme_snap');
        $fcat = new featured_categories();
        $this->assertNotEmpty($fcat->browseallurl);

        // Test featured categories title empty if unset.
        set_config('fcat_heading', '', 'theme_snap');
        $fcat = new featured_categories();
        $this->assertEmpty($fcat->heading);

        // Add a title and test for it.
        $fcatheading = 'Loads of lovely categories';
        set_config('fcat_heading', $fcatheading, 'theme_snap');
        $fcat = new featured_categories();
        $this->assertEquals($fcatheading, $fcat->heading);

        // Test edit URL empty when page not in edit mode.
        $this->assertEmpty($fcat->editurl);

        // Test edit URL not empty when page in edit mode.
        $this->setAdminUser();
        $USER->editing = true;
        $PAGE->set_context(\context_system::instance());
        $fcat = new featured_categories();
        $this->assertNotEmpty($fcat->editurl);
    }
}
