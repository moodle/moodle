
function onEditGroupSettingsSave() {
    valid =  validateEditgroupsettingsForm();
    if (valid) {
        editgroupsettings() ;
        hideAllForms();
        showElement("groupeditform");
    }
    return false;
}

/**
 * Creates a new group for the course. 
 */
function editGroupSettings() {
    // alert("Called editgroupsettings");
    var url = "editgroupsettings-xml.php";
    var requeststring = "groupid="+selectedgroupid
                +"&groupname="+getTextInputValue('groupname')
                +"&description="+getTextInputValue('edit-groupdescription')
                +"&enrolmentkey="+getTextInputValue('enrolmentkey')
                +"&hidepicture="+hidepicture;
    // The picture fields aren't displayed if the right library isn't present
    if (document.getElementById('menuhidepicture')) {
        requeststring = requeststring+"&hidepicture="+getTextInputValue('menuhidepicture');
    }
    sendPostRequest(request, url, requeststring, editGroupSettingsResponse);
 }
 
 /**
  * The callback for the response to the request sent in editgroupsettings(() 
  */
 function editGroupSettingsResponse() {
    if (checkAjaxResponse(request)) {
        // alert("editgroupsettingsResponse");
        // alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        updateSelectedGrouping();
        hideElement("editgroupsettingsform");
    }
 }

function getGroupSettings() {
    // alert("Called getgroupsettings");
    groupid = getSelectedGroup();
    var url = "getgroupsettings-xml.php";
    var requeststring = "groupid="+groupid;
    sendPostRequest(request, url, requeststring, getGroupSettingsResponse);
}

function getGroupSettingsResponse() {
    if (checkAjaxResponse(request)) {
        // alert("getgroupsettingsResponse");
        // alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        xml = request.responseXML;
        setTextInputValue('groupname', getFromXML(xml, 'name'));
        setTextInputValue('edit-groupdescription', getFromXML(xml, 'description'));
        setTextInputValue('enrolmentkey', getFromXML(xml, 'enrolmentkey'));
    }
}

function validateEditgroupsettingsForm() {
    valid = true;
    groupname = getTextInputValue('groupname');

    if (groupname == '') {
        alert('You must enter a name for the new group');
        valid = false;
    } 
    return valid;
}
