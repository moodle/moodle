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
 * Class img_alt_not_placeholder_testcase
 */
class img_alt_not_place_holder_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'img_alt_not_place_holder';

    /** @var string Html fail 1 */
    private $htmlfail1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="nbsp">
    </body>
</html>
EOD;

    /** @var string Html fail 2 */
    private $htmlfail2 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="&nbsp;">
    </body>
</html>
EOD;

    /** @var string Html fail 3 */
    private $htmlfail3 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="spacer">
    </body>
</html>
EOD;

    /** @var string Html fail 4 */
    private $htmlfail4 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="image">
    </body>
</html>
EOD;

    /** @var string Html fail 5 */
    private $htmlfail5 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="img">
    </body>
</html>
EOD;

    /** @var string Html fail 6 */
    private $htmlfail6 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes match the specified string array in imgAltNotPlaceHolder- fail</title>
    </head>
    <body>
    <img src="rex.jpg" alt="photo">
    </body>
</html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Image alt attributes must not match the string array specified in imgAltNotPlaceHolder - pass</title>
    </head>
    <body>
    <img src="rex.jpg" alt="A mountian with a sunset">
    </body>
</html>
EOD;

    /**
     * Notes:  espacio & imagen & foto within the 'es' lang_string, all pass - they shouldn't.
     */

    /**
     * Test for the each en string specified in $strings - line 43 in imgAltNotPlaceHolder.
     */
    public function test_failcheck() {
        $results = $this->get_checker_results($this->htmlfail1);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlfail3);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlfail4);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlfail5);
        $this->assertTrue($results[0]->element->tagName == 'img');

        $results = $this->get_checker_results($this->htmlfail6);
        $this->assertTrue($results[0]->element->tagName == 'img');
    }

    /**
     * Test with alt that was not specified in the $strings array.
     */
    public function test_passcheck1() {
        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);
    }
}
