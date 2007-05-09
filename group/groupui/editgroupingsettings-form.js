
function onEditGroupingSettingsSave() {
    valid = validateEditGroupingSettingsForm();
    if (valid) {
        hideAllForms();
        showElement("groupeditform");
        editGroupingSettings() ;
        return false;
    }
}


/**
 * Creates a new grouping for the course. 
 */
function editGroupingSettings() {
    var url = "editgroupingsettings-xml.php";
    var requeststring = "groupingid="+selectedgroupingid
        +"&groupingname="+getTextInputValue('edit-groupingname')
        +"&description="+getTextInputValue('edit-edit-groupingdescription');
    sendPostRequest(request, url, requeststring, editGroupingSettingsResponse);
}
 
 /**
  * The callback for the response to the request sent in editgroupingsettings() 
  * It sets the new grouping as selected in the form. 
  */
 function editGroupingSettingsResponse() {
    if (checkAjaxResponse(request)) {
        //alert("editGroupingSettingsResponse called");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        updateGroupings();
        hideElement("editgroupingsettingsform");
    }
 }

function getGroupingSettings() {
    //alert("Called getgroupingsettings");
    var url = "getgroupingsettings-xml.php";
    var requeststring = "groupingid="+selectedgroupingid;
    sendPostRequest(request, url, requeststring, getGroupingSettingsResponse);
}

function getGroupingSettingsResponse() {
    if (checkAjaxResponse(request)) {
        //alert("getgroupingsettingsResponse");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        xml = request.responseXML;
        setTextInputValue('edit-groupingname', getFromXML(xml, 'name'));
        setTextInputValue('edit-edit-groupingdescription', getFromXML(xml, 'description'));
    }
}


function validateEditGroupingSettingsForm() {
    valid = true;
    groupingname = getTextInputValue('edit-groupingname');

    if (groupingname == '') {
        alert('You must enter a name for the new grouping');
        valid = false;
    } 
    return valid;
}
