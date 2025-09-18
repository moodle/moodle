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

import Config from 'core/config';
import {getString} from 'core/str';
import {Reactive} from 'core/reactive';
import notification from 'core/notification';
import Exporter from 'core_courseformat/local/courseeditor/exporter';
import log from 'core/log';
import ajax from 'core/ajax';
import * as Storage from 'core/sessionstorage';
import {uploadFilesToCourse} from 'core_courseformat/local/courseeditor/fileuploader';

/**
 * Main course editor module.
 *
 * All formats can register new components on this object to create new reactive
 * UI components that watch the current course state.
 *
 * @module     core_courseformat/local/courseeditor/courseeditor
 * @class     core_courseformat/local/courseeditor/courseeditor
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class extends Reactive {

    /**
     * The current state cache key
     *
     * The state cache is considered dirty if the state changes from the last page or
     * if the page has editing mode on.
     *
     * @attribute stateKey
     * @type number|null
     * @default 1
     * @package
     */
    stateKey = 1;

    /**
     * The section number of the current page
     * @attribute sectionReturn
     * @type number
     * @default null
     */
    sectionReturn = null;

    /**
     * The section ID of the current page
     * @attribute pageSectionId
     * @type number
     * @default null
     */
    pageSectionId = null;

    /**
     * Set up the course editor when the page is ready.
     *
     * The course can only be loaded once per instance. Otherwise an error is thrown.
     *
     * The backend can inform the module of the current state key. This key changes every time some
     * update in the course affect the current user state. Some examples are:
     *  - The course content has been edited
     *  - The user marks some activity as completed
     *  - The user collapses or uncollapses a section (it is stored as a user preference)
     *
     * @param {number} courseId course id
     * @param {string} serverStateKey the current backend course cache reference
     */
    async loadCourse(courseId, serverStateKey) {

        if (this.courseId) {
            throw new Error(`Cannot load ${courseId}, course already loaded with id ${this.courseId}`);
        }

        if (!serverStateKey) {
            // The server state key is not provided, we use a invalid statekey to force reloading.
            serverStateKey = `invalidStateKey_${Date.now()}`;
        }

        // Default view format setup.
        this._editing = false;
        this._supportscomponents = false;
        this._fileHandlers = null;

        this.courseId = courseId;

        let stateData;

        const storeStateKey = Storage.get(`course/${courseId}/stateKey`);
        try {
            // Check if the backend state key is the same we have in our session storage.
            if (!this.isEditing && serverStateKey == storeStateKey) {
                stateData = JSON.parse(Storage.get(`course/${courseId}/staticState`));
            }
            if (!stateData) {
                stateData = await this.getServerCourseState();
            }

        } catch (error) {
            log.error("EXCEPTION RAISED WHILE INIT COURSE EDITOR");
            log.error(error);
            return;
        }

        // The bulk editing only applies to the frontend and the state data is not created in the backend.
        stateData.bulk = {
            enabled: false,
            selectedType: '',
            selection: [],
        };

        this.setInitialState(stateData);

        // In editing mode, the session cache is considered dirty always.
        if (this.isEditing) {
            this.stateKey = null;
        } else {
            // Check if the last state is the same as the cached one.
            const newState = JSON.stringify(stateData);
            const previousState = Storage.get(`course/${courseId}/staticState`);
            if (previousState !== newState || storeStateKey !== serverStateKey) {
                Storage.set(`course/${courseId}/staticState`, newState);
                Storage.set(`course/${courseId}/stateKey`, stateData?.course?.statekey ?? serverStateKey);
            }
            this.stateKey = Storage.get(`course/${courseId}/stateKey`);
        }

        this._loadFileHandlers();

        this._pageAnchorCmInfo = this._scanPageAnchorCmInfo();
    }

    /**
     * Load the file hanlders promise.
     */
    _loadFileHandlers() {
        // Load the course file extensions.
        this._fileHandlersPromise = new Promise((resolve) => {
            if (!this.isEditing) {
                resolve([]);
                return;
            }
            // Check the cache.
            const handlersCacheKey = `course/${this.courseId}/fileHandlers`;

            const cacheValue = Storage.get(handlersCacheKey);
            if (cacheValue) {
                try {
                    const cachedHandlers = JSON.parse(cacheValue);
                    resolve(cachedHandlers);
                    return;
                } catch (error) {
                    log.error("ERROR PARSING CACHED FILE HANDLERS");
                }
            }
            // Call file handlers webservice.
            ajax.call([{
                methodname: 'core_courseformat_file_handlers',
                args: {
                    courseid: this.courseId,
                }
            }])[0].then((handlers) => {
                Storage.set(handlersCacheKey, JSON.stringify(handlers));
                resolve(handlers);
                return;
            }).catch(error => {
                log.error(error);
                resolve([]);
                return;
            });
        });
    }

    /**
     * Setup the current view settings
     *
     * @param {Object} setup format, page and course settings
     * @param {boolean} setup.editing if the page is in edit mode
     * @param {boolean} setup.supportscomponents if the format supports components for content
     * @param {string} setup.cacherev the backend cached state revision
     * @param {Array} setup.overriddenStrings optional overridden strings
     */
    setViewFormat(setup) {
        this._editing = setup.editing ?? false;
        this._supportscomponents = setup.supportscomponents ?? false;
        const overriddenStrings = setup.overriddenStrings ?? [];
        this._overriddenStrings = overriddenStrings.reduce(
            (indexed, currentValue) => indexed.set(currentValue.key, currentValue),
            new Map()
        );
    }

    /**
     * Execute a get string for a possible format overriden editor string.
     *
     * Return the proper getString promise for an editor string using the core_courseformat
     * of the format_PLUGINNAME compoment depending on the current view format setup.
     * @param {String} key the string key
     * @param {string|undefined} param The param for variable expansion in the string.
     * @returns {Promise<String>} a getString promise
     */
    getFormatString(key, param) {
        if (this._overriddenStrings.has(key)) {
            const override = this._overriddenStrings.get(key);
            return getString(key, override.component ?? 'core_courseformat', param);
        }
        // All format overridable strings are from core_courseformat lang file.
        return getString(key, 'core_courseformat', param);
    }

    /**
     * Load the current course state from the server.
     *
     * @returns {Object} the current course state
     */
    async getServerCourseState() {
        // Only logged users can get the course state. Filtering here will prevent unnecessary
        // calls to the server and login page redirects. Especially for home activities with
        // guest access.
        if (Config.userId == 0) {
            return {
                course: {},
                section: [],
                cm: [],
            };
        }
        const courseState = await ajax.call([{
            methodname: 'core_courseformat_get_state',
            args: {
                courseid: this.courseId,
            }
        }])[0];

        const stateData = JSON.parse(courseState);

        return {
            course: {},
            section: [],
            cm: [],
            ...stateData,
        };
    }

    /**
     * Return the current edit mode.
     *
     * Components should use this method to check if edit mode is active.
     *
     * @return {boolean} if edit is enabled
     */
    get isEditing() {
        return this._editing ?? false;
    }

    /**
     * Return a data exporter to transform state part into mustache contexts.
     *
     * @return {Exporter} the exporter class
     */
    getExporter() {
        return new Exporter(this);
    }

    /**
     * Return if the current course support components to refresh the content.
     *
     * @returns {boolean} if the current content support components
     */
    get supportComponents() {
        return this._supportscomponents ?? false;
    }

    /**
     * Return the course file handlers promise.
     * @returns {Promise} the promise for file handlers.
     */
    async getFileHandlersPromise() {
        return this._fileHandlersPromise ?? [];
    }

    /**
     * Upload a file list to the course.
     *
     * This method is a wrapper to the course file uploader.
     *
     * @param {number} sectionId the section id
     * @param {number} sectionNum the section number
     * @param {Array} files and array of files
     * @return {Promise} the file queue promise
     */
    uploadFiles(sectionId, sectionNum, files) {
        return uploadFilesToCourse(this.courseId, sectionId, sectionNum, files);
    }

    /**
     * Get a value from the course editor static storage if any.
     *
     * The course editor static storage uses the sessionStorage to store values from the
     * components. This is used to prevent unnecesary template loadings on every page. However,
     * the storage does not work if no sessionStorage can be used (in debug mode for example),
     * if the page is in editing mode or if the initial state change from the last page.
     *
     * @param {string} key the key to get
     * @return {boolean|string} the storage value or false if cannot be loaded
     */
    getStorageValue(key) {
        if (this.isEditing || !this.stateKey) {
            return false;
        }
        const dataJson = Storage.get(`course/${this.courseId}/${key}`);
        if (!dataJson) {
            return false;
        }
        // Check the stateKey.
        try {
            const data = JSON.parse(dataJson);
            if (data?.stateKey !== this.stateKey) {
                return false;
            }
            return data.value;
        } catch (error) {
            return false;
        }
    }

    /**
     * Stores a value into the course editor static storage if available
     *
     * @param {String} key the key to store
     * @param {*} value the value to store (must be compatible with JSON,stringify)
     * @returns {boolean} true if the value is stored
     */
    setStorageValue(key, value) {
        // Values cannot be stored on edit mode.
        if (this.isEditing) {
            return false;
        }
        const data = {
            stateKey: this.stateKey,
            value,
        };
        return Storage.set(`course/${this.courseId}/${key}`, JSON.stringify(data));
    }

    /**
     * Convert a file dragging event into a proper dragging file list.
     * @param {DataTransfer} dataTransfer the event to convert
     * @return {Array} of file list info.
     */
    getFilesDraggableData(dataTransfer) {
        const exporter = this.getExporter();
        return exporter.fileDraggableData(this.state, dataTransfer);
    }

    /**
     * Dispatch a change in the state.
     *
     * Usually reactive modules throw an error directly to the components when something
     * goes wrong. However, course editor can directly display a notification.
     *
     * @method dispatch
     * @param {mixed} args any number of params the mutation needs.
     */
    async dispatch(...args) {
        try {
            await super.dispatch(...args);
        } catch (error) {
            // Display error modal.
            notification.exception(error);
            // Force unlock all elements.
            super.dispatch('unlockAll');
        }
    }

    /**
     * Calculate the cm info from the current page anchor.
     *
     * @returns {Object|null} the cm info or null if not found.
     */
    _scanPageAnchorCmInfo() {
        const anchor = new URL(window.location.href).hash;
        if (!anchor.startsWith('#module-')) {
            return null;
        }
        // The anchor is always #module-CMID.
        const cmid = anchor.split('-')[1];
        return this.stateManager.get('cm', parseInt(cmid));
    }

    /**
     * Return the current page anchor cm info.
     */
    getPageAnchorCmInfo() {
        return this._pageAnchorCmInfo;
    }
}
