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

namespace core;

use advanced_testcase;
use stdClass;
use ReflectionMethod;

/**
 * Unit tests for emoticon manager
 *
 * @package     core
 * @covers      \core\emoticon_manager
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class emoticon_manager_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_emoticons}
     *
     * @return array[]
     */
    public static function get_emoticons_provider(): array {
        return [
            'Only selectable' => [true, ['wink', 'biggrin'], ['egg', 'martin']],
            'Including non-selectable' => [false, ['wink', 'biggrin', 'egg', 'martin']],
        ];
    }

    /**
     * Test getting enabled emoticons
     *
     * @param bool $selectable
     * @param string[] $expected
     * @param string[] $excluded
     *
     * @dataProvider get_emoticons_provider
     */
    public function test_get_emoticons(
        bool $selectable,
        array $expected,
        array $excluded = [],
    ): void {
        $emoticons = (new emoticon_manager())->get_emoticons($selectable);
        $identifiers = array_column($emoticons, 'altidentifier');

        // Assert the subset of expected identifiers are all present.
        $this->assertEquals([], array_diff($expected, $identifiers));

        // Assert none of the excluded identifiers are present.
        $this->assertEquals($excluded, array_diff($excluded, $identifiers));
    }

    /**
     * Test converting emoticon object into renderable
     */
    public function test_prepare_renderable_emoticon(): void {
        $manager = new emoticon_manager();

        // With language string identifier.
        $emoticon = $this->prepare_emoticon_object(':)', 's/smiley', 'smiley');
        $pixemoticon = $manager->prepare_renderable_emoticon($emoticon);

        $this->assertEquals('s/smiley', $pixemoticon->pix);
        $this->assertEquals('core', $pixemoticon->component);
        $this->assertEquals([
            'class' => 'emoticon',
            'alt' => 'smile',
            'title' => 'smile',
        ], $pixemoticon->attributes);

        // With language string identifier from another component.
        $emoticon = $this->prepare_emoticon_object(':)', 's/smiley', 'thanks', 'core');
        $pixemoticon = $manager->prepare_renderable_emoticon($emoticon);

        $this->assertEquals('s/smiley', $pixemoticon->pix);
        $this->assertEquals('core', $pixemoticon->component);
        $this->assertEquals([
            'class' => 'emoticon',
            'alt' => 'Thanks',
            'title' => 'Thanks',
        ], $pixemoticon->attributes);

        // Without language string identifier.
        $emoticon = $this->prepare_emoticon_object(':-O', 's/shock');
        $pixemoticon = $manager->prepare_renderable_emoticon($emoticon);

        $this->assertEquals('s/shock', $pixemoticon->pix);
        $this->assertEquals('core', $pixemoticon->component);
        $this->assertEquals([
            'class' => 'emoticon',
            'alt' => ':-O',
            'title' => ':-O',
        ], $pixemoticon->attributes);
    }

    /**
     * Proxy method for creating emoticon object via {@see \core\emoticon_manager::prepare_emoticon_object}
     *
     * @param mixed ...$params
     * @return stdClass
     */
    private function prepare_emoticon_object(...$params): stdClass {
        $manager = new emoticon_manager();
        $method = new ReflectionMethod($manager, 'prepare_emoticon_object');
        return $method->invoke($manager, ...$params);
    }
}
