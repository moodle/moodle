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
 * Javascript popover for the `core_calendar` subsystem.
 *
 * @module core_calendar/popover
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 4.0
 */

import Popover from 'theme_boost/bootstrap/popover';
import * as CalendarSelectors from 'core_calendar/selectors';

/**
 * Check if we are allowing to enable the popover or not.
 * @param {Element} dateContainer
 * @returns {boolean}
 */
const isPopoverAvailable = (dateContainer) => {
    return window.getComputedStyle(dateContainer.querySelector(CalendarSelectors.elements.dateContent)).display === 'none';
};

const isPopoverConfigured = new Map();
const showPopover = target => {
    const dateContainer = target.closest(CalendarSelectors.elements.dateContainer);
    if (!isPopoverConfigured.has(dateContainer)) {
        const config = {
            trigger: 'manual',
            placement: 'top',
            html: true,
            title: dateContainer.dataset.title,
            content: () => {
                const source = dateContainer.querySelector(CalendarSelectors.elements.dateContent);
                return source ? source.querySelector('.hidden').innerHTML : '';
            },
            'animation': false,
        };
        new Popover(target, config);

        isPopoverConfigured.set(dateContainer, true);
    }

    if (isPopoverAvailable(dateContainer)) {
        Popover.getInstance(target).show();
        target.addEventListener('mouseleave', hidePopover);
        target.addEventListener('focusout', hidePopover);
        // Set up the hide function to the click event type.
        target.addEventListener('click', hidePopover);
    }
};

const hidePopover = e => {
    const target = e.target;
    const dateContainer = e.target.closest(CalendarSelectors.elements.dateContainer);
    if (!dateContainer) {
        return;
    }
    if (isPopoverConfigured.has(dateContainer)) {
        const isTargetActive = target.contains(document.activeElement);
        const isTargetHover = target.matches(':hover');

        // Checks if a target element is clicked or pressed.
        const isTargetClicked = document.activeElement.contains(target);

        let removeListener = true;
        if (!isTargetActive && !isTargetHover) {
            Popover.getOrCreateInstance(target).hide();
        } else if (isTargetClicked) {
            Popover.getOrCreateInstance(document.activeElement).hide();
        } else {
            removeListener = false;
        }

        if (removeListener) {
            target.removeEventListener('mouseleave', hidePopover);
            target.removeEventListener('focusout', hidePopover);
            target.removeEventListener('click', hidePopover);
        }
    }
};

/**
 * Register events for date container.
 */
const registerEventListeners = () => {
    const showPopoverHandler = (e) => {
        const dayLink = e.target.closest(CalendarSelectors.links.dayLink);
        if (!dayLink) {
            return;
        }

        e.preventDefault();
        showPopover(dayLink);
    };

    document.addEventListener('mouseover', showPopoverHandler);
    document.addEventListener('focusin', showPopoverHandler);
};

let listenersRegistered = false;
if (!listenersRegistered) {
    registerEventListeners();
    listenersRegistered = true;
}
