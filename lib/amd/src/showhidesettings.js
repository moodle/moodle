/**
 * Show/hide admin settings based on other settings selected
 *
 * @copyright 2018 Davo Smith, Synergy Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    var dependencies;

    // -------------------------------------------------
    // Support functions, used by dependency functions.
    // -------------------------------------------------

    /**
     * Check to see if the given element is the hidden element that makes sure checkbox
     * elements always submit a value.
     * @param {jQuery} $el
     * @returns {boolean}
     */
    function isCheckboxHiddenElement($el) {
        return ($el.is('input[type=hidden]') && $el.siblings('input[type=checkbox][name="' + $el.attr('name') + '"]').length);
    }

    /**
     * Check to see if this is a radio button with the wrong value (i.e. a radio button from
     * the group we are interested in, but not the specific one we wanted).
     * @param {jQuery} $el
     * @param {string} value
     * @returns {boolean}
     */
    function isWrongRadioButton($el, value) {
        return ($el.is('input[type=radio]') && $el.attr('value') !== value);
    }

    /**
     * Is this element relevant when we're looking for checked / not checked status?
     * @param {jQuery} $el
     * @param {string} value
     * @returns {boolean}
     */
    function isCheckedRelevant($el, value) {
        return (!isCheckboxHiddenElement($el) && !isWrongRadioButton($el, value));
    }

    /**
     * Is this an unchecked radio button? (If it is, we want to skip it, as
     * we're only interested in the value of the radio button that is checked)
     * @param {jQuery} $el
     * @returns {boolean}
     */
    function isUncheckedRadioButton($el) {
        return ($el.is('input[type=radio]') && !$el.prop('checked'));
    }

    /**
     * Is this an unchecked checkbox?
     * @param {jQuery} $el
     * @returns {boolean}
     */
    function isUncheckedCheckbox($el) {
        return ($el.is('input[type=checkbox]') && !$el.prop('checked'));
    }

    /**
     * Is this a multi-select select element?
     * @param {jQuery} $el
     * @returns {boolean}
     */
    function isMultiSelect($el) {
        return ($el.is('select') && $el.prop('multiple'));
    }

    /**
     * Does the multi-select exactly match the list of values provided?
     * @param {jQuery} $el
     * @param {array} values
     * @returns {boolean}
     */
    function multiSelectMatches($el, values) {
        var selected = $el.val() || [];
        if (values.length === 1 && values[0] === '') {
            // Values array contains a single empty entry -> value was empty.
            return selected.length === 0;
        }
        if (selected.length !== values.length) {
            // Different number of expected and actual values - cannot possibly be a match.
            return false;
        }
        for (var i in selected) {
            if (selected.hasOwnProperty(i)) {
                if (values.indexOf(selected[i]) === -1) {
                    return false; // Found a non-matching value - give up immediately.
                }
            }
        }
        // Didn't find a non-matching value, so we have a match.
        return true;
    }

    // -------------------------------
    // Specific dependency functions.
    // -------------------------------

    var depFns = {
        notchecked: function($dependon, value) {
            var hide = false;
            value = String(value);
            $dependon.each(function(idx, el) {
                var $el = $(el);
                if (isCheckedRelevant($el, value)) {
                    hide = hide || !$el.prop('checked');
                }
            });
            return hide;
        },

        checked: function($dependon, value) {
            var hide = false;
            value = String(value);
            $dependon.each(function(idx, el) {
                var $el = $(el);
                if (isCheckedRelevant($el, value)) {
                    hide = hide || $el.prop('checked');
                }
            });
            return hide;
        },

        noitemselected: function($dependon) {
            var hide = false;
            $dependon.each(function(idx, el) {
                var $el = $(el);
                hide = hide || ($el.prop('selectedIndex') === -1);
            });
            return hide;
        },

        eq: function($dependon, value) {
            var hide = false;
            var hiddenVal = false;
            value = String(value);
            $dependon.each(function(idx, el) {
                var $el = $(el);
                if (isUncheckedRadioButton($el)) {
                    // For radio buttons, we're only interested in the one that is checked.
                    return;
                }
                if (isCheckboxHiddenElement($el)) {
                    // This is the hidden input that is part of the checkbox setting.
                    // We will use this value, if the associated checkbox is unchecked.
                    hiddenVal = ($el.val() === value);
                    return;
                }
                if (isUncheckedCheckbox($el)) {
                    // Checkbox is not checked - hide depends on the 'unchecked' value stored in
                    // the associated hidden element, which we have already found, above.
                    hide = hide || hiddenVal;
                    return;
                }
                if (isMultiSelect($el)) {
                    // Expect a list of values to match, separated by '|' - all of them must
                    // match the values selected.
                    var values = value.split('|');
                    hide = multiSelectMatches($el, values);
                    return;
                }
                // All other element types - just compare the value directly.
                hide = hide || ($el.val() === value);
            });
            return hide;
        },

        'in': function($dependon, value) {
            var hide = false;
            var hiddenVal = false;
            var values = value.split('|');
            $dependon.each(function(idx, el) {
                var $el = $(el);
                if (isUncheckedRadioButton($el)) {
                    // For radio buttons, we're only interested in the one that is checked.
                    return;
                }
                if (isCheckboxHiddenElement($el)) {
                    // This is the hidden input that is part of the checkbox setting.
                    // We will use this value, if the associated checkbox is unchecked.
                    hiddenVal = (values.indexOf($el.val()) > -1);
                    return;
                }
                if (isUncheckedCheckbox($el)) {
                    // Checkbox is not checked - hide depends on the 'unchecked' value stored in
                    // the associated hidden element, which we have already found, above.
                    hide = hide || hiddenVal;
                    return;
                }
                if (isMultiSelect($el)) {
                    // For multiselect, we check to see if the list of values provided matches the list selected.
                    hide = multiSelectMatches($el, values);
                    return;
                }
                // All other element types - check to see if the value is in the list.
                hide = hide || (values.indexOf($el.val()) > -1);
            });
            return hide;
        },

        defaultCondition: function($dependon, value) { // Not equal.
            var hide = false;
            var hiddenVal = false;
            value = String(value);
            $dependon.each(function(idx, el) {
                var $el = $(el);
                if (isUncheckedRadioButton($el)) {
                    // For radio buttons, we're only interested in the one that is checked.
                    return;
                }
                if (isCheckboxHiddenElement($el)) {
                    // This is the hidden input that is part of the checkbox setting.
                    // We will use this value, if the associated checkbox is unchecked.
                    hiddenVal = ($el.val() !== value);
                    return;
                }
                if (isUncheckedCheckbox($el)) {
                    // Checkbox is not checked - hide depends on the 'unchecked' value stored in
                    // the associated hidden element, which we have already found, above.
                    hide = hide || hiddenVal;
                    return;
                }
                if (isMultiSelect($el)) {
                    // Expect a list of values to match, separated by '|' - all of them must
                    // match the values selected to *not* hide the element.
                    var values = value.split('|');
                    hide = !multiSelectMatches($el, values);
                    return;
                }
                // All other element types - just compare the value directly.
                hide = hide || ($el.val() !== value);
            });
            return hide;
        }
    };

    /**
     * Find the element with the given name
     * @param {String} name
     * @returns {*|jQuery|HTMLElement}
     */
    function getElementsByName(name) {
        // For the array elements, we use [name^="something["] to find the elements that their name begins with 'something['/
        // This is to find both name = 'something[]' and name='something[index]'.
        return $('[name="' + name + '"],[name^="' + name + '["]');
    }

    /**
     * Check to see whether a particular condition is met
     * @param {*|jQuery|HTMLElement} $dependon
     * @param {String} condition
     * @param {mixed} value
     * @returns {Boolean}
     */
    function checkDependency($dependon, condition, value) {
        if (typeof depFns[condition] === "function") {
            return depFns[condition]($dependon, value);
        }
        return depFns.defaultCondition($dependon, value);
    }

    /**
     * Show / hide the elements that depend on some elements.
     */
    function updateDependencies() {
        // Process all dependency conditions.
        var toHide = {};
        $.each(dependencies, function(dependonname) {
            var dependon = getElementsByName(dependonname);
            $.each(dependencies[dependonname], function(condition, values) {
                $.each(values, function(value, elements) {
                    var hide = checkDependency(dependon, condition, value);
                    $.each(elements, function(idx, elToHide) {
                        if (toHide.hasOwnProperty(elToHide)) {
                            toHide[elToHide] = toHide[elToHide] || hide;
                        } else {
                            toHide[elToHide] = hide;
                        }
                    });
                });
            });
        });

        // Update the hidden status of all relevant elements.
        $.each(toHide, function(elToHide, hide) {
            getElementsByName(elToHide).each(function(idx, el) {
                var $parent = $(el).closest('.form-item');
                if ($parent.length) {
                    if (hide) {
                        $parent.hide();
                    } else {
                        $parent.show();
                    }
                }
            });
        });
    }

    /**
     * Initialise the event handlers.
     */
    function initHandlers() {
        $.each(dependencies, function(depname) {
            var $el = getElementsByName(depname);
            if ($el.length) {
                $el.on('change', updateDependencies);
            }
        });
        updateDependencies();
    }

    /**
     * Hide the 'this setting may be hidden' messages.
     */
    function hideDependencyInfo() {
        $('.form-dependenton').hide();
    }

    return {
        init: function(opts) {
            dependencies = opts.dependencies;
            initHandlers();
            hideDependencyInfo();
        }
    };
});