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
 * @module     core_courseformat/local/courseeditor/fileuploader
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @typedef {Object} Handler
 * @property {String} extension the handled extension or * for any
 * @property {String} message the handler message
 * @property {String} module the module name
 */

import Config from 'core/config';
import ModalSaveCancel from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import {getFirst} from 'core/normalise';
import {prefetchStrings} from 'core/prefetch';
import {getString, getStrings} from 'core/str';
import {getCourseEditor} from 'core_courseformat/courseeditor';
import {processMonitor} from 'core/process_monitor';
import {debounce} from 'core/utils';

// Uploading url.
const UPLOADURL = Config.wwwroot + '/course/dndupload.php';
const DEBOUNCETIMER = 500;
const USERCANIGNOREFILESIZELIMITS = -1;

/** @var {ProcessQueue} uploadQueue the internal uploadQueue instance.  */
let uploadQueue = null;
/** @var {Object} handlerManagers the courseId indexed loaded handler managers. */
let handlerManagers = {};
/** @var {Map} courseUpdates the pending course sections updates. */
let courseUpdates = new Map();
/** @var {Object} errors the error messages. */
let errors = null;

// Load global strings.
prefetchStrings('moodle', ['addresourceoractivity', 'upload']);
prefetchStrings('core_error', ['dndmaxbytes', 'dndread', 'dndupload', 'dndunkownfile']);

/**
 * Class to upload a file into the course.
 * @private
 */
class FileUploader {
    /**
     * Class constructor.
     *
     * @param {number} courseId the course id
     * @param {number} sectionId the section id
     * @param {number} sectionNum the section number
     * @param {File} fileInfo the file information object
     * @param {Handler} handler the file selected file handler
     */
    constructor(courseId, sectionId, sectionNum, fileInfo, handler) {
        this.courseId = courseId;
        this.sectionId = sectionId;
        this.sectionNum = sectionNum;
        this.fileInfo = fileInfo;
        this.handler = handler;
    }

    /**
     * Execute the file upload and update the state in the given process.
     *
     * @param {LoadingProcess} process the process to store the upload result
     */
    execute(process) {
        const fileInfo = this.fileInfo;
        const xhr = this._createXhrRequest(process);
        const formData = this._createUploadFormData();

        // Try reading the file to check it is not a folder, before sending it to the server.
        const reader = new FileReader();
        reader.onload = function() {
            // File was read OK - send it to the server.
            xhr.open("POST", UPLOADURL, true);
            xhr.send(formData);
        };
        reader.onerror = function() {
            // Unable to read the file (it is probably a folder) - display an error message.
            process.setError(errors.dndread);
        };
        if (fileInfo.size > 0) {
            // If this is a non-empty file, try reading the first few bytes.
            // This will trigger reader.onerror() for folders and reader.onload() for ordinary, readable files.
            reader.readAsText(fileInfo.slice(0, 5));
        } else {
            // If you call slice() on a 0-byte folder, before calling readAsText, then Firefox triggers reader.onload(),
            // instead of reader.onerror().
            // So, for 0-byte files, just call readAsText on the whole file (and it will trigger load/error functions as expected).
            reader.readAsText(fileInfo);
        }
    }

    /**
     * Returns the bind version of execute function.
     *
     * This method is used to queue the process into a ProcessQueue instance.
     *
     * @returns {Function} the bind function to execute the process
     */
    getExecutionFunction() {
        return this.execute.bind(this);
    }

    /**
     * Generate a upload XHR file request.
     *
     * @param {LoadingProcess} process the current process
     * @return {XMLHttpRequest} the XHR request
     */
    _createXhrRequest(process) {
        const xhr = new XMLHttpRequest();
        // Update the progress bar as the file is uploaded.
        xhr.upload.addEventListener(
            'progress',
            (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded * 100) / event.total);
                    process.setPercentage(percent);
                }
            },
            false
        );
        // Wait for the AJAX call to complete.
        xhr.onreadystatechange = () => {
            if (xhr.readyState == 1) {
                // Add a 1% just to indicate that it is uploading.
                process.setPercentage(1);
            }
            // State 4 is DONE. Otherwise the connection is still ongoing.
            if (xhr.readyState != 4) {
                return;
            }
            if (xhr.status == 200) {
                var result = JSON.parse(xhr.responseText);
                if (result && result.error == 0) {
                    // All OK.
                    this._finishProcess(process);
                } else {
                    process.setError(result.error);
                }
            } else {
                process.setError(errors.dndupload);
            }
        };
        return xhr;
    }

    /**
     * Upload a file into the course.
     *
     * @return {FormData|null} the new form data object
     */
    _createUploadFormData() {
        const formData = new FormData();
        try {
            formData.append('repo_upload_file', this.fileInfo);
        } catch (error) {
            throw Error(error.dndread);
        }
        formData.append('sesskey', Config.sesskey);
        formData.append('course', this.courseId);
        formData.append('section', this.sectionNum);
        formData.append('module', this.handler.module);
        formData.append('type', 'Files');
        return formData;
    }

    /**
     * Finishes the current process.
     * @param {LoadingProcess} process the process
     */
    _finishProcess(process) {
        addRefreshSection(this.courseId, this.sectionId);
        process.setPercentage(100);
        process.finish();
    }
}

/**
 * The file handler manager class.
 *
 * @private
 */
class HandlerManager {

    /** @var {Object} lastHandlers the last handlers selected per each file extension. */
    lastHandlers = {};

    /** @var {Handler[]|null} allHandlers all the available handlers. */
    allHandlers = null;

    /**
     * Class constructor.
     *
     * @param {Number} courseId
     */
    constructor(courseId) {
        this.courseId = courseId;
        this.lastUploadId = 0;
        this.courseEditor = getCourseEditor(courseId);
        if (!this.courseEditor) {
            throw Error('Unkown course editor');
        }
        this.maxbytes = this.courseEditor.get('course')?.maxbytes ?? 0;
    }

    /**
     * Load the course file handlers.
     */
    async loadHandlers() {
        this.allHandlers = await this.courseEditor.getFileHandlersPromise();
    }

    /**
     * Extract the file extension from a fileInfo.
     *
     * @param {File} fileInfo
     * @returns {String} the file extension or an empty string.
     */
    getFileExtension(fileInfo) {
        let extension = '';
        const dotpos = fileInfo.name.lastIndexOf('.');
        if (dotpos != -1) {
            extension = fileInfo.name.substring(dotpos + 1, fileInfo.name.length).toLowerCase();
        }
        return extension;
    }

    /**
     * Check if the file is valid.
     *
     * @param {File} fileInfo the file info
     */
    validateFile(fileInfo) {
        if (this.maxbytes !== USERCANIGNOREFILESIZELIMITS && fileInfo.size > this.maxbytes) {
            throw Error(errors.dndmaxbytes);
        }
    }

    /**
     * Get the file handlers of an specific file.
     *
     * @param {File} fileInfo the file indo
     * @return {Array} Array of handlers
     */
    filterHandlers(fileInfo) {
        const extension = this.getFileExtension(fileInfo);
        return this.allHandlers.filter(handler => handler.extension == '*' || handler.extension == extension);
    }

    /**
     * Get the Handler to upload a specific file.
     *
     * It will ask the used if more than one handler is available.
     *
     * @param {File} fileInfo the file info
     * @returns {Promise<Handler|null>} the selected handler or null if the user cancel
     */
    async getFileHandler(fileInfo) {
        const fileHandlers = this.filterHandlers(fileInfo);
        if (fileHandlers.length == 0) {
            throw Error(errors.dndunkownfile);
        }
        let fileHandler = null;
        if (fileHandlers.length == 1) {
            fileHandler = fileHandlers[0];
        } else {
            fileHandler = await this.askHandlerToUser(fileHandlers, fileInfo);
        }
        return fileHandler;
    }

    /**
     * Ask the user to select a specific handler.
     *
     * @param {Handler[]} fileHandlers
     * @param {File} fileInfo the file info
     * @return {Promise<Handler>} the selected handler
     */
    async askHandlerToUser(fileHandlers, fileInfo) {
        const extension = this.getFileExtension(fileInfo);
        // Build the modal parameters from the event data.
        const modalParams = {
            title: getString('addresourceoractivity', 'moodle'),
            body: Templates.render(
                'core_courseformat/fileuploader',
                this.getModalData(
                    fileHandlers,
                    fileInfo,
                    this.lastHandlers[extension] ?? null
                )
            ),
            saveButtonText: getString('upload', 'moodle'),
        };
        // Create the modal.
        const modal = await this.modalBodyRenderedPromise(modalParams);
        const selectedHandler = await this.modalUserAnswerPromise(modal, fileHandlers);
        // Cancel action.
        if (selectedHandler === null) {
            return null;
        }
        // Save last selected handler.
        this.lastHandlers[extension] = selectedHandler.module;
        return selectedHandler;
    }

    /**
     * Generated the modal template data.
     *
     * @param {Handler[]} fileHandlers
     * @param {File} fileInfo the file info
     * @param {String|null} defaultModule the default module if any
     * @return {Object} the modal template data.
     */
    getModalData(fileHandlers, fileInfo, defaultModule) {
        const data = {
            filename: fileInfo.name,
            uploadid: ++this.lastUploadId,
            handlers: [],
        };
        let hasDefault = false;
        fileHandlers.forEach((handler, index) => {
            const isDefault = (defaultModule == handler.module);
            data.handlers.push({
                ...handler,
                selected: isDefault,
                labelid: `fileuploader_${data.uploadid}`,
                value: index,
            });
            hasDefault = hasDefault || isDefault;
        });
        if (!hasDefault && data.handlers.length > 0) {
            const lastHandler = data.handlers.pop();
            lastHandler.selected = true;
            data.handlers.push(lastHandler);
        }
        return data;
    }

    /**
     * Get the user handler choice.
     *
     * Wait for the user answer in the modal and resolve with the selected index.
     *
     * @param {Modal} modal the modal instance
     * @param {Handler[]} fileHandlers the availabvle file handlers
     * @return {Promise} with the option selected by the user.
     */
    modalUserAnswerPromise(modal, fileHandlers) {
        const modalBody = getFirst(modal.getBody());
        return new Promise((resolve, reject) => {
            modal.getRoot().on(
                ModalEvents.save,
                event => {
                    // Get the selected option.
                    const index = modalBody.querySelector('input:checked').value;
                    event.preventDefault();
                    modal.destroy();
                    if (!fileHandlers[index]) {
                        reject('Invalid handler selected');
                    }
                    resolve(fileHandlers[index]);

                }
            );
            modal.getRoot().on(
                ModalEvents.cancel,
                () => {
                    resolve(null);
                }
            );
        });
    }

    /**
     * Create a new modal and return a Promise to the body rendered.
     *
     * @param {Object} modalParams the modal params
     * @returns {Promise} the modal body rendered promise
     */
    modalBodyRenderedPromise(modalParams) {
        return new Promise((resolve, reject) => {
            ModalSaveCancel.create(modalParams).then((modal) => {
                modal.setRemoveOnClose(true);
                // Handle body loading event.
                modal.getRoot().on(ModalEvents.bodyRendered, () => {
                    resolve(modal);
                });
                // Configure some extra modal params.
                if (modalParams.saveButtonText !== undefined) {
                    modal.setSaveButtonText(modalParams.saveButtonText);
                }
                modal.show();
                return;
            }).catch(() => {
                reject(`Cannot load modal content`);
            });
        });
    }
}

/**
 * Add a section to refresh.
 *
 * @param {number} courseId the course id
 * @param {number} sectionId the seciton id
 */
function addRefreshSection(courseId, sectionId) {
    let refresh = courseUpdates.get(courseId);
    if (!refresh) {
        refresh = new Set();
    }
    refresh.add(sectionId);
    courseUpdates.set(courseId, refresh);
    refreshCourseEditors();
}

/**
 * Debounced processing all pending course refreshes.
 * @private
 */
const refreshCourseEditors = debounce(
    () => {
        const refreshes = courseUpdates;
        courseUpdates = new Map();
        refreshes.forEach((sectionIds, courseId) => {
            const courseEditor = getCourseEditor(courseId);
            if (!courseEditor) {
                return;
            }
            courseEditor.dispatch('sectionState', [...sectionIds]);
        });
    },
    DEBOUNCETIMER
);

/**
 * Load and return the course handler manager instance.
 *
 * @param {Number} courseId the course Id to load
 * @returns {Promise<HandlerManager>} promise of the the loaded handleManager
 */
async function loadCourseHandlerManager(courseId) {
    if (handlerManagers[courseId] !== undefined) {
        return handlerManagers[courseId];
    }
    const handlerManager = new HandlerManager(courseId);
    await handlerManager.loadHandlers();
    handlerManagers[courseId] = handlerManager;
    return handlerManagers[courseId];
}

/**
 * Load all the erros messages at once in the module "errors" variable.
 * @param {Number} courseId the course id
 */
async function loadErrorStrings(courseId) {
    if (errors !== null) {
        return;
    }
    const courseEditor = getCourseEditor(courseId);
    const maxbytestext = courseEditor.get('course')?.maxbytestext ?? '0';

    errors = {};
    const allStrings = [
        {key: 'dndmaxbytes', component: 'core_error', param: {size: maxbytestext}},
        {key: 'dndread', component: 'core_error'},
        {key: 'dndupload', component: 'core_error'},
        {key: 'dndunkownfile', component: 'core_error'},
    ];
    window.console.log(allStrings);
    const loadedStrings = await getStrings(allStrings);
    allStrings.forEach(({key}, index) => {
        errors[key] = loadedStrings[index];
    });
}

/**
 * Start a batch file uploading into the course.
 *
 * @private
 * @param {number} courseId the course id.
 * @param {number} sectionId the section id.
 * @param {number} sectionNum the section number.
 * @param {File} fileInfo the file information object
 * @param {HandlerManager} handlerManager the course handler manager
 */
const queueFileUpload = async function(courseId, sectionId, sectionNum, fileInfo, handlerManager) {
    let handler;
    uploadQueue = await processMonitor.createProcessQueue();
    try {
        handlerManager.validateFile(fileInfo);
        handler = await handlerManager.getFileHandler(fileInfo);
    } catch (error) {
        uploadQueue.addError(fileInfo.name, error.message);
        return;
    }
    // If we don't have a handler means the user cancel the upload.
    if (!handler) {
        return;
    }
    const fileProcessor = new FileUploader(courseId, sectionId, sectionNum, fileInfo, handler);
    uploadQueue.addPending(fileInfo.name, fileProcessor.getExecutionFunction());
};

/**
 * Upload a file to the course.
 *
 * This method will show any necesary modal to handle the request.
 *
 * @param {number} courseId the course id
 * @param {number} sectionId the section id
 * @param {number} sectionNum the section number
 * @param {Array} files and array of files
 */
export const uploadFilesToCourse = async function(courseId, sectionId, sectionNum, files) {
    // Get the course handlers.
    const handlerManager = await loadCourseHandlerManager(courseId);
    await loadErrorStrings(courseId);
    for (let index = 0; index < files.length; index++) {
        const fileInfo = files[index];
        await queueFileUpload(courseId, sectionId, sectionNum, fileInfo, handlerManager);
    }
};
