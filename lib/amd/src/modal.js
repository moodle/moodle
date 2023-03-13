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
 * @module core/modal
 * @class core/modal
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/templates',
    'core/notification',
    'core/key_codes',
    'core/custom_interaction_events',
    'core/modal_backdrop',
    'core_filters/events',
    'core/modal_events',
    'core/local/aria/focuslock',
    'core/pending',
    'core/aria',
    'core/fullscreen'
], function(
    $,
    Templates,
    Notification,
    KeyCodes,
    CustomEvents,
    ModalBackdrop,
    FilterEvents,
    ModalEvents,
    FocusLock,
    Pending,
    Aria,
    Fullscreen
) {

    var SELECTORS = {
        CONTAINER: '[data-region="modal-container"]',
        MODAL: '[data-region="modal"]',
        HEADER: '[data-region="header"]',
        TITLE: '[data-region="title"]',
        BODY: '[data-region="body"]',
        FOOTER: '[data-region="footer"]',
        HIDE: '[data-action="hide"]',
        DIALOG: '[role=dialog]',
        FORM: 'form',
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
        this.headerPromise = $.Deferred();
        this.title = this.header.find(SELECTORS.TITLE);
        this.titlePromise = $.Deferred();
        this.body = this.modal.find(SELECTORS.BODY);
        this.bodyPromise = $.Deferred();
        this.footer = this.modal.find(SELECTORS.FOOTER);
        this.footerPromise = $.Deferred();
        this.hiddenSiblings = [];
        this.isAttached = false;
        this.bodyJS = null;
        this.footerJS = null;
        this.modalCount = modalCounter++;
        this.attachmentPoint = document.createElement('div');
        document.body.append(this.attachmentPoint);
        this.focusOnClose = null;

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
     * Attach the modal to the correct part of the page.
     *
     * If it hasn't already been added it runs any
     * javascript that has been cached until now.
     *
     * @method attachToDOM
     */
    Modal.prototype.attachToDOM = function() {
        this.getAttachmentPoint().append(this.root);

        if (this.isAttached) {
            return;
        }

        FocusLock.trapFocus(this.root[0]);

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
     * Get a promise resolving to the title region.
     *
     * @method getTitlePromise
     * @return {Promise}
     */
    Modal.prototype.getTitlePromise = function() {
        return this.titlePromise;
    };

    /**
     * Get a promise resolving to the body region.
     *
     * @method getBodyPromise
     * @return {object} jQuery object
     */
    Modal.prototype.getBodyPromise = function() {
        return this.bodyPromise;
    };

    /**
     * Get a promise resolving to the footer region.
     *
     * @method getFooterPromise
     * @return {object} jQuery object
     */
    Modal.prototype.getFooterPromise = function() {
        return this.footerPromise;
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
        this.titlePromise = $.Deferred();

        this.asyncSet(value, title.html.bind(title))
        .then(function() {
            this.titlePromise.resolve(title);
        }.bind(this))
        .catch(Notification.exception);
    };

    /**
     * Set the modal body element.
     *
     * This method is overloaded to take either a string value for the body or a jQuery promise that is resolved with
     * HTML and Javascript most commonly from a Templates.render call.
     *
     * @method setBody
     * @param {(string|object)} value The body string or jQuery promise which resolves to the body.
     * @fires event:filterContentUpdated
     */
    Modal.prototype.setBody = function(value) {
        this.bodyPromise = $.Deferred();

        var body = this.getBody();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            body.html(value);
            FilterEvents.notifyFilterContentUpdated(body);
            this.getRoot().trigger(ModalEvents.bodyRendered, this);
            this.bodyPromise.resolve(body);
        } else {
            var jsPendingId = 'amd-modal-js-pending-id-' + this.getModalCount();
            M.util.js_pending(jsPendingId);
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            var contentPromise = null;
            body.css('overflow', 'hidden');

            // Ensure that the `value` is a jQuery Promise.
            value = $.when(value);

            if (value.state() == 'pending') {
                // We're still waiting for the body promise to resolve so
                // let's show a loading icon.
                var height = body.innerHeight();
                if (height < 100) {
                    height = 100;
                }

                body.animate({height: height + 'px'}, 150);

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

                return result;
            }.bind(this))
            .then(function(result) {
                FilterEvents.notifyFilterContentUpdated(body);
                this.getRoot().trigger(ModalEvents.bodyRendered, this);
                return result;
            }.bind(this))
            .then(function() {
                this.bodyPromise.resolve(body);
                return;
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
     * Alternative to setBody() that can be used from non-Jquery modules
     *
     * @param {Promise} promise promise that returns {html, js} object
     * @return {Promise}
     */
    Modal.prototype.setBodyContent = function(promise) {
        // Call the leegacy API for now and pass it a jQuery Promise.
        // This is a non-spec feature of jQuery and cannot be produced with spec promises.
        // We can encourage people to migrate to this approach, and in future we can swap
        // it so that setBody() calls setBodyPromise().
        return promise.then(({html, js}) => this.setBody($.when(html, js)))
            .catch(exception => {
                this.hide();
                throw exception;
            });
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
        this.footerPromise = $.Deferred();

        var footer = this.getFooter();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            footer.html(value);
            this.footerPromise.resolve(footer);
        } else {
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            Templates.render(TEMPLATES.LOADING, {})
            .then(function(html) {
                footer.html(html);

                return value;
            })
            .then(function(html, js) {
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

                return footer;
            }.bind(this))
            .then(function(footer) {
                this.footerPromise.resolve(footer);
                return;
            }.bind(this))
            .catch(Notification.exception);
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
     * Set this modal to be scrollable or not.
     *
     * @method setScrollable
     * @param {bool} value Whether the modal is scrollable or not
     */
    Modal.prototype.setScrollable = function(value) {
        if (!value) {
            this.getModal()[0].classList.remove('modal-dialog-scrollable');
            return;
        }

        this.getModal()[0].classList.add('modal-dialog-scrollable');
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
     * Gets the jQuery wrapped node that the Modal should be attached to.
     *
     * @returns {jQuery}
     */
    Modal.prototype.getAttachmentPoint = function() {
        return $(Fullscreen.getElement() || this.attachmentPoint);
    };

    /**
     * Display this modal. The modal will be attached to the DOM if it hasn't
     * already been.
     *
     * @method show
     * @returns {Promise}
     */
    Modal.prototype.show = function() {
        if (this.isVisible()) {
            return $.Deferred().resolve();
        }

        var pendingPromise = new Pending('core/modal:show');

        if (this.hasFooterContent()) {
            this.showFooter();
        } else {
            this.hideFooter();
        }

        this.attachToDOM();

        // If the focusOnClose was not set. Set the focus back to triggered element.
        if (!this.focusOnClose && document.activeElement) {
            this.focusOnClose = document.activeElement;
        }

        return this.getBackdrop()
        .then(function(backdrop) {
            var currentIndex = this.calculateZIndex();
            var newIndex = currentIndex + 2;
            var newBackdropIndex = newIndex - 1;
            this.root.css('z-index', newIndex);
            backdrop.setZIndex(newBackdropIndex);
            backdrop.show();

            this.root.removeClass('hide').addClass('show');
            this.accessibilityShow();
            this.getModal().focus();
            $('body').addClass('modal-open');
            this.root.trigger(ModalEvents.shown, this);

            return;
        }.bind(this))
        .then(pendingPromise.resolve);
    };

    /**
     * Hide this modal if it does not contain a form.
     *
     * @method hideIfNotForm
     */
    Modal.prototype.hideIfNotForm = function() {
        var formElement = this.modal.find(SELECTORS.FORM);
        if (formElement.length == 0) {
            this.hide();
        }
    };

    /**
     * Hide this modal.
     *
     * @method hide
     */
    Modal.prototype.hide = function() {
        this.getBackdrop().done(function(backdrop) {
            FocusLock.untrapFocus();

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

            // Ensure the modal is moved onto the body node if it is still attached to the DOM.
            if ($(document.body).find(this.getRoot()).length) {
                $(document.body).append(this.getRoot());
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
        this.hide();
        this.root.remove();
        this.root.trigger(ModalEvents.destroyed, this);
        this.attachmentPoint.remove();
    };

    /**
     * Sets the appropriate aria attributes on this dialogue and the other
     * elements in the DOM to ensure that screen readers are able to navigate
     * the dialogue popup correctly.
     *
     * @method accessibilityShow
     */
    Modal.prototype.accessibilityShow = function() {
        // Make us visible to screen readers.
        Aria.unhide(this.root.get());

        // Hide siblings.
        Aria.hideSiblings(this.root.get()[0]);
    };

    /**
     * Restores the aria visibility on the DOM elements changed when displaying
     * the dialogue popup and makes the dialogue aria hidden to allow screen
     * readers to navigate the main page correctly when the dialogue is closed.
     *
     * @method accessibilityHide
     */
    Modal.prototype.accessibilityHide = function() {
        // Unhide siblings.
        Aria.unhideSiblings(this.root.get()[0]);

        // Hide this modal.
        Aria.hide(this.root.get());
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

            if (e.keyCode == KeyCodes.escape) {
                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
        }.bind(this));

        // Listen for clicks on the modal container.
        this.getRoot().click(function(e) {
            // If the click wasn't inside the modal element then we should
            // hide the modal.
            if (!$(e.target).closest(SELECTORS.MODAL).length) {
                // The check above fails to detect the click was inside the modal when the DOM tree is already changed.
                // So, we check if we can still find the container element or not. If not, then the DOM tree is changed.
                // It's best not to hide the modal in that case.
                if ($(e.target).closest(SELECTORS.CONTAINER).length) {
                    var outsideClickEvent = $.Event(ModalEvents.outsideClick);
                    this.getRoot().trigger(outsideClickEvent, this);

                    if (!outsideClickEvent.isDefaultPrevented()) {
                        this.hideIfNotForm();
                    }
                }
            }
        }.bind(this));

        CustomEvents.define(this.getModal(), [CustomEvents.events.activate]);
        this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE, function(e, data) {
            if (this.removeOnClose) {
                this.destroy();
            } else {
                this.hide();
            }
            data.originalEvent.preventDefault();
        }.bind(this));

        this.getRoot().on(ModalEvents.hidden, () => {
            if (this.focusOnClose) {
                // Focus on the element that actually triggers the modal.
                this.focusOnClose.focus();
            }
        });
    };

    /**
     * Register a listener to close the dialogue when the cancel button is pressed.
     *
     * @method registerCloseOnCancel
     */
    Modal.prototype.registerCloseOnCancel = function() {
        // Handle the clicking of the Cancel button.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('cancel'), function(e, data) {
            var cancelEvent = $.Event(ModalEvents.cancel);
            this.getRoot().trigger(cancelEvent, this);

            if (!cancelEvent.isDefaultPrevented()) {
                data.originalEvent.preventDefault();

                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
        }.bind(this));
    };

    /**
     * Register a listener to close the dialogue when the save button is pressed.
     *
     * @method registerCloseOnSave
     */
    Modal.prototype.registerCloseOnSave = function() {
        // Handle the clicking of the Cancel button.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('save'), function(e, data) {
            var saveEvent = $.Event(ModalEvents.save);
            this.getRoot().trigger(saveEvent, this);

            if (!saveEvent.isDefaultPrevented()) {
                data.originalEvent.preventDefault();

                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
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

    /**
     * Set the title text of a button.
     *
     * This method is overloaded to take either a string value for the button title or a jQuery promise that is resolved with
     * text most commonly from a Str.get_string call.
     *
     * @param {DOMString} action The action of the button
     * @param {(String|object)} value The button text, or a promise which will resolve to it
     * @returns {Promise}
     */
    Modal.prototype.setButtonText = function(action, value) {
        const button = this.getFooter().find(this.getActionSelector(action));

        if (!button) {
            throw new Error("Unable to find the '" + action + "' button");
        }

        return this.asyncSet(value, button.text.bind(button));
    };

    /**
     * Get the Selector for an action.
     *
     * @param {String} action
     * @returns {DOMString}
     */
    Modal.prototype.getActionSelector = function(action) {
        return "[data-action='" + action + "']";
    };

    /**
     * Set the flag to remove the modal from the DOM on close.
     *
     * @param {Boolean} remove
     */
    Modal.prototype.setRemoveOnClose = function(remove) {
        this.removeOnClose = remove;
    };

    /**
     * Set the return element for the modal.
     *
     * @param {Element|jQuery} element Element to focus when the modal is closed
     */
    Modal.prototype.setReturnElement = function(element) {
        this.focusOnClose = element;
    };

    return Modal;
});
