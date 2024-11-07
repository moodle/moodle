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
 * Reactive simple state manager.
 *
 * The state manager contains the state data, trigger update events and
 * can lock and unlock the state data.
 *
 * This file contains the three main elements of the state manager:
 * - State manager: the public class to alter the state, dispatch events and process update messages.
 * - Proxy handler: a private class to keep track of the state object changes.
 * - StateMap class: a private class extending Map class that triggers event when a state list is modifed.
 *
 * @module     core/local/reactive/statemanager
 * @class      StateManager
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Logger from 'core/local/reactive/logger';

/**
 * State manager class.
 *
 * This class handle the reactive state and ensure only valid mutations can modify the state.
 * It also provide methods to apply batch state update messages (see processUpdates function doc
 * for more details on update messages).
 *
 * Implementing a deep state manager is complex and will require many frontend resources. To keep
 * the state fast and simple, the state can ONLY store two kind of data:
 *  - Object with attributes
 *  - Sets of objects with id attributes.
 *
 * This is an example of a valid state:
 *
 * {
 *  course: {
 *      name: 'course name',
 *      shortname: 'courseshort',
 *      sectionlist: [21, 34]
 *  },
 *  sections: [
 *      {id: 21, name: 'Topic 1', visible: true},
 *      {id: 34, name: 'Topic 2', visible: false,
 *  ],
 * }
 *
 * The following cases are NOT allowed at a state ROOT level (throws an exception if they are assigned):
 *  - Simple values (strings, boolean...).
 *  - Arrays of simple values.
 *  - Array of objects without ID attribute (all arrays will be converted to maps and requires an ID).
 *
 * Thanks to those limitations it can simplify the state update messages and the event names. If You
 * need to store simple data, just group them in an object.
 *
 * To grant any state change triggers the proper events, the class uses two private structures:
 * - proxy handler: any object stored in the state is proxied using this class.
 * - StateMap class: any object set in the state will be converted to StateMap using the
 *   objects id attribute.
 */
export default class StateManager {

    /**
     * Create a basic reactive state store.
     *
     * The state manager is meant to work with native JS events. To ensure each reactive module can use
     * it in its own way, the parent element must provide a valid event dispatcher function and an optional
     * DOM element to anchor the event.
     *
     * @param {function} dispatchEvent the function to dispatch the custom event when the state changes.
     * @param {element} target the state changed custom event target (document if none provided)
     */
    constructor(dispatchEvent, target) {

        // The dispatch event function.
        /** @package */
        this.dispatchEvent = dispatchEvent;

        // The DOM container to trigger events.
        /** @package */
        this.target = target ?? document;

        // State can be altered freely until initial state is set.
        /** @package */
        this.readonly = false;

        // List of state changes pending to be published as events.
        /** @package */
        this.eventsToPublish = [];

        // The update state types functions.
        /** @package */
        this.updateTypes = {
            "create": this.defaultCreate.bind(this),
            "update": this.defaultUpdate.bind(this),
            "delete": this.defaultDelete.bind(this),
            "put": this.defaultPut.bind(this),
            "override": this.defaultOverride.bind(this),
            "remove": this.defaultRemove.bind(this),
            "prepareFields": this.defaultPrepareFields.bind(this),
        };

        // The state_loaded event is special because it only happens one but all components
        // may react to that state, even if they are registered after the setIinitialState.
        // For these reason we use a promise for that event.
        this.initialPromise = new Promise((resolve) => {
            const initialStateDone = (event) => {
                resolve(event.detail.state);
            };
            this.target.addEventListener('state:loaded', initialStateDone);
        });

        this.logger = new Logger();
    }

    /**
     * Loads the initial state.
     *
     * Note this method will trigger a state changed event with "state:loaded" actionname.
     *
     * The state mode will be set to read only when the initial state is loaded.
     *
     * @param {object} initialState
     */
    setInitialState(initialState) {

        if (this.state !== undefined) {
            throw Error('Initial state can only be initialized ones');
        }

        // Create the state object.
        const state = new Proxy({}, new Handler('state', this, true));
        for (const [prop, propValue] of Object.entries(initialState)) {
            state[prop] = propValue;
        }
        this.state = state;

        // When the state is loaded we can lock it to prevent illegal changes.
        this.readonly = true;

        this.dispatchEvent({
            action: 'state:loaded',
            state: this.state,
        }, this.target);
    }

    /**
     * Generate a promise that will be resolved when the initial state is loaded.
     *
     * In most cases the final state will be loaded using an ajax call. This is the reason
     * why states manager are created unlocked and won't be reactive until the initial state is set.
     *
     * @return {Promise} the resulting promise
     */
    getInitialPromise() {
        return this.initialPromise;
    }

    /**
     * Locks or unlocks the state to prevent illegal updates.
     *
     * Mutations use this method to modify the state. Once the state is updated, they must
     * block again the state.
     *
     * All changes done while the state is writable will be registered using registerStateAction.
     * When the state is set again to read only the method will trigger _publishEvents to communicate
     * changes to all watchers.
     *
     * @param {bool} readonly if the state is in read only mode enabled
     */
    setReadOnly(readonly) {

        this.readonly = readonly;

        let mode = 'off';

        // When the state is in readonly again is time to publish all pending events.
        if (this.readonly) {
            mode = 'on';
            this._publishEvents();
        }

        // Dispatch a read only event.
        this.dispatchEvent({
            action: `readmode:${mode}`,
            state: this.state,
            element: null,
        }, this.target);
    }

    /**
     * Add methods to process update state messages.
     *
     * The state manager provide a default update, create and delete methods. However,
     * some applications may require to override the default methods or even add new ones
     * like "refresh" or "error".
     *
     * @param {Object} newFunctions the new update types functions.
     */
    addUpdateTypes(newFunctions) {
        for (const [updateType, updateFunction] of Object.entries(newFunctions)) {
            if (typeof updateFunction === 'function') {
                this.updateTypes[updateType] = updateFunction.bind(newFunctions);
            }
        }
    }

    /**
     * Process a state updates array and do all the necessary changes.
     *
     * Note this method unlocks the state while it is executing and relocks it
     * when finishes.
     *
     * @param {array} updates
     * @param {Object} updateTypes optional functions to override the default update types.
     */
    processUpdates(updates, updateTypes) {
        if (!Array.isArray(updates)) {
            throw Error('State updates must be an array');
        }
        this.setReadOnly(false);
        updates.forEach((update) => {
            if (update.name === undefined) {
                throw Error('Missing state update name');
            }
            this.processUpdate(
                update.name,
                update.action,
                update.fields,
                updateTypes
            );
        });
        this.setReadOnly(true);
    }

    /**
     * Process a single state update.
     *
     * Note this method will not lock or unlock the state by itself.
     *
     * @param {string} updateName the state element to update
     * @param {string} action to action to perform
     * @param {object} fields the new data
     * @param {Object} updateTypes optional functions to override the default update types.
     */
    processUpdate(updateName, action, fields, updateTypes) {

        if (!fields) {
            throw Error('Missing state update fields');
        }

        if (updateTypes === undefined) {
            updateTypes = {};
        }

        action = action ?? 'update';

        const method = updateTypes[action] ?? this.updateTypes[action];

        if (method === undefined) {
            throw Error(`Unkown update action ${action}`);
        }

        // Some state data may require some cooking before sending to the
        // state. Reactive instances can overrdide the default fieldDefaults
        // method to add extra logic to all updates.
        const prepareFields = updateTypes.prepareFields ?? this.updateTypes.prepareFields;

        method(this, updateName, prepareFields(this, updateName, fields));
    }

    /**
     * Prepare fields for processing.
     *
     * This method is used to add default values or calculations from the frontend side.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     * @returns {Object} final fields data
     */
    defaultPrepareFields(stateManager, updateName, fields) {
        return fields;
    }


    /**
     * Process a create state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultCreate(stateManager, updateName, fields) {

        let state = stateManager.state;

        // Create can be applied only to lists, not to objects.
        if (state[updateName] instanceof StateMap) {
            state[updateName].add(fields);
            return;
        }
        state[updateName] = fields;
    }

    /**
     * Process a delete state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultDelete(stateManager, updateName, fields) {

        // Get the current value.
        let current = stateManager.get(updateName, fields.id);
        if (!current) {
            throw Error(`Inexistent ${updateName} ${fields.id}`);
        }

        // Process deletion.
        let state = stateManager.state;

        if (state[updateName] instanceof StateMap) {
            state[updateName].delete(fields.id);
            return;
        }
        delete state[updateName];
    }

    /**
     * Process a remove state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultRemove(stateManager, updateName, fields) {

        // Get the current value.
        let current = stateManager.get(updateName, fields.id);
        if (!current) {
            return;
        }

        // Process deletion.
        let state = stateManager.state;

        if (state[updateName] instanceof StateMap) {
            state[updateName].delete(fields.id);
            return;
        }
        delete state[updateName];
    }

    /**
     * Process a update state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultUpdate(stateManager, updateName, fields) {

        // Get the current value.
        let current = stateManager.get(updateName, fields.id);
        if (!current) {
            throw Error(`Inexistent ${updateName} ${fields.id}`);
        }

        // Execute updates.
        for (const [fieldName, fieldValue] of Object.entries(fields)) {
            current[fieldName] = fieldValue;
        }
    }

    /**
     * Process a put state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultPut(stateManager, updateName, fields) {

        // Get the current value.
        let current = stateManager.get(updateName, fields.id);
        if (current) {
            // Update attributes.
            for (const [fieldName, fieldValue] of Object.entries(fields)) {
                current[fieldName] = fieldValue;
            }
        } else {
            // Create new object.
            let state = stateManager.state;
            if (state[updateName] instanceof StateMap) {
                state[updateName].add(fields);
                return;
            }
            state[updateName] = fields;
        }
    }

    /**
     * Process an override state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     */
    defaultOverride(stateManager, updateName, fields) {

        // Get the current value.
        let current = stateManager.get(updateName, fields.id);
        if (current) {
            // Remove any unnecessary fields.
            for (const [fieldName] of Object.entries(current)) {
                if (fields[fieldName] === undefined) {
                    delete current[fieldName];
                }
            }
            // Update field.
            for (const [fieldName, fieldValue] of Object.entries(fields)) {
                current[fieldName] = fieldValue;
            }
        } else {
            // Create the element if not exists.
            let state = stateManager.state;
            if (state[updateName] instanceof StateMap) {
                state[updateName].add(fields);
                return;
            }
            state[updateName] = fields;
        }
    }

    /**
     * Set the logger class instance.
     *
     * Reactive instances can provide alternative loggers to provide advanced logging.
     * @param {Logger} logger
     */
    setLogger(logger) {
        this.logger = logger;
    }

    /**
     * Add a new log entry into the reactive logger.
     * @param {LoggerEntry} entry
     */
    addLoggerEntry(entry) {
        this.logger.add(entry);
    }

    /**
     * Get an element from the state or form an alternative state object.
     *
     * The altstate param is used by external update functions that gets the current
     * state as param.
     *
     * @param {String} name the state object name
     * @param {*} id and object id for state maps.
     * @return {Object|undefined} the state object found
     */
    get(name, id) {
        const state = this.state;

        let current = state[name];
        if (current instanceof StateMap) {
            if (id === undefined) {
                throw Error(`Missing id for ${name} state update`);
            }
            current = state[name].get(id);
        }

        return current;
    }

    /**
     * Get all element ids from the given state.
     *
     * @param {String} name the state object name
     * @return {Array} the element ids.
     */
    getIds(name) {
        const state = this.state;
        const current = state[name];
        if (!(current instanceof StateMap)) {
            throw Error(`${name} is not an instance of StateMap`);
        }
        return [...state[name].keys()];
    }

    /**
     * Register a state modification and generate the necessary events.
     *
     * This method is used mainly by proxy helpers to dispatch state change event.
     * However, mutations can use it to inform components about non reactive changes
     * in the state (only the two first levels of the state are reactive).
     *
     * Each action can produce several events:
     * - The specific attribute updated, created or deleter (example: "cm.visible:updated")
     * - The general state object updated, created or deleted (example: "cm:updated")
     * - If the element has an ID attribute, the specific event with id (example: "cm[42].visible:updated")
     * - If the element has an ID attribute, the general event with id (example: "cm[42]:updated")
     * - A generic state update event "state:update"
     *
     * @param {string} field the affected state field name
     * @param {string|null} prop the affecter field property (null if affect the full object)
     * @param {string} action the action done (created/updated/deleted)
     * @param {*} data the affected data
     */
    registerStateAction(field, prop, action, data) {

        let parentAction = 'updated';

        if (prop !== null) {
            this.eventsToPublish.push({
                eventName: `${field}.${prop}:${action}`,
                eventData: data,
                action,
            });
        } else {
            parentAction = action;
        }

        // Trigger extra events if the element has an ID attribute.
        if (data.id !== undefined) {
            if (prop !== null) {
                this.eventsToPublish.push({
                    eventName: `${field}[${data.id}].${prop}:${action}`,
                    eventData: data,
                    action,
                });
            }
            this.eventsToPublish.push({
                eventName: `${field}[${data.id}]:${parentAction}`,
                eventData: data,
                action: parentAction,
            });
        }

        // Register the general change.
        this.eventsToPublish.push({
            eventName: `${field}:${parentAction}`,
            eventData: data,
            action: parentAction,
        });

        // Register state updated event.
        this.eventsToPublish.push({
            eventName: `state:updated`,
            eventData: data,
            action: 'updated',
        });
    }

    /**
     * Internal method to publish events.
     *
     * This is a private method, it will be invoked when the state is set back to read only mode.
     */
    _publishEvents() {
        const fieldChanges = this.eventsToPublish;
        this.eventsToPublish = [];

        // Dispatch a transaction start event.
        this.dispatchEvent({
            action: 'transaction:start',
            state: this.state,
            element: null,
            changes: fieldChanges,
        }, this.target);

        // State changes can be registered in any order. However it will avoid many
        // components errors if they are sorted to have creations-updates-deletes in case
        // some component needs to create or destroy DOM elements before updating them.
        fieldChanges.sort((a, b) => {
            const weights = {
                created: 0,
                updated: 1,
                deleted: 2,
            };
            const aweight = weights[a.action] ?? 0;
            const bweight = weights[b.action] ?? 0;
            // In case both have the same weight, the eventName length decide.
            if (aweight === bweight) {
                return a.eventName.length - b.eventName.length;
            }
            return aweight - bweight;
        });

        // List of the published events to prevent redundancies.
        let publishedEvents = new Set();
        let transactionEvents = [];

        fieldChanges.forEach((event) => {

            const eventkey = `${event.eventName}.${event.eventData.id ?? 0}`;

            if (!publishedEvents.has(eventkey)) {
                this.dispatchEvent({
                    action: event.eventName,
                    state: this.state,
                    element: event.eventData
                }, this.target);

                publishedEvents.add(eventkey);
                transactionEvents.push(event);
            }
        });

        // Dispatch a transaction end event.
        this.dispatchEvent({
            action: 'transaction:end',
            state: this.state,
            element: null,
            changes: transactionEvents,
        }, this.target);
    }
}

// Proxy helpers.

/**
 * The proxy handler.
 *
 * This class will inform any value change directly to the state manager.
 *
 * The proxied variable will throw an error if it is altered when the state manager is
 * in read only mode.
 */
class Handler {

    /**
     * Class constructor.
     *
     * @param {string} name the variable name used for identify triggered actions
     * @param {StateManager} stateManager the state manager object
     * @param {boolean} proxyValues if new values must be proxied (used only at state root level)
     */
    constructor(name, stateManager, proxyValues) {
        this.name = name;
        this.stateManager = stateManager;
        this.proxyValues = proxyValues ?? false;
    }

    /**
     * Set trap to trigger events when the state changes.
     *
     * @param {object} obj the source object (not proxied)
     * @param {string} prop the attribute to set
     * @param {*} value the value to save
     * @param {*} receiver the proxied element to be attached to events
     * @returns {boolean} if the value is set
     */
    set(obj, prop, value, receiver) {

        // Only mutations should be able to set state values.
        if (this.stateManager.readonly) {
            throw new Error(`State locked. Use mutations to change ${prop} value in ${this.name}.`);
        }

        // Check any data change.
        if (JSON.stringify(obj[prop]) === JSON.stringify(value)) {
            return true;
        }

        const action = (obj[prop] !== undefined) ? 'updated' : 'created';

        // Proxy value if necessary (used at state root level).
        if (this.proxyValues) {
            if (Array.isArray(value)) {
                obj[prop] = new StateMap(prop, this.stateManager).loadValues(value);
            } else {
                obj[prop] = new Proxy(value, new Handler(prop, this.stateManager));
            }
        } else {
            obj[prop] = value;
        }

        // If the state is not ready yet means the initial state is not yet loaded.
        if (this.stateManager.state === undefined) {
            return true;
        }

        this.stateManager.registerStateAction(this.name, prop, action, receiver);

        return true;
    }

    /**
     * Delete property trap to trigger state change events.
     *
     * @param {*} obj the affected object (not proxied)
     * @param {*} prop the prop to delete
     * @returns {boolean} if prop is deleted
     */
    deleteProperty(obj, prop) {
        // Only mutations should be able to set state values.
        if (this.stateManager.readonly) {
            throw new Error(`State locked. Use mutations to delete ${prop} in ${this.name}.`);
        }
        if (prop in obj) {

            delete obj[prop];

            this.stateManager.registerStateAction(this.name, prop, 'deleted', obj);
        }
        return true;
    }
}

/**
 * Class to add events dispatching to the JS Map class.
 *
 * When the state has a list of objects (with IDs) it will be converted into a StateMap.
 * StateMap is used almost in the same way as a regular JS map. Because all elements have an
 * id attribute, it has some specific methods:
 *  - add: a convenient method to add an element without specifying the key ("id" attribute will be used as a key).
 *  - loadValues: to add many elements at once wihout specifying keys ("id" attribute will be used).
 *
 * Apart, the main difference between regular Map and MapState is that this one will inform any change to the
 * state manager.
 */
class StateMap extends Map {

    /**
     * Create a reactive Map.
     *
     * @param {string} name the property name
     * @param {StateManager} stateManager the state manager
     * @param {iterable} iterable an iterable object to create the Map
     */
    constructor(name, stateManager, iterable) {
        // We don't have any "this" until be call super.
        super(iterable);
        this.name = name;
        this.stateManager = stateManager;
    }

    /**
     * Set an element into the map.
     *
     * Each value needs it's own id attribute. Objects without id will be rejected.
     * The function will throw an error if the value id and the key are not the same.
     *
     * @param {*} key the key to store
     * @param {*} value the value to store
     * @returns {Map} the resulting Map object
     */
    set(key, value) {

        // Only mutations should be able to set state values.
        if (this.stateManager.readonly) {
            throw new Error(`State locked. Use mutations to change ${key} value in ${this.name}.`);
        }

        // Normalize keys as string to prevent json decoding errors.
        key = this.normalizeKey(key);

        this.checkValue(value);

        if (key === undefined || key === null) {
            throw Error('State lists keys cannot be null or undefined');
        }

        // ID is mandatory and should be the same as the key.
        if (this.normalizeKey(value.id) !== key) {
            throw new Error(`State error: ${this.name} list element ID (${value.id}) and key (${key}) mismatch`);
        }

        const action = (super.has(key)) ? 'updated' : 'created';

        // Save proxied data into the list.
        const result = super.set(key, new Proxy(value, new Handler(this.name, this.stateManager)));

        // If the state is not ready yet means the initial state is not yet loaded.
        if (this.stateManager.state === undefined) {
            return result;
        }

        this.stateManager.registerStateAction(this.name, null, action, super.get(key));

        return result;
    }

    /**
     * Check if a value is valid to be stored in a a State List.
     *
     * Only objects with id attribute can be stored in State lists.
     *
     * This method throws an error if the value is not valid.
     *
     * @param {object} value (with ID)
     */
    checkValue(value) {
        if (!typeof value === 'object' && value !== null) {
            throw Error('State lists can contain objects only');
        }

        if (value.id === undefined) {
            throw Error('State lists elements must contain at least an id attribute');
        }
    }

    /**
     * Return a normalized key value for state map.
     *
     * Regular maps uses strict key comparissons but state maps are indexed by ID.JSON conversions
     * and webservices sometimes do unexpected types conversions so we convert any integer key to string.
     *
     * @param {*} key the provided key
     * @returns {string}
     */
    normalizeKey(key) {
        return String(key).valueOf();
    }

    /**
     * Insert a new element int a list.
     *
     * Each value needs it's own id attribute. Objects withouts id will be rejected.
     *
     * @param {object} value the value to add (needs an id attribute)
     * @returns {Map} the resulting Map object
     */
    add(value) {
        this.checkValue(value);
        return this.set(value.id, value);
    }

    /**
     * Return a state map element.
     *
     * @param {*} key the element id
     * @return {Object}
     */
    get(key) {
        return super.get(this.normalizeKey(key));
    }

    /**
     * Check whether an element with the specified key exists or not.
     *
     * @param {*} key the key to find
     * @return {boolean}
     */
    has(key) {
        return super.has(this.normalizeKey(key));
    }

    /**
     * Delete an element from the map.
     *
     * @param {*} key
     * @returns {boolean}
     */
    delete(key) {
        // State maps uses only string keys to avoid strict comparisons.
        key = this.normalizeKey(key);

        // Only mutations should be able to set state values.
        if (this.stateManager.readonly) {
            throw new Error(`State locked. Use mutations to change ${key} value in ${this.name}.`);
        }

        const previous = super.get(key);

        const result = super.delete(key);
        if (!result) {
            return result;
        }

        this.stateManager.registerStateAction(this.name, null, 'deleted', previous);

        return result;
    }

    /**
     * Return a suitable structure for JSON conversion.
     *
     * This function is needed because new values are compared in JSON. StateMap has Private
     * attributes which cannot be stringified (like this.stateManager which will produce an
     * infinite recursivity).
     *
     * @returns {array}
     */
    toJSON() {
        let result = [];
        this.forEach((value) => {
            result.push(value);
        });
        return result;
    }

    /**
     * Insert a full list of values using the id attributes as keys.
     *
     * This method is used mainly to initialize the list. Note each element is indexed by its "id" attribute.
     * This is a basic restriction of StateMap. All elements need an id attribute, otherwise it won't be saved.
     *
     * @param {iterable} values the values to load
     * @returns {StateMap} return the this value
     */
    loadValues(values) {
        values.forEach((data) => {
            this.checkValue(data);
            let key = data.id;
            let newvalue = new Proxy(data, new Handler(this.name, this.stateManager));
            this.set(key, newvalue);
        });
        return this;
    }
}
