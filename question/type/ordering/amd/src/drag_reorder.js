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

/*
 * To make a set of things draggable, create a new instance of this object passing the
 * necessary config, as explained in the comment on the constructor.
 *
 * @package   qtype_ordering\drag_reorder
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

'use strict';

import $ from 'jquery';
import drag from 'core/dragdrop';
import Templates from 'core/templates';
import Notification from 'core/notification';
import {getString} from 'core/str';
import {prefetchString} from 'core/prefetch';

export default class DragReorder {

    // Class variables handling state.
    config = {reorderStart: undefined, reorderEnd: undefined}; // Config object with some basic definitions.
    dragStart = null; // Information about when and where the drag started.
    originalOrder = null; // Array of ids that's used to compare the state after the drag event finishes.

    // DOM Nodes and jQuery representations.
    orderList = null; // Order list (HTMLElement).
    itemDragging = null; // Item being moved by dragging (jQuery object).
    proxy = null; // Drag proxy (jQuery object).

    /**
     * Constructor.
     *
     * To make a list draggable, create a new instance of this object, passing the necessary config.
     * For example:
     * {
     *      // Selector for the list (or lists) to be reordered.
     *      list: 'ul.my-list',
     *
     *      // Selector, relative to the list selector, for the items that can be moved.
     *      item: '> li',
     *
     *      // While the proxy is being dragged, this class is added to the item being moved.
     *      // You can probably use "osep-itemmoving" here.
     *      itemMovingClass: "osep-itemmoving",
     *
     *      // This is a callback which, when called with the DOM node for an item,
     *      // returns the string that uniquely identifies each item.
     *      // Therefore, the result of the drag action will be represented by the array
     *      // obtained by calling this method on each item in the list in order.
     *      idGetter: function(item) { return node.id; },
     *
     *      // Function that will be called when a re-order starts (optional, can be not set).
     *      // Useful if you need to save information about the initial state.
     *      // This function should have two parameters. The first will be a
     *      // jQuery object for the list that was reordered, the second will
     *      // be the jQuery object for the item moved - which will not yet have been moved.
     *      // Note, it is quite possible for reorderStart to be called with no
     *      // subsequent call to reorderDone.
     *      reorderStart: function($list, $item) { ... }
     *
     *      // Function that will be called when a drag has finished, and the list
     *      // has been reordered. This function should have three parameters. The first will be
     *      // a jQuery object for the list that was reordered, the second will be the jQuery
     *      // object for the item moved, and the third will be the new order, which is
     *      // an array of ids obtained by calling idGetter on each item in the list in order.
     *      // This callback will only be called in the new order is actually different from the old order.
     *      reorderDone: function($list, $item, newOrder) { ... }
     *
     *      // Function that is always called when a re-order ends (optional, can be not set)
     *      // whether the order has changed. Useful if you need to undo changes made
     *      // in reorderStart, since reorderDone is only called if the new order is different
     *      // from the original order.
     *      reorderEnd: function($list, $item) { ... }
     *  }
     *
     * There is a subtlety, If you have items in your list that do not have a drag handle,
     * they are considered to be placeholders in otherwise empty containers.
     *
     * @param {Object} config As above.
     */
    constructor(config) {
        // Bring in the config to our state.
        this.config = config;

        // Get the list we'll be working with this time.
        this.orderList = document.querySelector(this.config.list);

        this.startListeners();
    }

    /**
     * Start the listeners for the list.
     */
    startListeners() {
        /**
         * Handle mousedown or touchstart events on the list.
         *
         * @param {Event} e The event.
         */
        const pointerHandle = e => {
            if (e.target.closest(this.config.item) && !e.target.closest(this.config.actionButton)) {
                this.itemDragging = $(e.target.closest(this.config.item));
                const details = drag.prepare(e);
                if (details.start) {
                    this.startDrag(e, details);
                }
            }
        };
        // Set up the list listeners for moving list items around.
        this.orderList.addEventListener('mousedown', pointerHandle);
        this.orderList.addEventListener('touchstart', pointerHandle);
        this.orderList.addEventListener('click', this.itemMovedByClick.bind(this));
    }

    /**
     * Start dragging.
     *
     * @param {Event} e The event which is either mousedown or touchstart.
     * @param {Object} details Object with start (boolean flag) and x, y (only if flag true) values
     */
    startDrag(e, details) {
        this.dragStart = {
            time: new Date().getTime(),
            x: details.x,
            y: details.y
        };

        if (typeof this.config.reorderStart !== 'undefined') {
            this.config.reorderStart(this.itemDragging.closest(this.config.list), this.itemDragging);
        }

        this.originalOrder = this.getCurrentOrder();

        Templates.renderForPromise('qtype_ordering/proxyhtml', {
            itemHtml: this.itemDragging.html(),
            itemClassName: this.itemDragging.attr('class'),
            listClassName: this.orderList.classList.toString(),
            proxyStyles: [
                `width: ${this.itemDragging.outerWidth()}px;`,
                `height: ${this.itemDragging.outerHeight()}px;`,
            ].join(' '),
        }).then(({html, js}) => {
            this.proxy = $(Templates.appendNodeContents(document.body, html, js)[0]);
            this.proxy.css(this.itemDragging.offset());

            this.itemDragging.addClass(this.config.itemMovingClass);

            this.updateProxy();
            // Start drag.
            drag.start(e, this.proxy, this.dragMove.bind(this), this.dragEnd.bind(this));
        }).catch(Notification.exception);
    }

    /**
     * Move the proxy to the current mouse position.
     */
    dragMove() {
        let closestItem = null;
        let closestDistance = null;
        this.orderList.querySelectorAll(this.config.item).forEach(element => {
            const distance = this.distanceBetweenElements(element);
            if (closestItem === null || distance < closestDistance) {
                closestItem = $(element);
                closestDistance = distance;
            }
        });

        if (closestItem[0] === this.itemDragging[0]) {
            return;
        }

        // Set offset depending on if item is being dragged downwards/upwards.
        const offsetValue = this.midY(this.proxy) < this.midY(closestItem) ? 20 : -20;
        if (this.midY(this.proxy) + offsetValue < this.midY(closestItem)) {
            this.itemDragging.insertBefore(closestItem);
        } else {
            this.itemDragging.insertAfter(closestItem);
        }
        this.updateProxy();
    }

    /**
     * Update proxy's position.
     */
    updateProxy() {
        const items = [...this.orderList.querySelectorAll(this.config.item)];
        for (let i = 0; i < items.length; ++i) {
            if (this.itemDragging[0] === items[i]) {
                this.proxy.find('li').attr('value', i + 1);
                break;
            }
        }
    }

    /**
     * End dragging.
     */
    dragEnd() {
        if (typeof this.config.reorderEnd !== 'undefined') {
            this.config.reorderEnd(this.itemDragging.closest(this.config.list), this.itemDragging);
        }

        if (!this.arrayEquals(this.originalOrder, this.getCurrentOrder())) {
            // Order has changed, call the callback.
            this.config.reorderDone(this.itemDragging.closest(this.config.list), this.itemDragging, this.getCurrentOrder());

            getString('moved', 'qtype_ordering', {
                item: this.itemDragging.find('[data-itemcontent]').text().trim(),
                position: this.itemDragging.index() + 1,
                total: this.orderList.querySelectorAll(this.config.item).length
            }).then((str) => {
                this.config.announcementRegion.innerHTML = str;
            });
        }

        // Clean up after the drag is finished.
        this.proxy.remove();
        this.proxy = null;
        this.itemDragging.removeClass(this.config.itemMovingClass);
        this.itemDragging = null;
        this.dragStart = null;
    }

    /**
     * Handles the movement of an item by click.
     *
     * @param {MouseEvent} e The pointer event.
     */
    itemMovedByClick(e) {
        const actionButton = e.target.closest(this.config.actionButton);
        if (actionButton) {
            this.itemDragging = $(e.target.closest(this.config.item));

            // Store the current state of the list.
            this.originalOrder = this.getCurrentOrder();

            switch (actionButton.dataset.action) {
                case 'move-backward':
                    e.preventDefault();
                    e.stopPropagation();
                    if (this.itemDragging.prev().length) {
                        this.itemDragging.prev().insertAfter(this.itemDragging);
                    }
                    break;
                case 'move-forward':
                    e.preventDefault();
                    e.stopPropagation();
                    if (this.itemDragging.next().length) {
                        this.itemDragging.next().insertBefore(this.itemDragging);
                    }
                    break;
            }

            // After we have potentially moved the item, we need to check if the order has changed.
            if (!this.arrayEquals(this.originalOrder, this.getCurrentOrder())) {
                // Order has changed, call the callback.
                this.config.reorderDone(this.itemDragging.closest(this.config.list), this.itemDragging, this.getCurrentOrder());

                // When moving an item to the first or last position, the button that was clicked will be hidden.
                // In this case, we need to focus the other button.
                if (!this.itemDragging.prev().length) {
                    // Focus the 'next' action button.
                    this.itemDragging.find('[data-action="move-forward"]').focus();
                } else if (!this.itemDragging.next().length) {
                    // Focus the 'previous' action button.
                    this.itemDragging.find('[data-action="move-backward"]').focus();
                }

                getString('moved', 'qtype_ordering', {
                    item: this.itemDragging.find('[data-itemcontent]').text().trim(),
                    position: this.itemDragging.index() + 1,
                    total: this.orderList.querySelectorAll(this.config.item).length
                }).then((str) => {
                    this.config.announcementRegion.innerHTML = str;
                });
            }
        }
    }

    /**
     * Get the x-position of the middle of the DOM node represented by the given jQuery object.
     *
     * @param {jQuery} node jQuery wrapping a DOM node.
     * @returns {number} Number the x-coordinate of the middle (left plus half outerWidth).
     */
    midX(node) {
        return node.offset().left + node.outerWidth() / 2;
    }

    /**
     * Get the y-position of the middle of the DOM node represented by the given jQuery object.
     *
     * @param {jQuery} node jQuery wrapped DOM node.
     * @returns {number} Number the y-coordinate of the middle (top plus half outerHeight).
     */
    midY(node) {
        return node.offset().top + node.outerHeight() / 2;
    }

    /**
     * Calculate the distance between the centres of two elements.
     *
     * @param {HTMLLIElement} element DOM node of a list item.
     * @return {number} number the distance in pixels.
     */
    distanceBetweenElements(element) {
        const [e1, e2] = [$(element), $(this.proxy)];
        const [dx, dy] = [this.midX(e1) - this.midX(e2), this.midY(e1) - this.midY(e2)];
        return Math.sqrt(dx * dx + dy * dy);
    }

    /**
     * Get the current order of the list containing itemDragging.
     *
     * @returns {Array} Array of strings, the id of each element in order.
     */
    getCurrentOrder() {
        return this.itemDragging.closest(this.config.list).find(this.config.item).map(
            (index, item) => {
                return this.config.idGetter(item);
            }).get();
    }

    /**
     * Compare two arrays which contain primitive types to see if they are equal.
     * @param {Array} a1 first array.
     * @param {Array} a2 second array.
     * @return {Boolean} boolean true if they both contain the same elements in the same order, else false.
     */
    arrayEquals(a1, a2) {
        return a1.length === a2.length &&
            a1.every((v, i) => {
                return v === a2[i];
            });
    }

    /**
     * Initialise one ordering question.
     *
     * @param {String} sortableid id of ul for this question.
     * @param {String} responseid id of hidden field for this question.
     */
    static init(sortableid, responseid) {
        new DragReorder({
            actionButton: '[data-action]',
            announcementRegion: document.querySelector(`#${sortableid}-announcement`),
            list: 'ul#' + sortableid,
            item: 'li.sortableitem',
            itemMovingClass: "current-drop",
            idGetter: item => {
                return item.id;
            },
            reorderDone: (list, item, newOrder) => {
                $('input#' + responseid)[0].value = newOrder.join(',');
            }
        });

        prefetchString('qtype_ordering', 'moved');
    }
}
