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

namespace mod_wiki;

/**
 * Genarator tests class for mod_wiki.
 *
 * @package    mod_wiki
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {

    public function test_create_instance() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('wiki', array('course' => $course->id)));
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course));
        $records = $DB->get_records('wiki', array('course' => $course->id), 'id');
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($wiki->id, $records));

        $params = array('course' => $course->id, 'name' => 'Another wiki');
        $wiki = $this->getDataGenerator()->create_module('wiki', $params);
        $records = $DB->get_records('wiki', array('course' => $course->id), 'id');
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another wiki', $records[$wiki->id]->name);
    }

    public function test_create_content() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course));
        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');

        $page1 = $wikigenerator->create_first_page($wiki);
        $page2 = $wikigenerator->create_content($wiki);
        $page3 = $wikigenerator->create_content($wiki, array('title' => 'Custom title', 'tags' => array('Cats', 'mice')));
        unset($wiki->cmid);
        $page4 = $wikigenerator->create_content($wiki, array('tags' => 'Cats, dogs'));
        $subwikis = $DB->get_records('wiki_subwikis', array('wikiid' => $wiki->id), 'id');
        $this->assertEquals(1, count($subwikis));
        $subwikiid = key($subwikis);
        $records = $DB->get_records('wiki_pages', array('subwikiid' => $subwikiid), 'id');
        $this->assertEquals(4, count($records));
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page3->id, $records[$page3->id]->id);
        $this->assertEquals('Custom title', $records[$page3->id]->title);
        $this->assertEquals(array('Cats', 'mice'),
                array_values(\core_tag_tag::get_item_tags_array('mod_wiki', 'wiki_pages', $page3->id)));
        $this->assertEquals(array('Cats', 'dogs'),
                array_values(\core_tag_tag::get_item_tags_array('mod_wiki', 'wiki_pages', $page4->id)));
    }

    public function test_create_content_individual() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki',
                array('course' => $course, 'wikimode' => 'individual'));
        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');

        $page1 = $wikigenerator->create_first_page($wiki);
        $page2 = $wikigenerator->create_content($wiki);
        $page3 = $wikigenerator->create_content($wiki, array('title' => 'Custom title for admin'));
        $subwikis = $DB->get_records('wiki_subwikis', array('wikiid' => $wiki->id), 'id');
        $this->assertEquals(1, count($subwikis));
        $subwikiid = key($subwikis);
        $records = $DB->get_records('wiki_pages', array('subwikiid' => $subwikiid), 'id');
        $this->assertEquals(3, count($records));
        $this->assertEquals($page1->id, $records[$page1->id]->id);
        $this->assertEquals($page2->id, $records[$page2->id]->id);
        $this->assertEquals($page3->id, $records[$page3->id]->id);
        $this->assertEquals('Custom title for admin', $records[$page3->id]->title);

        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        $this->setUser($user);

        $page1s = $wikigenerator->create_first_page($wiki);
        $page2s = $wikigenerator->create_content($wiki);
        $page3s = $wikigenerator->create_content($wiki, array('title' => 'Custom title for student'));
        $subwikis = $DB->get_records('wiki_subwikis', array('wikiid' => $wiki->id), 'id');
        $this->assertEquals(2, count($subwikis));
        next($subwikis);
        $subwikiid = key($subwikis);
        $records = $DB->get_records('wiki_pages', array('subwikiid' => $subwikiid), 'id');
        $this->assertEquals(3, count($records));
        $this->assertEquals($page1s->id, $records[$page1s->id]->id);
        $this->assertEquals($page2s->id, $records[$page2s->id]->id);
        $this->assertEquals($page3s->id, $records[$page3s->id]->id);
        $this->assertEquals('Custom title for student', $records[$page3s->id]->title);
    }
}
