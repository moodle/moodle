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
 * The process wrapper class.
 *
 * This module is used to update a process in the process monitor.
 *
 * @module     core/local/process_monitor/loadingprocess
 * @class      LoadingProcess
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import log from 'core/log';

export class LoadingProcess {

    /** @var {Map} editorUpdates the courses pending to be updated. */
    processData = null;

    /** @var {Object} extraData any extra process information to store. */
    extraData = null;

    /** @var {ProcessMonitorManager} manager the page monitor. */
    manager = null;

    /** @var {Function} finishedCallback the finished callback if any. */
    finishedCallback = null;

    /** @var {Function} removedCallback the removed callback if any. */
    removedCallback = null;

    /** @var {Function} errorCallback the error callback if any. */
    errorCallback = null;

    /**
     * Class constructor
     * @param {ProcessMonitorManager} manager the monitor manager
     * @param {Object} definition the process definition data
     */
    constructor(manager, definition) {
        this.manager = manager;
        // Add defaults.
        this.processData = {
            id: manager.generateProcessId(),
            name: '',
            percentage: 0,
            url: null,
            error: null,
            finished: false,
            ...definition,
        };
        // Create a new entry.
        this._dispatch('addProcess', this.processData);
    }

    /**
     * Execute a monitor manager mutation when the state is ready.
     *
     * @private
     * @param {String} action the mutation to dispatch
     * @param {*} params the mutaiton params
     */
    _dispatch(action, params) {
        this.manager.getInitialStatePromise().then(() => {
            this.manager.dispatch(action, params);
            return;
        }).catch(() => {
            log.error(`Cannot update process monitor.`);
        });
    }

    /**
     * Define a finished process callback function.
     * @param {Function} callback the callback function
     */
    onFinish(callback) {
        this.finishedCallback = callback;
    }

    /**
     * Define a removed from monitor process callback function.
     * @param {Function} callback the callback function
     */
    onRemove(callback) {
        this.removedCallback = callback;
    }

    /**
     * Define a error process callback function.
     * @param {Function} callback the callback function
     */
    onError(callback) {
        this.errorCallback = callback;
    }

    /**
     * Set the process percentage.
     * @param {Number} percentage
     */
    setPercentage(percentage) {
        this.processData.percentage = percentage;
        this._dispatch('updateProcess', this.processData);
    }

    /**
     * Stores extra information to the process.
     *
     * This method is used to add information like the course, the user
     * or any other needed information.
     *
     * @param {Object} extraData any extra process information to store
     */
    setExtraData(extraData) {
        this.extraData = extraData;
    }

    /**
     * Set the process error string.
     *
     * Note: set the error message will mark the process as finished.
     *
     * @param {String} error the string message
     */
    setError(error) {
        this.processData.error = error;
        if (this.errorCallback !== null) {
            this.errorCallback(this);
        }
        this.processData.finished = true;
        if (this.finishedCallback !== null) {
            this.finishedCallback(this);
        }
        this._dispatch('updateProcess', this.processData);
    }

    /**
     * Rename the process
     * @param {String} name the new process name
     */
    setName(name) {
        this.processData.name = name;
        this._dispatch('updateProcess', this.processData);
    }

    /**
     * Mark the process as finished.
     */
    finish() {
        this.processData.finished = true;
        if (this.finishedCallback !== null) {
            this.finishedCallback(this);
        }
        this._dispatch('updateProcess', this.processData);
    }

    /**
     * Remove the process from the monitor.
     */
    remove() {
        if (this.removedCallback !== null) {
            this.removedCallback(this);
        }
        this._dispatch('removeProcess', this.processData.id);
    }

    /**
     * Returns the current rpocess data.
     * @returns {Object} the process data
     */
    getData() {
        return {...this.processData};
    }

    /**
     * Return the process name
     * @return {String}
     */
    get name() {
        return this.processData.name;
    }

    /**
     * Return the process internal id
     * @return {Number}
     */
    get id() {
        return this.processData.id;
    }

    /**
     * Return the process extra data.
     * @return {*} whatever is in extra data
     */
    get data() {
        return this.extraData;
    }
}
