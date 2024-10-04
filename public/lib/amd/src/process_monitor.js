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
 * Process monitor includer.
 *
 * @module     core/process_monitor
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import log from 'core/log';
import {manager} from 'core/local/process_monitor/manager';
import {LoadingProcess} from 'core/local/process_monitor/loadingprocess';
import {ProcessQueue} from 'core/local/process_monitor/processqueue';
import Templates from 'core/templates';

let initialized = false;

/**
 * Get the parent container.
 * @private
 * @return {HTMLelement} the process monitor container.
 */
const getParentContainer = () => {
    // The footer pop over depends on the theme.
    return document.querySelector(`#page`) ?? document.body;
};

export const processMonitor = {
    /**
     * Adds a new process to the monitor.
     * @param {Object} definition the process definition
     * @param {String} definition.name the process name
     * @param {Number} definition.percentage the current percentage (0 - 100)
     * @param {String} definition.error the error message if any
     * @param {String} definition.url possible link url if any
     * @returns {LoadingProcess} the loading process
     */
    addLoadingProcess: function(definition) {
        this.initProcessMonitor();
        const process = new LoadingProcess(manager, definition);
        return process;
    },

    /**
     * Remove all processes form the current monitor.
     */
    removeAllProcesses: function() {
        manager.getInitialStatePromise().then(() => {
            manager.dispatch('removeAllProcesses');
            return;
        }).catch(() => {
            log.error(`Cannot update process monitor.`);
        });
    },

    /**
     * Initialize the process monitor.
     */
    initProcessMonitor: async function() {
        if (initialized) {
            return;
        }
        initialized = true;
        const container = getParentContainer();
        if (document.getElementById(`#processMonitor`)) {
            return;
        }
        try {
            const {html, js} = await Templates.renderForPromise('core/local/process_monitor/monitor', {});
            Templates.appendNodeContents(container, html, js);
        } catch (error) {
            log.error(`Cannot load the process monitor`);
        }
    },

    /**
     * Return the process monitor initial state promise.
     * @returns {Promise} Promise of the initial state fully loaded
     */
    getInitialStatePromise: function() {
        return manager.getInitialStatePromise();
    },

    /**
     * Load the load queue monitor.
     *
     * @return {Promise<ProcessQueue>} when the file uploader is ready to be used.
     */
    createProcessQueue: async function() {
        processMonitor.initProcessMonitor();
        const processQueue = new ProcessQueue(manager);
        await processMonitor.getInitialStatePromise();
        return processQueue;
    }
};
