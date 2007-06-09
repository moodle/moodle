<?php
/**
 * Code quality unit tests that are so slow you don't want to run them every time.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package SimpleTestEx
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class slow_code_test extends UnitTestCase {
    var $php_code_extensions = array('php', 'html', 'php\.inc');
    var $ignore_folders = array();
    var $phppath;
    
    function prepend_dirroot($string) {
        global $CFG;
        return $CFG->dirroot . $string;
    }
    
    function test_php_syntax() {
        global $CFG;
        
        // See if we can run php from the command line:
        $this->phppath = 'php';
        if (!shell_exec($this->phppath . ' -v')) {
            // If not, we can't do anything.
            $this->fail('Cannot test PHP syntax because PHP is not on the path.');
            return;
        }
        
        $regexp = '/\.(' . implode('|', $this->php_code_extensions) . ')$/';
        $ignore = array_map(array($this, 'prepend_dirroot'), $this->ignore_folders);
        recurseFolders($CFG->dirroot, array($this, 'syntax_check_file'), $regexp, false, $ignore); 
    }
    
    var $dotcount = 0;
    function syntax_check_file($filepath) {
        // If you don't print something for each test, then for some reason the
        // server hangs after a thousand files or so. It is very intermittent.
        // Printing a space does not seem to be good enough.
        echo '.';
        if (++$this->dotcount % 100 == 0) {
            echo '<br>';   
        }
        flush();
        $output = shell_exec($this->phppath . ' -d max_execution_time=5 -d short_open_tag= -l ' . escapeshellarg($filepath));
        $this->assertTrue(strpos($output, 'No syntax errors detected') === 0, $output);
// This generates so many fails that it is currently useless.
//        $this->assertTrue(stripos(file_get_contents($filepath), "\t") === false,
//                    "File $filepath contains a tab character.");
    }
}
?>