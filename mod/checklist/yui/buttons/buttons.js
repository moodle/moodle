/*global M*/
YUI.add('moodle-mod_checklist-buttons', function (Y) {
    "use strict";
    var toggle_values, col_clicked, row_clicked, getNextToggle;

    toggle_values = [1, 2, 0];

    getNextToggle = function (btn) {
        var pos;
        pos = btn.getData('toggleposition');
        if (pos === undefined) {
            pos = 0;
        } else {
            pos = (pos + 1) % toggle_values.length;
        }
        btn.setData('toggleposition', pos);
        return toggle_values[pos];
    };

    col_clicked = function (e) {
        var toggleValue, id, regex;
        // use the 1st value in the toggle array to toggle through states
        toggleValue = getNextToggle(e.currentTarget);
        // select the added hidden value denoting the row in question
        id = e.currentTarget.getAttribute("id");
        regex = new RegExp('items_\\d+\\[' + id + '\\]');
        // loop through all the select elements in the column
        Y.all('select').each(function (sel) {
            if (sel.get('disabled')) {
                return;
            }
            if (!sel.get('name').match(regex)) {
                return; // Only if the select name matches the ID of the column.
            }
            // loop through all the option tags in the select dropdown
            sel.all('option').each(function (opt, n) {
                if (parseInt(opt.getAttribute("value"), 10) === toggleValue) {
                    sel.set('selectedIndex', n);
                }
            });
        });
    };

    row_clicked = function (e) {
        var toggleValue, id, regex;
        // use the 1st value in the toggle array to toggle through states
        toggleValue = getNextToggle(e.currentTarget);
        // select the added hidden value denoting the row in question
        id = e.currentTarget.getAttribute("id");
        regex = new RegExp('items_' + id + '\\[\\d+\\]');
        // loop through all the select elements in the row
        Y.all('select').each(function (sel) {
            if (sel.get('disabled')) {
                return;
            }
            if (!sel.get('name').match(regex)) {
                return; // Only if the select name matches the ID of the row.
            }
            // loop through all the option tags in the select dropdown
            sel.all('option').each(function (opt, n) {
                if (parseInt(opt.getAttribute("value"), 10) === toggleValue) {
                    sel.set('selectedIndex', n);
                }
            });
        });
    };

    // simple click handlers for every added button

    M.mod_checklist = M.mod_checklist || {};
    M.mod_checklist.buttons = {
        init: function () {
            Y.on("click", col_clicked, ".make_col_c");
            Y.on("click", row_clicked, ".make_c");
        }
    };
});
