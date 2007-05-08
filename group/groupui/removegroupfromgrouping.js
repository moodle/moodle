
function onRemoveGroup() {
    hideAllForms();
    showElement("groupeditform");
    removeGroupFromGrouping();
    return false;
}

/**
 * Removes the selected group from the selected grouping, does not delete the group (so it can e.g. be added to
 * another grouping
 */
function removeGroupFromGrouping() {
    //alert("Called removeGroupFromGrouping");
    var url = "removegroupfromgrouping-xml.php";
    var requeststring = "groupid="+selectedgroupid+"&groupingid="+selectedgroupingid;
    sendPostRequest(request, url, requeststring, removeGroupFromGroupingResponse);
}

/**
 * The callback for the response to the request sent in removeGroupFromGrouping() 
 */ 
function removeGroupFromGroupingResponse() {
    if (checkAjaxResponse(request)) {
        //alert("removeGroupFromGroupingResponse called");
        var xmlDoc= request.responseXML;
        // Need XML sent back with groupingid
        // Really want to set this to be the grouping before
        selectedgroupid = null;
        updateGroupings();
    }
}


