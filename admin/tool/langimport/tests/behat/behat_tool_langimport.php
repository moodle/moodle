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
 * Behat steps definitions for Language import tool
 *
 * @package   tool_langimport
 * @category  test
 * @copyright 2014 Dan Poltawski <dan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Moodle\BehatExtension\Exception\SkippedException;

/**
 * Steps definitions related with the Language import tool
 *
 * @package   tool_langimport
 * @category  test
 * @copyright 2014 Dan Poltawski <dan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_langimport extends behat_base {

    /**
     * This step looks to see if the remote language import tests should be run (indicated by
     * setting TOOL_LANGIMPORT_REMOTE_TESTS in config.php.
     *
     * @Given /^remote langimport tests are enabled$/
     */
    public function remote_langimport_tests_are_enabled() {
        if (!defined('TOOL_LANGIMPORT_REMOTE_TESTS')) {
            throw new SkippedException('To run the remote langimport tests you must '.
                'define TOOL_LANGIMPORT_REMOTE_TESTS in config.php');
        }
    }

    /**
     * Downloads a langpack and fakes it being outdated
     *
     * @param string $langcode The language code (e.g. en)
     * @Given /^outdated langpack \'([^\']*)\' is installed$/
     */
    public function outdated_langpack_is_installed($langcode) {
        global $CFG;
        require_once($CFG->libdir.'/componentlib.class.php');

        // Download the langpack.
        $dir = make_upload_directory('lang');
        $installer = new lang_installer($langcode);
        $result = $installer->run();

        if ($result[$langcode] !== lang_installer::RESULT_INSTALLED) {
            throw new coding_exception("Failed to install langpack '$langcode'");
        }

        $path = "$dir/$langcode/$langcode.md5";

        if (!file_exists($path)) {
            throw new coding_exception("Failed to find '$langcode' checksum");
        }
        file_put_contents($path, '000000');
    }
}
