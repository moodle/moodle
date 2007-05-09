

function onAddGroupsToGrouping() {
    hideAllForms();
    showElement("groupeditform");
    addGroupsToGrouping();
    setText('selectedgroupingforaddinggroups', "");
    return false;
}


/*
 * Adds the selected groups to the selected groupings
 */
function addGroupsToGrouping() {
    //alert("Called addGroupsToGrouping");
    selectedgroups = getMultipleSelect("groupsnotingrouping");
    if (selectedgroups != '') {
        var url = "addgroupstogrouping-xml.php";
        var requeststring = "groupingid="+selectedgroupingid
            +"&groups="+selectedgroups;
        sendPostRequest(request, url, requeststring, addGroupsToGroupingResponse);
    }
}


/**
 * The callback for the response to the request sent in addGroupsToGrouping() 
 */
function addGroupsToGroupingResponse() {
    if (checkAjaxResponse(request)) {
        //alert("addGroupsToGrouping called");
        //alert(request.responseText);
        // Need XML sent back with groupingid
        // Really want to set this to be the grouping before
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        updateGroupings();
        hideElement("addgroupstogroupingform");
    }
}


/**
 * Updates the groups not in the selected grouping for the form for adding groups to a grouping
 */
function updateGroupsNotInGrouping() {
    //alert("updateNonMembers called");
    var url="getgroupsnotingrouping-xml.php";
    var requeststring = "groupingid="+selectedgroupingid;
    sendPostRequest(request, url, requeststring, updateGroupsNotInGroupingResponse);
}


/**
 * The callback for the response to the request sent in updateGroupsNotInGrouping() 
 */
function updateGroupsNotInGroupingResponse() {
    if (checkAjaxResponse(request)) {
        //alert("updateGroupsNotInGroupingResponse");
        var xmlDoc = request.responseXML;
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        addOptionsFromXML("groupsnotingrouping", xmlDoc);
    }
}


