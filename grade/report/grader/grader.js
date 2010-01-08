YAHOO.namespace("graderreport");

YAHOO.graderreport.init = function() {
    // attach event listener to the table for mouseover and mouseout
    var table = document.getElementById('user-grades');
    YAHOO.util.Event.on(table, 'mouseover', YAHOO.graderreport.mouseoverHandler);
    YAHOO.util.Event.on(table, 'mouseout', YAHOO.graderreport.mouseoutHandler);

    // Make single panel that can be dynamically re-rendered wit the right data.
    YAHOO.graderreport.panelEl = new YAHOO.widget.Panel("tooltipPanel", {

        draggable: false,
        visible: false,
        close: false,
        preventcontextoverlap: true,
        underlay: 'none'
    });

    YAHOO.graderreport.panelEl.render(table);

};

YAHOO.graderreport.mouseoverHandler = function (e) {

    var tempNode = '';
    var searchString = '';
    var tooltipNode = '';

    // get the element that we just moved the mouse over
    var elTarget = YAHOO.util.Event.getTarget(e);

    // if it was part of the yui panel, we don't want to redraw yet
    searchString = /fullname|itemname|feedback/;
    if (elTarget.className.search(searchString) > -1) {
        return false;
    }

    // move up until we are in the actual cell, not any other child div or span
    while (elTarget.id != 'user-grades') {
        if(elTarget.nodeName.toUpperCase() == "TD") {
            break;
        } else {
            elTarget = elTarget.parentNode;
        }
    }

    // only make a tooltip for cells with grades
    if (elTarget.className.search('grade cell') > -1) {

        // each time we go over a new cell, we need to put it's tooltip into a div to stop it from
        // popping up on top of the panel.

        // don't do anything if we have already made the tooltip div
        var makeTooltip = true;
        for (var k=0; k < elTarget.childNodes.length; k++) {
            if (typeof(elTarget.childNodes[k].className) != 'undefined') {
                if (elTarget.childNodes[k].className.search('tooltipDiv') > -1) {
                    makeTooltip =  false;
                }
            }
        }

        // if need to, make the tooltip div and append it to the cell
        if (makeTooltip) {

            tempNode = document.createElement("div");
            tempNode.className = "tooltipDiv";
            tempNode.innerHTML = elTarget.title;
            elTarget.appendChild(tempNode);
            elTarget.title = null;
        }

        // Get the tooltip div
        elChildren = elTarget.childNodes;
        for (var m=0; m < elChildren.length; m++) {
            if (typeof(elChildren[m].className) != 'undefined') {
                if (elChildren[m].className.search('tooltipDiv') > -1) {
                    tooltipNode = elChildren[m];
                    break;
                }
            }
        }
        //build and show the tooltip (if not empty)
        if(tooltipNode.innerHTML)
        {
            YAHOO.graderreport.panelEl.setBody(tooltipNode.innerHTML);
            YAHOO.graderreport.panelEl.render(elTarget);
            YAHOO.graderreport.panelEl.show();
        }
    }
};

// only hide the overlay if the mouse has not moved over it
YAHOO.graderreport.mouseoutHandler = function (e) {

    var classVar = '';
    var searchString = '';
    var newTargetClass = '';
    var newTarget = YAHOO.util.Event.getRelatedTarget(e);

    // deals with an error if the mouseout event is over the lower scrollbar
    try {
        classVar = newTarget.className;
    } catch (err) {
        YAHOO.graderreport.panelEl.hide()
        return false;
    }

    // if we are over any part of the panel, do not hide
    // do this by walking up the DOM till we reach table level, looking for panel tag
    while ((typeof(newTarget.id) == 'undefined') || (newTarget.id != 'user-grades')) {

        try {
            newTargetClass = newTarget.className;
        } catch (err) {
            // we've gone over the scrollbar again
            YAHOO.graderreport.panelEl.hide()
            return false;
        }
        searchString = /yui-panel|grade cell/;
        if (newTargetClass.search(searchString) > -1) {
            // we're in the panel so don't hide it
            return false;
        }

        if (newTarget.nodeName.toUpperCase() == "HTML") {
            // we missed the user-grades table altogether by moving down off screen to read a long one
            YAHOO.graderreport.panelEl.hide()
            break;
        }

        newTarget = newTarget.parentNode;
    }

    // no panel so far and we went up to the
    YAHOO.graderreport.panelEl.hide()

};


YAHOO.util.Event.onDOMReady(YAHOO.graderreport.init);
