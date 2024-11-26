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

define([], function() {

    /**
     * When embedded the communicator helps talk to the parent page.
     * This is a copy of the H5P.communicator, which we need to communicate in this context
     */
    var H5PEmbedCommunicator = function() {
        this._actionHandlers = {};
        this.registerEventListeners();
    };

    /** @type {Object} Maps actions to functions. */
    H5PEmbedCommunicator.prototype._actionHandlers = {};

    /**
     * Register action listener.
     *
     * @param {string} action What you are waiting for
     * @param {function} handler What you want done
     */
    H5PEmbedCommunicator.prototype.on = function(action, handler) {
        this._actionHandlers[action] = handler;
    };

    /**
     * Send a message to the all mighty father.
     *
     * @param {string} action
     * @param {Object} [data] payload
     */
    H5PEmbedCommunicator.prototype.send = function(action, data) {
        if (data === undefined) {
            data = {};
        }
        data.context = 'h5p';
        data.action = action;

        // Parent origin can be anything.
        window.parent.postMessage(data, '*');
    };


    /**
     * Register event listeners for the communicator.
     *
     * @method registerEventListeners
     */
    H5PEmbedCommunicator.prototype.registerEventListeners = function() {
        var self = this;
        // Register message listener.
        window.addEventListener('message', function receiveMessage(event) {
            if (window.parent !== event.source || event.data.context !== 'h5p') {
                return; // Only handle messages from parent and in the correct context.
            }

            if (self._actionHandlers[event.data.action] !== undefined) {
                self._actionHandlers[event.data.action](event.data);
            }
        }, false);
    };

    return new H5PEmbedCommunicator();

});
