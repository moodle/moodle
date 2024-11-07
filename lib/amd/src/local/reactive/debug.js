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
 * Reactive module debug tools.
 *
 * @module     core/local/reactive/debug
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Reactive from 'core/local/reactive/reactive';
import log from 'core/log';

// The list of reactives instances.
const reactiveInstances = {};

// The reactive debugging objects.
const reactiveDebuggers = {};

/**
 * Reactive module debug tools.
 *
 * If debug is enabled, this reactive module will spy all the reactive instances and keep a record
 * of the changes and components they have.
 *
 * It is important to note that the Debug class is also a Reactive module. The debug instance keeps
 * the reactive instances data as its own state. This way it is possible to implement development tools
 * that whatches this data.
 *
 * @class      core/reactive/local/reactive/debug/Debug
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Debug extends Reactive {

    /**
     * Set the initial state.
     *
     * @param {object} stateData the initial state data.
     */
    setInitialState(stateData) {
        super.setInitialState(stateData);
        log.debug(`Debug module "M.reactive" loaded.`);
    }

    /**
     * List the currents page reactives instances.
     */
    get list() {
        return JSON.parse(JSON.stringify(this.state.reactives));
    }

    /**
     * Register a new Reactive instance.
     *
     * This method is called every time a "new Reactive" is executed.
     *
     * @param {Reactive} instance the reactive instance
     */
    registerNewInstance(instance) {

        // Generate a valid variable name for that instance.
        let name = instance.name ?? `instance${this.state.reactives.length}`;
        name = name.replace(/\W/g, '');

        log.debug(`Registering new reactive instance "M.reactive.${name}"`);

        reactiveInstances[name] = instance;
        reactiveDebuggers[name] = new DebugInstance(reactiveInstances[name]);
        // Register also in the state.
        this.dispatch('putInstance', name, instance);
        // Add debug watchers to instance.
        const refreshMethod = () => {
            this.dispatch('putInstance', name, instance);
        };
        instance.target.addEventListener('readmode:on', refreshMethod);
        instance.target.addEventListener('readmode:off', refreshMethod);
        instance.target.addEventListener('registerComponent:success', refreshMethod);
        instance.target.addEventListener('transaction:end', refreshMethod);
        // We store the last transaction into the state.
        const storeTransaction = ({detail}) => {
            const changes = detail?.changes;
            this.dispatch('lastTransaction', name, changes);
        };
        instance.target.addEventListener('transaction:end', storeTransaction);
    }

    /**
     * Returns a debugging object for a specific Reactive instance.
     *
     * A debugging object is a class that wraps a Reactive instance to quick access some of the
     * reactive methods using the browser JS console.
     *
     * @param {string} name the Reactive instance name
     * @returns {DebugInstance} a debug object wrapping the Reactive instance
     */
    debug(name) {
        return reactiveDebuggers[name];
    }
}

/**
 * The debug state mutations class.
 *
 * @class core/reactive/local/reactive/debug/Mutations
 */
class Mutations {

    /**
     * Insert or update a new instance into the debug state.
     *
     * @param {StateManager} stateManager the debug state manager
     * @param {string} name the instance name
     * @param {Reactive} instance the reactive instance
     */
    putInstance(stateManager, name, instance) {
        const state = stateManager.state;

        stateManager.setReadOnly(false);

        if (state.reactives.has(name)) {
            state.reactives.get(name).countcomponents = instance.components.length;
            state.reactives.get(name).readOnly = instance.stateManager.readonly;
            state.reactives.get(name).modified = new Date().getTime();
        } else {
            state.reactives.add({
                id: name,
                countcomponents: instance.components.length,
                readOnly: instance.stateManager.readonly,
                lastChanges: [],
                modified: new Date().getTime(),
            });
        }
        stateManager.setReadOnly(true);
    }

    /**
     * Update the lastChanges attribute with a list of changes
     *
     * @param {StateManager} stateManager the debug reactive state
     * @param {string} name the instance name
     * @param {array} changes the list of changes
     */
    lastTransaction(stateManager, name, changes) {
        if (!changes || changes.length === 0) {
            return;
        }

        const state = stateManager.state;
        const lastChanges = ['transaction:start'];

        changes.forEach(change => {
            lastChanges.push(change.eventName);
        });

        lastChanges.push('transaction:end');

        stateManager.setReadOnly(false);

        // Dirty hack to force the lastChanges:updated event to be dispatched.
        state.reactives.get(name).lastChanges = [];

        // Assign the actual value.
        state.reactives.get(name).lastChanges = lastChanges;

        stateManager.setReadOnly(true);
    }
}

/**
 * Class used to debug a specific instance and manipulate the state from the JS console.
 *
 * @class      core/reactive/local/reactive/debug/DebugInstance
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DebugInstance {

    /**
     * Constructor.
     *
     * @param {Reactive} instance the reactive instance
     */
    constructor(instance) {
        this.instance = instance;
        // Add some debug data directly into the instance. This way we avoid having attributes
        // that will confuse the console aoutocomplete.
        if (instance._reactiveDebugData === undefined) {
            instance._reactiveDebugData = {
                highlighted: false,
            };
        }
    }

    /**
     * Set the read only mode.
     *
     * Quick access to the instance setReadOnly method.
     *
     * @param {bool} value the new read only value
     */
    set readOnly(value) {
        this.instance.stateManager.setReadOnly(value);
    }

    /**
     * Get the read only value
     *
     * @returns {bool}
     */
    get readOnly() {
        return this.instance.stateManager.readonly;
    }

    /**
     * Return the current state object.
     *
     * @returns {object}
     */
    get state() {
        return this.instance.state;
    }

    /**
     * Tooggle the reactive HTML element highlight registered in this reactive instance.
     *
     * @param {bool} value the highlight value
     */
    set highlight(value) {
        this.instance._reactiveDebugData.highlighted = value;
        this.instance.components.forEach(({element}) => {
            const border = (value) ? `thick solid #0000FF` : '';
            element.style.border = border;
        });
    }

    /**
     * Get the current highligh value.
     *
     * @returns {bool}
     */
    get highlight() {
        return this.instance._reactiveDebugData.highlighted;
    }

    /**
     * List all the components registered in this instance.
     *
     * @returns {array}
     */
    get components() {
        return [...this.instance.components];
    }

    /**
     * List all the state changes evenet pending to dispatch.
     *
     * @returns {array}
     */
    get changes() {
        const result = [];
        this.instance.stateManager.eventsToPublish.forEach(
            (element) => {
                result.push(element.eventName);
            }
        );
        return result;
    }

    /**
     * Dispatch a change in the state.
     *
     * Usually reactive modules throw an error directly to the components when something
     * goes wrong. However, course editor can directly display a notification.
     *
     * @method dispatch
     * @param {*} args
     */
    async dispatch(...args) {
        this.instance.dispatch(...args);
    }

    /**
     * Return all the HTML elements registered in the instance components.
     *
     * @returns {array}
     */
    get elements() {
        const result = [];
        this.instance.components.forEach(({element}) => {
            result.push(element);
        });
        return result;
    }

    /**
     * Return a plain copy of the state data.
     *
     * @returns {object}
     */
    get stateData() {
        return JSON.parse(JSON.stringify(this.state));
    }

    /**
     * Process an update state array.
     *
     * @param {array} updates an array of update state messages
     */
    processUpdates(updates) {
        this.instance.stateManager.processUpdates(updates);
    }
}

const stateChangedEventName = 'core_reactive_debug:stateChanged';

/**
 * Internal state changed event.
 *
 * @method dispatchStateChangedEvent
 * @param {object} detail the full state
 * @param {object} target the custom event target (document if none provided)
 */
function dispatchStateChangedEvent(detail, target) {
    if (target === undefined) {
        target = document;
    }
    target.dispatchEvent(
        new CustomEvent(
            stateChangedEventName,
            {
                bubbles: true,
                detail: detail,
            }
        )
    );
}

/**
 * The main init method to initialize the reactive debug.
 * @returns {object}
 */
export const initDebug = () => {
    const debug = new Debug({
        name: 'CoreReactiveDebug',
        eventName: stateChangedEventName,
        eventDispatch: dispatchStateChangedEvent,
        mutations: new Mutations(),
        state: {
            reactives: [],
        },
    });

    // The reactiveDebuggers will be used as a way of access the debug instances but also to register every new
    // instance. To ensure this will update the reactive debug state we add the registerNewInstance method to it.
    reactiveDebuggers.registerNewInstance = debug.registerNewInstance.bind(debug);

    return {
        debug,
        debuggers: reactiveDebuggers,
    };
};
