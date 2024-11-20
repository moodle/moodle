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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/xmlize.php');

/**
 * This test compares library against the original xmlize XML importer.
 *
 * @package    core
 * @category   test
 * @copyright  2017 Kilian Singer {@link http://quantumtechnology.info}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xmlize_test extends \basic_testcase {
    /**
     * Test an XML import using a valid XML file.
     *
     * The test expected file was generated using the original xmlize
     * implentation found at https://github.com/rmccue/XMLize/blob/master/xmlize-php5.inc.
     */
    public function test_xmlimport_of_proper_file(): void {
        $xml = file_get_contents(__DIR__ . '/sample_questions.xml');
        $serialised = file_get_contents(__DIR__ . '/sample_questions.ser');
        $this->assertEquals(unserialize($serialised), xmlize($xml));
    }

    /**
     * Test an XML import using invalid XML.
     */
    public function test_xmlimport_of_wrong_file(): void {
        $xml = file_get_contents(__DIR__ . '/sample_questions_wrong.xml');
        $this->expectException('xml_format_exception');
        $this->expectExceptionMessage('Error parsing XML: Mismatched tag at line 18, char 23');
        $xmlnew = xmlize($xml, 1, "UTF-8", true);
    }

    /**
     * Test an XML import using legacy question data with old image tag.
     */
    public function test_xmlimport_of_sample_question_with_old_image_tag(): void {
        $xml = file_get_contents(__DIR__ . '/sample_questions_with_old_image_tag.xml');
        $serialised = file_get_contents(__DIR__ . '/sample_questions_with_old_image_tag.ser');

        // Compare the legacy representation in its serialized state and after unserialization.
        $this->assertEquals($serialised, serialize(xmlize($xml)));
        $this->assertEquals(unserialize($serialised), xmlize($xml));
    }
}
