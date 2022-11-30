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

import {debounce} from 'core/utils';
import {LoadingProcess} from 'core/local/process_monitor/loadingprocess';
import log from 'core/log';

const TOASTSTIMER = 3000;

/**
 * A process queue manager.
 *
 * Adding process to the queue will guarante process are executed in sequence.
 *
 * @module     core/local/process_monitor/processqueue
 * @class      ProcessQueue
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export class ProcessQueue {
    /** @var {Array} pending the pending queue. */
    pending = [];

    /** @var {LoadingProcess} current the current uploading process. */
    currentProcess = null;

    /**
     * Class constructor.
     * @param {ProcessMonitorManager} manager the monitor manager
     */
    constructor(manager) {
        this.manager = manager;
        this.cleanFinishedProcesses = debounce(
            () => manager.dispatch('cleanFinishedProcesses'),
            TOASTSTIMER
        );
    }

    /**
     * Adds a new pending upload to the queue.
     * @param {String} processName the process name
     * @param {Function} processor the execution function
     */
    addPending(processName, processor) {
        const process = new LoadingProcess(this.manager, {name: processName});
        process.setExtraData({
            processor,
        });
        process.onFinish((uploadedFile) => {
            if (this.currentProcess?.id !== uploadedFile.id) {
                return;
            }
            this._discardCurrent();
        });
        this.pending.push(process);
        this._continueProcessing();
    }

    /**
     * Adds a new pending upload to the queue.
     * @param {String} processName the file info
     * @param {String} errorMessage the file processor
     */
    addError(processName, errorMessage) {
        const process = new LoadingProcess(this.manager, {name: processName});
        process.setError(errorMessage);
    }

    /**
     * Discard the current process and execute the next one if any.
     */
    _discardCurrent() {
        if (this.currentProcess) {
            this.currentProcess = null;
        }
        this.cleanFinishedProcesses();
        this._continueProcessing();
    }

    /**
     * Return the current file uploader.
     * @return {FileUploader}
     */
    _currentProcessor() {
        return this.currentProcess.data.processor;
    }

    /**
     * Continue the queue processing if no current process is defined.
     */
    async _continueProcessing() {
        if (this.currentProcess !== null || this.pending.length === 0) {
            return;
        }
        this.currentProcess = this.pending.shift();
        try {
            const processor = this._currentProcessor();
            await processor(this.currentProcess);
        } catch (error) {
            this.currentProcess.setError(error.message);
            log.error(error);
        }
    }
}
