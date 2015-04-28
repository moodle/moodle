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
 * Test classes for \core\message\inbound.
 *
 * @package core_message
 * @category test
 * @copyright 2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Test script for message class.
 *
 * @package core_message
 * @category test
 * @copyright 2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_messageinbound_testcase extends advanced_testcase {

    /**
     * @dataProvider message_inbound_handler_trim_testprovider
     */
    public function test_messageinbound_handler_trim($file, $source, $expectedplain, $expectedhtml) {
        $this->resetAfterTest();

        $mime = Horde_Mime_Part::parseMessage($source);
        if ($plainpartid = $mime->findBody('plain')) {
            $messagedata = $mime->getPart($plainpartid)->getContents();

            $linecount = test_handler::get_linecount_to_remove($messagedata);
            $actual = test_handler::remove_quoted_text($messagedata, $linecount);
            $this->assertEquals($expectedplain, $actual);
        }

        if ($htmlpartid = $mime->findBody('html')) {
            $messagedata = html_to_text($mime->getPart($htmlpartid)->getContents());

            $linecount = test_handler::get_linecount_to_remove($messagedata);
            $actual = test_handler::remove_quoted_text($messagedata, $linecount);
            $this->assertEquals($expectedhtml, $actual);
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
        $tokens = preg_split('#(?:^|\n*)----([A-Z-]+)----\n#', file_get_contents($file->getRealPath()),
                null, PREG_SPLIT_DELIM_CAPTURE);
        $sections = array(
            // Key              => Required.
            'FULLSOURCE'        => true,
            'EXPECTEDPLAIN'     => true,
            'EXPECTEDHTML'      => true,
        );
        $section = null;
        foreach ($tokens as $i => $token) {
            if (null === $section && empty($token)) {
                continue; // skip leading blank
            }
            if (null === $section) {
                if (!isset($sections[$token])) {
                    throw new coding_exception(sprintf(
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
                throw new coding_exception(sprintf(
                    'The test file "%s" must have a section named "%s".',
                    str_replace($fixturesdir.'/', '', $file),
                    $section
                ));
            }
        }
        return $data;
    }
}

class test_handler extends \mod_forum\message\inbound\reply_handler {

    public static function remove_quoted_text($text, $linecount = 1) {
        return parent::remove_quoted_text($text, $linecount);
    }

    public static function get_linecount_to_remove($messagedata) {
        return parent::get_linecount_to_remove($messagedata);
    }
}
