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

import Templates from 'core/templates';
import {addOverlay, removeOverlay, removeAllOverlays} from 'core/local/reactive/overlay';

/**
 * Reactive UI component base class.
 *
 * Each UI reactive component should extend this class to interact with a reactive state.
 *
 * @module     core/local/reactive/basecomponent
 * @class     core/local/reactive/basecomponent
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {

    /**
     * The component descriptor data structure.
     *
     * This structure is used by any component and init method to define the way the component will interact
     * with the interface and whith reactive instance operates. The logic behind this object is to avoid
     * unnecessary dependancies between the final interface and the state logic.
     *
     * Any component interacts with a single main DOM element (description.element) but it can use internal
     * selector to select elements within this main element (descriptor.selectors). By default each component
     * will provide it's own default selectors, but those can be overridden by the "descriptor.selectors"
     * property in case the mustache wants to reuse the same component logic but with a different interface.
     *
     * @typedef {object} descriptor
     * @property {Reactive} reactive an optional reactive module to register in
     * @property {DOMElement} element all components needs an element to anchor events
     * @property {object} [selectors] an optional object to override query selectors
     */

    /**
     * The class constructor.
     *
     * The only param this method gets is a constructor with all the mandatory
     * and optional component data. Component will receive the same descriptor
     * as create method param.
     *
     * This method will call the "create" method before registering the component into
     * the reactive module. This way any component can add default selectors and events.
     *
     * @param {descriptor} descriptor data to create the object.
     */
    constructor(descriptor) {

        if (descriptor.element === undefined || !(descriptor.element instanceof HTMLElement)) {
            throw Error(`Reactive components needs a main DOM element to dispatch events`);
        }

        this.element = descriptor.element;

        // Variable to track event listeners.
        this.eventHandlers = new Map([]);
        this.eventListeners = [];

        // Empty default component selectors.
        this.selectors = {};

        // Empty default event list from the static method.
        this.events = this.constructor.getEvents();

        // Call create function to get the component defaults.
        this.create(descriptor);

        // Overwrite the components selectors if necessary.
        if (descriptor.selectors !== undefined) {
            this.addSelectors(descriptor.selectors);
        }

        // Register into a reactive instance.
        if (descriptor.reactive === undefined) {
            // Ask parent components for registration.
            this.element.dispatchEvent(new CustomEvent(
                'core/reactive:requestRegistration',
                {
                    bubbles: true,
                    detail: {component: this},
                }
            ));
        } else {
            this.reactive = descriptor.reactive;
            this.reactive.registerComponent(this);
            // Add a listener to register child components.
            this.addEventListener(
                this.element,
                'core/reactive:requestRegistration',
                (event) => {
                    if (event?.detail?.component) {
                        event.stopPropagation();
                        this.registerChildComponent(event.detail.component);
                    }
                }
            );
        }
    }

    /**
     * Return the component custom event names.
     *
     * Components may override this method to provide their own events.
     *
     * Component custom events is an important part of component reusability. This function
     * is static because is part of the component definition and should be accessible from
     * outsite the instances. However, values will be available at instance level in the
     * this.events object.
     *
     * @returns {Object} the component events.
     */
    static getEvents() {
        return {};
    }

    /**
     * Component create function.
     *
     * Default init method will call "create" when all internal attributes are set
     * but before the component is not yet registered in the reactive module.
     *
     * In this method any component can define its own defaults such as:
     * - this.selectors {object} the default query selectors of this component.
     * - this.events {object} a list of event names this component dispatch
     * - extract any data from the main dom element (this.element)
     * - set any other data the component uses
     *
     * @param {descriptor} descriptor the component descriptor
     */
    // eslint-disable-next-line no-unused-vars
    create(descriptor) {
        // Components may override this method to initialize selects, events or other data.
    }

    /**
     * Component destroy hook.
     *
     * BaseComponent call this method when a component is unregistered or removed.
     *
     * Components may override this method to clean the HTML or do some action when the
     * component is unregistered or removed.
     */
    destroy() {
        // Components can override this method.
    }

    /**
     * Return the list of watchers that component has.
     *
     * Each watcher is represented by an object with two attributes:
     * - watch (string) the specific state event to watch. Example 'section.visible:updated'
     * - handler (function) the function to call when the watching state change happens
     *
     * Any component shoudl override this method to define their state watchers.
     *
     * @returns {array} array of watchers.
     */
    getWatchers() {
        return [];
    }

    /**
     * Reactive module will call this method when the state is ready.
     *
     * Component can override this method to update/load the component HTML or to bind
     * listeners to HTML entities.
     */
    stateReady() {
        // Components can override this method.
    }

    /**
     * Get the main DOM element of this component or a subelement.
     *
     * @param {string|undefined} query optional subelement query
     * @param {string|undefined} dataId optional data-id value
     * @returns {element|undefined} the DOM element (if any)
     */
    getElement(query, dataId) {
        if (query === undefined && dataId === undefined) {
            return this.element;
        }
        const dataSelector = (dataId) ? `[data-id='${dataId}']` : '';
        const selector = `${query ?? ''}${dataSelector}`;
        return this.element.querySelector(selector);
    }

    /**
     * Get the all subelement that match a query selector.
     *
     * @param {string|undefined} query optional subelement query
     * @param {string|undefined} dataId optional data-id value
     * @returns {NodeList} the DOM elements
     */
    getElements(query, dataId) {
        const dataSelector = (dataId) ? `[data-id='${dataId}']` : '';
        const selector = `${query ?? ''}${dataSelector}`;
        return this.element.querySelectorAll(selector);
    }

    /**
     * Add or update the component selectors.
     *
     * @param {Object} newSelectors an object of new selectors.
     */
    addSelectors(newSelectors) {
        for (const [selectorName, selector] of Object.entries(newSelectors)) {
            this.selectors[selectorName] = selector;
        }
    }

    /**
     * Return a component selector.
     *
     * @param {string} selectorName the selector name
     * @return {string|undefined} the query selector
     */
    getSelector(selectorName) {
        return this.selectors[selectorName];
    }

    /**
     * Dispatch a custom event on this.element.
     *
     * This is just a convenient method to dispatch custom events from within a component.
     * Components are free to use an alternative function to dispatch custom
     * events. The only restriction is that it should be dispatched on this.element
     * and specify "bubbles:true" to alert any component listeners.
     *
     * @param {string} eventName the event name
     * @param {*} detail event detail data
     */
    dispatchEvent(eventName, detail) {
        this.element.dispatchEvent(new CustomEvent(eventName, {
            bubbles: true,
            detail: detail,
        }));
    }

    /**
     * Render a new Component using a mustache file.
     *
     * It is important to note that this method should NOT be used for loading regular mustache files
     * as it returns a Promise that will only be resolved if the mustache registers a component instance.
     *
     * @param {element} target the DOM element that contains the component
     * @param {string} file the component mustache file to render
     * @param {*} data the mustache data
     * @return {Promise} a promise of the resulting component instance
     */
    renderComponent(target, file, data) {
        return new Promise((resolve, reject) => {
            target.addEventListener('ComponentRegistration:Success', ({detail}) => {
                resolve(detail.component);
            });
            target.addEventListener('ComponentRegistration:Fail', () => {
                reject(`Registration of ${file} fails.`);
            });
            Templates.renderForPromise(
                file,
                data
            ).then(({html, js}) => {
                Templates.replaceNodeContents(target, html, js);
                return true;
            }).catch(error => {
                reject(`Rendering of ${file} throws an error.`);
                throw error;
            });
        });
    }

    /**
     * Add and bind an event listener to a target and keep track of all event listeners.
     *
     * The native element.addEventListener method is not object oriented friently as the
     * "this" represents the element that triggers the event and not the listener class.
     * As components can be unregister and removed at any time, the BaseComponent provides
     * this method to keep track of all component listeners and do all of the bind stuff.
     *
     * @param {Element} target the event target
     * @param {string} type the event name
     * @param {function} listener the class method that recieve the event
     */
    addEventListener(target, type, listener) {

        // Check if we have the bind version of that listener.
        let bindListener = this.eventHandlers.get(listener);

        if (bindListener === undefined) {
            bindListener = listener.bind(this);
            this.eventHandlers.set(listener, bindListener);
        }

        target.addEventListener(type, bindListener);

        // Keep track of all component event listeners in case we need to remove them.
        this.eventListeners.push({
            target,
            type,
            bindListener,
        });

    }

    /**
     * Remove an event listener from a component.
     *
     * This method allows components to remove listeners without keeping track of the
     * listeners bind versions of the method. Both addEventListener and removeEventListener
     * keeps internally the relation between the original class method and the bind one.
     *
     * @param {Element} target the event target
     * @param {string} type the event name
     * @param {function} listener the class method that recieve the event
     */
    removeEventListener(target, type, listener) {
        // Check if we have the bind version of that listener.
        let bindListener = this.eventHandlers.get(listener);

        if (bindListener === undefined) {
            // This listener has not been added.
            return;
        }

        target.removeEventListener(type, bindListener);
    }

    /**
     * Remove all event listeners from this component.
     *
     * This method is called also when the component is unregistered or removed.
     *
     * Note that only listeners registered with the addEventListener method
     * will be removed. Other manual listeners will keep active.
     */
    removeAllEventListeners() {
        this.eventListeners.forEach(({target, type, bindListener}) => {
            target.removeEventListener(type, bindListener);
        });
        this.eventListeners = [];
    }

    /**
     * Remove a previously rendered component instance.
     *
     * This method will remove the component HTML and unregister it from the
     * reactive module.
     */
    remove() {
        this.unregister();
        this.element.remove();
    }

    /**
     * Unregister the component from the reactive module.
     *
     * This method will disable the component logic, event listeners and watchers
     * but it won't remove any HTML created by the component. However, it will trigger
     * the destroy hook to allow the component to clean parts of the interface.
     */
    unregister() {
        this.reactive.unregisterComponent(this);
        this.removeAllEventListeners();
        this.destroy();
    }

    /**
     * Dispatch a component registration event to inform the parent node.
     *
     * The registration event is different from the rest of the component events because
     * is the only way in which components can communicate its existence to a possible parent.
     * Most components will be created by including a mustache file, child components
     * must emit a registration event to the parent DOM element to alert about the registration.
     */
    dispatchRegistrationSuccess() {
        // The registration event does not bubble because we just want to comunicate with the parentNode.
        // Otherwise, any component can get multiple registrations events and could not differentiate
        // between child components and grand child components.
        if (this.element.parentNode === undefined) {
            return;
        }
        // This custom element is captured by renderComponent method.
        this.element.parentNode.dispatchEvent(new CustomEvent(
            'ComponentRegistration:Success',
            {
                bubbles: false,
                detail: {component: this},
            }
        ));
    }

    /**
     * Dispatch a component registration fail event to inform the parent node.
     *
     * As dispatchRegistrationSuccess, this method will communicate the registration fail to the
     * parent node to inform the possible parent component.
     */
    dispatchRegistrationFail() {
        if (this.element.parentNode === undefined) {
            return;
        }
        // This custom element is captured only by renderComponent method.
        this.element.parentNode.dispatchEvent(new CustomEvent(
            'ComponentRegistration:Fail',
            {
                bubbles: false,
                detail: {component: this},
            }
        ));
    }

    /**
     * Register a child component into the reactive instance.
     *
     * @param {self} component the component to register.
     */
    registerChildComponent(component) {
        component.reactive = this.reactive;
        this.reactive.registerComponent(component);
    }

    /**
     * Set the lock value and locks or unlocks the element.
     *
     * @param {boolean} locked the new locked value
     */
    set locked(locked) {
        this.setElementLocked(this.element, locked);
    }

    /**
     * Get the current locked value from the element.
     *
     * @return {boolean}
     */
    get locked() {
        return this.getElementLocked(this.element);
    }

    /**
     * Lock/unlock an element.
     *
     * @param {Element} target the event target
     * @param {boolean} locked the new locked value
     */
    setElementLocked(target, locked) {
        target.dataset.locked = locked ?? false;
        if (locked) {
            // Disable interactions.
            target.style.pointerEvents = 'none';
            target.style.userSelect = 'none';
            // Check if it is draggable.
            if (target.hasAttribute('draggable')) {
                target.setAttribute('draggable', false);
            }
            target.setAttribute('aria-busy', true);
        } else {
            // Enable interactions.
            target.style.pointerEvents = null;
            target.style.userSelect = null;
            // Check if it was draggable.
            if (target.hasAttribute('draggable')) {
                target.setAttribute('draggable', true);
            }
            target.setAttribute('aria-busy', false);
        }
    }

    /**
     * Get the current locked value from the element.
     *
     * @param {Element} target the event target
     * @return {boolean}
     */
    getElementLocked(target) {
        return target.dataset.locked ?? false;
    }

    /**
     * Adds an overlay to a specific page element.
     *
     * @param {Object} definition the overlay definition.
     * @param {String} definition.content an optional overlay content.
     * @param {String} definition.classes an optional CSS classes
     * @param {Element} target optional parent object (this.element will be used if none provided)
     */
    async addOverlay(definition, target) {
        if (this._overlay) {
            this.removeOverlay();
        }
        this._overlay = await addOverlay(
            {
                content: definition.content,
                css: definition.classes ?? 'file-drop-zone',
            },
            target ?? this.element
        );
    }

    /**
     * Remove the current overlay.
     */
    removeOverlay() {
        if (!this._overlay) {
            return;
        }
        removeOverlay(this._overlay);
        this._overlay = null;
    }

    /**
     * Remove all page overlais.
     */
    removeAllOverlays() {
        removeAllOverlays();
    }
}
