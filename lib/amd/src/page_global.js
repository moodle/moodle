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
 * Provide global helper code to enhance page elements.
 *
 * @module     core/page_global
 * @package    core
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/str',
],
function(
    $,
    CustomEvents,
    Str
) {

    /**
     * Add an event handler for dropdown menus that wish to show their active item
     * in the dropdown toggle element.
     *
     * By default the handler will add the "active" class to the selected dropdown
     * item and set it's text as the HTML for the dropdown toggle.
     *
     * The behaviour of this handler is controlled by adding data attributes to
     * the HTML and requires the typically Bootstrap dropdown markup.
     *
     * data-show-active-item - Add to the .dropdown-menu element to enable default
     *                         functionality.
     * data-skip-active-class - Add to the .dropdown-menu to prevent this code from
     *                          adding the active class to the dropdown items
     * data-active-item-text - Add to an element within the data-toggle="dropdown" element
     *                         to use it as the active option text placeholder otherwise the
     *                         data-toggle="dropdown" element itself will be used.
     * data-active-item-button-aria-label-components - String components to set the aria
     *                         lable on the dropdown button. The string will be given the
     *                         active item text.
     */
    var initActionOptionDropdownHandler = function() {
        var body = $('body');

        CustomEvents.define(body, [CustomEvents.events.activate]);
        body.on(CustomEvents.events.activate, '[data-show-active-item]', function(e) {
            // The dropdown item that the user clicked on.
            var option = $(e.target).closest('.dropdown-item');
            // The dropdown menu element.
            var menuContainer = option.closest('[data-show-active-item]');

            if (!option.hasClass('dropdown-item')) {
                // Ignore non Bootstrap dropdowns.
                return;
            }

            if (option.hasClass('active')) {
                // If it's already active then we don't need to do anything.
                return;
            }

            // Clear the active class from all other options.
            var dropdownItems = menuContainer.find('.dropdown-item');
            dropdownItems.removeClass('active');
            dropdownItems.removeAttr('aria-current');

            if (!menuContainer.attr('data-skip-active-class')) {
                // Make this option active unless configured to ignore it.
                // Some code, for example the Bootstrap tabs, may want to handle
                // adding the active class itself.
                option.addClass('active');
            }

            // Update aria attribute for active item.
            option.attr('aria-current', true);

            var activeOptionText = option.text();
            var dropdownToggle = menuContainer.parent().find('[data-toggle="dropdown"]');
            var dropdownToggleText = dropdownToggle.find('[data-active-item-text]');

            if (dropdownToggleText.length) {
                // We have a specific placeholder for the active item text so
                // use that.
                dropdownToggleText.html(activeOptionText);
            } else {
                // Otherwise just replace all of the toggle text with the active item.
                dropdownToggle.html(activeOptionText);
            }

            var activeItemAriaLabelComponent = menuContainer.attr('data-active-item-button-aria-label-components');
            if (activeItemAriaLabelComponent) {
                // If we have string components for the aria label then load the string
                // and set the label on the dropdown toggle.
                var strParams = activeItemAriaLabelComponent.split(',');
                strParams.push(activeOptionText);

                Str.get_string(strParams[0].trim(), strParams[1].trim(), strParams[2].trim())
                    .then(function(string) {
                        dropdownToggle.attr('aria-label', string);
                        return string;
                    })
                    .catch(function() {
                        // Silently ignore that we couldn't load the string.
                        return false;
                    });
            }
        });
    };

    /**
     * Initialise the global helper functions.
     */
    var init = function() {
        initActionOptionDropdownHandler();
    };

    return {
        init: init
    };
});
