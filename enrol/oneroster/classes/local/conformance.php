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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\container as container_interface;

/**
 * One Roster conformance test base class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class conformance {

    /** @var client_interface The client under test */
    protected $client;

    /** @var container_interface The container under test */
    protected $container;

    /**
     * Constructor for a new conformance test.
     *
     * @param   client_interface $client
     */
    public function __construct(client_interface $client) {
        global $CFG;

        $this->client = $client;
        $this->container = $client->get_container();

        require_once("{$CFG->libdir}/clilib.php");
    }

    /**
     * Run all tests.
     */
    abstract public function run_all_tests(): void;

    /**
     * Handle authentication tests.
     */
    public function authenticate(): void {
        self::print_test_title("Authentication");
        self::print_test_header("OAuth2", "Send request with valid access token");
        $this->client->authenticate();
        self::print_test_result(true);

        $tokendata = $this->client->get_access_token();
        self::print_test_data("Token", $tokendata->token);
        self::print_test_data("Expiry", $tokendata->expires);
        self::print_test_data("Scope", $tokendata->scope);
        mtrace("");

    }

    /**
     * Print a test header.
     *
     * @param   string $title The type of test being run.
     */
    public static function print_test_title(string $title): void {
        $formattedtitle = sprintf(
            "<newline><colour:blue>%s<newline>%s<newline>%s<colour:normal><newline>",
            str_repeat("=", 76),
            $title,
            str_repeat("=", 76)
        );
        fwrite(STDOUT, cli_ansi_format($formattedtitle));
    }

    /**
     * Print a test header.
     *
     * @param   string $type The type of test being run.
     * @param   string $name The name of the test being run.
     */
    public static function print_test_header(string $type, string $name): void {
        $type = sprintf("%10s", "{$type}:");
        $name = sprintf("%35s", $name);
        fwrite(STDOUT, cli_ansi_format("<colour:blue>{$type}<colour:purple>{$name}\t"));
    }

    /**
     * Print a test result.
     *
     * @param   bool $pass If false the test will exit immediately.
     * @param   string $description
     */
    public static function print_test_result(bool $pass = true, ?string $description = null): void {
        if ($pass) {
            fwrite(STDOUT, cli_ansi_format("<colour:green>Passed<colour:normal><newline>"));
        } else {
            fwrite(STDOUT, cli_ansi_format("<colour:red>Fail<colour:normal><newline>"));
            if ($description) {
                fwrite(STDOUT, cli_ansi_format("<colour:red>    {$description}<colour:normal><newline>"));
            }
            die;
        }
    }

    /**
     * Print test data.
     *
     * @param   string $field A short name to describe the field
     * @param   string $value
     */
    public static function print_test_data(string $field, string $value): void {
        $field = sprintf("%20s%15s", "{$field}:", "");
        fwrite(STDOUT, cli_ansi_format("<colour:blue>{$field}<colour:purple>{$value}<newline>"));
    }

    /**
     * Print test success.
     */
    public static function print_suite_success(): void {
        fwrite(STDOUT, cli_ansi_format("<newline><colour:green>All tests passed successfully<colour:normal><newline>"));
    }
}
