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
 * Test unoconv functionality.
 *
 * @package    core
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * A set of tests for some of the unoconv functionality within Moodle.
 *
 * @package    core
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_unoconv_testcase extends advanced_testcase {

    public function get_converted_document_provider() {
        $fixturepath = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR;
        return [
            'HTML => PDF' => [
                'source'            => $fixturepath . 'unoconv-source.html',
                'sourcefilename'    => 'test.html',
                'format'            => 'pdf',
                'mimetype'          => 'application/pdf',
            ],
            'docx => PDF' => [
                'source'            => $fixturepath . 'unoconv-source.docx',
                'sourcefilename'    => 'test.docx',
                'format'            => 'pdf',
                'mimetype'          => 'application/pdf',
            ],
            'HTML => TXT' => [
                'source'            => $fixturepath . 'unoconv-source.html',
                'sourcefilename'    => 'test.html',
                'format'            => 'txt',
                'mimetype'          => 'text/plain',
            ],
            'docx => TXT' => [
                'source'            => $fixturepath . 'unoconv-source.docx',
                'sourcefilename'    => 'test.docx',
                'format'            => 'txt',
                'mimetype'          => 'text/plain',
            ],
        ];
    }

    /**
     * @dataProvider get_converted_document_provider
     */
    public function test_get_converted_document($source, $sourcefilename, $format, $mimetype) {
        global $CFG;

        if (empty($CFG->pathtounoconv) || !file_is_executable(trim($CFG->pathtounoconv))) {
            // No conversions are possible, sorry.
            return $this->markTestSkipped();
        }

        $this->resetAfterTest();

        $filerecord = array(
            'contextid' => context_system::instance()->id,
            'component' => 'test',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $sourcefilename,
        );

        $fs = get_file_storage();
        //$testfile = $fs->create_file_from_string($filerecord, file_get_contents($source));
        $testfile = $fs->create_file_from_pathname($filerecord, $source);

        $result = $fs->get_converted_document($testfile, $format);
        $this->assertNotFalse($result);
        $this->assertSame($mimetype, $result->get_mimetype());
        $this->assertGreaterThan(0, $result->get_filesize());

        // Repeat immediately with the file forcing re-generation.
        $new = $fs->get_converted_document($testfile, $format, true);
        $this->assertNotFalse($new);
        $this->assertSame($mimetype, $new->get_mimetype());
        $this->assertGreaterThan(0, $new->get_filesize());
        $this->assertNotEquals($result->get_id(), $new->get_id());
        // Note: We cannot compare contenthash for PDF because the PDF has a unique ID, and a creation timestamp
        // imprinted in the file.
    }
}
