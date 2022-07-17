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
 * Checks that lines are no more than 180 chars, ideally 132.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\Files;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff as GenericLineLengthSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class LineLengthSniff extends GenericLineLengthSniff {

    public function __construct() {
        $this->lineLimit = 132;
        $this->absoluteLineLimit = 180;
    }

    public function process(File $file, $stackptr) {
        // Lang files are allowed to have long lines.
        if (strpos($file->getFilename(),
                DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR) !== false) {
            return;
        }
        parent::process($file, $stackptr);
    }
}
