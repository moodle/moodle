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
 * Checks that each file contains the standard GPL comment.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Files;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class BoilerplateCommentSniff implements Sniff {
    protected static $comment = array(
        "// This file is part of",
        "//",
        "// Moodle is free software: you can redistribute it and/or modify",
        "// it under the terms of the GNU General Public License as published by",
        "// the Free Software Foundation, either version 3 of the License, or",
        "// (at your option) any later version.",
        "//",
        "// Moodle is distributed in the hope that it will be useful,",
        "// but WITHOUT ANY WARRANTY; without even the implied warranty of",
        "// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the",
        "// GNU General Public License for more details.",
        "//",
        "// You should have received a copy of the GNU General Public License",
        "// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.",
    );
    public function register() {
        return array(T_OPEN_TAG);
    }

    public function process(File $file, $stackptr) {
        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $stackptr - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        if ($stackptr > 0) {
            $file->addError('The first thing in a PHP file must be the <?php tag.', 0, 'NoPHP');
            return;
        }

        $tokens = $file->getTokens();

        // Allow T_PHPCS_XXX comment annotations in the first line (skip them).
        if ($commentptr = $file->findNext(Tokens::$phpcsCommentTokens, $stackptr + 1, $stackptr + 3)) {
            $stackptr = $commentptr;
        }

        // Find count the number of newlines after the opening <?PHP. We only
        // count enough to see if the number is right.
        // Note that the opening PHP tag includes one newline.
        $numnewlines = 0;
        for ($i = $stackptr + 1; $i <= $stackptr + 5; ++$i) {
            if ($tokens[$i]['code'] == T_WHITESPACE && $tokens[$i]['content'] == "\n") {
                $numnewlines++;
            } else {
                break;
            }
        }

        if ($numnewlines > 0) {
            $file->addError('The opening <?php tag must be followed by exactly one newline.',
                $stackptr + 1, 'WrongWhitespace');
            return;
        }
        $offset = $stackptr + $numnewlines + 1;

        // Now check the text of the comment.
        foreach (self::$comment as $lineindex => $line) {
            $tokenptr = $offset + $lineindex;

            if (!array_key_exists($tokenptr, $tokens)) {
                $file->addError('Reached the end of the file before finding ' .
                        'all of the opening comment.', $tokenptr - 1, 'FileTooShort');
                return;
            }

            $regex = str_replace(
                ['Moodle', 'http\\:'],
                ['.*', 'https?\\:'],
                '/^' . preg_quote($line, '/') . '/'
            );

            if ($tokens[$tokenptr]['code'] != T_COMMENT ||
                    !preg_match($regex, $tokens[$tokenptr]['content'])) {

                $file->addError('Line %s of the opening comment must start "%s".',
                        $tokenptr, 'WrongLine', array($lineindex + 1, $line));
            }
        }
    }
}
