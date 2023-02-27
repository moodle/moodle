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
 * Competency rule base module.
 *
 * @module     tool_lp/competency_rule
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * Competency rule abstract class.
     *
     * Any competency rule should extend this object. The event 'change' should be
     * triggered on the instance when the configuration has changed. This will allow
     * the components using the rule to gather the config, or check its validity.
     *
     * this._triggerChange();
     *
     * @param {Tree} tree The competency tree.
     */
    var Rule = function(tree) {
        this._eventNode = $('<div>');
        this._ready = $.Deferred();
        this._tree = tree;
    };

    /** @property {Object} The current competency. */
    Rule.prototype._competency = null;
    /** @property {Node} The node we attach the events to. */
    Rule.prototype._eventNode = null;
    /** @property {Promise} Resolved when the object is ready. */
    Rule.prototype._ready = null;
    /** @property {Tree} The competency tree. */
    Rule.prototype._tree = null;

    /**
     * Whether or not the current competency can be configured using this rule.
     *
     * @return {Boolean}
     * @method canConfig
     */
    Rule.prototype.canConfig = function() {
        return this._tree.hasChildren(this._competency.id);
    };

    /**
     * The config established by this rule.
     *
     * To override in subclasses when relevant.
     *
     * @return {String|null}
     * @method getConfig
     */
    Rule.prototype.getConfig = function() {
        return null;
    };

    // eslint-disable-line valid-jsdoc
    /**
     * Return the type of the module.
     *
     * @return {String}
     * @method getType
     */
    // eslint-enable-line valid-jsdoc
    Rule.prototype.getType = function() {
        throw new Error('Not implemented');
    };

    /**
     * The init process.
     *
     * Do not override this, instead override _load.
     *
     * @return {Promise} Revoled when the plugin is initialised.
     * @method init
     */
    Rule.prototype.init = function() {
        return this._load();
    };

    /**
     * Callback to inject the template.
     *
     * @returns {Promise} Resolved when done.
     * @method injectTemplate
     */
    Rule.prototype.injectTemplate = function() {
        return $.Deferred().reject().promise();
    };

    /**
     * Whether or not the current config is valid.
     *
     * Plugins should override this.
     *
     * @return {Boolean}
     * @method _isValid
     */
    Rule.prototype.isValid = function() {
        return false;
    };

    /**
     * Load the class.
     *
     * @return {Promise}
     * @method _load
     * @protected
     */
    Rule.prototype._load = function() {
        return $.when();
    };

    /**
     * Register an event listener.
     *
     * @param {String} type The event type.
     * @param {Function} handler The event listener.
     * @method on
     */
    Rule.prototype.on = function(type, handler) {
        this._eventNode.on(type, handler);
    };

    /**
     * Sets the current competency.
     *
     * @param {Competency} competency
     * @method setTargetCompetency
     */
    Rule.prototype.setTargetCompetency = function(competency) {
        this._competency = competency;
    };

    /**
     * Trigger an event.
     *
     * @param {String} type The type of event.
     * @param {Object} data The data to pass to the listeners.
     * @method _trigger
     * @protected
     */
    Rule.prototype._trigger = function(type, data) {
        this._eventNode.trigger(type, [data]);
    };

    /**
     * Trigger the change event.
     *
     * @method _triggerChange
     * @protected
     */
    Rule.prototype._triggerChange = function() {
        this._trigger('change', this);
    };

    return /** @alias module:tool_lp/competency_rule */ Rule;

});
