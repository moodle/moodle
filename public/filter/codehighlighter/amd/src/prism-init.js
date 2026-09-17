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
 * Prism.js initialization.
 *
 * @module     filter/codegihlighter
 * @copyright  2023 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['./prism'], function(PrismJS) {

    /** @type {string} The language class selector Prism uses to collect the elements to highlight. */
    const languageselector = '[class*="language-"], [class*="lang-"]';

    /**
     * Whether the language of the element comes from an actual code block markup rather than from the page itself.
     *
     * Prism resolves the language of a code block by walking up the DOM until it finds an ancestor carrying a
     * "language-xxx" or "lang-xxx" class. That is intended behaviour and allows a wrapper element to set the
     * language for several blocks at once. Moodle however adds a "lang-xx" class (e.g. "lang-en") to the body
     * element, so that walk always succeeds and every plain <code> element on the page - including hand written
     * <pre><code> blocks without any language - ends up being highlighted as language "en" (or whatever language
     * the user currently is using).
     *
     * Discarding the elements whose closest language class is the one of the body leaves all regular Prism
     * behaviour intact, while making sure the language of the page is never mistaken for the language of a
     * code block.
     *
     * @param {Element} element The candidate element.
     * @returns {boolean} True if the element should be highlighted.
     */
    const hasCodeBlockLanguage = (element) => {
        const source = element.closest(languageselector);
        return source !== null && source !== document.body;
    };

    PrismJS.plugins.customClass.prefix('prism-');
    PrismJS.hooks.add('before-all-elements-highlight', (env) => {
        env.elements = env.elements.filter(hasCodeBlockLanguage);
    });
    PrismJS.highlightAll();
});
