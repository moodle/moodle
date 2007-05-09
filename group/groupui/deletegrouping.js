
function onDeleteGrouping() {
    hideAllForms()
    showElement("groupeditform");
    deleteGrouping();
    return false;
}


/*
 * Deletes the selected grouping
 */
function deleteGrouping() {
    //alert("Called deleteGrouping");
    var url = "deletegrouping-xml.php";
    var requeststring = "groupingid="+selectedgroupingid;
    confirm('Are you sure you want to delete this grouping and the groups that it contains?');
    sendPostRequest(request, url, requeststring, deleteGroupingResponse);
 }
 
 /**
 * The callback for the response to the request sent in deleteGrouping() 
 */
 function deleteGroupingResponse() {
     if (checkAjaxResponse(request)) {
         //alert("deleteGroupingResponse called");
         //alert(request.responseText);
         error = getFromXML(request.responseXML, 'error');
         if (error != null) {
             alert(error);
         }
         selectedgroupingid = null;
         selectedgroupid = null;
         updateGroupings();
    }
}

