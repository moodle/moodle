/**
 * This class filters the rows of a table like the one on the define or
 * override roles pages. It adds a search box just above the table, and if
 * content is typed into that box, it hides any rows in the table where the
 * capability name does not contain that text.
 */

/**
 * Role namespace
 */
M.core_role = {};

/**
 * @param {YUI} Y
 * @param {string} tableid
 * @param {int} contextid
 */
M.core_role.init_cap_table_filter = function(Y, tableid, contextid) {

    var CapTableFilter = function(tableid) {
        this.tableid = tableid;
        this.context = contextid;
        this.initializer();
    };
    CapTableFilter.prototype = {
        tableid : null,     // ID of the cap table
        context : null,    // Context ID associated with what ever we are looking at
        delayhandle : -1,
        searchdelay : 100,  // milliseconds
        table : null,
        div : null,
        input : null,
        label : null,
        button : null,
        /**
         * Initialises the CapTableFilter object.
         * This is called initializer so that a move to convert this to a proper
         * YUI module will be easier.
         */
        initializer : function() {
            // Get any existing filter value
            var filtervalue = this.getFilterCookieValue();

            // Find the form controls.
            this.table = Y.one('#'+this.tableid);

            // Create a div to hold the search UI.
            this.div = Y.Node.create('<div class="capabilitysearchui"></div>').setStyles({
                width : this.table.get('offsetWidth'),
                marginLeft : 'auto',
                marginRight : 'auto'
            });
            // Create the capability search input.
            this.input = Y.Node.create('<input type="text" id="'+this.table.get('id')+'capabilitysearch" value="'+Y.Escape.html(filtervalue)+'" />');
            // Create a label for the search input.
            this.label = Y.Node.create('<label for="'+this.input.get('id')+'">'+M.str.moodle.filter+' </label>');
            // Create a clear button to clear the input.
            this.button = Y.Node.create('<input type="button" value="'+M.str.moodle.clear+'" />').set('disabled', filtervalue=='');

            // Tie it all together
            this.div.append(this.label).append(this.input).append(this.button);

            // Insert it into the div
            this.table.ancestor().insert(this.div, this.table);

            // Wire the events so it actually does something
            this.input.on('keyup', this.change, this);
            this.button.on('click', this.clear, this);

            if (filtervalue != '') {
                this.filter();
            }
        },
        /**
         * Sets a cookie that describes the filter value.
         * The cookie stores the context, and the time it was created and upon
         * retrieval is checked to ensure that the cookie is for the correct
         * context and is no more than an hour old.
         */
        setFilterCookieValue : function(value) {
            var cookie = {
                fltcontext : this.context,
                flttime : new Date().getTime(),
                fltvalue : value
            }
            Y.Cookie.setSubs("captblflt", cookie);
        },
        /**
         * Gets the existing filter value if there is one.
         * The cookie stores the context, and the time it was created and upon
         * retrieval is checked to ensure that the cookie is for the correct
         * context and is no more than an hour old.
         */
        getFilterCookieValue : function() {
            var cookie = Y.Cookie.getSubs('captblflt');
            if (cookie!=null && cookie.fltcontext && cookie.fltcontext == this.context && parseInt(cookie.flttime) > new Date().getTime()-(60*60*1000)) {
                return cookie.fltvalue;
            }
            return '';
        },
        /**
         * Clears the filter value.
         */
        clear : function() {
            this.input.set('value', '');
            if (this.delayhandle != -1) {
                clearTimeout(this.delayhandle);
                this.delayhandle = -1;
            }
            this.filter();
        },
        /**
         * Event callback for when the filter value changes
         */
        change : function() {
            var self = this;
            var handle = setTimeout(function(){self.filter();}, this.searchdelay);
            if (this.delayhandle != -1) {
                clearTimeout(this.delayhandle);
            }
            this.delayhandle = handle;
        },
        /**
         * Marks a row as visible or hidden
         */
        setVisible : function(row, visible) {
            if (visible) {
                row.removeClass('hiddenrow');
            } else {
                row.addClass('hiddenrow');
            }
        },
        /**
         * Filters the capability table
         */
        filter : function() {
            var filtertext = this.input.get('value').toLowerCase(),
                lastheading = null;

            this.setFilterCookieValue(filtertext);

            this.button.set('disabled', (filtertext == ''));

            this.table.all('tr').each(function(row){
                if (row.hasClass('rolecapheading')) {
                    this.setVisible(row, false);
                    lastheading = row;
                }
                if (row.hasClass('rolecap')) {
                    var capname = row.one('.cap-name').get('text') + '|' + row.one('.cap-desc a').get('text').toLowerCase();
                    if (capname.indexOf(filtertext) >= 0) {
                        this.setVisible(row, true);
                        if (lastheading) {
                            this.setVisible(lastheading, true);
                            lastheading = null;
                        }
                    } else {
                        this.setVisible(row, false);
                    }
                }
            }, this);
        }
    }

    new CapTableFilter(tableid);
};

M.core_role.init_add_assign_page = function(Y) {
    var add = Y.one('#add');
    var addselect = M.core_user.get_user_selector('addselect');
    add.set('disabled', addselect.is_selection_empty());
    addselect.on('user_selector:selectionchanged', function(isempty) {
        add.set('disabled', isempty);
    });

    var remove = Y.one('#remove');
    var removeselect = M.core_user.get_user_selector('removeselect');
    remove.set('disabled', removeselect.is_selection_empty());
    removeselect.on('user_selector:selectionchanged', function(isempty) {
        remove.set('disabled', isempty);
    });
};
