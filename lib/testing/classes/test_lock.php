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
 * Tests lock
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../lib.php');

/**
 * Tests lock to prevent concurrent executions of the same test suite
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_lock {

    /**
     * @var array Array of resource used for prevention of parallel test execution
     */
    protected static $lockhandles = array();

    /**
     * Prevent parallel test execution - this can not work in Moodle because we modify database and dataroot.
     *
     * Note: do not call manually!
     *
     * @internal
     * @static
     * @param   string $framework phpunit|behat
     * @param   string $lockfilesuffix A sub-type used by the framework
     * @return  void
     */
    public static function acquire(string $framework, string $lockfilesuffix = '') {
        global $CFG;

        $datarootpath = $CFG->{$framework . '_dataroot'} . '/' . $framework;
        $lockfile = "{$datarootpath}/lock{$lockfilesuffix}";
        if (!file_exists($datarootpath)) {
            // Dataroot not initialised yet.
            return;
        }
        if (!file_exists($lockfile)) {
            file_put_contents($lockfile, 'This file prevents concurrent execution of Moodle ' . $framework . ' tests');
            testing_fix_file_permissions($lockfile);
        }

        $lockhandlename = self::get_lock_handle_name($framework, $lockfilesuffix);
        if (self::$lockhandles[$lockhandlename] = fopen($lockfile, 'r')) {
            $wouldblock = null;
            $locked = flock(self::$lockhandles[$lockhandlename], (LOCK_EX | LOCK_NB), $wouldblock);
            if (!$locked) {
                if ($wouldblock) {
                    echo "Waiting for other test execution to complete...\n";
                }
                $locked = flock(self::$lockhandles[$lockhandlename], LOCK_EX);
            }
            if (!$locked) {
                fclose(self::$lockhandles[$lockhandlename]);
                self::$lockhandles[$lockhandlename] = null;
            }
        }
        register_shutdown_function(['test_lock', 'release'], $framework, $lockfilesuffix);
    }

    /**
     * Note: do not call manually!
     * @internal
     * @static
     * @param   string $framework phpunit|behat
     * @param   string $lockfilesuffix A sub-type used by the framework
     * @return  void
     */
    public static function release(string $framework, string $lockfilesuffix = '') {
        $lockhandlename = self::get_lock_handle_name($framework, $lockfilesuffix);

        if (self::$lockhandles[$lockhandlename]) {
            flock(self::$lockhandles[$lockhandlename], LOCK_UN);
            fclose(self::$lockhandles[$lockhandlename]);
            self::$lockhandles[$lockhandlename] = null;
        }
    }

    /**
     * Get the name of the lock handle stored in the class.
     *
     * @param   string $framework
     * @param   string $lockfilesuffix
     * @return  string
     */
    protected static function get_lock_handle_name(string $framework, string $lockfilesuffix): string {
        $lockhandlepieces = [$framework];

        if (!empty($lockfilesuffix)) {
            $lockhandlepieces[] = $lockfilesuffix;
        }

        return implode('%', $lockhandlepieces);
    }

}
