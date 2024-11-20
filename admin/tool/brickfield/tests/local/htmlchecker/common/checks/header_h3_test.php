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
 * Class header_h3_testcase
 */
class header_h3_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'header_h3';

    /** @var string Html fail 1 */
    private $htmlfail1 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h5>This is h5</h5>
    </body>
    </html>
EOD;

    /** @var string Html fail 2 */
    private $htmlfail2 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h6>This is h6</h6>
    </body>
    </html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h1>This is h1</h1>
    </body>
    </html>
EOD;

    /** @var string Html pass 2 */
    private $htmlpass2 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h2>This is h2</h2>
    </body>
    </html>
EOD;

    /** @var string Html pass 3 */
    private $htmlpass3 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h3>This is h3</h3>
    </body>
    </html>
EOD;

    /** @var string Html pass 4 */
    private $htmlpass4 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>The header following an h3 must be h1, h2, h3 or h4.</title>
    </head>
    <body>
    <h3>This is h3</h3>
    <h4>This is h4</h4>
    </body>
    </html>
EOD;

    /**
     * Test the header following an h3 must be h1, h2, h3 or h4.
     */
    public function test_check(): void {
        $results = $this->get_checker_results($this->htmlfail1);
        $this->assertTrue($results[0]->element->tagName == 'h5');

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertTrue($results[0]->element->tagName == 'h6');

        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass3);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass3);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass4);
        $this->assertEmpty($results);
    }
}
