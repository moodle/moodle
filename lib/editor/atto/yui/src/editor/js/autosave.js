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

var AUTOSAVE_FREQUENCY = 60000;

function EditorAutosave() {}

EditorAutosave.ATTRS= {
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

        if (!this.get('autosaveEnabled')) {
            // Autosave disabled for this instance.
            return;
        }


        this.autosaveInstance = Y.stamp(this);
        var draftid = -1, optiontype, options = this.get('filepickeroptions');
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

        Y.later(AUTOSAVE_FREQUENCY, this, this.saveDraft, false, true);

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
     * Show a confirm dialogue and recover some text returned by ajax.
     *
     * @method recoverText
     * @param {String} text
     * @chainable
     */
    recoverText: function(text) {
        var confirm = new M.core.confirm({
            title : M.util.get_string('confirm', 'editor_atto'),
            question : M.util.get_string('confirmrecover', 'editor_atto', {
                label : this.textareaLabel.get('text')
            }),
            yesLabel : M.util.get_string('recover', 'editor_atto'),
            noLabel : M.util.get_string('cancel', 'editor_atto')
        });
        confirm.on('complete-yes', function() {
            confirm.hide();
            confirm.destroy();
            this.editor.setHTML(text);
            this.saveSelection();
            this.updateOriginal();
            this.lastText = text;
        }, this);
        confirm.show();

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

            Y.io(url, {
                method: 'POST',
                data: params,
                on: {
                    success: function() {
                        Y.log('Text auto-saved');
                        this.lastText = newText;
                    }
                },
                context: this
            });

            this.lastText = newText;
        }
        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosave]);
