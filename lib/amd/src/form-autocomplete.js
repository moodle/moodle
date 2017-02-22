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
 * Autocomplete wrapper for select2 library.
 *
 * @module     core/form-autocomplete
 * @class      autocomplete
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */
/* globals require: false */
define(['jquery', 'core/log', 'core/str', 'core/templates', 'core/notification'], function($, log, str, templates, notification) {

    // Private functions and variables.
    /** @var {Object} KEYS - List of keycode constants. */
    var KEYS = {
        DOWN: 40,
        ENTER: 13,
        SPACE: 32,
        ESCAPE: 27,
        COMMA: 44,
        UP: 38
    };

    /**
     * Make an item in the selection list "active".
     *
     * @method activateSelection
     * @private
     * @param {Number} index The index in the current (visible) list of selection.
     * @param {Object} state State variables for this autocomplete element.
     */
    var activateSelection = function(index, state) {
        // Find the elements in the DOM.
        var selectionElement = $(document.getElementById(state.selectionId));

        // Count the visible items.
        var length = selectionElement.children('[aria-selected=true]').length;
        // Limit the index to the upper/lower bounds of the list (wrap in both directions).
        index = index % length;
        while (index < 0) {
            index += length;
        }
        // Find the specified element.
        var element = $(selectionElement.children('[aria-selected=true]').get(index));
        // Create an id we can assign to this element.
        var itemId = state.selectionId + '-' + index;

        // Deselect all the selections.
        selectionElement.children().attr('data-active-selection', false).attr('id', '');
        // Select only this suggestion and assign it the id.
        element.attr('data-active-selection', true).attr('id', itemId);
        // Tell the input field it has a new active descendant so the item is announced.
        selectionElement.attr('aria-activedescendant', itemId);
    };

    /**
     * Update the element that shows the currently selected items.
     *
     * @method updateSelectionList
     * @private
     * @param {Object} options Original options for this autocomplete element.
     * @param {Object} state State variables for this autocomplete element.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var updateSelectionList = function(options, state, originalSelect) {
        // Build up a valid context to re-render the template.
        var items = [];
        var newSelection = $(document.getElementById(state.selectionId));
        var activeId = newSelection.attr('aria-activedescendant');
        var activeValue = false;

        if (activeId) {
            activeValue = $(document.getElementById(activeId)).attr('data-value');
        }
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).prop('selected')) {
                items.push({label: $(ele).html(), value: $(ele).attr('value')});
            }
        });
        var context = $.extend({items: items}, options, state);

        // Render the template.
        templates.render('core/form_autocomplete_selection', context).done(function(newHTML) {
            // Add it to the page.
            newSelection.empty().append($(newHTML).html());

            if (activeValue !== false) {
                // Reselect any previously selected item.
                newSelection.children('[aria-selected=true]').each(function(index, ele) {
                    if ($(ele).attr('data-value') === activeValue) {
                        activateSelection(index, state);
                    }
                });
            }
        }).fail(notification.exception);
    };

    /**
     * Notify of a change in the selection.
     *
     * @param {jQuery} originalSelect The jQuery object matching the hidden select list.
     */
    var notifyChange = function(originalSelect) {
        if (typeof M.core_formchangechecker !== 'undefined') {
            M.core_formchangechecker.set_form_changed();
        }
        originalSelect.change();
    };

    /**
     * Remove the given item from the list of selected things.
     *
     * @method deselectItem
     * @private
     * @param {Object} options Original options for this autocomplete element.
     * @param {Object} state State variables for this autocomplete element.
     * @param {Element} item The item to be deselected.
     * @param {Element} originalSelect The original select list.
     */
    var deselectItem = function(options, state, item, originalSelect) {
        var selectedItemValue = $(item).attr('data-value');

        // We can only deselect items if this is a multi-select field.
        if (options.multiple) {
            // Look for a match, and toggle the selected property if there is a match.
            originalSelect.children('option').each(function(index, ele) {
                if ($(ele).attr('value') == selectedItemValue) {
                    $(ele).prop('selected', false);
                    // We remove newly created custom tags from the suggestions list when they are deselected.
                    if ($(ele).attr('data-iscustom')) {
                        $(ele).remove();
                    }
                }
            });
        }
        // Rerender the selection list.
        updateSelectionList(options, state, originalSelect);
        // Notifiy that the selection changed.
        notifyChange(originalSelect);
    };

    /**
     * Make an item in the suggestions "active" (about to be selected).
     *
     * @method activateItem
     * @private
     * @param {Number} index The index in the current (visible) list of suggestions.
     * @param {Object} state State variables for this instance of autocomplete.
     */
    var activateItem = function(index, state) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(state.inputId));
        var suggestionsElement = $(document.getElementById(state.suggestionsId));

        // Count the visible items.
        var length = suggestionsElement.children('[aria-hidden=false]').length;
        // Limit the index to the upper/lower bounds of the list (wrap in both directions).
        index = index % length;
        while (index < 0) {
            index += length;
        }
        // Find the specified element.
        var element = $(suggestionsElement.children('[aria-hidden=false]').get(index));
        // Find the index of this item in the full list of suggestions (including hidden).
        var globalIndex = $(suggestionsElement.children('[role=option]')).index(element);
        // Create an id we can assign to this element.
        var itemId = state.suggestionsId + '-' + globalIndex;

        // Deselect all the suggestions.
        suggestionsElement.children().attr('aria-selected', false).attr('id', '');
        // Select only this suggestion and assign it the id.
        element.attr('aria-selected', true).attr('id', itemId);
        // Tell the input field it has a new active descendant so the item is announced.
        inputElement.attr('aria-activedescendant', itemId);

        // Scroll it into view.
        var scrollPos = element.offset().top
                       - suggestionsElement.offset().top
                       + suggestionsElement.scrollTop()
                       - (suggestionsElement.height() / 2);
        suggestionsElement.animate({
            scrollTop: scrollPos
        }, 100);
    };

    /**
     * Find the index of the current active suggestion, and activate the next one.
     *
     * @method activateNextItem
     * @private
     * @param {Object} state State variable for this auto complete element.
     */
    var activateNextItem = function(state) {
        // Find the list of suggestions.
        var suggestionsElement = $(document.getElementById(state.suggestionsId));
        // Find the active one.
        var element = suggestionsElement.children('[aria-selected=true]');
        // Find it's index.
        var current = suggestionsElement.children('[aria-hidden=false]').index(element);
        // Activate the next one.
        activateItem(current + 1, state);
    };

    /**
     * Find the index of the current active selection, and activate the previous one.
     *
     * @method activatePreviousSelection
     * @private
     * @param {Object} state State variables for this instance of autocomplete.
     */
    var activatePreviousSelection = function(state) {
        // Find the list of selections.
        var selectionsElement = $(document.getElementById(state.selectionId));
        // Find the active one.
        var element = selectionsElement.children('[data-active-selection=true]');
        if (!element) {
            activateSelection(0, state);
            return;
        }
        // Find it's index.
        var current = selectionsElement.children('[aria-selected=true]').index(element);
        // Activate the next one.
        activateSelection(current - 1, state);
    };
    /**
     * Find the index of the current active selection, and activate the next one.
     *
     * @method activateNextSelection
     * @private
     * @param {Object} state State variables for this instance of autocomplete.
     */
    var activateNextSelection = function(state) {
        // Find the list of selections.
        var selectionsElement = $(document.getElementById(state.selectionId));
        // Find the active one.
        var element = selectionsElement.children('[data-active-selection=true]');
        if (!element) {
            activateSelection(0, state);
            return;
        }
        // Find it's index.
        var current = selectionsElement.children('[aria-selected=true]').index(element);
        // Activate the next one.
        activateSelection(current + 1, state);
    };

    /**
     * Find the index of the current active suggestion, and activate the previous one.
     *
     * @method activatePreviousItem
     * @private
     * @param {Object} state State variables for this autocomplete element.
     */
    var activatePreviousItem = function(state) {
        // Find the list of suggestions.
        var suggestionsElement = $(document.getElementById(state.suggestionsId));
        // Find the active one.
        var element = suggestionsElement.children('[aria-selected=true]');
        // Find it's index.
        var current = suggestionsElement.children('[aria-hidden=false]').index(element);
        // Activate the next one.
        activateItem(current - 1, state);
    };

    /**
     * Close the list of suggestions.
     *
     * @method closeSuggestions
     * @private
     * @param {Object} state State variables for this autocomplete element.
     */
    var closeSuggestions = function(state) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(state.inputId));
        var suggestionsElement = $(document.getElementById(state.suggestionsId));

        // Announce the list of suggestions was closed, and read the current list of selections.
        inputElement.attr('aria-expanded', false).attr('aria-activedescendant', state.selectionId);
        // Hide the suggestions list (from screen readers too).
        suggestionsElement.hide().attr('aria-hidden', true);
    };

    /**
     * Rebuild the list of suggestions based on the current values in the select list, and the query.
     *
     * @method updateSuggestions
     * @private
     * @param {Object} options The original options for this autocomplete.
     * @param {Object} state The state variables for this autocomplete.
     * @param {String} query The current text for the search string.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var updateSuggestions = function(options, state, query, originalSelect) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(state.inputId));
        var suggestionsElement = $(document.getElementById(state.suggestionsId));

        // Used to track if we found any visible suggestions.
        var matchingElements = false;
        // Options is used by the context when rendering the suggestions from a template.
        var suggestions = [];
        originalSelect.children('option').each(function(index, option) {
            if ($(option).prop('selected') !== true) {
                suggestions[suggestions.length] = {label: option.innerHTML, value: $(option).attr('value')};
            }
        });

        // Re-render the list of suggestions.
        var searchquery = state.caseSensitive ? query : query.toLocaleLowerCase();
        var context = $.extend({options: suggestions}, options, state);
        templates.render(
            'core/form_autocomplete_suggestions',
            context
        ).done(function(newHTML) {
            // We have the new template, insert it in the page.
            suggestionsElement.replaceWith(newHTML);
            // Get the element again.
            suggestionsElement = $(document.getElementById(state.suggestionsId));
            // Show it if it is hidden.
            suggestionsElement.show().attr('aria-hidden', false);
            // For each option in the list, hide it if it doesn't match the query.
            suggestionsElement.children().each(function(index, node) {
                node = $(node);
                if ((options.caseSensitive && node.text().indexOf(searchquery) > -1) ||
                        (!options.caseSensitive && node.text().toLocaleLowerCase().indexOf(searchquery) > -1)) {
                    node.show().attr('aria-hidden', false);
                    matchingElements = true;
                } else {
                    node.hide().attr('aria-hidden', true);
                }
            });
            // If we found any matches, show the list.
            inputElement.attr('aria-expanded', true);
            if (matchingElements) {
                // We only activate the first item in the list if tags is false,
                // because otherwise "Enter" would select the first item, instead of
                // creating a new tag.
                if (!options.tags) {
                    activateItem(0, state);
                }
            } else {
                // Nothing matches. Tell them that.
                str.get_string('nosuggestions', 'form').done(function(nosuggestionsstr) {
                    suggestionsElement.html(nosuggestionsstr);
                });
            }
        }).fail(notification.exception);

    };

    /**
     * Create a new item for the list (a tag).
     *
     * @method createItem
     * @private
     * @param {Object} options The original options for the autocomplete.
     * @param {Object} state State variables for the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var createItem = function(options, state, originalSelect) {
        // Find the element in the DOM.
        var inputElement = $(document.getElementById(state.inputId));
        // Get the current text in the input field.
        var query = inputElement.val();
        var tags = query.split(',');
        var found = false;

        $.each(tags, function(tagindex, tag) {
            // If we can only select one at a time, deselect any current value.
            tag = tag.trim();
            if (tag !== '') {
                if (!options.multiple) {
                    originalSelect.children('option').prop('selected', false);
                }
                // Look for an existing option in the select list that matches this new tag.
                originalSelect.children('option').each(function(index, ele) {
                    if ($(ele).attr('value') == tag) {
                        found = true;
                        $(ele).prop('selected', true);
                    }
                });
                // Only create the item if it's new.
                if (!found) {
                    var option = $('<option>');
                    option.append(tag);
                    option.attr('value', tag);
                    originalSelect.append(option);
                    option.prop('selected', true);
                    // We mark newly created custom options as we handle them differently if they are "deselected".
                    option.attr('data-iscustom', true);
                }
            }
        });

        updateSelectionList(options, state, originalSelect);
        // Notifiy that the selection changed.
        notifyChange(originalSelect);
        // Clear the input field.
        inputElement.val('');
        // Close the suggestions list.
        closeSuggestions(state);
    };

    /**
     * Select the currently active item from the suggestions list.
     *
     * @method selectCurrentItem
     * @private
     * @param {Object} options The original options for the autocomplete.
     * @param {Object} state State variables for the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var selectCurrentItem = function(options, state, originalSelect) {
        // Find the elements in the page.
        var inputElement = $(document.getElementById(state.inputId));
        var suggestionsElement = $(document.getElementById(state.suggestionsId));
        // Here loop through suggestions and set val to join of all selected items.

        var selectedItemValue = suggestionsElement.children('[aria-selected=true]').attr('data-value');
        // The select will either be a single or multi select, so the following will either
        // select one or more items correctly.
        // Take care to use 'prop' and not 'attr' for selected properties.
        // If only one can be selected at a time, start by deselecting everything.
        if (!options.multiple) {
            originalSelect.children('option').prop('selected', false);
        }
        // Look for a match, and toggle the selected property if there is a match.
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).attr('value') == selectedItemValue) {
                $(ele).prop('selected', true);
            }
        });
        // Rerender the selection list.
        updateSelectionList(options, state, originalSelect);
        // Notifiy that the selection changed.
        notifyChange(originalSelect);
        // Clear the input element.
        inputElement.val('');
        // Close the list of suggestions.
        closeSuggestions(state);
    };

    /**
     * Fetch a new list of options via ajax.
     *
     * @method updateAjax
     * @private
     * @param {Event} e The event that triggered this update.
     * @param {Object} options The original options for the autocomplete.
     * @param {Object} state The state variables for the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     * @param {Object} ajaxHandler This is a module that does the ajax fetch and translates the results.
     */
    var updateAjax = function(e, options, state, originalSelect, ajaxHandler) {
        // Get the query to pass to the ajax function.
        var query = $(e.currentTarget).val();
        // Call the transport function to do the ajax (name taken from Select2).
        ajaxHandler.transport(options.selector, query, function(results) {
            // We got a result - pass it through the translator before using it.
            var processedResults = ajaxHandler.processResults(options.selector, results);
            var existingValues = [];

            // Now destroy all options that are not currently selected.
            originalSelect.children('option').each(function(optionIndex, option) {
                option = $(option);
                if (!option.prop('selected')) {
                    option.remove();
                } else {
                    existingValues.push(String(option.attr('value')));
                }
            });

            if (!options.multiple && originalSelect.children('option').length === 0) {
                // If this is a single select - and there are no current options
                // the first option added will be selected by the browser. This causes a bug!
                // We need to insert an empty option so that none of the real options are selected.
                var option = $('<option>');
                originalSelect.append(option);
            }
            // And add all the new ones returned from ajax.
            $.each(processedResults, function(resultIndex, result) {
                if (existingValues.indexOf(String(result.value)) === -1) {
                    var option = $('<option>');
                    option.append(result.label);
                    option.attr('value', result.value);
                    originalSelect.append(option);
                }
            });
            // Update the list of suggestions now from the new values in the select list.
            updateSuggestions(options, state, '', originalSelect);
        }, notification.exception);
    };

    /**
     * Add all the event listeners required for keyboard nav, blur clicks etc.
     *
     * @method addNavigation
     * @private
     * @param {Object} options The options used to create this autocomplete element.
     * @param {Object} state State variables for this autocomplete element.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var addNavigation = function(options, state, originalSelect) {
        // Start with the input element.
        var inputElement = $(document.getElementById(state.inputId));
        // Add keyboard nav with keydown.
        inputElement.on('keydown', function(e) {
            switch (e.keyCode) {
                case KEYS.DOWN:
                    // If the suggestion list is open, move to the next item.
                    if (!options.showSuggestions) {
                        // Do not consume this event.
                        return true;
                    } else if (inputElement.attr('aria-expanded') === "true") {
                        activateNextItem(state);
                    } else {
                        // Handle ajax population of suggestions.
                        if (!inputElement.val() && options.ajax) {
                            require([options.ajax], function(ajaxHandler) {
                                updateAjax(e, options, state, originalSelect, ajaxHandler);
                            });
                        } else {
                            // Else - open the suggestions list.
                            updateSuggestions(options, state, inputElement.val(), originalSelect);
                        }
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.UP:
                    // Choose the previous active item.
                    activatePreviousItem(state);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.ENTER:
                    var suggestionsElement = $(document.getElementById(state.suggestionsId));
                    if ((inputElement.attr('aria-expanded') === "true") &&
                            (suggestionsElement.children('[aria-selected=true]').length > 0)) {
                        // If the suggestion list has an active item, select it.
                        selectCurrentItem(options, state, originalSelect);
                    } else if (options.tags) {
                        // If tags are enabled, create a tag.
                        createItem(options, state, originalSelect);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.ESCAPE:
                    if (inputElement.attr('aria-expanded') === "true") {
                        // If the suggestion list is open, close it.
                        closeSuggestions(state);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
            }
            return true;
        });
        // Support multi lingual COMMA keycode (44).
        inputElement.on('keypress', function(e) {
            if (e.keyCode === KEYS.COMMA) {
                if (options.tags) {
                    // If we are allowing tags, comma should create a tag (or enter).
                    createItem(options, state, originalSelect);
                }
                // We handled this event, so prevent it.
                e.preventDefault();
                return false;
            }
            return true;
        });
        // Handler used to force set the value from behat.
        inputElement.on('behat:set-value', function() {
            var suggestionsElement = $(document.getElementById(state.suggestionsId));
            if ((inputElement.attr('aria-expanded') === "true") &&
                    (suggestionsElement.children('[aria-selected=true]').length > 0)) {
                // If the suggestion list has an active item, select it.
                selectCurrentItem(options, state, originalSelect);
            } else if (options.tags) {
                // If tags are enabled, create a tag.
                createItem(options, state, originalSelect);
            }
        });
        inputElement.on('blur', function() {
            window.setTimeout(function() {
                // Get the current element with focus.
                var focusElement = $(document.activeElement);
                // Only close the menu if the input hasn't regained focus.
                if (focusElement.attr('id') != inputElement.attr('id')) {
                    if (options.tags) {
                        createItem(options, state, originalSelect);
                    }
                    closeSuggestions(state);
                }
            }, 500);
        });
        if (options.showSuggestions) {
            var arrowElement = $(document.getElementById(state.downArrowId));
            arrowElement.on('click', function() {
                // Prevent the close timer, or we will open, then close the suggestions.
                inputElement.focus();
                // Show the suggestions list.
                updateSuggestions(options, state, inputElement.val(), originalSelect);
            });
        }

        var suggestionsElement = $(document.getElementById(state.suggestionsId));
        suggestionsElement.parent().on('click', '[role=option]', function(e) {
            // Handle clicks on suggestions.
            var element = $(e.currentTarget).closest('[role=option]');
            var suggestionsElement = $(document.getElementById(state.suggestionsId));
            // Find the index of the clicked on suggestion.
            var current = suggestionsElement.children('[aria-hidden=false]').index(element);
            // Activate it.
            activateItem(current, state);
            // And select it.
            selectCurrentItem(options, state, originalSelect);
        });
        var selectionElement = $(document.getElementById(state.selectionId));
        // Handle clicks on the selected items (will unselect an item).
        selectionElement.on('click', '[role=listitem]', function(e) {
            // Get the item that was clicked.
            var item = $(e.currentTarget);
            // Remove it from the selection.
            deselectItem(options, state, item, originalSelect);
        });
        // Keyboard navigation for the selection list.
        selectionElement.on('keydown', function(e) {
            switch (e.keyCode) {
                case KEYS.DOWN:
                    // Choose the next selection item.
                    activateNextSelection(state);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.UP:
                    // Choose the previous selection item.
                    activatePreviousSelection(state);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.SPACE:
                case KEYS.ENTER:
                    // Get the item that is currently selected.
                    var selectedItem = $(document.getElementById(state.selectionId)).children('[data-active-selection=true]');
                    if (selectedItem) {
                        // Unselect this item.
                        deselectItem(options, state, selectedItem, originalSelect);
                        // We handled this event, so prevent it.
                        e.preventDefault();
                    }
                    return false;
            }
            return true;
        });
        // Whenever the input field changes, update the suggestion list.
        if (options.showSuggestions) {
            inputElement.on('input', function(e) {
                var query = $(e.currentTarget).val();
                var last = $(e.currentTarget).data('last-value');
                // IE11 fires many more input events than required - even when the value has not changed.
                // We need to only do this for real value changed events or the suggestions will be
                // unclickable on IE11 (because they will be rebuilt before the click event fires).
                // Note - because of this we cannot close the list when the query is empty or it will break
                // on IE11.
                if (last !== query) {
                    updateSuggestions(options, state, query, originalSelect);
                }
                $(e.currentTarget).data('last-value', query);
            });
        }
    };

    return /** @alias module:core/form-autocomplete */ {
        // Public variables and functions.
        /**
         * Turn a boring select box into an auto-complete beast.
         *
         * @method enhance
         * @param {string} selector The selector that identifies the select box.
         * @param {boolean} tags Whether to allow support for tags (can define new entries).
         * @param {string} ajax Name of an AMD module to handle ajax requests. If specified, the AMD
         *                      module must expose 2 functions "transport" and "processResults".
         *                      These are modeled on Select2 see: https://select2.github.io/options.html#ajax
         * @param {String} placeholder - The text to display before a selection is made.
         * @param {Boolean} caseSensitive - If search has to be made case sensitive.
         * @param {Boolean} showSuggestions - If suggestions should be shown
         * @param {String} noSelectionString - Text to display when there is no selection
         */
        enhance: function(selector, tags, ajax, placeholder, caseSensitive, showSuggestions, noSelectionString) {
            // Set some default values.
            var options = {
                selector: selector,
                tags: false,
                ajax: false,
                placeholder: placeholder,
                caseSensitive: false,
                showSuggestions: true,
                noSelectionString: noSelectionString
            };
            if (typeof tags !== "undefined") {
                options.tags = tags;
            }
            if (typeof ajax !== "undefined") {
                options.ajax = ajax;
            }
            if (typeof caseSensitive !== "undefined") {
                options.caseSensitive = caseSensitive;
            }
            if (typeof showSuggestions !== "undefined") {
                options.showSuggestions = showSuggestions;
            }
            if (typeof noSelectionString === "undefined") {
                str.get_string('noselection', 'form').done(function(result) {
                    options.noSelectionString = result;
                }).fail(notification.exception);
            }

            // Look for the select element.
            var originalSelect = $(selector);
            if (!originalSelect) {
                log.debug('Selector not found: ' + selector);
                return;
            }

            // Hide the original select.
            originalSelect.hide().attr('aria-hidden', true);

            // Find or generate some ids.
            var state = {
                selectId: originalSelect.attr('id'),
                inputId: 'form_autocomplete_input-' + $.now(),
                suggestionsId: 'form_autocomplete_suggestions-' + $.now(),
                selectionId: 'form_autocomplete_selection-' + $.now(),
                downArrowId: 'form_autocomplete_downarrow-' + $.now()
            };
            options.multiple = originalSelect.attr('multiple');

            var originalLabel = $('[for=' + state.selectId + ']');
            // Create the new markup and insert it after the select.
            var suggestions = [];
            originalSelect.children('option').each(function(index, option) {
                suggestions[index] = {label: option.innerHTML, value: $(option).attr('value')};
            });

            // Render all the parts of our UI.
            var context = $.extend({}, options, state);
            context.options = suggestions;
            context.items = [];

            var renderInput = templates.render('core/form_autocomplete_input', context);
            var renderDatalist = templates.render('core/form_autocomplete_suggestions', context);
            var renderSelection = templates.render('core/form_autocomplete_selection', context);

            $.when(renderInput, renderDatalist, renderSelection).done(function(input, suggestions, selection) {
                // Add our new UI elements to the page.
                originalSelect.after(suggestions);
                originalSelect.after(input);
                originalSelect.after(selection);
                // Update the form label to point to the text input.
                originalLabel.attr('for', state.inputId);
                // Add the event handlers.
                addNavigation(options, state, originalSelect);

                var inputElement = $(document.getElementById(state.inputId));
                var suggestionsElement = $(document.getElementById(state.suggestionsId));
                // Hide the suggestions by default.
                suggestionsElement.hide().attr('aria-hidden', true);

                // If this field uses ajax, set it up.
                if (options.ajax) {
                    require([options.ajax], function(ajaxHandler) {
                        var throttleTimeout = null;
                        var handler = function(e) {
                            updateAjax(e, options, state, originalSelect, ajaxHandler);
                        };

                        // For input events, we do not want to trigger many, many updates.
                        var throttledHandler = function(e) {
                            if (throttleTimeout !== null) {
                                window.clearTimeout(throttleTimeout);
                                throttleTimeout = null;
                            }
                            throttleTimeout = window.setTimeout(handler.bind(this, e), 300);
                        };
                        // Trigger an ajax update after the text field value changes.
                        inputElement.on("input", throttledHandler);
                        var arrowElement = $(document.getElementById(state.downArrowId));
                        arrowElement.on("click", handler);
                    });
                }
                // Show the current values in the selection list.
                updateSelectionList(options, state, originalSelect);
            });
        }
    };
});
