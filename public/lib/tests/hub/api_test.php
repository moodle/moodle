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

namespace core\hub;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for {@see api}.
 *
 * @package    core
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(api::class)]
final class api_test extends \advanced_testcase {
    /**
     * Clear the static registration cache so each test/tearDown sees the current database state.
     *
     * \core\hub\registration caches the registration record in a static property that
     * resetAfterTest() does not clear, so without this a record from an earlier test could leak
     * into whichever test class runs next in the same process.
     */
    private function reset_registration_cache(): void {
        $property = new \ReflectionProperty(registration::class, 'registration');
        $property->setValue(null, null);
    }

    protected function tearDown(): void {
        // Registration caches the current registration record in a static property that
        // resetAfterTest() does not clear, so a confirmed registration created here would
        // otherwise leak into whichever test class runs next in the same process.
        $this->reset_registration_cache();
        parent::tearDown();
    }

    /**
     * Create an unconfirmed, then confirmed, registration so api::update_registration() has a
     * token to send.
     *
     * @return void
     */
    private function register_confirmed_site(): void {
        global $DB;

        $DB->insert_record('registration_hubs', [
            'token' => 'sometoken',
            'hubname' => 'moodle',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'sometoken',
            'timemodified' => time(),
        ]);
        $this->reset_registration_cache();
    }

    /**
     * Registers the site locally so {@see registration::get_secret()} returns a known value.
     *
     * @param string $secret
     */
    private function register_site(string $secret): void {
        global $DB;

        $this->reset_registration_cache();
        $DB->insert_record('registration_hubs', [
            'token' => 'sometoken',
            'hubname' => 'moodle',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => $secret,
            'timemodified' => time(),
        ]);
    }

    /**
     * An empty response body must be treated as a failed request, not a quiet success. This is
     * what a blocked or unreachable hub URL, or a disabled hub, looks like: no exception key is
     * present to detect, because there is no decodable body at all.
     */
    public function test_call_rest_throws_on_empty_response_body(): void {
        $this->resetAfterTest();
        $this->register_confirmed_site();

        \curl::mock_response('');

        $this->expectException(\moodle_exception::class);
        api::update_registration(['url' => 'https://example.com']);
    }

    /**
     * A response body that is not valid JSON must be treated as a failed request rather than
     * silently discarded.
     */
    public function test_call_rest_throws_on_non_json_response_body(): void {
        $this->resetAfterTest();
        $this->register_confirmed_site();

        \curl::mock_response('<html>not json</html>');

        $this->expectException(\moodle_exception::class);
        api::update_registration(['url' => 'https://example.com']);
    }

    /**
     * The hub's update, is-registered and unregister functions return a JSON scalar (true or 1),
     * which must be accepted as a successful response rather than treated as an unreachable hub.
     */
    public function test_call_rest_succeeds_on_scalar_json_response(): void {
        $this->resetAfterTest();
        $this->register_confirmed_site();

        \curl::mock_response(json_encode(true));

        // No exception means the call was accepted as successful.
        api::update_registration(['url' => 'https://example.com']);
        $this->assertTrue(true);
    }

    /**
     * A well-formed, decodable JSON response with no exception key must still succeed.
     */
    public function test_call_rest_succeeds_on_valid_json_response(): void {
        $this->resetAfterTest();
        $this->register_confirmed_site();

        \curl::mock_response(json_encode(['status' => 'ok']));

        // No exception means the call was accepted as successful.
        api::update_registration(['url' => 'https://example.com']);
        $this->assertTrue(true);
    }

    /**
     * Calls the protected api::sign_registration_update() method.
     *
     * @param string $siteurl
     * @return array
     */
    private function sign_registration_update(string $siteurl): array {
        $method = new \ReflectionMethod(api::class, 'sign_registration_update');
        $method->setAccessible(true);
        return $method->invoke(null, $siteurl);
    }

    /**
     * A registered site with a known secret signs using HMAC-SHA256 keyed on md5(secret),
     * over 'siteurl|timestamp', matching the contract agreed with the hub (MDLSITE-8480).
     */
    public function test_sign_registration_update_returns_valid_signature(): void {
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen(1700000000);
        $this->register_site('thesharedsecret');

        $result = $this->sign_registration_update('https://example.com');

        $this->assertSame($clock->time(), $result['timestamp']);
        $expected = hash_hmac('sha256', 'https://example.com|' . $clock->time(), md5('thesharedsecret'));
        $this->assertSame($expected, $result['signature']);
    }

    /**
     * A site that is not registered has no secret to sign with, and must degrade to sending
     * no signature at all rather than failing.
     */
    public function test_sign_registration_update_without_registration_returns_empty(): void {
        $this->resetAfterTest();

        $result = $this->sign_registration_update('https://example.com');

        $this->assertSame([], $result);
    }

    /**
     * An empty site URL cannot be signed meaningfully and must degrade to no signature.
     */
    public function test_sign_registration_update_without_url_returns_empty(): void {
        $this->resetAfterTest();

        $this->register_site('thesharedsecret');

        $result = $this->sign_registration_update('');

        $this->assertSame([], $result);
    }

    /**
     * Different site secrets must produce different signatures for the same URL and timestamp,
     * otherwise the signature would prove nothing about which site sent the update.
     */
    public function test_sign_registration_update_signature_depends_on_secret(): void {
        $this->resetAfterTest();

        $this->mock_clock_with_frozen(1700000000);
        $this->register_site('secretone');
        $first = $this->sign_registration_update('https://example.com');

        $this->reset_registration_cache();
        global $DB;
        $DB->delete_records('registration_hubs', ['huburl' => HUB_MOODLEORGHUBURL]);
        $this->register_site('secrettwo');
        $second = $this->sign_registration_update('https://example.com');

        $this->assertNotSame($first['signature'], $second['signature']);
    }

    /**
     * An unexpected error while signing (for example, a broken clock service) must degrade to
     * sending no signature, exactly like the anticipated empty-input cases, and must not
     * propagate the exception.
     */
    public function test_sign_registration_update_degrades_on_unexpected_error(): void {
        $this->resetAfterTest();

        $this->register_site('thesharedsecret');

        \core\di::set(\core\clock::class, new class implements \core\clock {
            /**
             * Always throws, to simulate an unexpected error inside the signing code.
             */
            public function time(): int {
                throw new \RuntimeException('clock is unavailable');
            }

            /**
             * Always throws, to simulate an unexpected error inside the signing code.
             */
            public function now(): \DateTimeImmutable {
                throw new \RuntimeException('clock is unavailable');
            }
        });

        $result = $this->sign_registration_update('https://example.com');

        $this->assertSame([], $result);
        $this->assertDebuggingCalled('Failed to sign registration update: clock is unavailable');
    }
}
