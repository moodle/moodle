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
 * Verify there is a single space after a comma.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class SpaceAfterCommaSniff implements Sniff {
    public function register() {
        return array(T_COMMA);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $file The file being scanned.
     * @param int $stackptr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $file, $stackptr) {
        $tokens = $file->getTokens();

        if ($tokens[$stackptr + 1]['code'] !== T_WHITESPACE) {
            $error = 'Commas (,) must be followed by white space.';
            $fix = $file->addFixableError($error, $stackptr, 'NoSpace');
            if ($fix === true) {
                $file->fixer->beginChangeset();
                $file->fixer->addContent($stackptr, ' ');
                $file->fixer->endChangeset();
            }
        }
    }
}
