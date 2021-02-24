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
 * Global registry of core events that can be triggered/listened for.
 *
 * @module     core/event
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */

import {notifyEditorContentRestored} from 'core_editor/events';
import {notifyFormSubmittedByJavascript} from 'core_form/events';

// These are only imported for legacy.
import $ from 'jquery';
import Y from 'core/yui';

// Public variables and functions.
// These are AMD only events - no backwards compatibility for new things.
const Events = {
    FORM_FIELD_VALIDATION: "core_form-field-validation"
};

/**
 * Load the legacy YUI module which defines events in M.core.event and return it.
 *
 * @method getLegacyEvents
 * @return {Promise}
 */
const getLegacyEvents = () => {
    const result = $.Deferred();
    Y.use('event', 'moodle-core-event', function() {
        result.resolve(window.M.core.event);
    });
    return result.promise();
};

/**
 * Trigger an event using both JQuery and YUI
 *
 * @method notifyFilterContentUpdated
 * @param {string|JQuery} nodes - Selector or list of elements that were inserted.
 */
const notifyFilterContentUpdated = nodes => {
    nodes = $(nodes);
    Y.use('event', 'moodle-core-event', function(Y) {
        // Trigger it the JQuery way.
        $(document).trigger(M.core.event.FILTER_CONTENT_UPDATED, [nodes]);

        // Create a YUI NodeList from our JQuery Object.
        var yuiNodes = new Y.NodeList(nodes.get());

        // And again for YUI.
        Y.fire(M.core.event.FILTER_CONTENT_UPDATED, {nodes: yuiNodes});
    });
};

/**
 * Get a curried function to warn that a function has been moved and renamed
 *
 * @param   {String} oldFunctionName
 * @param   {String} newModule
 * @param   {String} newFunctionName
 * @param   {Function} newFunctionRef
 * @returns {Function}
 */
const getRenamedLegacyFunction = (oldFunctionName, newModule, newFunctionName, newFunctionRef) => (...args) => {
    window.console.warn(
        `The core/event::${oldFunctionName}() function has been moved to ${newModule}::${newFunctionName}. ` +
        `Please update your code to use the new module.`
    );

    return newFunctionRef(...args);
};

export default {
    Events,
    getLegacyEvents,

    notifyEditorContentRestored: getRenamedLegacyFunction(
        'notifyEditorContentRestored',
        'core_editor/events',
        'notifyEditorContentRestored',
        notifyEditorContentRestored
    ),

    notifyFilterContentUpdated,

    notifyFormSubmitAjax: getRenamedLegacyFunction(
        'notifyFormSubmitAjax',
        'core_form/events',
        'notifyFormSubmittedByJavascript',
        notifyFormSubmittedByJavascript
    ),
};
