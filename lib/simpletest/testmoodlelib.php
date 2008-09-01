<?php
/**
 * Unit tests for (some of) ../moodlelib.php.
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
require_once($CFG->libdir . '/moodlelib.php');

class moodlelib_test extends UnitTestCase {

    function setUp() {
    }

    function tearDown() {
    }

    function test_address_in_subnet() {
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.234.2', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.134.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('113.121.234.1', '123.121.234.1'));
        $this->assertTrue(address_in_subnet('123.121.234.0', '123.121.234.2/28'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.16', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.255', '123.121.234.2/28'));
        $this->assertTrue(address_in_subnet('123.121.234.0', '123.121.234.0/')); // / is like /32.
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.0/'));
        $this->assertFalse(address_in_subnet('232.232.232.232', '123.121.234.0/0'));
        $this->assertFalse(address_in_subnet('123.122.234.1', '123.121.'));
        $this->assertFalse(address_in_subnet('223.121.234.1', '123.121.'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('123.122.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('223.121.234.1', '123.121'));
        $this->assertFalse(address_in_subnet('123.121.234.100', '123.121.234.10'));
        $this->assertFalse(address_in_subnet('123.121.234.9', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.10', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.20', '123.121.234.10-20'));
        $this->assertFalse(address_in_subnet('123.121.234.21', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('  123.121.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  1.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  2.2.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  3.3.3.4  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  123.121.234.2  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.3.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  3.3.3.7  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
    }
    
}

?>
