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
 * moodle_sniffs_files_linelengthsniff.
 *
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-files
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_files_linelengthsniff.
 *
 * Checks all lines in the file, and throws warnings if they are over 80
 * characters in length and errors if they are over 100. Both these
 * figures can be changed by extending this sniff in your own standard.
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_files_linelengthsniff implements php_codesniffer_sniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    protected $lineLimit = 120;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var int
     */
    protected $absolutelineLimit = 200;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr)
    {
        $tokens = $phpcsfile->gettokens();

        // Make sure this is the first open tag.
        $previousOpenTag = $phpcsfile->findPrevious(array(T_OPEN_TAG), ($stackptr - 1));
        if ($previousOpenTag !== false) {
            return;
        }

        $tokenCount         = 0;
        $currentlinecontent = '';
        $currentline        = 1;

        for (; $tokenCount < $phpcsfile->numTokens; $tokenCount++) {
            if ($tokens[$tokenCount]['line'] === $currentline) {
                $currentlinecontent .= $tokens[$tokenCount]['content'];
            } else {
                $currentlinecontent = trim($currentlinecontent, $phpcsfile->eolChar);
                $this->checklineLength($phpcsfile, ($tokenCount - 1), $currentlinecontent);
                $currentlinecontent = $tokens[$tokenCount]['content'];
                $currentline++;
            }
        }

        $this->checklineLength($phpcsfile, ($tokenCount - 1), $currentlinecontent);

    }


    /**
     * Checks if a line is too long.
     *
     * @param PHP_CodeSniffer_File $phpcsfile   The file being scanned.
     * @param int                  $stackptr    The token at the end of the line.
     * @param string               $linecontent The content of the line.
     *
     * @return void
     */
    protected function checklineLength(PHP_CodeSniffer_File $phpcsfile, $stackptr, $linecontent)
    {
        // If the content is a CVS or SVN id in a version tag, or it is
        // a license tag with a name and URL, there is nothing the
        // developer can do to shorten the line, so don't throw errors.
        if (preg_match('|@version[^\$]+\$Id|', $linecontent) === 0 && preg_match('|@license|', $linecontent) === 0) {
            $lineLength = strlen($linecontent);
            if ($this->absolutelineLimit > 0 && $lineLength > $this->absolutelineLimit) {
                $error = 'line exceeds maximum limit of '.$this->absolutelineLimit." characters; contains $lineLength characters";
                $phpcsfile->adderror($error, $stackptr);
            } else if ($lineLength > $this->lineLimit) {
                $warning = 'line exceeds '.$this->lineLimit." characters; contains $lineLength characters";
                $phpcsfile->addwarning($warning, $stackptr);
            }
        }

    }


}

?>
