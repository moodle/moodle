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
 * Class img_alt_not_empty_in_anchor_testcase
 */
class img_alt_not_empty_in_anchor_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'img_alt_not_empty_in_anchor';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Anchor tags containing a nested img tag, must not have an empty alt attribute</title>
    </head>
    <body>
    <a href="http://google.com"><img src="rex.jpg" alt=""></a>
    </body>
</html>
EOD;

    /** @var string Html pass */
    private $htmlpass = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Anchor tags containing a nested img tag, must not have an empty alt attribute</title>
    </head>
    <body>
    <a href="http://google.com"><img src="rex.jpg" alt="Picture of Rex"></a>
    </body>
</html>
EOD;
    /**
     * Test for >Anchor tags containing a nested img tag, must not have an empty alt attribute
     */
    public function test_check() {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }
}
