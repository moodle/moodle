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

    var getAllCheckboxes = function(root, toggleGroup) {
        return root.find('[data-action="toggle"][data-togglegroup="' + toggleGroup + '"]');
    };

    var getAllSlaveCheckboxes = function(root, toggleGroup) {
        return getAllCheckboxes(root, toggleGroup).filter('[data-toggle="slave"]');
    };

    var getControlCheckboxes = function(root, toggleGroup) {
        return getAllCheckboxes(root, toggleGroup).filter('[data-toggle="master"]');
    };

    var toggleSlavesFromMasters = function(e) {
        var root = e.data.root;
        var target = $(e.target);

        var toggleGroupName = target.data('togglegroup');
        var targetState = target.is(':checked');

        var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
        var checkedSlaves = slaves.filter(':checked');

        setMasterStates(root, toggleGroupName, targetState);

        // Set the slave checkboxes from the masters.
        slaves.prop('checked', targetState);

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

        var toggleGroupName = target.data('togglegroup');

        var slaves = getAllSlaveCheckboxes(root, toggleGroupName);
        var checkedSlaves = slaves.filter(':checked');
        var targetState = (slaves.length === checkedSlaves.length);

        setMasterStates(root, toggleGroupName, targetState);

        PubSub.publish(events.checkboxToggled, {
            root: root,
            toggleGroupName: toggleGroupName,
            slaves: slaves,
            checkedSlaves: checkedSlaves,
            anyChecked: !!checkedSlaves.length,
        });
    };

    var setMasterStates = function(root, toggleGroupName, targetState) {
        // Set the master checkboxes value and ARIA labels..
        var masters = getControlCheckboxes(root, toggleGroupName);
        masters.prop('checked', targetState);
        masters.each(function(i, masterCheckbox) {
            masterCheckbox = $(masterCheckbox);
            var masterLabel = root.find('[for="' + masterCheckbox.attr('id') + '"]');
            var targetString;
            if (masterLabel.length) {
                if (targetState) {
                    targetString = masterCheckbox.data('toggle-deselectall');
                } else {
                    targetString = masterCheckbox.data('toggle-selectall');
                }

                if (masterLabel.html() !== targetString) {
                    masterLabel.html(targetString);
                }
            }
        });
    };

    var registerListeners = function() {
        if (!registered) {
            registered = true;

            var root = $(document.body);
            root.on('change', '[data-action="toggle"][data-toggle="master"]', {root: root}, toggleSlavesFromMasters);
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
