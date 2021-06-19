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
 * Event base javascript module.
 *
 * @module     tool_lp/event_base
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /**
     * Base class.
     */
    var Base = function() {
        this._eventNode = $('<div></div>');
    };

    /** @property {Node} The node we attach the events to. */
    Base.prototype._eventNode = null;

    /**
     * Register an event listener.
     *
     * @param {String} type The event type.
     * @param {Function} handler The event listener.
     * @method on
     */
    Base.prototype.on = function(type, handler) {
        this._eventNode.on(type, handler);
    };

    /**
     * Trigger an event.
     *
     * @param {String} type The type of event.
     * @param {Object} data The data to pass to the listeners.
     * @method _trigger
     */
    Base.prototype._trigger = function(type, data) {
        this._eventNode.trigger(type, [data]);
    };

    return /** @alias module:tool_lp/event_base */ Base;
});
