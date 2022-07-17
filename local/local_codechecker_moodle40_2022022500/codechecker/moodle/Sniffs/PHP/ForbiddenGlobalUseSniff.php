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
 * Sniff to detect people using global $PAGE and $OUTPUT in renderers, or global $PAGE in blocks.
 *
 * @package    local_codechecker
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\PHP;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

class ForbiddenGlobalUseSniff extends AbstractVariableSniff {

    /**
     * @var array list of problems to detect. Each element is an array with three keys:
     *      classtype        - this is a human-readable description of this type of class, used in error messages.
     *      variables        - this is an array of global variables names we want to ban here.
     *      classnamepattern - this is a regular expression to match against the class name.
     *                          the error will only be reported if this matches.
     */
    protected $forbiddencombinations = [
        [
            'classtype' => 'renderers',
            'variables' => ['OUTPUT', 'PAGE'],
            // The list of things that can come at the end of the next regex has to match the
            // possible values that {@link renderer_factory_base::get_target_suffix()} can return.
            'classnamepattern' => '~^(|\w+_)renderer(_cli|_ajax|_textemail|_htmlemail|_maintenance|)$~',
        ],
        [
            'classtype' => 'block classes',
            'variables' => ['PAGE'],
            'classnamepattern' => '~^block_.*~',
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) {
        // Won't be a global.
    }

    /**
     * @inheritDoc
     */
    protected function processVariable(File $phpcsFile, $stackPtr) {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        $this->check_variable_usage($varName, $phpcsFile, $stackPtr);
    }

    /**
     * @inheritDoc
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        if (preg_match_all('|[^\\\]\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                $this->check_variable_usage($varName, $phpcsFile, $stackPtr);
            }
        }
    }

    /**
     * Check a particular reference to a variable against all the forbidden things.
     *
     * @param string $varname the name of the variable that was found.
     * @param File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int $stackPtr The position where the token was found.
     */
    protected function check_variable_usage($varname, File $phpcsFile, $stackPtr) {
        foreach ($this->forbiddencombinations as $forbiddencombination) {
            $this->check_one_combination($varname, $forbiddencombination, $phpcsFile, $stackPtr);
        }
    }

    /**
     * Check a particular reference to a variable against one of the forbidden combinations.
     *
     * Format of $forbiddencombination documented above at {@link $forbiddencombinations}.
     *
     * @param string $varname the name of the variable that was found.
     * @param array $forbiddencombination the particular forbidden combination to check.
     * @param File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int $stackPtr  The position where the token was found.
     */
    protected function check_one_combination($varname, $forbiddencombination,
            File $phpcsFile, $stackPtr) {
        $tokens  = $phpcsFile->getTokens();

        if (!in_array($varname, $forbiddencombination['variables'])) {
            // Not a reference to one of the global variables we care about.
            return;
        }

        // See if we are in a class, and if so check its name.
        foreach ($tokens[$stackPtr]['conditions'] as $scopePtr => $type) {
            if ($type !== T_CLASS) {
                continue;
            }

            // OK, we are inside a class. Find the class name.
            $classnameptr = $phpcsFile->findNext(T_STRING, $scopePtr, $tokens[$scopePtr]['scope_opener']);
            $classname = $tokens[$classnameptr]['content'];

            // If the classname matches, report the error.
            if (preg_match($forbiddencombination['classnamepattern'], $classname)) {
                $phpcsFile->addError('global $' . $varname . ' cannot be used in ' .
                        $forbiddencombination['classtype'] . '. Use $this->' . strtolower($varname) . '.',
                        $stackPtr, 'BadGlobal');
                return;
            }
        }
    }
}
