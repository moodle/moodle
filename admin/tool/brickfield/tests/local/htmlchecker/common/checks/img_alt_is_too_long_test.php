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

use tool_brickfield\local\htmlchecker\brickfield_accessibility;

defined('MOODLE_INTERNAL') || die();

require_once('all_checks.php');

/**
 * Class img_alt_is_too_long_testcase
 *
 * @covers \tool_brickfield\local\htmlchecker\common\checks\img_alt_is_too_long
 */
final class img_alt_is_too_long_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'img_alt_is_too_long';

    /**
     * Get test HTML with an image tag.
     *
     * @param string $alttext
     * @return string
     */
    protected function get_test_html(string $alttext): string {
        return <<<EOD
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>Image alt attributes must not be too long</title>
    </head>
    <body>
    <img src="rex.jpg" alt="$alttext">
    </body>
</html>
EOD;
    }

    /**
     * Image alt text data provider.
     *
     * @return array
     */
    public static function img_alt_text_provider(): array {
        return [
            'Alt text <= 750 characters' => [
                true,
                str_repeat("Hello world!", 60),
            ],
            'Alt text > 750 characters' => [
                false,
                str_repeat("Hello world!", 65),
            ],
            'Multi-byte alt text <= 750 characters' => [
                true,
                str_repeat('こんにちは、世界！', 83),
            ],
            'Multi-byte alt text > 750 characters' => [
                false,
                str_repeat('こんにちは、世界！', 90),
            ],
        ];
    }

    /**
     * Test for image alt attributes being too long
     *
     * @dataProvider img_alt_text_provider
     * @param bool $expectedpass Whether the test is expected to pass or fail.
     * @param string $alttext The alt text to test.
     */
    public function test_check(bool $expectedpass, string $alttext): void {
        $html = $this->get_test_html($alttext);
        $results = $this->get_checker_results($html);
        if ($expectedpass) {
            $this->assertEmpty($results);
        } else {
            $this->assertEquals('img', $results[0]->element->tagName);
        }
    }

    /**
     * Test the severity of the {@see img_alt_is_too_long} check.
     *
     * @return void
     */
    public function test_severity(): void {
        $html = $this->get_test_html('Some alt text');
        $checker = new brickfield_accessibility($html, 'brickfield', 'string');
        $this->assertEquals(brickfield_accessibility::BA_TEST_SUGGESTION, $checker->get_test_severity(img_alt_is_too_long::class));
    }
}
