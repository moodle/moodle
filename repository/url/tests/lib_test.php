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
 * Unit tests for the URL repository.
 *
 * @package   repository_url
 * @copyright 2014 John Okely
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_url;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/repository/url/lib.php');


/**
 * URL repository test case.
 *
 * @copyright 2014 John Okely
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Check that the url escaper performs as expected
     */
    public function test_escape_url() {
        $this->resetAfterTest();

        $repoid = $this->getDataGenerator()->create_repository('url')->id;

        $conversions = array(
                'http://example.com/test_file.png' => 'http://example.com/test_file.png',
                'http://example.com/test%20file.png' => 'http://example.com/test%20file.png',
                'http://example.com/test file.png' => 'http://example.com/test%20file.png',
                'http://example.com/test file.png?query=string+test&more=string+tests' =>
                    'http://example.com/test%20file.png?query=string+test&more=string+tests',
                'http://example.com/?tag=<p>' => 'http://example.com/?tag=%3Cp%3E',
                'http://example.com/"quoted".txt' => 'http://example.com/%22quoted%22.txt',
                'http://example.com/\'quoted\'.txt' => 'http://example.com/%27quoted%27.txt',
                '' => ''
            );

        foreach ($conversions as $input => $expected) {
            // The constructor uses a optional_param, so we need to hack $_GET.
            $_GET['file'] = $input;
            $repository = new \repository_url($repoid);
            $this->assertSame($expected, $repository->file_url);
        }

        $exceptions = array(
                '%' => true,
                '!' => true,
                '!https://download.moodle.org/unittest/test.jpg' => true,
                'https://download.moodle.org/unittest/test.jpg' => false
            );

        foreach ($exceptions as $input => $expected) {
            $caughtexception = false;
            try {
                // The constructor uses a optional_param, so we need to hack $_GET.
                $_GET['file'] = $input;
                $repository = new \repository_url($repoid);
                $repository->get_listing();
            } catch (\repository_exception $e) {
                if ($e->errorcode == 'validfiletype') {
                    $caughtexception = true;
                }
            }
            $this->assertSame($expected, $caughtexception);
        }

        unset($_GET['file']);
    }

}
