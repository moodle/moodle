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
 * Helper that hides the fallback select and delegates to the autocomplete enhancer.
 *
 * @module     mod_bigbluebuttonbn/multiselect_tags
 * @copyright  2025 Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
define(['core/form-autocomplete'], function(autocomplete) {
    /**
     * Initialise autocomplete and enforce capitalization for newly typed tags.
     *
     * @param {string} selector Selector for the original select element.
     * @return {Promise}
     */
    const init = function(selector) {
        const args = Array.prototype.slice.call(arguments);
        const select = document.querySelector(selector);

        if (!select) {
            return Promise.resolve(null);
        }

        const root = select.parentElement;

        const toTitleCase = function(value) {
            const spaced = value.replace(/[_-]/g, ' ').replace(/\s+/g, ' ').trim();
            return spaced.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        };

        const getPillByValue = function(value) {
            if (!root) {
                return null;
            }
            const pills = root.querySelectorAll('.form-autocomplete-selection [data-value]');
            for (const pill of pills) {
                if (pill.getAttribute('data-value') === value) {
                    return pill;
                }
            }
            return null;
        };

        const updatePillValue = function(oldValue, newValue, display) {
            if (!root) {
                return;
            }

            const pill = getPillByValue(oldValue);
            if (!pill) {
                return;
            }

            pill.setAttribute('data-value', newValue);

            const textNodes = Array.from(pill.childNodes).filter(function(node) {
                return node.nodeType === Node.TEXT_NODE;
            });
            if (textNodes.length > 0) {
                textNodes[textNodes.length - 1].nodeValue = ' ' + display;
            }
        };

        const removePillValue = function(value) {
            if (!root) {
                return;
            }
            const pill = getPillByValue(value);
            if (pill) {
                pill.remove();
            }
        };

        return autocomplete.enhance.apply(autocomplete, args).then(function() {
            // Handler to normalize and check for duplicates.
            var processOptions = function() {
                // First pass: collect all existing normalized values from preset options.
                var existingValues = {};
                select.querySelectorAll('option:not([data-iscustom])').forEach(function(option) {
                    var val = option.getAttribute('value');
                    if (val) {
                        existingValues[val.toLowerCase()] = true;
                    }
                });

                // Second pass: process custom options and check for duplicates.
                var toRemove = [];
                select.querySelectorAll('option[data-iscustom]').forEach(function(option) {
                    var val = option.getAttribute('value');
                    if (!val) {
                        return;
                    }

                    var normalized = val.toLowerCase();

                    // Check if this normalized value already exists.
                    if (existingValues[normalized]) {
                        // Mark for removal.
                        toRemove.push({option: option, val: val});
                        return;
                    }

                    // Mark this value as seen for subsequent custom options.
                    existingValues[normalized] = true;

                    var display = toTitleCase(normalized);

                    option.setAttribute('value', normalized);
                    option.textContent = display;

                    // Update the visible pill label to match the capitalized display text.
                    updatePillValue(val, normalized, display);
                });

                // Remove duplicates after iteration to avoid modifying collection during iteration.
                toRemove.forEach(function(item) {
                    item.option.remove();
                    removePillValue(item.val);
                });
            };

            // Use both change event and a slight delay to catch additions quickly.
            select.addEventListener('change', function() {
                // Use setTimeout with 0 to run after the current call stack clears.
                setTimeout(processOptions, 0);
            });

            // Also process immediately in case there are existing values.
            processOptions();

            return select;
        });
    };

    return {init};
});
