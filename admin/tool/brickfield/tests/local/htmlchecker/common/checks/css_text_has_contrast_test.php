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
 * Class test_css_text_has_contrast test
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield\local\htmlchecker\common\checks;

defined('MOODLE_INTERNAL') || die();

require_once('all_checks.php');

/**
 * Class test_css_text_has_contrast_test
 */
class css_text_has_contrast_test extends all_checks {
    /** @var string The check type. */
    protected $checktype = 'css_text_has_contrast';

    /** @var string HTML that should get flagged. */
    private $htmlfail1 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000; font-weight: bold;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $htmlfail2 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000; font-size: 18px;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $htmlfail3 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000; font-size: 18%;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $htmlfail4 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000; font-size: 18em;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $htmlfail5 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000; font-size: 18ex;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $htmlfail6 = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#000000;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should not get flagged. */
    private $htmlpass = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:#333333; background-color:#ffffff;">This is contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML that should get flagged. */
    private $namecolours = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color: red; background-color: blue;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML with invalid colour names. */
    private $invalidcolours = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color: grog; background-color: numpi;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML with invalid colour numeric values. */
    private $invalidvalue = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color: 10000500; background-color: -10234;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /** @var string HTML with empty colour values. */
    private $emptyvalue = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>OAC Testfile - Check #6 - Positive</title>
    </head>
    <body>
    <p style="color:; background-color:;">This is not contrasty enough.</p>
    </body>
    </html>
EOD;

    /**
     * Test for the area assign intro
     */
    public function test_check() {
        $results = $this->get_checker_results($this->htmlfail1);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlfail3);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlfail4);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlfail5);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlfail6);
        $this->assertTrue($results[0]->element->tagName == 'p');

        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }

    /**
     * Test with valid colour names.
     */
    public function test_check_for_namedcolours() {
        $results = $this->get_checker_results($this->namecolours);
        $this->assertTrue($results[0]->element->tagName == 'p');
    }

    /**
     * Test with invalid colour names.
     */
    public function test_check_for_invalidcolours() {
        $results = $this->get_checker_results($this->invalidcolours);
        $this->assertTrue($results[0]->element->tagName == 'p');
    }

    /**
     * Test with invalid colour numeric values.
     */
    public function test_check_for_invalidvalues() {
        $results = $this->get_checker_results($this->invalidvalue);
        $this->assertTrue($results[0]->element->tagName == 'p');
    }

    /**
     * Test with empty colour values.
     */
    public function test_check_for_emptyvalues() {
        $results = $this->get_checker_results($this->emptyvalue);
        $this->assertEmpty($results);
    }
}
