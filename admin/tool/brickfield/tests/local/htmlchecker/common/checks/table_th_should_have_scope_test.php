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
 * Class table_th_should_have_scope_test
 */
class table_th_should_have_scope_test extends all_checks {
    /** @var string Check type */
    public $checktype = 'table_th_should_have_scope';

    /** @var string Html fail 1 */
    private $htmlfail1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Scope exists whilst specifying "col" - fail</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">1</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td>3</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>5</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
EOD;

    /** @var string Html fail 2 */
    private $htmlfail2 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Scope exists whilst specifying "row" - fail</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td>3</td>
                    <th>4</th>
                </tr>
                <tr>
                    <td>4</td>
                    <td>5</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Scope exists whilst specifying something that isn't "col" or "row" - pass</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td>3</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>5</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
EOD;

    /** @var string Html pass 2 */
    private $htmlpass2 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Scope is not specified - pass</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th scope="col">1</th>
                    <th scope="col">2</th>
                    <th scope="col">3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td>3</td>
                    <th scope="row">4</th>
                </tr>
                <tr>
                    <td>4</td>
                    <td>5</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
EOD;
    /**
     * Test that th has scope that is equal to col or row
     */
    public function test_check_fail() {
        $results = $this->get_checker_results($this->htmlfail1);
        $this->assertEquals(2, count($results));
        $this->assertTrue($results[0]->element->tagName == 'th');
        $this->assertTrue($results[1]->element->tagName == 'th');

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertEquals(1, count($results));
        $this->assertTrue($results[0]->element->tagName == 'th');
    }

    /**
     * Test that th has scope but != col || row. Test that th has no scope
     */
    public function test_check_pass() {
        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass2);
        $this->assertEmpty($results);
    }
}
