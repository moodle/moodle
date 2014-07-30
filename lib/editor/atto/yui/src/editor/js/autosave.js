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
    RECOVER_MESSAGE_TIMEOUT = 60000;

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
     * @type Integer
     * @default 60
     * @writeOnce
     */
    autosaveFrequency: {
        value: 60,
        writeOnce: true
    }
};

EditorAutosave.prototype = {

    /**
     * The text that was auto saved in the last request.
     *
     * @property lastText
     * @type string
     */
    lastText: null,

    /**
     * Autosave instance.
     *
     * @property autosaveInstance
     * @type string
     */
    autosaveInstance: null,

    /**
     * Initialize the autosave process
     *
     * @method setupAutosave
     * @chainable
     */
    setupAutosave: function() {
        var draftid = -1,
            optiontype = null,
            options = this.get('filepickeroptions');

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
        url = M.cfg.wwwroot + '/lib/editor/atto/autosave-ajax.php';
        params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'resume',
            drafttext: '',
            draftid: draftid,
            elementid: this.get('elementid'),
            pageinstance: this.autosaveInstance,
            pagedomid: Y.one('body').get('id')
        };

        Y.io(url, {
            method: 'POST',
            data: params,
            on: {
                success: function(id,o) {
                    if (typeof o.responseText !== "undefined" &&
                        o.responseText !== "" &&
                        o.responseText !== this.textarea.get('value')) {
                        Y.log('Autosave text found - confirm recovery.');
                        this.recoverText(o.responseText);
                    }
                }
            },
            context: this
        });

        // Now setup the timer for periodic saves.

        Y.log(this.get('autosaveFrequency'));
        var delay = parseInt(this.get('autosaveFrequency'), 10) * 1000;
        Y.later(delay, this, this.saveDraft, false, true);

        // Now setup the listener for form submission.
        this.textarea.ancestor('form').on('submit', this.resetAutosave, this);
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
        url = M.cfg.wwwroot + '/lib/editor/atto/autosave-ajax.php';
        params = {
            sesskey: M.cfg.sesskey,
            contextid: this.get('contextid'),
            action: 'reset',
            elementid: this.get('elementid'),
            pageinstance: this.autosaveInstance,
            pagedomid: Y.one('body').get('id')
        };

        // We don't even wait!
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

        this.showMessage(M.util.get_string('textrecovered', 'editor_atto'), 'info', RECOVER_MESSAGE_TIMEOUT);

        return this;
    },

    /**
     * Save a single draft via ajax.
     *
     * @method saveDraft
     * @chainable
     */
    saveDraft: function() {
        this.updateOriginal();
        var newText = this.textarea.get('value');

        if (newText !== this.lastText) {
            Y.log('Autosave text');

            // Make an ajax request.
            url = M.cfg.wwwroot + '/lib/editor/atto/autosave-ajax.php';
            params = {
                sesskey: M.cfg.sesskey,
                contextid: this.get('contextid'),
                action: 'save',
                drafttext: newText,
                elementid: this.get('elementid'),
                pagedomid: Y.one('body').get('id'),
                pageinstance: this.autosaveInstance
            };
            var errorDuration = parseInt(this.get('autosaveFrequency'), 10) * 1000;

            Y.io(url, {
                method: 'POST',
                data: params,
                on: {
                    error: function(code, response) {
                        Y.log('Error while autosaving text:' + code, 'warn');
                        Y.log(response, 'warn');
                        this.showMessage(M.util.get_string('autosavefailed', 'editor_atto'), 'warning', errorDuration);
                    },
                    failure: function(code, response) {
                        Y.log('Failure while autosaving text:' + code, 'warn');
                        Y.log(response, 'warn');
                        this.showMessage(M.util.get_string('autosavefailed', 'editor_atto'), 'warning', errorDuration);
                    },
                    success: function(code, response) {
                        if (response.response !== "") {
                            Y.log('Failure while autosaving text.', 'warn');
                            Y.log(response, 'debug');
                            this.showMessage(M.util.get_string('autosavefailed', 'editor_atto'), 'warning', errorDuration);
                        } else {
                            // All working.
                            this.lastText = newText;
                            this.showMessage(M.util.get_string('autosavesucceeded', 'editor_atto'), 'info', SUCCESS_MESSAGE_TIMEOUT);
                        }
                    }
                },
                context: this
            });
        }
        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosave]);
