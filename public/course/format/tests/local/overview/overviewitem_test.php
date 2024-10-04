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

namespace core_courseformat\local\overview;

use core\output\local\properties\text_align;

/**
 * Tests for overviewitem.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core_courseformat\local\overview\overviewitem
 */
final class overviewitem_test extends \advanced_testcase {
    /**
     * Tests the constructor.
     *
     * @covers ::__construct
     * @covers ::get_name
     * @covers ::get_value
     * @covers ::get_content
     * @covers ::get_text_align
     * @covers ::get_alert_count
     * @covers ::get_alert_label
     */
    public function test_constructor(): void {
        $name = 'Activity name';
        $value = 1;
        $content = 'Activity content';
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';

        $item = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel);

        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($value, $item->get_value());
        $this->assertEquals($content, $item->get_content());
        $this->assertEquals($textalign, $item->get_text_align());
        $this->assertEquals($alertcount, $item->get_alert_count());
        $this->assertEquals($alertlabel, $item->get_alert_label());
    }

    /**
     * Test chained setters.
     *
     * @covers ::set_name
     * @covers ::set_value
     * @covers ::set_content
     * @covers ::set_text_align
     * @covers ::set_alert_count
     * @covers ::set_alert_label
     * @covers ::get_name
     * @covers ::get_value
     * @covers ::get_content
     * @covers ::get_text_align
     * @covers ::get_alert_count
     * @covers ::get_alert_label
     */
    public function test_setters(): void {
        $item = new overviewitem('Sample', 1, 'Content', text_align::CENTER, 1, 'Alert label');

        $this->assertEquals('Sample', $item->get_name());
        $this->assertEquals(1, $item->get_value());
        $this->assertEquals('Content', $item->get_content());
        $this->assertEquals(text_align::CENTER, $item->get_text_align());
        $this->assertEquals(1, $item->get_alert_count());
        $this->assertEquals('Alert label', $item->get_alert_label());

        $name = 'New activity name';
        $value = 2;
        $content = 'New activity content';
        $textalign = text_align::END;
        $alertcount = 2;
        $alertlabel = 'New alert label';

        $item->set_name($name)
            ->set_value($value)
            ->set_content($content)
            ->set_text_align($textalign)
            ->set_alert($alertcount, $alertlabel);

        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($value, $item->get_value());
        $this->assertEquals($content, $item->get_content());
        $this->assertEquals($textalign, $item->get_text_align());
        $this->assertEquals($alertcount, $item->get_alert_count());
        $this->assertEquals($alertlabel, $item->get_alert_label());
    }
}
