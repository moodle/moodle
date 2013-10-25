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
 * Auto-save functionality for during quiz attempts.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_quiz = M.mod_quiz || {};
M.mod_quiz.autosave = {
    /** Delays and repeat counts. */
    TINYMCE_DETECTION_DELAY:  500,
    TINYMCE_DETECTION_REPEATS: 20,
    WATCH_HIDDEN_DELAY:      1000,
    FAILURES_BEFORE_NOTIFY:     1,
    FIRST_SUCCESSFUL_SAVE:     -1,

    /** Selectors. */
    SELECTORS: {
        QUIZ_FORM:             '#responseform',
        VALUE_CHANGE_ELEMENTS: 'input, textarea',
        CHANGE_ELEMENTS:       'input, select',
        HIDDEN_INPUTS:         'input[type=hidden]',
        CONNECTION_ERROR:      '#connection-error',
        CONNECTION_OK:         '#connection-ok'
    },

    /** Script that handles the auto-saves. */
    AUTOSAVE_HANDLER: M.cfg.wwwroot + '/mod/quiz/autosave.ajax.php',

    /** The delay between a change being made, and it being auto-saved. */
    delay: 120000,

    /** The form we are monitoring. */
    form: null,

    /** Whether the form has been modified since the last save started. */
    dirty: false,

    /** Timer object for the delay between form modifaction and the save starting. */
    delay_timer: null,

    /** Y.io transaction for the save ajax request. */
    save_transaction: null,

    /** @property Failed saves count. */
    savefailures: 0,

    /** Properly bound key change handler. */
    editor_change_handler: null,

    /** Record of the value of all the hidden fields, last time they were checked. */
    hidden_field_values: {},

    /**
     * Initialise the autosave code.
     * @param delay the delay, in seconds, between a change being detected, and
     * a save happening.
     */
    init: function(delay) {
        this.form = Y.one(this.SELECTORS.QUIZ_FORM);
        if (!this.form) {
            Y.log('No response form found. Why did you try to set up autosave?');
            return;
        }

        this.delay = delay * 1000;

        this.form.delegate('valuechange', this.value_changed, this.SELECTORS.VALUE_CHANGE_ELEMENTS, this);
        this.form.delegate('change',      this.value_changed, this.SELECTORS.CHANGE_ELEMENTS,       this);
        this.form.on('submit', this.stop_autosaving, this);

        this.init_tinymce(this.TINYMCE_DETECTION_REPEATS);

        this.save_hidden_field_values();
        this.watch_hidden_fields();
    },

    save_hidden_field_values: function() {
        this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {
            var name  = hidden.get('name');
            if (!name) {
                return;
            }
            this.hidden_field_values[name] = hidden.get('value');
        }, this);
    },

    watch_hidden_fields: function() {
        this.detect_hidden_field_changes();
        Y.later(this.WATCH_HIDDEN_DELAY, this, this.watch_hidden_fields);
    },

    detect_hidden_field_changes: function() {
        this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {
            var name  = hidden.get('name'),
                value = hidden.get('value');
            if (!name) {
                return;
            }
            if (!(name in this.hidden_field_values) || value !== this.hidden_field_values[name]) {
                this.hidden_field_values[name] = value;
                this.value_changed({target: hidden});
            }
        }, this);
    },

    /**
     * @param repeatcount Because TinyMCE might load slowly, after us, we need
     * to keep trying every 10 seconds or so, until we detect TinyMCE is there,
     * or enough time has passed.
     */
    init_tinymce: function(repeatcount) {
        if (typeof tinyMCE === 'undefined') {
            if (repeatcount > 0) {
                Y.later(this.TINYMCE_DETECTION_DELAY, this, this.init_tinymce, [repeatcount - 1]);
            } else {
                Y.log('Gave up looking for TinyMCE.');
            }
            return;
        }

        Y.log('Found TinyMCE.');
        this.editor_change_handler = Y.bind(this.editor_changed, this);
        tinyMCE.onAddEditor.add(Y.bind(this.init_tinymce_editor, this));
    },

    /**
     * @param repeatcount Because TinyMCE might load slowly, after us, we need
     * to keep trying every 10 seconds or so, until we detect TinyMCE is there,
     * or enough time has passed.
     */
    init_tinymce_editor: function(notused, editor) {
        Y.log('Found TinyMCE editor ' + editor.id + '.');
        editor.onChange.add(this.editor_change_handler);
        editor.onRedo.add(this.editor_change_handler);
        editor.onUndo.add(this.editor_change_handler);
        editor.onKeyDown.add(this.editor_change_handler);
    },

    value_changed: function(e) {
        if (e.target.get('name') === 'thispage' || e.target.get('name') === 'scrollpos' ||
                e.target.get('name').match(/_:flagged$/)) {
            return; // Not interesting.
        }
        Y.log('Detected a value change in element ' + e.target.get('name') + '.');
        this.start_save_timer_if_necessary();
    },

    editor_changed: function(editor) {
        Y.log('Detected a value change in editor ' + editor.id + '.');
        this.start_save_timer_if_necessary();
    },

    start_save_timer_if_necessary: function() {
        this.dirty = true;

        if (this.delay_timer || this.save_transaction) {
            // Already counting down or daving.
            return;
        }

        this.start_save_timer();
    },

    start_save_timer: function() {
        this.cancel_delay();
        this.delay_timer = Y.later(this.delay, this, this.save_changes);
    },

    cancel_delay: function() {
        if (this.delay_timer && this.delay_timer !== true) {
            this.delay_timer.cancel();
        }
        this.delay_timer = null;
    },

    save_changes: function() {
        this.cancel_delay();
        this.dirty = false;

        if (this.is_time_nearly_over()) {
            Y.log('No more saving, time is nearly over.');
            this.stop_autosaving();
            return;
        }

        Y.log('Doing a save.');
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }
        this.save_transaction = Y.io(this.AUTOSAVE_HANDLER, {
            method:  'POST',
            form:    {id: this.form},
            on:      {
                success: this.save_done,
                failure: this.save_failed
            },
            context: this
        });
    },

    save_done: function() {
        Y.log('Save completed.');
        this.save_transaction = null;

        if (this.dirty) {
            Y.log('Dirty after save.');
            this.start_save_timer();
        }

        if (this.savefailures > 0) {
            Y.one(this.SELECTORS.CONNECTION_ERROR).hide();
            Y.one(this.SELECTORS.CONNECTION_OK).show();
            this.savefailures = this.FIRST_SUCCESSFUL_SAVE;
        } else if (this.savefailures === this.FIRST_SUCCESSFUL_SAVE) {
            Y.one(this.SELECTORS.CONNECTION_OK).hide();
            this.savefailures = 0;
        }
    },

    save_failed: function() {
        Y.log('Save failed.');
        this.save_transaction = null;

        // We want to retry soon.
        this.start_save_timer();

        this.savefailures = Math.max(1, this.savefailures + 1);
        if (this.savefailures === this.FAILURES_BEFORE_NOTIFY) {
            Y.one(this.SELECTORS.CONNECTION_ERROR).show();
            Y.one(this.SELECTORS.CONNECTION_OK).hide();
        }
    },

    is_time_nearly_over: function() {
        return M.mod_quiz.timer && M.mod_quiz.timer.endtime &&
                (new Date().getTime() + 2*this.delay) > M.mod_quiz.timer.endtime;
    },

    stop_autosaving: function() {
        this.cancel_delay();
        this.delay_timer = true;
        if (this.save_transaction) {
            this.save_transaction.abort();
        }
    }
};
