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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit;

// phpcs:disable moodle.NamingConventions

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Checks that a test file has the @coversxxx annotations properly defined.
 *
 * @package    local_codechecker
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestCaseCoversSniff implements Sniff {

    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer) {

        // Before starting any check, let's look for various things.

        // Get the moodle branch being analysed.
        $moodleBranch = MoodleUtil::getMoodleBranch($file);

        // Detect if we are running PHPUnit.
        $runningPHPUnit = defined('PHPUNIT_TEST') && PHPUNIT_TEST;

        // We have all we need from core, let's start processing the file.

        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        // In various places we are going to ignore class/method prefixes (private, abstract...)
        // and whitespace, create an array for all them.
        $skipTokens = Tokens::$methodPrefixes + [T_WHITESPACE => T_WHITESPACE];

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        // If we aren't checking Moodle 4.0dev (400) and up, nothing to check.
        // Make and exception for codechecker phpunit tests, so they are run always.
        if (isset($moodleBranch) && $moodleBranch < 400 && !$runningPHPUnit) {
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

        // Iterate over all the classes (hopefully only one, but that's not this sniff problem).
        $cStart = $pointer;
        while ($cStart = $file->findNext(T_CLASS, $cStart + 1)) {
            $class = $file->getDeclarationName($cStart);
            $classCovers = false; // To control when the class has a @covers tag.
            $classCoversNothing = false; // To control when the class has a @coversNothing tag.

            // Only if the class is extending something.
            // TODO: We could add a list of valid classes once we have a class-map available.
            if (!$file->findNext(T_EXTENDS, $cStart + 1, $tokens[$cStart]['scope_opener'])) {
                continue;
            }

            // Ignore non ended "_test|_testcase" classes.
            if (substr($class, -5) !== '_test' && substr($class, -9) != '_testcase') {
                continue;
            }

            // Let's see if the class has any phpdoc block (first non skip token must be end of phpdoc comment).
            $docPointer = $file->findPrevious($skipTokens, $cStart - 1, null, true);

            // Found a phpdoc block, let's look for @covers, @coversNothing and @coversDefaultClass tags.
            if ($tokens[$docPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                $docStart = $tokens[$docPointer]['comment_opener'];
                while ($docPointer) { // Let's look upwards, until the beginning of the phpdoc block.
                    $docPointer = $file->findPrevious(T_DOC_COMMENT_TAG, $docPointer - 1, $docStart);
                    if ($docPointer) {
                        $docTag = trim($tokens[$docPointer]['content']);
                        switch ($docTag) {
                            case '@covers':
                                $classCovers = $docPointer;
                                // Validate basic syntax (FQCN or ::).
                                $this->checkCoversTagsSyntax($file, $docPointer, '@covers');
                                break;
                            case '@coversNothing':
                                $classCoversNothing = $docPointer;
                                // Validate basic syntax (empty).
                                $this->checkCoversTagsSyntax($file, $docPointer, '@coversNothing');
                                break;
                            case '@coversDefaultClass':
                                // Validate basic syntax (FQCN).
                                $this->checkCoversTagsSyntax($file, $docPointer, '@coversDefaultClass');
                                break;
                        }
                    }
                }
            }

            // Both @covers and @coversNothing, that's a mistake. 2 errors.
            if ($classCovers && $classCoversNothing) {
                $file->addError('Class %s has both @covers and @coversNothing tags, good contradiction',
                    $classCovers, 'ContradictoryClass', [$class]);
                $file->addError('Class %s has both @covers and @coversNothing tags, good contradiction',
                    $classCoversNothing, 'ContradictoryClass', [$class]);
            }

            // Iterate over all the methods in the class.
            $mStart = $cStart;
            while ($mStart = $file->findNext(T_FUNCTION, $mStart + 1, $tokens[$cStart]['scope_closer'])) {
                $method = $file->getDeclarationName($mStart);
                $methodCovers = false; // To control when the method has a @covers tag.
                $methodCoversNothing = false; // To control when the method has a @coversNothing tag.

                // Ignore non test_xxxx() methods.
                if (strpos($method, 'test_') !== 0) {
                    continue;
                }

                // Let's see if the method has any phpdoc block (first non skip token must be end of phpdoc comment).
                $docPointer = $file->findPrevious($skipTokens, $mStart - 1, null, true);

                // Found a phpdoc block, let's look for @covers and @coversNothing tags.
                if ($tokens[$docPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                    $docStart = $tokens[$docPointer]['comment_opener'];
                    while ($docPointer) { // Let's look upwards, until the beginning of the phpdoc block.
                        $docPointer = $file->findPrevious(T_DOC_COMMENT_TAG, $docPointer - 1, $docStart);
                        if ($docPointer) {
                            $docTag = trim($tokens[$docPointer]['content']);
                            switch ($docTag) {
                                case '@covers':
                                    $methodCovers = $docPointer;
                                    // Validate basic syntax (FQCN or ::).
                                    $this->checkCoversTagsSyntax($file, $docPointer, '@covers');
                                    break;
                                case '@coversNothing':
                                    $methodCoversNothing = $docPointer;
                                    // Validate basic syntax (empty).
                                    $this->checkCoversTagsSyntax($file, $docPointer, '@coversNothing');
                                    break;
                                case '@coversDefaultClass':
                                    // Not allowed in methods.
                                    $file->addError('Method %s() has @coversDefaultClass tag, only allowed in classes',
                                        $docPointer, 'DefaultClassNotAllowed', [$method]);
                                    break;
                            }
                        }
                    }
                }

                // No @covers or @coversNothing at any level, that's a missing one.
                if (!$classCovers && !$classCoversNothing && !$methodCovers && !$methodCoversNothing) {
                    $file->addWarning('Test method %s() is missing any coverage information, own or at class level',
                        $mStart, 'Missing', [$method]);
                }

                // Both @covers and @coversNothing, that's a mistake. 2 errors.
                if ($methodCovers && $methodCoversNothing) {
                    $file->addError('Method %s() has both @covers and @coversNothing tags, good contradiction',
                        $methodCovers, 'ContradictoryMethod', [$method]);
                    $file->addError('Method %s() has both @covers and @coversNothing tags, good contradiction',
                        $methodCoversNothing, 'ContradictoryMethod', [$method]);
                }

                // Found @coversNothing at class, and @covers at method, strange. Warning.
                if ($classCoversNothing && $methodCovers) {
                    $file->addWarning('Class %s has @coversNothing, but there are methods covering stuff',
                        $classCoversNothing, 'ContradictoryMixed', [$class]);
                    $file->addWarning('Test method %s() is covering stuff, but class has @coversNothing',
                        $methodCovers, 'ContradictoryMixed', [$method]);
                }

                // Found @coversNothing at class and method, redundant. Warning.
                if ($classCoversNothing && $methodCoversNothing) {
                    $file->addWarning('Test method %s() has @coversNothing, but class also has it, redundant',
                        $methodCoversNothing, 'Redundant', [$method]);
                }

                // Advance until the end of the method, if possible, to find the next one quicker.
                $mStart = $tokens[$mStart]['scope_closer'] ?? $pointer + 1;
            }
        }
    }

    /**
     * Perform a basic syntax cheking of the values of the @coversXXX tags.
     *
     * @param File $file The file being scanned
     * @param int $pointer pointer to the token that contains the tag. Calculations are based on that.
     * @param string $tag $coversXXX tag to be checked. Verifications are different based on that.
     * @return void
     */
    protected function checkCoversTagsSyntax(File $file, int $pointer, string $tag) {
        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        if ($tag === '@coversNothing') {
            // Check that there isn't whitespace and string.
            if ($tokens[$pointer + 1]['code'] === T_DOC_COMMENT_WHITESPACE &&
                    $tokens[$pointer + 2]['code'] === T_DOC_COMMENT_STRING) {
                $file->addError('Wrong %s annotation, it must be empty',
                    $pointer, 'NotEmpty', [$tag]);
            }
        }

        if ($tag === '@covers' || $tag === '@coversDefaultClass') {
            // Check that there is whitespace and string.
            if ($tokens[$pointer + 1]['code'] !== T_DOC_COMMENT_WHITESPACE ||
                    $tokens[$pointer + 2]['code'] !== T_DOC_COMMENT_STRING) {
                $file->addError('Wrong %s annotation, it must contain some value',
                    $pointer, 'Empty', [$tag]);
                // No value, nothing else to check.
                return;
            }
        }

        if ($tag === '@coversDefaultClass') {
            // Check that value begins with \ (FQCN).
            if (strpos($tokens[$pointer + 2]['content'], '\\') !== 0) {
                $file->addError('Wrong %s annotation, it must be FQCN (\\ prefixed)',
                    $pointer, 'NoFQCN', [$tag]);
            }
            // Check that value does not contain :: (method).
            if (strpos($tokens[$pointer + 2]['content'], '::') !== false) {
                $file->addError('Wrong %s annotation, cannot point to a method (contains ::)',
                    $pointer, 'WrongMethod', [$tag]);
            }
        }

        if ($tag === '@covers') {
            // Check value begins with \ (FQCN) or :: (method).
            if (strpos($tokens[$pointer + 2]['content'], '\\') !== 0 &&
                    strpos($tokens[$pointer + 2]['content'], '::') !== 0) {
                $file->addError('Wrong %s annotation, it must be FQCN (\\ prefixed) or point to method (:: prefixed)',
                    $pointer, 'NoFQCNOrMethod', [$tag]);
            }
        }
    }
}
