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
 * Dropdown handler for the Event monitor tool.
 *
 * @module     tool_monitor/dropdown
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
    const componentSelector = document.querySelector('[data-field="component"]');
    const eventSelector = document.querySelector('[data-field="eventname"]');

    const matchesComponent = (component, event) => event.startsWith(`\\${component}\\`);

    // Helper to fetch events for a component.
    const getEventsForComponent = (component) => {
        const events = Object.entries(JSON.parse(eventSelector.dataset.eventlist));
        return events.filter(([eventName], index) => {
            // Always return the Choose... option.
            if (index === 0) {
                return true;
            }
            return matchesComponent(component, eventName);
        });
    };

    // Helper to fetch the <option> elements for a compoment.
    const getEventOptionsForComponent = (component) => {
        return getEventsForComponent(component).map(([name, description]) => {
            const option = document.createElement('option');
            option.value = name;
            option.text = description;
            return option;
        });
    };

    // Change handler for the component selector.
    componentSelector.addEventListener('change', () => {
        eventSelector.innerHTML = '';
        getEventOptionsForComponent(componentSelector.value).forEach((option) => {
            eventSelector.options.add(option);
        });
        eventSelector.options.value = '';
    });

    // Set the initial value.
    // Rather than emptying the list and re-adding as the change handler does, remove any options that don't match.
    // This means that the current selection (when editing) is maintained.
    const initialCount = eventSelector.options.length;
    [...eventSelector.options].reverse().forEach((option, index) => {
        if (option.value === '') {
            // The first value is the "Choose..." pseudo-option.
            return;
        }
        if (!matchesComponent(componentSelector.value, option.value)) {
            eventSelector.options.remove(initialCount - index - 1);
        }
    });
};
