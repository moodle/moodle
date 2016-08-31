/**
 * JavaScript for the framework selectors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package frameworkselector
 */

// Define the core_framework namespace if it has not already been defined
M.local_framework_selector = M.local_framework_selector || {};
// Define a framework selectors array for against the cure_framework namespace
M.local_framework_selector.framework_selectors = [];
/**
 * Retrieves an instantiated framework selector or null if there isn't one by the requested name
 * @param {string} name The name of the selector to retrieve
 * @return bool
 */
M.local_framework_selector.get_framework_selector = function (name) {
    return this.framework_selectors[name] || null;
};

/**
 * Initialise a new framework selector.
 *
 * @param {YUI} Y The YUI3 instance
 * @param {string} name the control name/id.
 * @param {string} hash the hash that identifies this selector in the framework's session.
 * @param {array} extrafields extra fields we are displaying for each framework in addition to fullname.
 * @param {string} lastsearch The last search that took place
 */
M.local_framework_selector.init_framework_selector = function (Y, name, hash, extrafields, lastsearch) {
    // Creates a new framework_selector object
    var framework_selector = {
        /** This id/name used for this control in the HTML. */
        name : name,
        /** Array of fields to display for each framework, in addition to fullname. */
        extrafields: extrafields,
        /** Number of seconds to delay before submitting a query request */
        querydelay : 0.5,
        /** The input element that contains the search term. */
        searchfield : Y.one('#' + name + '_searchtext'),
        /** The clear button. */
        clearbutton : null,
        /** The select element that contains the list of frameworks. */
        listbox : Y.one('#' + name),
        /** Used to hold the timeout id of the timeout that waits before doing a search. */
        timeoutid : null,
        /** The last string that we searched for, so we can avoid unnecessary repeat searches. */
        lastsearch : lastsearch,
        /** Whether any options where selected last time we checked. Used by
         *  handle_selection_change to track when this status changes. */
        selectionempty : true,
        /**
         * Initialises the framework selector object
         * @constructor
         */
        init : function() {
            // Hide the search button and replace it with a label.
            var searchbutton = Y.one('#' + this.name + '_searchbutton');
            this.searchfield.insert(Y.Node.create('<label for="' + this.name + '_searchtext">' + searchbutton.get('value') + '</label>'), this.searchfield);
            searchbutton.remove();

            // Hook up the event handler for when the search text changes.
            this.searchfield.on('keyup', this.handle_keyup, this);

            // Hook up the event handler for when the selection changes.
            this.listbox.on('keyup', this.handle_selection_change, this);
            this.listbox.on('click', this.handle_selection_change, this);
            this.listbox.on('change', this.handle_selection_change, this);

            // And when the search any substring preference changes. Do an immediate re-search.
            Y.one('#frameworkselector_searchanywhereid').on('click', this.handle_searchanywhere_change, this);

            // Define our custom event.
            //this.createEvent('selectionchanged');
            this.selectionempty = this.is_selection_empty();

            // Replace the Clear submit button with a clone that is not a submit button.
            var clearbtn = Y.one('#' + this.name + '_clearbutton');
            this.clearbutton = Y.Node.create('<input type="button" value="' + clearbtn.get('value') + '" />');
            clearbtn.replace(Y.Node.getDOMNode(this.clearbutton));
            this.clearbutton.set('id', + this.name + "_clearbutton");
            this.clearbutton.on('click', this.handle_clear, this);

            this.send_query(false);
        },
        /**
         * Key up hander for the search text box.
         * @param {Y.Event} e the keyup event.
         */
        handle_keyup : function(e) {
            //  Trigger an ajax search after a delay.
            this.cancel_timeout();
            this.timeoutid = setTimeout(function(obj){obj.send_query(false)}, this.querydelay * 1000, this);

            // Enable or diable the clear button.
            this.clearbutton.set('disabled', (this.get_search_text() == ''));

            // If enter was pressed, prevent a form submission from happening.
            if (e.keyCode == 13) {
                e.halt();
            }
        },
        /**
         * Handles when the selection has changed. If the selection has changed from
         * empty to not-empty, or vice versa, then fire the event handlers.
         */
        handle_selection_change : function() {
            var isselectionempty = this.is_selection_empty();
            if (isselectionempty !== this.selectionempty) {
                this.fire('framework_selector:selectionchanged', isselectionempty);
            }
            this.selectionempty = isselectionempty;
        },
        /**
         * Trigger a re-search when the 'search any substring' option is changed.
         */
        handle_searchanywhere_change : function() {
            if (this.lastsearch != '' && this.get_search_text() != '') {
                this.send_query(true);
            }
        },
        /**
         * Click handler for the clear button..
         */
        handle_clear : function() {
            this.searchfield.set('value', '');
            this.clearbutton.set('disabled',true);
            this.send_query(false);
        },
        /**
         * Fires off the ajax search request.
         */
        send_query : function(forceresearch) {
            // Cancel any pending timeout.
            this.cancel_timeout();

            var value = this.get_search_text();
            this.searchfield.set('class', '');
            if (this.lastsearch == value && !forceresearch) {
                return;
            }

            Y.io(M.cfg.wwwroot + '/local/framework_selector/search.php', {
                method: 'POST',
                data: 'selectorid=' + hash + '&sesskey=' + M.cfg.sesskey + '&search=' + value + '&frameworkselector_searchanywhere=' + this.get_option('searchanywhere'),
                on: {
                    success:this.handle_response,
                    failure:this.handle_failure
                },
                context:this
            });

            this.lastsearch = value;
            this.listbox.setStyle('background','url(' + M.util.image_url('i/loading', 'moodle') + ') no-repeat center center');
        },
        /**
         * Handle what happens when we get some data back from the search.
         * @param {int} requestid not used.
         * @param {object} response the list of frameworks that was returned.
         */
        handle_response : function(requestid, response) {
            try {
                this.listbox.setStyle('background','');
                var data = Y.JSON.parse(response.responseText);
                this.output_options(data);
            } catch (e) {
                this.handle_failure();
            }
        },
        /**
         * Handles what happens when the ajax request fails.
         */
        handle_failure : function() {
            this.listbox.setStyle('background','');
            this.searchfield.addClass('error');

            // If we are in developer debug mode, output a link to help debug the failure.
            if (M.cfg.developerdebug) {
                this.searchfield.insert(Y.Node.create('<a href="' + M.cfg.wwwroot + '/local/framework_selector/search.php?selectorid=' +
                                                       hash + '&sesskey=' + M.cfg.sesskey + '&search=' + this.get_search_text() +
                                                      '&debug=1">Ajax call failed. Click here to try the search call directly.</a>'));
            }
        },
        /**
         * This method should do the same sort of thing as the PHP method
         * framework_selector_base::output_options.
         * @param {object} data the list of frameworks to populate the list box with.
         */
        output_options : function(data) {
            // Clear out the existing options, keeping any ones that are already selected.
            var selectedframeworks = {};
            this.listbox.all('optgroup').each(function(optgroup){
                optgroup.all('option').each(function(option){
                    if (option.get('selected')) {
                        selectedframeworks[option.get('value')] = {
                            id : option.get('value'),
                            name : option.get('innerText') || option.get('textContent'),
                            disabled: option.get('disabled')
                        }
                    }
                    option.remove();
                }, this);
                optgroup.remove();
            }, this);

            // Output each optgroup.
            var count = 0;
            for (var groupname in data.results) {
                this.output_group(groupname, data.results[groupname], selectedframeworks, true);
                count++;
            }
            if (!count) {
                var searchstr = (this.lastsearch != '')?this.insert_search_into_str(M.str.local_framework_selector.nomatchingframeworks, this.lastsearch):M.str.moodle.none;
                this.output_group(searchstr, {}, selectedframeworks, true)
            }

            // If there were previously selected frameworks who do not match the search, show them too.
            if (this.get_option('preserveselected') && selectedframeworks) {
                this.output_group(this.insert_search_into_str(M.str.local_framework_selector.previouslyselectedframeworks, this.lastsearch), selectedframeworks, true, false);
            }
            this.handle_selection_change();
        },
        /**
         * This method should do the same sort of thing as the PHP method
         * framework_selector_base::output_optgroup.
         *
         * @param {string} groupname the label for this optgroup.v
         * @param {object} frameworks the frameworks to put in this optgroup.
         * @param {boolean|object} selectedframeworks if true, select the frameworks in this group.
         * @param {boolean} processsingle
         */
        output_group : function(groupname, frameworks, selectedframeworks, processsingle) {
            var optgroup = Y.Node.create('<optgroup></optgroup>');
            var count = 0;
            for (var frameworkid in frameworks) {
                var framework = frameworks[frameworkid];
                var option = Y.Node.create('<option value="' + frameworkid + '">' + framework.name + '</option>');
                if (framework.disabled) {
                    option.set('disabled', true);
                } else if (selectedframeworks === true || selectedframeworks[frameworkid]) {
                    option.set('selected', true);
                } else {
                    option.set('selected', false);
                }
                optgroup.append(option);
                count++;
            }

            if (count > 0) {
                optgroup.set('label', groupname + ' (' + count + ')');
                if (processsingle && count === 1 && this.get_option('autoselectunique') && option.get('disabled')) {
                    option.set('selected', true);
                }
            } else {
                optgroup.append(Y.Node.create('<option disabled="disabled">\u00A0</option>'));
            }
            this.listbox.append(optgroup);
        },
        /**
         * Replace
         * @param {string} str
         * @param {string} search The search term
         * @return string
         */
        insert_search_into_str : function(str, search) {
            return str.replace("%%SEARCHTERM%%", search);
        },
        /**
         * Gets the search text
         * @return String the value to search for, with leading and trailing whitespace trimmed.
         */
        get_search_text : function() {
            return this.searchfield.get('value').toString().replace(/^ +| +$/, '');
        },
        /**
         * Returns true if the selection is empty (nothing is selected)
         * @return Boolean check all the options and return whether any are selected.
         */
        is_selection_empty : function() {
            var selection = false;
            this.listbox.all('option').each(function(){
                if (this.get('selected')) {
                    selection = true;
                }
            });
            return !(selection);
        },
        /**
         * Cancel the search delay timeout, if there is one.
         */
        cancel_timeout : function() {
            if (this.timeoutid) {
                clearTimeout(this.timeoutid);
                this.timeoutid = null;
            }
        },
        /**
         * @param {string} name The name of the option to retrieve
         * @return the value of one of the option checkboxes.
         */
        get_option : function(name) {
            var checkbox = Y.one('#frameworkselector_' + name + 'id');
            if (checkbox) {
                return (checkbox.get('checked'));
            } else {
                return false;
            }
        }
    };
    // Augment the framework selector with the EventTarget class so that we can use
    // custom events
    Y.augment(framework_selector, Y.EventTarget, null, null, {});
    // Initialise the framework selector
    framework_selector.init();
    // Store the framework selector so that it can be retrieved
    this.framework_selectors[name] = framework_selector;
    // Return the framework selector
    return framework_selector;
};

/**
 * Initialise a class that updates the framework's preferences when they change one of
 * the options checkboxes.
 * @constructor
 * @param {YUI} Y
 * @return Tracker object
 */
M.local_framework_selector.init_framework_selector_options_tracker = function(Y) {
    // Create a framework selector options tracker
    var framework_selector_options_tracker = {
        /**
         * Initlises the option tracker and gets everything going.
         * @constructor
         */
        init : function() {
            var settings = [
                'frameworkselector_preserveselected',
                'frameworkselector_autoselectunique',
                'frameworkselector_searchanywhere'
            ];
            for (var s in settings) {
                var setting = settings[s];
                Y.one('#' + setting + 'id').on('click', this.set_user_preference, this, setting);
            }
        },
        /**
         * Sets a user preference for the options tracker
         * @param {Y.Event|null} e
         * @param {string} name The name of the preference to set
         */
        set_user_preference : function(e, name) {
            M.util.set_user_preference(name, Y.one('#' + name + 'id').get('checked'));
        }
    };
    // Initialise the options tracker
    framework_selector_options_tracker.init();
    // Return it just incase it is ever wanted
    return framework_selector_options_tracker;
};
