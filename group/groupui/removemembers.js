


function onRemoveMembers() {
    hideAllForms();
    showElement("groupeditform");
    removeMembers();
    return false;
}



/**
 * Removes the selected members from the selected group
 */
function removeMembers() {
    //alert("Called removeMembers");
    users = getSelectedUsers();
    var url = "removemembers-xml.php";
    var requeststring = "groupid="+selectedgroupid+"&users="+users;
    sendPostRequest(request, url, requeststring, removeMembersResponse);
}

/**
 * The callback for the response to the request sent in removeMembers() 
 */
function removeMembersResponse() {
    if (checkAjaxResponse(request)) {
        //alert("removeMembersResponse called");
        //alert(request.responseText);
        updateSelectedGroup();
    }
}
