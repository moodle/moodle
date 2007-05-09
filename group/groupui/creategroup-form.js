
function onCreateGroup() {
    valid =  validateCreateGroupForm();

    if (valid) {
        hideAllForms();
        showElement("groupeditform");
        createGroup();
        replaceText('selectedgroupingforcreatinggroup', "");
    }

    return false;
}

/*
 * Adds a group with the name specified in the form to the selected grouping. 
 */
function createGroup() {
    //alert("Called createGroup");
    var url = "creategroup-xml.php";
    var requeststring = "groupname="+getTextInputValue('newgroupname')
        +"&groupingid="+selectedgroupingid
        +"&description="+getTextInputValue('edit-newgroupdescription')
        +"&enrolmentkey="+getTextInputValue('newgroupenrolmentkey');
    // The picture fields aren't displayed if the right library isn't present
    if (document.getElementById('menunewgrouphidepicture')) {
        requeststring = requeststring+"&hidepicture="+getTextInputValue('menunewgrouphidepicture');
    }
    sendPostRequest(request, url, requeststring, createGroupResponse);
}

/**
 * The callback for the response to the request sent in  createGroup() 
 * The function sets the new group as selected in the form. 
 */
function createGroupResponse() {
    if (checkAjaxResponse(request)) {
        //alert("createGroupResponse called");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        selectedgroupid = getFromXML(request.responseXML, 'groupid');
        updateGroupings();
        hideElement("creategroupform");
    }
}


function validateCreateGroupForm() {
    valid = true;
    groupname = getTextInputValue('newgroupname');

    if (groupname == '') {
        alert('You must enter a name for the new group');
        valid = false;
    } 
    return valid;
}

