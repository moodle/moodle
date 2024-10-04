// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny tiny_html for Moodle.
 *
 * @module      tiny_html/plugin
 * @copyright   2023 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getTinyMCE} from 'editor_tiny/loader';
import {getPluginMetadata} from 'editor_tiny/utils';

import {component, pluginName, codeMirrorStyle} from './common';

/* eslint-disable camelcase */
import {html_beautify} from './beautify/beautify-html';
import {get_strings} from 'core/str';
/* eslint-enable camelcase */
import {
    EditorState,
    EditorView,
    basicSetup,
    lang,
} from './codemirror-lazy';

/**
 * Options for the html_beautify function.
 * We disable the camelCase check here as these are
 * variables that we are passing to the js-beautify library.
 */
/* eslint-disable camelcase */
const beautifyOptions = {
    indent_size: 2,
    wrap_line_length: 80,
    unformatted: [],
};
/* eslint-enable camelcase */

// Set up the tiny_html Plugin.
// eslint-disable-next-line no-async-promise-executor
export default new Promise(async(resolve) => {
    // Note: The PluginManager.add function does not support asynchronous configuration.
    // Perform any asynchronous configuration here, and then call the PluginManager.add function.
    const [
        tinyMCE,
        pluginMetadata,
        buttonStrings,
    ] = await Promise.all([
        getTinyMCE(),
        getPluginMetadata(component, pluginName),
        get_strings([
            {key: 'cancel', component: 'moodle'},
            {key: 'save', component: 'moodle'},
        ])
    ]);

    // Reminder: Any asynchronous code must be run before this point.
    tinyMCE.PluginManager.add(pluginName, (editor) => {
        // Initial configuration for TinyMCE editor the windowManager.
        const windowManagerConfig = {
            title: 'Source code',
            size: 'large',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'htmlpanel',
                        html: '<div id="' + editor.id + '_codeMirrorContainer" style="height: 100%;"></div>',
                    },
                ],
            },
            buttons: null,
            initialData: null,
            onSubmit: null,
        };

        // Overriding the default 'mceCodeEditor' command
        editor.addCommand('mceCodeEditor', () => {
            // Get the current content of the editor
            // eslint-disable-next-line camelcase
            const content = editor.getContent({source_view: true});

            // Beautify the content using html_beautify
            const beautifiedContent = html_beautify(content, beautifyOptions);

            // Create the CodeMirror instance
            let cmInstance;

            let state = EditorState.create({
                doc: beautifiedContent,
                // This is where basicSetup should go as [basicSetup, ...].
                extensions: [
                    basicSetup,
                    EditorState.tabSize.of(2),
                    // Bring in all language extensions.
                    ...Object.entries(lang).map(([, languagePlugin]) => languagePlugin()),
                ],
            });

            // Create a new window to display the beautified code
            editor.windowManager.open({
                ...windowManagerConfig,
                onSubmit: (api) => {
                    const cmContent = cmInstance.state.doc.toString();
                    // eslint-disable-next-line camelcase
                    editor.setContent(cmContent, {source_view: true});
                    api.close();
                },
                buttons: [
                    {
                        type: 'cancel',
                        text: buttonStrings[0],
                    },
                    {
                        type: 'submit',
                        text: buttonStrings[1],
                        primary: true,
                    },
                ]
            });

            const container = document.getElementById(editor.id + '_codeMirrorContainer');
            // Create a shadow root for the CodeMirror instance.
            // This is required to prevent the TinyMCE editor styles from overriding the CodeMirror ones.
            const shadowRoot = container.attachShadow({mode: "open"});

            // Add the styles to the shadow root
            const style = document.createElement('style');
            style.textContent = codeMirrorStyle;
            shadowRoot.appendChild(style);

            // Create a new div and add the class 'my-codemirror-container'
            const div = document.createElement('div');
            div.classList.add('modal-codemirror-container');
            shadowRoot.appendChild(div);

            // Create the CodeMirror instance
            cmInstance = new EditorView({
                state,
                parent: div,
            });

            // Add an event listener to the shadow root to listen for the tab key press.
            shadowRoot.addEventListener('keydown', (event) => {
                // If the tab key is pressed, prevent the default action and select the save button.
                // We need to do this as the shadow root is not part of the DOM, so the tab key will not
                // be caught by the TinyMCE dialog.
                if (event.key === 'Tab') {
                    event.preventDefault();
                    const codeMirrorContainer = document.getElementById(editor.id + '_codeMirrorContainer');
                    const dialogElement = codeMirrorContainer.closest('.tox-dialog');
                    const cancelButton = dialogElement.querySelector('button[title="' + buttonStrings[1] + '"]');
                    cancelButton.focus();
                }
            });

        });
        // Return the pluginMetadata object. This is used by TinyMCE to display a help link for your plugin.
        return pluginMetadata;
    });

    resolve(pluginName);
});
