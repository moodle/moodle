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
 * @package moodlecore
 * @subpackage backup-tests
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the needed stuff
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/memory_xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/file_xml_output.class.php');

/*
 * xml_output tests (base, memory and file)
 */
class xml_output_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/xml/output');
    public static $excludecoverage = array('backup/util/xml/output/simpletest');

    /*
     * test memory_xml_output
     */
    function test_memory_xml_output() {
        // Instantiate xml_output
        $xo = new memory_xml_output();
        $this->assertTrue($xo instanceof xml_output);

        // Try to write some contents before starting it
        $xo = new memory_xml_output();
        try {
            $xo->write('test');
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_started');
        }

        // Try to set buffer size if unsupported
        $xo = new memory_xml_output();
        try {
            $xo->set_buffersize(8192);
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_buffer_nosupport');
        }

        // Try to set buffer after start
        $xo = new memory_xml_output();
        $xo->start();
        try {
            $xo->set_buffersize(8192);
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_already_started');
        }

        // Try to stop output before starting it
        $xo = new memory_xml_output();
        try {
            $xo->stop();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_started');
        }

        // Try to debug_info() before starting
        $xo = new memory_xml_output();
        try {
            $xo->debug_info();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_stopped');
        }

        // Start output twice
        $xo = new memory_xml_output();
        $xo->start();
        try {
            $xo->start();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_already_started');
        }

        // Try to debug_info() before stoping
        $xo = new memory_xml_output();
        $xo->start();
        try {
            $xo->debug_info();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_stopped');
        }

        // Stop output twice
        $xo = new memory_xml_output();
        $xo->start();
        $xo->stop();
        try {
            $xo->stop();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_started');
        }

        // Try to re-start after stop
        $xo = new memory_xml_output();
        $xo->start();
        $xo->stop();
        try {
            $xo->start();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_already_stopped');
        }

        // Try to get contents before stopping
        $xo = new memory_xml_output();
        $xo->start();
        try {
            $xo->get_allcontents();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'xml_output_not_stopped');
        }

        // Write some contents and check them
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write('first test');
        $xo->stop();
        $this->assertEqual('first test', $xo->get_allcontents());

        // Write 3 times and check them
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write('first test');
        $xo->write(', sencond test');
        $xo->write(', third test');
        $xo->stop();
        $this->assertEqual('first test, sencond test, third test', $xo->get_allcontents());

        // Write some line feeds, tabs and friends
        $string = "\n\r\tcrazy test\n\r\t";
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write($string);
        $xo->stop();
        $this->assertEqual($string, $xo->get_allcontents());

        // Write some UTF-8 chars
        $string = 'áéíóú';
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write($string);
        $xo->stop();
        $this->assertEqual($string, $xo->get_allcontents());

        // Write some empty content
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write('Hello ');
        $xo->write(null);
        $xo->write(false);
        $xo->write('');
        $xo->write('World');
        $xo->write(null);
        $xo->stop();
        $this->assertEqual('Hello World', $xo->get_allcontents());

        // Get debug info
        $xo = new memory_xml_output();
        $xo->start();
        $xo->write('01234');
        $xo->write('56789');
        $xo->stop();
        $this->assertEqual('0123456789', $xo->get_allcontents());
        $debug = $xo->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('sent', $debug));
        $this->assertEqual($debug['sent'], 10);
    }

    /*
     * test file_xml_output
     */
    function test_file_xml_output() {
        global $CFG;

        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        // Remove the test dir and any content
        @remove_dir(dirname($file));
        // Recreate test dir
        if (!check_dir_exists(dirname($file), true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', dirname($file));
        }

        // Instantiate xml_output
        $xo = new file_xml_output($file);
        $this->assertTrue($xo instanceof xml_output);

        // Try to init file in (near) impossible path
        $file = $CFG->dataroot . '/temp/test_azby/test_file_xml_output.txt';
        $xo = new file_xml_output($file);
        try {
            $xo->start();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'directory_not_exists');
        }

        // Try to init file already existing
        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        file_put_contents($file, 'createdtobedeleted'); // create file manually
        $xo = new file_xml_output($file);
        try {
            $xo->start();
            $this->assertTrue(false, 'xml_output_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof xml_output_exception);
            $this->assertEqual($e->errorcode, 'file_already_exists');
        }
        unlink($file); // delete file

        // Send some output and check
        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        $xo = new file_xml_output($file);
        $xo->start();
        $xo->write('first text');
        $xo->stop();
        $this->assertEqual('first text', file_get_contents($file));
        unlink($file); // delete file

        // With buffer of 4 bytes, send 3 contents of 3 bytes each
        // so we force both buffering and last write on stop
        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        $xo = new file_xml_output($file);
        $xo->set_buffersize(5);
        $xo->start();
        $xo->write('123');
        $xo->write('456');
        $xo->write('789');
        $xo->stop();
        $this->assertEqual('123456789',  file_get_contents($file));
        unlink($file); // delete file

        // Write some line feeds, tabs and friends
        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        $string = "\n\r\tcrazy test\n\r\t";
        $xo = new file_xml_output($file);
        $xo->start();
        $xo->write($string);
        $xo->stop();
        $this->assertEqual($string, file_get_contents($file));
        unlink($file); // delete file

        // Write some UTF-8 chars
        $file = $CFG->dataroot . '/temp/test/test_file_xml_output.txt';
        $string = 'áéíóú';
        $xo = new file_xml_output($file);
        $xo->start();
        $xo->write($string);
        $xo->stop();
        $this->assertEqual($string, file_get_contents($file));
        unlink($file); // delete file

        // Remove the test dir and any content
        @remove_dir(dirname($file));
    }
}
