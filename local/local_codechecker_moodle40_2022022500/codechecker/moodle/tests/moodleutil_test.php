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

namespace local_codechecker;

use MoodleCodeSniffer\moodle\Util\MoodleUtil;
use org\bovigo\vfs\vfsStream;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../tests/local_codechecker_testcase.php');
require_once(__DIR__ . '/../Util/MoodleUtil.php');

// phpcs:disable moodle.NamingConventions

/**
 * Test the MoodleUtil specific moodle utilities class
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleCodeSniffer\moodle\Util\MoodleUtil
 */
class moodleutil_test extends local_codechecker_testcase {

    /**
     * Unit test for calculateAllComponents.
     *
     * Not 100% orthodox because {@see calculateAllComponents()} is protected,
     * and it's already indirectly tested by {@see test_getMoodleComponent()}
     * but it has some feature that we need to test individually here.
     */
    public function test_calculateAllComponents() {
        // Let's calculate moodleRoot.
        $moodleRoot = MoodleUtil::getMoodleRoot();

        // Let's prepare a components file, with some correct and incorrect entries.
        $components =
            "nonono,mod_forum,{$moodleRoot}/mod_forum\n" .                // Wrong type.
            "plugin,mod__nono,{$moodleRoot}/mod_forum\n" .                // Wrong component.
            "plugin,mod_forum,/no/no/no/no//mod_forum\n" .                // Wrong path.
            "plugin,local_codechecker,{$moodleRoot}/local/codechecker\n" .// All ok.
            "plugin,mod_forum,{$moodleRoot}/mod/forum\n";                 // All ok.

        // Let's use virtual filesystem instead of real one.
        $vfs = vfsStream::setup('root', null, ['components.txt' => $components]);

        // Set codechecker config to point to it.
        Config::setConfigData('moodleComponentsListPath', $vfs->url() . '/components.txt', true);

        // Let's run calculateAllComponents() and evaluate results.
        $method = new \ReflectionMethod(MoodleUtil::class, 'calculateAllComponents');
        $method->setAccessible(true);
        $method->invokeArgs(null, [$moodleRoot]);

        // Let's inspect which components have been loaded.
        $property = new \ReflectionProperty(MoodleUtil::class, 'moodleComponents');
        $property->setAccessible(true);
        $loadedComponents = $property->getValue();

        $this->assertCount(2, $loadedComponents);
        $this->assertSame(['mod_forum', 'local_codechecker'],
            array_keys($loadedComponents)); // Verify they are ordered in ascending order.
        $this->assertSame(["{$moodleRoot}/mod/forum", "{$moodleRoot}/local/codechecker"],
            array_values($loadedComponents)); // Verify component paths are also the expected ones.

        // Now be evil and try with an unreadable file, it must throw an exception.

        $this->cleanMoodleUtilCaches(); // Need to clean previous cached values.
        Config::setConfigData('moodleComponentsListPath', '/path/to/non/readable/file', true);

        // We cannot use expectException() here, because we need to clean caches at the end.
        try {
            $method->invokeArgs(null, [$moodleRoot]);
            $this->fail('\PHP_CodeSniffer\Exceptions\DeepExitException was expected, got none');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\PHP_CodeSniffer\Exceptions\DeepExitException::class, $e);
        }

        // Ensure cached information doesn't affect other tests.
        $this->cleanMoodleUtilCaches();
        Config::setConfigData('moodleComponentsListPath', null, true);
    }

    /**
     * Provider for test_getMoodleComponent.
     */
    public function getMoodleComponentProvider() {
        return [
            'moodleComponent_file_without_moodleroot' => [
                'config' => ['file' => sys_get_temp_dir() . '/notexists.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
            ],
            'moodleComponent_file_without_component_class' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
            ],
            'moodleComponent_file_valid' => [
                'config' => ['file' => __FILE__],
                'return' => ['value' => 'local_codechecker'],
                'reset' => false, // Prevent resetting cached information to verify next works.
                'selfPath' => false,
            ],
            'moodleComponent_file_already_cached' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => 'local_codechecker'],
                'reset' => true,
                'selfPath' => false,
            ],
            'moodleComponent_file_cache_cleaned' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
            ],
            'moodleComponent_file_without_component' => [
                'config' => ['file' => dirname(__FILE__, 5) . '/userpix/index.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
            ],
        ];
    }

    /**
     * Unit test for getMoodleComponent.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if codechecker own path is good to find a valid moodle root.
     *
     * @dataProvider getMoodleComponentProvider
     */
    public function test_getMoodleComponent(array $config, array $return, bool $reset = true, bool $selfPath = true) {
        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $config = new Config();
                    $ruleset = new Ruleset($config);
                    $file = new File($value, $ruleset, $config);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleComponent($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }

        } else if (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleComponent($file, $selfPath));
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key !== 'file') {
                    Config::setConfigData($key, null, true);
                }
            }
        }
    }

    /**
     * Provider for test_getMoodleBranch.
     */
    public function getMoodleBranchProvider() {
        return [
            // Setting up moodleBranch config/runtime option.
            'moodleBranch_not_integer' => [
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value in not an integer'],
            ],
            'moodleBranch_big' => [
                'config' => ['moodleBranch' => '10000'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value must be 4 digit max'],
            ],
            'moodleBranch_valid' => [
                'config' => ['moodleBranch' => 999],
                'return' => ['value' => 999],
                'reset'  => false, // Prevent resetting cached information to verify next works.
            ],
            'moodleBranch_already_cached' => [
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['value' => 999],
            ],
            'moodleBranch_cache_cleaned' => [ // Verify that previous has cleaned cached information.
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value in not an integer'],
            ],

            // Passing a file to check with correct $branch information at moodle root.
            'moodleBranch_pass_file_good' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => 9876],
            ],

            // Passing a file to check with incorrect $branch information at moodle root.
            'moodleBranch_pass_file_bad' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/bad/lib/lib.php'],
                'return' => ['value' => null],
            ],
        ];
    }

    /**
     * Unit test for getMoodleBranch.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if codechecker own path is good to find a valid moodle root.
     *
     * @dataProvider getMoodleBranchProvider
     */
    public function test_getMoodleBranch(array $config, array $return, bool $reset = true, bool $selfPath = true) {
        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $config = new Config();
                    $ruleset = new Ruleset($config);
                    $file = new File($value, $ruleset, $config);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleBranch($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }

        } else if (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleBranch($file, $selfPath));
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key !== 'file') {
                    Config::setConfigData($key, null, true);
                }
            }
        }
    }

    /**
     * Provider for test_getMoodleRoot.
     */
    public function getMoodleRootProvider() {
        return [
            // Setting up moodleRoot config/runtime option.
            'moodleRoot_not_exists' => [
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'does not exist or is not readable'],
            ],
            'moodleRoot_not_moodle' => [
                'config' => ['moodleRoot' => sys_get_temp_dir()],
                'return' => ['exception' => DeepExitException::class, 'message' => 'not a valid moodle root'],
            ],
            'moodleRoot_valid' => [
                'config' => ['moodleRoot' => dirname(__FILE__, 5)],
                'return' => ['value' => dirname(__FILE__, 5)],
                'reset'  => false, // Prevent resetting cached information to verify next works.
            ],
            'moodleRoot_already_cached' => [
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['value' => dirname(__FILE__, 5)],
            ],
            'moodleRoot_cache_cleaned' => [ // Verify that previous has cleaned cached information.
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'does not exist or is not readable'],
            ],
            'moodleRoot_from_fixtures' => [
                'config' => ['moodleRoot' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
                'return' => ['value' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
            ],

            // Passing a file to check.
            'moodleRoot_pass_file' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
            ],

            // Passing nothing, defaults to this file.
            'moodleRoot_pass_nothing' => [
                'config' => [],
                'return' => ['value' => dirname(__FILE__, 5)],
            ],
        ];
    }

    /**
     * Unit test for getMoodleRoot.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if codechecker own path is good to find a valid moodle root.
     *
     * @dataProvider getMoodleRootProvider
     */
    public function test_getMoodleRoot(array $config, array $return, bool $reset = true, bool $selfPath = true) {
        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $config = new Config();
                    $ruleset = new Ruleset($config);
                    $file = new File($value, $ruleset, $config);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleRoot($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }

        } else if (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleRoot($file), $selfPath);
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key !== 'file') {
                    Config::setConfigData($key, null, true);
                }
            }
        }
    }

    /**
     * Utility method to clean MoodleUtil own "caches" (class properties).
     */
    protected function cleanMoodleUtilCaches() {
        $moodleRoot = new \ReflectionProperty(MoodleUtil::class, 'moodleRoot');
        $moodleRoot->setAccessible(true);
        $moodleRoot->setValue(false);

        $moodleBranch = new \ReflectionProperty(MoodleUtil::class, 'moodleBranch');
        $moodleBranch->setAccessible(true);
        $moodleBranch->setValue(false);

        $moodleComponents = new \ReflectionProperty(MoodleUtil::class, 'moodleComponents');
        $moodleComponents->setAccessible(true);
        $moodleComponents->setValue([]);
    }
}
