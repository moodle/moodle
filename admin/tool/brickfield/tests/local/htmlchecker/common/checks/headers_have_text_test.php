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
 * Class headers_have_text_testcase
 */
final class headers_have_text_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'headers_have_text';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
        <head>
            <title>Header has text - Fail</title>
        </head>
        <body>
        <h1>This is a correct header</h1>
        <p>Intrinsicly actualize web-enabled users and cross functional growth strategies. Monotonectally simplify B2B opportunities
        vis-a-vis top-line processes.</p>
        <h2></h2>
        <p>Globally synergize ethical process improvements before go forward technology. Synergistically seize backward-compatible
        quality vectors through magnetic sources. Distinctively reintermediate virtual "outside the box" thinking without market
        positioning supply chains.</p>
        </body>
    </html>
EOD;

    /** @var string Html pass */
    private $htmlpass = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
        <head>
            <title>Header has text - Fail</title>
        </head>
        <body>
        <h1>This is a correct header</h1>
        <p>Intrinsicly actualize web-enabled users and cross functional growth strategies. Monotonectally simplify B2B opportunities
         vis-a-vis top-line processes.</p>
        <h2>This header is also OK.</h2>
        <p>Globally synergize ethical process improvements before go forward technology. Synergistically seize backward-compatible
        quality vectors through magnetic sources. Distinctively reintermediate virtual "outside the box" thinking without market
        positioning supply chains.</p>
        </body>
    </html>
EOD;

    /**
     * Test for headers not containing text
     */
    public function test_check(): void {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->element->tagName == 'h2');

        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }
}
