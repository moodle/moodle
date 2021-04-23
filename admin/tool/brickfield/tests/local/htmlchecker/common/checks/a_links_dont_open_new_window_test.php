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
 * tool_brickfield check test.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield\local\htmlchecker\common\checks;

defined('MOODLE_INTERNAL') || die();

require_once('all_checks.php');

/**
 * Class a_links_dont_open_new_window_testcase
 */
class a_links_dont_open_new_window_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'a_links_dont_open_new_window';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>A links must not open a new tab or window</title>
    </head>
    <body>
    <a href="www.google.com" target="_blank">This is google</a>
    </body>
    </html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>A links must not open a new tab or window</title>
    </head>
    <body>
    <a href="www.google.com" target="_self">This is google</a>
    </body>
    </html>
EOD;

    /** @var string Html pass 2 */
    private $htmlpass2 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>A links must not open a new tab or window</title>
    </head>
    <body>
    <a href="www.youtube.com" target="_parent">This is youtube</a>
    </body>
    </html>
EOD;

    /** @var string Html pass 3 */
    private $htmlpass3 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>A links must not open a new tab or window</title>
    </head>
    <body>
    <a href="www.youtube.com" target="_top">This is youtube</a>
    </body>
    </html>
EOD;

    /**
     * Test for links opening a new tab or window
     */
    public function test_check() {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->element->tagName == 'a');

        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass2);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass3);
        $this->assertEmpty($results);
    }
}
