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
 * Checks that a test file has a class name matching the file name.
 *
 * @package    local_codechecker
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit;

// phpcs:disable moodle.NamingConventions

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class TestCaseNamesSniff implements Sniff {

    /**
     * List of classes that have been found during checking.
     *
     * @var array
     */
    protected $foundClasses = [];

    /**
     * List of classes that have been proposed during checking.
     *
     * @var array
     */
    protected $proposedClasses = [];

    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes php files and perform various checks with file, namespace and class names.
     * inclusion.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer) {
        // Before starting any check, let's look for various things.

        // Guess moodle component (from $file being processed).
        $moodleComponent = MoodleUtil::getMoodleComponent($file);

        // Detect if we are running PHPUnit.
        $runningPHPUnit = defined('PHPUNIT_TEST') && PHPUNIT_TEST;

        // We have all we need from core, let's start processing the file.

        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        // If the file isn't under tests directory, nothing to check.
        if (stripos($file->getFilename(), '/tests/') === false) {
            return; // @codeCoverageIgnore
        }

        // If the file isn't called, _test.php, nothing to check.
        // Make an exception for codechecker own phpunit fixtures here, allowing any name for them.
        $fileName = basename($file->getFilename());
        if (substr($fileName, -9) !== '_test.php' && !$runningPHPUnit) {
            return; // @codeCoverageIgnore
        }

        // In order to cover the duplicates detection, we need to set some
        // properties (caches) here. It's extremely hard to do
        // this via mocking / extending (at very least for this humble developer).
        if ($runningPHPUnit) {
            $this->prepareCachesForPHPUnit();
        }

        // Get the class namespace.
        $namespace = '';
        $nsStart = 0;
        if ($nsStart = $file->findNext(T_NAMESPACE, ($pointer + 1))) {
            $nsEnd = $file->findNext([T_NS_SEPARATOR, T_STRING, T_WHITESPACE], ($nsStart + 1), null, true);
            $namespace = strtolower(trim($file->getTokensAsString(($nsStart + 1), ($nsEnd - $nsStart - 1))));
        }
        $pointer = $nsEnd ?? $pointer; // When possible, move the pointer to after the namespace name.

        // Get the name of the 1st class in the file (this Sniff doesn't detects multiple),
        // verify that it extends something and that has a test_ method.
        $class = '';
        $classFound = false;
        $classPointers = []; // Save all class pointers to report later if no class is found.
        while ($cStart = $file->findNext(T_CLASS, $pointer)) {
            $classPointers[] = $cStart;
            $pointer = $cStart + 1; // Move the pointer to the class start.

            // Only if the class is extending something.
            // TODO: We could add a list of valid classes once we have a class-map available.
            if (!$file->findNext(T_EXTENDS, $cStart + 1, $tokens[$cStart]['scope_opener'])) {
                continue;
            }

            // Verify that the class has some test_xxx method.
            $method = '';
            $methodFound = false;
            while ($mStart = $file->findNext(T_FUNCTION, $pointer, $tokens[$cStart]['scope_closer'])) {
                $pointer = $tokens[$mStart]['scope_closer']; // Next iteration look after the end of current method.
                if (strpos($file->getDeclarationName($mStart), 'test_') === 0) {
                    $methodFound = true;
                    $method = $file->getDeclarationName($mStart);
                    break;
                }
            }

            // If we have found a test_ method, this is our class (the 1st having one).
            if ($methodFound) {
                $classFound = true;
                $class = $file->getDeclarationName($cStart);
                $class = strtolower(trim($class));
                break;
            }
            $pointer = $tokens[$cStart]['scope_closer']; // Move the pointer to the class end.
        }

        // No testcase class found, this is plain-wrong.
        if (!$classFound) {
            $classPointers = $classPointers ?: [0];
            foreach ($classPointers as $classPointer) {
                $file->addError('PHPUnit test file missing any valid testcase class declaration', $classPointer, 'Missing');
            }
            return; // If arrived here we don't have a valid class, we are finished.
        }

        // All the following checks assume that a valid class has been found.

        // Error if the found classname is "strange" (not "_test|_testcase" ended).
        if (substr($class, -5) !== '_test' && substr($class, -9) != '_testcase') {
            $file->addError('PHPUnit irregular testcase name found: %s (_test/_testcase ended expected)', $cStart,
                'Irregular', [$class]);
        }

        // Check if the file name and the class name match, warn if not.
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        if ($baseName !== $class) {
            $fix = $file->addFixableWarning('PHPUnit testcase name "%s" does not match file name "%s"', $cStart,
                'NoMatch', [$class, $baseName]);

            if ($fix === true) {
                if ($cNameToken = $file->findNext(T_STRING, $cStart + 1, $tokens[$cStart]['scope_opener'])) {
                    $file->fixer->replaceToken($cNameToken, $baseName);
                }
            }
        }

        // Check if the class has been already found (this is useful when running against a lot of files).
        $fdqnClass = $namespace ? $namespace . '\\' . $class : $class;
        if (isset($this->foundClasses[$fdqnClass])) {
            // Already found, this is a dupe class name, error!
            foreach ($this->foundClasses[$fdqnClass] as $exists) {
                $file->addError('PHPUnit testcase "%s" already exists at "%s" line %s', $cStart,
                    'DuplicateExists', [$fdqnClass, $exists['file'], $exists['line']]);
            }
        } else {
            // Create the empty element.
            $this->foundClasses[$fdqnClass] = [];
        }

        // Add the new element.
        $this->foundClasses[$fdqnClass][] = [
            'file' => $file->getFilename(),
            'line' => $tokens[$cStart]['line'],
        ];

        // Check if the class has been already proposed (this is useful when running against a lot of files).
        if (isset($this->proposedClasses[$fdqnClass])) {
            // Already found, this is a dupe class name, error!
            foreach ($this->proposedClasses[$fdqnClass] as $exists) {
                $file->addError('PHPUnit testcase "%s" already proposed for "%s" line %s. You ' .
                    'may want to change the testcase name (file and class)', $cStart,
                    'ProposedExists', [$fdqnClass, $exists['file'], $exists['line']]);
            }
        }

        // TODO: When all the namespace general rules Sniff is created, all these namespace checks
        // should be moved there and reused here.
        // Validate 1st level namespace.

        if ($namespace && $moodleComponent) {
            // Verify that the namespace declared in the class matches the namespace expected for the file.
            if (strpos($namespace . '\\', $moodleComponent . '\\') !== 0) {
                $file->addError('PHPUnit class namespace "%s" does not match expected file namespace "%s"', $nsStart,
                    'UnexpectedNS', [$namespace, $moodleComponent]);
            }

            // Verify that level2 and down match the directory structure under tests. Soft warn if not (till we fix all).
            $bspos = strpos(trim($namespace, ' \\'), '\\');
            if ($bspos !== false) { // Only if there are level2 and down namespace.
                $relns = str_replace('\\', '/', substr(trim($namespace, ' \\'), $bspos + 1));

                // Calculate the relative path under tests directory.
                $dirpos = strripos(trim(dirname($file->getFilename()), ' /') . '/', '/tests/');
                $reldir = str_replace('\\', '/', substr(trim(dirname($file->getFilename()), ' /'), $dirpos + 7));

                // Warning if the relative namespace does not match the relative directory.
                if ($reldir !== $relns) {
                    $file->addWarning('PHPUnit class "%s", with namespace "%s", currently located at "tests/%s" directory, '.
                        'does not match its expected location at "tests/%s"', $nsStart,
                        'UnexpectedLevel2NS', [$fdqnClass, $namespace, $reldir, $relns]);
                }

                // TODO: When we have APIs (https://docs.moodle.org/dev/Core_APIs) somewhere at hand (in core)
                // let's add here an error when incorrect ones are used. See MDL-71096 about it.
            }

        }

        if (!$namespace && $moodleComponent) {
            $file->addWarning('PHPUnit class "%s" does not have any namespace. It is recommended to add it to the "%s" ' .
                'namespace, using more levels if needed, in order to match the code being tested', $cStart,
                'MissingNS', [$fdqnClass, $moodleComponent]);

            // Check if the proposed class has been already proposed (this is useful when running against a lot of files).
            $fdqnProposed = $moodleComponent . '\\' . $fdqnClass;
            if (isset($this->proposedClasses[$fdqnProposed])) {
                // Already found, this is a dupe class name, error!
                foreach ($this->proposedClasses[$fdqnProposed] as $exists) {
                    $file->addError('Proposed PHPUnit testcase "%s" already proposed for "%s" line %s. You ' .
                        'may want to change the testcase name (file and class)', $cStart,
                        'DuplicateProposed', [$fdqnProposed, $exists['file'], $exists['line']]);
                }
            } else {
                // Create the empty element.
                $this->proposedClasses[$fdqnProposed] = [];
            }

            // Add the new element.
            $this->proposedClasses[$fdqnProposed][] = [
                'file' => $file->getFilename(),
                'line' => $tokens[$cStart]['line'],
            ];

            // Check if the proposed class has been already found (this is useful when running against a lot of files).
            if (isset($this->foundClasses[$fdqnProposed])) {
                // Already found, this is a dupe class name, error!
                foreach ($this->foundClasses[$fdqnProposed] as $exists) {
                    $file->addError('Proposed PHPUnit testcase "%s" already exists at "%s" line %s. You ' .
                        'may want to change the testcase name (file and class)', $cStart,
                        'ExistsProposed', [$fdqnProposed, $exists['file'], $exists['line']]);
                }
            }
        }
    }

    /**
     * Prepare found and proposed caches for PHPUnit.
     *
     * It's near impossible to extend or mock this class from PHPUnit in order
     * to get the caches pre-filled with some values that will cover some
     * of the logic of the sniff (at least for this developer).
     *
     * So we fill them here when it's detected that we are running PHPUnit.
     */
    private function prepareCachesForPHPUnit() {
        $this->foundClasses['local_codechecker\testcasenames_duplicate_exists'][] = [
            'file' => 'phpunit_fake_exists',
            'line' => -999,
        ];
        $this->foundClasses['local_codechecker\testcasenames_exists_proposed'][] = [
            'file' => 'phpunit_fake_exists',
            'line' => -999,
        ];
        $this->proposedClasses['local_codechecker\testcasenames_duplicate_proposed'][] = [
            'file' => 'phpunit_fake_proposed',
            'line' => -999,
        ];
        $this->proposedClasses['local_codechecker\testcasenames_proposed_exists'][] = [
            'file' => 'phpunit_fake_proposed',
            'line' => -999,
        ];
    }
}
