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

namespace core;

use testable_core_update_validator;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/testable_update_validator.php');

/**
 * Unit tests for the {@link \core\update\validator} class
 *
 * @package   core
 * @category  test
 * @copyright 2013, 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class update_validator_test extends \advanced_testcase {

    public function test_validate_files_layout(): void {
        $fixtures = __DIR__.'/fixtures/update_validator';

        // Non-existing directory.
        $validator = testable_core_update_validator::instance($fixtures.'/nulldir', array(
            'null/' => true,
            'null/lang/' => true,
            'null/lang/en/' => true,
            'null/lang/en/null.php' => true));
        $this->assertEquals('testable_core_update_validator', get_class($validator));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR,
            'filenotexists', array('file' => 'null/')));

        // Missing expected file.
        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'foobar/' => true,
            'foobar/version.php' => true,
            'foobar/index.php' => true,
            'foobar/lang/' => true,
            'foobar/lang/en/' => true,
            'foobar/lang/en/local_foobar.php' => true,
            'foobar/NOTEXISTS.txt' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR,
            'filenotexists', array('file' => 'foobar/NOTEXISTS.txt')));

        // Errors during ZIP extraction.
        $validator = testable_core_update_validator::instance($fixtures.'/multidir', array(
            'one/' => true,
            'one/version.php' => 'Can not write target file',
            'two/' => true,
            'two/README.txt' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'filestatus',
            array('file' => 'one/version.php', 'status' => 'Can not write target file')));

        // Insufficient number of extracted files.
        $validator = testable_core_update_validator::instance($fixtures.'/emptydir', array(
            'emptydir/' => true,
            'emptydir/README.txt' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'filesnumber'));

        // No wrapping directory.
        $validator = testable_core_update_validator::instance($fixtures.'/nowrapdir', array(
            'version.php' => true,
            'index.php' => true,
            'lang/' => true,
            'lang/en/' => true,
            'lang/en/foo.php' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'onedir'));

        // Multiple directories.
        $validator = testable_core_update_validator::instance($fixtures.'/multidir', array(
            'one/' => true,
            'one/version.php' => true,
            'two/' => true,
            'two/README.txt' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'onedir'));

        // Invalid root directory name.
        $validator = testable_core_update_validator::instance($fixtures.'/github', array(
            'moodle-repository_mahara-master/' => true,
            'moodle-repository_mahara-master/lang/' => true,
            'moodle-repository_mahara-master/lang/en/' => true,
            'moodle-repository_mahara-master/lang/en/repository_mahara.php' => true,
            'moodle-repository_mahara-master/version.php' => true));
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'rootdirinvalid',
            'moodle-repository_mahara-master'));
    }

    public function test_validate_version_php(): void {
        $fixtures = __DIR__.'/fixtures/update_validator';

        $validator = testable_core_update_validator::instance($fixtures.'/noversiontheme', array(
            'noversion/' => true,
            'noversion/lang/' => true,
            'noversion/lang/en/' => true,
            'noversion/lang/en/theme_noversion.php' => true));
        $validator->assert_plugin_type('theme');
        $validator->assert_moodle_version(0);
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::DEBUG, 'missingversionphp'));
        $this->assertTrue(is_null($validator->get_versionphp_info()));

        $validator = testable_core_update_validator::instance($fixtures.'/noversionmod', array(
            'noversion/' => true,
            'noversion/lang/' => true,
            'noversion/lang/en/' => true,
            'noversion/lang/en/noversion.php' => true));
        $validator->assert_plugin_type('mod');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'missingversionphp'));

        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'legacymod/' => true,
            'legacymod/version.php' => true,
            'legacymod/lang/' => true,
            'legacymod/lang/en/' => true,
            'legacymod/lang/en/legacymod.php' => true));
        $validator->assert_plugin_type('mod');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'versionphpsyntax', '$module'));

        $validator = testable_core_update_validator::instance($fixtures.'/nocomponent', array(
            'baz/' => true,
            'baz/version.php' => true,
            'baz/lang/' => true,
            'baz/lang/en/' => true,
            'baz/lang/en/auth_baz.php' => true));
        $validator->assert_plugin_type('auth');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'missingcomponent'));

        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'foobar/' => true,
            'foobar/version.php' => true,
            'foobar/index.php' => true,
            'foobar/lang/' => true));
        $validator->assert_plugin_type('block');
        $validator->assert_moodle_version('2013031400.00');
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'componentmismatchtype',
            array('expected' => 'block', 'found' => 'local')));

        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'foobar/' => true,
            'foobar/version.php' => true,
            'foobar/index.php' => true,
            'foobar/lang/' => true,
            'foobar/lang/en/' => true,
            'foobar/lang/en/local_foobar.php' => true));
        $validator->assert_plugin_type('local');
        $validator->assert_moodle_version('2013031400.00');
        $this->assertTrue($validator->execute());
        $this->assertTrue($validator->get_result());
        $this->assertEquals('foobar', $validator->get_rootdir());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::INFO, 'rootdir', 'foobar'));
        $versionphpinfo = $validator->get_versionphp_info();
        $this->assertIsArray($versionphpinfo);
        $this->assertCount(4, $versionphpinfo);
        $this->assertEquals(2013031900, $versionphpinfo['version']);
        $this->assertEquals(2013031200, $versionphpinfo['requires']);
        $this->assertEquals('local_foobar', $versionphpinfo['component']);
        $this->assertEquals('MATURITY_ALPHA', $versionphpinfo['maturity']); // Note we get the constant name here.
        $this->assertEquals(MATURITY_ALPHA, constant($versionphpinfo['maturity'])); // This is how to get the real value.
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::WARNING, 'maturity', 'MATURITY_ALPHA'));
    }

    public function test_validate_language_pack(): void {
        $fixtures = __DIR__.'/fixtures/update_validator';

        $validator = testable_core_update_validator::instance($fixtures.'/nolang', array(
            'bah/' => true,
            'bah/index.php' => true,
            'bah/view.php' => true,
            'bah/version.php' => true));
        $validator->assert_plugin_type('mod');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'missinglangenfolder'));

        $validator = testable_core_update_validator::instance($fixtures.'/nolang', array(
            'bah/' => true,
            'bah/version.php' => true,
            'bah/lang/' => true,
            'bah/lang/en/' => true));
        $validator->assert_plugin_type('mod');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'missinglangenfile'));

        $validator = testable_core_update_validator::instance($fixtures.'/nolang', array(
            'bah/' => true,
            'bah/version.php' => true,
            'bah/lang/' => true,
            'bah/lang/en/' => true,
            'bah/lang/en/bleh.php' => true,
            'bah/lang/en/bah.php' => true));
        $validator->assert_plugin_type('mod');
        $validator->assert_moodle_version(0);
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::WARNING, 'multiplelangenfiles'));
        $this->assertTrue(is_null($validator->get_language_file_name()));

        $validator = testable_core_update_validator::instance($fixtures.'/wronglang', array(
            'bah/' => true,
            'bah/version.php' => true,
            'bah/lang/' => true,
            'bah/lang/en/' => true,
            'bah/lang/en/bah.php' => true));
        $validator->assert_plugin_type('block');
        $validator->assert_moodle_version(0);
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'missingexpectedlangenfile',
            'block_bah.php'));
        $this->assertEquals('bah', $validator->get_language_file_name());

        $validator = testable_core_update_validator::instance($fixtures.'/noversiontheme', array(
            'noversion/' => true,
            'noversion/lang/' => true,
            'noversion/lang/en/' => true,
            'noversion/lang/en/theme_noversion.php' => true));
        $validator->assert_plugin_type('theme');
        $validator->assert_moodle_version(0);
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::DEBUG, 'foundlangfile', 'theme_noversion'));
        $this->assertEquals('theme_noversion', $validator->get_language_file_name());

        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'foobar/' => true,
            'foobar/version.php' => true,
            'foobar/index.php' => true,
            'foobar/lang/' => true,
            'foobar/lang/en/' => true,
            'foobar/lang/en/local_foobar.php' => true));
        $validator->assert_plugin_type('local');
        $validator->assert_moodle_version('2013031400.00');
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::DEBUG, 'foundlangfile', 'local_foobar'));
        $this->assertEquals('local_foobar', $validator->get_language_file_name());
    }

    public function test_validate_target_location(): void {
        $fixtures = __DIR__.'/fixtures/update_validator';

        $validator = testable_core_update_validator::instance($fixtures.'/installed', array(
            'greenbar/' => true,
            'greenbar/version.php' => true,
            'greenbar/index.php' => true,
            'greenbar/lang/' => true,
            'greenbar/lang/en/' => true,
            'greenbar/lang/en/local_greenbar.php' => true));
        $validator->assert_plugin_type('local');
        $validator->assert_moodle_version('2013031400.00');

        $this->assertTrue($validator->execute());
        $this->assertFalse($this->has_message($validator->get_messages(), $validator::WARNING, 'targetexists',
            $validator->get_plugintype_location('local').'/greenbar'));

        $typeroot = $validator->get_plugintype_location('local');
        make_writable_directory($typeroot.'/greenbar');
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::WARNING, 'targetexists',
            $validator->get_plugintype_location('local').'/greenbar'));

        remove_dir($typeroot.'/greenbar');
        file_put_contents($typeroot.'/greenbar', 'This file occupies a place where a plugin should land.');
        $this->assertFalse($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::ERROR, 'targetnotdir',
            $validator->get_plugintype_location('local').'/greenbar'));

        unlink($typeroot.'/greenbar');
        $this->assertTrue($validator->execute());

        $validator = testable_core_update_validator::instance($fixtures.'/plugindir', array(
            'foobar/' => true,
            'foobar/version.php' => true,
            'foobar/index.php' => true,
            'foobar/lang/' => true,
            'foobar/lang/en/' => true,
            'foobar/lang/en/local_foobar.php' => true));
        $validator->assert_plugin_type('local');
        $validator->assert_moodle_version('2013031400.00');
        $this->assertTrue($validator->execute());
        $this->assertTrue($this->has_message($validator->get_messages(), $validator::INFO, 'pathwritable',
            $validator->get_plugintype_location('local')));
    }

    public function test_parse_version_php(): void {
        $fixtures = __DIR__.'/fixtures/update_validator/versionphp';

        $validator = testable_core_update_validator::instance($fixtures, array());
        $this->assertEquals('testable_core_update_validator', get_class($validator));

        $info = $validator->testable_parse_version_php($fixtures.'/version1.php');
        $this->assertIsArray($info);
        $this->assertCount(7, $info);
        $this->assertEquals('block_foobar', $info['plugin->component']);  // Later in the file.
        $this->assertEquals('2013010100', $info['plugin->version']);      // Numeric wins over strings.
        $this->assertEquals('2012122401', $info['plugin->requires']);     // Commented.
        $this->assertEquals('MATURITY_STABLE', $info['module->maturity']);// Constant wins regardless the order (non-PHP behaviour).
        $this->assertEquals('MATURITY_ALPHA', $info['plugin->maturity']); // Constant wins regardless the order (non-PHP behaviour).
        $this->assertEquals('v2.3', $info['module->release']);            // String wins over numeric (non-PHP behaviour).
        $this->assertEquals('v2.4', $info['plugin->release']);            // String wins over numeric (non-PHP behaviour).
    }

    public function test_messages_output(): void {
        $fixtures = __DIR__.'/fixtures/update_validator';
        $validator = testable_core_update_validator::instance($fixtures, array());

        $this->assertDebuggingNotCalled();
        $this->assertNotEmpty($validator->message_level_name($validator::ERROR));
        $this->assertNotEmpty($validator->message_level_name($validator::WARNING));
        $this->assertNotEmpty($validator->message_level_name($validator::INFO));
        $this->assertNotEmpty($validator->message_level_name($validator::DEBUG));

        $this->assertNotEmpty($validator->message_code_name('missingversion'));
        $this->assertSame(
            'some_really_crazy_message_code_that_is_not_localised',
            $validator->message_code_name('some_really_crazy_message_code_that_is_not_localised')
        );

        $this->assertInstanceOf('help_icon', $validator->message_help_icon('onedir'));
        $this->assertFalse($validator->message_help_icon('some_really_crazy_message_code_that_is_not_localised'));

        $this->assertNotEmpty($validator->message_code_info('missingexpectedlangenfile', 'something'));
        $this->assertSame('', $validator->message_code_info('missingexpectedlangenfile', null));
        $this->assertSame('', $validator->message_code_info('some_really_crazy_message_code_that_is_not_localised', 'something'));
    }

    /**
     * Helper method for checking if the given message has been raised by the validator.
     *
     * @param array $messages list of returned messages
     * @param string $level expected message severity level
     * @param string $msgcode expected message code
     * @param string|array $addinfo expected additional info
     * @return bool
     */
    protected function has_message(array $messages, $level, $msgcode, $addinfo = null) {
        foreach ($messages as $message) {
            if ($message->level === $level and $message->msgcode === $msgcode and $message->addinfo === $addinfo) {
                return true;
            }
        }
        return false;
    }
}
