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
 * Tests for backup_xml_transformer class.
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2017 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');

/**
 * Tests for backup_xml_transformer.
 *
 * @package core_backup
 * @copyright 2017 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_xml_transformer_testcase extends advanced_testcase {

    /**
     * Initial set up.
     */
    public function setUp() {
        parent::setUp();

        $this->resetAfterTest(true);
    }

    /**
     * Data provider for ::test_filephp_links_replace.
     *
     * @return array
     */
    public function filephp_links_replace_data_provider() {
        return array(
            array('http://test.test/', 'http://test.test/'),
            array('http://test.test/file.php/1', 'http://test.test/file.php/1'),
            array('http://test.test/file.php/2/1.jpg', 'http://test.test/file.php/2/1.jpg'),
            array('http://test.test/file.php/2', 'http://test.test/file.php/2'),
            array('http://test.test/file.php/1/1.jpg', '$@FILEPHP@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php/1//1.jpg', '$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=/1', '$@FILEPHP@$'),
            array('http://test.test/file.php?file=/2/1.jpg', 'http://test.test/file.php?file=/2/1.jpg'),
            array('http://test.test/file.php?file=/2', 'http://test.test/file.php?file=/2'),
            array('http://test.test/file.php?file=/1/1.jpg', '$@FILEPHP@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=/1//1.jpg', '$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=%2f1', '$@FILEPHP@$'),
            array('http://test.test/file.php?file=%2f2%2f1.jpg', 'http://test.test/file.php?file=%2f2%2f1.jpg'),
            array('http://test.test/file.php?file=%2f2', 'http://test.test/file.php?file=%2f2'),
            array('http://test.test/file.php?file=%2f1%2f1.jpg', '$@FILEPHP@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=%2f1%2f%2f1.jpg', '$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=%2F1', '$@FILEPHP@$'),
            array('http://test.test/file.php?file=%2F2%2F1.jpg', 'http://test.test/file.php?file=%2F2%2F1.jpg'),
            array('http://test.test/file.php?file=%2F2', 'http://test.test/file.php?file=%2F2'),
            array('http://test.test/file.php?file=%2F1%2F1.jpg', '$@FILEPHP@$$@SLASH@$1.jpg'),
            array('http://test.test/file.php?file=%2F1%2F%2F1.jpg', '$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'),
            array('http://test.test/h5p/embed.php?url=testurl', '$@H5PEMBED@$?url=testurl'),
        );
    }

    /**
     * Test that backup_xml_transformer replaces file php links to $@FILEPHP@$.
     *
     * @dataProvider filephp_links_replace_data_provider
     * @param string $content Testing content.
     * @param string $expected Expected result.
     */
    public function test_filephp_links_replace($content, $expected) {
        global $CFG;

        $CFG->wwwroot = 'http://test.test';

        $transformer = new backup_xml_transformer(1);

        $this->assertEquals($expected, $transformer->process($content));
    }

}
