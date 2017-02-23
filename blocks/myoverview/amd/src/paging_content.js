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
 * Javascript to load and render the list of calendar events for a
 * given day range.
 *
 * @module     block_myoverview/event_list
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'block_myoverview/paging_bar'],
        function($, Templates, PagingBar) {

    var SELECTORS = {
        ROOT: '[data-region="paging-content"]',
    };

    var PagingContent = function(root, pagingBarElement, loadContentCallback) {
        this.root = $(root);
        this.pagingBar = $(pagingBarElement);
        this.loadContent = loadContentCallback;
    };

    PagingContent.rootSelector = SELECTORS.ROOT;

    PagingContent.prototype.createPage = function(pageNumber) {
        this.loadContent(pageNumber).done(function(html, js) {
            Templates.appendTo(this.root, html, js);
        }.bind(this));

        var page = null;

        return page;
    };

    PagingContent.prototype.findPage = function(pageNumber) {

    };

    PagingContent.prototype.showPage = function(pageNumber) {
        var existingPage = this.findPage(pageNumber);

        if (existingPage) {
            existingPage.addClass('active');
        } else {
            var newPage = this.createPage(pageNumber);
            newPage.addClass('active');

            this.root.append(newPage);
        }
    };

    PagingContent.prototype.registerEventListeners = function() {
        this.pagingBar.one(PagingBar.events.PAGE_SELECTED, function(e, data) {
            if (!data.isSamePage) {
                this.showPage(data.pageNumber);
            };
        }.bind(this));
    };

    return PagingContent;
});
