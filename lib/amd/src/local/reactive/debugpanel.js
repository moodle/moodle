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
 * Reactive module debug panel.
 *
 * This module contains all the UI components for the reactive debug tools.
 * Those tools are only available if the debug is enables and could be used
 * from the footer.
 *
 * @module     core/local/reactive/debugpanel
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent, DragDrop, debug} from 'core/reactive';
import log from 'core/log';
import {debounce} from 'core/utils';

/**
 * Init the main reactive panel.
 *
 * @param {element|string} target the DOM main element or its ID
 * @param {object} selectors optional css selector overrides
 */
export const init = (target, selectors) => {
    const element = document.getElementById(target);
    // Check if the debug reactive module is available.
    if (debug === undefined) {
        element.remove();
        return;
    }
    // Create the main component.
    new GlobalDebugPanel({
        element,
        reactive: debug,
        selectors,
    });
};

/**
 * Init an instance reactive subpanel.
 *
 * @param {element|string} target the DOM main element or its ID
 * @param {object} selectors optional css selector overrides
 */
export const initsubpanel = (target, selectors) => {
    const element = document.getElementById(target);
    // Check if the debug reactive module is available.
    if (debug === undefined) {
        element.remove();
        return;
    }
    // Create the main component.
    new DebugInstanceSubpanel({
        element,
        reactive: debug,
        selectors,
    });
};

/**
 * Component for the main reactive dev panel.
 *
 * This component shows the list of reactive instances and handle the buttons
 * to open a specific instance panel.
 */
class GlobalDebugPanel extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'GlobalDebugPanel';
        // Default query selectors.
        this.selectors = {
            LOADERS: `[data-for='loaders']`,
            SUBPANEL: `[data-for='subpanel']`,
            NOINSTANCES: `[data-for='noinstances']`,
            LOG: `[data-for='log']`,
        };
        this.classes = {
            HIDE: `d-none`,
        };
        // The list of loaded debuggers.
        this.subPanels = new Set();
    }

    /**
     * Initial state ready method.
     *
     * @param {object} state the initial state
     */
    stateReady(state) {
        this._updateReactivesPanels({state});
        // Remove loading wheel.
        this.getElement(this.selectors.SUBPANEL).innerHTML = '';
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `reactives:created`, handler: this._updateReactivesPanels},
        ];
    }

    /**
     * Update the list of reactive instances.
     * @param {Object} args
     * @param {Object} args.state the current state
     */
    _updateReactivesPanels({state}) {
        this.getElement(this.selectors.NOINSTANCES)?.classList?.toggle(
            this.classes.HIDE,
            state.reactives.size > 0
        );
        // Generate loading buttons.
        state.reactives.forEach(
            instance => {
                this._createLoader(instance);
            }
        );
    }

    /**
     * Create a debug panel button for a specific reactive instance.
     *
     * @param {object} instance hte instance data
     */
    _createLoader(instance) {
        if (this.subPanels.has(instance.id)) {
            return;
        }
        this.subPanels.add(instance.id);
        const loaders = this.getElement(this.selectors.LOADERS);
        const btn = document.createElement("button");
        btn.innerHTML = instance.id;
        btn.dataset.id = instance.id;
        loaders.appendChild(btn);
        // Add click event.
        this.addEventListener(btn, 'click', () => this._openPanel(btn, instance));
    }

    /**
     * Open a debug panel.
     *
     * @param {Element} btn the button element
     * @param {object} instance the instance data
     */
    async _openPanel(btn, instance) {
        try {
            const target = this.getElement(this.selectors.SUBPANEL);
            const data = {...instance};
            await this.renderComponent(target, 'core/local/reactive/debuginstancepanel', data);
        } catch (error) {
            log.error('Cannot load reactive debug subpanel');
            throw error;
        }
    }
}

/**
 * Component for the main reactive dev panel.
 *
 * This component shows the list of reactive instances and handle the buttons
 * to open a specific instance panel.
 */
class DebugInstanceSubpanel extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'DebugInstanceSubpanel';
        // Default query selectors.
        this.selectors = {
            NAME: `[data-for='name']`,
            CLOSE: `[data-for='close']`,
            READMODE: `[data-for='readmode']`,
            HIGHLIGHT: `[data-for='highlight']`,
            LOG: `[data-for='log']`,
            STATE: `[data-for='state']`,
            CLEAN: `[data-for='clean']`,
            PIN: `[data-for='pin']`,
            SAVE: `[data-for='save']`,
            INVALID: `[data-for='invalid']`,
        };
        this.id = this.element.dataset.id;
        this.controller = M.reactive[this.id];

        // The component is created always pinned.
        this.draggable = false;
        // We want the element to be dragged like modal.
        this.relativeDrag = true;
        // Save warning (will be loaded when state is ready.
        this.strings = {
            savewarning: '',
        };
    }

    /**
     * Initial state ready method.
     *
     */
    stateReady() {
        // Enable drag and drop.
        this.dragdrop = new DragDrop(this);

        // Close button.
        this.addEventListener(
            this.getElement(this.selectors.CLOSE),
            'click',
            this.remove
        );
        // Highlight button.
        if (this.controller.highlight) {
            this._toggleButtonText(this.getElement(this.selectors.HIGHLIGHT));
        }
        this.addEventListener(
            this.getElement(this.selectors.HIGHLIGHT),
            'click',
            () => {
                this.controller.highlight = !this.controller.highlight;
                this._toggleButtonText(this.getElement(this.selectors.HIGHLIGHT));
            }
        );
        // Edit mode button.
        this.addEventListener(
            this.getElement(this.selectors.READMODE),
            'click',
            this._toggleEditMode
        );
        // Clean log and state.
        this.addEventListener(
            this.getElement(this.selectors.CLEAN),
            'click',
            this._cleanAreas
        );
        // Unpin panel butotn.
        this.addEventListener(
            this.getElement(this.selectors.PIN),
            'click',
            this._togglePin
        );
        // Save button, state format error message and state textarea.
        this.getElement(this.selectors.SAVE).disabled = true;

        this.addEventListener(
            this.getElement(this.selectors.STATE),
            'keyup',
            debounce(this._checkJSON, 500)
        );

        this.addEventListener(
            this.getElement(this.selectors.SAVE),
            'click',
            this._saveState
        );
        // Save the default save warning message.
        this.strings.savewarning = this.getElement(this.selectors.INVALID)?.innerHTML ?? '';
        // Add current state.
        this._refreshState();
    }

    /**
     * Remove all subcomponents dependencies.
     */
    destroy() {
        if (this.dragdrop !== undefined) {
            this.dragdrop.unregister();
        }
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `reactives[${this.id}].lastChanges:updated`, handler: this._refreshLog},
            {watch: `reactives[${this.id}].modified:updated`, handler: this._refreshState},
            {watch: `reactives[${this.id}].readOnly:updated`, handler: this._refreshReadOnly},
        ];
    }

    /**
     * Wtacher method to refresh the log panel.
     *
     * @param {object} args
     * @param {HTMLElement} args.element
     */
    _refreshLog({element}) {
        const list = element?.lastChanges ?? [];

        const logContent = list.join("\n");
        // Append last log.
        const target = this.getElement(this.selectors.LOG);
        target.value += `\n\n= Transaction =\n ${logContent}`;
        target.scrollTop = target.scrollHeight;
    }

    /**
     * Listener method to clean the log area.
     */
    _cleanAreas() {
        let target = this.getElement(this.selectors.LOG);
        target.value = '';

        this._refreshState();
    }

    /**
     * Watcher to refresh the state information.
     */
    _refreshState() {
        const target = this.getElement(this.selectors.STATE);
        target.value = JSON.stringify(this.controller.state, null, 4);
    }

    /**
     * Watcher to update the read only information.
     */
    _refreshReadOnly() {
        // Toggle the read mode button.
        const target = this.getElement(this.selectors.READMODE);
        if (target.dataset.readonly === undefined) {
            target.dataset.readonly = target.innerHTML;
        }
        if (this.controller.readOnly) {
            target.innerHTML = target.dataset.readonly;
        } else {
            target.innerHTML = target.dataset.alt;
        }
    }

    /**
     * Listener to toggle the edit mode of the component.
     */
    _toggleEditMode() {
        this.controller.readOnly = !this.controller.readOnly;
    }

    /**
     * Check that the edited state JSON is valid.
     *
     * Not all valid JSON are suitable for transforming the state. For example,
     * the first level attributes cannot change the type.
     *
     * @return {undefined|array} Array of state updates.
     */
    _checkJSON() {
        const invalid = this.getElement(this.selectors.INVALID);
        const save = this.getElement(this.selectors.SAVE);

        const edited = this.getElement(this.selectors.STATE).value;

        const currentStateData = this.controller.stateData;

        // Check if the json is tha same as state.
        if (edited == JSON.stringify(this.controller.state, null, 4)) {
            invalid.style.color = '';
            invalid.innerHTML = '';
            save.disabled = true;
            return undefined;
        }

        // Check if the json format is valid.
        try {
            const newState = JSON.parse(edited);
            // Check the first level did not change types.
            const result = this._generateStateUpdates(currentStateData, newState);
            // Enable save button.
            invalid.style.color = '';
            invalid.innerHTML = this.strings.savewarning;
            save.disabled = false;
            return result;
        } catch (error) {
            invalid.style.color = 'red';
            invalid.innerHTML = error.message ?? 'Invalid JSON sctructure';
            save.disabled = true;
            return undefined;
        }
    }

    /**
     * Listener to save the current edited state into the real state.
     */
    _saveState() {
        const updates = this._checkJSON();
        if (!updates) {
            return;
        }
        // Sent the updates to the state manager.
        this.controller.processUpdates(updates);
    }

    /**
     * Check that the edited state JSON is valid.
     *
     * Not all valid JSON are suitable for transforming the state. For example,
     * the first level attributes cannot change the type. This method do a two
     * steps comparison between the current state data and the new state data.
     *
     * A reactive state cannot be overridden like any other variable. To keep
     * the watchers updated is necessary to transform the current state into
     * the new one. As a result, this method generates all the necessary state
     * updates to convert the state into the new state.
     *
     * @param {object} currentStateData
     * @param {object} newStateData
     * @return {array} Array of state updates.
     * @throws {Error} is the structure is not compatible
     */
    _generateStateUpdates(currentStateData, newStateData) {

        const updates = [];

        const ids = {};

        // Step 1: Add all overrides newStateData.
        for (const [key, newValue] of Object.entries(newStateData)) {
            // Check is it is new.
            if (Array.isArray(newValue)) {
                ids[key] = {};
                newValue.forEach(element => {
                    if (element.id === undefined) {
                        throw Error(`Array ${key} element without id attribute`);
                    }
                    updates.push({
                        name: key,
                        action: 'override',
                        fields: element,
                    });
                    const index = String(element.id).valueOf();
                    ids[key][index] = true;
                });
            } else {
                updates.push({
                    name: key,
                    action: 'override',
                    fields: newValue,
                });
            }
        }
        // Step 2: delete unnecesary data from currentStateData.
        for (const [key, oldValue] of Object.entries(currentStateData)) {
            let deleteField = false;
            // Check if the attribute is still there.
            if (newStateData[key] === undefined) {
                deleteField = true;
            }
            if (Array.isArray(oldValue)) {
                if (!deleteField && ids[key] === undefined) {
                    throw Error(`Array ${key} cannot change to object.`);
                }
                oldValue.forEach(element => {
                    const index = String(element.id).valueOf();
                    let deleteEntry = deleteField;
                    // Check if the id is there.
                    if (!deleteEntry && ids[key][index] === undefined) {
                        deleteEntry = true;
                    }
                    if (deleteEntry) {
                        updates.push({
                            name: key,
                            action: 'delete',
                            fields: element,
                        });
                    }
                });
            } else {
                if (!deleteField && ids[key] !== undefined) {
                    throw Error(`Object ${key} cannot change to array.`);
                }
                if (deleteField) {
                    updates.push({
                        name: key,
                        action: 'delete',
                        fields: oldValue,
                    });
                }
            }
        }
        // Delete all elements without action.
        return updates;
    }

    // Drag and drop methods.

    /**
     * Get the draggable data of this component.
     *
     * @returns {Object} exported course module drop data
     */
    getDraggableData() {
        return this.draggable;
    }

    /**
     * The element drop end hook.
     *
     * @param {Object} dropdata the dropdata
     * @param {Event} event the dropdata
     */
    dragEnd(dropdata, event) {
        this.element.style.top = `${event.newFixedTop}px`;
        this.element.style.left = `${event.newFixedLeft}px`;
    }

    /**
     * Pin and unpin the panel.
     */
    _togglePin() {
        this.draggable = !this.draggable;
        this.dragdrop.setDraggable(this.draggable);
        if (this.draggable) {
            this._unpin();
        } else {
            this._pin();
        }
    }

    /**
     * Unpin the panel form the footer.
     */
    _unpin() {
        // Find the initial spot.
        const pageCenterY = window.innerHeight / 2;
        const pageCenterX = window.innerWidth / 2;
        // Put the element in the middle of the screen
        const style = {
            position: 'fixed',
            resize: 'both',
            overflow: 'auto',
            height: '400px',
            width: '400px',
            top: `${pageCenterY - 200}px`,
            left: `${pageCenterX - 200}px`,
        };
        Object.assign(this.element.style, style);
        // Small also the text areas.
        this.getElement(this.selectors.STATE).style.height = '50px';
        this.getElement(this.selectors.LOG).style.height = '50px';

        this._toggleButtonText(this.getElement(this.selectors.PIN));
    }

    /**
     * Pin the panel into the footer.
     */
    _pin() {
        const props = [
            'position',
            'resize',
            'overflow',
            'top',
            'left',
            'height',
            'width',
        ];
        props.forEach(
            prop => this.element.style.removeProperty(prop)
        );
        this._toggleButtonText(this.getElement(this.selectors.PIN));
    }

    /**
     * Toogle the button text with the data-alt value.
     *
     * @param {Element} element the button element
     */
    _toggleButtonText(element) {
        [element.innerHTML, element.dataset.alt] = [element.dataset.alt, element.innerHTML];
    }

}
