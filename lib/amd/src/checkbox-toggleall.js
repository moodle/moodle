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
     * Fetches the target checkboxes for a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name.
     * @returns {jQuery} The target checkboxes belonging to the toggle group.
     */
    var getAllTargetCheckboxes = function(root, toggleGroup) {
        const targets = getToggleGroupElements(root, toggleGroup, false).filter('[data-toggle="target"]');

        // TODO: Remove this backward compatibility code in Moodle 6.0.
        const oldTargets = getToggleGroupElements(root, toggleGroup, false).filter('[data-toggle="slave"]');
        if (Array.isArray(oldTargets) && oldTargets.length > 0) {
            window.console.warn('The use of data-toggle="slave" is deprecated. Please use data-toggle="target" instead.');
            targets.concat(oldTargets);
        }
        // End of backward compatibility code.

        return targets;
    };

    /**
     * Fetches the toggler elements (checkboxes or buttons) that control the target checkboxes in a given toggle group.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroup The toggle group name.
     * @param {boolean} exactMatch
     * @returns {jQuery} The control elements belonging to the toggle group.
     */
    var getControlCheckboxes = function(root, toggleGroup, exactMatch) {
        const togglers = getToggleGroupElements(root, toggleGroup, exactMatch).filter('[data-toggle="toggler"]');

        // TODO: Remove this backward compatibility code in Moodle 6.0.
        const oldTogglers = getToggleGroupElements(root, toggleGroup, exactMatch).filter('[data-toggle="master"]');
        if (Array.isArray(oldTogglers) && oldTogglers.length > 0) {
            window.console.warn('The use of data-toggle="master" is deprecated. Please use data-toggle="toggler" instead.');
            togglers.concat(oldTogglers);
        }
        // End of backward compatibility code.

        return togglers;
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
     * Toggles the target checkboxes in a given toggle group when a toggler element in that toggle group is toggled.
     *
     * @private
     * @param {Object} e The event object.
     */
    var toggleTargetsFromTogglers = function(e) {
        var root = e.data.root;
        var target = $(e.target);

        var toggleGroupName = target.data('togglegroup');
        var targetState;
        if (target.is(':checkbox')) {
            targetState = target.is(':checked');
        } else {
            targetState = target.data('checkall') === 1;
        }

        toggleTargetsToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the target checkboxes from the togglers.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     * @deprecated since Moodle 5.0.
     */
    var updateSlavesFromMasterState = function(root, toggleGroupName) {
        window.console.warn(
            'The use of updateSlavesFromMasterState is deprecated. Please use updateTargetsFromTogglerState instead.'
        );
        updateTargetsFromTogglerState(root, toggleGroupName);
    };

    /**
     * Toggles the target checkboxes from the togglers.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     */
    var updateTargetsFromTogglerState = function(root, toggleGroupName) {
        // Normalise to jQuery Object.
        root = $(root);

        var target = getControlCheckboxes(root, toggleGroupName, false);
        var targetState;
        if (target.is(':checkbox')) {
            targetState = target.is(':checked');
        } else {
            targetState = target.data('checkall') === 1;
        }

        toggleTargetsToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the toggler checkboxes and action elements in a given toggle group.
     *
     * @param {jQuery} root The root jQuery element.
     * @param {String} toggleGroupName The name of the toggle group
     */
    var toggleTogglersAndActionElements = function(root, toggleGroupName) {
        var toggleGroupTargets = getAllTargetCheckboxes(root, toggleGroupName);
        if (toggleGroupTargets.length > 0) {
            var toggleGroupCheckedTargets = toggleGroupTargets.filter(':checked');
            var targetState = toggleGroupTargets.length === toggleGroupCheckedTargets.length;

            // Make sure to toggle the exact toggler checkbox in the given toggle group.
            setTogglerStates(root, toggleGroupName, targetState, true);
            // Enable the action elements if there's at least one checkbox checked in the given toggle group.
            // Disable otherwise.
            setActionElementStates(root, toggleGroupName, !toggleGroupCheckedTargets.length);
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
     * Toggles the target checkboxes to a specific state.
     *
     * @param {HTMLElement} root
     * @param {String} toggleGroupName
     * @param {Bool} targetState
     */
    var toggleTargetsToState = function(root, toggleGroupName, targetState) {
        var targets = getAllTargetCheckboxes(root, toggleGroupName);
        // Set the target checkboxes from the togglers and manually trigger the native 'change' event.
        targets.prop('checked', targetState).trigger('change');
        // Get all checked targets after the change of state.
        var checkedTargets = targets.filter(':checked');

        // Toggle the toggler checkbox in the given toggle group.
        setTogglerStates(root, toggleGroupName, targetState, false);
        // Enable the action elements if there's at least one checkbox checked in the given toggle group. Disable otherwise.
        setActionElementStates(root, toggleGroupName, !checkedTargets.length);

        // Get all toggle group levels and toggle accordingly all parent toggler checkboxes and action elements from each
        // level. Exclude the given toggle group (toggleGroupName) as the toggler checkboxes and action elements from this
        // level have been already toggled.
        var toggleGroupLevels = getToggleGroupLevels(toggleGroupName)
            .filter(toggleGroupLevel => toggleGroupLevel !== toggleGroupName);

        toggleGroupLevels.forEach(function(toggleGroupLevel) {
            // Toggle the toggler checkboxes action elements in the given toggle group level.
            toggleTogglersAndActionElements(root, toggleGroupLevel);
        });

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            targets: targets,
            checkedTargets: checkedTargets,
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

        // Set the toggler and targets.
        setTogglerStates(root, toggleGroupName, targetState, true);
        toggleTargetsToState(root, toggleGroupName, targetState);
    };

    /**
     * Toggles the toggler checkboxes in a given toggle group when all or none of the target checkboxes in the same toggle group
     * have been selected.
     *
     * @private
     * @param {Object} e The event object.
     */
    var toggleTogglersFromTargets = function(e) {
        var root = e.data.root;
        var target = $(e.target);
        var toggleGroupName = target.data('togglegroup');
        var targets = getAllTargetCheckboxes(root, toggleGroupName);
        var checkedTargets = targets.filter(':checked');

        // Get all toggle group levels for the given toggle group and toggle accordingly all toggler checkboxes
        // and action elements from each level.
        var toggleGroupLevels = getToggleGroupLevels(toggleGroupName);
        toggleGroupLevels.forEach(function(toggleGroupLevel) {
            // Toggle the toggler checkboxes action elements in the given toggle group level.
            toggleTogglersAndActionElements(root, toggleGroupLevel);
        });

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            targets: targets,
            checkedTargets: checkedTargets,
            anyChecked: !!checkedTargets.length,
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
     * Selects or deselects the toggler elements.
     *
     * @private
     * @param {jQuery} root The root jQuery element.
     * @param {string} toggleGroupName The toggle group name of the toggler element(s).
     * @param {boolean} targetState Whether to select (true) or deselect (false).
     * @param {boolean} exactMatch Whether to do an exact match for the toggle group name or not.
     */
    var setTogglerStates = function(root, toggleGroupName, targetState, exactMatch) {
        // Set the toggler checkboxes value and ARIA labels..
        var togglers = getControlCheckboxes(root, toggleGroupName, exactMatch);
        togglers.prop('checked', targetState);
        togglers.each(function(i, togglerElement) {
            togglerElement = $(togglerElement);

            var targetString;
            if (targetState) {
                targetString = togglerElement.data('toggle-deselectall');
            } else {
                targetString = togglerElement.data('toggle-selectall');
            }

            if (togglerElement.is(':checkbox')) {
                var togglerLabel = root.find('[for="' + togglerElement.attr('id') + '"]');
                if (togglerLabel.length) {
                    if (togglerLabel.html() !== targetString) {
                        togglerLabel.html(targetString);
                    }
                }
            } else {
                togglerElement.text(targetString);
                // Set the checkall data attribute.
                togglerElement.data('checkall', targetState ? 0 : 1);
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
            root.on('click', '[data-action="toggle"][data-toggle="toggler"]', {root: root}, toggleTargetsFromTogglers);
            root.on('click', '[data-action="toggle"][data-toggle="target"]', {root: root}, toggleTogglersFromTargets);

            // TODO: Remove this backward compatibility code in Moodle 6.0.
            const oldTogglers = document.querySelectorAll('[data-action="toggle"][data-toggle="master"]');
            if (oldTogglers.length > 0) {
                window.console.warn('The use of data-toggle="master" is deprecated. Please use data-toggle="toggler" instead.');
                root.on('click', '[data-action="toggle"][data-toggle="master"]', {root: root}, toggleTargetsFromTogglers);
            }
            const oldTargets = document.querySelectorAll('[data-action="toggle"][data-toggle="slave"]');
            if (oldTargets.length > 0) {
                window.console.warn('The use of data-toggle="slave" is deprecated. Please use data-toggle="target" instead.');
                root.on('click', '[data-action="toggle"][data-toggle="slave"]', {root: root}, toggleTogglersFromTargets);
            }
            // End of backward compatibility code.
        }
    };

    return {
        init: function() {
            registerListeners();
        },
        events: events,
        setGroupState: setGroupState,
        updateSlavesFromMasterState: updateSlavesFromMasterState, // TODO: Remove this deprecated method export in Moodle 6.0.
        updateTargetsFromTogglerState: updateTargetsFromTogglerState,
    };
});
