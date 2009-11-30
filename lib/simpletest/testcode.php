<?php
/**
 * Code quality unit tests that are fast enough to run each time.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package SimpleTestEx
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class code_test extends UnitTestCase {
    var $allok = array();

    var $badstrings;
    var $extensions_to_ignore = array('exe', 'gif', 'ico', 'jpg', 'png', 'ttf', 'log');
    var $ignore_folders = array();

    function test_dnc() {
        global $CFG;
        $regexp = '/\.(' . implode('|', $this->extensions_to_ignore) . ')$/';
        $this->badstrings = array();
        $this->badstrings['DONOT' . 'COMMIT'] = 'DONOT' . 'COMMIT'; // If we put the literal string here, it fails the test!
        $this->badstrings['trailing whitespace'] = "[\t ][\r\n]";
        foreach ($this->badstrings as $description => $ignored) {
            $this->allok[$description] = true;
        }
        recurseFolders($CFG->dirroot, array($this, 'search_file_for_dnc'), $regexp, true);
        foreach ($this->badstrings as $description => $ignored) {
            if ($this->allok[$description]) {
                $this->pass("No files contain $description.");
            }
        }
    }

    function search_file_for_dnc($filepath) {
        $content = file_get_contents($filepath);
        foreach ($this->badstrings as $description => $badstring) {
            $pass = (stripos($content, $badstring) === false);
            if (!$pass) {
                $this->fail("File $filepath contains $description.");
                $this->allok[$description] = false;
            }
        }
    }
}

