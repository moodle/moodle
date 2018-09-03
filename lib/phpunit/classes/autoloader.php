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
 * PHPUnit autoloader for Moodle.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class phpunit_autoloader.
 *
 * Please notice that phpunit testcases obey frankenstyle naming rules,
 * that is full component prefix + _testcase postfix. The files are expected
 * in tests directory inside each component. There are some extra tests
 * directories which require both classname and file path.
 *
 * Examples:
 *
 * vendor/bin/phpunit core_component_testcase
 * vendor/bin/phpunit lib/tests/component_test.php
 * vendor/bin/phpunit core_component_testcase lib/tests/component_test.php
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_autoloader implements \PHPUnit\Runner\TestSuiteLoader {
    public function load($suiteClassName, $suiteClassFile = '') {
        global $CFG;

        // Let's guess what user entered on the commandline...
        if ($suiteClassFile) {
            // This means they either entered the class+path or path only.
            if (strpos($suiteClassName, '/') !== false) {
                // Class names can not contain slashes,
                // user entered only path without testcase class name.
                return $this->guess_class_from_path($suiteClassFile);
            }
            if (strpos($suiteClassName, '\\') !== false and strpos($suiteClassFile, $suiteClassName.'.php') !== false) {
                // This must be backslashed windows path.
                return $this->guess_class_from_path($suiteClassFile);
            }
        }

        if (class_exists($suiteClassName, false)) {
            $class = new ReflectionClass($suiteClassName);
            return $class;
        }

        if ($suiteClassFile) {
            PHPUnit\Util\Fileloader::checkAndLoad($suiteClassFile);
            if (class_exists($suiteClassName, false)) {
                $class = new ReflectionClass($suiteClassName);
                return $class;
            }

            throw new PHPUnit\Framework\Exception(
                sprintf("Class '%s' could not be found in '%s'.", $suiteClassName, $suiteClassFile)
            );
        }

        /*
         * Try standard testcase naming rules based on frankenstyle component:
         *   1/ test classes should use standard frankenstyle class names plus suffix "_testcase"
         *   2/ test classes should be stored in files with suffix "_test"
         */

        $parts = explode('_', $suiteClassName);
        $suffix = end($parts);
        $component = '';

        if ($suffix === 'testcase') {
            unset($parts[key($parts)]);
            while($parts) {
                if (!$component) {
                    $component = array_shift($parts);
                } else {
                    $component = $component . '_' . array_shift($parts);
                }
                // Try standard plugin and core subsystem locations.
                if ($fulldir = core_component::get_component_directory($component)) {
                    $testfile = implode('_', $parts);
                    $fullpath = "{$fulldir}/tests/{$testfile}_test.php";
                    if (is_readable($fullpath)) {
                        include_once($fullpath);
                        if (class_exists($suiteClassName, false)) {
                            $class = new ReflectionClass($suiteClassName);
                            return $class;
                        }
                    }
                }
            }
            // The last option is testsuite directories in main phpunit.xml file.
            $xmlfile = "$CFG->dirroot/phpunit.xml";
            if (is_readable($xmlfile) and $xml = file_get_contents($xmlfile)) {
                $dom = new DOMDocument();
                $dom->loadXML($xml);
                $nodes = $dom->getElementsByTagName('testsuite');
                foreach ($nodes as $node) {
                    /** @var DOMNode $node */
                    $suitename = trim($node->attributes->getNamedItem('name')->nodeValue);
                    if (strpos($suitename, 'core') !== 0 or strpos($suitename, ' ') !== false) {
                        continue;
                    }
                    // This is a nasty hack: testsuit names are sometimes used as prefix for testcases
                    // in non-standard core subsystem locations.
                    if (strpos($suiteClassName, $suitename) !== 0) {
                        continue;
                    }
                    foreach ($node->childNodes as $dirnode) {
                        /** @var DOMNode $dirnode */
                        $dir = trim($dirnode->textContent);
                        if (!$dir) {
                            continue;
                        }
                        $dir = $CFG->dirroot.'/'.$dir;
                        $parts = explode('_', $suitename);
                        $prefix = '';
                        while ($parts) {
                            if ($prefix) {
                                $prefix = $prefix.'_'.array_shift($parts);
                            } else {
                                $prefix = array_shift($parts);
                            }
                            $filename = substr($suiteClassName, strlen($prefix)+1);
                            $filename = preg_replace('/testcase$/', 'test', $filename);
                            if (is_readable("$dir/$filename.php")) {
                                include_once("$dir/$filename.php");
                                if (class_exists($suiteClassName, false)) {
                                    $class = new ReflectionClass($suiteClassName);
                                    return $class;
                                }
                            }
                        }
                    }
                }
            }
        }

        throw new PHPUnit\Framework\Exception(
            sprintf("Class '%s' could not be found in '%s'.", $suiteClassName, $suiteClassFile)
        );
    }

    protected function guess_class_from_path($file) {
        // Somebody is using just the file name, we need to look inside the file and guess the testcase
        // class name. Let's throw fatal error if there are more testcases in one file.

        $classes = get_declared_classes();
        PHPUnit\Util\Fileloader::checkAndLoad($file);
        $includePathFilename = stream_resolve_include_path($file);
        $loadedClasses = array_diff(get_declared_classes(), $classes);

        $candidates = array();

        foreach ($loadedClasses as $loadedClass) {
            $class = new ReflectionClass($loadedClass);

            if ($class->isSubclassOf('PHPUnit\Framework\TestCase') and !$class->isAbstract()) {
                if (realpath($includePathFilename) === realpath($class->getFileName())) {
                    $candidates[] = $loadedClass;
                }
            }
        }

        if (count($candidates) == 0) {
            throw new PHPUnit\Framework\Exception(
                sprintf("File '%s' does not contain any test cases.", $file)
            );
        }

        if (count($candidates) > 1) {
            throw new PHPUnit\Framework\Exception(
                sprintf("File '%s' contains multiple test cases: ".implode(', ', $candidates), $file)
            );
        }

        $classname = reset($candidates);
        return new ReflectionClass($classname);
    }

    public function reload(ReflectionClass $aClass) {
        return $aClass;
    }
}
