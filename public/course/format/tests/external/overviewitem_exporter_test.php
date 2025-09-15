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

namespace core_courseformat\external;

use core\output\local\properties\text_align;
use core_courseformat\local\overview\overviewitem;

/**
 * Tests for overviewitem_exporter.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewitem_exporter::class)]
final class overviewitem_exporter_test extends \advanced_testcase {
    /**
     * Test export with basic content.
     */
    public function test_export(): void {
        $name = 'Activity name';
        $value = 1;
        $content = 'Activity content';
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';
        $key = 'newkey';
        $extradata = (object) [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel, $extradata);
        $source->set_key($key);

        $exporter = new overviewitem_exporter($source, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('name', $data);
        $this->assertObjectHasProperty('key', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('exportertype', $data);
        $this->assertObjectHasProperty('alertlabel', $data);
        $this->assertObjectHasProperty('alertcount', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertObjectHasProperty('extrajson', $data);
        $this->assertCount(8, get_object_vars($data));

        $expecteddata = (object)[
            'value' => $value,
            'datatype' => gettype($value),
            'content' => $content,
        ];
        $contentdata = json_decode($data->contentjson);
        $this->assertEquals($expecteddata, $contentdata);

        $this->assertEquals($name, $data->name);
        $this->assertEquals($key, $data->key);
        $this->assertEquals(overviewitem::BASIC_CONTENT_TYPE, $data->contenttype);
        $this->assertEquals('', $data->exportertype);
        $this->assertEquals($alertcount, $data->alertcount);
        $this->assertEquals($alertlabel, $data->alertlabel);
        $this->assertEquals($extradata, json_decode($data->extrajson));
    }

    /**
     * Test export with a content that is a renderable.
     */
    public function test_export_renderable_content(): void {
        $name = 'Activity name';
        $value = 1;
        $textalign = text_align::CENTER;
        $alertcount = 1;
        $alertlabel = 'Alert label';
        $key = 'newkey';

        $content = new \core\output\action_link(
            url: new \core\url('/some/url'),
            text: 'Click here',
        );

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new overviewitem($name, $value, $content, $textalign, $alertcount, $alertlabel);
        $source->set_key($key);

        $exporter = new overviewitem_exporter($source, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('name', $data);
        $this->assertObjectHasProperty('key', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('exportertype', $data);
        $this->assertObjectHasProperty('alertlabel', $data);
        $this->assertObjectHasProperty('alertcount', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertObjectHasProperty('extrajson', $data);
        $this->assertCount(8, get_object_vars($data));

        $contentexporter = $content->get_exporter();
        $expecteddata = $contentexporter->export($renderer);
        $contentdata = json_decode($data->contentjson);
        $this->assertEquals($expecteddata, $contentdata);

        $this->assertEquals($name, $data->name);
        $this->assertEquals($key, $data->key);
        $this->assertEquals($content::class, $data->contenttype);
        $this->assertEquals($contentexporter::class, $data->exportertype);
        $this->assertEquals($alertcount, $data->alertcount);
        $this->assertEquals($alertlabel, $data->alertlabel);
        $this->assertNull($data->extrajson);
    }

    /**
     * Test export with default values.
     *
     * This test checks that the exporter correctly handles default values when they are not specified.
     */
    public function test_export_default_values(): void {
        $name = 'Activity name';
        $value = 1;

        // Those are the default values when not specified.
        $alertcount = 0;
        $alertlabel = '';
        $key = '';

        $content = new \core\output\action_link(
            url: new \core\url('/some/url'),
            text: 'Click here',
        );

        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $source = new overviewitem($name, $value, $content);

        $exporter = new overviewitem_exporter($source, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('name', $data);
        $this->assertObjectHasProperty('key', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('exportertype', $data);
        $this->assertObjectHasProperty('alertlabel', $data);
        $this->assertObjectHasProperty('alertcount', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertObjectHasProperty('extrajson', $data);
        $this->assertCount(8, get_object_vars($data));

        $contentexporter = $content->get_exporter();
        $expecteddata = $contentexporter->export($renderer);
        $contentdata = json_decode($data->contentjson);
        $this->assertEquals($expecteddata, $contentdata);

        $this->assertEquals($name, $data->name);
        $this->assertEquals($key, $data->key);
        $this->assertEquals($content::class, $data->contenttype);
        $this->assertEquals($contentexporter::class, $data->exportertype);
        $this->assertEquals($alertcount, $data->alertcount);
        $this->assertEquals($alertlabel, $data->alertlabel);
        $this->assertNull($data->extrajson);
    }
}
