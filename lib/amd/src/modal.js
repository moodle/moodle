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
 * Contain the logic for modals.
 *
 * @module     core/modal
 * @class      modal
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core/key_codes',
        'core/custom_interaction_events', 'core/modal_backdrop', 'core/event', 'core/modal_events'],
     function($, Templates, Notification, KeyCodes, CustomEvents, ModalBackdrop, Event, ModalEvents) {

    var SELECTORS = {
        CONTAINER: '[data-region="modal-container"]',
        MODAL: '[data-region="modal"]',
        HEADER: '[data-region="header"]',
        TITLE: '[data-region="title"]',
        BODY: '[data-region="body"]',
        FOOTER: '[data-region="footer"]',
        HIDE: '[data-action="hide"]',
        DIALOG: '[role=dialog]',
        MENU_BAR: '[role=menubar]',
        HAS_Z_INDEX: '.moodle-has-zindex',
        CAN_RECEIVE_FOCUS: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
    };

    var TEMPLATES = {
        LOADING: 'core/loading',
        BACKDROP: 'core/modal_backdrop',
    };

    /**
     * Module singleton for the backdrop to be reused by all Modal instances.
     */
    var backdropPromise;

    /**
     * A counter that gets incremented for each modal created. This can be
     * used to generate unique values for the modals.
     */
    var modalCounter = 0;

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var Modal = function(root) {
        this.root = $(root);
        this.modal = this.root.find(SELECTORS.MODAL);
        this.header = this.modal.find(SELECTORS.HEADER);
        this.title = this.header.find(SELECTORS.TITLE);
        this.body = this.modal.find(SELECTORS.BODY);
        this.footer = this.modal.find(SELECTORS.FOOTER);
        this.hiddenSiblings = [];
        this.isAttached = false;
        this.bodyJS = null;
        this.footerJS = null;
        this.modalCount = modalCounter++;

        if (!this.root.is(SELECTORS.CONTAINER)) {
            Notification.exception({message: 'Element is not a modal container'});
        }

        if (!this.modal.length) {
            Notification.exception({message: 'Container does not contain a modal'});
        }

        if (!this.header.length) {
            Notification.exception({message: 'Modal is missing a header region'});
        }

        if (!this.title.length) {
            Notification.exception({message: 'Modal header is missing a title region'});
        }

        if (!this.body.length) {
            Notification.exception({message: 'Modal is missing a body region'});
        }

        if (!this.footer.length) {
            Notification.exception({message: 'Modal is missing a footer region'});
        }

        this.registerEventListeners();
    };

    /**
     * Add the modal to the page, if it hasn't already been added. This includes running any
     * javascript that has been cached until now.
     *
     * @method attachToDOM
     */
    Modal.prototype.attachToDOM = function() {
        if (this.isAttached) {
            return;
        }

        $('body').append(this.root);

        // If we'd cached any JS then we can run it how that the modal is
        // attached to the DOM.
        if (this.bodyJS) {
            Templates.runTemplateJS(this.bodyJS);
            this.bodyJS = null;
        }

        if (this.footerJS) {
            Templates.runTemplateJS(this.footerJS);
            this.footerJS = null;
        }

        this.isAttached = true;
    };

    /**
     * Count the number of other visible modals (not including this one).
     *
     * @method countOtherVisibleModals
     * @return {int}
     */
    Modal.prototype.countOtherVisibleModals = function() {
        var count = 0;
        $('body').find(SELECTORS.CONTAINER).each(function(index, element) {
            element = $(element);

            // If we haven't found ourself and the element is visible.
            if (!this.root.is(element) && element.hasClass('show')) {
                count++;
            }
        }.bind(this));

        return count;
    };

    /**
     * Get the modal backdrop.
     *
     * @method getBackdrop
     * @return {object} jQuery promise
     */
    Modal.prototype.getBackdrop = function() {
        if (!backdropPromise) {
            backdropPromise = Templates.render(TEMPLATES.BACKDROP, {})
                .then(function(html) {
                    var element = $(html);

                    return new ModalBackdrop(element);
                })
                .fail(Notification.exception);
        }

        return backdropPromise;
    };

    /**
     * Get the root element of this modal.
     *
     * @method getRoot
     * @return {object} jQuery object
     */
    Modal.prototype.getRoot = function() {
        return this.root;
    };

    /**
     * Get the modal element of this modal.
     *
     * @method getModal
     * @return {object} jQuery object
     */
    Modal.prototype.getModal = function() {
        return this.modal;
    };

    /**
     * Get the modal title element.
     *
     * @method getTitle
     * @return {object} jQuery object
     */
    Modal.prototype.getTitle = function() {
        return this.title;
    };

    /**
     * Get the modal body element.
     *
     * @method getBody
     * @return {object} jQuery object
     */
    Modal.prototype.getBody = function() {
        return this.body;
    };

    /**
     * Get the modal footer element.
     *
     * @method getFooter
     * @return {object} jQuery object
     */
    Modal.prototype.getFooter = function() {
        return this.footer;
    };

    /**
     * Get the unique modal count.
     *
     * @method getModalCount
     * @return {int}
     */
    Modal.prototype.getModalCount = function() {
        return this.modalCount;
    };

    /**
     * Set the modal title element.
     *
     * This method is overloaded to take either a string value for the title or a jQuery promise that is resolved with
     * HTML most commonly from a Str.get_string call.
     *
     * @method setTitle
     * @param {(string|object)} value The title string or jQuery promise which resolves to the title.
     */
    Modal.prototype.setTitle = function(value) {
        var title = this.getTitle();

        this.asyncSet(value, title.html.bind(title));
    };

    /**
     * Set the modal body element.
     *
     * This method is overloaded to take either a string value for the body or a jQuery promise that is resolved with
     * HTML and Javascript most commonly from a Templates.render call.
     *
     * @method setBody
     * @param {(string|object)} value The body string or jQuery promise which resolves to the body.
     */
    Modal.prototype.setBody = function(value) {
        var body = this.getBody();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            body.html(value);
            Event.notifyFilterContentUpdated(body);
            this.getRoot().trigger(ModalEvents.bodyRendered, this);
        } else {
            var jsPendingId = 'amd-modal-js-pending-id-' + this.getModalCount();
            M.util.js_pending(jsPendingId);
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            var contentPromise = null;
            body.css('overflow', 'hidden');

            if (value.state() == 'pending') {
                // We're still waiting for the body promise to resolve so
                // let's show a loading icon.
                body.animate({height: '100px'}, 150);

                body.html('');
                contentPromise = Templates.render(TEMPLATES.LOADING, {})
                    .then(function(html) {
                        var loadingIcon = $(html).hide();
                        body.html(loadingIcon);
                        loadingIcon.fadeIn(150);

                        // We only want the loading icon to fade out
                        // when the content for the body has finished
                        // loading.
                        return $.when(loadingIcon.promise(), value);
                    })
                    .then(function(loadingIcon) {
                        // Once the content has finished loading and
                        // the loading icon has been shown then we can
                        // fade the icon away to reveal the content.
                        return loadingIcon.fadeOut(100).promise();
                    })
                    .then(function() {
                        return value;
                    });
            } else {
                // The content is already loaded so let's just display
                // it to the user. No need for a loading icon.
                contentPromise = value;
            }

            // Now we can actually display the content.
            contentPromise.then(function(html, js) {
                var result = null;

                if (this.isVisible()) {
                    // If the modal is visible then we should display
                    // the content gracefully for the user.
                    body.css('opacity', 0);
                    var currentHeight = body.innerHeight();
                    body.html(html);
                    // We need to clear any height values we've set here
                    // in order to measure the height of the content being
                    // added. This then allows us to animate the height
                    // transition.
                    body.css('height', '');
                    var newHeight = body.innerHeight();
                    body.css('height', currentHeight + 'px');
                    result = body.animate(
                        {height: newHeight + 'px', opacity: 1},
                        {duration: 150, queue: false}
                    ).promise();
                } else {
                    // Since the modal isn't visible we can just immediately
                    // set the content. No need to animate it.
                    body.html(html);
                }

                if (js) {
                    if (this.isAttached) {
                        // If we're in the DOM then run the JS immediately.
                        Templates.runTemplateJS(js);
                    } else {
                        // Otherwise cache it to be run when we're attached.
                        this.bodyJS = js;
                    }
                }
                Event.notifyFilterContentUpdated(body);
                this.getRoot().trigger(ModalEvents.bodyRendered, this);

                return result;
            }.bind(this))
            .fail(Notification.exception)
            .always(function() {
                // When we're done displaying all of the content we need
                // to clear the custom values we've set here.
                body.css('height', '');
                body.css('overflow', '');
                body.css('opacity', '');
                M.util.js_complete(jsPendingId);

                return;
            })
            .fail(Notification.exception);
        }
    };

    /**
     * Set the modal footer element. The footer element is made visible, if it
     * isn't already.
     *
     * This method is overloaded to take either a string
     * value for the body or a jQuery promise that is resolved with HTML and Javascript
     * most commonly from a Templates.render call.
     *
     * @method setFooter
     * @param {(string|object)} value The footer string or jQuery promise
     */
    Modal.prototype.setFooter = function(value) {
        // Make sure the footer is visible.
        this.showFooter();

        var footer = this.getFooter();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            footer.html(value);
        } else {
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            Templates.render(TEMPLATES.LOADING, {}).done(function(html) {
                footer.html(html);

                value.done(function(html, js) {
                    footer.html(html);

                    if (js) {
                        if (this.isAttached) {
                            // If we're in the DOM then run the JS immediately.
                            Templates.runTemplateJS(js);
                        } else {
                            // Otherwise cache it to be run when we're attached.
                            this.footerJS = js;
                        }
                    }
                }.bind(this));
            }.bind(this));
        }
    };

    /**
     * Check if the footer has any content in it.
     *
     * @method hasFooterContent
     * @return {bool}
     */
    Modal.prototype.hasFooterContent = function() {
        return this.getFooter().children().length ? true : false;
    };

    /**
     * Hide the footer element.
     *
     * @method hideFooter
     */
    Modal.prototype.hideFooter = function() {
        this.getFooter().addClass('hidden');
    };

    /**
     * Show the footer element.
     *
     * @method showFooter
     */
    Modal.prototype.showFooter = function() {
        this.getFooter().removeClass('hidden');
    };

    /**
     * Mark the modal as a large modal.
     *
     * @method setLarge
     */
    Modal.prototype.setLarge = function() {
        if (this.isLarge()) {
            return;
        }

        this.getModal().addClass('modal-lg');
    };

    /**
     * Check if the modal is a large modal.
     *
     * @method isLarge
     * @return {bool}
     */
    Modal.prototype.isLarge = function() {
        return this.getModal().hasClass('modal-lg');
    };

    /**
     * Mark the modal as a small modal.
     *
     * @method setSmall
     */
    Modal.prototype.setSmall = function() {
        if (this.isSmall()) {
            return;
        }

        this.getModal().removeClass('modal-lg');
    };

    /**
     * Check if the modal is a small modal.
     *
     * @method isSmall
     * @return {bool}
     */
    Modal.prototype.isSmall = function() {
        return !this.getModal().hasClass('modal-lg');
    };

    /**
     * Determine the highest z-index value currently on the page.
     *
     * @method calculateZIndex
     * @return {int}
     */
    Modal.prototype.calculateZIndex = function() {
        var items = $(SELECTORS.DIALOG + ', ' + SELECTORS.MENU_BAR + ', ' + SELECTORS.HAS_Z_INDEX);
        var zIndex = parseInt(this.root.css('z-index'));

        items.each(function(index, item) {
            item = $(item);
            // Note that webkit browsers won't return the z-index value from the CSS stylesheet
            // if the element doesn't have a position specified. Instead it'll return "auto".
            var itemZIndex = item.css('z-index') ? parseInt(item.css('z-index')) : 0;

            if (itemZIndex > zIndex) {
                zIndex = itemZIndex;
            }
        });

        return zIndex;
    };

    /**
     * Check if this modal is visible.
     *
     * @method isVisible
     * @return {bool}
     */
    Modal.prototype.isVisible = function() {
        return this.root.hasClass('show');
    };

    /**
     * Check if this modal has focus.
     *
     * @method hasFocus
     * @return {bool}
     */
    Modal.prototype.hasFocus = function() {
        var target = $(document.activeElement);
        return this.root.is(target) || this.root.has(target).length;
    };

    /**
     * Check if this modal has CSS transitions applied.
     *
     * @method hasTransitions
     * @return {bool}
     */
    Modal.prototype.hasTransitions = function() {
        return this.getRoot().hasClass('fade');
    };

    /**
     * Display this modal. The modal will be attached to the DOM if it hasn't
     * already been.
     *
     * @method show
     */
    Modal.prototype.show = function() {
        if (this.isVisible()) {
            return;
        }

        if (this.hasFooterContent()) {
            this.showFooter();
        } else {
            this.hideFooter();
        }

        if (!this.isAttached) {
            this.attachToDOM();
        }

        this.getBackdrop().done(function(backdrop) {
            var currentIndex = this.calculateZIndex();
            var newIndex = currentIndex + 2;
            var newBackdropIndex = newIndex - 1;
            this.root.css('z-index', newIndex);
            backdrop.setZIndex(newBackdropIndex);
            backdrop.show();

            this.root.removeClass('hide').addClass('show');
            this.accessibilityShow();
            this.getTitle().focus();
            $('body').addClass('modal-open');
            this.root.trigger(ModalEvents.shown, this);
        }.bind(this));
    };

    /**
     * Hide this modal.
     *
     * @method hide
     */
    Modal.prototype.hide = function() {
        this.getBackdrop().done(function(backdrop) {
            if (!this.countOtherVisibleModals()) {
                // Hide the backdrop if we're the last open modal.
                backdrop.hide();
                $('body').removeClass('modal-open');
            }

            var currentIndex = parseInt(this.root.css('z-index'));
            this.root.css('z-index', '');
            backdrop.setZIndex(currentIndex - 3);

            this.accessibilityHide();

            if (this.hasTransitions()) {
                // Wait for CSS transitions to complete before hiding the element.
                this.getRoot().one('transitionend webkitTransitionEnd oTransitionEnd', function() {
                    this.getRoot().removeClass('show').addClass('hide');
                }.bind(this));
            } else {
                this.getRoot().removeClass('show').addClass('hide');
            }

            this.root.trigger(ModalEvents.hidden, this);
        }.bind(this));
    };

    /**
     * Remove this modal from the DOM.
     *
     * @method destroy
     */
    Modal.prototype.destroy = function() {
        this.root.remove();
        this.root.trigger(ModalEvents.destroyed, this);
    };

    /**
     * Sets the appropriate aria attributes on this dialogue and the other
     * elements in the DOM to ensure that screen readers are able to navigate
     * the dialogue popup correctly.
     *
     * @method accessibilityShow
     */
    Modal.prototype.accessibilityShow = function() {
        // We need to get a list containing each sibling element and the shallowest
        // non-ancestral nodes in the DOM. We can shortcut this a little by leveraging
        // the fact that this dialogue is always appended to the document body therefore
        // it's siblings are the shallowest non-ancestral nodes. If that changes then
        // this code should also be updated.
        $('body').children().each(function(index, child) {
            // Skip the current modal.
            if (!this.root.is(child)) {
                child = $(child);
                var hidden = child.attr('aria-hidden');
                // If they are already hidden we can ignore them.
                if (hidden !== 'true') {
                    // Save their current state.
                    child.data('previous-aria-hidden', hidden);
                    this.hiddenSiblings.push(child);

                    // Hide this node from screen readers.
                    child.attr('aria-hidden', 'true');
                }
            }
        }.bind(this));

        // Make us visible to screen readers.
        this.root.attr('aria-hidden', 'false');
    };

    /**
     * Restores the aria visibility on the DOM elements changed when displaying
     * the dialogue popup and makes the dialogue aria hidden to allow screen
     * readers to navigate the main page correctly when the dialogue is closed.
     *
     * @method accessibilityHide
     */
    Modal.prototype.accessibilityHide = function() {
        this.root.attr('aria-hidden', 'true');

        // Restore the sibling nodes back to their original values.
        $.each(this.hiddenSiblings, function(index, sibling) {
            sibling = $(sibling);
            var previousValue = sibling.data('previous-aria-hidden');
            // If the element didn't previously have an aria-hidden attribute
            // then we can just remove the one we set.
            if (typeof previousValue == 'undefined') {
                sibling.removeAttr('aria-hidden');
            } else {
                // Otherwise set it back to the old value (which will be false).
                sibling.attr('aria-hidden', previousValue);
            }
        });

        // Clear the cache. No longer need to store these.
        this.hiddenSiblings = [];
    };

    /**
     * Handle the tab event to lock focus within this modal.
     *
     * @method handleTabLock
     * @param {event} e The tab key jQuery event
     */
    Modal.prototype.handleTabLock = function(e) {
        if (!this.hasFocus()) {
            return;
        }

        var target = $(document.activeElement);
        var focusableElements = this.modal.find(SELECTORS.CAN_RECEIVE_FOCUS);
        var firstFocusable = focusableElements.first();
        var lastFocusable = focusableElements.last();

        if (target.is(firstFocusable) && e.shiftKey) {
            lastFocusable.focus();
            e.preventDefault();
        } else if (target.is(lastFocusable) && !e.shiftKey) {
            firstFocusable.focus();
            e.preventDefault();
        }
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    Modal.prototype.registerEventListeners = function() {
        this.getRoot().on('keydown', function(e) {
            if (!this.isVisible()) {
                return;
            }

            if (e.keyCode == KeyCodes.tab) {
                this.handleTabLock(e);
            } else if (e.keyCode == KeyCodes.escape) {
                this.hide();
            }
        }.bind(this));

        CustomEvents.define(this.getModal(), [CustomEvents.events.activate]);
        this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE, function(e, data) {
            this.hide();
            data.originalEvent.preventDefault();
        }.bind(this));
    };

    /**
     * Set or resolve and set the value using the function.
     *
     * @method asyncSet
     * @param {(string|object)} value The string or jQuery promise.
     * @param {function} setFunction The setter
     * @return {Promise}
     */
    Modal.prototype.asyncSet = function(value, setFunction) {
        var p = value;
        if (typeof value !== 'object' || !value.hasOwnProperty('then')) {
            p = $.Deferred();
            p.resolve(value);
        }

        p.then(function(content) {
            setFunction(content);

            return;
        })
        .fail(Notification.exception);

        return p;
    };

    return Modal;
});
