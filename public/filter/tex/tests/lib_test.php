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
 * Tex filter library functions tests
 *
 * @package   filter_tex
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace filter_tex;

use advanced_testcase;

global $CFG;
require_once($CFG->dirroot . '/filter/tex/lib.php');

/**
 * Tex filter library functions tests
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::filter_tex_sanitize_formula
 * @covers ::filter_tex_updatedcallback
 */
final class lib_test extends advanced_testcase {
    /**
     * Data provider for test_filter_tex_sanitize_formula.
     *
     * @return array
     */
    public static function filter_tex_sanitize_formula_provider(): array {
        return [
            ['x\ =\ \frac{\sqrt{144}}{2}\ \times\ (y\ +\ 12)', 'x\ =\ \frac{\sqrt{144}}{2}\ \times\ (y\ +\ 12)'],
            ['\usepackage[latin1]{inputenc}', '\usepackage[latin1]{inputenc}'],
            ['\newcommand{\A}{\verbatiminput}', '\newforbiddenkeyword_command{\A}{\verbatimforbiddenkeyword_input}'],
            ['\pdffiledump offset 0 length', 'forbiddenkeyword_pdffiledump offset 0 length'],
        ];
    }

    /**
     * Tests for filter_tex_sanitize_formula() function.
     *
     * @dataProvider filter_tex_sanitize_formula_provider
     * @param $formula The formula to test
     * @param $expected The sanitized version of the formula we expect to get
     */
    public function test_filter_tex_sanitize_formula(string $formula, string $expected): void {
        $this->assertEquals($expected, filter_tex_sanitize_formula($formula));
    }

    public function test_updatedcallback_purges_file_area_and_cache(): void {
        $this->resetAfterTest(true);

        $syscontext = \core\context\system::instance();
        $fs = get_file_storage();

        $filerecord = [
            'contextid' => $syscontext->id,
            'component' => 'filter_tex',
            'filearea' => 'rendered_images',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'testimage.png',
        ];
        $fs->create_file_from_string($filerecord, 'test content');

        $cache = \cache::make('filter_tex', 'rendered_images');
        $cache->set('testimage_png', 1);

        $this->assertTrue($fs->file_exists($syscontext->id, 'filter_tex', 'rendered_images', 0, '/', 'testimage.png'));
        $this->assertNotFalse($cache->get('testimage_png'));

        filter_tex_updatedcallback('convertformat');

        $this->assertFalse($fs->file_exists($syscontext->id, 'filter_tex', 'rendered_images', 0, '/', 'testimage.png'));
        $this->assertFalse($cache->get('testimage_png'));
    }

    public function test_updatedcallback_deletes_cache_filters_records(): void {
        $this->resetAfterTest(true);

        global $DB;

        $DB->insert_record('cache_filters', (object)[
            'filter' => 'tex',
            'md5key' => 'abc123',
            'rawtext' => 'x^2',
            'timemodified' => time(),
        ]);

        $this->assertEquals(1, $DB->count_records('cache_filters', ['filter' => 'tex']));

        filter_tex_updatedcallback('convertformat');

        $this->assertEquals(0, $DB->count_records('cache_filters', ['filter' => 'tex']));
    }
}
