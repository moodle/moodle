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
 * Enhancements to Bootstrap components for accessibility.
 *
 * @module     theme_boost/aria
 * @copyright  2018 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    return {
        init: function() {
            // Drop downs from bootstrap don't support keyboard accessibility by default.
            var focusEnd = false,
                setFocusEnd = function() {
                    focusEnd = true;
                },
                getFocusEnd = function() {
                    var result = focusEnd;
                    focusEnd = false;
                    return result;
                };

            // Special handling for "up" keyboard control.
            $('[data-toggle="dropdown"]').keydown(function (e) {
                var trigger = e.which || e.keyCode,
                    expanded;

                // Up key opens the menu at the end.
                if (trigger == 38) {
                    // Focus the end of the menu, not the beginning.
                    setFocusEnd();
                }

                // Escape key only closes the menu, it doesn't open it.
                if (trigger == 27) {
                    expanded = $(e.target).attr('aria-expanded');
                    e.preventDefault();
                    if (expanded == "false") {
                        $(e.target).click();
                    }
                }

                // Space key or Enter key opens the menu.
                if (trigger == 32 || trigger == 13) {
                    // Cancel random scroll.
                    e.preventDefault();
                    // Open the menu instead.
                    $(e.target).click();
                }
            });

            // Special handling for navigation keys when menu is open.
            var shiftFocus = function(element) {
                var delayedFocus = function() {
                    $(this).focus();
                }.bind(element);
                setTimeout(delayedFocus, 50);
            };

            $('.dropdown').on('shown.bs.dropdown', function(e) {
                // We need to focus on the first menuitem.
                var menu = $(e.target).find('[role="menu"]'),
                    menuItems = false,
                    foundMenuItem = false;

                if (menu) {
                    menuItems = $(menu).find('[role="menuitem"]');
                }
                if (menuItems && menuItems.length > 0) {
                    if (getFocusEnd()) {
                        foundMenuItem = menuItems[menuItems.length - 1];
                    } else {
                        // The first menu entry, pretty reasonable.
                        foundMenuItem = menuItems[0];
                    }
                }
                if (foundMenuItem) {
                    shiftFocus(foundMenuItem);
                }
            });
            // Search for menu items.
            $('.dropdown [role="menu"] [role="menuitem"]').keypress(function (e) {
                var trigger = String.fromCharCode(e.which || e.keyCode),
                    menu = $(e.target).closest('[role="menu"]'),
                    i = 0,
                    menuItems = false,
                    item;

                if (!menu) {
                    return;
                }
                menuItems = $(menu).find('[role="menuitem"]');
                if (!menuItems) {
                    return;
                }

                trigger = trigger.toLowerCase();
                for (i = 0; i < menuItems.length; i++) {
                    item = $(menuItems[i]);
                    if ((item.text().trim().indexOf(trigger) == 0) ||
                            (item.text().trim().indexOf(trigger.toUpperCase()) == 0)) {
                        shiftFocus(item);
                        break;
                    }
                }
            });

            // Keyboard navigation for arrow keys, home and end keys.
            $('.dropdown [role="menu"] [role="menuitem"]').keydown(function (e) {
                var trigger = e.which || e.keyCode,
                    next = false,
                    menu = $(e.target).closest('[role="menu"]'),
                    i = 0,
                    menuItems = false;
                if (!menu) {
                    return;
                }
                menuItems = $(menu).find('[role="menuitem"]');
                if (!menuItems) {
                    return;
                }
                // Down.
                if (trigger == 40) {
                    for (i = 0; i < menuItems.length - 1; i++) {
                        if (menuItems[i] == e.target) {
                            next = menuItems[i + 1];
                        }
                    }
                    if (!next) {
                        // Wrap to first item.
                        trigger = 36;
                    }
                }
                // Up.
                if (trigger == 38) {
                    for (i = 1; i < menuItems.length; i++) {
                        if (menuItems[i] == e.target) {
                            next = menuItems[i - 1];
                        }
                    }
                    if (!next) {
                        // Wrap to last item.
                        trigger = 35;
                    }
                }
                // Home.
                if (trigger == 36) {
                    next = menuItems[0];
                }
                // End.
                if (trigger == 35) {
                    next = menuItems[menuItems.length - 1];
                }
                if (next) {
                    e.preventDefault();
                    shiftFocus(next);
                }
                return;
            });
            $('.dropdown').on('hidden.bs.dropdown', function(e) {
                // We need to focus on the menu trigger.
                var trigger = $(e.target).find('[data-toggle="dropdown"]');
                if (trigger) {
                    shiftFocus(trigger);
                }
            });
        }
    };
});
