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
 * Class table_td_should_not_merge_test
 */
final class table_td_should_not_merge_test extends all_checks {
    /** @var string Check type */
    public $checktype = 'table_td_should_not_merge';

    /** @var string Html fail 1 */
    private $htmlfail1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Rowspan exists - fail</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>1</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td rowspan="2">3</td>
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
        <title>Colspan exists - fail</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>1</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2</td>
                    <td colspan="2">3</td>
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

    /** @var string Html pass */
    private $htmlpass = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>colspan or rowspan do not exist - pass</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>1</th>
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

    /**
     * Test for rowspan and colspan
     */
    public function test_check_fail(): void {
        $results = $this->get_checker_results($this->htmlfail1);
        $this->assertNotEmpty($results);

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertNotEmpty($results);
    }

    /**
     * Test for rowspan and colspan
     */
    public function test_check_pass(): void {
        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }
}
