if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js",
    code: []
};
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"].code=["YUI.add('moodle-mod_quiz-autosave', function (Y, NAME) {","","// This file is part of Moodle - http://moodle.org/","//","// Moodle is free software: you can redistribute it and/or modify","// it under the terms of the GNU General Public License as published by","// the Free Software Foundation, either version 3 of the License, or","// (at your option) any later version.","//","// Moodle is distributed in the hope that it will be useful,","// but WITHOUT ANY WARRANTY; without even the implied warranty of","// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the","// GNU General Public License for more details.","//","// You should have received a copy of the GNU General Public License","// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.","","","/**"," * Auto-save functionality for during quiz attempts."," *"," * @package   mod_quiz"," * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}"," * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later"," */","","M.mod_quiz = M.mod_quiz || {};","M.mod_quiz.autosave = {","    /** Delays and repeat counts. */","    TINYMCE_DETECTION_DELAY:  500,","    TINYMCE_DETECTION_REPEATS: 20,","    WATCH_HIDDEN_DELAY:      1000,","","    /** Selectors. */","    SELECTORS: {","        QUIZ_FORM:             '#responseform',","        VALUE_CHANGE_ELEMENTS: 'input, textarea',","        CHANGE_ELEMENTS:       'input, select',","        HIDDEN_INPUTS:         'input[type=hidden]'","    },","","    /** Script that handles the auto-saves. */","    AUTOSAVE_HANDLER: M.cfg.wwwroot + '/mod/quiz/autosave.ajax.php',","","    /** The delay between a change being made, and it being auto-saved. */","    delay: 120000,","","    /** The form we are monitoring. */","    form: null,","","    /** Whether the form has been modified since the last save started. */","    dirty: false,","","    /** Timer object for the delay between form modifaction and the save starting. */","    delay_timer: null,","","    /** Y.io transaction for the save ajax request. */","    save_transaction: null,","","    /** Properly bound key change handler. */","    editor_change_handler: null,","","    hidden_field_values: {},","","    /**","     * Initialise the autosave code.","     * @param delay the delay, in seconds, between a change being detected, and","     * a save happening.","     */","    init: function(delay) {","        this.form = Y.one(this.SELECTORS.QUIZ_FORM);","        if (!this.form) {","            return;","        }","","        this.delay = delay * 1000;","","        this.form.delegate('valuechange', this.value_changed, this.SELECTORS.VALUE_CHANGE_ELEMENTS, this);","        this.form.delegate('change',      this.value_changed, this.SELECTORS.CHANGE_ELEMENTS,       this);","        this.form.on('submit', this.stop_autosaving, this);","","        this.init_tinymce(this.TINYMCE_DETECTION_REPEATS);","","        this.save_hidden_field_values();","        this.watch_hidden_fields();","    },","","    save_hidden_field_values: function() {","        this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {","            var name  = hidden.get('name');","            if (!name) {","                return;","            }","            this.hidden_field_values[name] = hidden.get('value');","        }, this);","    },","","    watch_hidden_fields: function() {","        this.detect_hidden_field_changes();","        Y.later(this.WATCH_HIDDEN_DELAY, this, this.watch_hidden_fields);","    },","","    detect_hidden_field_changes: function() {","        this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {","            var name  = hidden.get('name'),","                value = hidden.get('value');","            if (!name) {","                return;","            }","            if (!(name in this.hidden_field_values) || value !== this.hidden_field_values[name]) {","                this.hidden_field_values[name] = value;","                this.value_changed({target: hidden});","            }","        }, this);","    },","","    /**","     * @param repeatcount Because TinyMCE might load slowly, after us, we need","     * to keep trying every 10 seconds or so, until we detect TinyMCE is there,","     * or enough time has passed.","     */","    init_tinymce: function(repeatcount) {","        if (typeof tinymce === 'undefined') {","            if (repeatcount > 0) {","                Y.later(this.TINYMCE_DETECTION_DELAY, this, self.init_tinymce,repeatcount - 1);","            }","            return;","        }","","        this.editor_change_handler = Y.bind(this.editor_changed, this);","        tinyMCE.onAddEditor.add(Y.bind(this.init_tinymce_editor, this));","    },","","    /**","     * @param repeatcount Because TinyMCE might load slowly, after us, we need","     * to keep trying every 10 seconds or so, until we detect TinyMCE is there,","     * or enough time has passed.","     */","    init_tinymce_editor: function(notused, editor) {","        editor.onChange.add(this.editor_change_handler);","        editor.onRedo.add(this.editor_change_handler);","        editor.onUndo.add(this.editor_change_handler);","        editor.onKeyDown.add(this.editor_change_handler);","    },","","    value_changed: function(e) {","        if (e.target.get('name') === 'thispage') {","            return; // Not interesting.","        }","        this.start_save_timer_if_necessary();","    },","","    editor_changed: function(editor) {","        this.start_save_timer_if_necessary();","    },","","    start_save_timer_if_necessary: function() {","        this.dirty = true;","","        if (this.delay_timer || this.save_transaction) {","            // Already counting down or daving.","            return;","        }","","        this.start_save_timer();","    },","","    start_save_timer: function() {","        this.cancel_delay();","        this.delay_timer = Y.later(this.delay, this, this.save_changes);","    },","","    cancel_delay: function() {","        if (this.delay_timer) {","            this.delay_timer.cancel();","        }","        this.delay_timer = null;","    },","","    save_changes: function() {","        this.cancel_delay();","        this.dirty = false;","","        if (this.is_time_nearly_over()) {","            this.stop_autosaving();","            return;","        }","","        this.save_transaction = Y.io(this.AUTOSAVE_HANDLER, {","            method:  'POST',","            form:    {id: this.form},","            on:      {complete: this.save_done},","            context: this","        });","    },","","    save_done: function() {","        this.save_transaction = null;","","        if (this.dirty) {","            this.start_save_timer();","        }","    },","","    is_time_nearly_over: function() {","        return M.mod_quiz.timer && M.mod_quiz.timer.endtime &&","                (new Date().getTime() + 2*this.delay) > M.mod_quiz.timer.endtime;","    },","","    stop_autosaving: function() {","        this.cancel_delay();","        this.delay_timer = true;","        if (this.save_transaction) {","            this.save_transaction.abort();","        }","    }","};","","","}, '@VERSION@', {\"requires\": [\"base\", \"node\", \"event\", \"event-valuechange\", \"node-event-delegate\", \"io-form\"]});"];
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"].lines = {"1":0,"27":0,"28":0,"71":0,"72":0,"73":0,"76":0,"78":0,"79":0,"80":0,"82":0,"84":0,"85":0,"89":0,"90":0,"91":0,"92":0,"94":0,"99":0,"100":0,"104":0,"105":0,"107":0,"108":0,"110":0,"111":0,"112":0,"123":0,"124":0,"125":0,"127":0,"130":0,"131":0,"140":0,"141":0,"142":0,"143":0,"147":0,"148":0,"150":0,"154":0,"158":0,"160":0,"162":0,"165":0,"169":0,"170":0,"174":0,"175":0,"177":0,"181":0,"182":0,"184":0,"185":0,"186":0,"189":0,"198":0,"200":0,"201":0,"206":0,"211":0,"212":0,"213":0,"214":0};
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"].functions = {"init:70":0,"(anonymous 2):89":0,"save_hidden_field_values:88":0,"watch_hidden_fields:98":0,"(anonymous 3):104":0,"detect_hidden_field_changes:103":0,"init_tinymce:122":0,"init_tinymce_editor:139":0,"value_changed:146":0,"editor_changed:153":0,"start_save_timer_if_necessary:157":0,"start_save_timer:168":0,"cancel_delay:173":0,"save_changes:180":0,"save_done:197":0,"is_time_nearly_over:205":0,"stop_autosaving:210":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"].coveredLines = 64;
_yuitest_coverage["build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js"].coveredFunctions = 18;
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 1);
YUI.add('moodle-mod_quiz-autosave', function (Y, NAME) {

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

_yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 27);
M.mod_quiz = M.mod_quiz || {};
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 28);
M.mod_quiz.autosave = {
    /** Delays and repeat counts. */
    TINYMCE_DETECTION_DELAY:  500,
    TINYMCE_DETECTION_REPEATS: 20,
    WATCH_HIDDEN_DELAY:      1000,

    /** Selectors. */
    SELECTORS: {
        QUIZ_FORM:             '#responseform',
        VALUE_CHANGE_ELEMENTS: 'input, textarea',
        CHANGE_ELEMENTS:       'input, select',
        HIDDEN_INPUTS:         'input[type=hidden]'
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

    /** Properly bound key change handler. */
    editor_change_handler: null,

    hidden_field_values: {},

    /**
     * Initialise the autosave code.
     * @param delay the delay, in seconds, between a change being detected, and
     * a save happening.
     */
    init: function(delay) {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "init", 70);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 71);
this.form = Y.one(this.SELECTORS.QUIZ_FORM);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 72);
if (!this.form) {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 73);
return;
        }

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 76);
this.delay = delay * 1000;

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 78);
this.form.delegate('valuechange', this.value_changed, this.SELECTORS.VALUE_CHANGE_ELEMENTS, this);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 79);
this.form.delegate('change',      this.value_changed, this.SELECTORS.CHANGE_ELEMENTS,       this);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 80);
this.form.on('submit', this.stop_autosaving, this);

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 82);
this.init_tinymce(this.TINYMCE_DETECTION_REPEATS);

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 84);
this.save_hidden_field_values();
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 85);
this.watch_hidden_fields();
    },

    save_hidden_field_values: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "save_hidden_field_values", 88);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 89);
this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {
            _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "(anonymous 2)", 89);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 90);
var name  = hidden.get('name');
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 91);
if (!name) {
                _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 92);
return;
            }
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 94);
this.hidden_field_values[name] = hidden.get('value');
        }, this);
    },

    watch_hidden_fields: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "watch_hidden_fields", 98);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 99);
this.detect_hidden_field_changes();
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 100);
Y.later(this.WATCH_HIDDEN_DELAY, this, this.watch_hidden_fields);
    },

    detect_hidden_field_changes: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "detect_hidden_field_changes", 103);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 104);
this.form.all(this.SELECTORS.HIDDEN_INPUTS).each(function(hidden) {
            _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "(anonymous 3)", 104);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 105);
var name  = hidden.get('name'),
                value = hidden.get('value');
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 107);
if (!name) {
                _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 108);
return;
            }
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 110);
if (!(name in this.hidden_field_values) || value !== this.hidden_field_values[name]) {
                _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 111);
this.hidden_field_values[name] = value;
                _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 112);
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
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "init_tinymce", 122);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 123);
if (typeof tinymce === 'undefined') {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 124);
if (repeatcount > 0) {
                _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 125);
Y.later(this.TINYMCE_DETECTION_DELAY, this, self.init_tinymce,repeatcount - 1);
            }
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 127);
return;
        }

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 130);
this.editor_change_handler = Y.bind(this.editor_changed, this);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 131);
tinyMCE.onAddEditor.add(Y.bind(this.init_tinymce_editor, this));
    },

    /**
     * @param repeatcount Because TinyMCE might load slowly, after us, we need
     * to keep trying every 10 seconds or so, until we detect TinyMCE is there,
     * or enough time has passed.
     */
    init_tinymce_editor: function(notused, editor) {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "init_tinymce_editor", 139);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 140);
editor.onChange.add(this.editor_change_handler);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 141);
editor.onRedo.add(this.editor_change_handler);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 142);
editor.onUndo.add(this.editor_change_handler);
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 143);
editor.onKeyDown.add(this.editor_change_handler);
    },

    value_changed: function(e) {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "value_changed", 146);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 147);
if (e.target.get('name') === 'thispage') {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 148);
return; // Not interesting.
        }
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 150);
this.start_save_timer_if_necessary();
    },

    editor_changed: function(editor) {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "editor_changed", 153);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 154);
this.start_save_timer_if_necessary();
    },

    start_save_timer_if_necessary: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "start_save_timer_if_necessary", 157);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 158);
this.dirty = true;

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 160);
if (this.delay_timer || this.save_transaction) {
            // Already counting down or daving.
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 162);
return;
        }

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 165);
this.start_save_timer();
    },

    start_save_timer: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "start_save_timer", 168);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 169);
this.cancel_delay();
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 170);
this.delay_timer = Y.later(this.delay, this, this.save_changes);
    },

    cancel_delay: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "cancel_delay", 173);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 174);
if (this.delay_timer) {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 175);
this.delay_timer.cancel();
        }
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 177);
this.delay_timer = null;
    },

    save_changes: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "save_changes", 180);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 181);
this.cancel_delay();
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 182);
this.dirty = false;

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 184);
if (this.is_time_nearly_over()) {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 185);
this.stop_autosaving();
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 186);
return;
        }

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 189);
this.save_transaction = Y.io(this.AUTOSAVE_HANDLER, {
            method:  'POST',
            form:    {id: this.form},
            on:      {complete: this.save_done},
            context: this
        });
    },

    save_done: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "save_done", 197);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 198);
this.save_transaction = null;

        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 200);
if (this.dirty) {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 201);
this.start_save_timer();
        }
    },

    is_time_nearly_over: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "is_time_nearly_over", 205);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 206);
return M.mod_quiz.timer && M.mod_quiz.timer.endtime &&
                (new Date().getTime() + 2*this.delay) > M.mod_quiz.timer.endtime;
    },

    stop_autosaving: function() {
        _yuitest_coverfunc("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", "stop_autosaving", 210);
_yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 211);
this.cancel_delay();
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 212);
this.delay_timer = true;
        _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 213);
if (this.save_transaction) {
            _yuitest_coverline("build/moodle-mod_quiz-autosave/moodle-mod_quiz-autosave.js", 214);
this.save_transaction.abort();
        }
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "event-valuechange", "node-event-delegate", "io-form"]});
