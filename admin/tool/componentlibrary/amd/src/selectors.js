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
 * @package    tool_componentlibrary
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    clipboardbutton: '.btn-clipboard',
    clipboardcontent: 'figure.highlight, div.highlight',
    searchinput: '[data-region="docsearch"] input',
    searchsubmit: '[data-region="docsearch"] .btn-submit',
    dropdownmenu: '[data-region="docsearch"] .dropdown-menu',
    componentlibrary: '[data-region="componentlibrary"]',
    jscode: '[data-action="runjs"]',
    mustachecode: '[data-region="mustachecode"]',
    mustacherawcontext: '[data-region="rawcontext"]',
    mustacherendered: '[data-region="mustacherendered"]',
    mustachesource: '[data-region="mustachesource"]',
    mustachecontext: '[data-region="mustachecontext"]',
};
