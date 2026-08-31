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

namespace core\api\form;

use core\api\repository\api_token_repository;
use core\api\token_manager;
use core\oauth2\server\repository\scope_repository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for {@see create_token}.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(create_token::class)]
final class create_token_test extends \advanced_testcase {
    /** @var int The fixed 'now' used by every test, so expiry boundaries are deterministic. */
    private const NOW = 1786000000;

    /**
     * Build a form whose manager's clock is frozen at {@see self::NOW}.
     *
     * @return array{0: create_token, 1: token_manager}
     */
    private function get_form(): array {
        $manager = new token_manager(
            new api_token_repository(),
            $this->mock_clock_with_frozen(self::NOW),
            new scope_repository(),
        );

        return [new create_token(null, ['manager' => $manager]), $manager];
    }

    /**
     * A period resolves to exactly that many days from now.
     */
    public function test_get_expiry_time_from_preset(): void {
        $this->resetAfterTest();
        [$form] = $this->get_form();

        $expiry = $form->get_expiry_time((object) ['expirypreset' => 7]);

        $this->assertEquals(self::NOW + (7 * DAYSECS), $expiry);
    }

    /**
     * A token must carry at least one scope.
     */
    public function test_validation_requires_a_scope(): void {
        $this->resetAfterTest();
        [$form] = $this->get_form();

        $errors = $form->validation(['expirypreset' => 30], []);

        // Asserted by message rather than by element name: which row reports it is a layout
        // decision, that a scope is required is not.
        $this->assertContains(get_string('apitokennoscopes', 'error'), $errors);
    }
}
