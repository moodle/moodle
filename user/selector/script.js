// JavaScript for the user selectors.
// This is somewhat inspired by the autocomplete component in YUI.
// license: http://www.gnu.org/copyleft/gpl.html GNU Public License
// package: userselector

/**
 * Initialise a new user selector.
 * @constructor
 * @param String name the control name/id.
 * @param String hash the hash that identifies this selector in the user's session.
 * @param Array extrafields extra fields we are displaying for each user in addition to fullname.
 * @param String label used for the optgroup of users who are selected but who do not match the current search.
 */
function user_selector(name, hash, extrafields, lastsearch, strprevselected, strnomatchingusers, strnone) {
    this.name = name;
    this.extrafields = extrafields;
    this.strprevselected = strprevselected;
    this.strnomatchingusers = strnomatchingusers;
    this.strnone = strnone;
    this.searchurl = moodle_cfg.wwwroot + '/user/selector/search.php?selectorid=' +
            hash + '&sesskey=' + moodle_cfg.sesskey + '&search='

    // Set up the data source.
    this.datasource = new YAHOO.util.XHRDataSource(this.searchurl); 
    this.datasource.connXhrMode = 'cancelStaleRequests';
    this.datasource.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
    this.datasource.responseSchema = {resultsList: 'results'};

    // Find some key HTML elements.
    this.searchfield = document.getElementById(this.name + '_searchtext');
    this.listbox = document.getElementById(this.name);

    // Hide the search button and replace it with a label.
    var searchbutton = document.getElementById(this.name + '_searchbutton');
    var label = document.createElement('label');
    label.htmlFor = this.name + '_searchtext';
    label.appendChild(document.createTextNode(searchbutton.value));
    this.searchfield.parentNode.insertBefore(label, this.searchfield);
    searchbutton.parentNode.removeChild(searchbutton);

    // Hook up the event handler for when the search text changes.
    var oself = this;
    YAHOO.util.Event.addListener(this.searchfield, "keyup", function(e) { oself.handle_keyup(e) });
    this.lastsearch = lastsearch;

    // Define our custom event.
    this.createEvent('selectionchanged');
    this.selectionempty = this.is_selection_empty();
    user_selector.allselectors[name] = this;

    // Hook up the event handler for when the selection changes.
    YAHOO.util.Event.addListener(this.listbox, "keyup", function(e) { oself.handle_selection_change() });
    YAHOO.util.Event.addListener(this.listbox, "click", function(e) { oself.handle_selection_change() });
    YAHOO.util.Event.addListener(this.listbox, "change", function(e) { oself.handle_selection_change() });

    // And when the search any substring preference changes. Do an immediate re-search.
    YAHOO.util.Event.addListener('userselector_searchanywhereid', 'click', function(e) { oself.handle_searchanywhere_change() });

    // Replace the Clear submit button with a clone that is not a submit button.
    var oldclearbutton = document.getElementById(this.name + '_clearbutton');
    this.clearbutton = document.createElement('input');
    this.clearbutton.type = 'button';
    this.clearbutton.value = oldclearbutton.value;
    this.clearbutton.id = oldclearbutton.id;
    oldclearbutton.id = '';
    oldclearbutton.parentNode.insertBefore(this.clearbutton, oldclearbutton);
    oldclearbutton.parentNode.removeChild(oldclearbutton);

    // Enable or diable the clear button.
    this.clearbutton.disabled = this.get_search_text() == '';

    // Hook up the event handler for the clear button.
    YAHOO.util.Event.addListener(this.clearbutton, "click", function(e) { oself.handle_clear() });

    // If the contents of the search box is different from the search that was
    // done on the server, reload the options. (This happens, for example,
    // in Firefox. Go to the role assign page - the search box will be blank.
    // Type something in the search box. Then click reload. The text will stay
    // in the search box, but the server does not know about the new search term,
    // so without this line, the list would contain all the users.
    this.send_query(false);
}

/**
 * A list of all the user_selectors on this page. Use by user_selector.get.
 *
 * @property allselectors
 * @type Object
 */
user_selector.allselectors = {};

/**
 * @param String name the name of a user selector on this page.
 * @return user_selector the named user selector.
 */
user_selector.get = function(name) {
    return user_selector.allselectors[name];
}

// Fields set be the constructor ===============================================

/**
 * This id/name used for this control in the HTML.
 * @property name
 * @type String
 */
user_selector.prototype.name = null;

/**
 * Array of fields to display for each user, in addition to fullname.
 * @property extrafields
 * @type Array
 */
user_selector.prototype.extrafields = [];

/**
 * Name of the previously selected users group.
 *
 * @property strprevselected
 * @type String
 */
user_selector.prototype.strprevselected = '';

/**
 * Name of the no matching users group.
 *
 * @property strnomatchingusers
 * @type String
 */
user_selector.prototype.strnomatchingusers = '';

/**
 * Name of the no matching users group when empty.
 *
 * @property strnone
 * @type String
 */
user_selector.prototype.strnone = '';

// Fields that configure the control's behaviour ===============================

/**
 * Number of seconds to delay before submitting a query request. If a query
 * request is received before a previous one has completed its delay, the
 * previous request is cancelled and the new request is set to the delay.
 *
 * @property querydelay
 * @type Number
 * @default 0.2
 */
user_selector.prototype.querydelay = 0.5;

// Internal fields =============================================================

/**
 * The URL for the datasource.
 * @property searchurl
 * @type String
 */
user_selector.prototype.searchurl = null;

/**
 * The datasource used to fetch lists of users from Moodle.
 * @property datasource
 * @type YAHOO.widget.DataSource
 */
user_selector.prototype.datasource = null;

/**
 * The input element that contains the search term.
 * 
 * @property searchfield
 * @type HTMLInputElement
 */
user_selector.prototype.searchfield = null;

/**
 * The clear button.
 * 
 * @property clearbutton
 * @type HTMLInputElement
 */
user_selector.prototype.clearbutton = null;

/**
 * The select element that contains the list of users.
 * 
 * @property listbox
 * @type HTMLSelectElement
 */
user_selector.prototype.listbox = null;

/**
 * Used to hold the timeout id of the timeout that waits before doing a search.
 * 
 * @property timeoutid
 * @type Number
 */
user_selector.prototype.timeoutid = null;

/**
 * The last string that we searched for, so we can avoid unnecessary repeat searches.
 * 
 * @property lastsearch
 * @type String
 */
user_selector.prototype.lastsearch = null;

/**
 * Used while the list of options is being refreshed, to track options that were
 * selected before, so they are not lost if they do not appear in the search results.
 *
 * @property selected
 * @type Object
 */
user_selector.prototype.selected = null;

/**
 * Used while the list of options is being refreshed to determine if there is only
 * one user matching the search, so they can be automatically selected.
 *
 * @property onlyoption
 * @type HTMLOptionElement
 **/
user_selector.prototype.onlyoption = null;

/**
 * Whether any options where selected last time we checked. Used by
 * handle_selection_change to track when this status changes.
 *
 * @property selectionempty
 * @type Boolean
 */
user_selector.prototype.selectionempty = true;

// Methods for handing various events ==========================================

/**
 * Key up hander for the search text box.
 * @param object e the keyup event.
 */
user_selector.prototype.handle_keyup = function(e) {
    //  Trigger an ajax search after a delay.
    this.cancel_timeout();
    var oself = this;
    this.timeoutid = setTimeout(function() { oself.send_query(false) }, this.querydelay * 1000);

    // Enable or diable the clear button.
    this.clearbutton.disabled = this.get_search_text() == '';

    // If enter was pressed, prevent a form submission from happening.
    var keyCode = e.keyCode ? e.keyCode : e.which;
    if (keyCode == 13) {
        YAHOO.util.Event.stopEvent(e);
    }
}

/**
 * Cancel the search delay timeout, if there is one.
 */
user_selector.prototype.cancel_timeout = function() {
    if (this.timeoutid) {
        clearTimeout(this.timeoutid);
        this.timeoutid = null;
    }
}

/**
 * Click handler for the clear button..
 */
user_selector.prototype.handle_clear = function() {
    this.searchfield.value = '';
    this.clearbutton.disabled = true;
    this.send_query(false);
}

/**
 * Trigger a re-search when the 'search any substring' option is changed.
 */
user_selector.prototype.handle_searchanywhere_change = function() {
    if (this.lastsearch != '' && this.get_search_text() != '') {
        this.send_query(true);
    }
}

/**
 * @return String the value to search for, with leading and trailing whitespace trimmed.
 */
user_selector.prototype.get_search_text = function() {
    return this.searchfield.value.replace(/^ +| +$/, '');
}

/**
 * @return the value of one of the option checkboxes.<b> 
 */
user_selector.prototype.get_option = function(name) {
    var checkbox = document.getElementById('userselector_' + name + 'id');
    if (checkbox) {
        return checkbox.checked;
    } else {
        return false;
    }
}

/**
 * Fires off the ajax search request.
 */
user_selector.prototype.send_query = function(forceresearch) {
    // Cancel any pending timeout.
    this.cancel_timeout();

    var value = this.get_search_text();
    this.searchfield.className = '';
    if (this.lastsearch == value && !forceresearch) {
        return;
    }
    this.datasource.sendRequest(value + '&userselector_searchanywhere=' + this.get_option('searchanywhere'), {
        success: this.handle_response,
        failure: this.handle_failure,
        scope: this
    });
    this.lastsearch = value;
    this.listbox.style.background = 'url(' + moodle_cfg.pixpath + '/i/loading.gif) no-repeat center center';
}

/**
 * Handle what happens when we get some data back from the search.
 * @param Object request not used.
 * @param Object data the list of users that was returned.
 */
user_selector.prototype.handle_response = function(request, data) {
    this.listbox.style.background = '';
    this.output_options(data);
}

/**
 * Handles what happens when the ajax request fails.
 */
user_selector.prototype.handle_failure = function() {
    this.listbox.style.background = '';
    this.searchfield.className = 'error';

    // If we are in developer debug mode, output a link to help debug the failure.
    if (moodle_cfg.developerdebug) {
        var link = document.createElement('a');
        link.href = this.searchurl + this.get_search_text() + '&debug=1';
        link.appendChild(document.createTextNode('Ajax call failed. Click here to try the search call directly.'))
        this.searchfield.parentNode.appendChild(link);
    }
}

/**
 * @return Boolean check all the options and return whether any are selected.
 */
user_selector.prototype.is_selection_empty = function() {
    var options = this.listbox.getElementsByTagName('option');
    for (i = 0; i < options.length; i++) {
        var option = options[i];
        if (option.selected) {
            return false;
        }
    }
    return true;
}

/**
 * Handles when the selection has changed. If the selection has changed from
 * empty to not-empty, or vice versa, then fire the event handlers.
 */
user_selector.prototype.handle_selection_change = function() {
    var isselectionempty = this.is_selection_empty();
    if (isselectionempty !== this.selectionempty) {
        this.fireEvent('selectionchanged', isselectionempty);
    }
    this.selectionempty = isselectionempty;
}

// Methods for refreshing the list of displayed options ========================
user_selector.prototype.insert_search_into_str = function(string, search) {
    return string.replace("%%SEARCHTERM%%", search);
}

/**
 * This method should do the same sort of thing as the PHP method
 * user_selector_base::output_options.
 * @param object data the list of users to populate the list box with.
 */
user_selector.prototype.output_options = function(data) {
    // Clear out the existing options, keeping any ones that are already selected.
    this.selected = {};
    var groups = this.listbox.getElementsByTagName('optgroup');
    var preserveselected = this.get_option('preserveselected');
    while (groups.length > 0) {
        var optgroup = groups[0]; // Remeber that groups is a live array as we remove optgroups from the select, it updates.
        var options = optgroup.getElementsByTagName('option');
        while (options.length > 0) {
            var option = options[0];
            if (option.selected) {
                var optiontext = option.innerText || option.textContent
                this.selected[option.value] = { id: option.value, name: optiontext, disabled: option.disabled };
            }
            optgroup.removeChild(option);
        }
        this.listbox.removeChild(optgroup);
    }

    var results = data.results[0];

    // Output each optgroup.
    this.onlyoption = null;
    var nogroups = true;
    for (groupname in results) {
        this.output_group(groupname, results[groupname], false);
        nogroups = false;
    }

    if (nogroups) {
        if (this.lastsearch != '') {
            this.output_group(this.insert_search_into_str(this.strnomatchingusers, this.lastsearch), {}, false)
        } else {
            this.output_group(this.strnone, {}, false)
        }
    }

    // If there was only one option matching the search results, select it.
    if (this.get_option('autoselectunique') && this.onlyoption && !this.onlyoption.disabled) {
        this.onlyoption.selected = true;
        if (!this.listbox.multiple) {
            this.selected = {};
        }
        this.handle_selection_change();
    }
    this.onlyoption = null;

    // If there were previously selected users who do not match the search, show them too.
    var areprevselected = false;
    for (user in this.selected) {
        areprevselected = true;
        break;
    }
    if (preserveselected && areprevselected) {
        this.output_group(this.insert_search_into_str(this.strprevselected, this.lastsearch), this.selected, true);
    }
    this.selected = null;
}

/**
 * This method should do the same sort of thing as the PHP method
 * user_selector_base::output_optgroup.
 *
 * @param String groupname the label for this optgroup.v
 * @param Object users the users to put in this optgroup.
 * @param Boolean select if true, select the users in this group.
 */
user_selector.prototype.output_group = function(groupname, users, select) {
    var optgroup = document.createElement('optgroup');
    optgroup.label = groupname;
    var count = 0;
    for (var userid in users) {
        var user = users[userid];
        var option = document.createElement('option');
        option.value = user.id;
        option.appendChild(document.createTextNode(user.name));
        if (user.disabled) {
            option.disabled = 'disabled';
        } else if (select || this.selected[user.id]) {
            option.selected = 'selected';
        }
        delete this.selected[user.id];
        optgroup.appendChild(option);
        if (this.onlyoption === null) {
            this.onlyoption = option;
        } else {
            this.onlyoption = false;
        }
        count++;
    }
    if (count > 0) {
        optgroup.label += ' (' + count + ')';
    } else {
        var option = document.createElement('option');
        option.disabled = 'disabled';
        option.appendChild(document.createTextNode('\u00A0'));
        optgroup.appendChild(option);
    }
    this.listbox.appendChild(optgroup);
}

// Say that we want to be a source of custom events.
YAHOO.lang.augmentProto(user_selector, YAHOO.util.EventProvider);

/**
 * Initialise a class that updates the user's preferences when they change one of
 * the options checkboxes.
 * @constructor
 */
function user_selector_options_tracker() {
    var oself = this;

    // Add event listeners.
    YAHOO.util.Event.addListener('userselector_preserveselectedid', 'click',
            function(e) { oself.handle_option_change('userselector_preserveselected') });
    YAHOO.util.Event.addListener('userselector_autoselectuniqueid', 'click',
            function(e) { oself.handle_option_change('userselector_autoselectunique') });
    YAHOO.util.Event.addListener('userselector_searchanywhereid', 'click',
            function(e) { oself.handle_option_change('userselector_searchanywhere') });
}

user_selector_options_tracker.prototype.handle_option_change = function(option) {
    set_user_preference(option, document.getElementById(option + 'id').checked);
}