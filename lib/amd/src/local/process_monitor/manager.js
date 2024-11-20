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
 * The course file uploader.
 *
 * This module is used to upload files directly into the course.
 *
 * @module     core/local/process_monitor/manager
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Reactive} from 'core/reactive';
import {eventTypes, dispatchStateChangedEvent} from 'core/local/process_monitor/events';

const initialState = {
    display: {
        show: false,
    },
    queue: [],
};

/**
 * The reactive file uploader class.
 *
 * As all the upload queues are reactive, any plugin can implement its own upload monitor.
 *
 * @module     core/local/process_monitor/manager
 * @class      ProcessMonitorManager
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ProcessMonitorManager extends Reactive {
    /**
     * The next process id to use.
     *
     * @attribute nextId
     * @type number
     * @default 1
     * @package
     */
    nextId = 1;

    /**
     * Generate a unique process id.
     * @return {number} a generated process Id
     */
    generateProcessId() {
        return this.nextId++;
    }
}

/**
 * @var {Object} mutations the monitor mutations.
 */
const mutations = {
    /**
     * Add a new process to the queue.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {Object} processData the upload id to finish
     */
    addProcess: function(stateManager, processData) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.queue.add({...processData});
        state.display.show = true;
        stateManager.setReadOnly(true);
    },

    /**
     * Remove a process from the queue.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {Number} processId the process id
     */
    removeProcess: function(stateManager, processId) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.queue.delete(processId);
        if (state.queue.size === 0) {
            state.display.show = false;
        }
        stateManager.setReadOnly(true);
    },

    /**
     * Update a process process to the queue.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {Object} processData the upload id to finish
     * @param {Number} processData.id the process id
     */
    updateProcess: function(stateManager, processData) {
        if (processData.id === undefined) {
            throw Error(`Missing process ID in process data`);
        }
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        const queueItem = state.queue.get(processData.id);
        if (!queueItem) {
            throw Error(`Unkown process with id ${processData.id}`);
        }
        for (const [prop, propValue] of Object.entries(processData)) {
            queueItem[prop] = propValue;
        }
        stateManager.setReadOnly(true);
    },

    /**
     * Set the monitor show attribute.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {Boolean} show the show value
     */
    setShow: function(stateManager, show) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.display.show = show;
        if (!show) {
            this.cleanFinishedProcesses(stateManager);
        }
        stateManager.setReadOnly(true);
    },

    /**
     * Remove a processes from the queue.
     *
     * @param {StateManager} stateManager the current state manager
     */
    removeAllProcesses: function(stateManager) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.queue.forEach((element) => {
            state.queue.delete(element.id);
        });
        state.display.show = false;
        stateManager.setReadOnly(true);
    },

    /**
     * Clean all finished processes.
     *
     * @param {StateManager} stateManager the current state manager
     */
    cleanFinishedProcesses: function(stateManager) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.queue.forEach((element) => {
            if (element.finished && !element.error) {
                state.queue.delete(element.id);
            }
        });
        if (state.queue.size === 0) {
            state.display.show = false;
        }
        stateManager.setReadOnly(true);
    },
};

const manager = new ProcessMonitorManager({
    name: `ProcessMonitor`,
    eventName: eventTypes.processMonitorStateChange,
    eventDispatch: dispatchStateChangedEvent,
    mutations: mutations,
    state: initialState,
});

export {manager};
