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
 * Code quality unit tests that are fast enough to run each time.
 *
 * @package    core
 * @category   phpunit
 * @copyright  &copy; 2006 The Open University
 * @author     T.J.Hunt@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

class code_testcase extends advanced_testcase {
    protected $badstrings;
    protected $extensions_to_ignore = array('exe', 'gif', 'ico', 'jpg', 'png', 'ttf', 'log');
    protected $ignore_folders = array();

    public function test_dnc() {
        global $CFG;

        if ($CFG->ostype === 'UNIX') {
            // try it the faster way
            $oldcwd = getcwd();
            chdir($CFG->dirroot);
            $output = null;
            $exclude = array();
            foreach ($this->extensions_to_ignore as $ext) {
                $exclude[] = '--exclude="*.'.$ext.'"';
            }
            $exclude = implode(' ', $exclude);
            exec('grep -r '.$exclude.' DONOT'.'COMMIT .', $output, $code);
            chdir($oldcwd);
            // return code 0 means found, return code 1 means NOT found, 127 is grep not found
            if ($code == 1) {
                // executed only if no file failed the test
                $this->assertTrue(true);
                return;
            }
        }

        $regexp = '/\.(' . implode('|', $this->extensions_to_ignore) . ')$/';
        $this->badstrings = array();
        $this->badstrings['DONOT' . 'COMMIT'] = 'DONOT' . 'COMMIT'; // If we put the literal string here, it fails the test!
        $this->badstrings['trailing whitespace'] = "[\t ][\r\n]";
        foreach ($this->badstrings as $description => $ignored) {
            $this->allok[$description] = true;
        }
        $this->recurseFolders($CFG->dirroot, 'search_file_for_dnc', $regexp, true);
        $this->assertTrue(true); // executed only if no file failed the test
    }

    protected function search_file_for_dnc($filepath) {
        $content = file_get_contents($filepath);
        foreach ($this->badstrings as $description => $badstring) {
            if (stripos($content, $badstring) !== false) {
                $this->fail("File $filepath contains $description.");
            }
        }
    }
}

