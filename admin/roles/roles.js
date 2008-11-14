/* This class filters the rows of a table like the one on the define or 
override roles pages. It adds a search box just above the table, and if
content is typed into that box, it hides any rows in the table where the
capability name does not contain that text. */
cap_table_filter = {
    input: null,
    table: null,
    button: null,
    delayhandle: -1,
    searchdelay: 100, // milliseconds

    init: function(tableid, strsearch, strclear) {
        // Find the form controls.
        cap_table_filter.table = document.getElementById(tableid);

        // Create a div to hold the search UI.
        var div = document.createElement('div');
        div.className = 'capabilitysearchui';
        div.style.width = cap_table_filter.table.offsetWidth + 'px';

        // Create the capability search input.
        var input = document.createElement('input');
        input.type = 'text';
        input.id = tableid + 'capabilitysearch';
        cap_table_filter.input = input;

        // Create a label for the search input.
        var label = document.createElement('label');
        label.htmlFor = input.id;
        label.appendChild(document.createTextNode(strsearch + ' '));

        // Create a clear button to clear the input.
        var button = document.createElement('input');
        button.value = strclear;
        button.type = 'button';
        button.disabled = true;
        cap_table_filter.button = button;

        // Tie it all together
        div.appendChild(label);
        div.appendChild(input);
        div.appendChild(button);
        cap_table_filter.table.parentNode.insertBefore(div, cap_table_filter.table);
        YAHOO.util.Event.addListener(input, 'keyup', cap_table_filter.change);
        YAHOO.util.Event.addListener(button, 'click', cap_table_filter.clear);

        // Horrible hack!
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.disabled = true;
        div.appendChild(hidden);
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.disabled = true;
        div.appendChild(hidden);
    },

    clear: function () {
        cap_table_filter.input.value = '';
        if(cap_table_filter.delayhandle != -1) {
            clearTimeout(cap_table_filter.delayhandle);
            cap_table_filter.delayhandle = -1;
        }
        cap_table_filter.filter();
    },

    change: function() {
        var handle = setTimeout(function(){cap_table_filter.filter();}, cap_table_filter.searchdelay);
        if(cap_table_filter.delayhandle != -1) {
            clearTimeout(cap_table_filter.delayhandle);
        }
        cap_table_filter.delayhandle = handle;
    },

    set_visible: function(row, visible) {
        if (visible) {
            YAHOO.util.Dom.removeClass(row, 'hiddenrow');
        } else {
            YAHOO.util.Dom.addClass(row, 'hiddenrow');
        }
    },

    filter: function() {
        var filtertext = cap_table_filter.input.value;
        cap_table_filter.button.disabled = filtertext == '';
        var rows =  cap_table_filter.table.getElementsByTagName('tr');
        var lastheading = null;
        var capssincelastheading = 0;
        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            if (YAHOO.util.Dom.hasClass(row, 'rolecapheading')) {
                if (lastheading) {
                    cap_table_filter.set_visible(lastheading, capssincelastheading > 0);
                }
                lastheading = row;
                capssincelastheading = 0;
            }
            if (YAHOO.util.Dom.hasClass(row, 'rolecap')) {
                var capcell = YAHOO.util.Dom.getElementsByClassName('name', 'th', row)[0];
                var capname = capcell.innerText || capcell.textContent;
                if (capname.indexOf(filtertext) >= 0) {
                    cap_table_filter.set_visible(row, true);
                    capssincelastheading += 1;
                } else {
                    cap_table_filter.set_visible(row, false);
                }
            }
        }
        if (lastheading) {
            cap_table_filter.set_visible(lastheading, capssincelastheading > 0);
        }
    }
};

function init_add_assign_page() {
    var addselect = user_selector.get('addselect');
    document.getElementById('add').disabled = addselect.is_selection_empty();
    addselect.subscribe('selectionchanged', function(isempty) {
        document.getElementById('add').disabled = isempty;
    });

    var removeselect = user_selector.get('removeselect');
    document.getElementById('remove').disabled = removeselect.is_selection_empty();
    removeselect.subscribe('selectionchanged', function(isempty) {
        document.getElementById('remove').disabled = isempty;
    });
}