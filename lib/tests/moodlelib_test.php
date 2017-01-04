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
 * Unit tests for (some of) ../moodlelib.php.
 *
 * @package    core
 * @category   phpunit
 * @copyright  &copy; 2006 The Open University
 * @author     T.J.Hunt@open.ac.uk
 * @author     nicolas@moodle.com
 */

defined('MOODLE_INTERNAL') || die();

class core_moodlelib_testcase extends advanced_testcase {

    public static $includecoverage = array('lib/moodlelib.php');

    /**
     * Define a local decimal separator.
     *
     * It is not possible to directly change the result of get_string in
     * a unit test. Instead, we create a language pack for language 'xx' in
     * dataroot and make langconfig.php with the string we need to change.
     * The example separator used here is 'X'; on PHP 5.3 and before this
     * must be a single byte character due to PHP bug/limitation in
     * number_format, so you can't use UTF-8 characters.
     */
    protected function define_local_decimal_separator() {
        global $SESSION, $CFG;

        $SESSION->lang = 'xx';
        $langconfig = "<?php\n\$string['decsep'] = 'X';";
        $langfolder = $CFG->dataroot . '/lang/xx';
        check_dir_exists($langfolder);
        file_put_contents($langfolder . '/langconfig.php', $langconfig);
    }

    public function test_cleanremoteaddr() {
        // IPv4.
        $this->assertNull(cleanremoteaddr('1023.121.234.1'));
        $this->assertSame('123.121.234.1', cleanremoteaddr('123.121.234.01 '));

        // IPv6.
        $this->assertNull(cleanremoteaddr('0:0:0:0:0:0:0:0:0'));
        $this->assertNull(cleanremoteaddr('0:0:0:0:0:0:0:abh'));
        $this->assertNull(cleanremoteaddr('0:0:0:::0:0:1'));
        $this->assertSame('::', cleanremoteaddr('0:0:0:0:0:0:0:0', true));
        $this->assertSame('::1:1', cleanremoteaddr('0:0:0:0:0:0:1:1', true));
        $this->assertSame('abcd:ef::', cleanremoteaddr('abcd:00ef:0:0:0:0:0:0', true));
        $this->assertSame('1::1', cleanremoteaddr('1:0:0:0:0:0:0:1', true));
        $this->assertSame('0:0:0:0:0:0:10:1', cleanremoteaddr('::10:1', false));
        $this->assertSame('1:1:0:0:0:0:0:0', cleanremoteaddr('01:1::', false));
        $this->assertSame('10:0:0:0:0:0:0:10', cleanremoteaddr('10::10', false));
        $this->assertSame('::ffff:c0a8:11', cleanremoteaddr('::ffff:192.168.1.1', true));
    }

    public function test_address_in_subnet() {
        // 1: xxx.xxx.xxx.xxx/nn or xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx/nnn (number of bits in net mask).
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.1/32'));
        $this->assertFalse(address_in_subnet('123.121.23.1', '123.121.23.0/32'));
        $this->assertTrue(address_in_subnet('10.10.10.100',  '123.121.23.45/0'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.0/24'));
        $this->assertFalse(address_in_subnet('123.121.34.1', '123.121.234.0/24'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.0/30'));
        $this->assertFalse(address_in_subnet('123.121.23.8', '123.121.23.0/30'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba/128'));
        $this->assertFalse(address_in_subnet('bab:baba::baba', 'bab:baba::cece/128'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'cece:cece::cece/0'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba/128'));
        $this->assertTrue(address_in_subnet('baba:baba::00ba', 'baba:baba::/120'));
        $this->assertFalse(address_in_subnet('baba:baba::aba', 'baba:baba::/120'));
        $this->assertTrue(address_in_subnet('baba::baba:00ba', 'baba::baba:0/112'));
        $this->assertFalse(address_in_subnet('baba::aba:00ba', 'baba::baba:0/112'));
        $this->assertFalse(address_in_subnet('aba::baba:0000', 'baba::baba:0/112'));

        // Fixed input.
        $this->assertTrue(address_in_subnet('123.121.23.1   ', ' 123.121.23.0 / 24'));
        $this->assertTrue(address_in_subnet('::ffff:10.1.1.1', ' 0:0:0:000:0:ffff:a1:10 / 126'));

        // Incorrect input.
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/-2'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/64'));
        $this->assertFalse(address_in_subnet('123.121.234.x', '123.121.234.1/24'));
        $this->assertFalse(address_in_subnet('123.121.234.0', '123.121.234.xx/24'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.1/xx0'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/xx0'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/-5'));
        $this->assertFalse(address_in_subnet('::1', '::aa:0/130'));
        $this->assertFalse(address_in_subnet('x:1', '::aa:0/130'));
        $this->assertFalse(address_in_subnet('::1', '::ax:0/130'));

        // 2: xxx.xxx.xxx.xxx-yyy or  xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx::xxxx-yyyy (a range of IP addresses in the last group).
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('123.121.234.13', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('123.121.234.14', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.234.20', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.121.23.12', '123.121.234.12-14'));
        $this->assertFalse(address_in_subnet('123.12.234.12', '123.121.234.12-14'));
        $this->assertTrue(address_in_subnet('baba:baba::baba', 'baba:baba::baba-babe'));
        $this->assertTrue(address_in_subnet('baba:baba::babc', 'baba:baba::baba-babe'));
        $this->assertTrue(address_in_subnet('baba:baba::babe', 'baba:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::bab0', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::babf', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bab:baba::bfbe', 'bab:baba::baba-babe'));
        $this->assertFalse(address_in_subnet('bfb:baba::babe', 'bab:baba::baba-babe'));

        // Fixed input.
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12 - 14 '));
        $this->assertTrue(address_in_subnet('bab:baba::babe', 'bab:baba::baba - babe  '));

        // Incorrect input.
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12-234.14'));
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12-256'));
        $this->assertFalse(address_in_subnet('123.121.234.12', '123.121.234.12--256'));

        // 3: xxx.xxx or xxx.xxx. or xxx:xxx:xxxx or xxx:xxx:xxxx. (incomplete address, a bit non-technical ;-).
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.12'));
        $this->assertFalse(address_in_subnet('123.121.23.12', '123.121.23.13'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234.'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121.234'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123.121'));
        $this->assertTrue(address_in_subnet('123.121.234.12', '123'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '12.121.234.'));
        $this->assertFalse(address_in_subnet('123.121.234.1', '12.121.234'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:baba::bab'));
        $this->assertFalse(address_in_subnet('baba:baba::ba', 'baba:baba::bc'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:baba'));
        $this->assertTrue(address_in_subnet('baba:baba::bab', 'baba:'));
        $this->assertFalse(address_in_subnet('bab:baba::bab', 'baba:'));

        // Multiple subnets.
        $this->assertTrue(address_in_subnet('123.121.234.12', '::1/64, 124., 123.121.234.10-30'));
        $this->assertTrue(address_in_subnet('124.121.234.12', '::1/64, 124., 123.121.234.10-30'));
        $this->assertTrue(address_in_subnet('::2',            '::1/64, 124., 123.121.234.10-30'));
        $this->assertFalse(address_in_subnet('12.121.234.12', '::1/64, 124., 123.121.234.10-30'));

        // Other incorrect input.
        $this->assertFalse(address_in_subnet('123.123.123.123', ''));
    }

    public function test_fix_utf8() {
        // Make sure valid data including other types is not changed.
        $this->assertSame(null, fix_utf8(null));
        $this->assertSame(1, fix_utf8(1));
        $this->assertSame(1.1, fix_utf8(1.1));
        $this->assertSame(true, fix_utf8(true));
        $this->assertSame('', fix_utf8(''));
        $this->assertSame('abc', fix_utf8('abc'));
        $array = array('do', 're', 'mi');
        $this->assertSame($array, fix_utf8($array));
        $object = new stdClass();
        $object->a = 'aa';
        $object->b = 'bb';
        $this->assertEquals($object, fix_utf8($object));

        // valid utf8 string
        $this->assertSame("žlutý koníček přeskočil potůček \n\t\r", fix_utf8("žlutý koníček přeskočil potůček \n\t\r\0"));

        // Invalid utf8 string.
        $this->assertSame('aš', fix_utf8('a'.chr(130).'š'), 'This fails with buggy iconv() when mbstring extenstion is not available as fallback.');
    }

    public function test_optional_param() {
        global $CFG;

        $_POST['username'] = 'post_user';
        $_GET['username'] = 'get_user';
        $this->assertSame($_POST['username'], optional_param('username', 'default_user', PARAM_RAW));

        unset($_POST['username']);
        $this->assertSame($_GET['username'], optional_param('username', 'default_user', PARAM_RAW));

        unset($_GET['username']);
        $this->assertSame('default_user', optional_param('username', 'default_user', PARAM_RAW));

        // Make sure exception is triggered when some params are missing, hide error notices here - new in 2.2.
        $_POST['username'] = 'post_user';
        try {
            optional_param('username', 'default_user', null);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            @optional_param('username', 'default_user');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            @optional_param('username');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            optional_param('', 'default_user', PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Make sure warning is displayed if array submitted - TODO: throw exception in Moodle 2.3.
        $_POST['username'] = array('a'=>'a');
        $this->assertSame($_POST['username'], optional_param('username', 'default_user', PARAM_RAW));
        $this->assertDebuggingCalled();
    }

    public function test_optional_param_array() {
        global $CFG;

        $_POST['username'] = array('a'=>'post_user');
        $_GET['username'] = array('a'=>'get_user');
        $this->assertSame($_POST['username'], optional_param_array('username', array('a'=>'default_user'), PARAM_RAW));

        unset($_POST['username']);
        $this->assertSame($_GET['username'], optional_param_array('username', array('a'=>'default_user'), PARAM_RAW));

        unset($_GET['username']);
        $this->assertSame(array('a'=>'default_user'), optional_param_array('username', array('a'=>'default_user'), PARAM_RAW));

        // Make sure exception is triggered when some params are missing, hide error notices here - new in 2.2.
        $_POST['username'] = array('a'=>'post_user');
        try {
            optional_param_array('username', array('a'=>'default_user'), null);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            @optional_param_array('username', array('a'=>'default_user'));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            @optional_param_array('username');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            optional_param_array('', array('a'=>'default_user'), PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Do not allow nested arrays.
        try {
            $_POST['username'] = array('a'=>array('b'=>'post_user'));
            optional_param_array('username', array('a'=>'default_user'), PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (coding_exception $ex) {
            $this->assertTrue(true);
        }

        // Do not allow non-arrays.
        $_POST['username'] = 'post_user';
        $this->assertSame(array('a'=>'default_user'), optional_param_array('username', array('a'=>'default_user'), PARAM_RAW));
        $this->assertDebuggingCalled();

        // Make sure array keys are sanitised.
        $_POST['username'] = array('abc123_;-/*-+ '=>'arrggh', 'a1_-'=>'post_user');
        $this->assertSame(array('a1_-'=>'post_user'), optional_param_array('username', array(), PARAM_RAW));
        $this->assertDebuggingCalled();
    }

    public function test_required_param() {
        $_POST['username'] = 'post_user';
        $_GET['username'] = 'get_user';
        $this->assertSame('post_user', required_param('username', PARAM_RAW));

        unset($_POST['username']);
        $this->assertSame('get_user', required_param('username', PARAM_RAW));

        unset($_GET['username']);
        try {
            $this->assertSame('default_user', required_param('username', PARAM_RAW));
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }

        // Make sure exception is triggered when some params are missing, hide error notices here - new in 2.2.
        $_POST['username'] = 'post_user';
        try {
            @required_param('username');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            required_param('username', '');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            required_param('', PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Make sure warning is displayed if array submitted - TODO: throw exception in Moodle 2.3.
        $_POST['username'] = array('a'=>'a');
        $this->assertSame($_POST['username'], required_param('username', PARAM_RAW));
        $this->assertDebuggingCalled();
    }

    public function test_required_param_array() {
        global $CFG;

        $_POST['username'] = array('a'=>'post_user');
        $_GET['username'] = array('a'=>'get_user');
        $this->assertSame($_POST['username'], required_param_array('username', PARAM_RAW));

        unset($_POST['username']);
        $this->assertSame($_GET['username'], required_param_array('username', PARAM_RAW));

        // Make sure exception is triggered when some params are missing, hide error notices here - new in 2.2.
        $_POST['username'] = array('a'=>'post_user');
        try {
            required_param_array('username', null);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            @required_param_array('username');
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            required_param_array('', PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Do not allow nested arrays.
        try {
            $_POST['username'] = array('a'=>array('b'=>'post_user'));
            required_param_array('username', PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Do not allow non-arrays.
        try {
            $_POST['username'] = 'post_user';
            required_param_array('username', PARAM_RAW);
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }

        // Make sure array keys are sanitised.
        $_POST['username'] = array('abc123_;-/*-+ '=>'arrggh', 'a1_-'=>'post_user');
        $this->assertSame(array('a1_-'=>'post_user'), required_param_array('username', PARAM_RAW));
        $this->assertDebuggingCalled();
    }

    public function test_clean_param() {
        // Forbid objects and arrays.
        try {
            clean_param(array('x', 'y'), PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            $param = new stdClass();
            $param->id = 1;
            clean_param($param, PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Require correct type.
        try {
            clean_param('x', 'xxxxxx');
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }
        try {
            @clean_param('x');
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }
    }

    public function test_clean_param_array() {
        $this->assertSame(array(), clean_param_array(null, PARAM_RAW));
        $this->assertSame(array('a', 'b'), clean_param_array(array('a', 'b'), PARAM_RAW));
        $this->assertSame(array('a', array('b')), clean_param_array(array('a', array('b')), PARAM_RAW, true));

        // Require correct type.
        try {
            clean_param_array(array('x'), 'xxxxxx');
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }
        try {
            @clean_param_array(array('x'));
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('moodle_exception', $ex);
        }

        try {
            clean_param_array(array('x', array('y')), PARAM_RAW);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Test recursive.
    }

    public function test_clean_param_raw() {
        $this->assertSame(
            '#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)',
            clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_RAW));
    }

    public function test_clean_param_trim() {
        $this->assertSame('Frog toad', clean_param("   Frog toad   \r\n  ", PARAM_RAW_TRIMMED));
    }

    public function test_clean_param_clean() {
        // PARAM_CLEAN is an ugly hack, do not use in new code (skodak),
        // instead use more specific type, or submit sothing that can be verified properly.
        $this->assertSame('xx', clean_param('xx<script>', PARAM_CLEAN));
    }

    public function test_clean_param_alpha() {
        $this->assertSame('DSFMOSDJ', clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHA));
    }

    public function test_clean_param_alphanum() {
        $this->assertSame('978942897DSFMOSDJ', clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHANUM));
    }

    public function test_clean_param_alphaext() {
        $this->assertSame('DSFMOSDJ', clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_ALPHAEXT));
    }

    public function test_clean_param_sequence() {
        $this->assertSame(',9789,42897', clean_param('#()*#,9789\'".,<42897></?$(*DSFMO#$*)(SDJ)($*)', PARAM_SEQUENCE));
    }

    public function test_clean_param_component() {
        // Please note the cleaning of component names is very strict, no guessing here.
        $this->assertSame('mod_forum', clean_param('mod_forum', PARAM_COMPONENT));
        $this->assertSame('block_online_users', clean_param('block_online_users', PARAM_COMPONENT));
        $this->assertSame('block_blond_online_users', clean_param('block_blond_online_users', PARAM_COMPONENT));
        $this->assertSame('mod_something2', clean_param('mod_something2', PARAM_COMPONENT));
        $this->assertSame('forum', clean_param('forum', PARAM_COMPONENT));
        $this->assertSame('user', clean_param('user', PARAM_COMPONENT));
        $this->assertSame('rating', clean_param('rating', PARAM_COMPONENT));
        $this->assertSame('feedback360', clean_param('feedback360', PARAM_COMPONENT));
        $this->assertSame('mod_feedback360', clean_param('mod_feedback360', PARAM_COMPONENT));
        $this->assertSame('', clean_param('mod_2something', PARAM_COMPONENT));
        $this->assertSame('', clean_param('2mod_something', PARAM_COMPONENT));
        $this->assertSame('', clean_param('mod_something_xx', PARAM_COMPONENT));
        $this->assertSame('', clean_param('auth_something__xx', PARAM_COMPONENT));
        $this->assertSame('', clean_param('mod_Something', PARAM_COMPONENT));
        $this->assertSame('', clean_param('mod_somethíng', PARAM_COMPONENT));
        $this->assertSame('', clean_param('mod__something', PARAM_COMPONENT));
        $this->assertSame('', clean_param('auth_xx-yy', PARAM_COMPONENT));
        $this->assertSame('', clean_param('_auth_xx', PARAM_COMPONENT));
        $this->assertSame('', clean_param('a2uth_xx', PARAM_COMPONENT));
        $this->assertSame('', clean_param('auth_xx_', PARAM_COMPONENT));
        $this->assertSame('', clean_param('auth_xx.old', PARAM_COMPONENT));
        $this->assertSame('', clean_param('_user', PARAM_COMPONENT));
        $this->assertSame('', clean_param('2rating', PARAM_COMPONENT));
        $this->assertSame('', clean_param('user_', PARAM_COMPONENT));
    }

    public function test_is_valid_plugin_name() {
        $this->assertTrue(is_valid_plugin_name('forum'));
        $this->assertTrue(is_valid_plugin_name('forum2'));
        $this->assertTrue(is_valid_plugin_name('feedback360'));
        $this->assertTrue(is_valid_plugin_name('online_users'));
        $this->assertTrue(is_valid_plugin_name('blond_online_users'));
        $this->assertFalse(is_valid_plugin_name('online__users'));
        $this->assertFalse(is_valid_plugin_name('forum '));
        $this->assertFalse(is_valid_plugin_name('forum.old'));
        $this->assertFalse(is_valid_plugin_name('xx-yy'));
        $this->assertFalse(is_valid_plugin_name('2xx'));
        $this->assertFalse(is_valid_plugin_name('Xx'));
        $this->assertFalse(is_valid_plugin_name('_xx'));
        $this->assertFalse(is_valid_plugin_name('xx_'));
    }

    public function test_clean_param_plugin() {
        // Please note the cleaning of plugin names is very strict, no guessing here.
        $this->assertSame('forum', clean_param('forum', PARAM_PLUGIN));
        $this->assertSame('forum2', clean_param('forum2', PARAM_PLUGIN));
        $this->assertSame('feedback360', clean_param('feedback360', PARAM_PLUGIN));
        $this->assertSame('online_users', clean_param('online_users', PARAM_PLUGIN));
        $this->assertSame('blond_online_users', clean_param('blond_online_users', PARAM_PLUGIN));
        $this->assertSame('', clean_param('online__users', PARAM_PLUGIN));
        $this->assertSame('', clean_param('forum ', PARAM_PLUGIN));
        $this->assertSame('', clean_param('forum.old', PARAM_PLUGIN));
        $this->assertSame('', clean_param('xx-yy', PARAM_PLUGIN));
        $this->assertSame('', clean_param('2xx', PARAM_PLUGIN));
        $this->assertSame('', clean_param('Xx', PARAM_PLUGIN));
        $this->assertSame('', clean_param('_xx', PARAM_PLUGIN));
        $this->assertSame('', clean_param('xx_', PARAM_PLUGIN));
    }

    public function test_clean_param_area() {
        // Please note the cleaning of area names is very strict, no guessing here.
        $this->assertSame('something', clean_param('something', PARAM_AREA));
        $this->assertSame('something2', clean_param('something2', PARAM_AREA));
        $this->assertSame('some_thing', clean_param('some_thing', PARAM_AREA));
        $this->assertSame('some_thing_xx', clean_param('some_thing_xx', PARAM_AREA));
        $this->assertSame('feedback360', clean_param('feedback360', PARAM_AREA));
        $this->assertSame('', clean_param('_something', PARAM_AREA));
        $this->assertSame('', clean_param('something_', PARAM_AREA));
        $this->assertSame('', clean_param('2something', PARAM_AREA));
        $this->assertSame('', clean_param('Something', PARAM_AREA));
        $this->assertSame('', clean_param('some-thing', PARAM_AREA));
        $this->assertSame('', clean_param('somethííng', PARAM_AREA));
        $this->assertSame('', clean_param('something.x', PARAM_AREA));
    }

    public function test_clean_param_text() {
        $this->assertSame(PARAM_TEXT, PARAM_MULTILANG);
        // Standard.
        $this->assertSame('xx<lang lang="en">aa</lang><lang lang="yy">pp</lang>', clean_param('xx<lang lang="en">aa</lang><lang lang="yy">pp</lang>', PARAM_TEXT));
        $this->assertSame('<span lang="en" class="multilang">aa</span><span lang="xy" class="multilang">bb</span>', clean_param('<span lang="en" class="multilang">aa</span><span lang="xy" class="multilang">bb</span>', PARAM_TEXT));
        $this->assertSame('xx<lang lang="en">aa'."\n".'</lang><lang lang="yy">pp</lang>', clean_param('xx<lang lang="en">aa'."\n".'</lang><lang lang="yy">pp</lang>', PARAM_TEXT));
        // Malformed.
        $this->assertSame('<span lang="en" class="multilang">aa</span>', clean_param('<span lang="en" class="multilang">aa</span>', PARAM_TEXT));
        $this->assertSame('aa', clean_param('<span lang="en" class="nothing" class="multilang">aa</span>', PARAM_TEXT));
        $this->assertSame('aa', clean_param('<lang lang="en" class="multilang">aa</lang>', PARAM_TEXT));
        $this->assertSame('aa', clean_param('<lang lang="en!!">aa</lang>', PARAM_TEXT));
        $this->assertSame('aa', clean_param('<span lang="en==" class="multilang">aa</span>', PARAM_TEXT));
        $this->assertSame('abc', clean_param('a<em>b</em>c', PARAM_TEXT));
        $this->assertSame('a>c>', clean_param('a><xx >c>', PARAM_TEXT)); // Standard strip_tags() behaviour.
        $this->assertSame('a', clean_param('a<b', PARAM_TEXT));
        $this->assertSame('a>b', clean_param('a>b', PARAM_TEXT));
        $this->assertSame('<lang lang="en">a>a</lang>', clean_param('<lang lang="en">a>a</lang>', PARAM_TEXT)); // Standard strip_tags() behaviour.
        $this->assertSame('a', clean_param('<lang lang="en">a<a</lang>', PARAM_TEXT));
        $this->assertSame('<lang lang="en">aa</lang>', clean_param('<lang lang="en">a<br>a</lang>', PARAM_TEXT));
    }

    public function test_clean_param_url() {
        // Test PARAM_URL and PARAM_LOCALURL a bit.
        $this->assertSame('http://google.com/', clean_param('http://google.com/', PARAM_URL));
        $this->assertSame('http://some.very.long.and.silly.domain/with/a/path/', clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_URL));
        $this->assertSame('http://localhost/', clean_param('http://localhost/', PARAM_URL));
        $this->assertSame('http://0.255.1.1/numericip.php', clean_param('http://0.255.1.1/numericip.php', PARAM_URL));
        $this->assertSame('/just/a/path', clean_param('/just/a/path', PARAM_URL));
        $this->assertSame('', clean_param('funny:thing', PARAM_URL));
    }

    public function test_clean_param_localurl() {
        global $CFG;

        $this->resetAfterTest();

        // External, invalid.
        $this->assertSame('', clean_param('funny:thing', PARAM_LOCALURL));
        $this->assertSame('', clean_param('http://google.com/', PARAM_LOCALURL));
        $this->assertSame('', clean_param('https://google.com/?test=true', PARAM_LOCALURL));
        $this->assertSame('', clean_param('http://some.very.long.and.silly.domain/with/a/path/', PARAM_LOCALURL));

        // Local absolute.
        $this->assertSame(clean_param($CFG->wwwroot, PARAM_LOCALURL), $CFG->wwwroot);
        $this->assertSame($CFG->wwwroot . '/with/something?else=true',
            clean_param($CFG->wwwroot . '/with/something?else=true', PARAM_LOCALURL));

        // Local relative.
        $this->assertSame('/just/a/path', clean_param('/just/a/path', PARAM_LOCALURL));
        $this->assertSame('course/view.php?id=3', clean_param('course/view.php?id=3', PARAM_LOCALURL));

        // Local absolute HTTPS.
        $httpsroot = str_replace('http:', 'https:', $CFG->wwwroot);
        $CFG->loginhttps = false;
        $this->assertSame('', clean_param($httpsroot, PARAM_LOCALURL));
        $this->assertSame('', clean_param($httpsroot . '/with/something?else=true', PARAM_LOCALURL));
        $CFG->loginhttps = true;
        $this->assertSame($httpsroot, clean_param($httpsroot, PARAM_LOCALURL));
        $this->assertSame($httpsroot . '/with/something?else=true',
            clean_param($httpsroot . '/with/something?else=true', PARAM_LOCALURL));

        // Test open redirects are not possible.
        $CFG->loginhttps = false;
        $CFG->wwwroot = 'http://www.example.com';
        $this->assertSame('', clean_param('http://www.example.com.evil.net/hack.php', PARAM_LOCALURL));
        $CFG->loginhttps = true;
        $this->assertSame('', clean_param('https://www.example.com.evil.net/hack.php', PARAM_LOCALURL));
    }

    public function test_clean_param_file() {
        $this->assertSame('correctfile.txt', clean_param('correctfile.txt', PARAM_FILE));
        $this->assertSame('badfile.txt', clean_param('b\'a<d`\\/fi:l>e.t"x|t', PARAM_FILE));
        $this->assertSame('..parentdirfile.txt', clean_param('../parentdirfile.txt', PARAM_FILE));
        $this->assertSame('....grandparentdirfile.txt', clean_param('../../grandparentdirfile.txt', PARAM_FILE));
        $this->assertSame('..winparentdirfile.txt', clean_param('..\winparentdirfile.txt', PARAM_FILE));
        $this->assertSame('....wingrandparentdir.txt', clean_param('..\..\wingrandparentdir.txt', PARAM_FILE));
        $this->assertSame('myfile.a.b.txt', clean_param('myfile.a.b.txt', PARAM_FILE));
        $this->assertSame('myfile..a..b.txt', clean_param('myfile..a..b.txt', PARAM_FILE));
        $this->assertSame('myfile.a..b...txt', clean_param('myfile.a..b...txt', PARAM_FILE));
        $this->assertSame('myfile.a.txt', clean_param('myfile.a.txt', PARAM_FILE));
        $this->assertSame('myfile...txt', clean_param('myfile...txt', PARAM_FILE));
        $this->assertSame('...jpg', clean_param('...jpg', PARAM_FILE));
        $this->assertSame('.a.b.', clean_param('.a.b.', PARAM_FILE));
        $this->assertSame('', clean_param('.', PARAM_FILE));
        $this->assertSame('', clean_param('..', PARAM_FILE));
        $this->assertSame('...', clean_param('...', PARAM_FILE));
        $this->assertSame('. . . .', clean_param('. . . .', PARAM_FILE));
        $this->assertSame('dontrtrim.me. .. .. . ', clean_param('dontrtrim.me. .. .. . ', PARAM_FILE));
        $this->assertSame(' . .dontltrim.me', clean_param(' . .dontltrim.me', PARAM_FILE));
        $this->assertSame('here is a tab.txt', clean_param("here is a tab\t.txt", PARAM_FILE));
        $this->assertSame('here is a linebreak.txt', clean_param("here is a line\r\nbreak.txt", PARAM_FILE));

        // The following behaviours have been maintained although they seem a little odd.
        $this->assertSame('funnything', clean_param('funny:thing', PARAM_FILE));
        $this->assertSame('.currentdirfile.txt', clean_param('./currentdirfile.txt', PARAM_FILE));
        $this->assertSame('ctempwindowsfile.txt', clean_param('c:\temp\windowsfile.txt', PARAM_FILE));
        $this->assertSame('homeuserlinuxfile.txt', clean_param('/home/user/linuxfile.txt', PARAM_FILE));
        $this->assertSame('~myfile.txt', clean_param('~/myfile.txt', PARAM_FILE));
    }

    public function test_clean_param_path() {
        $this->assertSame('correctfile.txt', clean_param('correctfile.txt', PARAM_PATH));
        $this->assertSame('bad/file.txt', clean_param('b\'a<d`\\/fi:l>e.t"x|t', PARAM_PATH));
        $this->assertSame('/parentdirfile.txt', clean_param('../parentdirfile.txt', PARAM_PATH));
        $this->assertSame('/grandparentdirfile.txt', clean_param('../../grandparentdirfile.txt', PARAM_PATH));
        $this->assertSame('/winparentdirfile.txt', clean_param('..\winparentdirfile.txt', PARAM_PATH));
        $this->assertSame('/wingrandparentdir.txt', clean_param('..\..\wingrandparentdir.txt', PARAM_PATH));
        $this->assertSame('funnything', clean_param('funny:thing', PARAM_PATH));
        $this->assertSame('./here', clean_param('./././here', PARAM_PATH));
        $this->assertSame('./currentdirfile.txt', clean_param('./currentdirfile.txt', PARAM_PATH));
        $this->assertSame('c/temp/windowsfile.txt', clean_param('c:\temp\windowsfile.txt', PARAM_PATH));
        $this->assertSame('/home/user/linuxfile.txt', clean_param('/home/user/linuxfile.txt', PARAM_PATH));
        $this->assertSame('/home../user ./.linuxfile.txt', clean_param('/home../user ./.linuxfile.txt', PARAM_PATH));
        $this->assertSame('~/myfile.txt', clean_param('~/myfile.txt', PARAM_PATH));
        $this->assertSame('~/myfile.txt', clean_param('~/../myfile.txt', PARAM_PATH));
        $this->assertSame('/..b../.../myfile.txt', clean_param('/..b../.../myfile.txt', PARAM_PATH));
        $this->assertSame('..b../.../myfile.txt', clean_param('..b../.../myfile.txt', PARAM_PATH));
        $this->assertSame('/super/slashes/', clean_param('/super//slashes///', PARAM_PATH));
    }

    public function test_clean_param_username() {
        global $CFG;
        $currentstatus =  $CFG->extendedusernamechars;

        // Run tests with extended character == false;.
        $CFG->extendedusernamechars = false;
        $this->assertSame('johndoe123', clean_param('johndoe123', PARAM_USERNAME) );
        $this->assertSame('john.doe', clean_param('john.doe', PARAM_USERNAME));
        $this->assertSame('john-doe', clean_param('john-doe', PARAM_USERNAME));
        $this->assertSame('john-doe', clean_param('john- doe', PARAM_USERNAME));
        $this->assertSame('john_doe', clean_param('john_doe', PARAM_USERNAME));
        $this->assertSame('john@doe', clean_param('john@doe', PARAM_USERNAME));
        $this->assertSame('johndoe', clean_param('john~doe', PARAM_USERNAME));
        $this->assertSame('johndoe', clean_param('john´doe', PARAM_USERNAME));
        $this->assertSame(clean_param('john# $%&()+_^', PARAM_USERNAME), 'john_');
        $this->assertSame(clean_param(' john# $%&()+_^ ', PARAM_USERNAME), 'john_');
        $this->assertSame(clean_param('john#$%&() ', PARAM_USERNAME), 'john');
        $this->assertSame('johnd', clean_param('JOHNdóé ', PARAM_USERNAME));
        $this->assertSame(clean_param('john.,:;-_/|\ñÑ[]A_X-,D {} ~!@#$%^&*()_+ ?><[] ščřžžý ?ýá?ý??doe ', PARAM_USERNAME), 'john.-_a_x-d@_doe');

        // Test success condition, if extendedusernamechars == ENABLE;.
        $CFG->extendedusernamechars = true;
        $this->assertSame('john_doe', clean_param('john_doe', PARAM_USERNAME));
        $this->assertSame('john@doe', clean_param('john@doe', PARAM_USERNAME));
        $this->assertSame(clean_param('john# $%&()+_^', PARAM_USERNAME), 'john# $%&()+_^');
        $this->assertSame(clean_param(' john# $%&()+_^ ', PARAM_USERNAME), 'john# $%&()+_^');
        $this->assertSame('john~doe', clean_param('john~doe', PARAM_USERNAME));
        $this->assertSame('john´doe', clean_param('joHN´doe', PARAM_USERNAME));
        $this->assertSame('johndoe', clean_param('johnDOE', PARAM_USERNAME));
        $this->assertSame('johndóé', clean_param('johndóé ', PARAM_USERNAME));

        $CFG->extendedusernamechars = $currentstatus;
    }

    public function test_clean_param_stringid() {
        // Test string identifiers validation.
        // Valid strings.
        $this->assertSame('validstring', clean_param('validstring', PARAM_STRINGID));
        $this->assertSame('mod/foobar:valid_capability', clean_param('mod/foobar:valid_capability', PARAM_STRINGID));
        $this->assertSame('CZ', clean_param('CZ', PARAM_STRINGID));
        $this->assertSame('application/vnd.ms-powerpoint', clean_param('application/vnd.ms-powerpoint', PARAM_STRINGID));
        $this->assertSame('grade2', clean_param('grade2', PARAM_STRINGID));
        // Invalid strings.
        $this->assertSame('', clean_param('trailing ', PARAM_STRINGID));
        $this->assertSame('', clean_param('space bar', PARAM_STRINGID));
        $this->assertSame('', clean_param('0numeric', PARAM_STRINGID));
        $this->assertSame('', clean_param('*', PARAM_STRINGID));
        $this->assertSame('', clean_param(' ', PARAM_STRINGID));
    }

    public function test_clean_param_timezone() {
        // Test timezone validation.
        $testvalues = array (
            'America/Jamaica'                => 'America/Jamaica',
            'America/Argentina/Cordoba'      => 'America/Argentina/Cordoba',
            'America/Port-au-Prince'         => 'America/Port-au-Prince',
            'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
            'PST8PDT'                        => 'PST8PDT',
            'Wrong.Value'                    => '',
            'Wrong/.Value'                   => '',
            'Wrong(Value)'                   => '',
            '0'                              => '0',
            '0.0'                            => '0.0',
            '0.5'                            => '0.5',
            '9.0'                            => '9.0',
            '-9.0'                           => '-9.0',
            '+9.0'                           => '+9.0',
            '9.5'                            => '9.5',
            '-9.5'                           => '-9.5',
            '+9.5'                           => '+9.5',
            '12.0'                           => '12.0',
            '-12.0'                          => '-12.0',
            '+12.0'                          => '+12.0',
            '12.5'                           => '12.5',
            '-12.5'                          => '-12.5',
            '+12.5'                          => '+12.5',
            '13.0'                           => '13.0',
            '-13.0'                          => '-13.0',
            '+13.0'                          => '+13.0',
            '13.5'                           => '',
            '+13.5'                          => '',
            '-13.5'                          => '',
            '0.2'                            => '');

        foreach ($testvalues as $testvalue => $expectedvalue) {
            $actualvalue = clean_param($testvalue, PARAM_TIMEZONE);
            $this->assertEquals($expectedvalue, $actualvalue);
        }
    }

    public function test_validate_param() {
        try {
            $param = validate_param('11a', PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }

        $param = validate_param('11', PARAM_INT);
        $this->assertSame(11, $param);

        try {
            $param = validate_param(null, PARAM_INT, false);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }

        $param = validate_param(null, PARAM_INT, true);
        $this->assertSame(null, $param);

        try {
            $param = validate_param(array(), PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
        try {
            $param = validate_param(new stdClass, PARAM_INT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }

        $param = validate_param('1.0', PARAM_FLOAT);
        $this->assertSame(1.0, $param);

        // Make sure valid floats do not cause exception.
        validate_param(1.0, PARAM_FLOAT);
        validate_param(10, PARAM_FLOAT);
        validate_param('0', PARAM_FLOAT);
        validate_param('119813454.545464564564546564545646556564465465456465465465645645465645645645', PARAM_FLOAT);
        validate_param('011.1', PARAM_FLOAT);
        validate_param('11', PARAM_FLOAT);
        validate_param('+.1', PARAM_FLOAT);
        validate_param('-.1', PARAM_FLOAT);
        validate_param('1e10', PARAM_FLOAT);
        validate_param('.1e+10', PARAM_FLOAT);
        validate_param('1E-1', PARAM_FLOAT);

        try {
            $param = validate_param('1,2', PARAM_FLOAT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
        try {
            $param = validate_param('', PARAM_FLOAT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
        try {
            $param = validate_param('.', PARAM_FLOAT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
        try {
            $param = validate_param('e10', PARAM_FLOAT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
        try {
            $param = validate_param('abc', PARAM_FLOAT);
            $this->fail('invalid_parameter_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('invalid_parameter_exception', $ex);
        }
    }

    public function test_shorten_text_no_tags_already_short_enough() {
        // ......12345678901234567890123456.
        $text = "short text already no tags";
        $this->assertSame($text, shorten_text($text));
    }

    public function test_shorten_text_with_tags_already_short_enough() {
        // .........123456...7890....12345678.......901234567.
        $text = "<p>short <b>text</b> already</p><p>with tags</p>";
        $this->assertSame($text, shorten_text($text));
    }

    public function test_shorten_text_no_tags_needs_shortening() {
        // Default truncation is after 30 chars, but allowing 3 for the final '...'.
        // ......12345678901234567890123456789023456789012345678901234.
        $text = "long text without any tags blah de blah blah blah what";
        $this->assertSame('long text without any tags ...', shorten_text($text));
    }

    public function test_shorten_text_with_tags_needs_shortening() {
        // .......................................123456789012345678901234567890...
        $text = "<div class='frog'><p><blockquote>Long text with tags that will ".
            "be chopped off but <b>should be added back again</b></blockquote></p></div>";
        $this->assertEquals("<div class='frog'><p><blockquote>Long text with " .
            "tags that ...</blockquote></p></div>", shorten_text($text));
    }

    public function test_shorten_text_with_tags_and_html_comment() {
        $text = "<div class='frog'><p><blockquote><!--[if !IE]><!-->Long text with ".
            "tags that will<!--<![endif]--> ".
            "be chopped off but <b>should be added back again</b></blockquote></p></div>";
        $this->assertEquals("<div class='frog'><p><blockquote><!--[if !IE]><!-->Long text with " .
            "tags that ...<!--<![endif]--></blockquote></p></div>", shorten_text($text));
    }

    public function test_shorten_text_with_entities() {
        // Remember to allow 3 chars for the final '...'.
        // ......123456789012345678901234567_____890...
        $text = "some text which shouldn't &nbsp; break there";
        $this->assertSame("some text which shouldn't &nbsp; ...", shorten_text($text, 31));
        $this->assertSame("some text which shouldn't &nbsp;...", shorten_text($text, 30));
        $this->assertSame("some text which shouldn't ...", shorten_text($text, 29));
    }

    public function test_shorten_text_known_tricky_case() {
        // This case caused a bug up to 1.9.5
        // ..........123456789012345678901234567890123456789.....0_____1___2___...
        $text = "<h3>standard 'break-out' sub groups in TGs?</h3>&nbsp;&lt;&lt;There are several";
        $this->assertSame("<h3>standard 'break-out' sub groups in ...</h3>",
            shorten_text($text, 41));
        $this->assertSame("<h3>standard 'break-out' sub groups in TGs?...</h3>",
            shorten_text($text, 42));
        $this->assertSame("<h3>standard 'break-out' sub groups in TGs?</h3>&nbsp;...",
            shorten_text($text, 43));
    }

    public function test_shorten_text_no_spaces() {
        // ..........123456789.
        $text = "<h1>123456789</h1>"; // A string with no convenient breaks.
        $this->assertSame("<h1>12345...</h1>", shorten_text($text, 8));
    }

    public function test_shorten_text_utf8_european() {
        // Text without tags.
        // ......123456789012345678901234567.
        $text = "Žluťoučký koníček přeskočil";
        $this->assertSame($text, shorten_text($text)); // 30 chars by default.
        $this->assertSame("Žluťoučký koníče...", shorten_text($text, 19, true));
        $this->assertSame("Žluťoučký ...", shorten_text($text, 19, false));
        // And try it with 2-less (that are, in bytes, the middle of a sequence).
        $this->assertSame("Žluťoučký koní...", shorten_text($text, 17, true));
        $this->assertSame("Žluťoučký ...", shorten_text($text, 17, false));

        // .........123456789012345678...901234567....89012345.
        $text = "<p>Žluťoučký koníček <b>přeskočil</b> potůček</p>";
        $this->assertSame($text, shorten_text($text, 60));
        $this->assertSame("<p>Žluťoučký koníček ...</p>", shorten_text($text, 21));
        $this->assertSame("<p>Žluťoučký koníče...</p>", shorten_text($text, 19, true));
        $this->assertSame("<p>Žluťoučký ...</p>", shorten_text($text, 19, false));
        // And try it with 2 fewer (that are, in bytes, the middle of a sequence).
        $this->assertSame("<p>Žluťoučký koní...</p>", shorten_text($text, 17, true));
        $this->assertSame("<p>Žluťoučký ...</p>", shorten_text($text, 17, false));
        // And try over one tag (start/end), it does proper text len.
        $this->assertSame("<p>Žluťoučký koníček <b>př...</b></p>", shorten_text($text, 23, true));
        $this->assertSame("<p>Žluťoučký koníček <b>přeskočil</b> pot...</p>", shorten_text($text, 34, true));
        // And in the middle of one tag.
        $this->assertSame("<p>Žluťoučký koníček <b>přeskočil...</b></p>", shorten_text($text, 30, true));
    }

    public function test_shorten_text_utf8_oriental() {
        // Japanese
        // text without tags
        // ......123456789012345678901234.
        $text = '言語設定言語設定abcdefghijkl';
        $this->assertSame($text, shorten_text($text)); // 30 chars by default.
        $this->assertSame("言語設定言語...", shorten_text($text, 9, true));
        $this->assertSame("言語設定言語...", shorten_text($text, 9, false));
        $this->assertSame("言語設定言語設定ab...", shorten_text($text, 13, true));
        $this->assertSame("言語設定言語設定...", shorten_text($text, 13, false));

        // Chinese
        // text without tags
        // ......123456789012345678901234.
        $text = '简体中文简体中文abcdefghijkl';
        $this->assertSame($text, shorten_text($text)); // 30 chars by default.
        $this->assertSame("简体中文简体...", shorten_text($text, 9, true));
        $this->assertSame("简体中文简体...", shorten_text($text, 9, false));
        $this->assertSame("简体中文简体中文ab...", shorten_text($text, 13, true));
        $this->assertSame("简体中文简体中文...", shorten_text($text, 13, false));
    }

    public function test_shorten_text_multilang() {
        // This is not necessaryily specific to multilang. The issue is really
        // tags with attributes, where before we were generating invalid HTML
        // output like shorten_text('<span id="x" class="y">A</span> B', 1)
        // returning '<span id="x" ...</span>'. It is just that multilang
        // requires the sort of HTML that is quite likely to trigger this.
        // ........................................1...
        $text = '<span lang="en" class="multilang">A</span>' .
                '<span lang="fr" class="multilang">B</span>';
        $this->assertSame('<span lang="en" class="multilang">...</span>',
                shorten_text($text, 1));
    }

    public function test_usergetdate() {
        global $USER, $CFG, $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $USER->timezone = 2;// Set the timezone to a known state.

        $ts = 1261540267; // The time this function was created.

        $arr = usergetdate($ts, 1); // Specify the timezone as an argument.
        $arr = array_values($arr);

        list($seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month) = $arr;
        $this->assertSame(7, $seconds);
        $this->assertSame(51, $minutes);
        $this->assertSame(4, $hours);
        $this->assertSame(23, $mday);
        $this->assertSame(3, $wday);
        $this->assertSame(12, $mon);
        $this->assertSame(2009, $year);
        $this->assertSame(356, $yday);
        $this->assertSame('Wednesday', $weekday);
        $this->assertSame('December', $month);
        $arr = usergetdate($ts); // Gets the timezone from the $USER object.
        $arr = array_values($arr);

        list($seconds, $minutes, $hours, $mday, $wday, $mon, $year, $yday, $weekday, $month) = $arr;
        $this->assertSame(7, $seconds);
        $this->assertSame(51, $minutes);
        $this->assertSame(5, $hours);
        $this->assertSame(23, $mday);
        $this->assertSame(3, $wday);
        $this->assertSame(12, $mon);
        $this->assertSame(2009, $year);
        $this->assertSame(356, $yday);
        $this->assertSame('Wednesday', $weekday);
        $this->assertSame('December', $month);
    }

    public function test_mark_user_preferences_changed() {
        $this->resetAfterTest();
        $otheruser = $this->getDataGenerator()->create_user();
        $otheruserid = $otheruser->id;

        set_cache_flag('userpreferenceschanged', $otheruserid, null);
        mark_user_preferences_changed($otheruserid);

        $this->assertEquals(get_cache_flag('userpreferenceschanged', $otheruserid, time()-10), 1);
        set_cache_flag('userpreferenceschanged', $otheruserid, null);
    }

    public function test_check_user_preferences_loaded() {
        global $DB;
        $this->resetAfterTest();

        $otheruser = $this->getDataGenerator()->create_user();
        $otheruserid = $otheruser->id;

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, null);

        $user = new stdClass();
        $user->id = $otheruserid;

        // Load.
        check_user_preferences_loaded($user);
        $this->assertTrue(isset($user->preference));
        $this->assertTrue(is_array($user->preference));
        $this->assertArrayHasKey('_lastloaded', $user->preference);
        $this->assertCount(1, $user->preference);

        // Add preference via direct call.
        $DB->insert_record('user_preferences', array('name'=>'xxx', 'value'=>'yyy', 'userid'=>$user->id));

        // No cache reload yet.
        check_user_preferences_loaded($user);
        $this->assertCount(1, $user->preference);

        // Forced reloading of cache.
        unset($user->preference);
        check_user_preferences_loaded($user);
        $this->assertCount(2, $user->preference);
        $this->assertSame('yyy', $user->preference['xxx']);

        // Add preference via direct call.
        $DB->insert_record('user_preferences', array('name'=>'aaa', 'value'=>'bbb', 'userid'=>$user->id));

        // Test timeouts and modifications from different session.
        set_cache_flag('userpreferenceschanged', $user->id, 1, time() + 1000);
        $user->preference['_lastloaded'] = $user->preference['_lastloaded'] - 20;
        check_user_preferences_loaded($user);
        $this->assertCount(2, $user->preference);
        check_user_preferences_loaded($user, 10);
        $this->assertCount(3, $user->preference);
        $this->assertSame('bbb', $user->preference['aaa']);
        set_cache_flag('userpreferenceschanged', $user->id, null);
    }

    public function test_set_user_preference() {
        global $DB, $USER;
        $this->resetAfterTest();

        $this->setAdminUser();

        $otheruser = $this->getDataGenerator()->create_user();
        $otheruserid = $otheruser->id;

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, null);

        $user = new stdClass();
        $user->id = $otheruserid;

        set_user_preference('aaa', 'bbb', $otheruserid);
        $this->assertSame('bbb', $DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'aaa')));
        $this->assertSame('bbb', get_user_preferences('aaa', null, $otheruserid));

        set_user_preference('xxx', 'yyy', $user);
        $this->assertSame('yyy', $DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'xxx')));
        $this->assertSame('yyy', get_user_preferences('xxx', null, $otheruserid));
        $this->assertTrue(is_array($user->preference));
        $this->assertSame('bbb', $user->preference['aaa']);
        $this->assertSame('yyy', $user->preference['xxx']);

        set_user_preference('xxx', null, $user);
        $this->assertFalse($DB->get_field('user_preferences', 'value', array('userid'=>$otheruserid, 'name'=>'xxx')));
        $this->assertNull(get_user_preferences('xxx', null, $otheruserid));

        set_user_preference('ooo', true, $user);
        $prefs = get_user_preferences(null, null, $otheruserid);
        $this->assertSame($user->preference['aaa'], $prefs['aaa']);
        $this->assertSame($user->preference['ooo'], $prefs['ooo']);
        $this->assertSame('1', $prefs['ooo']);

        set_user_preference('null', 0, $user);
        $this->assertSame('0', get_user_preferences('null', null, $otheruserid));

        $this->assertSame('lala', get_user_preferences('undefined', 'lala', $otheruserid));

        $DB->delete_records('user_preferences', array('userid'=>$otheruserid));
        set_cache_flag('userpreferenceschanged', $otheruserid, null);

        // Test $USER default.
        set_user_preference('_test_user_preferences_pref', 'ok');
        $this->assertSame('ok', $USER->preference['_test_user_preferences_pref']);
        unset_user_preference('_test_user_preferences_pref');
        $this->assertTrue(!isset($USER->preference['_test_user_preferences_pref']));

        // Test 1333 char values (no need for unicode, there are already tests for that in DB tests).
        $longvalue = str_repeat('a', 1333);
        set_user_preference('_test_long_user_preference', $longvalue);
        $this->assertEquals($longvalue, get_user_preferences('_test_long_user_preference'));
        $this->assertEquals($longvalue,
            $DB->get_field('user_preferences', 'value', array('userid' => $USER->id, 'name' => '_test_long_user_preference')));

        // Test > 1333 char values, coding_exception expected.
        $longvalue = str_repeat('a', 1334);
        try {
            set_user_preference('_test_long_user_preference', $longvalue);
            $this->fail('Exception expected - longer than 1333 chars not allowed as preference value');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        // Test invalid params.
        try {
            set_user_preference('_test_user_preferences_pref', array());
            $this->fail('Exception expected - array not valid preference value');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            set_user_preference('_test_user_preferences_pref', new stdClass);
            $this->fail('Exception expected - class not valid preference value');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            set_user_preference('_test_user_preferences_pref', 1, array('xx' => 1));
            $this->fail('Exception expected - user instance expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            set_user_preference('_test_user_preferences_pref', 1, 'abc');
            $this->fail('Exception expected - user instance expected');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            set_user_preference('', 1);
            $this->fail('Exception expected - invalid name accepted');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        try {
            set_user_preference('1', 1);
            $this->fail('Exception expected - invalid name accepted');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
    }

    public function test_get_extra_user_fields() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        // It would be really nice if there were a way to 'mock' has_capability
        // checks (either to return true or false) but as there is not, this
        // test doesn't test the capability check. Presumably, anyone running
        // unit tests will have the capability.
        $context = context_system::instance();

        // No fields.
        $CFG->showuseridentity = '';
        $this->assertEquals(array(), get_extra_user_fields($context));

        // One field.
        $CFG->showuseridentity = 'frog';
        $this->assertEquals(array('frog'), get_extra_user_fields($context));

        // Two fields.
        $CFG->showuseridentity = 'frog,zombie';
        $this->assertEquals(array('frog', 'zombie'), get_extra_user_fields($context));

        // No fields, except.
        $CFG->showuseridentity = '';
        $this->assertEquals(array(), get_extra_user_fields($context, array('frog')));

        // One field.
        $CFG->showuseridentity = 'frog';
        $this->assertEquals(array(), get_extra_user_fields($context, array('frog')));

        // Two fields.
        $CFG->showuseridentity = 'frog,zombie';
        $this->assertEquals(array('zombie'), get_extra_user_fields($context, array('frog')));
    }

    public function test_get_extra_user_fields_sql() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $context = context_system::instance();

        // No fields.
        $CFG->showuseridentity = '';
        $this->assertSame('', get_extra_user_fields_sql($context));

        // One field.
        $CFG->showuseridentity = 'frog';
        $this->assertSame(', frog', get_extra_user_fields_sql($context));

        // Two fields with table prefix.
        $CFG->showuseridentity = 'frog,zombie';
        $this->assertSame(', u1.frog, u1.zombie', get_extra_user_fields_sql($context, 'u1'));

        // Two fields with field prefix.
        $CFG->showuseridentity = 'frog,zombie';
        $this->assertSame(', frog AS u_frog, zombie AS u_zombie',
            get_extra_user_fields_sql($context, '', 'u_'));

        // One field excluded.
        $CFG->showuseridentity = 'frog';
        $this->assertSame('', get_extra_user_fields_sql($context, '', '', array('frog')));

        // Two fields, one excluded, table+field prefix.
        $CFG->showuseridentity = 'frog,zombie';
        $this->assertEquals(', u1.zombie AS u_zombie',
            get_extra_user_fields_sql($context, 'u1', 'u_', array('frog')));
    }

    /**
     * Test some critical TZ/DST.
     *
     * This method tests some special TZ/DST combinations that were fixed
     * by MDL-38999. The tests are done by comparing the results of the
     * output using Moodle TZ/DST support and PHP native one.
     *
     * Note: If you don't trust PHP TZ/DST support, can verify the
     * harcoded expectations below with:
     * http://www.tools4noobs.com/online_tools/unix_timestamp_to_datetime/
     */
    public function test_some_moodle_special_dst() {
        $stamp = 1365386400; // 2013/04/08 02:00:00 GMT/UTC.

        // In Europe/Tallinn it was 2013/04/08 05:00:00.
        $expectation = '2013/04/08 05:00:00';
        $phpdt = DateTime::createFromFormat('U', $stamp, new DateTimeZone('UTC'));
        $phpdt->setTimezone(new DateTimeZone('Europe/Tallinn'));
        $phpres = $phpdt->format('Y/m/d H:i:s'); // PHP result.
        $moodleres = userdate($stamp, '%Y/%m/%d %H:%M:%S', 'Europe/Tallinn', false); // Moodle result.
        $this->assertSame($expectation, $phpres);
        $this->assertSame($expectation, $moodleres);

        // In St. Johns it was 2013/04/07 23:30:00.
        $expectation = '2013/04/07 23:30:00';
        $phpdt = DateTime::createFromFormat('U', $stamp, new DateTimeZone('UTC'));
        $phpdt->setTimezone(new DateTimeZone('America/St_Johns'));
        $phpres = $phpdt->format('Y/m/d H:i:s'); // PHP result.
        $moodleres = userdate($stamp, '%Y/%m/%d %H:%M:%S', 'America/St_Johns', false); // Moodle result.
        $this->assertSame($expectation, $phpres);
        $this->assertSame($expectation, $moodleres);

        $stamp = 1383876000; // 2013/11/08 02:00:00 GMT/UTC.

        // In Europe/Tallinn it was 2013/11/08 04:00:00.
        $expectation = '2013/11/08 04:00:00';
        $phpdt = DateTime::createFromFormat('U', $stamp, new DateTimeZone('UTC'));
        $phpdt->setTimezone(new DateTimeZone('Europe/Tallinn'));
        $phpres = $phpdt->format('Y/m/d H:i:s'); // PHP result.
        $moodleres = userdate($stamp, '%Y/%m/%d %H:%M:%S', 'Europe/Tallinn', false); // Moodle result.
        $this->assertSame($expectation, $phpres);
        $this->assertSame($expectation, $moodleres);

        // In St. Johns it was 2013/11/07 22:30:00.
        $expectation = '2013/11/07 22:30:00';
        $phpdt = DateTime::createFromFormat('U', $stamp, new DateTimeZone('UTC'));
        $phpdt->setTimezone(new DateTimeZone('America/St_Johns'));
        $phpres = $phpdt->format('Y/m/d H:i:s'); // PHP result.
        $moodleres = userdate($stamp, '%Y/%m/%d %H:%M:%S', 'America/St_Johns', false); // Moodle result.
        $this->assertSame($expectation, $phpres);
        $this->assertSame($expectation, $moodleres);
    }

    public function test_userdate() {
        global $USER, $CFG, $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $testvalues = array(
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => '0.0', // No dst offset.
                'expectedoutput' => 'Friday, 1 July 2011, 10:00 AM'
            ),
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => '99', // Dst offset and timezone offset.
                'expectedoutput' => 'Friday, 1 July 2011, 7:00 AM'
            ),
            array(
                'time' => '1309514400',
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton', // Dst offset and timezone offset.
                'expectedoutput' => 'Friday, 1 July 2011, 7:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => '0.0', // No dst offset.
                'expectedoutput' => 'Saturday, 1 January 2011, 10:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => '99', // No dst offset in jan, so just timezone offset.
                'expectedoutput' => 'Saturday, 1 January 2011, 6:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton', // No dst offset in jan.
                'expectedoutput' => 'Saturday, 1 January 2011, 6:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '2',
                'timezone' => '99', // Take user timezone.
                'expectedoutput' => 'Saturday, 1 January 2011, 12:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-2',
                'timezone' => '99', // Take user timezone.
                'expectedoutput' => 'Saturday, 1 January 2011, 8:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => '2', // Take this timezone.
                'expectedoutput' => 'Saturday, 1 January 2011, 12:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => '-2', // Take this timezone.
                'expectedoutput' => 'Saturday, 1 January 2011, 8:00 AM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '-10',
                'timezone' => 'random/time', // This should show server time.
                'expectedoutput' => 'Saturday, 1 January 2011, 6:00 PM'
            ),
            array(
                'time' => '1293876000 ',
                'usertimezone' => '20', // Fallback to server time zone.
                'timezone' => '99',     // This should show user time.
                'expectedoutput' => 'Saturday, 1 January 2011, 6:00 PM'
            ),
        );

        // Set default timezone to Australia/Perth, else time calculated
        // will not match expected values.
        $this->setTimezone(99, 'Australia/Perth');

        foreach ($testvalues as $vals) {
            $USER->timezone = $vals['usertimezone'];
            $actualoutput = userdate($vals['time'], '%A, %d %B %Y, %I:%M %p', $vals['timezone']);

            // On different systems case of AM PM changes so compare case insensitive.
            $vals['expectedoutput'] = core_text::strtolower($vals['expectedoutput']);
            $actualoutput = core_text::strtolower($actualoutput);

            $this->assertSame($vals['expectedoutput'], $actualoutput,
                "Expected: {$vals['expectedoutput']} => Actual: {$actualoutput} \ndata: " . var_export($vals, true));
        }
    }

    /**
     * Make sure the DST changes happen at the right time in Moodle.
     */
    public function test_dst_changes() {
        // DST switching in Prague.
        // From 2AM to 3AM in 1989.
        $date = new DateTime('1989-03-26T01:59:00+01:00');
        $this->assertSame('Sunday, 26 March 1989, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('1989-03-26T02:01:00+01:00');
        $this->assertSame('Sunday, 26 March 1989, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        // From 3AM to 2AM in 1989 - not the same as the west Europe.
        $date = new DateTime('1989-09-24T01:59:00+01:00');
        $this->assertSame('Sunday, 24 September 1989, 02:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('1989-09-24T02:01:00+01:00');
        $this->assertSame('Sunday, 24 September 1989, 02:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        // From 2AM to 3AM in 2014.
        $date = new DateTime('2014-03-30T01:59:00+01:00');
        $this->assertSame('Sunday, 30 March 2014, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('2014-03-30T02:01:00+01:00');
        $this->assertSame('Sunday, 30 March 2014, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        // From 3AM to 2AM in 2014.
        $date = new DateTime('2014-10-26T01:59:00+01:00');
        $this->assertSame('Sunday, 26 October 2014, 02:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('2014-10-26T02:01:00+01:00');
        $this->assertSame('Sunday, 26 October 2014, 02:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        // From 2AM to 3AM in 2020.
        $date = new DateTime('2020-03-29T01:59:00+01:00');
        $this->assertSame('Sunday, 29 March 2020, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('2020-03-29T02:01:00+01:00');
        $this->assertSame('Sunday, 29 March 2020, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        // From 3AM to 2AM in 2020.
        $date = new DateTime('2020-10-25T01:59:00+01:00');
        $this->assertSame('Sunday, 25 October 2020, 02:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));
        $date = new DateTime('2020-10-25T02:01:00+01:00');
        $this->assertSame('Sunday, 25 October 2020, 02:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Europe/Prague'));

        // DST switching in NZ.
        // From 3AM to 2AM in 2015.
        $date = new DateTime('2015-04-05T02:59:00+13:00');
        $this->assertSame('Sunday, 5 April 2015, 02:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Pacific/Auckland'));
        $date = new DateTime('2015-04-05T03:01:00+13:00');
        $this->assertSame('Sunday, 5 April 2015, 02:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Pacific/Auckland'));
        // From 2AM to 3AM in 2009.
        $date = new DateTime('2015-09-27T01:59:00+12:00');
        $this->assertSame('Sunday, 27 September 2015, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Pacific/Auckland'));
        $date = new DateTime('2015-09-27T02:01:00+12:00');
        $this->assertSame('Sunday, 27 September 2015, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Pacific/Auckland'));

        // DST switching in Perth.
        // From 3AM to 2AM in 2009.
        $date = new DateTime('2008-03-30T01:59:00+08:00');
        $this->assertSame('Sunday, 30 March 2008, 02:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Australia/Perth'));
        $date = new DateTime('2008-03-30T02:01:00+08:00');
        $this->assertSame('Sunday, 30 March 2008, 02:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Australia/Perth'));
        // From 2AM to 3AM in 2009.
        $date = new DateTime('2008-10-26T01:59:00+08:00');
        $this->assertSame('Sunday, 26 October 2008, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Australia/Perth'));
        $date = new DateTime('2008-10-26T02:01:00+08:00');
        $this->assertSame('Sunday, 26 October 2008, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'Australia/Perth'));

        // DST switching in US.
        // From 2AM to 3AM in 2014.
        $date = new DateTime('2014-03-09T01:59:00-05:00');
        $this->assertSame('Sunday, 9 March 2014, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'America/New_York'));
        $date = new DateTime('2014-03-09T02:01:00-05:00');
        $this->assertSame('Sunday, 9 March 2014, 03:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'America/New_York'));
        // From 3AM to 2AM in 2014.
        $date = new DateTime('2014-11-02T01:59:00-04:00');
        $this->assertSame('Sunday, 2 November 2014, 01:59', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'America/New_York'));
        $date = new DateTime('2014-11-02T02:01:00-04:00');
        $this->assertSame('Sunday, 2 November 2014, 01:01', userdate($date->getTimestamp(), '%A, %d %B %Y, %H:%M', 'America/New_York'));
    }

    public function test_make_timestamp() {
        global $USER, $CFG, $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $testvalues = array(
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '0.0',
                'applydst' => false, // No dst offset.
                'expectedoutput' => '1309514400' // 6pm at UTC+0.
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99',  // User default timezone.
                'applydst' => false, // Don't apply dst.
                'expectedoutput' => '1309528800'
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', // User default timezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309525200'
            ),
            array(
                'usertimezone' => 'America/Moncton',
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => 'America/Moncton', // String timezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309525200'
            ),
            array(
                'usertimezone' => '2', // No dst applyed.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', // Take user timezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309507200'
            ),
            array(
                'usertimezone' => '-2', // No dst applyed.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', // Take usertimezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309521600'
            ),
            array(
                'usertimezone' => '-10', // No dst applyed.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '2',  // Take this timezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309507200'
            ),
            array(
                'usertimezone' => '-10', // No dst applyed.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '-2', // Take this timezone.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309521600'
            ),
            array(
                'usertimezone' => '-10', // No dst applyed.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => 'random/time', // This should show server time.
                'applydst' => true,          // Apply dst.
                'expectedoutput' => '1309485600'
            ),
            array(
                'usertimezone' => '-14', // Server time.
                'year' => '2011',
                'month' => '7',
                'day' => '1',
                'hour' => '10',
                'minutes' => '00',
                'seconds' => '00',
                'timezone' => '99', // Get user time.
                'applydst' => true, // Apply dst.
                'expectedoutput' => '1309485600'
            )
        );

        // Set default timezone to Australia/Perth, else time calculated
        // will not match expected values.
        $this->setTimezone(99, 'Australia/Perth');

        // Test make_timestamp with all testvals and assert if anything wrong.
        foreach ($testvalues as $vals) {
            $USER->timezone = $vals['usertimezone'];
            $actualoutput = make_timestamp(
                $vals['year'],
                $vals['month'],
                $vals['day'],
                $vals['hour'],
                $vals['minutes'],
                $vals['seconds'],
                $vals['timezone'],
                $vals['applydst']
            );

            // On different systems case of AM PM changes so compare case insensitive.
            $vals['expectedoutput'] = core_text::strtolower($vals['expectedoutput']);
            $actualoutput = core_text::strtolower($actualoutput);

            $this->assertSame($vals['expectedoutput'], $actualoutput,
                "Expected: {$vals['expectedoutput']} => Actual: {$actualoutput},
                Please check if timezones are updated (Site adminstration -> location -> update timezone)");
        }
    }

    /**
     * Test get_string and most importantly the implementation of the lang_string
     * object.
     */
    public function test_get_string() {
        global $COURSE;

        // Make sure we are using English.
        $originallang = $COURSE->lang;
        $COURSE->lang = 'en';

        $yes = get_string('yes');
        $yesexpected = 'Yes';
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $yes = get_string('yes', 'moodle');
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $yes = get_string('yes', 'core');
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $yes = get_string('yes', '');
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $yes = get_string('yes', null);
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $yes = get_string('yes', null, 1);
        $this->assertInternalType('string', $yes);
        $this->assertSame($yesexpected, $yes);

        $days = 1;
        $numdays = get_string('numdays', 'core', '1');
        $numdaysexpected = $days.' days';
        $this->assertInternalType('string', $numdays);
        $this->assertSame($numdaysexpected, $numdays);

        $yes = get_string('yes', null, null, true);
        $this->assertInstanceOf('lang_string', $yes);
        $this->assertSame($yesexpected, (string)$yes);

        // Test using a lang_string object as the $a argument for a normal
        // get_string call (returning string).
        $test = new lang_string('yes', null, null, true);
        $testexpected = get_string('numdays', 'core', get_string('yes'));
        $testresult = get_string('numdays', null, $test);
        $this->assertInternalType('string', $testresult);
        $this->assertSame($testexpected, $testresult);

        // Test using a lang_string object as the $a argument for an object
        // get_string call (returning lang_string).
        $test = new lang_string('yes', null, null, true);
        $testexpected = get_string('numdays', 'core', get_string('yes'));
        $testresult = get_string('numdays', null, $test, true);
        $this->assertInstanceOf('lang_string', $testresult);
        $this->assertSame($testexpected, "$testresult");

        // Make sure that object properties that can't be converted don't cause
        // errors.
        // Level one: This is as deep as current language processing goes.
        $test = new stdClass;
        $test->one = 'here';
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that object properties that can't be converted don't cause
        // errors.
        // Level two: Language processing doesn't currently reach this deep.
        // only immediate scalar properties are worked with.
        $test = new stdClass;
        $test->one = new stdClass;
        $test->one->two = 'here';
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that object properties that can't be converted don't cause
        // errors.
        // Level three: It should never ever go this deep, but we're making sure
        // it doesn't cause any probs anyway.
        $test = new stdClass;
        $test->one = new stdClass;
        $test->one->two = new stdClass;
        $test->one->two->three = 'here';
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that object properties that can't be converted don't cause
        // errors and check lang_string properties.
        // Level one: This is as deep as current language processing goes.
        $test = new stdClass;
        $test->one = new lang_string('yes');
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that object properties that can't be converted don't cause
        // errors and check lang_string properties.
        // Level two: Language processing doesn't currently reach this deep.
        // only immediate scalar properties are worked with.
        $test = new stdClass;
        $test->one = new stdClass;
        $test->one->two = new lang_string('yes');
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that object properties that can't be converted don't cause
        // errors and check lang_string properties.
        // Level three: It should never ever go this deep, but we're making sure
        // it doesn't cause any probs anyway.
        $test = new stdClass;
        $test->one = new stdClass;
        $test->one->two = new stdClass;
        $test->one->two->three = new lang_string('yes');
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Make sure that array properties that can't be converted don't cause
        // errors.
        $test = array();
        $test['one'] = new stdClass;
        $test['one']->two = 'here';
        $string = get_string('yes', null, $test, true);
        $this->assertEquals($yesexpected, $string);

        // Same thing but as above except using an object... this is allowed :P.
        $string = get_string('yes', null, null, true);
        $object = new stdClass;
        $object->$string = 'Yes';
        $this->assertEquals($yesexpected, $string);
        $this->assertEquals($yesexpected, $object->$string);

        // Reset the language.
        $COURSE->lang = $originallang;
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function test_get_string_limitation() {
        // This is one of the limitations to the lang_string class. It can't be
        // used as a key.
        $array = array(get_string('yes', null, null, true) => 'yes');
    }

    /**
     * Test localised float formatting.
     */
    public function test_format_float() {

        // Special case for null.
        $this->assertEquals('', format_float(null));

        // Default 1 decimal place.
        $this->assertEquals('5.4', format_float(5.43));
        $this->assertEquals('5.0', format_float(5.001));

        // Custom number of decimal places.
        $this->assertEquals('5.43000', format_float(5.43, 5));

        // Option to strip ending zeros after rounding.
        $this->assertEquals('5.43', format_float(5.43, 5, true, true));
        $this->assertEquals('5', format_float(5.0001, 3, true, true));

        // Tests with a localised decimal separator.
        $this->define_local_decimal_separator();

        // Localisation on (default).
        $this->assertEquals('5X43000', format_float(5.43, 5));
        $this->assertEquals('5X43', format_float(5.43, 5, true, true));

        // Localisation off.
        $this->assertEquals('5.43000', format_float(5.43, 5, false));
        $this->assertEquals('5.43', format_float(5.43, 5, false, true));
    }

    /**
     * Test localised float unformatting.
     */
    public function test_unformat_float() {

        // Tests without the localised decimal separator.

        // Special case for null, empty or white spaces only strings.
        $this->assertEquals(null, unformat_float(null));
        $this->assertEquals(null, unformat_float(''));
        $this->assertEquals(null, unformat_float('    '));

        // Regular use.
        $this->assertEquals(5.4, unformat_float('5.4'));
        $this->assertEquals(5.4, unformat_float('5.4', true));

        // No decimal.
        $this->assertEquals(5.0, unformat_float('5'));

        // Custom number of decimal.
        $this->assertEquals(5.43267, unformat_float('5.43267'));

        // Empty decimal.
        $this->assertEquals(100.0, unformat_float('100.00'));

        // With the thousand separator.
        $this->assertEquals(1000.0, unformat_float('1 000'));
        $this->assertEquals(1000.32, unformat_float('1 000.32'));

        // Negative number.
        $this->assertEquals(-100.0, unformat_float('-100'));

        // Wrong value.
        $this->assertEquals(0.0, unformat_float('Wrong value'));
        // Wrong value in strict mode.
        $this->assertFalse(unformat_float('Wrong value', true));

        // Combining options.
        $this->assertEquals(-1023.862567, unformat_float('   -1 023.862567     '));

        // Bad decimal separator (should crop the decimal).
        $this->assertEquals(50.0, unformat_float('50,57'));
        // Bad decimal separator in strict mode (should return false).
        $this->assertFalse(unformat_float('50,57', true));

        // Tests with a localised decimal separator.
        $this->define_local_decimal_separator();

        // We repeat the tests above but with the current decimal separator.

        // Regular use without and with the localised separator.
        $this->assertEquals (5.4, unformat_float('5.4'));
        $this->assertEquals (5.4, unformat_float('5X4'));

        // Custom number of decimal.
        $this->assertEquals (5.43267, unformat_float('5X43267'));

        // Empty decimal.
        $this->assertEquals (100.0, unformat_float('100X00'));

        // With the thousand separator.
        $this->assertEquals (1000.32, unformat_float('1 000X32'));

        // Bad different separator (should crop the decimal).
        $this->assertEquals (50.0, unformat_float('50Y57'));
        // Bad different separator in strict mode (should return false).
        $this->assertFalse (unformat_float('50Y57', true));

        // Combining options.
        $this->assertEquals (-1023.862567, unformat_float('   -1 023X862567     '));
        // Combining options in strict mode.
        $this->assertEquals (-1023.862567, unformat_float('   -1 023X862567     ', true));
    }

    /**
     * Test deleting of users.
     */
    public function test_delete_user() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $guest = $DB->get_record('user', array('id'=>$CFG->siteguest), '*', MUST_EXIST);
        $admin = $DB->get_record('user', array('id'=>$CFG->siteadmins), '*', MUST_EXIST);
        $this->assertEquals(0, $DB->count_records('user', array('deleted'=>1)));

        $user = $this->getDataGenerator()->create_user(array('idnumber'=>'abc'));
        $user2 = $this->getDataGenerator()->create_user(array('idnumber'=>'xyz'));
        $usersharedemail1 = $this->getDataGenerator()->create_user(array('email' => 'sharedemail@example.invalid'));
        $usersharedemail2 = $this->getDataGenerator()->create_user(array('email' => 'sharedemail@example.invalid'));
        $useremptyemail1 = $this->getDataGenerator()->create_user(array('email' => ''));
        $useremptyemail2 = $this->getDataGenerator()->create_user(array('email' => ''));

        // Delete user and capture event.
        $sink = $this->redirectEvents();
        $result = delete_user($user);
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        // Test user is deleted in DB.
        $this->assertTrue($result);
        $deluser = $DB->get_record('user', array('id'=>$user->id), '*', MUST_EXIST);
        $this->assertEquals(1, $deluser->deleted);
        $this->assertEquals(0, $deluser->picture);
        $this->assertSame('', $deluser->idnumber);
        $this->assertSame(md5($user->username), $deluser->email);
        $this->assertRegExp('/^'.preg_quote($user->email, '/').'\.\d*$/', $deluser->username);

        $this->assertEquals(1, $DB->count_records('user', array('deleted'=>1)));

        // Test Event.
        $this->assertInstanceOf('\core\event\user_deleted', $event);
        $this->assertSame($user->id, $event->objectid);
        $this->assertSame('user_deleted', $event->get_legacy_eventname());
        $this->assertEventLegacyData($user, $event);
        $expectedlogdata = array(SITEID, 'user', 'delete', "view.php?id=$user->id", $user->firstname.' '.$user->lastname);
        $this->assertEventLegacyLogData($expectedlogdata, $event);
        $eventdata = $event->get_data();
        $this->assertSame($eventdata['other']['username'], $user->username);
        $this->assertSame($eventdata['other']['email'], $user->email);
        $this->assertSame($eventdata['other']['idnumber'], $user->idnumber);
        $this->assertSame($eventdata['other']['picture'], $user->picture);
        $this->assertSame($eventdata['other']['mnethostid'], $user->mnethostid);
        $this->assertEquals($user, $event->get_record_snapshot('user', $event->objectid));
        $this->assertEventContextNotUsed($event);

        // Try invalid params.
        $record = new stdClass();
        $record->grrr = 1;
        try {
            delete_user($record);
            $this->fail('Expecting exception for invalid delete_user() $user parameter');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }
        $record->id = 1;
        try {
            delete_user($record);
            $this->fail('Expecting exception for invalid delete_user() $user parameter');
        } catch (moodle_exception $ex) {
            $this->assertInstanceOf('coding_exception', $ex);
        }

        $record = new stdClass();
        $record->id = 666;
        $record->username = 'xx';
        $this->assertFalse($DB->record_exists('user', array('id'=>666))); // Any non-existent id is ok.
        $result = delete_user($record);
        $this->assertFalse($result);

        $result = delete_user($guest);
        $this->assertFalse($result);

        $result = delete_user($admin);
        $this->assertFalse($result);

        // Simultaneously deleting users with identical email addresses.
        $result1 = delete_user($usersharedemail1);
        $result2 = delete_user($usersharedemail2);

        $usersharedemail1after = $DB->get_record('user', array('id' => $usersharedemail1->id));
        $usersharedemail2after = $DB->get_record('user', array('id' => $usersharedemail2->id));
        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertStringStartsWith($usersharedemail1->email . '.', $usersharedemail1after->username);
        $this->assertStringStartsWith($usersharedemail2->email . '.', $usersharedemail2after->username);

        // Simultaneously deleting users without email addresses.
        $result1 = delete_user($useremptyemail1);
        $result2 = delete_user($useremptyemail2);

        $useremptyemail1after = $DB->get_record('user', array('id' => $useremptyemail1->id));
        $useremptyemail2after = $DB->get_record('user', array('id' => $useremptyemail2->id));
        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertStringStartsWith($useremptyemail1->username . '.' . $useremptyemail1->id . '@unknownemail.invalid.',
            $useremptyemail1after->username);
        $this->assertStringStartsWith($useremptyemail2->username . '.' . $useremptyemail2->id . '@unknownemail.invalid.',
            $useremptyemail2after->username);

        $this->resetDebugging();
    }

    /**
     * Test function convert_to_array()
     */
    public function test_convert_to_array() {
        // Check that normal classes are converted to arrays the same way as (array) would do.
        $obj = new stdClass();
        $obj->prop1 = 'hello';
        $obj->prop2 = array('first', 'second', 13);
        $obj->prop3 = 15;
        $this->assertEquals(convert_to_array($obj), (array)$obj);

        // Check that context object (with iterator) is converted to array properly.
        $obj = context_system::instance();
        $ar = array(
            'id'           => $obj->id,
            'contextlevel' => $obj->contextlevel,
            'instanceid'   => $obj->instanceid,
            'path'         => $obj->path,
            'depth'        => $obj->depth
        );
        $this->assertEquals(convert_to_array($obj), $ar);
    }

    /**
     * Test the function date_format_string().
     */
    public function test_date_format_string() {
        global $CFG;

        $this->resetAfterTest();
        $this->setTimezone(99, 'Australia/Perth');

        $tests = array(
            array(
                'tz' => 99,
                'str' => '%A, %d %B %Y, %I:%M %p',
                'expected' => 'Saturday, 01 January 2011, 06:00 PM'
            ),
            array(
                'tz' => 0,
                'str' => '%A, %d %B %Y, %I:%M %p',
                'expected' => 'Saturday, 01 January 2011, 10:00 AM'
            ),
            array(
                // Note: this function expected the timestamp in weird format before,
                // since 2.9 it uses UTC.
                'tz' => 'Pacific/Auckland',
                'str' => '%A, %d %B %Y, %I:%M %p',
                'expected' => 'Saturday, 01 January 2011, 11:00 PM'
            ),
            // Following tests pass on Windows only because en lang pack does
            // not contain localewincharset, in real life lang pack maintainers
            // may use only characters that are present in localewincharset
            // in format strings!
            array(
                'tz' => 99,
                'str' => 'Žluťoučký koníček %A',
                'expected' => 'Žluťoučký koníček Saturday'
            ),
            array(
                'tz' => 99,
                'str' => '言語設定言語 %A',
                'expected' => '言語設定言語 Saturday'
            ),
            array(
                'tz' => 99,
                'str' => '简体中文简体 %A',
                'expected' => '简体中文简体 Saturday'
            ),
        );

        // Note: date_format_string() uses the timezone only to differenciate
        // the server time from the UTC time. It does not modify the timestamp.
        // Hence similar results for timezones <= 13.
        // On different systems case of AM PM changes so compare case insensitive.
        foreach ($tests as $test) {
            $str = date_format_string(1293876000, $test['str'], $test['tz']);
            $this->assertSame(core_text::strtolower($test['expected']), core_text::strtolower($str));
        }
    }

    public function test_get_config() {
        global $CFG;

        $this->resetAfterTest();

        // Preparation.
        set_config('phpunit_test_get_config_1', 'test 1');
        set_config('phpunit_test_get_config_2', 'test 2', 'mod_forum');
        if (!is_array($CFG->config_php_settings)) {
            $CFG->config_php_settings = array();
        }
        $CFG->config_php_settings['phpunit_test_get_config_3'] = 'test 3';

        if (!is_array($CFG->forced_plugin_settings)) {
            $CFG->forced_plugin_settings = array();
        }
        if (!array_key_exists('mod_forum', $CFG->forced_plugin_settings)) {
            $CFG->forced_plugin_settings['mod_forum'] = array();
        }
        $CFG->forced_plugin_settings['mod_forum']['phpunit_test_get_config_4'] = 'test 4';
        $CFG->phpunit_test_get_config_5 = 'test 5';

        // Testing.
        $this->assertSame('test 1', get_config('core', 'phpunit_test_get_config_1'));
        $this->assertSame('test 2', get_config('mod_forum', 'phpunit_test_get_config_2'));
        $this->assertSame('test 3', get_config('core', 'phpunit_test_get_config_3'));
        $this->assertSame('test 4', get_config('mod_forum', 'phpunit_test_get_config_4'));
        $this->assertFalse(get_config('core', 'phpunit_test_get_config_5'));
        $this->assertFalse(get_config('core', 'phpunit_test_get_config_x'));
        $this->assertFalse(get_config('mod_forum', 'phpunit_test_get_config_x'));

        // Test config we know to exist.
        $this->assertSame($CFG->dataroot, get_config('core', 'dataroot'));
        $this->assertSame($CFG->phpunit_dataroot, get_config('core', 'phpunit_dataroot'));
        $this->assertSame($CFG->dataroot, get_config('core', 'phpunit_dataroot'));
        $this->assertSame(get_config('core', 'dataroot'), get_config('core', 'phpunit_dataroot'));

        // Test setting a config var that already exists.
        set_config('phpunit_test_get_config_1', 'test a');
        $this->assertSame('test a', $CFG->phpunit_test_get_config_1);
        $this->assertSame('test a', get_config('core', 'phpunit_test_get_config_1'));

        // Test cache invalidation.
        $cache = cache::make('core', 'config');
        $this->assertInternalType('array', $cache->get('core'));
        $this->assertInternalType('array', $cache->get('mod_forum'));
        set_config('phpunit_test_get_config_1', 'test b');
        $this->assertFalse($cache->get('core'));
        set_config('phpunit_test_get_config_4', 'test c', 'mod_forum');
        $this->assertFalse($cache->get('mod_forum'));
    }

    public function test_get_max_upload_sizes() {
        // Test with very low limits so we are not affected by php upload limits.
        // Test activity limit smallest.
        $sitebytes = 102400;
        $coursebytes = 51200;
        $modulebytes = 10240;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes);

        $this->assertSame('Activity upload limit (10KB)', $result['0']);
        $this->assertCount(2, $result);

        // Test course limit smallest.
        $sitebytes = 102400;
        $coursebytes = 10240;
        $modulebytes = 51200;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes);

        $this->assertSame('Course upload limit (10KB)', $result['0']);
        $this->assertCount(2, $result);

        // Test site limit smallest.
        $sitebytes = 10240;
        $coursebytes = 102400;
        $modulebytes = 51200;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes);

        $this->assertSame('Site upload limit (10KB)', $result['0']);
        $this->assertCount(2, $result);

        // Test site limit not set.
        $sitebytes = 0;
        $coursebytes = 102400;
        $modulebytes = 51200;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes);

        $this->assertSame('Activity upload limit (50KB)', $result['0']);
        $this->assertCount(3, $result);

        $sitebytes = 0;
        $coursebytes = 51200;
        $modulebytes = 102400;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes);

        $this->assertSame('Course upload limit (50KB)', $result['0']);
        $this->assertCount(3, $result);

        // Test custom bytes in range.
        $sitebytes = 102400;
        $coursebytes = 51200;
        $modulebytes = 51200;
        $custombytes = 10240;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes, $custombytes);

        $this->assertCount(3, $result);

        // Test custom bytes in range but non-standard.
        $sitebytes = 102400;
        $coursebytes = 51200;
        $modulebytes = 51200;
        $custombytes = 25600;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes, $custombytes);

        $this->assertCount(4, $result);

        // Test custom bytes out of range.
        $sitebytes = 102400;
        $coursebytes = 51200;
        $modulebytes = 51200;
        $custombytes = 102400;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes, $custombytes);

        $this->assertCount(3, $result);

        // Test custom bytes out of range and non-standard.
        $sitebytes = 102400;
        $coursebytes = 51200;
        $modulebytes = 51200;
        $custombytes = 256000;
        $result = get_max_upload_sizes($sitebytes, $coursebytes, $modulebytes, $custombytes);

        $this->assertCount(3, $result);

        // Test site limit only.
        $sitebytes = 51200;
        $result = get_max_upload_sizes($sitebytes);

        $this->assertSame('Site upload limit (50KB)', $result['0']);
        $this->assertSame('50KB', $result['51200']);
        $this->assertSame('10KB', $result['10240']);
        $this->assertCount(3, $result);

        // Test no limit.
        $result = get_max_upload_sizes();
        $this->assertArrayHasKey('0', $result);
        $this->assertArrayHasKey(get_max_upload_file_size(), $result);
    }

    /**
     * Test function password_is_legacy_hash().
     */
    public function test_password_is_legacy_hash() {
        // Well formed md5s should be matched.
        foreach (array('some', 'strings', 'to_check!') as $string) {
            $md5 = md5($string);
            $this->assertTrue(password_is_legacy_hash($md5));
        }
        // Strings that are not md5s should not be matched.
        foreach (array('', AUTH_PASSWORD_NOT_CACHED, 'IPW8WTcsWNgAWcUS1FBVHegzJnw5M2jOmYkmfc8z.xdBOyC4Caeum') as $notmd5) {
            $this->assertFalse(password_is_legacy_hash($notmd5));
        }
    }

    /**
     * Test function validate_internal_user_password().
     */
    public function test_validate_internal_user_password() {
        // Test bcrypt hashes.
        $validhashes = array(
            'pw' => '$2y$10$LOSDi5eaQJhutSRun.OVJ.ZSxQZabCMay7TO1KmzMkDMPvU40zGXK',
            'abc' => '$2y$10$VWTOhVdsBbWwtdWNDRHSpewjd3aXBQlBQf5rBY/hVhw8hciarFhXa',
            'C0mP1eX_&}<?@*&%` |\"' => '$2y$10$3PJf.q.9ywNJlsInPbqc8.IFeSsvXrGvQLKRFBIhVu1h1I3vpIry6',
            'ĩńťėŕňăţĩōŋāĹ' => '$2y$10$3A2Y8WpfRAnP3czJiSv6N.6Xp0T8hW3QZz2hUCYhzyWr1kGP1yUve'
        );

        foreach ($validhashes as $password => $hash) {
            $user = new stdClass();
            $user->auth = 'manual';
            $user->password = $hash;
            // The correct password should be validated.
            $this->assertTrue(validate_internal_user_password($user, $password));
            // An incorrect password should not be validated.
            $this->assertFalse(validate_internal_user_password($user, 'badpw'));
        }
    }

    /**
     * Test function hash_internal_user_password().
     */
    public function test_hash_internal_user_password() {
        $passwords = array('pw', 'abc123', 'C0mP1eX_&}<?@*&%` |\"', 'ĩńťėŕňăţĩōŋāĹ');

        // Check that some passwords that we convert to hashes can
        // be validated.
        foreach ($passwords as $password) {
            $hash = hash_internal_user_password($password);
            $fasthash = hash_internal_user_password($password, true);
            $user = new stdClass();
            $user->auth = 'manual';
            $user->password = $hash;
            $this->assertTrue(validate_internal_user_password($user, $password));

            // They should not be in md5 format.
            $this->assertFalse(password_is_legacy_hash($hash));

            // Check that cost factor in hash is correctly set.
            $this->assertRegExp('/\$10\$/', $hash);
            $this->assertRegExp('/\$04\$/', $fasthash);
        }
    }

    /**
     * Test function update_internal_user_password().
     */
    public function test_update_internal_user_password() {
        global $DB;
        $this->resetAfterTest();
        $passwords = array('password', '1234', 'changeme', '****');
        foreach ($passwords as $password) {
            $user = $this->getDataGenerator()->create_user(array('auth'=>'manual'));
            update_internal_user_password($user, $password);
            // The user object should have been updated.
            $this->assertTrue(validate_internal_user_password($user, $password));
            // The database field for the user should also have been updated to the
            // same value.
            $this->assertSame($user->password, $DB->get_field('user', 'password', array('id' => $user->id)));
        }

        $user = $this->getDataGenerator()->create_user(array('auth'=>'manual'));
        // Manually set the user's password to the md5 of the string 'password'.
        $DB->set_field('user', 'password', '5f4dcc3b5aa765d61d8327deb882cf99', array('id' => $user->id));

        $sink = $this->redirectEvents();
        // Update the password.
        update_internal_user_password($user, 'password');
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        // Password should have been updated to a bcrypt hash.
        $this->assertFalse(password_is_legacy_hash($user->password));

        // Verify event information.
        $this->assertInstanceOf('\core\event\user_password_updated', $event);
        $this->assertSame($user->id, $event->relateduserid);
        $this->assertEquals(context_user::instance($user->id), $event->get_context());
        $this->assertEventContextNotUsed($event);

        // Verify recovery of property 'auth'.
        unset($user->auth);
        update_internal_user_password($user, 'newpassword');
        $this->assertDebuggingCalled('User record in update_internal_user_password() must include field auth',
                DEBUG_DEVELOPER);
        $this->assertEquals('manual', $user->auth);
    }

    /**
     * Testing that if the password is not cached, that it does not update
     * the user table and fire event.
     */
    public function test_update_internal_user_password_no_cache() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(array('auth' => 'cas'));
        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);

        $sink = $this->redirectEvents();
        update_internal_user_password($user, 'wonkawonka');
        $this->assertEquals(0, $sink->count(), 'User updated event should not fire');
    }

    /**
     * Test if the user has a password hash, but now their auth method
     * says not to cache it.  Then it should update.
     */
    public function test_update_internal_user_password_update_no_cache() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(array('password' => 'test'));
        $this->assertNotEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);
        $user->auth = 'cas'; // Change to a auth that does not store passwords.

        $sink = $this->redirectEvents();
        update_internal_user_password($user, 'wonkawonka');
        $this->assertGreaterThanOrEqual(1, $sink->count(), 'User updated event should fire');

        $this->assertEquals(AUTH_PASSWORD_NOT_CACHED, $user->password);
    }

    public function test_fullname() {
        global $CFG;

        $this->resetAfterTest();

        // Create a user to test the name display on.
        $record = array();
        $record['firstname'] = 'Scott';
        $record['lastname'] = 'Fletcher';
        $record['firstnamephonetic'] = 'スコット';
        $record['lastnamephonetic'] = 'フレチャー';
        $record['alternatename'] = 'No friends';
        $user = $this->getDataGenerator()->create_user($record);

        // Back up config settings for restore later.
        $originalcfg = new stdClass();
        $originalcfg->fullnamedisplay = $CFG->fullnamedisplay;
        $originalcfg->alternativefullnameformat = $CFG->alternativefullnameformat;

        // Testing existing fullnamedisplay settings.
        $CFG->fullnamedisplay = 'firstname';
        $testname = fullname($user);
        $this->assertSame($user->firstname, $testname);

        $CFG->fullnamedisplay = 'firstname lastname';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        $CFG->fullnamedisplay = 'lastname firstname';
        $expectedname = "$user->lastname $user->firstname";
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        $expectedname = get_string('fullnamedisplay', null, $user);
        $CFG->fullnamedisplay = 'language';
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        // Test override parameter.
        $CFG->fullnamedisplay = 'firstname';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user, true);
        $this->assertSame($expectedname, $testname);

        // Test alternativefullnameformat setting.
        // Test alternativefullnameformat that has been set to nothing.
        $CFG->alternativefullnameformat = '';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user, true);
        $this->assertSame($expectedname, $testname);

        // Test alternativefullnameformat that has been set to 'language'.
        $CFG->alternativefullnameformat = 'language';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user, true);
        $this->assertSame($expectedname, $testname);

        // Test customising the alternativefullnameformat setting with all additional name fields.
        $CFG->alternativefullnameformat = 'firstname lastname firstnamephonetic lastnamephonetic middlename alternatename';
        $expectedname = "$user->firstname $user->lastname $user->firstnamephonetic $user->lastnamephonetic $user->middlename $user->alternatename";
        $testname = fullname($user, true);
        $this->assertSame($expectedname, $testname);

        // Test additional name fields.
        $CFG->fullnamedisplay = 'lastname lastnamephonetic firstname firstnamephonetic';
        $expectedname = "$user->lastname $user->lastnamephonetic $user->firstname $user->firstnamephonetic";
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        // Test for handling missing data.
        $user->middlename = null;
        // Parenthesis with no data.
        $CFG->fullnamedisplay = 'firstname (middlename) lastname';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        // Extra spaces due to no data.
        $CFG->fullnamedisplay = 'firstname middlename lastname';
        $expectedname = "$user->firstname $user->lastname";
        $testname = fullname($user);
        $this->assertSame($expectedname, $testname);

        // Regular expression testing.
        // Remove some data from the user fields.
        $user->firstnamephonetic = '';
        $user->lastnamephonetic = '';

        // Removing empty brackets and excess whitespace.
        // All of these configurations should resolve to just firstname lastname.
        $configarray = array();
        $configarray[] = 'firstname lastname [firstnamephonetic lastnamephonetic]';
        $configarray[] = 'firstname lastname \'middlename\'';
        $configarray[] = 'firstname "firstnamephonetic" lastname';
        $configarray[] = 'firstname 「firstnamephonetic」 lastname 「lastnamephonetic」';

        foreach ($configarray as $config) {
            $CFG->fullnamedisplay = $config;
            $expectedname = "$user->firstname $user->lastname";
            $testname = fullname($user);
            $this->assertSame($expectedname, $testname);
        }

        // Check to make sure that other characters are left in place.
        $configarray = array();
        $configarray['0'] = new stdClass();
        $configarray['0']->config = 'lastname firstname, middlename';
        $configarray['0']->expectedname = "$user->lastname $user->firstname,";
        $configarray['1'] = new stdClass();
        $configarray['1']->config = 'lastname firstname + alternatename';
        $configarray['1']->expectedname = "$user->lastname $user->firstname + $user->alternatename";
        $configarray['2'] = new stdClass();
        $configarray['2']->config = 'firstname aka: alternatename';
        $configarray['2']->expectedname = "$user->firstname aka: $user->alternatename";
        $configarray['3'] = new stdClass();
        $configarray['3']->config = 'firstname (alternatename)';
        $configarray['3']->expectedname = "$user->firstname ($user->alternatename)";
        $configarray['4'] = new stdClass();
        $configarray['4']->config = 'firstname [alternatename]';
        $configarray['4']->expectedname = "$user->firstname [$user->alternatename]";
        $configarray['5'] = new stdClass();
        $configarray['5']->config = 'firstname "lastname"';
        $configarray['5']->expectedname = "$user->firstname \"$user->lastname\"";

        foreach ($configarray as $config) {
            $CFG->fullnamedisplay = $config->config;
            $expectedname = $config->expectedname;
            $testname = fullname($user);
            $this->assertSame($expectedname, $testname);
        }

        // Test debugging message displays when
        // fullnamedisplay setting is "normal".
        $CFG->fullnamedisplay = 'firstname lastname';
        unset($user);
        $user = new stdClass();
        $user->firstname = 'Stan';
        $user->lastname = 'Lee';
        $namedisplay = fullname($user);
        $this->assertDebuggingCalled();

        // Tidy up after we finish testing.
        $CFG->fullnamedisplay = $originalcfg->fullnamedisplay;
        $CFG->alternativefullnameformat = $originalcfg->alternativefullnameformat;
    }

    public function test_get_all_user_name_fields() {
        $this->resetAfterTest();

        // Additional names in an array.
        $testarray = array('firstnamephonetic' => 'firstnamephonetic',
                'lastnamephonetic' => 'lastnamephonetic',
                'middlename' => 'middlename',
                'alternatename' => 'alternatename',
                'firstname' => 'firstname',
                'lastname' => 'lastname');
        $this->assertEquals($testarray, get_all_user_name_fields());

        // Additional names as a string.
        $teststring = 'firstnamephonetic,lastnamephonetic,middlename,alternatename,firstname,lastname';
        $this->assertEquals($teststring, get_all_user_name_fields(true));

        // Additional names as a string with an alias.
        $teststring = 't.firstnamephonetic,t.lastnamephonetic,t.middlename,t.alternatename,t.firstname,t.lastname';
        $this->assertEquals($teststring, get_all_user_name_fields(true, 't'));

        // Additional name fields with a prefix - object.
        $testarray = array('firstnamephonetic' => 'authorfirstnamephonetic',
                'lastnamephonetic' => 'authorlastnamephonetic',
                'middlename' => 'authormiddlename',
                'alternatename' => 'authoralternatename',
                'firstname' => 'authorfirstname',
                'lastname' => 'authorlastname');
        $this->assertEquals($testarray, get_all_user_name_fields(false, null, 'author'));

        // Additional name fields with an alias and a title - string.
        $teststring = 'u.firstnamephonetic AS authorfirstnamephonetic,u.lastnamephonetic AS authorlastnamephonetic,u.middlename AS authormiddlename,u.alternatename AS authoralternatename,u.firstname AS authorfirstname,u.lastname AS authorlastname';
        $this->assertEquals($teststring, get_all_user_name_fields(true, 'u', null, 'author'));

        // Test the order parameter of the function.
        // Returning an array.
        $testarray = array('firstname' => 'firstname',
                'lastname' => 'lastname',
                'firstnamephonetic' => 'firstnamephonetic',
                'lastnamephonetic' => 'lastnamephonetic',
                'middlename' => 'middlename',
                'alternatename' => 'alternatename'
        );
        $this->assertEquals($testarray, get_all_user_name_fields(false, null, null, null, true));

        // Returning a string.
        $teststring = 'firstname,lastname,firstnamephonetic,lastnamephonetic,middlename,alternatename';
        $this->assertEquals($teststring, get_all_user_name_fields(true, null, null, null, true));
    }

    public function test_order_in_string() {
        $this->resetAfterTest();

        // Return an array in an order as they are encountered in a string.
        $valuearray = array('second', 'firsthalf', 'first');
        $formatstring = 'first firsthalf some other text (second)';
        $expectedarray = array('0' => 'first', '6' => 'firsthalf', '33' => 'second');
        $this->assertEquals($expectedarray, order_in_string($valuearray, $formatstring));

        // Try again with a different order for the format.
        $valuearray = array('second', 'firsthalf', 'first');
        $formatstring = 'firsthalf first second';
        $expectedarray = array('0' => 'firsthalf', '10' => 'first', '16' => 'second');
        $this->assertEquals($expectedarray, order_in_string($valuearray, $formatstring));

        // Try again with yet another different order for the format.
        $valuearray = array('second', 'firsthalf', 'first');
        $formatstring = 'start seconds away second firstquater first firsthalf';
        $expectedarray = array('19' => 'second', '38' => 'first', '44' => 'firsthalf');
        $this->assertEquals($expectedarray, order_in_string($valuearray, $formatstring));
    }

    public function test_complete_user_login() {
        global $USER, $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser(0);

        $sink = $this->redirectEvents();
        $loginuser = clone($user);
        $this->setCurrentTimeStart();
        @complete_user_login($loginuser); // Hide session header errors.
        $this->assertSame($loginuser, $USER);
        $this->assertEquals($user->id, $USER->id);
        $events = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_loggedin', $event);
        $this->assertEquals('user', $event->objecttable);
        $this->assertEquals($user->id, $event->objectid);
        $this->assertEquals(context_system::instance()->id, $event->contextid);
        $this->assertEventContextNotUsed($event);

        $user = $DB->get_record('user', array('id'=>$user->id));

        $this->assertTimeCurrent($user->firstaccess);
        $this->assertTimeCurrent($user->lastaccess);

        $this->assertTimeCurrent($USER->firstaccess);
        $this->assertTimeCurrent($USER->lastaccess);
        $this->assertTimeCurrent($USER->currentlogin);
        $this->assertSame(sesskey(), $USER->sesskey);
        $this->assertTimeCurrent($USER->preference['_lastloaded']);
        $this->assertObjectNotHasAttribute('password', $USER);
        $this->assertObjectNotHasAttribute('description', $USER);
    }

    /**
     * Test require_logout.
     */
    public function test_require_logout() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->assertTrue(isloggedin());

        // Logout user and capture event.
        $sink = $this->redirectEvents();
        require_logout();
        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);

        // Check if user is logged out.
        $this->assertFalse(isloggedin());

        // Test Event.
        $this->assertInstanceOf('\core\event\user_loggedout', $event);
        $this->assertSame($user->id, $event->objectid);
        $this->assertSame('user_logout', $event->get_legacy_eventname());
        $this->assertEventLegacyData($user, $event);
        $expectedlogdata = array(SITEID, 'user', 'logout', 'view.php?id='.$event->objectid.'&course='.SITEID, $event->objectid, 0,
            $event->objectid);
        $this->assertEventLegacyLogData($expectedlogdata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * A data provider for testing email messageid
     */
    public function generate_email_messageid_provider() {
        return array(
            'nopath' => array(
                'wwwroot' => 'http://www.example.com',
                'ids' => array(
                    'a-custom-id' => '<a-custom-id@www.example.com>',
                    'an-id-with-/-a-slash' => '<an-id-with-%2F-a-slash@www.example.com>',
                ),
            ),
            'path' => array(
                'wwwroot' => 'http://www.example.com/path/subdir',
                'ids' => array(
                    'a-custom-id' => '<a-custom-id/path/subdir@www.example.com>',
                    'an-id-with-/-a-slash' => '<an-id-with-%2F-a-slash/path/subdir@www.example.com>',
                ),
            ),
        );
    }

    /**
     * Test email message id generation
     *
     * @dataProvider generate_email_messageid_provider
     *
     * @param string $wwwroot The wwwroot
     * @param array $msgids An array of msgid local parts and the final result
     */
    public function test_generate_email_messageid($wwwroot, $msgids) {
        global $CFG;

        $this->resetAfterTest();
        $CFG->wwwroot = $wwwroot;

        foreach ($msgids as $local => $final) {
            $this->assertEquals($final, generate_email_messageid($local));
        }
    }

    /**
     * A data provider for testing email diversion
     */
    public function diverted_emails_provider() {
        return array(
            'nodiverts' => array(
                'divertallemailsto' => null,
                'divertallemailsexcept' => null,
                array(
                    'foo@example.com',
                    'test@real.com',
                    'fred.jones@example.com',
                    'dev1@dev.com',
                    'fred@example.com',
                    'fred+verp@example.com',
                ),
                false,
            ),
            'alldiverts' => array(
                'divertallemailsto' => 'somewhere@elsewhere.com',
                'divertallemailsexcept' => null,
                array(
                    'foo@example.com',
                    'test@real.com',
                    'fred.jones@example.com',
                    'dev1@dev.com',
                    'fred@example.com',
                    'fred+verp@example.com',
                ),
                true,
            ),
            'alsodiverts' => array(
                'divertallemailsto' => 'somewhere@elsewhere.com',
                'divertallemailsexcept' => '@dev.com, fred(\+.*)?@example.com',
                array(
                    'foo@example.com',
                    'test@real.com',
                    'fred.jones@example.com',
                ),
                true,
            ),
            'divertsexceptions' => array(
                'divertallemailsto' => 'somewhere@elsewhere.com',
                'divertallemailsexcept' => '@dev.com, fred(\+.*)?@example.com',
                array(
                    'dev1@dev.com',
                    'fred@example.com',
                    'fred+verp@example.com',
                ),
                false,
            ),
        );
    }

    /**
     * Test email diversion
     *
     * @dataProvider diverted_emails_provider
     *
     * @param string $divertallemailsto An optional email address
     * @param string $divertallemailsexcept An optional exclusion list
     * @param array $addresses An array of test addresses
     * @param boolean $expected Expected result
     */
    public function test_email_should_be_diverted($divertallemailsto, $divertallemailsexcept, $addresses, $expected) {
        global $CFG;

        $this->resetAfterTest();
        $CFG->divertallemailsto = $divertallemailsto;
        $CFG->divertallemailsexcept = $divertallemailsexcept;

        foreach ($addresses as $address) {
            $this->assertEquals($expected, email_should_be_diverted($address));
        }
    }

    public function test_email_to_user() {
        global $CFG;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $subject = 'subject';
        $messagetext = 'message text';
        $subject2 = 'subject 2';
        $messagetext2 = 'message text 2';

        // Close the default email sink.
        $sink = $this->redirectEmails();
        $sink->close();

        $CFG->noemailever = true;
        $this->assertNotEmpty($CFG->noemailever);
        email_to_user($user1, $user2, $subject, $messagetext);
        $this->assertDebuggingCalled('Not sending email due to $CFG->noemailever config setting');

        unset_config('noemailever');

        email_to_user($user1, $user2, $subject, $messagetext);
        $this->assertDebuggingCalled('Unit tests must not send real emails! Use $this->redirectEmails()');

        $sink = $this->redirectEmails();
        email_to_user($user1, $user2, $subject, $messagetext);
        email_to_user($user2, $user1, $subject2, $messagetext2);
        $this->assertSame(2, $sink->count());
        $result = $sink->get_messages();
        $this->assertCount(2, $result);
        $sink->close();

        $this->assertSame($subject, $result[0]->subject);
        $this->assertSame($messagetext, trim($result[0]->body));
        $this->assertSame($user1->email, $result[0]->to);
        $this->assertSame($user2->email, $result[0]->from);

        $this->assertSame($subject2, $result[1]->subject);
        $this->assertSame($messagetext2, trim($result[1]->body));
        $this->assertSame($user2->email, $result[1]->to);
        $this->assertSame($user1->email, $result[1]->from);

        email_to_user($user1, $user2, $subject, $messagetext);
        $this->assertDebuggingCalled('Unit tests must not send real emails! Use $this->redirectEmails()');

        // Test $CFG->emailonlyfromnoreplyaddress.
        set_config('emailonlyfromnoreplyaddress', 1);
        $this->assertNotEmpty($CFG->emailonlyfromnoreplyaddress);
        $sink = $this->redirectEmails();
        email_to_user($user1, $user2, $subject, $messagetext);
        unset_config('emailonlyfromnoreplyaddress');
        email_to_user($user1, $user2, $subject, $messagetext);
        $result = $sink->get_messages();
        $this->assertEquals($CFG->noreplyaddress, $result[0]->from);
        $this->assertNotEquals($CFG->noreplyaddress, $result[1]->from);
        $sink->close();
    }

    /**
     * Test setnew_password_and_mail.
     */
    public function test_setnew_password_and_mail() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        // Update user password.
        $sink = $this->redirectEvents();
        $sink2 = $this->redirectEmails(); // Make sure we are redirecting emails.
        setnew_password_and_mail($user);
        $events = $sink->get_events();
        $sink->close();
        $sink2->close();
        $event = array_pop($events);

        // Test updated value.
        $dbuser = $DB->get_record('user', array('id' => $user->id));
        $this->assertSame($user->firstname, $dbuser->firstname);
        $this->assertNotEmpty($dbuser->password);

        // Test event.
        $this->assertInstanceOf('\core\event\user_password_updated', $event);
        $this->assertSame($user->id, $event->relateduserid);
        $this->assertEquals(context_user::instance($user->id), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test remove_course_content deletes course contents
     * TODO Add asserts to verify other data related to course is deleted as well.
     */
    public function test_remove_course_contents() {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $gen = $this->getDataGenerator()->get_plugin_generator('core_notes');
        $note = $gen->create_instance(array('courseid' => $course->id, 'userid' => $user->id));

        $this->assertNotEquals(false, note_load($note->id));
        remove_course_contents($course->id, false);
        $this->assertFalse(note_load($note->id));
    }

    /**
     * Test function username_load_fields_from_object().
     */
    public function test_username_load_fields_from_object() {
        $this->resetAfterTest();

        // This object represents the information returned from an sql query.
        $userinfo = new stdClass();
        $userinfo->userid = 1;
        $userinfo->username = 'loosebruce';
        $userinfo->firstname = 'Bruce';
        $userinfo->lastname = 'Campbell';
        $userinfo->firstnamephonetic = 'ブルース';
        $userinfo->lastnamephonetic = 'カンベッル';
        $userinfo->middlename = '';
        $userinfo->alternatename = '';
        $userinfo->email = '';
        $userinfo->picture = 23;
        $userinfo->imagealt = 'Michael Jordan draining another basket.';
        $userinfo->idnumber = 3982;

        // Just user name fields.
        $user = new stdClass();
        $user = username_load_fields_from_object($user, $userinfo);
        $expectedarray = new stdClass();
        $expectedarray->firstname = 'Bruce';
        $expectedarray->lastname = 'Campbell';
        $expectedarray->firstnamephonetic = 'ブルース';
        $expectedarray->lastnamephonetic = 'カンベッル';
        $expectedarray->middlename = '';
        $expectedarray->alternatename = '';
        $this->assertEquals($user, $expectedarray);

        // User information for showing a picture.
        $user = new stdClass();
        $additionalfields = explode(',', user_picture::fields());
        $user = username_load_fields_from_object($user, $userinfo, null, $additionalfields);
        $user->id = $userinfo->userid;
        $expectedarray = new stdClass();
        $expectedarray->id = 1;
        $expectedarray->firstname = 'Bruce';
        $expectedarray->lastname = 'Campbell';
        $expectedarray->firstnamephonetic = 'ブルース';
        $expectedarray->lastnamephonetic = 'カンベッル';
        $expectedarray->middlename = '';
        $expectedarray->alternatename = '';
        $expectedarray->email = '';
        $expectedarray->picture = 23;
        $expectedarray->imagealt = 'Michael Jordan draining another basket.';
        $this->assertEquals($user, $expectedarray);

        // Alter the userinfo object to have a prefix.
        $userinfo->authorfirstname = 'Bruce';
        $userinfo->authorlastname = 'Campbell';
        $userinfo->authorfirstnamephonetic = 'ブルース';
        $userinfo->authorlastnamephonetic = 'カンベッル';
        $userinfo->authormiddlename = '';
        $userinfo->authorpicture = 23;
        $userinfo->authorimagealt = 'Michael Jordan draining another basket.';
        $userinfo->authoremail = 'test@example.com';


        // Return an object with user picture information.
        $user = new stdClass();
        $additionalfields = explode(',', user_picture::fields());
        $user = username_load_fields_from_object($user, $userinfo, 'author', $additionalfields);
        $user->id = $userinfo->userid;
        $expectedarray = new stdClass();
        $expectedarray->id = 1;
        $expectedarray->firstname = 'Bruce';
        $expectedarray->lastname = 'Campbell';
        $expectedarray->firstnamephonetic = 'ブルース';
        $expectedarray->lastnamephonetic = 'カンベッル';
        $expectedarray->middlename = '';
        $expectedarray->alternatename = '';
        $expectedarray->email = 'test@example.com';
        $expectedarray->picture = 23;
        $expectedarray->imagealt = 'Michael Jordan draining another basket.';
        $this->assertEquals($user, $expectedarray);
    }

    /**
     * Test function count_words().
     */
    public function test_count_words() {
        $count = count_words("one two three'four");
        $this->assertEquals(3, $count);

        $count = count_words('one+two three’four');
        $this->assertEquals(3, $count);

        $count = count_words('one"two three-four');
        $this->assertEquals(2, $count);

        $count = count_words('one@two three_four');
        $this->assertEquals(4, $count);

        $count = count_words('one\two three/four');
        $this->assertEquals(4, $count);

        $count = count_words(' one ... two &nbsp; three...four ');
        $this->assertEquals(4, $count);

        $count = count_words('one.2 3,four');
        $this->assertEquals(4, $count);

        $count = count_words('1³ £2 €3.45 $6,789');
        $this->assertEquals(4, $count);

        $count = count_words('one—two ブルース カンベッル');
        $this->assertEquals(4, $count);

        $count = count_words('one…two ブルース … カンベッル');
        $this->assertEquals(4, $count);
    }
    /**
     * Tests the getremoteaddr() function.
     */
    public function test_getremoteaddr() {
        $xforwardedfor = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $noip = getremoteaddr('1.1.1.1');
        $this->assertEquals('1.1.1.1', $noip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $noip = getremoteaddr();
        $this->assertEquals('0.0.0.0', $noip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
        $singleip = getremoteaddr();
        $this->assertEquals('127.0.0.1', $singleip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1,127.0.0.2';
        $twoip = getremoteaddr();
        $this->assertEquals('127.0.0.1', $twoip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1,127.0.0.2, 127.0.0.3';
        $threeip = getremoteaddr();
        $this->assertEquals('127.0.0.1', $threeip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1:65535,127.0.0.2';
        $portip = getremoteaddr();
        $this->assertEquals('127.0.0.1', $portip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '0:0:0:0:0:0:0:1,127.0.0.2';
        $portip = getremoteaddr();
        $this->assertEquals('0:0:0:0:0:0:0:1', $portip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '0::1,127.0.0.2';
        $portip = getremoteaddr();
        $this->assertEquals('0:0:0:0:0:0:0:1', $portip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '[0:0:0:0:0:0:0:1]:65535,127.0.0.2';
        $portip = getremoteaddr();
        $this->assertEquals('0:0:0:0:0:0:0:1', $portip);

        $_SERVER['HTTP_X_FORWARDED_FOR'] = $xforwardedfor;

    }

    /*
     * Test emulation of random_bytes() function.
     */
    public function test_random_bytes_emulate() {
        $result = random_bytes_emulate(10);
        $this->assertSame(10, strlen($result));
        $this->assertnotSame($result, random_bytes_emulate(10));

        $result = random_bytes_emulate(21);
        $this->assertSame(21, strlen($result));
        $this->assertnotSame($result, random_bytes_emulate(21));

        $result = random_bytes_emulate(666);
        $this->assertSame(666, strlen($result));

        $result = random_bytes_emulate(40);
        $this->assertSame(40, strlen($result));

        $this->assertDebuggingNotCalled();

        $result = random_bytes_emulate(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = random_bytes_emulate(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }

    /**
     * Test function for creation of random strings.
     */
    public function test_random_string() {
        $pool = 'a-zA-Z0-9';

        $result = random_string(10);
        $this->assertSame(10, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, random_string(10));

        $result = random_string(21);
        $this->assertSame(21, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, random_string(21));

        $result = random_string(666);
        $this->assertSame(666, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $result = random_string();
        $this->assertSame(15, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $this->assertDebuggingNotCalled();

        $result = random_string(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = random_string(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }

    /**
     * Test function for creation of complex random strings.
     */
    public function test_complex_random_string() {
        $pool = preg_quote('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#%^&*()_+-=[];,./<>?:{} ', '/');

        $result = complex_random_string(10);
        $this->assertSame(10, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, complex_random_string(10));

        $result = complex_random_string(21);
        $this->assertSame(21, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, complex_random_string(21));

        $result = complex_random_string(666);
        $this->assertSame(666, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $result = complex_random_string();
        $this->assertEquals(28, strlen($result), '', 4); // Expected length is 24 - 32.
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $this->assertDebuggingNotCalled();

        $result = complex_random_string(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = complex_random_string(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }

    /**
     * Test that generate_email_processing_address() returns valid email address.
     */
    public function test_generate_email_processing_address() {
        global $CFG;
        $this->resetAfterTest();

        $data = (object)[
            'id' => 42,
            'email' => 'my.email+from_moodle@example.com',
        ];

        $modargs = 'B'.base64_encode(pack('V', $data->id)).substr(md5($data->email), 0, 16);

        $CFG->maildomain = 'example.com';
        $CFG->mailprefix = 'mdl+';
        $this->assertEquals(1, validate_email(generate_email_processing_address(0, $modargs)));

        $CFG->maildomain = 'mail.example.com';
        $CFG->mailprefix = 'mdl-';
        $this->assertEquals(1, validate_email(generate_email_processing_address(23, $modargs)));
    }
}
