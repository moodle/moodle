function onAddMembers() {
    hideAllForms();
    showElement("groupeditform");
    addMembers();
    return false;
}

function onShowAll() {
    updateNonMembers();
    return false;
}



/*
 * Adds the selected users to the selected group
 */
function addMembers() {
    //alert("Called addMembers");
    users = getMultipleSelect("nonmembers");
    if (users != '') {
        var url = "addmembers-xml.php";
        var requeststring = "groupid="+selectedgroupid+"&users="+users;
        sendPostRequest(request, url, requeststring, addMembersResponse);
    }
}

/**
 * The callback for the response to the request sent in addMembers() 
 */
function addMembersResponse() {
    if (checkAjaxResponse(request)) {
        //alert("addMembersResponse called");
        //alert(request.responseText);
        // Need XML sent back with groupingid
        // Really want to set this to be the grouping before
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        updateSelectedGrouping();
        hideElement("addmembersform");
    }
}


/**
 * Updates the list of non members of a group in the form for adding members to a group
 */
function updateNonMembers() {
    //alert("updateNonMembers called");
    var url="getnonmembers-xml.php";
    // showall indicates if we should show users already in groups in the grouping
    // we have to turn it into a variable that we can put in a post
    var showall = getCheckBoxValue('showall');;
    var requeststring = "groupid="+selectedgroupid
        +"&groupingid="+selectedgroupingid
        +"&showall="+showall;

    sendPostRequest(request, url, requeststring, updateNonMembersResponse);
}

/**
 * The callback for the response to the request sent in updateNonMembers() 
 */
function updateNonMembersResponse() {
    if (checkAjaxResponse(request)) {
        //alert("updateNonMembersResponse");
        var xmlDoc = request.responseXML;
        // alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        addOptionsFromXML("nonmembers", xmlDoc);
    }
}


