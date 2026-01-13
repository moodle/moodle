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

namespace core\check\environment;

use core\check\check;
use core\check\result;
use core\output\action_link;
use GuzzleHttp\HandlerStack;

/**
 * Checks status of router by making test requests to shimmed and API pages.
 *
 * @package    core
 * @category   check
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class router extends check {
    #[\Override]
    public function get_name(): string {
        return get_string('check_router', 'admin');
    }

    /**
     * Get the list of tests
     *
     * @return \Generator
     */
    protected function get_tests(): \Generator {
        yield [
            'url' => '/core/check/controller/test',
            'expectedcode' => 200,
            'expectedcodetitle' => "OK",
            'failfeedbackstr' => 'routerfailpage',
            'bookmarkanchor' => 'routerfailpage',
        ];
        yield [
            'url' => '/api/rest/v2/openapi.json',
            'expectedcode' => 200,
            'expectedcodetitle' => "OK",
            'failfeedbackstr' => 'routerfailapipage',
            'bookmarkanchor' => 'routerfailapipage',
        ];
        yield [
            'url' => '/not/a/valid/request',
            'expectedcode' => 404,
            'expectedcodetitle' => "Not Found",
            'failfeedbackstr' => 'routerfailmissingpage',
            'bookmarkanchor' => 'routerfailmissingpage',
        ];
        yield [
            'url' => \core\router\util::get_path_for_callable([\core\route\shim\test_controller::class, 'real_file_shim']),
            'expectedcode' => 302,
            'expectedcodetitle' => "Found",
            'failfeedbackstr' => 'routerfailshim',
            'bookmarkanchor' => 'routerfailshim',
        ];
        yield [
            'url' => \core\router\util::get_path_for_callable([\core\route\shim\test_controller::class, 'nofile_shim']),
            'expectedcode' => 302,
            'expectedcodetitle' => "Found",
            'failfeedbackstr' => 'routerfailroutershim',
            'bookmarkanchor' => 'routerfailroutershim',
        ];
    }

    #[\Override]
    public function get_result(): result {
        global $CFG;

        if (empty($CFG->routerconfigured)) {
            $result = new result(
                result::ERROR,
                get_string('routernotconfigured', 'admin'),
                get_string('routernotconfigureddetail', 'admin', [
                    'docs' => get_docs_url("Configuring_the_Router#routernotconfigured"),
                ]),
            );

            return $result;
        }

        // The router is marked as configured. Check if it actually works though.
        $client = \core\di::get(\core\http_client::class);

        $clientoptions = [
            'http_errors' => false,
            'allow_redirects' => false,
            // Override the Handler Stack to ensure that no caching is used, an that the security helper is not applied.
            'handler' => HandlerStack::create(),
        ];

        foreach ($this->get_tests() as $test) {
            if ($test['url'] instanceof \core\url) {
                $fullurl = $test['url']->out(false);
            } else {
                $fullurl = $CFG->wwwroot . $test['url'];
            }

            try {
                $response = $client->get($fullurl, $clientoptions);
                $code = $response->getStatusCode();
                $codetitle = $response->getReasonPhrase();
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $code = $e->getCode();
                $codetitle = $e->getMessage();
            }

            $resultprops = [
                'url' => $fullurl,
                'docs' => get_docs_url("Configuring_the_Router#{$test['bookmarkanchor']}"),
                'expectedstatuscode' => $test['expectedcode'],
                'expectedstatuscodetitle' => $test['expectedcodetitle'],
                'statuscode' => $code,
                'statuscodetitle' => $codetitle,
            ];
            if ($code !== $test['expectedcode']) {
                $expectedgot = get_string('routerexpectedgot', 'admin', $resultprops);
                return new result(
                    result::ERROR,
                    get_string($test['failfeedbackstr'], 'admin', $resultprops),
                    get_string('routernotconfigureddetailwithurl', 'admin', $resultprops) . " {$expectedgot}",
                    new action_link(
                        new \core\url(get_docs_url("Configuring_the_Router#{$test['bookmarkanchor']}")),
                        get_string('routerdocs', 'admin'),
                    ),
                );
            }
        }

        return new result(
            result::OK,
            get_string('routerconfiguredok', 'admin'),
        );
    }

    #[\Override]
    public function get_action_link(): ?action_link {
        return new action_link(
            new \core\url(get_docs_url('Configuring_the_Router')),
            get_string('routerdocs', 'admin'),
        );
    }
}
