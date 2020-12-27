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
 * A module to help with toggle select/deselect all.
 *
 * @module     core/checkbox-toggleall
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/pubsub'], function($, PubSub) {

    /**
     * Whether event listeners have already been registered.
     *
     * @private
     * @type {boolean}
     */
    var registered = false;

    /**
     * List of custom events that this module publishes.
     *
     * @private
     * @type {{checkboxToggled: string}}
     */
    var events = {
        checkboxToggled: 'core/checkbox-toggleall:checkboxToggled',
    };

    /**
     * Fetches elements that are member of a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name that we're searching form.
     * @param {boolean} exactMatch Whether we want an exact match we just want to match toggle groups that start with the given
     *                             toggle group name.
     * @returns {jQuery} The elements matching the given toggle group.
     */
    var getToggleGroupElements = function(root, toggleGroup, exactMatch) {
        if (exactMatch) {
            return root.find('[data-action="toggle"][data-togglegroup="' + toggleGroup + '"]');
        } else {
            return root.find('[data-action="toggle"][data-togglegroup^="' + toggleGroup + '"]');
        }
    };

    /**
     * Fetches the slave checkboxes for a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name.
     * @returns {jQuery} The slave checkboxes belonging to the toggle group.
     */
    var getAllSlaveCheckboxes = function(root, toggleGroup) {
        return getToggleGroupElements(root, toggleGroup, false).filter('[data-toggle="slave"]');
    };

    /**
     * Fetches the master elements (checkboxes or buttons) that control the slave checkboxes in a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name.
     * @param {boolean} exactMatch
     * @returns {jQuery} The control elements belonging to the toggle group.
     */
    var getControlCheckboxes = function(root, toggleGroup, exactMatch) {
        return getToggleGroupElements(root, toggleGroup, exactMatch).filter('[data-toggle="master"]');
    };

    /**
     * Fetches the action elements that perform actions on the selected checkboxes in a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name.
     * @returns {jQuery} The action elements belonging to the toggle group.
     */
    var getActionElements = function(root, toggleGroup) {
        return getToggleGroupElements(root, toggleGroup, true).filter('[data-toggle="action"]');
    };

    /**
     * Toggles the slave checkboxes in a given toggle group when a master element in that toggle group is toggled.
     *
     * @private
     * @param {Object} e The event object.
     */
    var toggleSlavesFromMasters = function(e) {
        var root = e.data.root;
        var target = $(e.target);

        var toggleGroupName = target.data('togglegroup');
        var targetState;
        if (target.is(':checkbox')) {
            targetState = target.is(':checked');
        } else {
            targetState = target.data('checkall') === 1;
        }

        toggleSlavesToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the slave checkboxes from the masters.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     */
    var updateSlavesFromMasterState = function(root, toggleGroupName) {
        // Normalise to jQuery Object.
        root = $(root);

        var target = getControlCheckboxes(root, toggleGroupName, false);
        var targetState;
        if (target.is(':checkbox')) {
            targetState = target.is(':checked');
        } else {
            targetState = target.data('checkall') === 1;
        }

        toggleSlavesToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the master checkboxes and action elements in a given toggle group.
     *
     * @param {jQuery} root The root jQuery element.
     * @param {String} toggleGroupName The name of the toggle group
     */
    var toggleMastersAndActionElements = function(root, toggleGroupName) {
        var toggleGroupSlaves = getAllSlaveCheckboxes(root, toggleGroupName);
        if (toggleGroupSlaves.length > 0) {
            var toggleGroupCheckedSlaves = toggleGroupSlaves.filter(':checked');
            var targetState = toggleGroupSlaves.length === toggleGroupCheckedSlaves.length;

            // Make sure to toggle the exact master checkbox in the given toggle group.
            setMasterStates(root, toggleGroupName, targetState, true);
            // Enable the action elements if there's at least one checkbox checked in the given toggle group.
            // Disable otherwise.
            setActionElementStates(root, toggleGroupName, !toggleGroupCheckedSlaves.length);
        }
    };

    /**
     * Returns an array containing every toggle group level of a given toggle group.
     *
     * @param {String} toggleGroupName The name of the toggle group
     * @return {Array} toggleGroupLevels Array that contains every toggle group level of a given toggle group
     */
    var getToggleGroupLevels = function(toggleGroupName) {
        var toggleGroups = toggleGroupName.split(' ');
        var toggleGroupLevels = [];
        var toggleGroupLevel = '';

        toggleGroups.forEach(function(toggleGroupName) {
            toggleGroupLevel += ' ' + toggleGroupName;
            toggleGroupLevels.push(toggleGroupLevel.trim());
        });

        return toggleGroupLevels;
    };

    /**
     * Toggles the slave checkboxes to a specific state.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     * @param {Bool} targetState
     */
    var toggleSlavesToState = function(root, toggleGroupName, targetState) {
        var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
        // Set the slave checkboxes from the masters and manually trigger the native 'change' event.
        slaves.prop('checked', targetState).trigger('change');
        // Get all checked slaves after the change of state.
        var checkedSlaves = slaves.filter(':checked');

        // Toggle the master checkbox in the given toggle group.
        setMasterStates(root, toggleGroupName, targetState, false);
        // Enable the action elements if there's at least one checkbox checked in the given toggle group. Disable otherwise.
        setActionElementStates(root, toggleGroupName, !checkedSlaves.length);

        // Get all toggle group levels and toggle accordingly all parent master checkboxes and action elements from each
        // level. Exclude the given toggle group (toggleGroupName) as the master checkboxes and action elements from this
        // level have been already toggled.
        var toggleGroupLevels = getToggleGroupLevels(toggleGroupName)
            .filter(toggleGroupLevel => toggleGroupLevel !== toggleGroupName);

        toggleGroupLevels.forEach(function(toggleGroupLevel) {
            // Toggle the master checkboxes action elements in the given toggle group level.
            toggleMastersAndActionElements(root, toggleGroupLevel);
        });

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            slaves: slaves,
            checkedSlaves: checkedSlaves,
            anyChecked: targetState,
        });
    };

    /**
     * Set the state for an entire group of checkboxes.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     * @param {Bool} targetState
     */
    var setGroupState = function(root, toggleGroupName, targetState) {
        // Normalise to jQuery Object.
        root = $(root);

        // Set the master and slaves.
        setMasterStates(root, toggleGroupName, targetState, true);
        toggleSlavesToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the master checkboxes in a given toggle group when all or none of the slave checkboxes in the same toggle group
     * have been selected.
     *
     * @private
     * @param {Object} e The event object.
     */
    var toggleMastersFromSlaves = function(e) {
        var root = e.data.root;
        var target = $(e.target);
        var toggleGroupName = target.data('togglegroup');
        var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
        var checkedSlaves = slaves.filter(':checked');

        // Get all toggle group levels for the given toggle group and toggle accordingly all master checkboxes
        // and action elements from each level.
        var toggleGroupLevels = getToggleGroupLevels(toggleGroupName);
        toggleGroupLevels.forEach(function(toggleGroupLevel) {
            // Toggle the master checkboxes action elements in the given toggle group level.
            toggleMastersAndActionElements(root, toggleGroupLevel);
        });

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            slaves: slaves,
            checkedSlaves: checkedSlaves,
            anyChecked: !!checkedSlaves.length,
        });
    };

    /**
     * Enables or disables the action elements.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroupName The toggle group name of the action element(s).
     * @param {boolean} disableActionElements Whether to disable or to enable the action elements.
     */
    var setActionElementStates = function(root, toggleGroupName, disableActionElements) {
        getActionElements(root, toggleGroupName).prop('disabled', disableActionElements);
    };

    /**
     * Selects or deselects the master elements.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroupName The toggle group name of the master element(s).
     * @param {boolean} targetState Whether to select (true) or deselect (false).
     * @param {boolean} exactMatch Whether to do an exact match for the toggle group name or not.
     */
    var setMasterStates = function(root, toggleGroupName, targetState, exactMatch) {
        // Set the master checkboxes value and ARIA labels..
        var masters = getControlCheckboxes(root, toggleGroupName, exactMatch);
        masters.prop('checked', targetState);
        masters.each(function(i, masterElement) {
            masterElement = $(masterElement);

            var targetString;
            if (targetState) {
                targetString = masterElement.data('toggle-deselectall');
            } else {
                targetString = masterElement.data('toggle-selectall');
            }

            if (masterElement.is(':checkbox')) {
                var masterLabel = root.find('[for="' + masterElement.attr('id') + '"]');
                if (masterLabel.length) {
                    if (masterLabel.html() !== targetString) {
                        masterLabel.html(targetString);
                    }
                }
            } else {
                masterElement.text(targetString);
                // Set the checkall data attribute.
                masterElement.data('checkall', targetState ? 0 : 1);
            }
        });
    };

    /**
     * Registers the event listeners.
     *
     * @private
     */
    var registerListeners = function() {
        if (!registered) {
            registered = true;

            var root = $(document.body);
            root.on('click', '[data-action="toggle"][data-toggle="master"]', {root: root}, toggleSlavesFromMasters);
            root.on('click', '[data-action="toggle"][data-toggle="slave"]', {root: root}, toggleMastersFromSlaves);
        }
    };

    return {
        init: function() {
            registerListeners();
        },
        events: events,
        setGroupState: setGroupState,
        updateSlavesFromMasterState: updateSlavesFromMasterState,
    };
});
