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
 * Unit tests for /lib/componentlib.class.php.
 *
 * @package   core
 * @category  test
 * @copyright 2011 Tomasz Muras
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use component_installer;
use lang_installer;
use lang_installer_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/componentlib.class.php');

/**
 * Unit tests for /lib/componentlib.class.php.
 *
 * @package   core
 * @category  test
 * @copyright 2011 Tomasz Muras
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class componentlib_test extends \advanced_testcase {

    public function test_component_installer(): void {
        global $CFG;

        $url = $this->getExternalTestFileUrl('');
        $ci = new component_installer($url, '', 'downloadtests.zip');
        $this->assertTrue($ci->check_requisites());

        $destpath = $CFG->dataroot.'/downloadtests';

        // Carefully remove component files to enforce fresh installation.
        @unlink($destpath.'/'.'downloadtests.md5');
        @unlink($destpath.'/'.'test.html');
        @unlink($destpath.'/'.'test.jpg');
        @rmdir($destpath);

        $this->assertSame(COMPONENT_NEEDUPDATE, $ci->need_upgrade());

        $status = $ci->install();
        $this->assertSame(COMPONENT_INSTALLED, $status);
        $this->assertSame('9e94f74b3efb1ff6cf075dc6b2abf15c', $ci->get_component_md5());

        // It's already installed, so Moodle should detect it's up to date.
        $this->assertSame(COMPONENT_UPTODATE, $ci->need_upgrade());
        $status = $ci->install();
        $this->assertSame(COMPONENT_UPTODATE, $status);

        // Check if correct files were downloaded.
        $this->assertSame('2af180e813dc3f446a9bb7b6af87ce24', md5_file($destpath.'/'.'test.jpg'));
        $this->assertSame('47250a973d1b88d9445f94db4ef2c97a', md5_file($destpath.'/'.'test.html'));
    }

    /**
     * Test the public API of the {@link lang_installer} class.
     */
    public function test_lang_installer(): void {

        // Test the manipulation with the download queue.
        $installer = new testable_lang_installer();
        $this->assertFalse($installer->protected_is_queued());
        $installer->protected_add_to_queue('cs');
        $installer->protected_add_to_queue(array('cs', 'sk'));
        $this->assertTrue($installer->protected_is_queued());
        $this->assertTrue($installer->protected_is_queued('cs'));
        $this->assertTrue($installer->protected_is_queued('sk'));
        $this->assertFalse($installer->protected_is_queued('de_kids'));
        $installer->set_queue('de_kids');
        $this->assertFalse($installer->protected_is_queued('cs'));
        $this->assertFalse($installer->protected_is_queued('sk'));
        $this->assertFalse($installer->protected_is_queued('de'));
        $this->assertFalse($installer->protected_is_queued('de_du'));
        $this->assertTrue($installer->protected_is_queued('de_kids'));
        $installer->set_queue(array('cs', 'de_kids'));
        $this->assertTrue($installer->protected_is_queued('cs'));
        $this->assertFalse($installer->protected_is_queued('sk'));
        $this->assertFalse($installer->protected_is_queued('de'));
        $this->assertFalse($installer->protected_is_queued('de_du'));
        $this->assertTrue($installer->protected_is_queued('de_kids'));
        $installer->set_queue(array());
        $this->assertFalse($installer->protected_is_queued());
        unset($installer);

        // Install a set of lang packs.
        $installer = new testable_lang_installer(array('cs', 'de_kids', 'xx'));
        $result = $installer->run();
        $this->assertSame($result['cs'], lang_installer::RESULT_UPTODATE);
        $this->assertSame($result['de_kids'], lang_installer::RESULT_INSTALLED);
        $this->assertSame($result['xx'], lang_installer::RESULT_DOWNLOADERROR);

        // The following two were automatically added to the queue.
        $this->assertSame($result['de_du'], lang_installer::RESULT_INSTALLED);
        $this->assertSame($result['de'], lang_installer::RESULT_UPTODATE);

        // Exception throwing.
        $installer = new testable_lang_installer(array('yy'));
        try {
            $installer->run();
            $this->fail('lang_installer_exception exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('lang_installer_exception', $e);
        }
    }
}


/**
 * Testable lang_installer subclass that does not actually install anything
 * and provides access to the protected methods of the parent class
 *
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_lang_installer extends lang_installer {

    /**
     * @see parent::is_queued()
     */
    public function protected_is_queued($langcode = '') {
        return $this->is_queued($langcode);
    }

    /**
     * @see parent::add_to_queue()
     */
    public function protected_add_to_queue($langcodes) {
        return $this->add_to_queue($langcodes);
    }

    /**
     * Simulate lang pack installation via component_installer.
     *
     * Language packages 'de_du' and 'de_kids' reported as installed
     * Language packages 'cs' and 'de' reported as up-to-date
     * Language package 'xx' returns download error
     * All other language packages will throw an unknown exception
     *
     * @see parent::install_language_pack()
     */
    protected function install_language_pack($langcode) {

        switch ($langcode) {
            case 'de_du':
            case 'de_kids':
                return self::RESULT_INSTALLED;

            case 'cs':
            case 'de':
                return self::RESULT_UPTODATE;

            case 'xx':
                return self::RESULT_DOWNLOADERROR;

            default:
                throw new lang_installer_exception('testing-unknown-exception', $langcode);
        }
    }

    /**
     * Simulate detection of parent language.
     *
     * @see parent::get_parent_language()
     */
    protected function get_parent_language($langcode) {

        switch ($langcode) {
            case 'de_kids':
                return 'de_du';
            case 'de_du':
                return 'de';
            default:
                return '';
        }
    }
}
