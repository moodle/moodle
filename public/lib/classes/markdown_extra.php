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

namespace core;
/**
 * Moodle implementation of MarkdownExtra parsing.
 *
 * @package    core
 * @copyright  2026 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class markdown_extra extends \Michelf\MarkdownExtra {
    #[\Override]
    // Parent method name is defined by the MarkdownExtra library, so the underscore prefix cannot be avoided.
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _doFencedCodeBlocks_callback($matches) {
        // Normalizes the language specifier for fenced code blocks.
        // If no language info is provided, this explicitly sets 'none' so
        // filter_codehighlighter picks it up properly.
        if (trim($matches[2]) === '') {
            $matches[2] = 'none';
        }

        return parent::_doFencedCodeBlocks_callback($matches);
    }
}
