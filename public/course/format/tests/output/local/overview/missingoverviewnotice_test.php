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

namespace core_courseformat\output\local\overview;

/**
 * Tests for courseformat
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(missingoverviewnotice::class)]
final class missingoverviewnotice_test extends \advanced_testcase {
    /**
     * Test overview integrations.
     *
     * @param string $modname
     * @param bool $expectempty
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('overview_integrations_provider')]
    public function test_overview_integrations(
        string $modname,
        bool $expectempty,
    ): void {
        global $PAGE;

        $this->resetAfterTest();

        $renderer = $PAGE->get_renderer('core');
        $course = $this->getDataGenerator()->create_course();

        $missingoverviewnotice = new missingoverviewnotice($course, $modname);
        $export = $missingoverviewnotice->export_for_template($renderer);

        if ($expectempty) {
            $this->assertEquals((object) [], $export);
        } else {
            $this->assertNotEquals((object) [], $export);
        }
    }

    /**
     * Data provider for test_overview_integrations.
     *
     * @return \Generator
     */
    public static function overview_integrations_provider(): \Generator {
        yield 'assign' => [
            'modname' => 'assign',
            'expectempty' => true,
        ];
        yield 'bigbluebuttonbn' => [
            'modname' => 'bigbluebuttonbn',
            'expectempty' => true,
        ];
        yield 'book' => [
            'modname' => 'book',
            'expectempty' => false,
        ];
        yield 'choice' => [
            'modname' => 'choice',
            'expectempty' => true,
        ];
        yield 'data' => [
            'modname' => 'data',
            'expectempty' => true,
        ];
        yield 'feedback' => [
            'modname' => 'feedback',
            'expectempty' => true,
        ];
        yield 'folder' => [
            'modname' => 'folder',
            'expectempty' => false,
        ];
        yield 'forum' => [
            'modname' => 'forum',
            'expectempty' => true,
        ];
        yield 'glossary' => [
            'modname' => 'glossary',
            'expectempty' => true,
        ];
        yield 'h5pactivity' => [
            'modname' => 'h5pactivity',
            'expectempty' => true,
        ];
        yield 'imscp' => [
            'modname' => 'imscp',
            'expectempty' => false,
        ];
        yield 'label' => [
            'modname' => 'label',
            'expectempty' => false,
        ];
        yield 'lesson' => [
            'modname' => 'lesson',
            'expectempty' => true,
        ];
        yield 'lti' => [
            'modname' => 'lti',
            'expectempty' => false,
        ];
        yield 'page' => [
            'modname' => 'page',
            'expectempty' => false,
        ];
        yield 'qbank' => [
            'modname' => 'qbank',
            'expectempty' => false,
        ];
        yield 'quiz' => [
            'modname' => 'quiz',
            'expectempty' => true,
        ];
        yield 'resource' => [
            'modname' => 'resource',
            'expectempty' => true,
        ];
        yield 'scorm' => [
            'modname' => 'scorm',
            'expectempty' => true,
        ];
        yield 'url' => [
            'modname' => 'url',
            'expectempty' => false,
        ];
        yield 'wiki' => [
            'modname' => 'wiki',
            'expectempty' => true,
        ];
        yield 'workshop' => [
            'modname' => 'workshop',
            'expectempty' => true,
        ];
    }
}
