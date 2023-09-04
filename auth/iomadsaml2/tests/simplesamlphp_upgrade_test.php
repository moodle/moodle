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
 * auth_iomadsaml2 SimpleSAMLphp upgrade unit tests
 *
 * @package    auth_iomadsaml2
 * @copyright  Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * auth_iomadsaml2 SimpleSAMLphp upgrade unit tests
 *
 * @package    auth_iomadsaml2
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_simplesamlphp_upgrade_testcase extends advanced_testcase {

    /**
     * Test to ensure that composer files are removed from compiled extlib/simplesamlphp.
     */
    public function test_remove_composer_files_from_compiled_extlib_simplesamlphp() {
        $this->resetAfterTest();

        $filenames = [
            "auth/iomadsaml2/.extlib/simplesamlphp/composer.json",
            "auth/iomadsaml2/.extlib/simplesamlphp/composer.lock",
            "auth/iomadsaml2/.extlib/simplesamlphp/modules/.gitignore",
        ];

        foreach ($filenames as $filename) {
            // Backwards compatibility with older PHPUnit - use old assertFile method.
            if (method_exists($this, 'assertFileDoesNotExist')) {
                $this->assertFileDoesNotExist($filename);
            } else {
                $this->assertFileNotExists($filename);
            }
        }
    }

    /**
     * Test to ensure that PHPMailer are removed from autoloaded files.
     */
    public function test_remove_phpmailer_from_autoloaded_files() {
        global $CFG;
        $this->resetAfterTest();

        // Backwards compatibility with older PHPUnit - use old assertDirectory method.
        if (method_exists($this, 'assertDirectoryDoesNotExist')) {
            $this->assertDirectoryDoesNotExist($CFG->dirroot."/auth/iomadsaml2/.extlib/simplesamlphp/vendor/phpmailer");
        } else {
            $this->assertDirectoryNotExists($CFG->dirroot."/auth/iomadsaml2/.extlib/simplesamlphp/vendor/phpmailer");
        }

        $filenames = [
            $CFG->dirroot."/auth/iomadsaml2/.extlib/simplesamlphp/vendor/composer/autoload_psr4.php",
            $CFG->dirroot."/auth/iomadsaml2/.extlib/simplesamlphp/vendor/composer/autoload_static.php",
            $CFG->dirroot."/auth/iomadsaml2/.extlib/simplesamlphp/vendor/composer/installed.json",
        ];

        foreach ($filenames as $filename) {
            $this->assertFalse(strpos(file_get_contents($filename), "PHPMailer\\\\PHPMailer\\\\"));
            $this->assertFalse(strpos(file_get_contents($filename), "PHPMailer\\\\Test\\\\"));
        }
    }
}
