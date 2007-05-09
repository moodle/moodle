function onDeleteGroup() {
    hideAllForms()
    showElement("groupeditform");
    deleteGroup();
    return false;
}


/**
 * Deletes the selected group
 */
function deleteGroup() {
    //alert("Called deleteGroup");
    var url = "deletegroup-xml.php";
    var requeststring = "groupid="+selectedgroupid;
    sendPostRequest(request, url, requeststring, deleteGroupResponse);
}
 
/**
 * The callback for the response to the request sent in updateSelectedGrouping() 
 */ 
function deleteGroupResponse() {
    if (checkAjaxResponse(request)) {
        //alert("deleteGroupResponse called");
        // alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        // Need XML sent back with groupingid
        // Really want to set this to be the grouping before
        selectedgroupid = null;
        updateGroupings();
    }
}
