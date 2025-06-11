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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\messenger\message\body_substitution_code_parser;

class block_quickmail_body_substitution_code_parser_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_gets_tokens_from_message_with_no_codes() {
        $message = 'This is a simple message with NO codes! How easy should this be?';

        $codes = body_substitution_code_parser::get_codes($message);

        $this->assertIsArray($codes);
        $this->assertCount(0, $codes);
    }

    public function test_gets_tokens_from_message_with_codes() {
        $message = '[:hey:] this message has [:some:] [:codes:].
                    There should [:be:] four in fact. No [:two:][:more:] actually so six.';

        $codes = body_substitution_code_parser::get_codes($message);

        $this->assertIsArray($codes);
        $this->assertCount(6, $codes);
        $this->assertContains('hey', $codes);
        $this->assertContains('some', $codes);
        $this->assertContains('codes', $codes);
        $this->assertContains('be', $codes);
        $this->assertContains('two', $codes);
        $this->assertContains('more', $codes);
    }
}
