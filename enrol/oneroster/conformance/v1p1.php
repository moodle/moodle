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

// MUC must be disabled to ensure that all endpoints are called.
define('CACHE_DISABLE_ALL', true);

// The Conformance Test Suite runner must be run via CLI.
define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once("{$CFG->libdir}/clilib.php");

use enrol_oneroster\client_helper;

[$options, $unrecognized] = cli_get_params(
    [
        'help' => false,
        'oauth' => client_helper::OAUTH_20,
        'tokenurl' => 'https://oauth2server.imsglobal.org/oauth2server/clienttoken',
        'rooturl' => 'https://onerostervalidator.imsglobal.org:8443/oneroster-client-cts-endpoint',
        'clientid' => '',
        'secret' => '',
        'runtests' => true,
        'verbose' => true,
    ],
    [
        'h' => 'help',
        'v' => 'verbose',
    ]
);

fwrite(STDOUT, cli_ansi_format("<colour:green>"));
mtrace("============================================================================");
mtrace("= One Roster Version 1.1 Conformance Test Suite test runner");
mtrace("============================================================================");
mtrace("");

if (empty($options['help']) && (empty($options['clientid']) || empty($options['secret']))) {
    $ctstestsite = "https://onerostervalidator.imsglobal.org:8443/oneroster-client-cts-webapp";
    fwrite(STDOUT, cli_ansi_format("<colour:red>"));
    mtrace("Client credentials not provided.");
    mtrace("");
    mtrace("Please visit {$ctstestsite} in your browser to obtain test credentials");
    mtrace("");
    $options['help'] = true;
}

if (!empty($options['help'])) {
    $help = <<<EOF
One Roster Version 1.1 Conformance Test Suite Usage:

Options:
    --oauth=[oauth1|oauth2]     The version of OAuth to use for testing
    --tokenurl=[tokenurl]       The URL used to fetch a Bearer Token for OAuth 2.0 requests
    --rooturl=[rooturl]         The root of the URL serving endpoint requests
    --clientid=[client_id]      The Client ID as provided by the Conformance Test Suite
    --secret=[secret]           The Secret relating to the supplied Client ID

    -h,--help                   Print out this usage information
    -v,--verbose                Print all debugging information


Example:
php enrol/oneroster/conformance/v1p1.php --clientid=moodleclient --secret=bananas

EOF;

    fwrite(STDOUT, cli_ansi_format("<colour:green>{$help}"));
    exit(128);
}

$client = enrol_oneroster\client_helper::get_client(
    $options['oauth'],
    enrol_oneroster\client_helper::VERSION_V1P1,
    $options['tokenurl'],
    $options['rooturl'],
    $options['clientid'],
    $options['secret']
);

if ($options['verbose']) {
    $client->set_trace(new text_progress_trace());
}

$conformancesuite = new enrol_oneroster\local\v1p1\conformance($client);

$conformancesuite::print_test_data("Token server", $options['tokenurl']);
$conformancesuite::print_test_data("Client Id", $options['clientid']);
$conformancesuite::print_test_data("Client Secret", $options['secret']);

if ($options['oauth'] === client_helper::OAUTH_20) {
    $conformancesuite->authenticate();
}
$conformancesuite->run_all_tests();
exit(0);
