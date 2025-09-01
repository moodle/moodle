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

namespace core\navigation;

/**
 * Tests for navbar.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(navbar::class)]
final class navbar_test extends \advanced_testcase {
    public function test_navbar_prepend_and_add(): \moodle_page {
        global $PAGE;
        // Unfortunate hack needed because people use global $PAGE around the place.
        $PAGE->set_url('/');

        // We need to reset after this test because we using the generator.
        $this->resetAfterTest();

        $generator = self::getDataGenerator();
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category(['parent' => $cat1->id]);
        $course = $generator->create_course(['category' => $cat2->id]);

        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url(new \moodle_url('/course/view.php', ['id' => $course->id]));
        $page->navbar->prepend('test 1');
        $page->navbar->prepend('test 2');
        $page->navbar->add('test 3');
        $page->navbar->add('test 4');

        $items = $page->navbar->get_items();
        foreach ($items as $item) {
            $this->assertInstanceOf(navigation_node::class, $item);
        }

        $i = 0;
        $this->assertSame('test 1', $items[$i++]->text);
        $this->assertSame('test 2', $items[$i++]->text);
        $this->assertSame('home', $items[$i++]->key);
        $this->assertSame('courses', $items[$i++]->key);
        $this->assertSame($cat1->id, $items[$i++]->key);
        $this->assertSame($cat2->id, $items[$i++]->key);
        $this->assertSame($course->id, $items[$i++]->key);
        $this->assertSame('test 3', $items[$i++]->text);
        $this->assertSame('test 4', $items[$i++]->text);

        return $page;
    }

    #[\PHPUnit\Framework\Attributes\Depends('test_navbar_prepend_and_add')]
    public function test_navbar_has_items(\moodle_page $page): void {
        $this->resetAfterTest();

        $this->assertTrue($page->navbar->has_items());
    }
}
