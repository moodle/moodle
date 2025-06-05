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
 * Helper module for manipulating the current page title
 *
 * @module      core/page_title
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

let pageTitleRoot = document.title;
let pageTitleSeparator = '|';

/**
 * Set the current page title
 *
 * @param {String} pageTitle
 */
export const setPageTitle = (pageTitle) => {
    document.title = pageTitle;
};

/**
 * Prepend value to the page title root, and set as the current page title
 *
 * @param {String} pageTitlePrepend
 */
export const prependPageTitle = (pageTitlePrepend) =>
    setPageTitle(`${pageTitlePrepend} ${pageTitleSeparator} ${pageTitleRoot}`);

/**
 * Set the page title root, to be used later when prepending to the title
 *
 * By default, the page title root is the document title
 *
 * @param {String} titleRoot
 */
export const setPageTitleRoot = (titleRoot) => {
    pageTitleRoot = titleRoot;
};

/**
 * Set the page title separator
 *
 * By default, the page title separator is the pipe character
 *
 * @param {String} titleSeparator
 */
export const setPageTitleSeparator = (titleSeparator) => {
    pageTitleSeparator = titleSeparator;
};
