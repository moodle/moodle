<?php
/**
 * Unit tests for (some of) question/type/shortanswer/questiontype.php.
 *
 * @copyright &copy; 2007 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/questiontype.php');

class question_shortanswer_qtype_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
        $this->qtype = new question_shortanswer_qtype();
    }
    
    function tearDown() {
        $this->qtype = null;   
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'shortanswer');
    }

    function test_compare_string_with_wildcard() {
        // Test case sensitive literal matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'Frog', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frog', 'frog', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Frog   ', 'Frog', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'Frog', false));

        // Test case insensitive literal matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'frog', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   FROG   ', 'Frog', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'Frog', true));

        // Test case sensitive wildcard matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'F*og', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Fog', 'F*og', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Fat dog   ', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Fg', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('frog', 'F*og', false));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('   fat dog   ', 'F*og', false));

        // Test case insensitive wildcard matches.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Fog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   Fat dog   ', 'F*og', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Frogs', 'F*og', true));
        $this->assertFalse($this->qtype->compare_string_with_wildcard('Fg', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('frog', 'F*og', true));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   fat dog   ', 'F*og', true));

        // Test match using regexp special chars.
        $this->assertTrue($this->qtype->compare_string_with_wildcard('   *   ', '\*', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('*', '\*', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('Frog*toad', 'Frog\*toad', false));
        $this->assertfalse($this->qtype->compare_string_with_wildcard('a', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('[a-z]', '[a-z]', false));
        $this->assertTrue($this->qtype->compare_string_with_wildcard('\{}/', '\{}/', true));
    }
}

?>
