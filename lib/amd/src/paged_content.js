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
 * Javascript to load and render a paged content section.
 *
 * @module     core/paged_content
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/paged_content_pages',
    'core/paged_content_paging_bar',
    'core/paged_content_paging_bar_limit_selector',
    'core/paged_content_paging_dropdown'
],
function(
    $,
    Pages,
    PagingBar,
    PagingBarLimitSelector,
    Dropdown
) {

    /**
     * Initialise the paged content region by running the pages
     * module and initialising any paging controls in the DOM.
     *
     * @param {object} root The paged content container element
     * @param {function} renderPagesContentCallback (optional) A callback function to render a
     *                                              content page. See core/paged_content_pages for
     *                                              more defails.
     * @param {string} namespaceOverride (optional) Provide a unique namespace override. If none provided defaults
     *                                      to generate html's id
     */
    var init = function(root, renderPagesContentCallback, namespaceOverride) {
        root = $(root);
        var pagesContainer = root.find(Pages.rootSelector);
        var pagingBarContainer = root.find(PagingBar.rootSelector);
        var dropdownContainer = root.find(Dropdown.rootSelector);
        var pagingBarLimitSelectorContainer = root.find(PagingBarLimitSelector.rootSelector);
        var id = root.attr('id');

        // Set the id to the custom namespace provided
        if (namespaceOverride) {
            id = namespaceOverride;
        }

        Pages.init(pagesContainer, id, renderPagesContentCallback);

        if (pagingBarContainer.length) {
            PagingBar.init(pagingBarContainer, id);
        }

        if (pagingBarLimitSelectorContainer.length) {
            PagingBarLimitSelector.init(pagingBarLimitSelectorContainer, id);
        }

        if (dropdownContainer.length) {
            Dropdown.init(dropdownContainer, id);
        }
    };

    return {
        init: init,
        rootSelector: '[data-region="paged-content-container"]'
    };
});
