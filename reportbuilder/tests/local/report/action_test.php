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

declare(strict_types=1);

namespace core_reportbuilder\local\report;

use advanced_testcase;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

/**
 * Unit tests for a report action
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\report\action
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class action_test extends advanced_testcase {

    /**
     * Test adding a callback that returns true
     */
    public function test_add_callback_true(): void {
        $action = $this->create_action()
            ->add_callback(static function(stdClass $row): bool {
                return true;
            });

        $this->assertNotNull($action->get_action_link(new stdClass()));
    }

    /**
     * Test adding a callback that returns false
     */
    public function test_add_callback_false(): void {
        $action = $this->create_action()
            ->add_callback(static function(stdClass $row): bool {
                return false;
            });

        $this->assertNull($action->get_action_link(new stdClass()));
    }

    /**
     * Data provider for {@see test_action_title}
     *
     * @return array[]
     */
    public static function action_title_provider(): array {
        $title = new lang_string('yes');
        return [
            'Specified via constructor' => ['', [], $title],
            'Specified via pix icon' => [(string) $title],
            'Specified via attributes' => ['', ['title' => $title]],
            'Specified via attributes placeholder' => ['', ['title' => ':title'], null, ['title' => $title]],
        ];
    }

    /**
     * Test action title is correct
     *
     * @param string $pixiconalt
     * @param array $attributes
     * @param lang_string|null $title
     * @param array $row
     *
     * @dataProvider action_title_provider
     */
    public function test_action_title(
        string $pixiconalt,
        array $attributes = [],
        ?lang_string $title = null,
        array $row = []
    ): void {

        $action = new action(
            new moodle_url('#'),
            new pix_icon('t/edit', $pixiconalt),
            $attributes,
            false,
            $title
        );

        // Assert correct title appears inside action link, after the icon.
        $actionlink = $action->get_action_link((object) $row);
        $this->assertEquals('Yes', $actionlink->text);
    }

    /**
     * Test that action link URL parameters have placeholders replaced
     */
    public function test_get_action_link_url_parameters(): void {
        $action = $this->create_action(['id' => ':id', 'action' => 'edit']);
        $actionlink = $action->get_action_link((object) ['id' => 42]);

        // This is the action URL we expect.
        $expectedactionurl = (new moodle_url('/', ['id' => 42, 'action' => 'edit']))->out(false);
        $this->assertEquals($expectedactionurl, $actionlink->url->out(false));
    }

    /**
     * Test that action link attributes have placeholders replaced
     */
    public function test_get_action_link_attributes(): void {
        $action = $this->create_action([], ['data-id' => ':id', 'data-action' => 'edit']);
        $actionlink = $action->get_action_link((object) ['id' => 42]);

        // We expect each of these attributes to exist.
        $expectedattributes = [
            'data-id' => 42,
            'data-action' => 'edit',
        ];
        foreach ($expectedattributes as $key => $value) {
            $this->assertEquals($value, $actionlink->attributes[$key]);
        }
    }

    /**
     * Helper method to create an action instance
     *
     * @param array $urlparams
     * @param array $attributes
     * @return action
     */
    private function create_action(array $urlparams = [], array $attributes = []): action {
        return new action(
            new moodle_url('/', $urlparams),
            new pix_icon('t/edit', get_string('edit')),
            $attributes
        );
    }
}
