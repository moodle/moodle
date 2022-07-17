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
 * Checks that the include_once is used in conditional situations, and
 * require_once is used elsewhere.
 *
 * Checks that brackets surround the file being included.
 *
 * Based on {@link PEAR_Sniffs_Files_IncludingFileSniff}.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\PHP;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class IncludingFileSniff implements Sniff {
    public function register() {
        return array(T_INCLUDE_ONCE, T_REQUIRE_ONCE, T_REQUIRE, T_INCLUDE);
    }

    public function process(File $file, $stackptr) {
        $tokens = $file->getTokens();

        if ($tokens[$stackptr + 1]['code'] !== T_OPEN_PARENTHESIS) {
            $error = '"%s" must be immediately followed by an open parenthesis';
            $data  = array($tokens[$stackptr]['content']);
            $file->addError($error, $stackptr, 'BracketsNotRequired', $data);
        }

        $incondition = (count($tokens[$stackptr]['conditions']) !== 0) ? true : false;

        // Check to see if this including statement is within the parenthesis
        // of a condition. If that's the case then we need to process it as being
        // within a condition, as they are checking the return value.
        if (isset($tokens[$stackptr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackptr]['nested_parenthesis'] as $left => $right) {
                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $incondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this
        // including call. If they are then they are probably checking it, so
        // it's conditional.
        $previous = $file->findPrevious(Tokens::$emptyTokens,
                ($stackptr - 1), null, true);
        if (in_array($tokens[$previous]['code'], Tokens::$assignmentTokens)) {
            // The have assigned the return value to it, so its conditional.
            $incondition = true;
        }

        $tokentype = $tokens[$stackptr]['code'];
        if ($incondition !== true) {
            // We are unconditionally including, we should use require_once.
            if ($tokentype === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require_once" instead';
                $file->addError($error, $stackptr, 'UseRequireOnce');
            } else if ($tokentype === T_INCLUDE) {
                $error  = 'File is being unconditionally included; ';
                $error .= 'use "require" instead';
                $file->addError($error, $stackptr, 'UseRequire');
            }
        }
    }
}
