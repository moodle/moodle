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
 * Upload module for Google Docs repository.
 *
 * @module     repository_googledocs/upload
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {subscribe} from 'core/pubsub';
import SaveCancelModal from 'core/modal_save_cancel';
import {getString} from 'core/str';
import Templates from 'core/templates';
import ModalEvents from 'core/modal_events';
import Dropzone from 'core/dropzone';
import * as config from 'core/config';
import Notification from 'core/notification';

let listenersRegistered = false;
let droppedFiles = [];

/**
 * Open the upload modal.
 *
 * @param {object} data Data passed from the event
 */
const openUploadModal = (data) => {
    Templates.render('repository_googledocs/upload_dialogue', {}).then(function(bodyHtml) {
        return SaveCancelModal.create({
            title: getString('upload'),
            body: bodyHtml,
            large: true,
        }).then(function(modal) {
            modal.getRoot().on(ModalEvents.shown, () => {
                droppedFiles = [];
                initDropzone(modal);
            });
            modal.getRoot().on(ModalEvents.hidden, () => {
                modal.destroy();
            });
            modal.getRoot().on(ModalEvents.save, (e) => {
                e.preventDefault();
                commitFiles(data.repoId, data.contextId, data.callback, droppedFiles, modal);
            });
            modal.getRoot().on(ModalEvents.cancel, () => {
                modal.hide();
            });
            modal.show();

            return modal;
        });
    });
};

/**
 * Initialize the dropzone inside the modal.
 *
 * @param {Modal} modal Modal instance
 */
const initDropzone = (modal) => {
    const $body = modal.getBody();
    const dropzoneContainer = $body.find('.repository_googledocs_dropzone_container').get(0);
    const fileListContainer = $body.find('ul.repository_googledocs_files_list').get(0);
    const dz = new Dropzone(dropzoneContainer, '*', (files) => {
        droppedFiles.push(...files);
        let fileListHTML = '';
        for (let i = 0; i < droppedFiles.length; i++) {
            fileListHTML += '<li>' + droppedFiles[i].name + '</li>';
        }
        fileListContainer.innerHTML = fileListHTML;
    });

    getString('dropfiles', 'repository').then((label) => {
        dz.setLabel(label);
    });

    dz.init();
};

/**
 * Upload files to server.
 *
 * @param {Integer} repoId Repository ID
 * @param {Integer} contextId Context ID
 * @param {function} callback Callback function
 * @param {array} files Files to be uploaded
 * @param {Modal} modal Modal instance
 */
const commitFiles = (repoId, contextId, callback, files, modal) => {
    const saveButton = modal.getFooter().find('[data-action="save"]');
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('repo_id', repoId);
    formData.append('contextid', contextId);
    formData.append('sesskey', config.sesskey);
    for (let i = 0; i < files.length; i++) {
        formData.append("files[]", files[i]);
    }
    const xhr = new XMLHttpRequest();
    xhr.open('POST', config.wwwroot + '/repository/googledocs/repository_ajax.php', false);
    xhr.onload = function() {
        const response = JSON.parse(xhr.responseText);
        if (response.error) {
            saveButton.removeAttr('disabled');
            Notification.alert(
                getString('uploaderror', 'repository'),
                response.error,
                getString('close', 'repository'),
            );
        } else {
            saveButton.removeAttr('disabled');
            modal.hide();
            callback();
        }
    };
    xhr.send(formData);
};

/**
 * Register events.
 */
const registerEventListeners = () => {
    if (!listenersRegistered) {
        subscribe('repository_googledocs_upload', (data) => {
            openUploadModal(data);
        });
        listenersRegistered = true;
    }
};

/**
 * Initializes the upload module.
 */
export const init = () => {
    registerEventListeners();
};
