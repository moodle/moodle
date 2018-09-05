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
 * Paging content module.
 *
 * @module     block_myoverview/paging_content
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'block_myoverview/paging_bar'],
        function($, Templates, PagingBar) {

    var SELECTORS = {
        ROOT: '[data-region="paging-content"]',
        PAGE_REGION: '[data-region="paging-content-item"]'
    };

    /**
     * Constructor of the paging content module.
     *
     * @param {object} root
     * @param {object} pagingBarElement
     * @constructor
     */
    var PagingContent = function(root, pagingBarElement) {
        this.root = $(root);
        this.pagingBar = $(pagingBarElement);

    };

    PagingContent.rootSelector = SELECTORS.ROOT;

    /**
     * Load content and create page.
     *
     * @param {Number} pageNumber
     * @returns {*|Promise}
     */
    PagingContent.prototype.createPage = function(pageNumber) {

        return this.loadContent(pageNumber).then(function(html, js) {
            Templates.appendNodeContents(this.root, html, js);
        }.bind(this)).then(function() {
                return this.findPage(pageNumber);
            }.bind(this)
        );
    };

    /**
     * Find a page by the number.
     *
     * @param {Number} pageNumber The number of the page to be found.
     * @returns {*} Page root
     */
    PagingContent.prototype.findPage = function(pageNumber) {
        return this.root.find('[data-page="' + pageNumber + '"]');
    };

    /**
     * Make a page visible.
     *
     * @param {Number} pageNumber The number of the page to be visible.
     */
    PagingContent.prototype.showPage = function(pageNumber) {

        var existingPage = this.findPage(pageNumber);
        this.root.find(SELECTORS.PAGE_REGION).addClass('hidden');

        if (existingPage.length) {
            existingPage.removeClass('hidden');
        } else {
            this.createPage(pageNumber).done(function(newPage) {
                newPage.removeClass('hidden');
            });
        }
    };

    /**
     * Event listeners.
     */
    PagingContent.prototype.registerEventListeners = function() {

        this.pagingBar.on(PagingBar.events.PAGE_SELECTED, function(e, data) {
            if (!data.isSamePage) {
                this.showPage(data.pageNumber);
            }
        }.bind(this));
    };

    return PagingContent;
});
