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
 * @package    core_backup
 * @category   test
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup;

use restore_decode_rule;
use restore_decode_rule_exception;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');


/**
 * Restore_decode tests (both rule and content)
 *
 * @package    core_backup
 * @category   test
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class decode_test extends \basic_testcase {

    /**
     * test restore_decode_rule class
     */
    function test_restore_decode_rule() {

        // Test various incorrect constructors
        try {
            $dr = new restore_decode_rule('28 HJH', '/index.php', array());
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_incorrect_name');
            $this->assertEquals($e->a, '28 HJH');
        }

        try {
            $dr = new restore_decode_rule('HJHJhH', '/index.php', array());
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_incorrect_name');
            $this->assertEquals($e->a, 'HJHJhH');
        }

        try {
            $dr = new restore_decode_rule('', '/index.php', array());
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_incorrect_name');
            $this->assertEquals($e->a, '');
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', 'index.php', array());
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_incorrect_urltemplate');
            $this->assertEquals($e->a, 'index.php');
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', '', array());
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_incorrect_urltemplate');
            $this->assertEquals($e->a, '');
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', '/course/view.php?id=$1&c=$2$3', array('test1', 'test2'));
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_mappings_incorrect_count');
            $this->assertEquals($e->a->placeholders, 3);
            $this->assertEquals($e->a->mappings, 2);
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', '/course/view.php?id=$5&c=$4$1', array('test1', 'test2', 'test3'));
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_nonconsecutive_placeholders');
            $this->assertEquals($e->a, '1, 4, 5');
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', '/course/view.php?id=$0&c=$3$2', array('test1', 'test2', 'test3'));
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_nonconsecutive_placeholders');
            $this->assertEquals($e->a, '0, 2, 3');
        }

        try {
            $dr = new restore_decode_rule('TESTRULE', '/course/view.php?id=$1&c=$3$3', array('test1', 'test2', 'test3'));
            $this->assertTrue(false, 'restore_decode_rule_exception exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof restore_decode_rule_exception);
            $this->assertEquals($e->errorcode, 'decode_rule_duplicate_placeholders');
            $this->assertEquals($e->a, '1, 3, 3');
        }

        // Provide some example content and test the regexp is calculated ok
        $content    = '$@TESTRULE*22*33*44@$';
        $linkname   = 'TESTRULE';
        $urltemplate= '/course/view.php?id=$1&c=$3$2';
        $mappings   = array('test1', 'test2', 'test3');
        $result     = '1/course/view.php?id=44&c=8866';
        $dr = new mock_restore_decode_rule($linkname, $urltemplate, $mappings);
        $this->assertEquals($dr->decode($content), $result);

        $content    = '$@TESTRULE*22*33*44@$ñ$@TESTRULE*22*33*44@$';
        $linkname   = 'TESTRULE';
        $urltemplate= '/course/view.php?id=$1&c=$3$2';
        $mappings   = array('test1', 'test2', 'test3');
        $result     = '1/course/view.php?id=44&c=8866ñ1/course/view.php?id=44&c=8866';
        $dr = new mock_restore_decode_rule($linkname, $urltemplate, $mappings);
        $this->assertEquals($dr->decode($content), $result);

        $content    = 'ñ$@TESTRULE*22*0*44@$ñ$@TESTRULE*22*33*44@$ñ';
        $linkname   = 'TESTRULE';
        $urltemplate= '/course/view.php?id=$1&c=$3$2';
        $mappings   = array('test1', 'test2', 'test3');
        $result     = 'ñ0/course/view.php?id=22&c=440ñ1/course/view.php?id=44&c=8866ñ';
        $dr = new mock_restore_decode_rule($linkname, $urltemplate, $mappings);
        $this->assertEquals($dr->decode($content), $result);
    }

    /**
     * test restore_decode_content class
     */
    function test_restore_decode_content() {
        // TODO: restore_decode_content tests
    }

    /**
     * test restore_decode_processor class
     */
    function test_restore_decode_processor() {
        // TODO: restore_decode_processor tests
    }
}

/**
 * Mockup restore_decode_rule for testing purposes
 */
class mock_restore_decode_rule extends restore_decode_rule {

    /**
     * Originally protected, make it public
     */
    public function get_calculated_regexp() {
        return parent::get_calculated_regexp();
    }

    /**
     * Simply map each itemid by its double
     */
    protected function get_mapping($itemname, $itemid) {
        return $itemid * 2;
    }

    /**
     * Simply prefix with '0' non-mapped results and with '1' mapped ones
     */
    protected function apply_modifications($toreplace, $mappingsok) {
        return ($mappingsok ? '1' : '0') . $toreplace;
    }
}
