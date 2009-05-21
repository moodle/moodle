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
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-files
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_files_linelengthsniff.
 *
 * Checks all lines in the file, and throws warnings if they are over 80
 * characters in length and errors if they are over 100. Both these
 * figures can be changed by extending this sniff in your own standard.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_files_linelengthsniff implements php_codesniffer_sniff {

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    protected $linelimit = 120;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var int
     */
    protected $absolutelinelimit = 200;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
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
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        // Make sure this is the first open tag.
        $previousopentag = $phpcsfile->findprevious(array(T_OPEN_TAG), ($stackptr - 1));

        if ($previousopentag !== false) {
            return;
        }

        $tokencount         = 0;
        $currentlinecontent = '';
        $currentline        = 1;

        for (; $tokencount < $phpcsfile->numTokens; $tokencount++) {

            if ($tokens[$tokencount]['line'] === $currentline) {
                $currentlinecontent .= $tokens[$tokencount]['content'];

            } else {
                $currentlinecontent = trim($currentlinecontent, $phpcsfile->eolChar);
                $this->checklinelength($phpcsfile, ($tokencount - 1), $currentlinecontent);
                $currentlinecontent = $tokens[$tokencount]['content'];
                $currentline++;
            }
        }

        $this->checklinelength($phpcsfile, ($tokencount - 1), $currentlinecontent);

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
    protected function checklinelength(PHP_CodeSniffer_File $phpcsfile, $stackptr, $linecontent) {
        // If the content is a CVS or SVN id in a version tag, or it is
        // a license tag with a name and URL, there is nothing the
        // developer can do to shorten the line, so don't throw errors.
        if (preg_match('|@version[^\$]+\$Id|', $linecontent) === 0 && preg_match('|@license|', $linecontent) === 0) {
            $linelength = strlen($linecontent);

            if ($this->absolutelinelimit > 0 && $linelength > $this->absolutelinelimit) {
                $error = 'line exceeds maximum limit of '.$this->absolutelinelimit." characters; contains $linelength characters";
                $phpcsfile->adderror($error, $stackptr);

            } else if ($linelength > $this->linelimit) {
                $warning = 'line exceeds '.$this->linelimit." characters; contains $linelength characters";
                $phpcsfile->addwarning($warning, $stackptr);
            }
        }
    }
}
