(function() {
    // Set up the M object - only pending_js is implemented.
    window.M = window.M ? window.M : {};
    var M = window.M;
    M.util = M.util ? M.util : {};
    M.util.pending_js = M.util.pending_js ? M.util.pending_js : []; // eslint-disable-line camelcase

    /**
     * Logs information from this Behat runtime JavaScript, including the time and the 'BEHAT'
     * keyword so we can easily filter for it if needed.
     *
     * @param {string} text Information to log
     */
    var log = function(text) {
        var now = new Date();
        var nowFormatted = String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0') + '.' +
                String(now.getMilliseconds()).padStart(2, '0');
        console.log('BEHAT: ' + nowFormatted + ' ' + text); // eslint-disable-line no-console
    };

    /**
     * Run after several setTimeouts to ensure queued events are finished.
     *
     * @param {function} target function to run
     * @param {number} count Number of times to do setTimeout (leave blank for 10)
     */
    var runAfterEverything = function(target, count) {
        if (count === undefined) {
            count = 10;
        }
        setTimeout(function() {
            count--;
            if (count == 0) {
                target();
            } else {
                runAfterEverything(target, count);
            }
        }, 0);
    };

    /**
     * Adds a pending key to the array.
     *
     * @param {string} key Key to add
     */
    var addPending = function(key) {
        // Add a special DELAY entry whenever another entry is added.
        if (window.M.util.pending_js.length == 0) {
            window.M.util.pending_js.push('DELAY');
        }
        window.M.util.pending_js.push(key);

        log('PENDING+: ' + window.M.util.pending_js);
    };

    /**
     * Removes a pending key from the array. If this would clear the array, the actual clear only
     * takes effect after the queued events are finished.
     *
     * @param {string} key Key to remove
     */
    var removePending = function(key) {
        // Remove the key immediately.
        window.M.util.pending_js = window.M.util.pending_js.filter(function(x) { // eslint-disable-line camelcase
            return x !== key;
        });
        log('PENDING-: ' + window.M.util.pending_js);

        // If the only thing left is DELAY, then remove that as well, later...
        if (window.M.util.pending_js.length === 1) {
            runAfterEverything(function() {
                // Check there isn't a spinner...
                updateSpinner();

                // Only remove it if the pending array is STILL empty after all that.
                if (window.M.util.pending_js.length === 1) {
                    window.M.util.pending_js = []; // eslint-disable-line camelcase
                    log('PENDING-: ' + window.M.util.pending_js);
                }
            });
        }
    };

    /**
     * Adds a pending key to the array, but removes it after some setTimeouts finish.
     */
    var addPendingDelay = function() {
        addPending('...');
        removePending('...');
    };

    // Override XMLHttpRequest to mark things pending while there is a request waiting.
    var realOpen = XMLHttpRequest.prototype.open;
    var requestIndex = 0;
    XMLHttpRequest.prototype.open = function() {
        var index = requestIndex++;
        var key = 'httprequest-' + index;

        // Add to the list of pending requests.
        addPending(key);

        // Detect when it finishes and remove it from the list.
        this.addEventListener('loadend', function() {
            removePending(key);
        });

        return realOpen.apply(this, arguments);
    };

    var waitingSpinner = false;

    /**
     * Checks if a loading spinner is present and visible; if so, adds it to the pending array
     * (and if not, removes it).
     */
    var updateSpinner = function() {
        var spinner = document.querySelector('span.core-loading-spinner');
        if (spinner && spinner.offsetParent) {
            if (!waitingSpinner) {
                addPending('spinner');
                waitingSpinner = true;
            }
        } else {
            if (waitingSpinner) {
                removePending('spinner');
                waitingSpinner = false;
            }
        }
    };

    // It would be really beautiful if you could detect CSS transitions and animations, that would
    // cover almost everything, but sadly there is no way to do this because the transitionstart
    // and animationcancel events are not implemented in Chrome, so we cannot detect either of
    // these reliably. Instead, we have to look for any DOM changes and do horrible polling. Most
    // of the animations are set to 500ms so we allow it to continue from 500ms after any DOM
    // change.

    var recentMutation = false;
    var lastMutation;

    /**
     * Called from the mutation callback to remove the pending tag after 500ms if nothing else
     * gets mutated.
     *
     * This will be called after 500ms, then every 100ms until there have been no mutation events
     * for 500ms.
     */
    var pollRecentMutation = function() {
        if (Date.now() - lastMutation > 500) {
            recentMutation = false;
            removePending('dom-mutation');
        } else {
            setTimeout(pollRecentMutation, 100);
        }
    };

    /**
     * Mutation callback, called whenever the DOM is mutated.
     */
    var mutationCallback = function() {
        lastMutation = Date.now();
        if (!recentMutation) {
            recentMutation = true;
            addPending('dom-mutation');
            setTimeout(pollRecentMutation, 500);
        }
        // Also update the spinner presence if needed.
        updateSpinner();
    };

    // Set listener using the mutation callback.
    var observer = new MutationObserver(mutationCallback);
    observer.observe(document, {attributes: true, childList: true, subtree: true});

    /**
     * Generic shared function to find possible xpath matches within the document, that are visible,
     * and then process them using a callback function.
     *
     * @param {string} xpath Xpath to use
     * @param {function} process Callback function that handles each matched node
     */
    var findPossibleMatches = function(xpath, process) {
        var matches = document.evaluate(xpath, document);
        while (true) {
            var match = matches.iterateNext();
            if (!match) {
                break;
            }
            // Skip invisible text nodes.
            if (!match.offsetParent) {
                continue;
            }

            process(match);
        }
    };

    /**
     * Function to find an element based on its text or Aria label.
     *
     * @param {string} text Text (full or partial)
     * @param {string} [near] Optional 'near' text - if specified, must have a single match on page
     * @return {HTMLElement} Found element
     * @throws {string} Error message beginning 'ERROR:' if something went wrong
     */
    var findElementBasedOnText = function(text, near) {
        // Find all the elements that contain this text (and don't have a child element that
        // contains it - i.e. the most specific elements).
        var escapedText = text.replace('"', '""');
        var exactMatches = [];
        var anyMatches = [];
        findPossibleMatches('//*[contains(normalize-space(.), "' + escapedText +
                '") and not(child::*[contains(normalize-space(.), "' + escapedText + '")])]',
                function(match) {
                    // Get the text. Note that innerText returns capitalised values for Android buttons
                    // for some reason, so we'll have to do a case-insensitive match.
                    var matchText = match.innerText.trim().toLowerCase();

                    // Let's just check - is this actually a label for something else? If so we will click
                    // that other thing instead.
                    var labelId = document.evaluate('string(ancestor-or-self::ion-label[@id][1]/@id)', match).stringValue;
                    if (labelId) {
                        var target = document.querySelector('*[aria-labelledby=' + labelId + ']');
                        if (target) {
                            match = target;
                        }
                    }

                    // Add to array depending on if it's an exact or partial match.
                    if (matchText === text.toLowerCase()) {
                        exactMatches.push(match);
                    } else {
                        anyMatches.push(match);
                    }
                });

        // Find all the Aria labels that contain this text.
        var exactLabelMatches = [];
        var anyLabelMatches = [];
        findPossibleMatches('//*[@aria-label and contains(@aria-label, "' + escapedText + '")]' +
                '| //img[@alt and contains(@alt, "' + escapedText + '")]', function(match) {
                    // Add to array depending on if it's an exact or partial match.
                    var attributeData = match.getAttribute('aria-label') || match.getAttribute('alt');
                    if (attributeData.trim() === text) {
                        exactLabelMatches.push(match);
                    } else {
                        anyLabelMatches.push(match);
                    }
                });

        // If the 'near' text is set, use it to filter results.
        var nearAncestors = [];
        if (near !== undefined) {
            escapedText = near.replace('"', '""');
            var exactNearMatches = [];
            var anyNearMatches = [];
            findPossibleMatches('//*[contains(normalize-space(.), "' + escapedText +
                    '") and not(child::*[contains(normalize-space(.), "' + escapedText +
                    '")])]', function(match) {
                        // Get the text.
                        var matchText = match.innerText.trim();

                        // Add to array depending on if it's an exact or partial match.
                        if (matchText === text) {
                            exactNearMatches.push(match);
                        } else {
                            anyNearMatches.push(match);
                        }
                    });

            var nearFound = null;

            // If there is an exact text match, use that (regardless of other matches).
            if (exactNearMatches.length > 1) {
                throw new Error('Too many exact matches for near text');
            } else if (exactNearMatches.length) {
                nearFound = exactNearMatches[0];
            }

            if (nearFound === null) {
                // If there is one partial text match, use that.
                if (anyNearMatches.length > 1) {
                    throw new Error('Too many partial matches for near text');
                } else if (anyNearMatches.length) {
                    nearFound = anyNearMatches[0];
                }
            }

            if (!nearFound) {
                throw new Error('No matches for near text');
            }

            while (nearFound) {
                nearAncestors.push(nearFound);
                nearFound = nearFound.parentNode;
            }

            /**
             * Checks the number of steps up the tree from a specified node before getting to an
             * ancestor of the 'near' item
             *
             * @param {HTMLElement} node HTML node
             * @returns {number} Number of steps up, or Number.MAX_SAFE_INTEGER if it never matched
             */
            var calculateNearDepth = function(node) {
                var depth = 0;
                while (node) {
                    var ancestorDepth = nearAncestors.indexOf(node);
                    if (ancestorDepth !== -1) {
                        return depth + ancestorDepth;
                    }
                    node = node.parentNode;
                    depth++;
                }
                return Number.MAX_SAFE_INTEGER;
            };

            /**
             * Reduces an array to include only the nearest in each category.
             *
             * @param {Array} arr Array to
             * @return {Array} Array including only the items with minimum 'near' depth
             */
            var filterNonNearest = function(arr) {
                var nearDepth = arr.map(function(node) {
                    return calculateNearDepth(node);
                });
                var minDepth = Math.min.apply(null, nearDepth);
                return arr.filter(function(element, index) {
                    return nearDepth[index] == minDepth;
                });
            };

            // Filter all the category arrays.
            exactMatches = filterNonNearest(exactMatches);
            exactLabelMatches = filterNonNearest(exactLabelMatches);
            anyMatches = filterNonNearest(anyMatches);
            anyLabelMatches = filterNonNearest(anyLabelMatches);
        }

        // Select the resulting match. Note this 'do' loop is not really a loop, it is just so we
        // can easily break out of it as soon as we find a match.
        var found = null;
        do {
            // If there is an exact text match, use that (regardless of other matches).
            if (exactMatches.length > 1) {
                throw new Error('Too many exact matches for text');
            } else if (exactMatches.length) {
                found = exactMatches[0];
                break;
            }

            // If there is an exact label match, use that.
            if (exactLabelMatches.length > 1) {
                throw new Error('Too many exact label matches for text');
            } else if (exactLabelMatches.length) {
                found = exactLabelMatches[0];
                break;
            }

            // If there is one partial text match, use that.
            if (anyMatches.length > 1) {
                throw new Error('Too many partial matches for text');
            } else if (anyMatches.length) {
                found = anyMatches[0];
                break;
            }

            // Finally if there is one partial label match, use that.
            if (anyLabelMatches.length > 1) {
                throw new Error('Too many partial label matches for text');
            } else if (anyLabelMatches.length) {
                found = anyLabelMatches[0];
                break;
            }
        } while (false);

        if (!found) {
            throw new Error('No matches for text');
        }

        return found;
    };

    /**
     * Function to find and click an app standard button.
     *
     * @param {string} button Type of button to press
     * @return {string} OK if successful, or ERROR: followed by message
     */
    var behatPressStandard = function(button) {
        log('Action - Click standard button: ' + button);
        var selector;
        switch (button) {
            case 'back' :
                selector = 'ion-navbar > button.back-button-md';
                break;
            case 'main menu' :
                selector = 'page-core-mainmenu .tab-button > ion-icon[aria-label=more]';
                break;
            case 'page menu' :
                // This lang string was changed in app version 3.6.
                selector = 'core-context-menu > button[aria-label=Info], ' +
                        'core-context-menu > button[aria-label=Information]';
                break;
            default:
                return 'ERROR: Unsupported standard button type';
        }
        var buttons = Array.from(document.querySelectorAll(selector));
        var foundButton = null;
        var tooMany = false;
        buttons.forEach(function(button) {
            if (button.offsetParent) {
                if (foundButton === null) {
                    foundButton = button;
                } else {
                    tooMany = true;
                }
            }
        });
        if (!foundButton) {
            return 'ERROR: Could not find button';
        }
        if (tooMany) {
            return 'ERROR: Found too many buttons';
        }
        foundButton.click();

        // Mark busy until the button click finishes processing.
        addPendingDelay();

        return 'OK';
    };

    /**
     * When there is a popup, clicks on the backdrop.
     *
     * @return {string} OK if successful, or ERROR: followed by message
     */
    var behatClosePopup = function() {
        log('Action - Close popup');

        var backdrops = Array.from(document.querySelectorAll('ion-backdrop'));
        var found = null;
        var tooMany = false;
        backdrops.forEach(function(backdrop) {
            if (backdrop.offsetParent) {
                if (found === null) {
                    found = backdrop;
                } else {
                    tooMany = true;
                }
            }
        });
        if (!found) {
            return 'ERROR: Could not find backdrop';
        }
        if (tooMany) {
            return 'ERROR: Found too many backdrops';
        }
        found.click();

        // Mark busy until the click finishes processing.
        addPendingDelay();

        return 'OK';
    };

    /**
     * Function to press arbitrary item based on its text or Aria label.
     *
     * @param {string} text Text (full or partial)
     * @param {string} near Optional 'near' text - if specified, must have a single match on page
     * @return {string} OK if successful, or ERROR: followed by message
     */
    var behatPress = function(text, near) {
        log('Action - Press ' + text + (near === undefined ? '' : ' - near ' + near));

        var found;
        try {
            found = findElementBasedOnText(text, near);
        } catch (error) {
            return 'ERROR: ' + error.message;
        }

        // Simulate a mouse click on the button.
        found.scrollIntoView();
        var rect = found.getBoundingClientRect();
        var eventOptions = {clientX: rect.left + rect.width / 2, clientY: rect.top + rect.height / 2,
                bubbles: true, view: window, cancelable: true};
        setTimeout(function() {
            found.dispatchEvent(new MouseEvent('mousedown', eventOptions));
        }, 0);
        setTimeout(function() {
            found.dispatchEvent(new MouseEvent('mouseup', eventOptions));
        }, 0);
        setTimeout(function() {
            found.dispatchEvent(new MouseEvent('click', eventOptions));
        }, 0);

        // Mark busy until the button click finishes processing.
        addPendingDelay();

        return 'OK';
    };

    /**
     * Gets the currently displayed page header.
     *
     * @return {string} OK: followed by header text if successful, or ERROR: followed by message.
     */
    var behatGetHeader = function() {
        log('Action - Get header');

        var result = null;
        var resultCount = 0;
        var titles = Array.from(document.querySelectorAll('ion-header ion-title'));
        titles.forEach(function(title) {
            if (title.offsetParent) {
                result = title.innerText.trim();
                resultCount++;
            }
        });

        if (resultCount > 1) {
            return 'ERROR: Too many possible titles';
        } else if (!resultCount) {
            return 'ERROR: No title found';
        } else {
            return 'OK:' + result;
        }
    };

    /**
     * Sets the text of a field to the specified value.
     *
     * This currently matches fields only based on the placeholder attribute.
     *
     * @param {string} field Field name
     * @param {string} value New value
     * @return {string} OK or ERROR: followed by message
     */
    var behatSetField = function(field, value) {
        log('Action - Set field ' + field + ' to: ' + value);

        // Find input(s) with given placeholder.
        var escapedText = field.replace('"', '""');
        var exactMatches = [];
        var anyMatches = [];
        findPossibleMatches(
                '//input[contains(@placeholder, "' + escapedText + '")] |' +
                '//textarea[contains(@placeholder, "' + escapedText + '")] |' +
                '//core-rich-text-editor/descendant::div[contains(@data-placeholder-text, "' +
                escapedText + '")]', function(match) {
                    // Add to array depending on if it's an exact or partial match.
                    var placeholder;
                    if (match.nodeName === 'DIV') {
                        placeholder = match.getAttribute('data-placeholder-text');
                    } else {
                        placeholder = match.getAttribute('placeholder');
                    }
                    if (placeholder.trim() === field) {
                        exactMatches.push(match);
                    } else {
                        anyMatches.push(match);
                    }
                });

        // Select the resulting match.
        var found = null;
        do {
            // If there is an exact text match, use that (regardless of other matches).
            if (exactMatches.length > 1) {
                return 'ERROR: Too many exact placeholder matches for text';
            } else if (exactMatches.length) {
                found = exactMatches[0];
                break;
            }

            // If there is one partial text match, use that.
            if (anyMatches.length > 1) {
                return 'ERROR: Too many partial placeholder matches for text';
            } else if (anyMatches.length) {
                found = anyMatches[0];
                break;
            }
        } while (false);

        if (!found) {
            return 'ERROR: No matches for text';
        }

        // Functions to get/set value depending on field type.
        var setValue;
        var getValue;
        switch (found.nodeName) {
            case 'INPUT':
            case 'TEXTAREA':
                setValue = function(text) {
                    found.value = text;
                };
                getValue = function() {
                    return found.value;
                };
                break;
            case 'DIV':
                setValue = function(text) {
                    found.innerHTML = text;
                };
                getValue = function() {
                    return found.innerHTML;
                };
                break;
        }

        // Pretend we have cut and pasted the new text.
        var event;
        if (getValue() !== '') {
            event = new InputEvent('input', {bubbles: true, view: window, cancelable: true,
                inputType: 'devareByCut'});
            setTimeout(function() {
                setValue('');
                found.dispatchEvent(event);
            }, 0);
        }
        if (value !== '') {
            event = new InputEvent('input', {bubbles: true, view: window, cancelable: true,
                inputType: 'insertFromPaste', data: value});
            setTimeout(function() {
                setValue(value);
                found.dispatchEvent(event);
            }, 0);
        }

        return 'OK';
    };

    // Make some functions publicly available for Behat to call.
    window.behat = {
        pressStandard : behatPressStandard,
        closePopup : behatClosePopup,
        press : behatPress,
        setField : behatSetField,
        getHeader : behatGetHeader,
    };
})();
