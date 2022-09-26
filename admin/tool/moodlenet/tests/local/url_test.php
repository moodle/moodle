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

namespace tool_moodlenet\local;

use tool_moodlenet\local\url;

/**
 * Class tool_moodlenet_url_testcase, providing test cases for the url class.
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_test extends \advanced_testcase {

    /**
     * Test the parsing to host + path components.
     *
     * @dataProvider url_provider
     * @param string $urlstring The full URL string
     * @param string $host the expected host component of the URL.
     * @param string $path the expected path component of the URL.
     * @param bool $exception whether or not an exception is expected during construction.
     */
    public function test_parsing($urlstring, $host, $path, $exception) {
        if ($exception) {
            $this->expectException(\coding_exception::class);
            $url = new url($urlstring);
            return;
        }

        $url = new url($urlstring);
        $this->assertEquals($urlstring, $url->get_value());
        $this->assertEquals($host, $url->get_host());
        $this->assertEquals($path, $url->get_path());
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function url_provider() {
        return [
            'No path' => [
                'url' => 'https://example.moodle.net',
                'host' => 'example.moodle.net',
                'path' => null,
                'exception' => false,
            ],
            'Slash path' => [
                'url' => 'https://example.moodle.net/',
                'host' => 'example.moodle.net',
                'path' => '/',
                'exception' => false,
            ],
            'Path includes file and extension' => [
                'url' => 'https://example.moodle.net/uploads/123456789/pic.png',
                'host' => 'example.moodle.net',
                'path' => '/uploads/123456789/pic.png',
                'exception' => false,
            ],
            'Path includes file, extension and params' => [
                'url' => 'https://example.moodle.net/uploads/123456789/pic.png?option=1&option2=test',
                'host' => 'example.moodle.net',
                'path' => '/uploads/123456789/pic.png',
                'exception' => false,
            ],
            'Malformed - invalid' => [
                'url' => 'invalid',
                'host' => null,
                'path' => null,
                'exception' => true,
            ],
            'Direct, non-encoded utf8 - invalid' => [
                'url' => 'http://москва.рф/services/',
                'host' => 'москва.рф',
                'path' => '/services/',
                'exception' => true,
            ],
        ];
    }
}
