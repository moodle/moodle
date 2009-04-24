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

    toggleCategorySelector();

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

/**
 * Check if any of the grade item checkboxes is ticked. If yes, enable the dropdown. Otherwise, disable it
 */
function toggleCategorySelector() {
    var itemboxes = YAHOO.util.Dom.getElementsByClassName('itemselect');
    for (var i = 0; i < itemboxes.length; i++) {
        if (itemboxes[i].checked) {
            document.getElementById('menumoveafter').disabled = false;
            return true;
        }
    }
    document.getElementById('menumoveafter').disabled = 'disabled';

}
