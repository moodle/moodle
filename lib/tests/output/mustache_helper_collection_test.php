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
 * Unit tests for the mustache_helper_collection class.
 *
 * @package   core
 * @copyright 2019 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\output\mustache_helper_collection
 */
class mustache_helper_collection_test extends \advanced_testcase {

    /**
     * Test cases to confirm that disallowed helpers are stripped from the source
     * text by the helper before being passed to other another helper. This prevents
     * nested calls to helpers.
     */
    public function get_strip_disallowed_helpers_testcases() {
        return [
            'no disallowed' => [
                'disallowed' => [],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{#js}} some nasty JS {{/js}}'
            ],
            'disallowed no match' => [
                'disallowed' => ['foo'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{#js}} some nasty JS {{/js}}'
            ],
            'disallowed partial match 1' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{#json}} some nasty JS {{/json}}',
                'expected' => 'core, move, {{#json}} some nasty JS {{/json}}'
            ],
            'disallowed partial match 2' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{#onjs}} some nasty JS {{/onjs}}',
                'expected' => 'core, move, {{#onjs}} some nasty JS {{/onjs}}'
            ],
            'single disallowed 1' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'single disallowed 2' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{ # js }} some nasty JS {{ /  js }}',
                'expected' => 'core, move, {{}}'
            ],
            'single disallowed 3' => [
                'disallowed' => ['js'],
                'input' => 'core, {{#js}} some nasty JS {{/js}}, test',
                'expected' => 'core, {{}}, test'
            ],
            'single disallowed 4' => [
                'disallowed' => ['js'],
                'input' => 'core, {{#ok}} this is ok {{/ok}}, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, {{#ok}} this is ok {{/ok}}, {{}}'
            ],
            'single disallowed multiple matches 1' => [
                'disallowed' => ['js'],
                'input' => 'core, {{#js}} some nasty JS {{/js}}, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, {{}}'
            ],
            'single disallowed multiple matches 2' => [
                'disallowed' => ['js'],
                'input' => 'core, {{ # js }} some nasty JS {{ /  js }}, {{ # js }} some nasty JS {{ /  js }}',
                'expected' => 'core, {{}}'
            ],
            'single disallowed multiple matches nested 1' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{#js}} some nasty JS {{#js}} some nasty JS {{/js}} {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'single disallowed multiple matches nested 2' => [
                'disallowed' => ['js'],
                'input' => 'core, move, {{ # js }} some nasty JS {{ # js }} some nasty JS {{ /  js }}{{ /  js }}',
                'expected' => 'core, move, {{}}'
            ],
            'multiple disallowed 1' => [
                'disallowed' => ['js', 'foo'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'multiple disallowed 2' => [
                'disallowed' => ['js', 'foo'],
                'input' => 'core, {{#foo}} blah {{/foo}}, {{#js}} js {{/js}}',
                'expected' => 'core, {{}}, {{}}'
            ],
            'multiple disallowed 3' => [
                'disallowed' => ['js', 'foo'],
                'input' => '{{#foo}} blah {{/foo}}, {{#foo}} blah {{/foo}}, {{#js}} js {{/js}}',
                'expected' => '{{}}, {{}}'
            ],
            'multiple disallowed 4' => [
                'disallowed' => ['js', 'foo'],
                'input' => '{{#foo}} blah {{/foo}}, {{#js}} js {{/js}}, {{#foo}} blah {{/foo}}',
                'expected' => '{{}}'
            ],
            'multiple disallowed 5' => [
                'disallowed' => ['js', 'foo'],
                'input' => 'core, move, {{#js}} JS {{#foo}} blah {{/foo}} {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
        ];
    }

    /**
     * Test that the mustache_helper_collection class correctly strips
     * @dataProvider get_strip_disallowed_helpers_testcases()
     * @param string[] $disallowed The list of helpers to strip
     * @param string $input The input string for the helper
     * @param string $expected The expected output of the string after disallowed strip
     */
    public function test_strip_disallowed_helpers($disallowed, $input, $expected) {
        $collection = new mustache_helper_collection(null, $disallowed);
        $this->assertEquals($expected, $collection->strip_disallowed_helpers($disallowed, $input));
    }

    /**
     * Test that the disallowed helpers are disabled during the execution of other
     * helpers.
     *
     * Any allowed helper should still be available to call during the
     * execution of a helper.
     */
    public function test_disallowed_helpers_disabled_during_execution() {
        $engine = new \Mustache_Engine();
        $context = new \Mustache_Context();
        $lambdahelper = new \Mustache_LambdaHelper($engine, $context);
        $disallowed = ['bad'];
        $collection = new mustache_helper_collection(null, $disallowed);
        $badcalled = false;
        $goodcalled = false;

        $badhelper = function() use (&$badcalled) {
            $badcalled = true;
            return '';
        };
        $goodhelper = function() use (&$goodcalled) {
            $goodcalled = true;
            return '';
        };
        // A test helper that just returns the text without modifying it.
        $testhelper = function($text, $lambda) use ($collection) {
            $collection->get('good')($text, $lambda);
            $collection->get('bad')($text, $lambda);
            return $text;
        };
        $collection->add('bad', $badhelper);
        $collection->add('good', $goodhelper);
        $collection->add('test', $testhelper);

        $this->assertEquals('success output', $collection->get('test')('success output', $lambdahelper));
        $this->assertTrue($goodcalled);
        $this->assertFalse($badcalled);
    }

    /**
     * Test that calling deprecated method strip_blacklisted_helpers() still works and shows developer debugging.
     */
    public function test_deprecated_strip_blacklisted_helpers() {

        $collection = new mustache_helper_collection(null, ['js']);
        $stripped = $collection->strip_blacklisted_helpers(['js'], '{{#js}} JS {{/js}}');
        $this->assertEquals('{{}}', $stripped);
        $this->assertDebuggingCalled('mustache_helper_collection::strip_blacklisted_helpers() is deprecated. ' .
            'Please use mustache_helper_collection::strip_disallowed_helpers() instead.', DEBUG_DEVELOPER);
    }
}
