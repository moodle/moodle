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
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewitem::class)]
final class overviewitem_test extends \advanced_testcase {
    /**
     * Tests the constructor.
     */
    public function test_constructor(): void {
        $name = 'Activity name';
        $value = 1;
        $content = 'Activity content';
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';
        $extradata = (object)[
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $item = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel, $extradata);

        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($value, $item->get_value());
        $this->assertEquals($content, $item->get_content());
        $this->assertEquals($textalign, $item->get_text_align());
        $this->assertEquals($alertcount, $item->get_alert_count());
        $this->assertEquals($alertlabel, $item->get_alert_label());
        $this->assertNull($item->get_key()); // Key is null by default.
        $this->assertEquals($extradata, $item->get_extra_data());
    }

    /**
     * Test chained setters.
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
        $key = 'newkey';
        $extradata = (object)[
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $item->set_name($name)
            ->set_value($value)
            ->set_content($content)
            ->set_text_align($textalign)
            ->set_alert($alertcount, $alertlabel)
            ->set_key($key)
            ->set_extra_data($extradata);

        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($value, $item->get_value());
        $this->assertEquals($content, $item->get_content());
        $this->assertEquals($textalign, $item->get_text_align());
        $this->assertEquals($alertcount, $item->get_alert_count());
        $this->assertEquals($alertlabel, $item->get_alert_label());
        $this->assertEquals($key, $item->get_key());
        $this->assertEquals($extradata, $item->get_extra_data());
    }

    /**
     * Test get_exporter method.
     */
    public function test_get_exporter(): void {
        $name = 'Activity name';
        $value = 1;
        $content = 'Activity content';
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';
        $extradata = (object) [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $source = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel, $extradata);
        $expectedclass = \core_courseformat\external\overviewitem_exporter::class;

        $exporter = $source->get_exporter();
        $this->assertInstanceOf($expectedclass, $exporter);

        $structure = overviewitem::get_read_structure();
        $this->assertInstanceOf(\core_external\external_single_structure::class, $structure);
        $this->assertEquals(
            $expectedclass::get_read_structure(),
            $structure,
        );

        $structure = overviewitem::read_properties_definition();
        $this->assertEquals(
            $expectedclass::read_properties_definition(),
            $structure,
        );
    }

    /**
     * Test get_content_type method.
     */
    public function test_get_content_type(): void {
        $name = 'Activity name';
        $value = 1;
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';
        $extradata = (object) [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        // Test a basic string content.
        $content = 'Activity content';
        $source = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel, $extradata);
        $this->assertEquals('basic', $source->get_content_type());

        // Test an renderable content.
        $content = new \core\output\action_link(
            url: new \core\url('/some/url'),
            text: 'Click here',
        );
        $source = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel, $extradata);
        $this->assertEquals(\core\output\action_link::class, $source->get_content_type());
    }
}
