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

namespace core_auth\output;

/**
 * Unit tests for the login renderable.
 *
 * @package    core_auth
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(login::class)]
final class login_test extends \advanced_testcase {
    /**
     * set_signup_allowed(false) must clear the "don't have an account? sign up" instructions
     * text when that text was only present because it was auto-filled as a fallback for
     * sign-up being enabled site-wide.
     */
    public function test_set_signup_allowed_false_clears_signup_fallback_instructions(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->registerauth = 'email';
        $CFG->auth_instructions = '';

        $login = new login([]);

        // The constructor should have auto-filled the fallback instructions.
        $this->assertSame(get_string('logindonthaveaccount'), $login->instructions);

        $login->set_signup_allowed(false);

        $this->assertFalse($login->cansignup);
        $this->assertSame('', $login->instructions);
    }

    /**
     * set_signup_allowed(false) must not clear instructions that were explicitly configured
     * by the site admin ($CFG->auth_instructions), only the auto-generated fallback text.
     */
    public function test_set_signup_allowed_false_preserves_custom_instructions(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->registerauth = 'email';
        $CFG->auth_instructions = 'Contact the site administrator for an account.';

        $login = new login([]);

        $this->assertSame($CFG->auth_instructions, $login->instructions);

        $login->set_signup_allowed(false);

        $this->assertFalse($login->cansignup);
        $this->assertSame($CFG->auth_instructions, $login->instructions);
    }

    /**
     * set_signup_allowed(false) must not clear the unrelated "loginstepsnone" instructions
     * shown when the 'none' authentication method is enabled, even though that text is also
     * auto-generated.
     */
    public function test_set_signup_allowed_false_preserves_loginstepsnone_instructions(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->auth = 'none';
        $CFG->registerauth = 'email';
        $CFG->auth_instructions = '';

        $login = new login([]);

        $this->assertSame(get_string('loginstepsnone'), $login->instructions);

        $login->set_signup_allowed(false);

        $this->assertFalse($login->cansignup);
        $this->assertSame(get_string('loginstepsnone'), $login->instructions);
    }

    /**
     * set_signup_allowed(true) must never touch the instructions text, regardless of how it
     * was populated.
     */
    public function test_set_signup_allowed_true_leaves_instructions_untouched(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->registerauth = 'email';
        $CFG->auth_instructions = '';

        $login = new login([]);
        $fallbackinstructions = $login->instructions;

        $login->set_signup_allowed(true);

        $this->assertTrue($login->cansignup);
        $this->assertSame($fallbackinstructions, $login->instructions);
    }

    /**
     * Calling set_signup_allowed(false) twice must not error, and must be a no-op the second
     * time (the fallback flag is reset after the first clear).
     */
    public function test_set_signup_allowed_false_is_idempotent(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->registerauth = 'email';
        $CFG->auth_instructions = '';

        $login = new login([]);
        $login->set_signup_allowed(false);
        $login->set_signup_allowed(false);

        $this->assertFalse($login->cansignup);
        $this->assertSame('', $login->instructions);
    }
}
