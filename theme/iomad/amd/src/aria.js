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
 * @module     theme_iomad/aria
 * @copyright  2018 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/pending'], function($, Pending) {
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
            $('[data-toggle="dropdown"]').keydown(function(e) {
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
                var delayedFocus = function(pendingPromise) {
                    $(this).focus();
                    pendingPromise.resolve();
                }.bind(element);
                setTimeout(delayedFocus, 50, new Pending('core/aria:delayed-focus'));
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
            // Search for menu items by finding the first item that has
            // text starting with the typed character (case insensitive).
            $('.dropdown [role="menu"] [role="menuitem"]').keypress(function(e) {
                var trigger = String.fromCharCode(e.which || e.keyCode),
                    menu = $(e.target).closest('[role="menu"]'),
                    i = 0,
                    menuItems = false,
                    item,
                    itemText;

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
                    itemText = item.text().trim().toLowerCase();
                    if (itemText.indexOf(trigger) == 0) {
                        shiftFocus(item);
                        break;
                    }
                }
            });

            // Keyboard navigation for arrow keys, home and end keys.
            $('.dropdown [role="menu"] [role="menuitem"]').keydown(function(e) {
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
                // Down key.
                if (trigger == 40) {
                    for (i = 0; i < menuItems.length - 1; i++) {
                        if (menuItems[i] == e.target) {
                            next = menuItems[i + 1];
                            break;
                        }
                    }
                    if (!next) {
                        // Wrap to first item.
                        next = menuItems[0];
                    }

                } else if (trigger == 38) {
                    // Up key.
                    for (i = 1; i < menuItems.length; i++) {
                        if (menuItems[i] == e.target) {
                            next = menuItems[i - 1];
                            break;
                        }
                    }
                    if (!next) {
                        // Wrap to last item.
                        next = menuItems[menuItems.length - 1];
                    }

                } else if (trigger == 36) {
                    // Home key.
                    next = menuItems[0];

                } else if (trigger == 35) {
                    // End key.
                    next = menuItems[menuItems.length - 1];
                }
                // Variable next is set if we do want to act on the keypress.
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

            // After page load, focus on any element with special autofocus attribute.
            window.addEventListener("load", () => {
                const alerts = document.querySelectorAll('[data-aria-autofocus="true"][role="alert"]');
                Array.prototype.forEach.call(alerts, autofocusElement => {
                    // According to the specification an role="alert" region is only read out on change to the content
                    // of that region.
                    autofocusElement.innerHTML += ' ';
                    autofocusElement.removeAttribute('data-aria-autofocus');
                });
            });
        }
    };
});
