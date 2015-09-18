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
        COMMA: 188,
        UP: 38
    };

    /** @var {Number} closeSuggestionsTimer - integer used to cancel window.setTimeout. */
    var closeSuggestionsTimer = null;

    /**
     * Make an item in the selection list "active".
     *
     * @method activateSelection
     * @private
     * @param {Number} index The index in the current (visible) list of selection.
     * @param {String} selectionId The id of the selection element for this instance of the autocomplete.
     */
    var activateSelection = function(index, selectionId) {
        // Find the elements in the DOM.
        var selectionElement = $(document.getElementById(selectionId));

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
        var itemId = selectionId + '-' + index;

        // Deselect all the selections.
        selectionElement.children().attr('data-active-selection', false).attr('id', '');
        // Select only this suggestion and assign it the id.
        element.attr('data-active-selection', true).attr('id', itemId);
        // Tell the input field it has a new active descendant so the item is announced.
        selectionElement.attr('aria-activedescendant', itemId);
    };

    /**
     * Remove the current item from the list of selected things.
     *
     * @method deselectCurrentSelection
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {String} selectionId The id of the selection element for this instance of the autocomplete.
     * @param {Element} originalSelect The original select list.
     * @param {Boolean} multiple Is this a multi select.
     * @param {Boolean} tags Is this a tags select.
     */
    var deselectCurrentSelection = function(inputId, suggestionsId, selectionId, originalSelect, multiple, tags) {
        var selectionElement = $(document.getElementById(selectionId));
        var selectedItemValue = selectionElement.children('[data-active-selection=true]').attr('data-value');
        // The select will either be a single or multi select, so the following will either
        // select one or more items correctly.
        // Take care to use 'prop' and not 'attr' for selected properties.
        // If only one can be selected at a time, start by deselecting everything.
        if (!multiple) {
            originalSelect.children('option').prop('selected', false);
        }
        // Look for a match, and toggle the selected property if there is a match.
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).attr('value') == selectedItemValue) {
                $(ele).prop('selected', false);
                if (tags) {
                    $(ele).remove();
                }
            }
        });
        // Rerender the selection list.
        updateSelectionList(selectionId, inputId, originalSelect, multiple);
    };

    /**
     * Make an item in the suggestions "active" (about to be selected).
     *
     * @method activateItem
     * @private
     * @param {Number} index The index in the current (visible) list of suggestions.
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     */
    var activateItem = function(index, inputId, suggestionsId) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(inputId));
        var suggestionsElement = $(document.getElementById(suggestionsId));

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
        var itemId = suggestionsId + '-' + globalIndex;

        // Deselect all the suggestions.
        suggestionsElement.children().attr('aria-selected', false).attr('id', '');
        // Select only this suggestion and assign it the id.
        element.attr('aria-selected', true).attr('id', itemId);
        // Tell the input field it has a new active descendant so the item is announced.
        inputElement.attr('aria-activedescendant', itemId);
    };

    /**
     * Find the index of the current active suggestion, and activate the next one.
     *
     * @method activateNextItem
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     */
    var activateNextItem = function(inputId, suggestionsId) {
        // Find the list of suggestions.
        var suggestionsElement = $(document.getElementById(suggestionsId));
        // Find the active one.
        var element = suggestionsElement.children('[aria-selected=true]');
        // Find it's index.
        var current = suggestionsElement.children('[aria-hidden=false]').index(element);
        // Activate the next one.
        activateItem(current+1, inputId, suggestionsId);
    };

    /**
     * Find the index of the current active selection, and activate the previous one.
     *
     * @method activatePreviousSelection
     * @private
     * @param {String} selectionId The id of the selection element for this instance of the autocomplete.
     */
    var activatePreviousSelection = function(selectionId) {
        // Find the list of selections.
        var selectionsElement = $(document.getElementById(selectionId));
        // Find the active one.
        var element = selectionsElement.children('[data-active-selection=true]');
        if (!element) {
            activateSelection(0, selectionId);
            return;
        }
        // Find it's index.
        var current = selectionsElement.children('[aria-selected=true]').index(element);
        // Activate the next one.
        activateSelection(current-1, selectionId);
    };
    /**
     * Find the index of the current active selection, and activate the next one.
     *
     * @method activateNextSelection
     * @private
     * @param {String} selectionId The id of the selection element for this instance of the autocomplete.
     */
    var activateNextSelection = function(selectionId) {
        // Find the list of selections.
        var selectionsElement = $(document.getElementById(selectionId));
        // Find the active one.
        var element = selectionsElement.children('[data-active-selection=true]');
        if (!element) {
            activateSelection(0, selectionId);
            return;
        }
        // Find it's index.
        var current = selectionsElement.children('[aria-selected=true]').index(element);
        // Activate the next one.
        activateSelection(current+1, selectionId);
    };

    /**
     * Find the index of the current active suggestion, and activate the previous one.
     *
     * @method activatePreviousItem
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     */
    var activatePreviousItem = function(inputId, suggestionsId) {
        // Find the list of suggestions.
        var suggestionsElement = $(document.getElementById(suggestionsId));
        // Find the active one.
        var element = suggestionsElement.children('[aria-selected=true]');
        // Find it's index.
        var current = suggestionsElement.children('[aria-hidden=false]').index(element);
        // Activate the next one.
        activateItem(current-1, inputId, suggestionsId);
    };

    /**
     * Close the list of suggestions.
     *
     * @method closeSuggestions
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     */
    var closeSuggestions = function(inputId, suggestionsId, selectionId) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(inputId));
        var suggestionsElement = $(document.getElementById(suggestionsId));

        // Announce the list of suggestions was closed, and read the current list of selections.
        inputElement.attr('aria-expanded', false).attr('aria-activedescendant', selectionId);
        // Hide the suggestions list (from screen readers too).
        suggestionsElement.hide().attr('aria-hidden', true);
    };

    /**
     * Rebuild the list of suggestions based on the current values in the select list, and the query.
     *
     * @method updateSuggestions
     * @private
     * @param {String} query The current query typed in the input field.
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     * @param {Boolean} multiple Are multiple items allowed to be selected?
     * @param {Boolean} tags Are we allowed to create new items on the fly?
     */
    var updateSuggestions = function(query, inputId, suggestionsId, originalSelect, multiple, tags) {
        // Find the elements in the DOM.
        var inputElement = $(document.getElementById(inputId));
        var suggestionsElement = $(document.getElementById(suggestionsId));

        // Used to track if we found any visible suggestions.
        var matchingElements = false;
        // Options is used by the context when rendering the suggestions from a template.
        var options = [];
        originalSelect.children('option').each(function(index, option) {
            if ($(option).prop('selected') !== true) {
                options[options.length] = { label: option.innerHTML, value: $(option).attr('value') };
            }
        });

        // Re-render the list of suggestions.
        templates.render(
            'core/form_autocomplete_suggestions',
            { inputId: inputId, suggestionsId: suggestionsId, options: options, multiple: multiple}
        ).done(function(newHTML) {
            // We have the new template, insert it in the page.
            suggestionsElement.replaceWith(newHTML);
            // Get the element again.
            suggestionsElement = $(document.getElementById(suggestionsId));
            // Show it if it is hidden.
            suggestionsElement.show().attr('aria-hidden', false);
            // For each option in the list, hide it if it doesn't match the query.
            suggestionsElement.children().each(function(index, node) {
                node = $(node);
                if (node.text().indexOf(query) > -1) {
                    node.show().attr('aria-hidden', false);
                    matchingElements = true;
                } else {
                    node.hide().attr('aria-hidden', true);
                }
            });
            // If we found any matches, show the list.
            if (matchingElements) {
                inputElement.attr('aria-expanded', true);
                // We only activate the first item in the list if tags is false,
                // because otherwise "Enter" would select the first item, instead of
                // creating a new tag.
                if (!tags) {
                    activateItem(0, inputId, suggestionsId);
                }
            } else {
                // Abort - nothing matches. Hide the suggestions properly.
                suggestionsElement.hide();
                suggestionsElement.attr('aria-hidden', true);
                inputElement.attr('aria-expanded', false);
            }
        }).fail(notification.exception);

    };

    /**
     * Create a new item for the list (a tag).
     *
     * @method createItem
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {Boolean} multiple Are multiple items allowed to be selected?
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var createItem = function(inputId, suggestionsId, selectionId, multiple, originalSelect) {
        // Find the element in the DOM.
        var inputElement = $(document.getElementById(inputId));
        // Get the current text in the input field.
        var query = inputElement.val();
        var tags = query.split(',');
        var found = false;

        $.each(tags, function(tagindex, tag) {
            // If we can only select one at a time, deselect any current value.
            tag = tag.trim();
            if (tag !== '') {
                if (!multiple) {
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
                }
            }
        });
        // Get the selection element.
        var newSelection = $(document.getElementById(selectionId));
        // Build up a valid context to re-render the selection.
        var items = [];
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).prop('selected')) {
                items.push( { label: $(ele).html(), value: $(ele).attr('value') } );
            }
        });
        var context = {
            selectionId: selectionId,
            items: items,
            multiple: multiple
        };
        // Re-render the selection.
        templates.render('core/form_autocomplete_selection', context).done(function(newHTML) {
            // Update the page.
            newSelection.empty().append($(newHTML).html());
        }).fail(notification.exception);
        // Clear the input field.
        inputElement.val('');
        // Close the suggestions list.
        closeSuggestions(inputId, suggestionsId, selectionId);
        // Trigger a change event so that the mforms javascript can check for required fields etc.
        originalSelect.change();
    };

    /**
     * Update the element that shows the currently selected items.
     *
     * @method updateSelectionList
     * @private
     * @param {String} selectionId The id of the selections element for this instance of the autocomplete.
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     * @param {Boolean} multiple Does this element support multiple selections.
     */
    var updateSelectionList = function(selectionId, inputId, originalSelect, multiple) {
        // Build up a valid context to re-render the template.
        var items = [];
        var newSelection = $(document.getElementById(selectionId));
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).prop('selected')) {
                items.push( { label: $(ele).html(), value: $(ele).attr('value') } );
            }
        });
        var context = {
            selectionId: selectionId,
            items: items,
            multiple: multiple
        };
        // Render the template.
        templates.render('core/form_autocomplete_selection', context).done(function(newHTML) {
            // Add it to the page.
            newSelection.empty().append($(newHTML).html());
        }).fail(notification.exception);
        // Because this function get's called after changing the selection, this is a good place
        // to trigger a change notification.
        originalSelect.change();
    };

    /**
     * Select the currently active item from the suggestions list.
     *
     * @method selectCurrentItem
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {String} selectionId The id of the selection element for this instance of the autocomplete.
     * @param {Boolean} multiple Are multiple items allowed to be selected?
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     */
    var selectCurrentItem = function(inputId, suggestionsId, selectionId, multiple, originalSelect) {
        // Find the elements in the page.
        var inputElement = $(document.getElementById(inputId));
        var suggestionsElement = $(document.getElementById(suggestionsId));
        // Here loop through suggestions and set val to join of all selected items.

        var selectedItemValue = suggestionsElement.children('[aria-selected=true]').attr('data-value');
        // The select will either be a single or multi select, so the following will either
        // select one or more items correctly.
        // Take care to use 'prop' and not 'attr' for selected properties.
        // If only one can be selected at a time, start by deselecting everything.
        if (!multiple) {
            originalSelect.children('option').prop('selected', false);
        }
        // Look for a match, and toggle the selected property if there is a match.
        originalSelect.children('option').each(function(index, ele) {
            if ($(ele).attr('value') == selectedItemValue) {
                $(ele).prop('selected', true);
            }
        });
        // Rerender the selection list.
        updateSelectionList(selectionId, inputId, originalSelect, multiple);
        // Clear the input element.
        inputElement.val('');
        // Close the list of suggestions.
        closeSuggestions(inputId, suggestionsId, selectionId);
    };

    /**
     * Fetch a new list of options via ajax.
     *
     * @method updateAjax
     * @private
     * @param {Event} e The event that triggered this update.
     * @param {String} selector The selector pointing to the original select.
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     * @param {Boolean} multiple Are multiple items allowed to be selected?
     * @param {Boolean} tags Are we allowed to create new items on the fly?
     * @param {Object} ajaxHandler This is a module that does the ajax fetch and translates the results.
     */
    var updateAjax = function(e, selector, inputId, suggestionsId, originalSelect, multiple, tags, ajaxHandler) {
        // Get the query to pass to the ajax function.
        var query = $(e.currentTarget).val();
        // Call the transport function to do the ajax (name taken from Select2).
        ajaxHandler.transport(selector, query, function(results) {
            // We got a result - pass it through the translator before using it.
            var processedResults = ajaxHandler.processResults(selector, results);
            var existingValues = [];

            // Now destroy all options that are not currently selected.
            originalSelect.children('option').each(function(optionIndex, option) {
                option = $(option);
                if (!option.prop('selected')) {
                    option.remove();
                } else {
                    existingValues.push(option.attr('value'));
                }
            });
            // And add all the new ones returned from ajax.
            $.each(processedResults, function(resultIndex, result) {
                if (existingValues.indexOf(result.value) === -1) {
                    var option = $('<option>');
                    option.append(result.label);
                    option.attr('value', result.value);
                    originalSelect.append(option);
                }
            });
            // Update the list of suggestions now from the new values in the select list.
            updateSuggestions('', inputId, suggestionsId, originalSelect, multiple, tags);
        }, notification.exception);
    };

    /**
     * Add all the event listeners required for keyboard nav, blur clicks etc.
     *
     * @method addNavigation
     * @private
     * @param {String} inputId The id of the input element for this instance of the autocomplete.
     * @param {String} suggestionsId The id of the suggestions element for this instance of the autocomplete.
     * @param {String} downArrowId The id of arrow to open the suggestions list.
     * @param {String} selectionId The id of element that shows the current selections.
     * @param {JQuery} originalSelect The JQuery object matching the hidden select list.
     * @param {Boolean} multiple Are multiple items allowed to be selected?
     * @param {Boolean} tags Are we allowed to create new items on the fly?
     */
    var addNavigation = function(inputId, suggestionsId, downArrowId, selectionId, originalSelect, multiple, tags) {
        // Start with the input element.
        var inputElement = $(document.getElementById(inputId));
        // Add keyboard nav with keydown.
        inputElement.on('keydown', function(e) {
            switch (e.keyCode) {
                case KEYS.DOWN:
                    // If the suggestion list is open, move to the next item.
                    if (inputElement.attr('aria-expanded') === "true") {
                        activateNextItem(inputId, suggestionsId);
                    } else {
                        // Else - open the suggestions list.
                        updateSuggestions(inputElement.val(), inputId, suggestionsId, originalSelect, multiple, tags);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.COMMA:
                    if (tags) {
                        // If we are allowing tags, comma should create a tag (or enter).
                        createItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.UP:
                    // Choose the previous active item.
                    activatePreviousItem(inputId, suggestionsId);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.ENTER:
                    var suggestionsElement = $(document.getElementById(suggestionsId));
                    if ((inputElement.attr('aria-expanded') === "true") &&
                            (suggestionsElement.children('[aria-selected=true]').length > 0)) {
                        // If the suggestion list has an active item, select it.
                        selectCurrentItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
                    } else if (tags) {
                        // If tags are enabled, create a tag.
                        createItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.ESCAPE:
                    if (inputElement.attr('aria-expanded') === "true") {
                        // If the suggestion list is open, close it.
                        closeSuggestions(inputId, suggestionsId, selectionId);
                    }
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
            }
            return true;
        });
        // Handler used to force set the value from behat.
        inputElement.on('behat:set-value', function() {
            if (tags) {
                createItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
            }
        });
        inputElement.on('blur focus', function(e) {
            // We may be blurring because we have clicked on the suggestion list. We
            // dont want to close the selection list before the click event fires, so
            // we have to delay.
            if (closeSuggestionsTimer) {
                window.clearTimeout(closeSuggestionsTimer);
            }
            closeSuggestionsTimer = window.setTimeout(function() {
                if ((e.type == 'blur') && tags) {
                    createItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
                }
                closeSuggestions(inputId, suggestionsId, selectionId);
            }, 500);
        });
        var arrowElement = $(document.getElementById(downArrowId));
        arrowElement.on('click', function() {
            // Prevent the close timer, or we will open, then close the suggestions.
            inputElement.focus();
            if (closeSuggestionsTimer) {
                window.clearTimeout(closeSuggestionsTimer);
            }
            // Show the suggestions list.
            updateSuggestions(inputElement.val(), inputId, suggestionsId, originalSelect, multiple, tags);
        });

        var suggestionsElement = $(document.getElementById(suggestionsId));
        suggestionsElement.parent().on('click', '[role=option]', function(e) {
            // Handle clicks on suggestions.
            var element = $(e.currentTarget).closest('[role=option]');
            var suggestionsElement = $(document.getElementById(suggestionsId));
            // Find the index of the clicked on suggestion.
            var current = suggestionsElement.children('[aria-hidden=false]').index(element);
            // Activate it.
            activateItem(current, inputId, suggestionsId);
            // And select it.
            selectCurrentItem(inputId, suggestionsId, selectionId, multiple, originalSelect);
        });
        var selectionElement = $(document.getElementById(selectionId));
        // Handle clicks on the selected items (will unselect an item).
        selectionElement.parent().on('click', '[role=listitem]', function(e) {
            var value = $(e.currentTarget).attr('data-value');

            // Only allow deselect if we allow multiple selections.
            if (multiple) {
                // Find the matching element and deselect it.
                originalSelect.children('option').each(function(index, ele) {
                    if ($(ele).attr('value') == value) {
                        $(ele).prop('selected', !$(ele).prop('selected'));
                    }
                });
            }

            // Re-render the selection list.
            updateSelectionList(selectionId, inputId, originalSelect, multiple);
        });
        // Keyboard navigation for the selection list.
        selectionElement.parent().on('keydown', function(e) {
            switch (e.keyCode) {
                case KEYS.DOWN:
                    // Choose the next selection item.
                    activateNextSelection(selectionId);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.UP:
                    // Choose the previous selection item.
                    activatePreviousSelection(selectionId);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
                case KEYS.SPACE:
                case KEYS.ENTER:
                    // Unselect this item.
                    deselectCurrentSelection(inputId, suggestionsId, selectionId, originalSelect, multiple, tags);
                    // We handled this event, so prevent it.
                    e.preventDefault();
                    return false;
            }
            return true;
        });
        // Whenever the input field changes, update the suggestion list.
        inputElement.on('input', function(e) {
            var query = $(e.currentTarget).val();
            updateSuggestions(query, inputId, suggestionsId, originalSelect, multiple, tags);
        });
    };

    return /** @alias module:core/form-autocomplete */ {
        // Public variables and functions.
        /**
         * Turn a boring select box into an auto-complete beast.
         *
         * @method enhance
         * @param {string} select The selector that identifies the select box.
         * @param {boolean} tags Whether to allow support for tags (can define new entries).
         * @param {string} ajax Name of an AMD module to handle ajax requests. If specified, the AMD
         *                      module must expose 2 functions "transport" and "processResults".
         *                      These are modeled on Select2 see: https://select2.github.io/options.html#ajax
         * @param {String} placeholder - The text to display before a selection is made.
         */
        enhance: function(selector, tags, ajax, placeholder) {
            // Set some default values.
            if (typeof tags === "undefined") {
                tags = false;
            }
            if (typeof ajax === "undefined") {
                ajax = false;
            }

            // Look for the select element.
            var originalSelect = $(selector);
            if (!originalSelect) {
                log.debug('Selector not found: ' + selector);
                return false;
            }

            // Hide the original select.
            originalSelect.hide().attr('aria-hidden', true);

            // Find or generate some ids.
            var selectId = originalSelect.attr('id');
            var multiple = originalSelect.attr('multiple');
            var inputId = 'form_autocomplete_input-' + $.now();
            var suggestionsId = 'form_autocomplete_suggestions-' + $.now();
            var selectionId = 'form_autocomplete_selection-' + $.now();
            var downArrowId = 'form_autocomplete_downarrow-' + $.now();

            var originalLabel = $('[for=' + selectId + ']');
            // Create the new markup and insert it after the select.
            var options = [];
            originalSelect.children('option').each(function(index, option) {
                options[index] = { label: option.innerHTML, value: $(option).attr('value') };
            });

            // Render all the parts of our UI.
            var renderInput = templates.render(
                'core/form_autocomplete_input',
                { downArrowId: downArrowId,
                  inputId: inputId,
                  suggestionsId: suggestionsId,
                  selectionId: selectionId,
                  placeholder: placeholder,
                  multiple: multiple }
            );
            var renderDatalist = templates.render(
                'core/form_autocomplete_suggestions',
                { inputId: inputId, suggestionsId: suggestionsId, options: options, multiple: multiple}
            );
            var renderSelection = templates.render(
                'core/form_autocomplete_selection',
                { selectionId: selectionId, items: [], multiple: multiple}
            );

            $.when(renderInput, renderDatalist, renderSelection).done(function(input, suggestions, selection) {
                // Add our new UI elements to the page.
                originalSelect.after(suggestions);
                originalSelect.after(input);
                originalSelect.after(selection);
                // Update the form label to point to the text input.
                originalLabel.attr('for', inputId);
                // Add the event handlers.
                addNavigation(inputId, suggestionsId, downArrowId, selectionId, originalSelect, multiple, tags);

                var inputElement = $(document.getElementById(inputId));
                var suggestionsElement = $(document.getElementById(suggestionsId));
                // Hide the suggestions by default.
                suggestionsElement.hide().attr('aria-hidden', true);

                // If this field uses ajax, set it up.
                if (ajax) {
                    require([ajax], function(ajaxHandler) {
                        var handler = function(e) {
                            updateAjax(e, selector, inputId, suggestionsId, originalSelect, multiple, tags, ajaxHandler);
                        };
                        // Trigger an ajax update after the text field value changes.
                        inputElement.on("input keypress", handler);
                        var arrowElement = $(document.getElementById(downArrowId));
                        arrowElement.on("click", handler);
                    });
                }
                // Show the current values in the selection list.
                updateSelectionList(selectionId, inputId, originalSelect, multiple);
            });
        }
    };
});
