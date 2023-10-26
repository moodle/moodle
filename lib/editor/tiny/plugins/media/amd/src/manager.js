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
 * Tiny Media Manager plugin class for Moodle.
 *
 * @module      tiny_media/manager
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import {getData} from './options';
import Config from 'core/config';

export const MediaManager = class {

    editor = null;
    area = null;

    constructor(editor) {
        this.editor = editor;
        const data = getData(editor);
        this.area = data.params.area;
        this.area.itemid = data.fpoptions.image.itemid;
    }

    displayDialogue = () => {
        Modal.create({
            type: Modal.types.DEFAULT,
            large: true,
            title: getString('mediamanagerproperties', 'tiny_media'),
            body: Templates.render('tiny_media/mm_iframe', {
                src: this.getIframeURL()
            })
        }).then(modal => {
            modal.getRoot().on(ModalEvents.bodyRendered, () => {
                this.selectFirstElement();
            });
            modal.getRoot().on(ModalEvents.hidden, () => {
                modal.destroy();
            });
            modal.show();
            document.querySelector('.modal-lg').style.cssText = `max-width: 850px`;
            return modal;
        }).catch();
    };

    // It will select the first element in the file manager.
    selectFirstElement = () => {
        const iframe = document.getElementById('mm-iframe');
        iframe.addEventListener('load', function() {
            let intervalId = setInterval(function() {
                const iDocument = iframe.contentWindow.document;
                if (iDocument.querySelector('.filemanager')) {
                    const firstFocusableElement = iDocument.querySelector('.fp-navbar a:not([disabled])');
                    if (firstFocusableElement) {
                        firstFocusableElement.focus();
                    }
                    clearInterval(intervalId);
                }
            }, 200);
        });
    };

    getIframeURL = () => {
        const url = new URL(`${Config.wwwroot}/lib/editor/tiny/plugins/media/manage.php`);
        url.searchParams.append('elementid', this.editor.getElement().id);
        for (const key in this.area) {
            url.searchParams.append(key, this.area[key]);
        }
        return url.toString();
    };
};
