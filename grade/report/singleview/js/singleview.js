M.gradereport_singleview = {};

M.gradereport_singleview.init = function(Y) {
    var getColumnIndex = function(cell) {
        var rowNode = cell.ancestor('tr');
        if (!rowNode || !cell) {
            return;
        }
        var cells = rowNode.all('td, th');
        return cells.indexOf(cell);
    },
    getNextCell = function(cell) {
        var n = cell || document.activeElement,
            next = n.next('td.cell, th.cell');
        if (!next) {
            return null;
        }
        // Continue on until we find a navigable cell
        if (!next || !Y.one(next).one('input:not([type="hidden"]):not([disabled="DISABLED"]), select, a')) {
            return getNextCell(next);
        }
        return next;
    },
    getPrevCell = function(cell) {
        var n = cell || document.activeElement,
            next = n.previous('td.cell, th.cell');
        if (!next) {
            return null;
        }
        // Continue on until we find a navigable cell
        if (!Y.one(next).one('input:not([type="hidden"]):not([disabled="DISABLED"]), select, a')) {
            return getPrevCell(next);
        }
        return next;
    },
    getAboveCell = function(cell) {
        var n = cell || document.activeElement,
            tr = n.ancestor('tr').previous('tr'),
            columnIndex = getColumnIndex(n),
            next = null;
        if (tr) {
            next = tr.all('td, th').item(columnIndex);
        } else {
            return null;
        }
        // Continue on until we find a navigable cell
        if (!Y.one(next).one('input:not([type="hidden"]):not([disabled="DISABLED"]), select, a')) {
            return getAboveCell(next);
        }
        return next;
    },
    getBelowCell = function(cell) {
        var n = cell || document.activeElement,
            tr = n.ancestor('tr').next('tr'),
            columnIndex = getColumnIndex(n),
            next = null;
        if (tr) {
            next = tr.all('td, th').item(columnIndex);
        } else {
            return null;
        }
        // Continue on until we find a navigable cell
        if (!Y.one(next).one('input:not([type="hidden"]):not([disabled="DISABLED"]), select, a')) {
            return getBelowCell(next);
        }
        return next;
    };

    // Add ctrl+arrow controls for navigation
    Y.one(Y.config.doc.body).delegate('key', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var next = null;
        switch (e.keyCode) {
            case 37:
                next = getPrevCell(this.ancestor('td, th'));
                break;
            case 38:
                next = getAboveCell(this.ancestor('td, th'));
                break;
            case 39:
                next = getNextCell(this.ancestor('td, th'));
                break;
            case 40:
                next = getBelowCell(this.ancestor('td, th'));
                break;
        }
        if (next) {
            Y.one(next).one('input:not([type="hidden"]):not([disabled="DISABLED"]), select, a').focus();
        }
        return;
    }, 'down:37,38,39,40+ctrl', 'table input, table select, table a');

    // Make toggle links
    Y.all('.include').each(function(link) {
        var type = link.getAttribute('class').split(" ")[2];

        var toggle = function(checked) {
            return function(input) {
                input.getDOMNode().checked = checked;
                Y.Event.simulate(input.getDOMNode(), 'change');
            };
        };

        link.on('click', function(e) {
            e.preventDefault();
            Y.all('input[name^=' + type + ']').each(toggle(link.hasClass('all')));
        });
    });

    // Override Toggle
    Y.all('input[name^=override_]').each(function(input) {
        input.on('change', function() {
            var checked = input.getDOMNode().checked;
            var names = input.getAttribute('name').split("_");

            var itemid = names[1];
            var userid = names[2];

            var interest = '_' + itemid + '_' + userid;

            Y.all('input[name$=' + interest + ']').filter('input[type=text]').each(function(text) {
                text.getDOMNode().disabled = !checked;
            });
            // deal with scales that are not text... UCSB
            Y.all('select[name$=' + interest + ']').each(function(select) {
                select.getDOMNode().disabled = !checked;
            });
        });
    });
};
