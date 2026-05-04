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

namespace tool_messageinbound;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use tool_messageinbound\privacy\provider;

/**
 * Manager testcase class.
 *
 * @package    tool_messageinbound
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(manager::class)]
final class manager_test extends provider_testcase {
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest();

        // Pretend the system is enabled.
        $CFG->messageinbound_enabled = true;
        $CFG->messageinbound_mailbox = 'mailbox';
        $CFG->messageinbound_domain = 'example.com';
    }

    public function test_tidy_old_verification_failures(): void {
        global $DB;

        $now = time();
        $stale = $now - DAYSECS - 1;    // Make a second older because PHP Unit is too damn fast!!

        $this->create_messagelist(['timecreated' => $now]);
        $this->create_messagelist(['timecreated' => $now - HOURSECS]);
        $this->create_messagelist(['timecreated' => $stale]);
        $this->create_messagelist(['timecreated' => $stale - HOURSECS]);
        $this->create_messagelist(['timecreated' => $stale - YEARSECS]);

        $this->assertEquals(5, $DB->count_records('messageinbound_messagelist', []));
        $this->assertEquals(3, $DB->count_records_select('messageinbound_messagelist', 'timecreated < :t', ['t' => $stale + 1]));

        $manager = new \tool_messageinbound\manager();
        $manager->tidy_old_verification_failures();

        $this->assertEquals(2, $DB->count_records('messageinbound_messagelist', []));
        $this->assertEquals(0, $DB->count_records_select('messageinbound_messagelist', 'timecreated < :t', ['t' => $stale + 1]));
    }

    /**
     * Create a message to validate.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_messagelist(array $params) {
        global $DB, $USER;
        $record = (object) array_merge([
            'messageid' => 'abc',
            'userid' => $USER->id,
            'address' => 'text@example.com',
            'timecreated' => time(),
        ], $params);
        $record->id = $DB->insert_record('messageinbound_messagelist', $record);
        return $record;
    }

    /**
     * Build a manager instance with a stub IMAP client that returns the supplied bodypart
     * bytes for any fetch() call. Lets the private process_message_data_body_part() be
     * exercised in isolation without touching a real IMAP server.
     *
     * @param string $bodypartbytes Raw bytes the stub returns from $messagedata->bodypart[$section].
     * @param string $section       Section key used in the returned bodypart array.
     * @return manager
     */
    private function manager_with_stub_client(string $bodypartbytes, string $section = '1'): manager {
        $stub = new class ($bodypartbytes, $section) {
            /** @var string Mailbox name. Read by manager::get_mailbox() via property access. */
            public string $selected = 'INBOX';

            /**
             * Constructor.
             *
             * @param string $bytes Raw bytes returned by fetch().
             * @param string $section Section key under which the bytes are returned.
             */
            public function __construct(
                /** @var string Raw bytes returned by fetch(). */
                private string $bytes,
                /** @var string Section key under which the bytes are returned. */
                private string $section,
            ) {
            }

            /**
             * Stub fetch() matching the real IMAP client's signature.
             *
             * @param mixed ...$args Unused; present to match the real client's signature.
             * @return array
             */
            public function fetch(...$args): array {
                $msg = new \stdClass();
                $msg->bodypart = [$this->section => $this->bytes];
                return [$msg];
            }
        };

        $manager = new manager();
        // Since PHP 8.1, all Reflection*::setAccessible() calls are no-ops and are
        // deprecation-warned as of PHP 8.5, so we skip them and write directly.
        (new \ReflectionProperty($manager, 'client'))->setValue($manager, $stub);
        return $manager;
    }

    /**
     * Invoke the private process_message_data_body_part() while preserving the four
     * by-reference output parameters. ReflectionMethod::invokeArgs() does not preserve
     * references in PHP 8, so we use Closure::bind to drop into the class scope and call
     * the private method directly.
     *
     * @param manager $manager        The manager instance (with stub client injected).
     * @param array   $partstructure  The IMAP body-structure tuple under test.
     * @param string  $section        Section identifier for the bodypart fetch.
     * @param string  $contentplain   By-reference output: accumulated text/plain content.
     * @param string  $contenthtml    By-reference output: accumulated text/html content.
     * @param array   $attachments    By-reference output: accumulated attachments by disposition.
     * @param array   $parameters     By-reference input/output: parameters accumulated across parts.
     */
    private function invoke_process_part(
        manager $manager,
        array $partstructure,
        string $section,
        string &$contentplain,
        string &$contenthtml,
        array &$attachments,
        array &$parameters,
    ): void {
        $caller = function (
            array $ps,
            string $sec,
            string &$cp,
            string &$ch,
            array &$at,
            array &$pa,
        ): void {
            $this->process_message_data_body_part(
                messageuid: 1,
                partstructure: $ps,
                section: $sec,
                contentplain: $cp,
                contenthtml: $ch,
                attachments: $at,
                parameters: $pa,
            );
        };
        $bound = \Closure::bind($caller, $manager, manager::class);
        $bound($partstructure, $section, $contentplain, $contenthtml, $attachments, $parameters);
    }

    /**
     * Regression test for MDL-85256: when the upstream IMAP body-structure parser leaves
     * $partstructure[1] (subtype), [2] (parameters) and [5] (encoding) unset, the function
     * must degrade gracefully instead of fataling on strtoupper(null) or on the typed
     * `array $attributes` argument of process_message_body_structure_parameters().
     */
    public function test_process_part_handles_missing_partstructure_slots(): void {
        $manager = $this->manager_with_stub_client('Hello world');

        // Slots [1] (subtype), [2] (parameters) and [5] (encoding) are NIL -- the failure
        // mode reported in MDL-85256. The other slots are present so the test exercises
        // the three named regressions in isolation without tripping unrelated warnings
        // on $partstructure[6] (size) and [8] (disposition).
        $partstructure = [null, null, null, null, null, null, 0, null, null];
        $contentplain = '';
        $contenthtml = '';
        $attachments = [];
        $parameters = [];

        $this->invoke_process_part(
            $manager,
            $partstructure,
            '1',
            $contentplain,
            $contenthtml,
            $attachments,
            $parameters,
        );

        // With unknown subtype the PLAIN/HTML branches must skip; nothing should be added.
        $this->assertSame('', $contentplain);
        $this->assertSame('', $contenthtml);
        $this->assertSame([], $attachments);
    }

    /**
     * Regression test for MDL-85256: a NIL or otherwise non-array attributes slot must not
     * fatal at process_message_body_structure_parameters()'s typed `array $attributes`
     * parameter. The part should still be processed using the existing fallbacks.
     */
    public function test_process_part_handles_non_array_attributes_slot(): void {
        $manager = $this->manager_with_stub_client('Hello world');

        // Valid subtype/encoding/bytes, but $partstructure[2] is NIL (null).
        $partstructure = ['text', 'PLAIN', null, '', '', '7BIT', 11, '', null];
        $contentplain = '';
        $contenthtml = '';
        $attachments = [];
        $parameters = [];

        $this->invoke_process_part(
            $manager,
            $partstructure,
            '1',
            $contentplain,
            $contenthtml,
            $attachments,
            $parameters,
        );

        $this->assertSame('Hello world', $contentplain);
    }

    /**
     * Regression test for MDL-85256: when a text/* part omits the CHARSET attribute,
     * the body must be decoded as UTF-8 (matching Moodle's outbound default in
     * email_to_user()). Decoding as us-ascii would strip or replace high-byte sequences
     * via core_text::convert().
     */
    public function test_process_part_defaults_missing_charset_to_utf8(): void {
        // Café encoded as raw UTF-8 bytes (the 'é' is 0xC3 0xA9, invalid us-ascii).
        $manager = $this->manager_with_stub_client('Café');

        // Attributes list contains NAME but no CHARSET.
        $partstructure = ['text', 'PLAIN', ['NAME', 'note.txt'], '', '', '7BIT', 6, '', null];
        $contentplain = '';
        $contenthtml = '';
        $attachments = [];
        $parameters = [];

        $this->invoke_process_part(
            $manager,
            $partstructure,
            '1',
            $contentplain,
            $contenthtml,
            $attachments,
            $parameters,
        );

        $this->assertSame('Café', $contentplain);
    }

    /**
     * Regression test for MDL-85256: a malformed part processed after a well-formed
     * attachment part must not inherit the prior part's NAME / FILENAME and re-run
     * the attachment branch with its own body content -- which would create a
     * spurious duplicate attachment using the earlier filename but this part's data.
     * Per-part semantics: each part contributes only its own declared attributes,
     * and attachments produced by earlier parts are already stored in $attachments
     * by the time the next part runs.
     */
    public function test_process_part_does_not_leak_parameters_into_malformed_part(): void {
        $manager = $this->manager_with_stub_client('leaked body content');

        // Malformed part: slots [1] / [2] / [5] are NIL (the MDL-85256 scenario).
        $partstructure = [null, null, null, null, null, null, 0, null, null];
        $contentplain = '';
        $contenthtml = '';
        $attachments = ['inline' => [], 'attachment' => []];
        // Simulate parameters left over from an earlier attachment part.
        $parameters = ['NAME' => 'report.pdf', 'CHARSET' => 'iso-8859-1'];

        $this->invoke_process_part(
            $manager,
            $partstructure,
            '1',
            $contentplain,
            $contenthtml,
            $attachments,
            $parameters,
        );

        // The stale NAME must not produce a spurious attachment.
        $this->assertSame(['inline' => [], 'attachment' => []], $attachments);
        // Parameters are per-part: a part declaring no attributes contributes none.
        $this->assertSame([], $parameters);
        // Unknown subtype: the PLAIN/HTML branches must not run.
        $this->assertSame('', $contentplain);
        $this->assertSame('', $contenthtml);
    }
}
