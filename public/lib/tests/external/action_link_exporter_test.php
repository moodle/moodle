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

namespace core\external;

/**
 * Tests for action_link_exporter.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\external\action_link_exporter
 */
final class action_link_exporter_test extends \advanced_testcase {
    /**
     * Test the export returns the right structure when the content is a string.
     */
    public function test_export_string(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $url = new \core\url('/some/url');
        $text = 'Click here';
        $icon = new \core\output\pix_icon('i/warning', 'sample');
        $attributes = ['class' => 'me-0 pb-1'];

        $icondata = $icon->get_exporter()->export($renderer);

        $actionlink = new \core\output\action_link(
            url: $url,
            text: $text,
            icon: $icon,
            attributes: $attributes,
        );
        $exporter = new action_link_exporter($actionlink, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('linkurl', $data);
        $this->assertObjectHasProperty('content', $data);
        $this->assertObjectHasProperty('icondata', $data);
        $this->assertObjectHasProperty('classes', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertCount(6, get_object_vars($data));

        $this->assertEquals($url->out(false), $data->linkurl);
        $this->assertEquals($text, $data->content);
        $this->assertEquals($icondata, $data->icondata);
        $this->assertEquals($attributes['class'], $data->classes);
        // For string content, we don't have extra data, so contenttype is 'string' and contentjson is null.
        $this->assertEquals('string', $data->contenttype);
        $this->assertEquals(null, $data->contentjson);
    }

    /**
     * Test the export returns the right structure when the content is a renderable.
     */
    public function test_export_renderable(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $url = new \core\url('/some/url');
        $icon = new \core\output\pix_icon('i/warning', 'sample');
        $attributes = ['class' => 'me-0 pb-1'];

        $icondata = $icon->get_exporter()->export($renderer);

        // We use help_icon as text to simulate a renderable content that is not externable.
        $text = new \core\output\help_icon('search', 'core');

        $actionlink = new \core\output\action_link(
            url: $url,
            text: $text,
            icon: $icon,
            attributes: $attributes,
        );
        $exporter = new action_link_exporter($actionlink, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('linkurl', $data);
        $this->assertObjectHasProperty('content', $data);
        $this->assertObjectHasProperty('icondata', $data);
        $this->assertObjectHasProperty('classes', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertCount(6, get_object_vars($data));

        $this->assertEquals($url->out(false), $data->linkurl);
        $this->assertEquals($renderer->render($text), $data->content);
        $this->assertEquals($icondata, $data->icondata);
        $this->assertEquals($attributes['class'], $data->classes);
        // Since help_icon is not externable, we expect the contenttype to be 'string' and contentjson to be null.
        $this->assertEquals('string', $data->contenttype);
        $this->assertEquals(null, $data->contentjson);
    }

    /**
     * Test the export returns the right structure when the content is a externable.
     */
    public function test_export_externable(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $url = new \core\url('/some/url');
        $icon = new \core\output\pix_icon('i/warning', 'sample');
        $attributes = ['class' => 'me-0 pb-1'];

        $icondata = $icon->get_exporter()->export($renderer);

        // We use pix_icon as text to simulate a renderable content that is externable.
        $text = new \core\output\pix_icon('i/info', 'Information');

        $contentjson = json_encode($text->get_exporter()->export($renderer));

        $actionlink = new \core\output\action_link(
            url: $url,
            text: $text,
            icon: $icon,
            attributes: $attributes,
        );
        $exporter = new action_link_exporter($actionlink, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('linkurl', $data);
        $this->assertObjectHasProperty('content', $data);
        $this->assertObjectHasProperty('icondata', $data);
        $this->assertObjectHasProperty('classes', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertCount(6, get_object_vars($data));

        $this->assertEquals($url->out(false), $data->linkurl);
        $this->assertEquals($renderer->render($text), $data->content);
        $this->assertEquals($icondata, $data->icondata);
        $this->assertEquals($attributes['class'], $data->classes);
        $this->assertEquals('core\output\pix_icon', $data->contenttype);
        $this->assertEquals($contentjson, $data->contentjson);
    }

    /**
     * Test the export returns the right structure when the icon is not set.
     */
    public function test_export_no_icon(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $url = new \core\url('/some/url');
        $attributes = ['class' => 'me-0 pb-1'];

        $text = 'Click here';
        $contentjson = null;

        $actionlink = new \core\output\action_link(
            url: $url,
            text: $text,
            attributes: $attributes,
        );
        $exporter = new action_link_exporter($actionlink, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('linkurl', $data);
        $this->assertObjectHasProperty('content', $data);
        $this->assertObjectHasProperty('icondata', $data);
        $this->assertObjectHasProperty('classes', $data);
        $this->assertObjectHasProperty('contenttype', $data);
        $this->assertObjectHasProperty('contentjson', $data);
        $this->assertCount(6, get_object_vars($data));

        $this->assertEquals($url->out(false), $data->linkurl);
        $this->assertEquals($text, $data->content);
        $this->assertEquals(null, $data->icondata);
        $this->assertEquals($attributes['class'], $data->classes);
        $this->assertEquals('string', $data->contenttype);
        $this->assertEquals($contentjson, $data->contentjson);
    }
}
