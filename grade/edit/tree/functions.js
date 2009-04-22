/**
 * Toggles the selection checkboxes of all grade items children of the given eid (a category id)
 */
function togglecheckboxes(eid, value) {
    var rows = YAHOO.util.Dom.getElementsByClassName(eid);

    for (var i = 0; i < rows.length; i++) {
        var element = new YAHOO.util.Element(rows[i]);
        var checkboxes = element.getElementsByClassName('itemselect');
        if (checkboxes[0]) {
            checkboxes[0].checked=value;
        }
    }

}

function toggle_advanced_columns() {
    var advEls = YAHOO.util.Dom.getElementsByClassName("advanced");
    var shownAdvEls = YAHOO.util.Dom.getElementsByClassName("advancedshown");

    for (var i = 0; i < advEls.length; i++) {
        YAHOO.util.Dom.replaceClass(advEls[i], "advanced", "advancedshown");
    }

    for (var i = 0; i < shownAdvEls.length; i++) {
        YAHOO.util.Dom.replaceClass(shownAdvEls[i], "advancedshown", "advanced");
    }
}
