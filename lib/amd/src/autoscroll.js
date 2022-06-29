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
 * JavaScript to provide automatic scrolling, e.g. during a drag operation.
 *
 * Note: this module is defined statically. It is a singleton. You
 * can only have one use of it active at any time. However, since this
 * is usually used in relation to drag-drop, and since you only ever
 * drag one thing at a time, this is not a problem in practice.
 *
 * @module     core/autoscroll
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */
define(['jquery'], function($) {
    /**
     * @alias module:core/autoscroll
     */
    var autoscroll = {
        /**
         * Size of area near edge of screen that triggers scrolling.
         * @private
         */
        SCROLL_THRESHOLD: 30,

        /**
         * How frequently to scroll window.
         * @private
         */
        SCROLL_FREQUENCY: 1000 / 60,

        /**
         * How many pixels to scroll per unit (1 = max scroll 30).
         * @private
         */
        SCROLL_SPEED: 0.5,

        /**
         * Set if currently scrolling up/down.
         * @private
         */
        scrollingId: null,

        /**
         * Speed we are supposed to scroll (range 1 to SCROLL_THRESHOLD).
         * @private
         */
        scrollAmount: 0,

        /**
         * Optional callback called when it scrolls
         * @private
         */
        callback: null,

        /**
         * Starts automatically scrolling if user moves near edge of window.
         * This should be called in response to mouse down or touch start.
         *
         * @public
         * @param {Function} callback Optional callback that is called every time it scrolls
         */
        start: function(callback) {
            $(window).on('mousemove', autoscroll.mouseMove);
            $(window).on('touchmove', autoscroll.touchMove);
            autoscroll.callback = callback;
        },

        /**
         * Stops automatically scrolling. This should be called in response to mouse up or touch end.
         *
         * @public
         */
        stop: function() {
            $(window).off('mousemove', autoscroll.mouseMove);
            $(window).off('touchmove', autoscroll.touchMove);
            if (autoscroll.scrollingId !== null) {
                autoscroll.stopScrolling();
            }
        },

        /**
         * Event handler for touch move.
         *
         * @private
         * @param {Object} e Event
         */
        touchMove: function(e) {
            for (var i = 0; i < e.changedTouches.length; i++) {
                autoscroll.handleMove(e.changedTouches[i].clientX, e.changedTouches[i].clientY);
            }
        },

        /**
         * Event handler for mouse move.
         *
         * @private
         * @param {Object} e Event
         */
        mouseMove: function(e) {
            autoscroll.handleMove(e.clientX, e.clientY);
        },

        /**
         * Handles user moving.
         *
         * @private
         * @param {number} clientX X
         * @param {number} clientY Y
         */
        handleMove: function(clientX, clientY) {
            // If near the bottom or top, start auto-scrolling.
            if (clientY < autoscroll.SCROLL_THRESHOLD) {
                autoscroll.scrollAmount = -Math.min(autoscroll.SCROLL_THRESHOLD - clientY, autoscroll.SCROLL_THRESHOLD);
            } else if (clientY > $(window).height() - autoscroll.SCROLL_THRESHOLD) {
                autoscroll.scrollAmount = Math.min(clientY - ($(window).height() - autoscroll.SCROLL_THRESHOLD),
                    autoscroll.SCROLL_THRESHOLD);
            } else {
                autoscroll.scrollAmount = 0;
            }
            if (autoscroll.scrollAmount && autoscroll.scrollingId === null) {
                autoscroll.startScrolling();
            } else if (!autoscroll.scrollAmount && autoscroll.scrollingId !== null) {
                autoscroll.stopScrolling();
            }
        },

        /**
         * Starts automatic scrolling.
         *
         * @private
         */
        startScrolling: function() {
            var maxScroll = $(document).height() - $(window).height();
            autoscroll.scrollingId = window.setInterval(function() {
                // Work out how much to scroll.
                var y = $(window).scrollTop();
                var offset = Math.round(autoscroll.scrollAmount * autoscroll.SCROLL_SPEED);
                if (y + offset < 0) {
                    offset = -y;
                }
                if (y + offset > maxScroll) {
                    offset = maxScroll - y;
                }
                if (offset === 0) {
                    return;
                }

                // Scroll.
                $(window).scrollTop(y + offset);
                var realOffset = $(window).scrollTop() - y;
                if (realOffset === 0) {
                    return;
                }

                // Inform callback
                if (autoscroll.callback) {
                    autoscroll.callback(realOffset);
                }

            }, autoscroll.SCROLL_FREQUENCY);
        },

        /**
         * Stops the automatic scrolling.
         *
         * @private
         */
        stopScrolling: function() {
            window.clearInterval(autoscroll.scrollingId);
            autoscroll.scrollingId = null;
        }
    };

    return {
        /**
         * Starts automatic scrolling if user moves near edge of window.
         * This should be called in response to mouse down or touch start.
         *
         * @public
         * @param {Function} callback Optional callback that is called every time it scrolls
         */
        start: autoscroll.start,

        /**
         * Stops automatic scrolling. This should be called in response to mouse up or touch end.
         *
         * @public
         */
        stop: autoscroll.stop
    };

});
