<?php
/**
 * Unit tests for (some of) ../weblib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/** */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/weblib.php');

class web_test extends UnitTestCase {

    function setUp() {
    }

    function tearDown() {
    }

    function test_format_string() {
        $this->assertEqual(format_string("& &&&&& &&"), "&amp; &amp;&amp;&amp;&amp;&amp; &amp;&amp;");        
        $this->assertEqual(format_string("ANother & &&&&& Category"), "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category");
        $this->assertEqual(format_string("ANother & &&&&& Category", true), "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category");
        $this->assertEqual(format_string("Nick's Test Site & Other things", true), "Nick's Test Site &amp; Other things");
    }    
}
?>
