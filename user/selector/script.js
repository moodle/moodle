// JavaScript for the user selectors.
// This is somewhat inspired by the autocomplete component in YUI.
// license: http://www.gnu.org/copyleft/gpl.html GNU Public License
// package: userselector

/**
 * 
 * @constructor
 */
function user_selector(name, hash, sesskey, extrafields, strprevselected) {
    this.name = name;
    this.extrafields = extrafields;
    this.strprevselected = strprevselected;

    // Set up the data source.
    this.datasource = new YAHOO.util.XHRDataSource(moodle_cfg.wwwroot +
            '/user/selector/search.php?selectorid=' + hash + '&sesskey=' + sesskey + '&search='); 
    this.datasource.connXhrMode = 'cancelStaleRequests';
    this.datasource.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
    this.datasource.responseSchema = {resultsList: 'results'};

    // Find some key HTML elements.
    this.searchfield = document.getElementById(this.name + '_searchtext');
    this.listbox = document.getElementById(this.name);

    // Hide the search button and replace it with a label.
    var searchbutton = document.getElementById(this.name + '_searchbutton');
    var label = document.createElement('label');
    label.for = this.name + '_searchtext';
    label.appendChild(document.createTextNode(searchbutton.value));
    this.searchfield.parentNode.insertBefore(label, this.searchfield);
    searchbutton.parentNode.removeChild(searchbutton);

    // Hook up the event handler.
    var oself = this;
    YAHOO.util.Event.addListener(this.searchfield, "keyup", function(e) { oself.handle_keyup() });
    this.lastsearch = this.get_search_text();
}

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
 * The datasource used to fetch lists of users from Moodle.
 * @property datasource
 * @type YAHOO.widget.DataSource
 */
user_selector.prototype.datasource = null;

/**
 * Number of seconds to delay before submitting a query request. If a query
 * request is received before a previous one has completed its delay, the
 * previous request is cancelled and the new request is set to the delay.
 *
 * @property querydelay
 * @type Number
 * @default 0.2
 */
user_selector.prototype.querydelay = 0.2;

/**
 * The input element that contains the search term.
 * 
 * @property searchfield
 * @type HTMLInputElement
 */
user_selector.prototype.searchfield = 0.2;

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
 * The last string that we searched for.
 * 
 * @property lastsearch
 * @type String
 */
user_selector.prototype.lastsearch = null;

/**
 * Name of the previously selected users group.
 *
 * @property strprevselected
 * @type String
 */
user_selector.prototype.strprevselected = '';

/**
 * Used to track whether there is only one optoin matchin the search results, if
 * so, it is automatically selected.
 *
 * @property strprevselected
 * @type Object
 **/
user_selector.prototype.onlyoption = null;

/**
 * Key up hander for the search text box. Trigger an ajax search after a delay.
 */
user_selector.prototype.handle_keyup = function() {
    if (this.timeoutid) {
        clearTimeout(this.timeoutid);
        this.timeoutid = null;
    }
    var oself = this;
    this.timeoutid = setTimeout(function() { oself.send_query() }, this.querydelay * 1000);
}

/**
 * @return String the value to search for, with leading and trailing whitespace trimmed.
 */
user_selector.prototype.get_search_text = function() {
    return this.searchfield.value.replace(/^ +| +$/, '');
}

/**
 * Fires off the ajax search request.
 */
user_selector.prototype.send_query = function() {
    var value = this.get_search_text();
    if (this.lastsearch == value) {
        return;
    }
    this.datasource.sendRequest(this.searchfield.value, {
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
    while (groups.length > 0) {
        var optgroup = groups[0]; // Remeber that groups is a live array as we remove optgroups from the select, it updates.
        var options = optgroup.getElementsByTagName('option');
        while (options.length > 0) {
            var option = options[0];
            if (option.selected) {
                var optiontext = option.innerText || option.textContent
                this.selected[option.value] = { id: option.value, formatted: optiontext };
            }
            optgroup.removeChild(option);
        }
        this.listbox.removeChild(optgroup);
    }

    var results = data.results[0];

    // Output each optgroup.
    this.onlyoption = null;
    for (groupname in results) {
        this.output_group(groupname, results[groupname], false);
    }

    // If there was only one option matching the search results, select it.
    if (this.onlyoption) {
        this.onlyoption.selected = true;
    }
    this.onlyoption = null;

    // If there were previously selected users who do not match the search, show them too.
    var areprevselected = false;
    for (user in this.selected) {
        areprevselected = true;
        break;
    }
    if (areprevselected) {
        this.output_group(this.strprevselected, this.selected, true);
    }
    this.selected = null;

}

user_selector.prototype.output_group = function(groupname, users, select) {
    var optgroup = document.createElement('optgroup');
    optgroup.label = groupname;
    var count = 0;
    for (var userid in users) {
        var user = users[userid];
        var option = document.createElement('option');
        option.value = user.id;
        option.appendChild(document.createTextNode(this.output_user(user)));
        if (select || this.selected[user.id]) {
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
    if (count == 0) {
        var option = document.createElement('option');
        option.disabled = 'disabled';
        option.appendChild(document.createTextNode('\u00A0'));
        optgroup.appendchild(option);
    }
    optgroup.label += ' (' + count + ')';
    this.listbox.appendChild(optgroup);
}

/**
 * Convert a user object to a string suitable for displaying as an option in the list box.
 *
 * @param Object user the user to display.
 * @return string a string representation of the user.
 */
user_selector.prototype.output_user = function(user) {
    if (user.formatted) {
        return user.formatted;
    }
    var output = user.fullname;
    for (var i = 0; i < this.extrafields.length; i++) {
        output += ', ' + user[this.extrafields[i]];
    }
    return output;
}