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
 * Tests for the dataformat_pdf writer
 *
 * @package    dataformat_pdf
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformat_pdf;

use core\dataformat;
use context_system;
use html_writer;
use moodle_url;

/**
 * Writer tests
 *
 * @package    dataformat_pdf
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer_testcase extends \advanced_testcase {

    /**
     * Test writing data whose content contains an image with pluginfile.php source
     */
    public function test_write_data_with_pluginfile_image(): void {
        global $CFG;

        $this->resetAfterTest(true);

        $imagefixture = "{$CFG->dirroot}/lib/filestorage/tests/fixtures/testimage.jpg";
        $image = get_file_storage()->create_file_from_pathname([
            'contextid' => context_system::instance()->id,
            'component' => 'dataformat_pdf',
            'filearea'  => 'test',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => basename($imagefixture),

        ], $imagefixture);

        $imageurl = moodle_url::make_pluginfile_url($image->get_contextid(), $image->get_component(), $image->get_filearea(),
            $image->get_itemid(), $image->get_filepath(), $image->get_filename());

        // Insert out test image into the data so it is exported.
        $columns = ['animal', 'image'];
        $row = ['cat', html_writer::img($imageurl->out(), 'My image')];

        // Export to file. Assert that the exported file exists.
        $exportfile = dataformat::write_data('My export', 'pdf', $columns, [$row]);
        $this->assertFileExists($exportfile);

        // The exported file should be a reasonable size (~275kb).
        $this->assertGreaterThan(270000, filesize($exportfile));
    }
}
