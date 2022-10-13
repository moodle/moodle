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
 * Checks that each file contains the standard MOODLE_INTERNAL check or
 * a config.php inclusion.
 *
 * @package    local_codechecker
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Files;

// phpcs:disable moodle.NamingConventions

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class MoodleInternalSniff implements Sniff {
    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes php files and looks for MOODLE_INTERNAL or config.php
     * inclusion.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer) {
        // Guess moodle root, so we can do better dispensations below.
        $moodleRoot = MoodleUtil::getMoodleRoot($file);
        if ($moodleRoot) {
            $relPath = str_replace('\\', '/', substr($file->path, strlen($moodleRoot)));
            // Special dispensation for /tests/behat/ and /lib/behat/ dirs at any level.
            if (strpos($relPath, '/tests/behat/') !== false || strpos($relPath, '/lib/behat/') !== false) {
                return;
            }
            // Special dispensation for lang dirs at any level.
            if (strpos($relPath, '/lang/') !== false) {
                return;
            }
        } else {
            // Falback to simpler dispensations, only looking 1 level.
            // Special dispensation for behat files.
            if (basename(dirname($file->getFilename())) === 'behat') {
                return;
            }
            // Special dispensation for lang files.
            if (basename(dirname(dirname($file->getFilename()))) === 'lang') {
                return;
            }
        }

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        // Find where real code is and check from there.
        $pointer = $this->get_position_of_relevant_code($file, $pointer);
        if (!$pointer) {
            // The file only contains non-relevant (and non side-effects) code. We are done.
            return;
        }

        if ($this->is_config_php_incluson($file, $pointer)) {
            // We are requiring config.php. This file is good, hurrah!
            return;
        }

        $hasMoodleInternal = false;
        $isOldMoodleInternal = false;
        $sideEffectsPointer = $pointer;

        // OK, we've got to the first bit of relevant code. It must be the MOODLE_INTERNAL check.
        if ($this->is_moodle_internal_or_die_check($file, $pointer)) {
            $hasMoodleInternal = true;
            // Let's look for side effects after the check.
            $sideEffectsPointer = $file->findNext(T_SEMICOLON, $pointer) + 1;

        } else if ($this->is_if_not_moodle_internal_die_check($file, $pointer)) {
            $hasMoodleInternal = true;
            $isOldMoodleInternal = true;
            // Let's look for side effects after the check.
            $sideEffectsPointer = $file->getTokens()[$pointer]['scope_closer'] + 1;
        }

        $hasSideEffects = $this->code_changes_global_state($file, $sideEffectsPointer, ($file->numTokens - 1));
        $hasMultipleArtifacts = ($this->count_artifacts($file) > 1);

        // Missing MOODLE_INTERNAL and having side effects, error.
        if (!$hasMoodleInternal && $hasSideEffects) {
            $file->addError('Expected MOODLE_INTERNAL check or config.php inclusion. Change in global state detected.',
                $pointer, 'MoodleInternalGlobalState');
            return;
        }

        // Missing MOODLE_INTERNAL, not having side effects, but having multiple artifacts, warning.
        if (!$hasMoodleInternal && !$hasSideEffects && $hasMultipleArtifacts) {
            $file->addWarning('Expected MOODLE_INTERNAL check or config.php inclusion. Multiple artifacts detected.',
                $pointer, 'MoodleInternalMultipleArtifacts');
            return;
        }

        // Having MOODLE_INTERNAL, not having side effects and not having multiple artifacts, error.
        if ($hasMoodleInternal && !$hasSideEffects && !$hasMultipleArtifacts) {
            $file->addWarning('Unexpected MOODLE_INTERNAL check. No side effects or multiple artifacts detected.',
                $pointer, 'MoodleInternalNotNeeded');
            return;
        }

        // Having old MOODLE_INTERNAL check, warn.
        if ($hasMoodleInternal && $isOldMoodleInternal) {
            $file->addWarning('Old MOODLE_INTERNAL check detected. Replace it by "defined(\'MOODLE_INTERNAL\') || die();"',
                $pointer, 'MoodleInternalOld');
            return;
        }
    }

    /**
     * Finds the position of the first bit of relevant code (ignoring namespaces,
     * uses, declares and define statements).
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return int position in stack of relevant code.
     */
    protected function get_position_of_relevant_code(File $file, $pointer) {
        // Advance through tokens until we find some real code.
        $tokens = $file->getTokens();
        $relevantcodefound = false;
        $ignoredtokens = array_merge([T_OPEN_TAG, T_SEMICOLON], Tokens::$emptyTokens);

        do {
            // Find some non-whitespace (etc) code.
            $pointer = $file->findNext($ignoredtokens, $pointer, null, true);
            if ($tokens[$pointer]['code'] === T_NAMESPACE || $tokens[$pointer]['code'] === T_USE) {
                // Namespace definitions are allowed before anything else, jump to end of namspace statement.
                $pointer = $file->findEndOfStatement($pointer + 1, T_COMMA);
            } else if ($tokens[$pointer]['code'] === T_STRING && $tokens[$pointer]['content'] == 'define') {
                // Some things like AJAX_SCRIPT NO_MOODLE_COOKIES need to be defined before config inclusion.
                // Jump to end of define().
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else if ($tokens[$pointer]['code'] === T_DECLARE && $tokens[$pointer]['content'] == 'declare') {
                // Declare statements must be at start of file.
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else {
                $relevantcodefound = true;
            }
        } while (!$relevantcodefound);

        return $pointer;
    }

    /**
     * Is the code in the passes position a moodle internal check?
     * Looks for code like:
     *   defined('MOODLE_INTERNAL') or die()
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a moodle internal statement
     */
    protected function is_moodle_internal_or_die_check(File $file, $pointer) {
        $tokens = $file->getTokens();
        if ($tokens[$pointer]['code'] !== T_STRING or $tokens[$pointer]['content'] !== 'defined') {
            return false;
        }

        $ignoredtokens = array_merge(Tokens::$emptyTokens, Tokens::$bracketTokens);

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_CONSTANT_ENCAPSED_STRING or
            $tokens[$pointer]['content'] !== "'MOODLE_INTERNAL'") {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);

        if ($tokens[$pointer]['code'] !== T_BOOLEAN_OR and $tokens[$pointer]['code'] !== T_LOGICAL_OR) {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXIT) {
            return false;
        }

        return true;
    }

    /**
     * Is the code in the passes position a require(config.php) statement?
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a config.php inclusion.
     */
    protected function is_config_php_incluson(File $file, $pointer) {
        $tokens = $file->getTokens();

        if ($tokens[$pointer]['code'] !== T_REQUIRE and $tokens[$pointer]['code'] !== T_REQUIRE_ONCE) {
            return false;
        }

        // It's a require() or require_once() statement. Is it require(config.php)?
        $requirecontent = $file->getTokensAsString($pointer, ($file->findEndOfStatement($pointer) - $pointer));
        if (strpos($requirecontent, '/config.php') === false) {
            return false;
        }

        return true;
    }

    /**
     * Is the code in the passed position an old skool MOODLE_INTERNAL check?
     * Looks for code like:
     *    if (!defined('MOODLE_INTERNAL')) {
     *       die('Direct access to this script is forbidden.');
     *    }
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a moodle internal statement
     */
    protected function is_if_not_moodle_internal_die_check(File $file, $pointer) {
        $tokens = $file->getTokens();

        // Detect 'if'.
        if ($tokens[$pointer]['code'] !== T_IF ) {
            return false;
        }

        $ignoredtokens = array_merge(Tokens::$emptyTokens, Tokens::$bracketTokens);

        // Detect '!'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_BOOLEAN_NOT) {
            return false;
        }

        // Detect 'defined'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_STRING or $tokens[$pointer]['content'] !== 'defined') {
            return false;
        }

        // Detect 'MOODLE_INTERNAL'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_CONSTANT_ENCAPSED_STRING or
            $tokens[$pointer]['content'] !== "'MOODLE_INTERNAL'") {
            return false;
        }

        // Detect die.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXIT) {
            return false;
        }

        return true;
    }

    /**
     * Counts how many classes, interfaces or traits a file has.
     *
     * @param File $file The file being scanned.
     *
     * @return int the number of classes, interfaces and traits in the file.
     */
    private function count_artifacts(File $file) {
        $position = 0;
        $counter = 0;
        while ($position !== false) {
            if ($position = $file->findNext([T_CLASS, T_INTERFACE, T_TRAIT], ($position + 1))) {
                $counter++;
            }

        }
        return $counter;
    }

    /**
     * Searches for changes in 'global state' rather than just symbol definitions in the code.
     *
     * Heavily inspired by PSR1.Files.SideEffects:
     * https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PSR1/Sniffs/Files/SideEffectsSniff.php
     *
     * @param File $file The file being scanned.
     * @param int $start The token to start searching from.
     * @param int $end The token to search to.
     * @param array $tokens The stack of tokens that make up the file.
     * @return true if side effect is detected in the code.
     */
    private function code_changes_global_state(File $file, $start, $end) {
        $tokens = $file->getTokens();
        $symbols = [T_CLASS => T_CLASS, T_INTERFACE => T_INTERFACE, T_TRAIT => T_TRAIT, T_FUNCTION => T_FUNCTION];
        $conditions = [T_IF => T_IF, T_ELSE   => T_ELSE, T_ELSEIF => T_ELSEIF];

        for ($i = $start; $i <= $end; $i++) {
            // Ignore whitespace and comments.
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore function/class prefixes.
            if (isset(Tokens::$methodPrefixes[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore anon classes.
            if ($tokens[$i]['code'] === T_ANON_CLASS) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            switch ($tokens[$i]['code']) {
                case T_NAMESPACE:
                case T_USE:
                case T_DECLARE:
                case T_CONST:
                    // Ignore entire namespace, declare, const and use statements.
                    if (isset($tokens[$i]['scope_opener']) === true) {
                        $i = $tokens[$i]['scope_closer'];
                    } else {
                        $semicolon = $file->findNext(T_SEMICOLON, ($i + 1));
                        if ($semicolon !== false) {
                            $i = $semicolon;
                        }
                    }
                    continue 2;
                case T_STRING:
                    if (isset($tokens[$i]['content']) === true) {
                        // Ignore class_alias as this is no different to declaring a class.
                        // This will be in the format `class_alias(source, target);` and represented by:
                        // - T_STRING['content'] = 'class_alias'
                        // - T_OPEN_PARENTHESIS
                        // - ...
                        // - T_CLOSE_PARENTHESIS
                        if ($tokens[$i]['content'] === 'class_alias') {
                            $paren = $file->findNext(T_OPEN_PARENTHESIS, ($i + 1));
                            if ($paren !== false) {
                                $i = $tokens[$paren]['parenthesis_closer'] + 1;
                                continue 2;
                            }
                        }
                    }
            }

            // Detect and skip over symbols.
            if (isset($symbols[$tokens[$i]['code']]) === true && isset($tokens[$i]['scope_closer']) === true) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            } else if ($tokens[$i]['code'] === T_STRING && strtolower($tokens[$i]['content']) === 'define') {
                $prev = $file->findPrevious(T_WHITESPACE, ($i - 1), null, true);
                if ($tokens[$prev]['code'] !== T_OBJECT_OPERATOR) {

                    $semicolon = $file->findNext(T_SEMICOLON, ($i + 1));
                    if ($semicolon !== false) {
                        $i = $semicolon;
                    }

                    continue;
                }
            }

            // Conditional statements are allowed in symbol files as long as the
            // contents is only a symbol definition. So don't count these as effects
            // in this case.
            if (isset($conditions[$tokens[$i]['code']]) === true) {
                if (isset($tokens[$i]['scope_opener']) === false) {
                    // Probably an "else if", so just ignore.
                    continue;
                }

                if ($this->code_changes_global_state($file, ($tokens[$i]['scope_opener'] + 1), ($tokens[$i]['scope_closer'] - 1))) {
                    return true;
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            // If we got here, there is a token which change state..
            return true;
        }

        // If we got here, we got out of the loop.
        return false;
    }
}
