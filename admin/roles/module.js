/* This class filters the rows of a table like the one on the define or
override roles pages. It adds a search box just above the table, and if
content is typed into that box, it hides any rows in the table where the
capability name does not contain that text. */

M.core_role = {};

M.core_role.init_cap_table_filter = function(Y, tableid, strsearch, strclear) {
    var CapTableFilter = function(tableid, strsearch, strclear) {
        this.delayhandle = -1,
        this.searchdelay = 100, // milliseconds

        // Find the form controls.
        this.table = document.getElementById(tableid);

        // Create a div to hold the search UI.
        this.div = document.createElement('div');
        this.div.className = 'capabilitysearchui';
        this.div.style.width = this.table.offsetWidth + 'px';

        // Create the capability search input.
        this.input = document.createElement('input');
        this.input.type = 'text';
        this.input.id = tableid + 'capabilitysearch';

        // Create a label for the search input.
        this.label = document.createElement('label');
        this.label.htmlFor = this.input.id;
        this.label.appendChild(document.createTextNode(strsearch + ' '));

        // Create a clear button to clear the input.
        this.button = document.createElement('input');
        this.button.value = strclear;
        this.button.type = 'button';
        this.button.disabled = true;

        // Tie it all together
        this.div.appendChild(this.label);
        this.div.appendChild(this.input);
        this.div.appendChild(this.button);
        this.table.parentNode.insertBefore(this.div, this.table);
        Y.on('keyup', this.change, this.input, this);
        Y.on('click', this.clear, this.button, this);

        // Horrible hack!
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.disabled = true;
        this.div.appendChild(hidden);
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.disabled = true;
        this.div.appendChild(hidden);
    };

    CapTableFilter.prototype.clear = function () {
        this.input.value = '';
        if (this.delayhandle != -1) {
            clearTimeout(this.delayhandle);
            this.delayhandle = -1;
        }
        this.filter();
    };

    CapTableFilter.prototype.change = function() {
        var self = this;
        var handle = setTimeout(function(){self.filter();}, this.searchdelay);
        if (this.delayhandle != -1) {
            clearTimeout(this.delayhandle);
        }
        this.delayhandle = handle;
    };

    CapTableFilter.prototype.set_visible = function(row, visible) {
        if (visible) {
            Y.one(row).removeClass('hiddenrow');
        } else {
            Y.one(row).addClass('hiddenrow');
        }
    };

    CapTableFilter.prototype.filter = function() {
        var filtertext = this.input.value.toLowerCase();
        this.button.disabled = (filtertext == '');
        var lastheading = null;
        var capssincelastheading = 0;

        Y.all('#'+this.table.id+' tr').each(function(row, index, list) {
            if (row.hasClass('rolecapheading')) {
                if (lastheading) {
                    this.set_visible(lastheading, capssincelastheading > 0);
                }
                lastheading = row;
                capssincelastheading = 0;
            }
            if (row.hasClass('rolecap')) {
                var capname = row.one('.cap-name').get('text') + '|' + row.one('.cap-desc a').get('text').toLowerCase();
                if (capname.indexOf(filtertext) >= 0) {
                    this.set_visible(row, true);
                    capssincelastheading += 1;
                } else {
                    this.set_visible(row, false);
                }
            }
        }, this);

        if (lastheading) {
            this.set_visible(lastheading, capssincelastheading > 0);
        }
    };

    new CapTableFilter(tableid, strsearch, strclear);
};


M.core_role.init_add_assign_page = function(Y) {
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
};
