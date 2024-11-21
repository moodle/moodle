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
 * Test case.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\tests\base_testcase;
use Generator;

defined('MOODLE_INTERNAL') || die();

global $CFG, $DB;

/**
 * Test case.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class php_test extends base_testcase {

    /**
     * PHP files provider.
     *
     * @return array
     */
    public static function php_files_provider(): Generator {
        global $CFG;

        $xproot = $CFG->dirroot . '/blocks/xp';

        // Retrieve all files, except db/ and backup/ ones which have variable and include dependencies.
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_PATHNAME;
        $browser = new \AppendIterator();
        $browser->append(new \FilesystemIterator($xproot, $flags));
        $browser->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($xproot . '/classes', $flags)));
        $browser->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($xproot . '/lang', $flags)));
        $browser = new \RegexIterator($browser, '/\.php$/');

        // Ignore the files that are including the config, depending on local variables, or deprecated.
        $ignorefiles = ['/ajax.php', '/index.php', '/settings.php', '/version.php', '/classes/external.php'];

        foreach ($browser as $file) {
            $relpath = str_replace($xproot, '', $file);
            if (in_array($relpath, $ignorefiles)) {
                continue;
            }
            yield [$relpath];
        }
    }

    /**
     * Test inclusion of files.
     *
     * This is an attempt to detect whether we used a syntax that is not valid with
     * other PHP versions. It would make the tests fail entirely.
     *
     * @dataProvider php_files_provider
     * @covers \block_xp\di
     */
    public function test_file_inclusion($relpath): void {
        global $CFG, $DB;
        try {
            require_once($CFG->dirroot . '/blocks/xp' . $relpath);
        } catch (\Throwable $e) {
            $this->fail("Failed to include file: $relpath");
        }
    }

}
