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
     * Create a real client_entity fixture with a name and description, so that
     * oauth2_page::describe_client() can be exercised without touching an uninitialised typed
     * property (client_entity's name/description are only ever populated via
     * create_from_record()).
     *
     * @param string $name
     * @param string $description
     */
    protected function make_oauth2_client(
        string $name = 'Example client',
        string $description = 'This application would like to access your account.',
    ): \core\oauth2\server\entity\client_entity {
        $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);

        $client = $clientmanager->create_client(
            name: $name,
            ownercontext: \core\context\system::instance(),
            granttypes: [],
            description: $description,
            isconfidential: true,
        );

        return $client;
    }

    /**
     * export_for_template() includes the OAuth2 client's identity when set_oauth2_client() has
     * been called, in the same shape as the other OAuth2 pages (continue_as_user_page,
     * confirm_scopes_page).
     */
    public function test_export_for_template_includes_oauth2_client_when_set(): void {
        global $PAGE;

        $this->resetAfterTest();

        $client = $this->make_oauth2_client('Example client', 'View your course enrolments.');

        $login = new login([]);
        $login->set_oauth2_client($client);

        $data = $login->export_for_template($PAGE->get_renderer('core'));

        $this->assertTrue($data->hasoauth2client);
        $this->assertNotNull($data->client);
        $this->assertSame('Example client', $data->client->name);
        $this->assertStringContainsString('View your course enrolments.', $data->client->description);
        $this->assertSame($client->getIdentifier(), $data->client->identifier);
    }

    /**
     * export_for_template() does not include any OAuth2 client information when
     * set_oauth2_client() has never been called, so ordinary Moodle login (login/index.php)
     * renders exactly as before.
     */
    public function test_export_for_template_excludes_oauth2_client_by_default(): void {
        global $PAGE;

        $this->resetAfterTest();

        $login = new login([]);

        $data = $login->export_for_template($PAGE->get_renderer('core'));

        $this->assertFalse($data->hasoauth2client);
        $this->assertNull($data->client);
    }

    /**
     * The OAuth2 client's identity is actually rendered onto the login screen's HTML when set,
     * reusing the same heading string and client_description partial as the other OAuth2 pages.
     */
    public function test_render_shows_oauth2_client_identity_when_set(): void {
        global $OUTPUT;

        $this->resetAfterTest();

        $client = $this->make_oauth2_client('Example client', 'View your course enrolments.');

        $login = new login([]);
        $login->set_oauth2_client($client);

        $html = $OUTPUT->render($login);

        $this->assertStringContainsString('Example client', $html);
        $this->assertStringContainsString('View your course enrolments.', $html);
    }

    /**
     * The ordinary (non-OAuth2) login screen never shows any client identity block, since
     * set_oauth2_client() was never called.
     */
    public function test_render_excludes_oauth2_client_block_by_default(): void {
        global $OUTPUT;

        $this->resetAfterTest();

        $login = new login([]);

        $html = $OUTPUT->render($login);

        $this->assertStringNotContainsString('wants to access your', $html);
    }

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
