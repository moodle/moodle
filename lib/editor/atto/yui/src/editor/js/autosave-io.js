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
 * @module     moodle-editor_atto-autosave-io
 * @submodule  autosave-io
 * @package    editor_atto
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var EditorAutosaveIoDispatcherInstance = null;

function EditorAutosaveIoDispatcher() {
    EditorAutosaveIoDispatcher.superclass.constructor.apply(this, arguments);
    this._submitEvents = {};
    this._queue = [];
    this._throttle = null;
}
EditorAutosaveIoDispatcher.NAME = 'EditorAutosaveIoDispatcher';
EditorAutosaveIoDispatcher.ATTRS = {

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
    },

    /**
     * The time buffer for the throttled requested.
     *
     * @attribute delay
     * @type Number
     * @default 50
     * @readOnly
     */
    delay: {
        value: 50,
        readOnly: true
    }

};
Y.extend(EditorAutosaveIoDispatcher, Y.Base, {

    /**
     * Dispatch an IO request.
     *
     * This method will put the requests in a queue in order to attempt to bulk them.
     *
     * @param  {Object} params    The parameters of the request.
     * @param  {Object} context   The context in which the callbacks are called.
     * @param  {Object} callbacks Object with 'success', 'complete', 'end', 'failure' and 'start' as
     *                            optional keys defining the callbacks to call. Success and Complete
     *                            functions will receive the response as parameter. Success and Complete
     *                            may receive an object containing the error key, use this to confirm
     *                            that no errors occured.
     * @return {Void}
     */
    dispatch: function(params, context, callbacks) {
        if (this._throttle) {
            this._throttle.cancel();
        }

        this._throttle = Y.later(this.get('delay'), this, this._processDispatchQueue);
        this._queue.push([params, context, callbacks]);
    },

    /**
     * Dispatches the requests in the queue.
     *
     * @return {Void}
     */
    _processDispatchQueue: function() {
        var queue = this._queue,
            data = {};

        this._queue = [];
        if (queue.length < 1) {
            return;
        }

        Y.Array.each(queue, function(item, index) {
            data[index] = item[0];
        });

        Y.io(M.cfg.wwwroot + this.get('autosaveAjaxScript'), {
            method: 'POST',
            data: Y.QueryString.stringify({
                actions: data,
                sesskey: M.cfg.sesskey
            }),
            on: {
                start: this._makeIoEventCallback('start', queue),
                complete: this._makeIoEventCallback('complete', queue),
                failure: this._makeIoEventCallback('failure', queue),
                end: this._makeIoEventCallback('end', queue),
                success: this._makeIoEventCallback('success', queue)
            }
        });
    },

    /**
     * Creates a function that dispatches an IO response to callbacks.
     *
     * @param  {String} event The type of event.
     * @param  {Array} queue The queue.
     * @return {Function}
     */
    _makeIoEventCallback: function(event, queue) {
        var noop = function() {};
        return function() {
            var response = arguments[1],
                parsed = {};

            if ((event == 'complete' || event == 'success') && (typeof response !== 'undefined'
                    && typeof response.responseText !== 'undefined' && response.responseText !== '')) {

                // Success and complete events need to parse the response.
                parsed = JSON.parse(response.responseText) || {};
            }

            Y.Array.each(queue, function(item, index) {
                var context = item[1],
                    cb = (item[2] && item[2][event]) || noop,
                    arg;

                if (parsed && parsed.error) {
                    // The response is an error, we send it to everyone.
                    arg = parsed;
                } else if (parsed) {
                    // The response was parsed, we only communicate the relevant portion of the response.
                    arg = parsed[index];
                }

                cb.apply(context, [arg]);
            });
        };
    },

    /**
     * Form submit handler.
     *
     * @param  {EventFacade} e The event.
     * @return {Void}
     */
    _onSubmit: function(e) {
        var data = {},
            id = e.currentTarget.generateID(),
            params = this._submitEvents[id];

        if (!params || params.ios.length < 1) {
            return;
        }

        Y.Array.each(params.ios, function(param, index) {
            data[index] = param;
        });

        Y.io(M.cfg.wwwroot + this.get('autosaveAjaxScript'), {
            method: 'POST',
            data: Y.QueryString.stringify({
                actions: data,
                sesskey: M.cfg.sesskey
            }),
            sync: true
        });
    },

    /**
     * Registers a request to be made on form submission.
     *
     * @param  {Node} node The forum node we will listen to.
     * @param  {Object} params Parameters for the IO request.
     * @return {Void}
     */
    whenSubmit: function(node, params) {
        if (typeof this._submitEvents[node.generateID()] === 'undefined') {
            this._submitEvents[node.generateID()] = {
                event: node.on('submit', this._onSubmit, this),
                ajaxEvent: node.on(M.core.event.FORM_SUBMIT_AJAX, this._onSubmit, this),
                ios: []
            };
        }
        this._submitEvents[node.get('id')].ios.push([params]);
    }

});
EditorAutosaveIoDispatcherInstance = new EditorAutosaveIoDispatcher();


function EditorAutosaveIo() {}
EditorAutosaveIo.prototype = {

    /**
     * Dispatch an IO request.
     *
     * This method will put the requests in a queue in order to attempt to bulk them.
     *
     * @param  {Object} params    The parameters of the request.
     * @param  {Object} context   The context in which the callbacks are called.
     * @param  {Object} callbacks Object with 'success', 'complete', 'end', 'failure' and 'start' as
     *                            optional keys defining the callbacks to call. Success and Complete
     *                            functions will receive the response as parameter. Success and Complete
     *                            may receive an object containing the error key, use this to confirm
     *                            that no errors occured.
     * @return {Void}
     */
    autosaveIo: function(params, context, callbacks) {
        EditorAutosaveIoDispatcherInstance.dispatch(params, context, callbacks);
    },

    /**
     * Registers a request to be made on form submission.
     *
     * @param  {Node} form The forum node we will listen to.
     * @param  {Object} params Parameters for the IO request.
     * @return {Void}
     */
    autosaveIoOnSubmit: function(form, params) {
        EditorAutosaveIoDispatcherInstance.whenSubmit(form, params);
    }

};
Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosaveIo]);
