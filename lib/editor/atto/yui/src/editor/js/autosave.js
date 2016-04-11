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
 * A autosave function for the Atto editor.
 *
 * @module     moodle-editor_atto-autosave
 * @submodule  autosave-base
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var SUCCESS_MESSAGE_TIMEOUT = 5000,
    RECOVER_MESSAGE_TIMEOUT = 60000,
    LOGNAME_AUTOSAVE = 'moodle-editor_atto-editor-autosave';

function EditorAutosave() {}

EditorAutosave.ATTRS= {
    /**
     * Enable/Disable auto save for this instance.
     *
     * @attribute autosaveEnabled
     * @type Boolean
     * @writeOnce
     */
    autosaveEnabled: {
        value: true,
        writeOnce: true
    },

    /**
     * The time between autosaves (in seconds).
     *
     * @attribute autosaveFrequency
     * @type Number
     * @default 60
     * @writeOnce
     */
    autosaveFrequency: {
        value: 60,
        writeOnce: true
    },

    /**
     * Unique hash for this page instance. Calculated from $PAGE->url in php.
     *
     * @attribute pageHash
     * @type String
     * @writeOnce
     */
    pageHash: {
        value: '',
        writeOnce: true
    },

    /**
     * The relative path to the ajax script.
     *
     * @attribute autosaveAjaxScript
     * @type String
     * @default '/lib/editor/atto/autosave-ajax.php'
     * @readOnly
     */
    autosaveAjaxScript: {
        value: '/lib/editor/atto/autosave-ajax.php',
        readOnly: true
    }
};

EditorAutosave.prototype = {

    /**
     * The text that was auto saved in the last request.
     *
     * @property lastText
     * @type string
     */
    lastText: "",

    /**
     * Autosave instance.
     *
     * @property autosaveInstance
     * @type string
     */
    autosaveInstance: null,

    /**
     * Autosave Timer.
     *
     * @property autosaveTimer
     * @type object
     */
    autosaveTimer: null,

    /**
     * Initialize the autosave process
     *
     * @method setupAutosave
     * @chainable
     */
    setupAutosave: function() {
        var draftid = -1,
            form,
            optiontype = null,
            options = this.get('filepickeroptions'),
            params,
            url;

        if (!this.get('autosaveEnabled')) {
            // Autosave disabled for this instance.
            return;
        }

        this.autosaveInstance = Y.stamp(this);
        for (optiontype in options) {
            if (typeof options[optiontype].itemid !== "undefined") {
                draftid = options[optiontype].itemid;
            }
        }

        // First see if there are any saved drafts.
        // Make an ajax request.
        url = M.cfg.wwwroot + this.get('autosaveAjaxScript');
        params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'resume',
            drafttext: '',
            draftid: draftid,
            elementid: this.get('elementid'),
            pageinstance: this.autosaveInstance,
            pagehash: this.get('pageHash')
        };

        Y.io(url, {
            method: 'POST',
            data: params,
            context: this,
            on: {
                success: function(id,o) {
                    var response_json;
                    if (typeof o.responseText !== "undefined" && o.responseText !== "") {
                        response_json = JSON.parse(o.responseText);

                        // Revert untouched editor contents to an empty string.
                        // Check for FF and Chrome.
                        if (response_json.result === '<p></p>' || response_json.result === '<p><br></p>' ||
                            response_json.result === '<br>') {
                            response_json.result = '';
                        }

                        // Check for IE 9 and 10.
                        if (response_json.result === '<p>&nbsp;</p>' || response_json.result === '<p><br>&nbsp;</p>') {
                            response_json.result = '';
                        }

                        if (response_json.error || typeof response_json.result === 'undefined') {
                            Y.log('Error occurred recovering draft text: ' + response_json.error, 'debug', LOGNAME_AUTOSAVE);
                            this.showMessage(M.util.get_string('errortextrecovery', 'editor_atto'),
                                    NOTIFY_WARNING, RECOVER_MESSAGE_TIMEOUT);
                        } else if (response_json.result !== this.textarea.get('value') &&
                                response_json.result !== '') {
                            Y.log('Autosave text found - recover it.', 'debug', LOGNAME_AUTOSAVE);
                            this.recoverText(response_json.result);
                        }
                        this._fireSelectionChanged();
                    }
                },
                failure: function() {
                    this.showMessage(M.util.get_string('errortextrecovery', 'editor_atto'),
                            NOTIFY_WARNING, RECOVER_MESSAGE_TIMEOUT);
                }
            }
        });

        // Now setup the timer for periodic saves.

        var delay = parseInt(this.get('autosaveFrequency'), 10) * 1000;
        this.autosaveTimer = Y.later(delay, this, this.saveDraft, false, true);

        // Now setup the listener for form submission.
        form = this.textarea.ancestor('form');
        if (form) {
            form.on('submit', this.resetAutosave, this);
        }
        return this;
    },

    /**
     * Clear the autosave text because the form was submitted normally.
     *
     * @method resetAutosave
     * @chainable
     */
    resetAutosave: function() {
        // Make an ajax request to reset the autosaved text.
        var url = M.cfg.wwwroot + this.get('autosaveAjaxScript');
        var params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'reset',
            elementid: this.get('elementid'),
            pageinstance: this.autosaveInstance,
            pagehash: this.get('pageHash')
        };

        Y.io(url, {
            method: 'POST',
            data: params,
            sync: true
        });
        return this;
    },


    /**
     * Recover a previous version of this text and show a message.
     *
     * @method recoverText
     * @param {String} text
     * @chainable
     */
    recoverText: function(text) {
        this.editor.setHTML(text);
        this.saveSelection();
        this.updateOriginal();
        this.lastText = text;

        this.showMessage(M.util.get_string('textrecovered', 'editor_atto'),
                NOTIFY_INFO, RECOVER_MESSAGE_TIMEOUT);

        return this;
    },

    /**
     * Save a single draft via ajax.
     *
     * @method saveDraft
     * @chainable
     */
    saveDraft: function() {
        var url, params;

        if (!this.editor.getDOMNode()) {
            // Stop autosaving if the editor was removed from the page.
            this.autosaveTimer.cancel();
            return;
        }
        // Only copy the text from the div to the textarea if the textarea is not currently visible.
        if (!this.editor.get('hidden')) {
            this.updateOriginal();
        }
        var newText = this.textarea.get('value');

        if (newText !== this.lastText) {
            Y.log('Autosave text', 'debug', LOGNAME_AUTOSAVE);

            // Make an ajax request.
            url = M.cfg.wwwroot + this.get('autosaveAjaxScript');
            params = {
                sesskey: M.cfg.sesskey,
                contextid: this.get('contextid'),
                action: 'save',
                drafttext: newText,
                elementid: this.get('elementid'),
                pagehash: this.get('pageHash'),
                pageinstance: this.autosaveInstance
            };

            // Reusable error handler - must be passed the correct context.
            var ajaxErrorFunction = function(code, response) {
                var errorDuration = parseInt(this.get('autosaveFrequency'), 10) * 1000;
                Y.log('Error while autosaving text:' + code, 'warn', LOGNAME_AUTOSAVE);
                Y.log(response, 'warn', LOGNAME_AUTOSAVE);
                this.showMessage(M.util.get_string('autosavefailed', 'editor_atto'), NOTIFY_WARNING, errorDuration);
            };

            Y.io(url, {
                method: 'POST',
                data: params,
                context: this,
                on: {
                    error: ajaxErrorFunction,
                    failure: ajaxErrorFunction,
                    success: function(code, response) {
                        if (response.responseText !== "") {
                            Y.soon(Y.bind(ajaxErrorFunction, this, [code, response]));
                        } else {
                            // All working.
                            this.lastText = newText;
                            this.showMessage(M.util.get_string('autosavesucceeded', 'editor_atto'),
                                    NOTIFY_INFO, SUCCESS_MESSAGE_TIMEOUT);
                        }
                    }
                }
            });
        }
        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosave]);
