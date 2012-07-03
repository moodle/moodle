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
 * Helper test listener.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Helper test listener that prints command necessary
 * for execution of failed test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Hint_ResultPrinter extends PHPUnit_TextUI_ResultPrinter {
    protected function printDefectTrace(PHPUnit_Framework_TestFailure $defect) {
        global $CFG;

        parent::printDefectTrace($defect);

        $failedTest = $defect->failedTest();
        $testName = get_class($failedTest);

        $exception = $defect->thrownException();
        $trace = $exception->getTrace();

        if (class_exists('ReflectionClass')) {
            $reflection = new ReflectionClass($testName);
            $file = $reflection->getFileName();

        } else {
            $file = false;
            $dirroot = realpath($CFG->dirroot).DIRECTORY_SEPARATOR;
            $classpath = realpath("$CFG->dirroot/lib/phpunit/classes").DIRECTORY_SEPARATOR;
            foreach ($trace as $item) {
                if (strpos($item['file'], $dirroot) === 0 and strpos($item['file'], $classpath) !== 0) {
                    if ($content = file_get_contents($item['file'])) {
                        if (preg_match('/class\s+'.$testName.'\s+extends/', $content)) {
                            $file = $item['file'];
                            break;
                        }
                    }
                }
            }
        }

        if ($file === false) {
            return;
        }

        $cwd = getcwd();
        if (strpos($file, $cwd) === 0) {
            $file = substr($file, strlen($cwd)+1);
        }

        $executable = 'phpunit';
        if (phpunit_bootstrap_is_cygwin()) {
            $file = str_replace('\\', '/', $file);
            $executable = 'phpunit.bat';
        }

        $this->write("\nTo re-run:\n $executable $testName $file\n");
    }
}
