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

namespace core\output;

/**
 * Test escaping of Mustache template placeholders.
 *
 * @package   core
 * @copyright 2025 Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class mustache_escape_test extends \advanced_testcase {
    /**
     * Test escaping of characters in {{ }} placeholders.
     *
     * @covers \renderer_base::render_from_template
     */
    public function test_escape(): void {
        $page = new \moodle_page();
        $page->set_url('/');
        $page->set_context(null);
        $renderer = new renderer_base($page, RENDERER_TARGET_GENERAL);

        // Get the mustache engine from the renderer.
        $reflection = new \ReflectionMethod($renderer, 'get_mustache');
        /** @var \Mustache_Engine $engine */
        $engine = $reflection->invoke($renderer);

        // Swap to custom loader.
        $loader = new \Mustache_Loader_ArrayLoader([
            'core/test' => '<a href="#" title="{{title}}">test</a>',
        ]);
        $engine->setLoader($loader);

        $teststring = 'Test title < > " \' & &lt;';

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => $teststring],
        );
        $this->assertSame(
            '<a href="#" title="Test title &lt; &gt; &quot; &#039; &amp; &amp;lt;">test</a>',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => s($teststring)],
        );
        $this->assertSame(
            '<a href="#" title="Test title &amp;lt; &amp;gt; &amp;quot; &#039; &amp;amp; &amp;amp;lt;">test</a>',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => clean_string($teststring)],
        );
        $this->assertSame(
            '<a href="#" title="Test title &#60; &#62; &#34; &#39; &#38; &#60;">test</a>',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => clean_string(clean_string($teststring))],
        );
        $this->assertSame(
            '<a href="#" title="Test title &#60; &#62; &#34; &#39; &#38; &#60;">test</a>',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => s(clean_string($teststring))],
        );
        $this->assertSame(
            '<a href="#" title="Test title &#60; &#62; &#34; &#39; &#38; &#60;">test</a>',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['title' => format_string(clean_string($teststring))],
        );
        $this->assertSame(
            '<a href="#" title="Test title &#60; &#62; &#34; &#39; &#38; &#60;">test</a>',
            $result
        );
    }

    /**
     * Test that there is no escaping of characters in {{{ }}} placeholders.
     *
     * @covers \renderer_base::render_from_template
     */
    public function test_no_escape(): void {
        $page = new \moodle_page();
        $page->set_url('/');
        $page->set_context(null);
        $renderer = new renderer_base($page, RENDERER_TARGET_GENERAL);

        // Get the mustache engine from the renderer.
        $reflection = new \ReflectionMethod($renderer, 'get_mustache');
        /** @var \Mustache_Engine $engine */
        $engine = $reflection->invoke($renderer);

        // Swap to custom loader.
        $loader = new \Mustache_Loader_ArrayLoader([
            'core/test' => 'Some {{{html}}} test',
        ]);
        $engine->setLoader($loader);

        $teststring = 'Test title < > " \' & &lt;';

        $result = $renderer->render_from_template(
            'core/test',
            ['html' => $teststring],
        );
        $this->assertSame(
            'Some Test title < > " \' & &lt; test',
            $result
        );

        $result = $renderer->render_from_template(
            'core/test',
            ['html' => clean_string($teststring)],
        );
        $this->assertSame(
            'Some Test title &#60; &#62; &#34; &#39; &#38; &#60; test',
            $result
        );
    }
}
