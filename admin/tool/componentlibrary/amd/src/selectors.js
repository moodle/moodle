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
 * Selectors for the component library
 *
 * @module     tool_componentlibrary/selectors
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default {
    /**
     * A selector relating to the 'Copy to clipboard' button.
     *
     * @type {string}
     */
    clipboardbutton: '.btn-clipboard',

    /**
     * A selector relating to the content copied by the 'Copy to clipboard' button.
     *
     * @type {string}
     */
    clipboardcontent: 'figure.highlight, div.highlight',

    /**
     * A selector relating to the 'Search' text input.
     *
     * @type {string}
     */
    searchinput: '[data-region="docsearch"] input',

    /**
     * A selector relating to the 'Search' submit btton.
     *
     * @type {string}
     */
    searchsubmit: '[data-region="docsearch"] .btn-submit',

    /**
     * A selector relating to the search dropdown menu.
     *
     * @type {string}
     */
    dropdownmenu: '[data-region="docsearch"] .dropdown-menu',

    /**
     * A selector relating to the entire Component Library content region.
     *
     * @type {string}
     */
    componentlibrary: '[data-region="componentlibrary"]',

    /**
     * A selector relating to JS Code which is to be run for examples to function.
     *
     * @type {string}
     */
    jscode: '[data-action="runjs"]',

    /**
     * A selector relating to Mustache Template code regions.
     *
     * @type {string}
     */
    mustachecode: '[data-region="mustachecode"]',

    /**
     * A selector relating to raw Mustache content regions.
     *
     * @type {string}
     */
    mustacherawcontext: '[data-region="rawcontext"]',

    /**
     * A selector relating to rendered Mustache content regions.
     *
     * @type {string}
     */
    mustacherendered: '[data-region="mustacherendered"]',

    /**
     * A selector relating to Mustache source code regions.
     *
     * @type {string}
     */
    mustachesource: '[data-region="mustachesource"]',

    /**
     * A selector relating to Mustache context regions.
     *
     * @type {string}
     */
    mustachecontext: '[data-region="mustachecontext"]',
};
