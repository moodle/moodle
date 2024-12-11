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
 * Class object_must_have_valid_title_test
 */
final class object_must_have_valid_title_test extends all_checks {
    /** @var string Check type */
    public $checktype = 'object_must_have_valid_title';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute does not exist within string array - fail</title>
    </head>
    <body>
        <object title="This is not a perfectly valid title" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="nbsp" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 3 */
    private $htmlpass3 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="object" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 4 */
    private $htmlpass4 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="spacer" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 5 */
    private $htmlpass5 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="image" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 6 */
    private $htmlpass6 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="img" ></object>
    </body>
</html>
EOD;

    /** @var string Html pass 7 */
    private $htmlpass7 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Title attribute must exist within string array - pass</title>
    </head>
    <body>
        <object title="photo" ></object>
    </body>
</html>
EOD;

    /**
     * Test that embed element within object element.
     */
    public function test_check_fail(): void {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->element->tagName == 'object');
    }

    /**
     * Test for embed element within object element.
     */
    public function test_check_pass(): void {
        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass3);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass4);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass5);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass6);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass7);
        $this->assertEmpty($results);
    }
}
