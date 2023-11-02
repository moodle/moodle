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
 * Generic library to allow things in a vertical list to be re-ordered using drag and drop.
 *
 * To make a set of things draggable, create a new instance of this object passing the
 * necessary config, as explained in the comment on the constructor.
 *
 * @package   qtype_ordering
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

'use strict';

import $ from 'jquery';
import drag from 'core/dragdrop';
import keys from 'core/key_codes';

export default class DragReorder {

    config = {reorderStart: 'undefined', reorderEnd: 'undefined'}; // Config object with some basic definitions.
    dragStart = null; // Information about when and where the drag started.
    originalOrder = null; // Array of ids.
    itemDragging = null; // Item being moved by dragging (jQuery object).
    itemMoving = null; // Item being moved using the accessible modal (jQuery object).
    orderList = null; // Order list (jQuery object).
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
     *      // The user actually drags a proxy object, which is constructed from this string,
     *      // and then added directly as a child of <body>. The token %%ITEM_HTML%% is
     *      // replaced with the innerHtml of the item being dragged. The token %%ITEM_CLASS_NAME%%
     *      // is replaced with the class attribute of the item being dragged. Because of this,
     *      // the styling of the contents of your list item needs to work for the proxy, as well as
     *      // for items in place in the context of the list. Your CSS also needs to ensure
     *      // that this proxy has position: absolute. You probably want other styles, like a
     *      // drop shadow. Using class osep-itemmoving might be all you need to do.
     *      proxyHtml: '<div class="osep-itemmoving %%ITEM_CLASS_NAME%%">%%ITEM_HTML%%</div>,
     *
     *      // While the proxy is being dragged, this class is added to the item being moved.
     *      // You can probably use "osep-itemmoving" here.
     *      itemMovingClass: "osep-itemmoving",
     *
     *      // This is a callback which, when called with the DOM node for an item,
     *      // returns the string that uniquely identifies each item.
     *      // Therefore, the result of the drag action will be represented by the array
     *      // obtained by calling this method on each item in the list in order.
     *      idGetter: function(item) { return $(node).data('id'); },
     *
     *      // This is a callback which, when called with the DOM node for an item,
     *      // returns a string that is the name of the item.
     *      nameGetter: function(item) { return $(node).text(); },
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

        this.config = config;

        this.config.itemInPage = this.combineSelectors(config.list, config.item);

        // AJAX for section drag and click-to-move.
        $(this.config.list).on('mousedown touchstart', config.item, e => {
            const details = drag.prepare(e);
            if (details.start) {
                this.startDrag(e, details);
            }
        });

        $(this.config.list).on('keydown', config.item, e => {
            this.itemMoving = $(e.currentTarget).closest(config.itemInPage);
            this.originalOrder = this.getCurrentOrder();
            this.itemMovedByKeyboard(e, this.itemMoving);
            const newOrder = this.getCurrentOrder();
            if (!this.arrayEquals(this.originalOrder, newOrder)) {
                // Order has changed, call the callback.
                this.config.reorderDone(this.itemMoving.closest(this.config.list), this.itemMoving, newOrder);
            }
        });

        // Make the items tabbable.
        $(this.config.itemInPage).attr('tabindex', '0');
    }

    /**
     * Start dragging.
     *
     * @param {jQuery} e The jQuery event which is either mousedown or touchstart.
     * @param {Object} details Object with start (boolean flag) and x, y (only if flag true) values
     */
    startDrag(e, details) {
        this.orderList = $(this.config.list);

        this.dragStart = {
            time: new Date().getTime(),
            x: details.x,
            y: details.y
        };

        this.itemDragging = $(e.currentTarget).closest(this.config.itemInPage);

        if (typeof this.config.reorderStart !== 'undefined') {
            this.config.reorderStart(this.itemDragging.closest(this.config.list), this.itemDragging);
        }

        this.originalOrder = this.getCurrentOrder();
        this.proxy = $(this.config.proxyHtml.replace('%%ITEM_HTML%%', this.itemDragging.html())
            .replace('%%ITEM_CLASS_NAME%%', this.itemDragging.attr('class'))
            .replace('%%LIST_CLASS_NAME%%', this.orderList.attr('class')));

        $(document.body).append(this.proxy);
        this.proxy.css('position', 'absolute');
        this.proxy.css(this.itemDragging.offset());
        this.proxy.width(this.itemDragging.outerWidth());
        this.proxy.height(this.itemDragging.outerHeight());
        this.itemDragging.addClass(this.config.itemMovingClass);
        this.updateProxy();

        // Start drag.
        drag.start(e, this.proxy, this.dragMove.bind(this), this.dragEnd.bind(this));
    }

    /**
     * Move the proxy to the current mouse position.
     */
    dragMove() {
        const list = this.itemDragging.closest(this.config.list);
        let closestItem = null;
        let closestDistance = null;
        list.find(this.config.item).each((index, element) => {
            const distance = this.distanceBetweenElements(element, this.proxy);
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
        const list = this.itemDragging.closest('ol, ul');
        const items = list.find('li');
        const count = items.length;
        for (let i = 0; i < count; ++i) {
            if (this.itemDragging[0] === items[i]) {
                this.proxy.find('li').attr('value', i + 1);
                break;
            }
        }
    }

    /**
     * Our outer and inner are two CSS selectors, which may contain commas.
     * We want to combine them safely. So for instance combineSelectors('a, b', 'c, d')
     * gives 'a c, a d, b c, b d'.
     *
     * @param {String} outer The selector for the outer element.
     * @param {String} inner The selector for the inner element.
     * @returns {String} The combined selector used to listen to the list item.
     */
    combineSelectors(outer, inner) {
        let combined = [];
        outer.split(',').forEach(firstSelector => {
            inner.split(',').forEach(secondSelector => {
                combined.push(firstSelector.trim() + ' ' + secondSelector.trim());
            });
        });
        return combined.join(', ');
    }

    /**
     * End dragging.
     *
     * @param {number} x X co-ordinate
     * @param {number} y Y co-ordinate
     */
    dragEnd(x, y) {
        if (typeof this.config.reorderEnd !== 'undefined') {
            this.config.reorderEnd(this.itemDragging.closest(this.config.list), this.itemDragging);
        }

        const newOrder = this.getCurrentOrder();
        if (!this.arrayEquals(this.originalOrder, newOrder)) {
            // Order has changed, call the callback.
            this.config.reorderDone(this.itemDragging.closest(this.config.list), this.itemDragging, newOrder);

        } else if (new Date().getTime() - this.dragStart.time < 500 &&
            Math.abs(this.dragStart.x - x) < 10 && Math.abs(this.dragStart.y - y) < 10) {
            // This was really a click. Set the focus on the current item.
            this.itemDragging[0].focus();
        }
        this.proxy.remove();
        this.proxy = null;
        this.itemDragging.removeClass(this.config.itemMovingClass);
        this.itemDragging = null;
        this.dragStart = null;
    }

    /**
     * Items can be moved and placed using certain keys.
     * Tab for tabbing though and choose the item to be moved
     * space, arrow-right arrow-down for moving current element forwards.
     * arrow-right arrow-down for moving the current element backwards.
     *
     * @param {Event} e The keyboard event.
     * @param {jQuery} current An object representing the current moving item and the previous item we just moved past.
     */
    itemMovedByKeyboard(e, current) {
        switch (e.keyCode) {
            case keys.space:
            case keys.arrowRight:
            case keys.arrowDown:
                e.preventDefault();
                e.stopPropagation();
                if (current.next().length) {
                    current.next().insertBefore(current);
                }
                break;

            case keys.arrowLeft:
            case keys.arrowUp:
                e.preventDefault();
                e.stopPropagation();
                if (current.prev().length) {
                    current.prev().insertAfter(current);
                }
                break;
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
     * @param {HTMLLIElement} element1 DOM node of a list item.
     * @param {HTMLLIElement} element2 DOM node of a list item.
     * @return {number} number the distance in pixels.
     */
    distanceBetweenElements(element1, element2) {
        const [e1, e2] = [$(element1), $(element2)];
        const [dx, dy] = [this.midX(e1) - this.midX(e2), this.midY(e1) - this.midY(e2)];
        return Math.sqrt(dx * dx + dy * dy);
    }

    /**
     * Get the current order of the list containing itemDragging.
     *
     * @returns {Array} Array of strings, the id of each element in order.
     */
    getCurrentOrder() {
        return (this.itemDragging || this.itemMoving).closest(this.config.list).find(this.config.item).map(
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
            list: 'ul#' + sortableid,
            item: 'li.sortableitem',
            proxyHtml: '<div class="que ordering dragproxy">' +
                '<ul class="%%LIST_CLASS_NAME%%"><li class="%%ITEM_CLASS_NAME%% item-moving">' +
                '%%ITEM_HTML%%</li></ul></div>',
            itemMovingClass: "current-drop",
            idGetter: item => {
                return $(item).attr('id');
            },
            nameGetter: item => {
                return $(item).text;
            },
            reorderDone: (list, item, newOrder) => {
                $('input#' + responseid)[0].value = newOrder.join(',');
            }
        });
    }
}
