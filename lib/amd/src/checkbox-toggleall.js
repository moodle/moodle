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

    var registered = false;

    var events = {
        checkboxToggled: 'core/checkbox-toggleall:checkboxToggled',
    };

    var getToggleGroupElements = function(root, toggleGroup, exactMatch) {
        if (exactMatch) {
            return root.find('[data-action="toggle"][data-togglegroup="' + toggleGroup + '"]');
        } else {
            return root.find('[data-action="toggle"][data-togglegroup*="' + toggleGroup + '"]');
        }
    };

    var getAllSlaveCheckboxes = function(root, toggleGroup) {
        return getToggleGroupElements(root, toggleGroup).filter('[data-toggle="slave"]');
    };

    var getControlCheckboxes = function(root, toggleGroup, exactMatch) {
        return getToggleGroupElements(root, toggleGroup, exactMatch).filter('[data-toggle="master"]');
    };

    var getActionElements = function(root, toggleGroup) {
        return getToggleGroupElements(root, toggleGroup, true).filter('[data-toggle="action"]');
    };

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

        var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
        var checkedSlaves = slaves.filter(':checked');

        setMasterStates(root, toggleGroupName, targetState);

        // Set the slave checkboxes from the masters.
        slaves.prop('checked', targetState);
        // Trigger 'change' event to toggle other master checkboxes (e.g. parent master checkboxes) and action elements.
        slaves.trigger('change');

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            slaves: slaves,
            checkedSlaves: checkedSlaves,
            anyChecked: targetState,
        });
    };

    var toggleMastersFromSlaves = function(e) {
        var root = e.data.root;
        var target = $(e.target);

        var toggleGroups = target.data('togglegroup').split(' ');
        var toggleGroupLevels = [];
        var toggleGroupLevel = '';
        toggleGroups.forEach(function(toggleGroupName) {
            toggleGroupLevel += ' ' + toggleGroupName;
            toggleGroupLevels.push(toggleGroupLevel.trim());
        });

        toggleGroupLevels.forEach(function(toggleGroupName) {
            var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
            var checkedSlaves = slaves.filter(':checked');
            var targetState = (slaves.length === checkedSlaves.length);

            // Make sure to toggle the exact master checkbox.
            setMasterStates(root, toggleGroupName, targetState, true);

            // Enable action elements when there's at least one checkbox checked. Disable otherwise.
            setActionElementStates(root, toggleGroupName, !checkedSlaves.length);

            PubSub.publish(events.checkboxToggled, {
                root: root,
                toggleGroupName: toggleGroupName,
                slaves: slaves,
                checkedSlaves: checkedSlaves,
                anyChecked: !!checkedSlaves.length,
            });
        });
    };

    var setActionElementStates = function(root, toggleGroupName, disableActionElements) {
        getActionElements(root, toggleGroupName).prop('disabled', disableActionElements);
    };

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

    var registerListeners = function() {
        if (!registered) {
            registered = true;

            var root = $(document.body);
            root.on('click', '[data-action="toggle"][data-toggle="master"]', {root: root}, toggleSlavesFromMasters);
            root.on('change', '[data-action="toggle"][data-toggle="slave"]', {root: root}, toggleMastersFromSlaves);
        }
    };

    return {
        init: function() {
            registerListeners();
        },
        events: events,
    };
});
