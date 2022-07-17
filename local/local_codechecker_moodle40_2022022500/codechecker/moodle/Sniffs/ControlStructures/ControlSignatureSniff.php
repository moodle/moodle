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
 * Verifies that control statements conform to their coding standards.
 *
 * Based on {@link Squiz_Sniffs_ControlStructures_ControlSignatureSniff}.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\ControlStructures;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\AbstractPatternSniff;
use PHP_CodeSniffer\Files\File;

class ControlSignatureSniff extends AbstractPatternSniff {

    public function __construct() {
        parent::__construct(true);
        $this->supportedTokenizers = array('PHP', 'JS');
    }

    /** @var array A list of tokenizers this sniff supports. */

    protected function getPatterns() {
        return array(
            'try {EOL...} catch (...) {EOL',
            'do {EOL...} while (...);EOL',
            'while (...) {EOL',
            'for (...) {EOL',
            'if (...) {EOL',
            'foreach (...) {EOL',
            '} else if (...) {EOL',
            '} else {EOL',
         );
    }
}
