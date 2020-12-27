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
 * Contain the logic for modal backdrops.
 *
 * @module     core/modal_backdrop
 * @class      modal_backdrop
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core/fullscreen'],
     function($, Templates, Notification, Fullscreen) {

    var SELECTORS = {
        ROOT: '[data-region="modal-backdrop"]',
    };

    /**
     * Constructor for ModalBackdrop.
     *
     * @param {object} root The root element for the modal backdrop
     */
    var ModalBackdrop = function(root) {
        this.root = $(root);
        this.isAttached = false;

        if (!this.root.is(SELECTORS.ROOT)) {
            Notification.exception({message: 'Element is not a modal backdrop'});
        }
    };

    /**
     * Get the root element of this modal backdrop.
     *
     * @method getRoot
     * @return {object} jQuery object
     */
    ModalBackdrop.prototype.getRoot = function() {
        return this.root;
    };

     /**
      * Gets the jQuery wrapped node that the Modal should be attached to.
      *
      * @returns {jQuery}
      */
     ModalBackdrop.prototype.getAttachmentPoint = function() {
         return $(Fullscreen.getElement() || document.body);
     };

    /**
     * Add the modal backdrop to the page, if it hasn't already been added.
     *
     * @method attachToDOM
     */
    ModalBackdrop.prototype.attachToDOM = function() {
        this.getAttachmentPoint().append(this.root);

        if (this.isAttached) {
            return;
        }

        this.isAttached = true;
    };

    /**
     * Set the z-index value for this backdrop.
     *
     * @method setZIndex
     * @param {int} value The z-index value
     */
    ModalBackdrop.prototype.setZIndex = function(value) {
        this.root.css('z-index', value);
    };

    /**
     * Check if this backdrop is visible.
     *
     * @method isVisible
     * @return {bool}
     */
    ModalBackdrop.prototype.isVisible = function() {
        return this.root.hasClass('show');
    };

    /**
     * Check if this backdrop has CSS transitions applied.
     *
     * @method hasTransitions
     * @return {bool}
     */
    ModalBackdrop.prototype.hasTransitions = function() {
        return this.getRoot().hasClass('fade');
    };

    /**
     * Display this backdrop. The backdrop will be attached to the DOM if it hasn't
     * already been.
     *
     * @method show
     */
    ModalBackdrop.prototype.show = function() {
        if (this.isVisible()) {
            return;
        }

        this.attachToDOM();

        this.root.removeClass('hide').addClass('show');
    };

    /**
     * Hide this backdrop.
     *
     * @method hide
     */
    ModalBackdrop.prototype.hide = function() {
        if (!this.isVisible()) {
            return;
        }

        if (this.hasTransitions()) {
            // Wait for CSS transitions to complete before hiding the element.
            this.getRoot().one('transitionend webkitTransitionEnd oTransitionEnd', function() {
                this.getRoot().removeClass('show').addClass('hide');
            }.bind(this));
        } else {
            this.getRoot().removeClass('show').addClass('hide');
        }

        // Ensure the modal is moved onto the body node if it is still attached to the DOM.
        if ($(document.body).find(this.getRoot()).length) {
            $(document.body).append(this.getRoot());
        }
    };

    /**
     * Remove this backdrop from the DOM.
     *
     * @method destroy
     */
    ModalBackdrop.prototype.destroy = function() {
        this.root.remove();
    };

    return ModalBackdrop;
});
