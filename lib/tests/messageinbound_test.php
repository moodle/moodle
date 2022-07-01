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
 * Test script for message class.
 *
 * Test classes for \core\message\inbound.
 *
 * @package core
 * @category test
 * @copyright 2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

defined('MOODLE_INTERNAL') || die();

/**
 * Test script for message class.
 *
 * Test classes for \core\message\inbound.
 *
 * @package core
 * @category test
 * @copyright 2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messageinbound_test extends \advanced_testcase {

    /**
     * @dataProvider message_inbound_handler_trim_testprovider
     */
    public function test_messageinbound_handler_trim($file, $source, $expectedplain, $expectedhtml) {
        $this->resetAfterTest();

        $mime = \Horde_Mime_Part::parseMessage($source);
        if ($plainpartid = $mime->findBody('plain')) {
            $messagedata = new \stdClass();
            $messagedata->plain = $mime->getPart($plainpartid)->getContents();
            $messagedata->html = '';

            list($message, $format) = test_handler::remove_quoted_text($messagedata);
            list ($message, $expectedplain) = preg_replace("#\r\n#", "\n", array($message, $expectedplain));

            // Normalise line endings on both strings.
            $this->assertEquals($expectedplain, $message);
            $this->assertEquals(FORMAT_PLAIN, $format);
        }

        if ($htmlpartid = $mime->findBody('html')) {
            $messagedata = new \stdClass();
            $messagedata->plain = '';
            $messagedata->html = $mime->getPart($htmlpartid)->getContents();

            list($message, $format) = test_handler::remove_quoted_text($messagedata);

            // Normalise line endings on both strings.
            list ($message, $expectedhtml) = preg_replace("#\r\n#", "\n", array($message, $expectedhtml));
            $this->assertEquals($expectedhtml, $message);
            $this->assertEquals(FORMAT_PLAIN, $format);
        }
    }

    public function message_inbound_handler_trim_testprovider() {
        $fixturesdir = realpath(__DIR__ . '/fixtures/messageinbound/');
        $tests = array();
        $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fixturesdir),
                \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $file) {
            if (!preg_match('/\.test$/', $file)) {
                continue;
            }

            try {
                $testdata = $this->read_test_file($file, $fixturesdir);
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            $test = array(
                    // The filename.
                    basename($file),

                    $testdata['FULLSOURCE'],

                    // The plaintext component of the message.
                    $testdata['EXPECTEDPLAIN'],

                    // The HTML component of the message.
                    $testdata['EXPECTEDHTML'],
                );

            $tests[basename($file)] = $test;
        }
        return $tests;
    }

    protected function read_test_file(\SplFileInfo $file, $fixturesdir) {
        // Break on the --[TOKEN]-- tags in the file.
        $content = file_get_contents($file->getRealPath());
        $content = preg_replace("#\r\n#", "\n", $content);
        $tokens = preg_split('#(?:^|\n*)----([A-Z]+)----\n#', $content,
                null, PREG_SPLIT_DELIM_CAPTURE);
        $sections = array(
            // Key              => Required.
            'FULLSOURCE'        => true,
            'EXPECTEDPLAIN'     => true,
            'EXPECTEDHTML'      => true,
            'CLIENT'            => true, // Required but not needed for tests, just for documentation.
        );
        $section = null;
        $data = array();
        foreach ($tokens as $i => $token) {
            if (null === $section && empty($token)) {
                continue; // Skip leading blank.
            }
            if (null === $section) {
                if (!isset($sections[$token])) {
                    throw new \coding_exception(sprintf(
                        'The test file "%s" should not contain a section named "%s".',
                        basename($file),
                        $token
                    ));
                }
                $section = $token;
                continue;
            }
            $sectiondata = $token;
            $data[$section] = $sectiondata;
            $section = $sectiondata = null;
        }
        foreach ($sections as $section => $required) {
            if ($required && !isset($data[$section])) {
                throw new \coding_exception(sprintf(
                    'The test file "%s" must have a section named "%s".',
                    str_replace($fixturesdir.'/', '', $file),
                    $section
                ));
            }
        }
        return $data;
    }
}

/**
 * Class test_handler
 */
class test_handler extends \core\message\inbound\handler {

    public static function remove_quoted_text($messagedata) {
        return parent::remove_quoted_text($messagedata);
    }

    public function get_name() {}

    public function get_description() {}

    public function process_message(\stdClass $record, \stdClass $messagedata) {}
}
