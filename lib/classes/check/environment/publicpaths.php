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
 * Check the presence of public paths via curl.
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\environment;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Check the public access of various paths.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class publicpaths extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_publicpaths_name', 'report_security');
    }

    /**
     * Returns a list of test urls and metadata.
     */
    public function get_pathsets() {
        global $CFG;

        // The intention here is that each pattern is a simple regex such that
        // in future perhaps the various webserver config could be generated as more
        // pattens are added to these checks.
        return [
            [
                'pattern'   => '/vendor/',
                '404'       => [
                    'vendor/',
                    'vendor/bin/behat',
                ],
                'details'   => get_string('check_vendordir_details', 'report_security', ['path' => $CFG->dirroot.'/vendor']),
                'summary'   => get_string('check_vendordir_info', 'report_security'),
            ],
            [
                'pattern'   => '/node_modules/',
                '404'       => [
                    'node_modules/',
                    'node_modules/cli/cli.js',
                ],
                'summary'   => get_string('check_nodemodules_info', 'report_security'),
                'details'   => get_string('check_nodemodules_details', 'report_security',
                        ['path' => $CFG->dirroot . '/node_modules']),
            ],
            [
                'pattern'   => '^\..*',
                '404'       => [
                    '.git/',
                    '.git/HEAD',
                    '.github/FUNDING.yml',
                    '.stylelintrc',
                    '.upgradenotes/',
                ],
            ],
            [
                'pattern'   => 'composer.json',
                '404'       => [
                    'composer.json',
                ],
            ],
            [
                'pattern'   => '.lock',
                '404'       => [
                    'composer.lock',
                ],
            ],
            [
                'pattern'   => 'environment.xml',
                '404'       => [
                    'admin/environment.xml',
                ],
            ],
            [
                'pattern'   => '',
                '404'       => [
                    'doesnotexist', // Just to make sure that real 404s are still 404s.
                ],
                'summary'   => '',
            ],
            [
                'pattern'   => '',
                '404'       => [
                    'lib/classes/',
                ],
                'summary'   => get_string('check_dirindex_info', 'report_security'),
            ],
            [
                'pattern'   => 'db/install.xml',
                '404'       => [
                    'lib/db/install.xml',
                    'mod/assign/db/install.xml',
                ],
            ],
            [
                'pattern'   => 'readme.txt',
                '404'       => [
                    'lib/scssphp/readme_moodle.txt',
                    'mod/resource/readme.txt',
                ],
            ],
            [
                'pattern'   => 'README',
                '404'       => [
                    'mod/README.txt',
                    'mod/book/README.md',
                ],
            ],
            [
                'pattern'   => '\/(upgrade\.txt|UPGRADING\.md|UPGRADING\-CURRENT\.md)',
                '404'       => [
                    'auth/manual/upgrade.txt',
                    'lib/upgrade.txt',
                    'UPGRADING.md',
                    'UPGRADING-CURRENT.md',
                    'reportbuilder/UPGRADING.md',
                ],
                'summary' => get_string('check_upgradefile_info', 'report_security'),
            ],
            [
                'pattern'   => 'phpunit.xml',
                '404'       => ['phpunit.xml.dist'],
            ],
            [
                'pattern'   => '/fixtures/',
                '404'       => [
                    'privacy/tests/fixtures/logo.png',
                    'enrol/lti/tests/fixtures/input.xml',
                ],
            ],
            [
                'pattern'   => '/behat/',
                '404'       => ['blog/tests/behat/delete.feature'],
            ],
        ];
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG, $OUTPUT;

        $status = result::OK;
        $details = '';
        $summary = get_string('check_publicpaths_ok', 'report_security');
        $errors = [];

        $c = new \curl();
        $paths = $this->get_pathsets();

        $table = new \html_table();
        $table->align = ['center', 'right', 'left'];
        $table->size = ['1%', '1%', '1%', '1%', '1%', '99%'];
        $table->head = [
            get_string('status'),
            get_string('checkexpected'),
            get_string('checkactual'),
            get_string('url'),
            get_string('category'),
            get_string('details'),
        ];
        $table->attributes['class'] = 'flexible generaltable generalbox table-sm';
        $table->data = [];

        // Used to track duplicated errors.
        $lastdetail = '-';

        $curl = new \curl();
        $requests = [];

        // Build up a list of all url so we can load them in parallel.
        foreach ($paths as $path) {
            foreach (['200', '404'] as $expected) {
                if (!isset($path[$expected])) {
                    continue;
                }
                foreach ($path[$expected] as $test) {
                    $requests[] = [
                        'nobody'    => true,
                        'header'    => 1,
                        'url'       => $CFG->wwwroot . '/' . $test,
                        'returntransfer' => true,
                    ];
                }
            }
        }

        $headers = $curl->download($requests);

        foreach ($paths as $path) {
            foreach (['200', '404'] as $expected) {
                if (!isset($path[$expected])) {
                    continue;
                }
                foreach ($path[$expected] as $test) {
                    $rowsummary = '';
                    $rowdetail = '';

                    $url = $CFG->wwwroot . '/' . $test;

                    // Parse the HTTP header to get the 200 / 404 code.
                    $header = array_shift($headers);
                    $actual = strtok($header, "\n");
                    $actual = strtok($actual, " ");
                    $actual = strtok(" ");

                    if ($actual != $expected) {
                        if (isset($path['summary'])) {
                            $rowsummary = $path['summary'];
                        } else {
                            $rowsummary = get_string('check_publicpaths_generic',
                                'report_security', $path['pattern']);
                        }

                        // Special case where a 404 is ideal but a 403 is ok too.
                        if ($actual == 403) {
                            $result = new result(result::INFO, '', '');
                            $rowsummary .= get_string('check_publicpaths_403', 'report_security');
                        } else {
                            $result = new result(result::ERROR, '', '');
                            $status = result::ERROR;
                            $summary = get_string('check_publicpaths_warning', 'report_security');
                        }

                        $rowdetail = isset($path['details']) ? $path['details'] : $rowsummary;

                        if (empty($errors[$path['pattern']])) {
                            $summary .= '<li>' . $rowsummary . '</li>';
                            $errors[$path['pattern']] = 1;
                        }

                    } else {
                        $result = new result(result::OK, '', '');
                    }

                    $table->data[] = [
                        $OUTPUT->check_result($result),
                        $expected,
                        $actual,
                        $OUTPUT->action_link($url, $test, null, ['target' => '_blank']),
                        "<pre>{$path['pattern']}</pre>",
                    ];

                    // Merge duplicate details to display a nicer table.
                    if ($rowdetail == $lastdetail) {
                        $duplicates++;
                    } else {
                        $duplicates = 1;
                    }
                    $detailcell = new \html_table_cell($rowdetail);
                    $detailcell->rowspan = $duplicates;
                    $rows = count($table->data);
                    $table->data[$rows - $duplicates][5] = $detailcell;
                    $lastdetail = $rowdetail;
                }
            }
        }

        $details .= \html_writer::table($table);

        return new result($status, $summary, $details);
    }

    /**
     * Link to the dev docs for more info.
     *
     * @return \action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url(\get_docs_url('Installing_Moodle#Set_up_your_server')),
            get_string('moodledocs'));
    }

}

