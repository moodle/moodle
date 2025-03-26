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
 * Tiny Media Manager usedfiles.
 *
 * @module      tiny_media/usedfiles
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Templates from 'core/templates';
import Config from 'core/config';

class UsedFileManager {
    constructor(userContext, itemId, elementId) {
        this.files = this.getFiles();
        this.userContext = userContext;
        this.itemId = itemId;
        this.elementId = elementId;
    }

    getElementId() {
        return this.elementId;
    }

    getUsedFiles() {
        const editor = window.parent.tinymce.EditorManager.get(this.getElementId());
        if (!editor) {
            window.console.error(`Editor not found for ${this.getElementId()}`);
            return [];
        }
        const content = editor.getContent();
        const baseUrl = `${Config.wwwroot}/draftfile.php/${this.userContext}/user/draft/${this.itemId}/`;
        const pattern = new RegExp("[\"']" + baseUrl.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&') + "(?<filename>.+?)[\\?\"']", 'gm');

        const usedFiles = [...content.matchAll(pattern)].map((match) => decodeURIComponent(match.groups.filename));

        return usedFiles;
    }

    // Return an array of unused files.
    findUnusedFiles(usedFiles) {
        return Object.entries(this.files)
            .filter(([filename]) => !usedFiles.includes(filename))
            .map(([filename]) => filename);
    }

    // Return an array of missing files.
    findMissingFiles(usedFiles) {
        return usedFiles.filter((filename) => !this.files.hasOwnProperty(filename));
    }

    updateFiles() {
        const form = document.querySelector('form');
        const usedFiles = this.getUsedFiles();
        const unusedFiles = this.findUnusedFiles(usedFiles);
        const missingFiles = this.findMissingFiles(usedFiles);

        form.querySelectorAll('input[type=checkbox][name^="deletefile"]').forEach((checkbox) => {
            if (!unusedFiles.includes(checkbox.dataset.filename)) {
                checkbox.closest('.fitem').remove();
            }
        });

        form.classList.toggle('has-missing-files', !!missingFiles.length);
        form.classList.toggle('has-unused-files', !!unusedFiles.length);

        return Templates.renderForPromise('tiny_media/missingfiles', {
            missingFiles,
        }).then(({html, js}) => {
            Templates.replaceNodeContents(form.querySelector('.missing-files'), html, js);
            return;
        });
    }

    /**
     * Retrieves a list of existing files selected for deletion.
     *
     * @returns {Object} An object where the keys are filenames and the values are file hashes.
     *
     */
     getFiles() {
        const files = {};
        document.querySelectorAll('input[type=checkbox][name^="deletefile"]').forEach(input => {
            files[input.dataset.filename] = input.dataset.filehash;
        });
        return files;
    }
}

export const init = (files, usercontext, itemid, elementid) => {
    const manager = new UsedFileManager(files, usercontext, itemid, elementid);
    manager.updateFiles();

    return manager;
};
