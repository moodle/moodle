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
 * Tiny Equation plugin helper function to build queryable data selectors.
 *
 * @module      tiny_equation/selectors
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    equationPatterns: [
        // We use space or not space because . does not match new lines.
        // $$ blah $$.
        /\$\$([\S\s]+?)\$\$/,
        // E.g. "\( blah \)".
        /\\\(([\S\s]+?)\\\)/,
        // E.g. "\[ blah \]".
        /\\\[([\S\s]+?)\\\]/,
        // E.g. "[tex] blah [/tex]".
        /\[tex\]([\S\s]+?)\[\/tex\]/
    ],
    delimiters: {
        start: '\\(',
        end: '\\)'
    },
    cursorLatex: '\\Downarrow ',
    actions: {
        submit: '[data-action="save"]'
    },
    elements: {
        form: 'form',
        library: '.tiny_equation_library',
        libraryItem: '.tiny_equation_library button',
        equationTextArea: '.tiny_equation_equation',
        preview: '.tiny_equation_preview',
    }
};
