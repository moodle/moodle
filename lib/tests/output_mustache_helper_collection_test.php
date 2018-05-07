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

/**
 * Unit tests for lib/classes/output/mustache_helper_collection
 *
 * @copyright 2019 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\mustache_helper_collection;

/**
 * Unit tests for the mustache_helper_collection class.
 */
class core_output_mustache_helper_collection_testcase extends advanced_testcase {
    /**
     * Test cases to confirm that blacklisted helpers are stripped from the source
     * text by the helper before being passed to other another helper. This prevents
     * nested calls to helpers.
     */
    public function get_strip_blacklisted_helpers_testcases() {
        return [
            'no blacklist' => [
                'blacklist' => [],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{#js}} some nasty JS {{/js}}'
            ],
            'blacklist no match' => [
                'blacklist' => ['foo'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{#js}} some nasty JS {{/js}}'
            ],
            'blacklist partial match 1' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{#json}} some nasty JS {{/json}}',
                'expected' => 'core, move, {{#json}} some nasty JS {{/json}}'
            ],
            'blacklist partial match 2' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{#onjs}} some nasty JS {{/onjs}}',
                'expected' => 'core, move, {{#onjs}} some nasty JS {{/onjs}}'
            ],
            'single blacklist 1' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'single blacklist 2' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{ # js }} some nasty JS {{ /  js }}',
                'expected' => 'core, move, {{}}'
            ],
            'single blacklist 3' => [
                'blacklist' => ['js'],
                'input' => 'core, {{#js}} some nasty JS {{/js}}, test',
                'expected' => 'core, {{}}, test'
            ],
            'single blacklist 3' => [
                'blacklist' => ['js'],
                'input' => 'core, {{#ok}} this is ok {{/ok}}, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, {{#ok}} this is ok {{/ok}}, {{}}'
            ],
            'single blacklist multiple matches 1' => [
                'blacklist' => ['js'],
                'input' => 'core, {{#js}} some nasty JS {{/js}}, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, {{}}'
            ],
            'single blacklist multiple matches 2' => [
                'blacklist' => ['js'],
                'input' => 'core, {{ # js }} some nasty JS {{ /  js }}, {{ # js }} some nasty JS {{ /  js }}',
                'expected' => 'core, {{}}'
            ],
            'single blacklist multiple matches nested 1' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{#js}} some nasty JS {{#js}} some nasty JS {{/js}} {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'single blacklist multiple matches nested 2' => [
                'blacklist' => ['js'],
                'input' => 'core, move, {{ # js }} some nasty JS {{ # js }} some nasty JS {{ /  js }}{{ /  js }}',
                'expected' => 'core, move, {{}}'
            ],
            'multiple blacklist 1' => [
                'blacklist' => ['js', 'foo'],
                'input' => 'core, move, {{#js}} some nasty JS {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
            'multiple blacklist 2' => [
                'blacklist' => ['js', 'foo'],
                'input' => 'core, {{#foo}} blah {{/foo}}, {{#js}} js {{/js}}',
                'expected' => 'core, {{}}, {{}}'
            ],
            'multiple blacklist 3' => [
                'blacklist' => ['js', 'foo'],
                'input' => '{{#foo}} blah {{/foo}}, {{#foo}} blah {{/foo}}, {{#js}} js {{/js}}',
                'expected' => '{{}}, {{}}'
            ],
            'multiple blacklist 4' => [
                'blacklist' => ['js', 'foo'],
                'input' => '{{#foo}} blah {{/foo}}, {{#js}} js {{/js}}, {{#foo}} blah {{/foo}}',
                'expected' => '{{}}'
            ],
            'multiple blacklist 4' => [
                'blacklist' => ['js', 'foo'],
                'input' => 'core, move, {{#js}} JS {{#foo}} blah {{/foo}} {{/js}}',
                'expected' => 'core, move, {{}}'
            ],
        ];
    }

    /**
     * Test that the mustache_helper_collection class correctly strips
     * @dataProvider get_strip_blacklisted_helpers_testcases()
     * @param string[] $blacklist The list of helpers to strip
     * @param string $input The input string for the helper
     * @param string $expected The expected output of the string after blacklist strip
     */
    public function test_strip_blacklisted_helpers($blacklist, $input, $expected) {
        $collection = new mustache_helper_collection(null, $blacklist);
        $this->assertEquals($expected, $collection->strip_blacklisted_helpers($blacklist, $input));
    }

    /**
     * Test that the blacklisted helpers are disabled during the execution of other
     * helpers.
     *
     * Any non-blacklisted helper should still be available to call during the
     * execution of a helper.
     */
    public function test_blacklisted_helpers_disabled_during_execution() {
        $engine = new \Mustache_Engine();
        $context = new \Mustache_Context();
        $lambdahelper = new \Mustache_LambdaHelper($engine, $context);
        $blacklist = ['bad'];
        $collection = new mustache_helper_collection(null, $blacklist);
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
}
