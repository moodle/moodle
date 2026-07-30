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
 * Unit tests for the renderer_base class.
 *
 * @package    core
 * @category   test
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use core\tests\output\react_component_renderable_fixture;

/**
 * Unit tests for the renderer_base class.
 */
#[\PHPUnit\Framework\Attributes\CoversMethod(renderer_base::class, 'render')]
final class renderer_base_test extends \advanced_testcase {
    /**
     * Test that a react_component_renderable is rendered as a React component placeholder.
     */
    public function test_render_react_component_renderable(): void {
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $widget = new react_component_renderable_fixture();

        $this->assertSame(
            html_writer::react_component('@moodle/lms/core/example', (object) ['foo' => 'bar']),
            $renderer->render($widget),
        );
    }

    /**
     * Test that an explicit render_<classname> method on the renderer takes priority over
     * the react_component_renderable handling.
     */
    public function test_render_react_component_renderable_render_method_priority(): void {
        $widget = new react_component_renderable_fixture();

        $renderer = new class (new \moodle_page(), 'general') extends renderer_base {
            // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod, moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
            public function render_react_component_renderable_fixture(renderable $widget): string {
                return 'rendered by explicit method';
            }
        };

        $this->assertSame('rendered by explicit method', $renderer->render($widget));
    }
}
