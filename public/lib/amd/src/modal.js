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
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as KeyCodes from 'core/key_codes';
import ModalBackdrop from 'core/modal_backdrop';
import ModalEvents from 'core/modal_events';
import Pending from 'core/pending';
import * as CustomEvents from 'core/custom_interaction_events';
import * as FilterEvents from 'core_filters/events';
import * as FocusLock from 'core/local/aria/focuslock';
import * as Aria from 'core/aria';
import * as Fullscreen from 'core/fullscreen';
import {removeToastRegion} from './toast';
import {dispatchEvent} from 'core/event_dispatcher';
import * as Prefetch from 'core/prefetch';

/**
 * A configuration to provide to the modal.
 *
 * @typedef {Object} ModalConfig
 *
 * @property {string} [type] The type of modal to create.
 * @property {string|Promise<string>} [title] The title of the modal.
 * @property {string|Promise<string>} [body] The body of the modal.
 * @property {string|Promise<string>} [footer] The footer of the modal.
 * @property {boolean} [show=false] Whether to show the modal immediately.
 * @property {boolean} [scrollable=true] Whether the modal should be scrollable.
 * @property {boolean} [removeOnClose=true] Whether the modal should be removed from the DOM when it is closed.
 * @property {Element|jQuery} [returnElement] The element to focus when closing the modal.
 * @property {boolean} [large=false] Whether the modal should be a large modal.
 * @property {boolean} [isVerticallyCentered=false] Whether the modal should be vertically centered.
 * @property {object} [buttons={}] The buttons to display in the footer as a key => title pair.
 */

const SELECTORS = {
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

const TEMPLATES = {
    LOADING: 'core/loading',
    BACKDROP: 'core/modal_backdrop',
};

export default class Modal {
    /** @var {string} The type of modal */
    static TYPE = 'default';

    /** @var {string} The template to use for this modal */
    static TEMPLATE = 'core/modal';

    /** @var {Promise} Module singleton for the backdrop to be reused by all Modal instances */
    static backdropPromise = null;

    /**
     * @var {Number} A counter that gets incremented for each modal created.
     * This can be used to generate unique values for the modals.
     */
    static modalCounter = 0;

    /**
     * @var {Number} A singleton registry for all modules to access. Allows types to be
     * added at runtime.
     */
    static registry = new Map();

    /**
     * Getter method for .root element.
     * @return {object} jQuery object
     */
    get root() {
        return $(this._root.filter(SELECTORS.CONTAINER));
    }

    /**
     * Setter method for .root element.
     * @param {object} root jQuery object
     */
    set root(root) {
        this._root = root;
    }

    /**
     * Constructor for the Modal.
     *
     * @param {HTMLElement} root The HTMLElement at the root of the Modal content
     */
    constructor(root) {
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
        this.modalCount = Modal.modalCounter++;
        this.attachmentPoint = document.createElement('div');
        document.body.append(this.attachmentPoint);
        this.focusOnClose = null;
        this.templateJS = null;

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
    }

    /**
     * Register a modal with the legacy modal registry.
     *
     * This is provided to allow backwards-compatibility with existing code that uses the legacy modal registry.
     * It is not necessary to register modals for code only present in Moodle 4.3 and later.
     */
    static registerModalType() {
        if (!this.TYPE) {
            throw new Error(`Unknown modal type`, this);
        }

        if (!this.TEMPLATE) {
            throw new Error(`Unknown modal template`, this);
        }

        this.register(
            this.TYPE,
            this,
            this.TEMPLATE,
        );
    }

    /**
     * Register a modal.
     *
     * @param {string} type The type of modal (must be unique)
     * @param {function} module The modal module (must be a constructor function of type core/modal)
     * @param {string} template The template name of the modal
     */
    static register = (type, module, template) => {
        const existing = this.registry.get(type);
        if (existing && existing.module !== module) {
            Notification.exception({
                message: `Modal of  type '${type}' is already registered`,
            });
        }

        if (!module || typeof module !== 'function') {
            Notification.exception({message: "You must provide a modal module"});
        }

        if (!template) {
            Notification.exception({message: "You must provide a modal template"});
        }

        this.registry.set(type, {module, template});

        // Prefetch the template.
        Prefetch.prefetchTemplate(template);
    };

    /**
     * Create a new modal using the ModalFactory.
     * This is a shortcut to creating the modal.
     * Create a new modal using the supplied configuration.
     *
     * @param {ModalConfig} modalConfig
     * @returns {Promise<Modal>}
     */
    static async create(modalConfig = {}) {
        const pendingModalPromise = new Pending('core/modal:create');
        modalConfig.type = this.TYPE;

        const templateName = this._getTemplateName(modalConfig);
        const templateContext = modalConfig.templateContext || {};
        const {html, js} = await Templates.renderForPromise(templateName, templateContext);

        const modal = new this(html);
        if (js) {
            modal.setTemplateJS(js);
        }
        modal.configure(modalConfig);

        pendingModalPromise.resolve();

        return modal;
    }

    /**
     * A helper to get the template name for this modal.
     *
     * @param {ModalConfig} modalConfig
     * @returns {string}
     * @protected
     */
    static _getTemplateName(modalConfig) {
        if (modalConfig.template) {
            return modalConfig.template;
        }

        if (this.TEMPLATE) {
            return this.TEMPLATE;
        }

        if (this.registry.has(this.TYPE)) {
            const config = this.registry.get(this.TYPE);
            return config.template;
        }

        throw new Error(`Unable to determine template name for modal ${this.TYPE}`);
    }

    /**
     * Configure the modal.
     *
     * @param {ModalConfig} param0 The configuration options
     */
    configure({
        show = false,
        large = false,
        isVerticallyCentered = false,
        removeOnClose = false,
        scrollable = true,
        returnElement,
        title,
        body,
        footer,
        buttons = {},
    } = {}) {
        if (large) {
            this.setLarge();
        }

        if (isVerticallyCentered) {
            this.setVerticallyCentered();
        }

        // If configured remove the modal when hiding it.
        // Ideally this should be true, but we need to identify places that this breaks first.
        this.setRemoveOnClose(removeOnClose);
        this.setReturnElement(returnElement);
        this.setScrollable(scrollable);

        if (title !== undefined) {
            this.setTitle(title);
        }

        if (body !== undefined) {
            this.setBody(body);
        }

        if (footer !== undefined) {
            this.setFooter(footer);
        }

        Object.entries(buttons).forEach(([key, value]) => this.setButtonText(key, value));

        // If configured show the modal.
        if (show) {
            this.show();
        }
    }

    /**
     * Attach the modal to the correct part of the page.
     *
     * If it hasn't already been added it runs any
     * javascript that has been cached until now.
     *
     * @method attachToDOM
     */
    attachToDOM() {
        this.getAttachmentPoint().append(this._root);

        if (this.isAttached) {
            return;
        }

        FocusLock.trapFocus(this.root[0]);

        // If we'd cached any JS then we can run it how that the modal is
        // attached to the DOM.
        if (this.templateJS) {
            Templates.runTemplateJS(this.templateJS);
            this.templateJS = null;
        }

        if (this.bodyJS) {
            Templates.runTemplateJS(this.bodyJS);
            this.bodyJS = null;
        }

        if (this.footerJS) {
            Templates.runTemplateJS(this.footerJS);
            this.footerJS = null;
        }

        this.isAttached = true;
    }

    /**
     * Count the number of other visible modals (not including this one).
     *
     * @method countOtherVisibleModals
     * @return {int}
     */
    countOtherVisibleModals() {
        let count = 0;
        $('body').find(SELECTORS.CONTAINER).each((index, element) => {
            element = $(element);

            // If we haven't found ourself and the element is visible.
            if (!this.root.is(element) && element.hasClass('show')) {
                count++;
            }
        });

        return count;
    }

    /**
     * Get the modal backdrop.
     *
     * @method getBackdrop
     * @return {object} jQuery promise
     */
    getBackdrop() {
        if (!Modal.backdropPromise) {
            Modal.backdropPromise = Templates.render(TEMPLATES.BACKDROP, {})
                .then((html) => new ModalBackdrop($(html)))
                .catch(Notification.exception);
        }

        return Modal.backdropPromise;
    }

    /**
     * Get the root element of this modal.
     *
     * @method getRoot
     * @return {object} jQuery object
     */
    getRoot() {
        return this.root;
    }

    /**
     * Get the modal element of this modal.
     *
     * @method getModal
     * @return {object} jQuery object
     */
    getModal() {
        return this.modal;
    }

    /**
     * Get the modal title element.
     *
     * @method getTitle
     * @return {object} jQuery object
     */
    getTitle() {
        return this.title;
    }

    /**
     * Get the modal body element.
     *
     * @method getBody
     * @return {object} jQuery object
     */
    getBody() {
        return this.body;
    }

    /**
     * Get the modal footer element.
     *
     * @method getFooter
     * @return {object} jQuery object
     */
    getFooter() {
        return this.footer;
    }

    /**
     * Get a promise resolving to the title region.
     *
     * @method getTitlePromise
     * @return {Promise}
     */
    getTitlePromise() {
        return this.titlePromise;
    }

    /**
     * Get a promise resolving to the body region.
     *
     * @method getBodyPromise
     * @return {object} jQuery object
     */
    getBodyPromise() {
        return this.bodyPromise;
    }

    /**
     * Get a promise resolving to the footer region.
     *
     * @method getFooterPromise
     * @return {object} jQuery object
     */
    getFooterPromise() {
        return this.footerPromise;
    }

    /**
     * Get the unique modal count.
     *
     * @method getModalCount
     * @return {int}
     */
    getModalCount() {
        return this.modalCount;
    }

    /**
     * Set the modal title element.
     *
     * This method is overloaded to take either a string value for the title or a jQuery promise that is resolved with
     * HTML most commonly from a Str.get_string call.
     *
     * @method setTitle
     * @param {(string|object)} value The title string or jQuery promise which resolves to the title.
     */
    setTitle(value) {
        const title = this.getTitle();
        this.titlePromise = $.Deferred();

        this.asyncSet(value, title.html.bind(title))
        .then(() => {
            this.titlePromise.resolve(title);
            return;
        })
        .catch(Notification.exception);
    }

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
    setBody(value) {
        this.bodyPromise = $.Deferred();

        const body = this.getBody();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            body.html(value);
            FilterEvents.notifyFilterContentUpdated(body);
            this.getRoot().trigger(ModalEvents.bodyRendered, this);
            this.bodyPromise.resolve(body);
        } else {
            const modalPromise = new Pending(`amd-modal-js-pending-id-${this.getModalCount()}`);
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            let contentPromise = null;
            body.css('overflow', 'hidden');

            // Ensure that the `value` is a jQuery Promise.
            value = $.when(value);

            if (value.state() == 'pending') {
                // We're still waiting for the body promise to resolve so
                // let's show a loading icon.
                let height = body.innerHeight();
                if (height < 100) {
                    height = 100;
                }

                body.animate({height: `${height}px`}, 150);

                body.html('');
                contentPromise = Templates.render(TEMPLATES.LOADING, {})
                    .then((html) => {
                        const loadingIcon = $(html).hide();
                        body.html(loadingIcon);
                        loadingIcon.fadeIn(150);

                        // We only want the loading icon to fade out
                        // when the content for the body has finished
                        // loading.
                        return $.when(loadingIcon.promise(), value);
                    })
                    .then((loadingIcon) => {
                        // Once the content has finished loading and
                        // the loading icon has been shown then we can
                        // fade the icon away to reveal the content.
                        return loadingIcon.fadeOut(100).promise();
                    })
                    .then(() => {
                        return value;
                    });
            } else {
                // The content is already loaded so let's just display
                // it to the user. No need for a loading icon.
                contentPromise = value;
            }

            // Now we can actually display the content.
            contentPromise.then((html, js) => {
                let result = null;

                if (this.isVisible()) {
                    // If the modal is visible then we should display
                    // the content gracefully for the user.
                    body.css('opacity', 0);
                    const currentHeight = body.innerHeight();
                    body.html(html);
                    // We need to clear any height values we've set here
                    // in order to measure the height of the content being
                    // added. This then allows us to animate the height
                    // transition.
                    body.css('height', '');
                    const newHeight = body.innerHeight();
                    body.css('height', `${currentHeight}px`);
                    result = body.animate(
                        {height: `${newHeight}px`, opacity: 1},
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
            })
            .then((result) => {
                FilterEvents.notifyFilterContentUpdated(body);
                this.getRoot().trigger(ModalEvents.bodyRendered, this);
                dispatchEvent('core/modal:bodyRendered', this, this.modal[0]);
                return result;
            })
            .then(() => {
                this.bodyPromise.resolve(body);
                return;
            })
            .catch(Notification.exception)
            .always(() => {
                // When we're done displaying all of the content we need
                // to clear the custom values we've set here.
                body.css('height', '');
                body.css('overflow', '');
                body.css('opacity', '');
                modalPromise.resolve();

                return;
            });
        }
    }

    /**
     * Alternative to setBody() that can be used from non-Jquery modules
     *
     * @param {Promise} promise promise that returns {html, js} object
     * @return {Promise}
     */
    setBodyContent(promise) {
        // Call the leegacy API for now and pass it a jQuery Promise.
        // This is a non-spec feature of jQuery and cannot be produced with spec promises.
        // We can encourage people to migrate to this approach, and in future we can swap
        // it so that setBody() calls setBodyPromise().
        return promise.then(({html, js}) => this.setBody($.when(html, js)))
            .catch(exception => {
                this.hide();
                throw exception;
            });
    }

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
    setFooter(value) {
        // Make sure the footer is visible.
        this.showFooter();
        this.footerPromise = $.Deferred();

        const footer = this.getFooter();

        if (typeof value === 'string') {
            // Just set the value if it's a string.
            footer.html(value);
            this.footerPromise.resolve(footer);
        } else {
            // Otherwise we assume it's a promise to be resolved with
            // html and javascript.
            Templates.render(TEMPLATES.LOADING, {})
            .then((html) => {
                footer.html(html);

                return value;
            })
            .then((html, js) => {
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
            })
            .then((footer) => {
                this.footerPromise.resolve(footer);
                this.showFooter();
                return;
            })
            .catch(Notification.exception);
        }
    }

    /**
     * Check if the footer has any content in it.
     *
     * @method hasFooterContent
     * @return {bool}
     */
    hasFooterContent() {
        return this.getFooter().children().length ? true : false;
    }

    /**
     * Hide the footer element.
     *
     * @method hideFooter
     */
    hideFooter() {
        this.getFooter().addClass('hidden');
    }

    /**
     * Show the footer element.
     *
     * @method showFooter
     */
    showFooter() {
        this.getFooter().removeClass('hidden');
    }

    /**
     * Mark the modal as a large modal.
     *
     * @method setLarge
     */
    setLarge() {
        if (this.isLarge()) {
            return;
        }

        this.getModal().addClass('modal-lg');
    }

    /**
     * Mark the modal as a centered modal.
     *
     * @method setVerticallyCentered
     */
    setVerticallyCentered() {
        if (this.isVerticallyCentered()) {
            return;
        }
        this.getModal().addClass('modal-dialog-centered');
    }

    /**
     * Check if the modal is a large modal.
     *
     * @method isLarge
     * @return {bool}
     */
    isLarge() {
        return this.getModal().hasClass('modal-lg');
    }

    /**
     * Check if the modal is vertically centered.
     *
     * @method isVerticallyCentered
     * @return {bool}
     */
    isVerticallyCentered() {
        return this.getModal().hasClass('modal-dialog-centered');
    }

    /**
     * Mark the modal as a small modal.
     *
     * @method setSmall
     */
    setSmall() {
        if (this.isSmall()) {
            return;
        }

        this.getModal().removeClass('modal-lg');
    }

    /**
     * Check if the modal is a small modal.
     *
     * @method isSmall
     * @return {bool}
     */
    isSmall() {
        return !this.getModal().hasClass('modal-lg');
    }

    /**
     * Set this modal to be scrollable or not.
     *
     * @method setScrollable
     * @param {bool} value Whether the modal is scrollable or not
     */
    setScrollable(value) {
        if (!value) {
            this.getModal()[0].classList.remove('modal-dialog-scrollable');
            return;
        }

        this.getModal()[0].classList.add('modal-dialog-scrollable');
    }


    /**
     * Determine the highest z-index value currently on the page.
     *
     * @method calculateZIndex
     * @return {int}
     */
    calculateZIndex() {
        const items = $(`${SELECTORS.DIALOG}, ${SELECTORS.MENU_BAR}, ${SELECTORS.HAS_Z_INDEX}`);
        let zIndex = parseInt(this.root.css('z-index'));

        items.each((index, item) => {
            item = $(item);
            if (!item.is(':visible')) {
                // Do not include items which are not visible in the z-index calculation.
                // This is important because some dialogues are not removed from the DOM.
                return;
            }
            // Note that webkit browsers won't return the z-index value from the CSS stylesheet
            // if the element doesn't have a position specified. Instead it'll return "auto".
            const itemZIndex = item.css('z-index') ? parseInt(item.css('z-index')) : 0;

            if (itemZIndex > zIndex) {
                zIndex = itemZIndex;
            }
        });

        return zIndex;
    }

    /**
     * Check if this modal is visible.
     *
     * @method isVisible
     * @return {bool}
     */
    isVisible() {
        return this.root.hasClass('show');
    }

    /**
     * Check if this modal has focus.
     *
     * @method hasFocus
     * @return {bool}
     */
    hasFocus() {
        const target = $(document.activeElement);
        return this.root.is(target) || this.root.has(target).length;
    }

    /**
     * Check if this modal has CSS transitions applied.
     *
     * @method hasTransitions
     * @return {bool}
     */
    hasTransitions() {
        return this.getRoot().hasClass('fade');
    }

    /**
     * Gets the jQuery wrapped node that the Modal should be attached to.
     *
     * @returns {jQuery}
     */
    getAttachmentPoint() {
        return $(Fullscreen.getElement() || this.attachmentPoint);
    }

    /**
     * Display this modal. The modal will be attached to the DOM if it hasn't
     * already been.
     *
     * @method show
     * @returns {Promise}
     */
    show() {
        if (this.isVisible()) {
            return $.Deferred().resolve();
        }

        const pendingPromise = new Pending('core/modal:show');

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
        .then((backdrop) => {
            const currentIndex = this.calculateZIndex();
            const newIndex = currentIndex + 2;
            const newBackdropIndex = newIndex - 1;
            this.root.css('z-index', newIndex);
            backdrop.setZIndex(newBackdropIndex);
            backdrop.show();

            this.root.removeClass('hide').addClass('show');
            this.accessibilityShow();
            this.getModal().focus();
            $('body').addClass('modal-open');
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            $('body').css({overflow: "hidden", paddingRight: `${scrollbarWidth}px`});
            this.root.trigger(ModalEvents.shown, this);
            dispatchEvent('core/modal:shown', this, this.modal[0]);

            return;
        })
        .then(pendingPromise.resolve);
    }

    /**
     * Hide this modal if it does not contain a form.
     *
     * @method hideIfNotForm
     */
    hideIfNotForm() {
        const formElement = this.modal.find(SELECTORS.FORM);
        if (formElement.length == 0) {
            this.hide();
        }
    }

    /**
     * Hide this modal.
     *
     * @method hide
     */
    hide() {
        this.getBackdrop().done((backdrop) => {
            FocusLock.untrapFocus();

            if (!this.countOtherVisibleModals()) {
                // Hide the backdrop if we're the last open modal.
                backdrop.hide();
                $('body').removeClass('modal-open');
                $('body').css({overflow: "", paddingRight: ""});
            }

            const currentIndex = parseInt(this.root.css('z-index'));
            this.root.css('z-index', '');
            backdrop.setZIndex(currentIndex - 3);

            this.accessibilityHide();

            if (this.hasTransitions()) {
                // Wait for CSS transitions to complete before hiding the element.
                this.getRoot().one('transitionend webkitTransitionEnd oTransitionEnd', () => {
                    this.getRoot().removeClass('show').addClass('hide');
                });
            } else {
                this.getRoot().removeClass('show').addClass('hide');
            }

            // Ensure the modal is moved onto the body node if it is still attached to the DOM.
            if ($(document.body).find(this.getRoot()).length) {
                $(document.body).append(this.getRoot());
            }

            // Closes popover elements that are inside the modal at the time the modal is closed.
            this.getRoot().find('[data-bs-toggle="popover"]').each(function() {
                document.getElementById(this.getAttribute('aria-describedby'))?.remove();
            });

            this.root.trigger(ModalEvents.hidden, this);
        });
    }

    /**
     * Remove this modal from the DOM.
     *
     * @method destroy
     */
    destroy() {
        this.hide();
        removeToastRegion(this.getBody().get(0));
        this.root.remove();
        this.root.trigger(ModalEvents.destroyed, this);
        this.attachmentPoint.remove();
    }

    /**
     * Sets the appropriate aria attributes on this dialogue and the other
     * elements in the DOM to ensure that screen readers are able to navigate
     * the dialogue popup correctly.
     *
     * @method accessibilityShow
     */
    accessibilityShow() {
        // Make us visible to screen readers.
        Aria.unhide(this.root.get());

        // Hide siblings.
        Aria.hideSiblings(this.root.get()[0]);
    }

    /**
     * Restores the aria visibility on the DOM elements changed when displaying
     * the dialogue popup and makes the dialogue aria hidden to allow screen
     * readers to navigate the main page correctly when the dialogue is closed.
     *
     * @method accessibilityHide
     */
    accessibilityHide() {
        // Unhide siblings.
        Aria.unhideSiblings(this.root.get()[0]);

        // Hide this modal.
        Aria.hide(this.root.get());
    }

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    registerEventListeners() {
        this.getRoot().on('keydown', (e) => {
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
        });

        // Listen for clicks on the modal container.
        this.getRoot().click((e) => {
            // If the click wasn't inside the modal element then we should
            // hide the modal.
            if (!$(e.target).closest(SELECTORS.MODAL).length) {
                // The check above fails to detect the click was inside the modal when the DOM tree is already changed.
                // So, we check if we can still find the container element or not. If not, then the DOM tree is changed.
                // It's best not to hide the modal in that case.
                if ($(e.target).closest(SELECTORS.CONTAINER).length) {
                    const outsideClickEvent = $.Event(ModalEvents.outsideClick);
                    this.getRoot().trigger(outsideClickEvent, this);

                    if (!outsideClickEvent.isDefaultPrevented()) {
                        this.hideIfNotForm();
                    }
                }
            }
        });

        CustomEvents.define(this.getModal(), [CustomEvents.events.activate]);
        this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE, (e, data) => {
            if (this.removeOnClose) {
                this.destroy();
            } else {
                this.hide();
            }
            data.originalEvent.preventDefault();
        });

        this.getRoot().on(ModalEvents.hidden, () => {
            if (this.focusOnClose) {
                // Focus on the element that actually triggers the modal.
                this.focusOnClose.focus();
            }
        });
    }

    /**
     * Register a listener to close the dialogue when the cancel button is pressed.
     *
     * @method registerCloseOnCancel
     */
    registerCloseOnCancel() {
        // Handle the clicking of the Cancel button.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('cancel'), (e, data) => {
            const cancelEvent = $.Event(ModalEvents.cancel);
            this.getRoot().trigger(cancelEvent, this);

            if (!cancelEvent.isDefaultPrevented()) {
                data.originalEvent.preventDefault();

                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
        });
    }

    /**
     * Register a listener to close the dialogue when the save button is pressed.
     *
     * @method registerCloseOnSave
     */
    registerCloseOnSave() {
        // Handle the clicking of the Cancel button.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('save'), (e, data) => {
            const saveEvent = $.Event(ModalEvents.save);
            this.getRoot().trigger(saveEvent, this);

            if (!saveEvent.isDefaultPrevented()) {
                data.originalEvent.preventDefault();

                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
        });
    }


    /**
     * Register a listener to close the dialogue when the delete button is pressed.
     *
     * @method registerCloseOnDelete
     */
    registerCloseOnDelete() {
        // Handle the clicking of the Cancel button.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('delete'), (e, data) => {
            const deleteEvent = $.Event(ModalEvents.delete);
            this.getRoot().trigger(deleteEvent, this);

            if (!deleteEvent.isDefaultPrevented()) {
                data.originalEvent.preventDefault();

                if (this.removeOnClose) {
                    this.destroy();
                } else {
                    this.hide();
                }
            }
        });
    }

    /**
     * Set or resolve and set the value using the function.
     *
     * @method asyncSet
     * @param {(string|object)} value The string or jQuery promise.
     * @param {function} setFunction The setter
     * @return {Promise}
     */
    asyncSet(value, setFunction) {
        const getWrappedValue = (value) => {
            if (value instanceof Promise) {
                return $.when(value);
            }

            if (typeof value !== 'object' || !value.hasOwnProperty('then')) {
                return $.Deferred().resolve(value);
            }

            return value;
        };

        return getWrappedValue(value)
            .then((content) => setFunction(content))
            .catch(Notification.exception);
    }

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
    setButtonText(action, value) {
        const button = this.getFooter().find(this.getActionSelector(action));

        if (!button) {
            throw new Error("Unable to find the '" + action + "' button");
        }

        return this.asyncSet(value, button.text.bind(button));
    }

    /**
     * Get the Selector for an action.
     *
     * @param {String} action
     * @returns {DOMString}
     */
    getActionSelector(action) {
        return "[data-action='" + action + "']";
    }

    /**
     * Set the flag to remove the modal from the DOM on close.
     *
     * @param {Boolean} remove
     */
    setRemoveOnClose(remove) {
        this.removeOnClose = remove;
    }

    /**
     * Set the return element for the modal.
     *
     * @param {Element|jQuery} element Element to focus when the modal is closed
     */
    setReturnElement(element) {
        this.focusOnClose = element;
    }

    /**
     * Set the a button enabled or disabled.
     *
     * @param {DOMString} action The action of the button
     * @param {Boolean} disabled the new disabled value
     */
    setButtonDisabled(action, disabled) {
        const button = this.getFooter().find(this.getActionSelector(action));

        if (!button) {
            throw new Error("Unable to find the '" + action + "' button");
        }
        if (disabled) {
            button.attr('disabled', '');
        } else {
            button.removeAttr('disabled');
        }
    }

    /**
     * Set the template JS for this modal.
     * @param {String} js The JavaScript to run when the modal is attached to the DOM.
     */
    setTemplateJS(js) {
        this.templateJS = js;
    }
}
