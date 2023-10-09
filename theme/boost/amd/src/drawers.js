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
 * Toggling the visibility of the secondary navigation on mobile.
 *
 * @module     theme_boost/drawers
 * @copyright  2021 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ModalBackdrop from 'core/modal_backdrop';
import Templates from 'core/templates';
import * as Aria from 'core/aria';
import {dispatchEvent} from 'core/event_dispatcher';
import {debounce} from 'core/utils';
import {isSmall, isLarge} from 'core/pagehelpers';
import Pending from 'core/pending';
import {setUserPreference} from 'core_user/repository';
// The jQuery module is only used for interacting with Boostrap 4. It can we removed when MDL-71979 is integrated.
import jQuery from 'jquery';

let backdropPromise = null;

const drawerMap = new Map();

const SELECTORS = {
    BUTTONS: '[data-toggler="drawers"]',
    CLOSEBTN: '[data-toggler="drawers"][data-action="closedrawer"]',
    OPENBTN: '[data-toggler="drawers"][data-action="opendrawer"]',
    TOGGLEBTN: '[data-toggler="drawers"][data-action="toggle"]',
    DRAWERS: '[data-region="fixed-drawer"]',
    DRAWERCONTENT: '.drawercontent',
    PAGECONTENT: '#page-content',
    HEADERCONTENT: '.drawerheadercontent',
};

const CLASSES = {
    SCROLLED: 'scrolled',
    SHOW: 'show',
    NOTINITIALISED: 'not-initialized',
};

/**
 * Pixel thresshold to auto-hide drawers.
 *
 * @type {Number}
 */
const THRESHOLD = 20;

/**
 * Try to get the drawer z-index from the page content.
 *
 * @returns {Number|null} the z-index of the drawer.
 * @private
 */
const getDrawerZIndex = () => {
    const drawer = document.querySelector(SELECTORS.DRAWERS);
    if (!drawer) {
        return null;
    }
    return parseInt(window.getComputedStyle(drawer).zIndex, 10);
};

/**
 * Add a backdrop to the page.
 *
 * @returns {Promise} rendering of modal backdrop.
 * @private
 */
const getBackdrop = () => {
    if (!backdropPromise) {
        backdropPromise = Templates.render('core/modal_backdrop', {})
        .then(html => new ModalBackdrop(html))
        .then(modalBackdrop => {
            const drawerZindex = getDrawerZIndex();
            if (drawerZindex) {
                modalBackdrop.setZIndex(getDrawerZIndex() - 1);
            }
            modalBackdrop.getAttachmentPoint().get(0).addEventListener('click', e => {
                e.preventDefault();
                Drawers.closeAllDrawers();
            });
            return modalBackdrop;
        })
        .catch();
    }
    return backdropPromise;
};

/**
 * Get the button element to open a specific drawer.
 *
 * @param {String} drawerId the drawer element Id
 * @return {HTMLElement|undefined} the open button element
 * @private
 */
const getDrawerOpenButton = (drawerId) => {
    let openButton = document.querySelector(`${SELECTORS.OPENBTN}[data-target="${drawerId}"]`);
    if (!openButton) {
        openButton = document.querySelector(`${SELECTORS.TOGGLEBTN}[data-target="${drawerId}"]`);
    }
    return openButton;
};

/**
 * Disable drawer tooltips.
 *
 * @param {HTMLElement} drawerNode the drawer main node
 * @private
 */
const disableDrawerTooltips = (drawerNode) => {
    const buttons = [
        drawerNode.querySelector(SELECTORS.CLOSEBTN),
        getDrawerOpenButton(drawerNode.id),
    ];
    buttons.forEach(button => {
        if (!button) {
            return;
        }
        disableButtonTooltip(button);
    });
};

/**
 * Disable the button tooltips.
 *
 * @param {HTMLElement} button the button element
 * @param {boolean} enableOnBlur if the tooltip must be re-enabled on blur.
 * @private
 */
const disableButtonTooltip = (button, enableOnBlur) => {
    if (button.hasAttribute('data-original-title')) {
        // The jQuery is still used in Boostrap 4. It can we removed when MDL-71979 is integrated.
        jQuery(button).tooltip('disable');
        button.setAttribute('title', button.dataset.originalTitle);
    } else {
        button.dataset.disabledToggle = button.dataset.toggle;
        button.removeAttribute('data-toggle');
    }
    if (enableOnBlur) {
        button.dataset.restoreTooltipOnBlur = true;
    }
};

/**
 * Enable drawer tooltips.
 *
 * @param {HTMLElement} drawerNode the drawer main node
 * @private
 */
const enableDrawerTooltips = (drawerNode) => {
    const buttons = [
        drawerNode.querySelector(SELECTORS.CLOSEBTN),
        getDrawerOpenButton(drawerNode.id),
    ];
    buttons.forEach(button => {
        if (!button) {
            return;
        }
        enableButtonTooltip(button);
    });
};

/**
 * Enable the button tooltips.
 *
 * @param {HTMLElement} button the button element
 * @private
 */
const enableButtonTooltip = (button) => {
    // The jQuery is still used in Boostrap 4. It can we removed when MDL-71979 is integrated.
    if (button.hasAttribute('data-original-title')) {
        jQuery(button).tooltip('enable');
        button.removeAttribute('title');
    } else if (button.dataset.disabledToggle) {
        button.dataset.toggle = button.dataset.disabledToggle;
        jQuery(button).tooltip();
    }
    delete button.dataset.restoreTooltipOnBlur;
};

/**
 * Add scroll listeners to a drawer element.
 *
 * @param {HTMLElement} drawerNode the drawer main node
 * @private
 */
const addInnerScrollListener = (drawerNode) => {
    const content = drawerNode.querySelector(SELECTORS.DRAWERCONTENT);
    if (!content) {
        return;
    }
    content.addEventListener("scroll", () => {
        drawerNode.classList.toggle(
            CLASSES.SCROLLED,
            content.scrollTop != 0
        );
    });
};

/**
 * The Drawers class is used to control on-screen drawer elements.
 *
 * It handles opening, and closing of drawer elements, as well as more detailed behaviours such as closing a drawer when
 * another drawer is opened, and supports closing a drawer when the screen is resized.
 *
 * Drawers are instantiated on page load, and can also be toggled lazily when toggling any drawer toggle, open button,
 * or close button.
 *
 * A range of show and hide events are also dispatched as detailed in the class
 * {@link module:theme_boost/drawers#eventTypes eventTypes} object.
 *
 * @example <caption>Standard usage</caption>
 *
 * // The module just needs to be included to add drawer support.
 * import 'theme_boost/drawers';
 *
 * @example <caption>Manually open or close any drawer</caption>
 *
 * import Drawers from 'theme_boost/drawers';
 *
 * const myDrawer = Drawers.getDrawerInstanceForNode(document.querySelector('.myDrawerNode');
 * myDrawer.closeDrawer();
 *
 * @example <caption>Listen to the before show event and cancel it</caption>
 *
 * import Drawers from 'theme_boost/drawers';
 *
 * document.addEventListener(Drawers.eventTypes.drawerShow, e => {
 *     // The drawer which will be shown.
 *     window.console.log(e.target);
 *
 *     // The instance of the Drawers class for this drawer.
 *     window.console.log(e.detail.drawerInstance);
 *
 *     // Prevent this drawer from being shown.
 *     e.preventDefault();
 * });
 *
 * @example <caption>Listen to the shown event</caption>
 *
 * document.addEventListener(Drawers.eventTypes.drawerShown, e => {
 *     // The drawer which was shown.
 *     window.console.log(e.target);
 *
 *     // The instance of the Drawers class for this drawer.
 *     window.console.log(e.detail.drawerInstance);
 * });
 */
export default class Drawers {
    /**
     * The underlying HTMLElement which is controlled.
     */
    drawerNode = null;

    /**
     * The drawer page bounding box dimensions.
     * @var {DOMRect} boundingRect
     */
    boundingRect = null;

    constructor(drawerNode) {
        // Some behat tests may use fake drawer divs to test components in drawers.
        if (drawerNode.dataset.behatFakeDrawer !== undefined) {
            return;
        }

        this.drawerNode = drawerNode;

        if (isSmall()) {
            this.closeDrawer({focusOnOpenButton: false, updatePreferences: false});
        }

        if (this.drawerNode.classList.contains(CLASSES.SHOW)) {
            this.openDrawer({focusOnCloseButton: false});
        } else if (this.drawerNode.dataset.forceopen == 1) {
            if (!isSmall()) {
                this.openDrawer({focusOnCloseButton: false});
            }
        } else {
            Aria.hide(this.drawerNode);
        }

        // Disable tooltips in small screens.
        if (isSmall()) {
            disableDrawerTooltips(this.drawerNode);
        }

        addInnerScrollListener(this.drawerNode);

        drawerMap.set(drawerNode, this);

        drawerNode.classList.remove(CLASSES.NOTINITIALISED);
    }

    /**
     * Whether the drawer is open.
     *
     * @returns {boolean}
     */
    get isOpen() {
        return this.drawerNode.classList.contains(CLASSES.SHOW);
    }

    /**
     * Whether the drawer should close when the window is resized
     *
     * @returns {boolean}
     */
    get closeOnResize() {
        return !!parseInt(this.drawerNode.dataset.closeOnResize);
    }

    /**
     * The list of event types.
     *
     * @static
     * @property {String} drawerShow See {@link event:theme_boost/drawers:show}
     * @property {String} drawerShown See {@link event:theme_boost/drawers:shown}
     * @property {String} drawerHide See {@link event:theme_boost/drawers:hide}
     * @property {String} drawerHidden See {@link event:theme_boost/drawers:hidden}
     */
    static eventTypes = {
        /**
         * An event triggered before a drawer is shown.
         *
         * @event theme_boost/drawers:show
         * @type {CustomEvent}
         * @property {HTMLElement} target The drawer that will be opened.
         */
        drawerShow: 'theme_boost/drawers:show',

        /**
         * An event triggered after a drawer is shown.
         *
         * @event theme_boost/drawers:shown
         * @type {CustomEvent}
         * @property {HTMLElement} target The drawer that was be opened.
         */
        drawerShown: 'theme_boost/drawers:shown',

        /**
         * An event triggered before a drawer is hidden.
         *
         * @event theme_boost/drawers:hide
         * @type {CustomEvent}
         * @property {HTMLElement} target The drawer that will be hidden.
         */
        drawerHide: 'theme_boost/drawers:hide',

        /**
         * An event triggered after a drawer is hidden.
         *
         * @event theme_boost/drawers:hidden
         * @type {CustomEvent}
         * @property {HTMLElement} target The drawer that was be hidden.
         */
        drawerHidden: 'theme_boost/drawers:hidden',
    };


    /**
     * Get the drawer instance for the specified node
     *
     * @param {HTMLElement} drawerNode
     * @returns {module:theme_boost/drawers}
     */
    static getDrawerInstanceForNode(drawerNode) {
        if (!drawerMap.has(drawerNode)) {
            new Drawers(drawerNode);
        }

        return drawerMap.get(drawerNode);
    }

    /**
     * Dispatch a drawer event.
     *
     * @param {string} eventname the event name
     * @param {boolean} cancelable if the event is cancelable
     * @returns {CustomEvent} the resulting custom event
     */
    dispatchEvent(eventname, cancelable = false) {
        return dispatchEvent(
            eventname,
            {
                drawerInstance: this,
            },
            this.drawerNode,
            {
                cancelable,
            }
        );
    }

    /**
     * Open the drawer.
     *
     * By default, openDrawer sets the page focus to the close drawer button. However, when a drawer is open at page
     * load, this represents an accessibility problem as the initial focus changes without any user interaction. The
     * focusOnCloseButton parameter can be set to false to prevent this behaviour.
     *
     * @param {object} args
     * @param {boolean} [args.focusOnCloseButton=true] Whether to alter page focus when opening the drawer
     */
    openDrawer({focusOnCloseButton = true} = {}) {

        const pendingPromise = new Pending('theme_boost/drawers:open');
        const showEvent = this.dispatchEvent(Drawers.eventTypes.drawerShow, true);
        if (showEvent.defaultPrevented) {
            return;
        }

        // Hide close button and header content while the drawer is showing to prevent glitchy effects.
        this.drawerNode.querySelector(SELECTORS.CLOSEBTN)?.classList.toggle('hidden', true);
        this.drawerNode.querySelector(SELECTORS.HEADERCONTENT)?.classList.toggle('hidden', true);


        // Remove open tooltip if still visible.
        let openButton = getDrawerOpenButton(this.drawerNode.id);
        if (openButton && openButton.hasAttribute('data-original-title')) {
            // The jQuery is still used in Boostrap 4. It can we removed when MDL-71979 is integrated.
            jQuery(openButton)?.tooltip('hide');
        }

        Aria.unhide(this.drawerNode);
        this.drawerNode.classList.add(CLASSES.SHOW);

        const preference = this.drawerNode.dataset.preference;
        if (preference && !isSmall() && (this.drawerNode.dataset.forceopen != 1)) {
            setUserPreference(preference, true);
        }

        const state = this.drawerNode.dataset.state;
        if (state) {
            const page = document.getElementById('page');
            page.classList.add(state);
        }

        this.boundingRect = this.drawerNode.getBoundingClientRect();

        if (isSmall()) {
            getBackdrop().then(backdrop => {
                backdrop.show();

                const pageWrapper = document.getElementById('page');
                pageWrapper.style.overflow = 'hidden';
                return backdrop;
            })
            .catch();
        }

        // Show close button and header content once the drawer is fully opened.
        const closeButton = this.drawerNode.querySelector(SELECTORS.CLOSEBTN);
        const headerContent = this.drawerNode.querySelector(SELECTORS.HEADERCONTENT);
        if (focusOnCloseButton && closeButton) {
            disableButtonTooltip(closeButton, true);
        }
        setTimeout(() => {
            closeButton.classList.toggle('hidden', false);
            headerContent.classList.toggle('hidden', false);
            if (focusOnCloseButton) {
                closeButton.focus();
            }
            pendingPromise.resolve();
        }, 300);

        this.dispatchEvent(Drawers.eventTypes.drawerShown);
    }

    /**
     * Close the drawer.
     *
     * @param {object} args
     * @param {boolean} [args.focusOnOpenButton=true] Whether to alter page focus when opening the drawer
     * @param {boolean} [args.updatePreferences=true] Whether to update the user prewference
     */
    closeDrawer({focusOnOpenButton = true, updatePreferences = true} = {}) {

        const pendingPromise = new Pending('theme_boost/drawers:close');

        const hideEvent = this.dispatchEvent(Drawers.eventTypes.drawerHide, true);
        if (hideEvent.defaultPrevented) {
            return;
        }

        // Hide close button and header content while the drawer is hiding to prevent glitchy effects.
        const closeButton = this.drawerNode.querySelector(SELECTORS.CLOSEBTN);
        closeButton?.classList.toggle('hidden', true);
        const headerContent = this.drawerNode.querySelector(SELECTORS.HEADERCONTENT);
        headerContent?.classList.toggle('hidden', true);
        // Remove the close button tooltip if visible.
        if (closeButton.hasAttribute('data-original-title')) {
            // The jQuery is still used in Boostrap 4. It can we removed when MDL-71979 is integrated.
            jQuery(closeButton)?.tooltip('hide');
        }

        const preference = this.drawerNode.dataset.preference;
        if (preference && updatePreferences && !isSmall()) {
            setUserPreference(preference, false);
        }

        const state = this.drawerNode.dataset.state;
        if (state) {
            const page = document.getElementById('page');
            page.classList.remove(state);
        }

        Aria.hide(this.drawerNode);
        this.drawerNode.classList.remove(CLASSES.SHOW);

        getBackdrop().then(backdrop => {
            backdrop.hide();

            if (isSmall()) {
                const pageWrapper = document.getElementById('page');
                pageWrapper.style.overflow = 'visible';
            }
            return backdrop;
        })
        .catch();

        // Move focus to the open drawer (or toggler) button once the drawer is hidden.
        let openButton = getDrawerOpenButton(this.drawerNode.id);
        if (openButton) {
            disableButtonTooltip(openButton, true);
        }
        setTimeout(() => {
            if (openButton && focusOnOpenButton) {
                openButton.focus();
            }
            pendingPromise.resolve();
        }, 300);

        this.dispatchEvent(Drawers.eventTypes.drawerHidden);
    }

    /**
     * Toggle visibility of the drawer.
     */
    toggleVisibility() {
        if (this.drawerNode.classList.contains(CLASSES.SHOW)) {
            this.closeDrawer();
        } else {
            this.openDrawer();
        }
    }

    /**
     * Displaces the drawer outsite the page.
     *
     * @param {Number} scrollPosition the page current scroll position
     */
    displace(scrollPosition) {
        let displace = scrollPosition;
        let openButton = getDrawerOpenButton(this.drawerNode.id);
        if (scrollPosition === 0) {
            this.drawerNode.style.transform = '';
            if (openButton) {
                openButton.style.transform = '';
            }
            return;
        }
        const state = this.drawerNode.dataset?.state;
        const drawrWidth = this.drawerNode.offsetWidth;
        let scrollThreshold = drawrWidth;
        let direction = -1;
        if (state === 'show-drawer-right') {
            direction = 1;
            scrollThreshold = THRESHOLD;
        }
        // LTR scroll is positive while RTL scroll is negative.
        if (Math.abs(scrollPosition) > scrollThreshold) {
            displace = Math.sign(scrollPosition) * (drawrWidth + THRESHOLD);
        }
        displace *= direction;
        const transform = `translateX(${displace}px)`;
        if (openButton) {
            openButton.style.transform = transform;
        }
        this.drawerNode.style.transform = transform;
    }

    /**
     * Prevent drawer from overlapping an element.
     *
     * @param {HTMLElement} currentFocus
     */
    preventOverlap(currentFocus) {
        // Start position drawer (aka. left drawer) will never overlap with the page content.
        if (!this.isOpen || this.drawerNode.dataset?.state === 'show-drawer-left') {
            return;
        }
        const drawrWidth = this.drawerNode.offsetWidth;
        const element = currentFocus.getBoundingClientRect();

        // The this.boundingRect is calculated only once and it is reliable
        // for horizontal overlapping (which is the most common). However,
        // it is not reliable for vertical overlapping because the drawer
        // height can be changed by other elements like sticky footer.
        // To prevent recalculating the boundingRect on every
        // focusin event, we use horizontal overlapping as first fast check.
        let overlapping = (
            (element.right + THRESHOLD) > this.boundingRect.left &&
            (element.left - THRESHOLD) < this.boundingRect.right
        );
        if (overlapping) {
            const currentBoundingRect = this.drawerNode.getBoundingClientRect();
            overlapping = (
                (element.bottom) > currentBoundingRect.top &&
                (element.top) < currentBoundingRect.bottom
            );
        }

        if (overlapping) {
            // Force drawer to displace out of the page.
            let displaceOut = drawrWidth + 1;
            if (window.right_to_left()) {
                displaceOut *= -1;
            }
            this.displace(displaceOut);
        } else {
            // Reset drawer displacement.
            this.displace(window.scrollX);
        }
    }

    /**
     * Close all drawers.
     */
    static closeAllDrawers() {
        drawerMap.forEach(drawerInstance => {
            drawerInstance.closeDrawer();
        });
    }

    /**
     * Close all drawers except for the specified drawer.
     *
     * @param {module:theme_boost/drawers} comparisonInstance
     */
    static closeOtherDrawers(comparisonInstance) {
        drawerMap.forEach(drawerInstance => {
            if (drawerInstance === comparisonInstance) {
                return;
            }

            drawerInstance.closeDrawer();
        });
    }

    /**
     * Prevent drawers from covering the focused element.
     */
    static preventCoveringFocusedElement() {
        const currentFocus = document.activeElement;
        // Focus on page layout elements should be ignored.
        const pagecontent = document.querySelector(SELECTORS.PAGECONTENT);
        if (!currentFocus || !pagecontent?.contains(currentFocus)) {
            Drawers.displaceDrawers(window.scrollX);
            return;
        }
        drawerMap.forEach(drawerInstance => {
            drawerInstance.preventOverlap(currentFocus);
        });
    }

    /**
     * Prevent drawer from covering the content when the page content covers the full page.
     *
     * @param {Number} displace
     */
    static displaceDrawers(displace) {
        drawerMap.forEach(drawerInstance => {
            drawerInstance.displace(displace);
        });
    }
}

/**
 * Set the last used attribute for the last used toggle button for a drawer.
 *
 * @param {object} toggleButton The clicked button.
 */
const setLastUsedToggle = (toggleButton) => {
    if (toggleButton.dataset.target) {
        document.querySelectorAll(`${SELECTORS.BUTTONS}[data-target="${toggleButton.dataset.target}"]`)
        .forEach(btn => {
            btn.dataset.lastused = false;
        });
        toggleButton.dataset.lastused = true;
    }
};

/**
 * Set the focus to the last used button to open this drawer.
 * @param {string} target The drawer target.
 */
const focusLastUsedToggle = (target) => {
    const lastUsedButton = document.querySelector(`${SELECTORS.BUTTONS}[data-target="${target}"][data-lastused="true"`);
    if (lastUsedButton) {
        lastUsedButton.focus();
    }
};

/**
 * Register the event listeners for the drawer.
 *
 * @private
 */
const registerListeners = () => {
    // Listen for show/hide events.
    document.addEventListener('click', e => {
        const toggleButton = e.target.closest(SELECTORS.TOGGLEBTN);
        if (toggleButton && toggleButton.dataset.target) {
            e.preventDefault();
            const targetDrawer = document.getElementById(toggleButton.dataset.target);
            const drawerInstance = Drawers.getDrawerInstanceForNode(targetDrawer);
            setLastUsedToggle(toggleButton);

            drawerInstance.toggleVisibility();
        }

        const openDrawerButton = e.target.closest(SELECTORS.OPENBTN);
        if (openDrawerButton && openDrawerButton.dataset.target) {
            e.preventDefault();
            const targetDrawer = document.getElementById(openDrawerButton.dataset.target);
            const drawerInstance = Drawers.getDrawerInstanceForNode(targetDrawer);
            setLastUsedToggle(toggleButton);

            drawerInstance.openDrawer();
        }

        const closeDrawerButton = e.target.closest(SELECTORS.CLOSEBTN);
        if (closeDrawerButton && closeDrawerButton.dataset.target) {
            e.preventDefault();
            const targetDrawer = document.getElementById(closeDrawerButton.dataset.target);
            const drawerInstance = Drawers.getDrawerInstanceForNode(targetDrawer);

            drawerInstance.closeDrawer();
            focusLastUsedToggle(closeDrawerButton.dataset.target);
        }
    });

    // Close drawer when another drawer opens.
    document.addEventListener(Drawers.eventTypes.drawerShow, e => {
        if (isLarge()) {
            return;
        }
        Drawers.closeOtherDrawers(e.detail.drawerInstance);
    });

    // Tooglers and openers blur listeners.
    const btnSelector = `${SELECTORS.TOGGLEBTN}, ${SELECTORS.OPENBTN}, ${SELECTORS.CLOSEBTN}`;
    document.addEventListener('focusout', (e) => {
        const button = e.target.closest(btnSelector);
        if (button?.dataset.restoreTooltipOnBlur !== undefined) {
            enableButtonTooltip(button);
        }
    });

    const closeOnResizeListener = () => {
        if (isSmall()) {
            let anyOpen = false;
            drawerMap.forEach(drawerInstance => {
                disableDrawerTooltips(drawerInstance.drawerNode);
                if (drawerInstance.isOpen) {
                    if (drawerInstance.closeOnResize) {
                        drawerInstance.closeDrawer();
                    } else {
                        anyOpen = true;
                    }
                }
            });

            if (anyOpen) {
                getBackdrop().then(backdrop => backdrop.show()).catch();
            }
        } else {
            drawerMap.forEach(drawerInstance => {
                enableDrawerTooltips(drawerInstance.drawerNode);
            });
            getBackdrop().then(backdrop => backdrop.hide()).catch();
        }
    };

    document.addEventListener('scroll', () => {
        const body = document.querySelector('body');
        if (window.scrollY >= window.innerHeight) {
            body.classList.add(CLASSES.SCROLLED);
        } else {
            body.classList.remove(CLASSES.SCROLLED);
        }
        // Horizontal scroll listener to displace the drawers to prevent covering
        // any possible sticky content.
        Drawers.displaceDrawers(window.scrollX);
    });

    const preventOverlap = debounce(Drawers.preventCoveringFocusedElement, 100);
    document.addEventListener('focusin', preventOverlap);
    document.addEventListener('focusout', preventOverlap);

    window.addEventListener('resize', debounce(closeOnResizeListener, 400));
};

registerListeners();

const drawers = document.querySelectorAll(SELECTORS.DRAWERS);
drawers.forEach(drawerNode => Drawers.getDrawerInstanceForNode(drawerNode));
