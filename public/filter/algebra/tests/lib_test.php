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

namespace filter_algebra;

use advanced_testcase;

/**
 * Algebra filter library functions tests.
 *
 * @package    filter_algebra
 * @category   test
 * @copyright  2026 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::filter_algebra_updatedcallback
 */
final class lib_test extends advanced_testcase {
    public function test_updatedcallback_purges_file_area_and_cache(): void {
        $this->resetAfterTest(true);

        $syscontext = \core\context\system::instance();
        $fs = get_file_storage();

        $filerecord = [
            'contextid' => $syscontext->id,
            'component' => 'filter_algebra',
            'filearea' => 'rendered_images',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'testimage.png',
        ];
        $fs->create_file_from_string($filerecord, 'test content');

        $cache = \cache::make('filter_algebra', 'rendered_images');
        $cache->set('testimage_png', 1);

        $this->assertTrue($fs->file_exists($syscontext->id, 'filter_algebra', 'rendered_images', 0, '/', 'testimage.png'));
        $this->assertNotFalse($cache->get('testimage_png'));

        filter_algebra_updatedcallback('convertformat');

        $this->assertFalse($fs->file_exists($syscontext->id, 'filter_algebra', 'rendered_images', 0, '/', 'testimage.png'));
        $this->assertFalse($cache->get('testimage_png'));
    }

    public function test_updatedcallback_deletes_cache_filters_records(): void {
        $this->resetAfterTest(true);

        global $DB;

        $DB->insert_record('cache_filters', (object)[
            'filter' => 'algebra',
            'md5key' => 'abc123',
            'rawtext' => 'x^2',
            'timemodified' => time(),
        ]);

        $this->assertEquals(1, $DB->count_records('cache_filters', ['filter' => 'algebra']));

        filter_algebra_updatedcallback('convertformat');

        $this->assertEquals(0, $DB->count_records('cache_filters', ['filter' => 'algebra']));
    }
}
