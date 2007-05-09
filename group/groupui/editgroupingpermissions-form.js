
function onEditGroupingPermissionsSave() {
    hideAllForms();
    showElement("groupeditform");
    editGroupingPermissions() ;
    return false;
}


/**
 * Creates a new grouping for the course. 
 */
function editGroupingPermissions() {
    var url = "editgroupingpermissions-xml.php";
    var requeststring = "groupingid=" + selectedgroupingid
        +"&viewowngroup=" + getCheckBoxValue('edit-viewowngroup')
        +"&viewallgroupsmembers=" + getCheckBoxValue('edit-viewallgroupsmembers')
        +"&viewallgroupsactivities=" + getCheckBoxValue('edit-viewallgroupsactivities')
        +"&teachersgroupmark=" + getCheckBoxValue('edit-teachersgroupmark')
        +"&teachersgroupview=" + getCheckBoxValue('edit-teachersgroupview')
        +"&teachersoverride=" + getCheckBoxValue('edit-teachersoverride');
    sendPostRequest(request, url, requeststring, editGroupingPermissionsResponse);
}
 
 /**
  * The callback for the response to the request sent in editgroupingpermissions() 
  * It sets the new grouping as selected in the form. 
  */
 function editGroupingPermissionsResponse() {
    if (checkAjaxResponse(request)) {
        //alert("editGroupingPermissionsResponse called");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        updateGroupings();
        hideElement("editgroupingpermissionsform");
    }
 }

function getGroupingPermissions() {
    //alert("Called getgroupingpermissions");
    var url = "getgroupingpermissions-xml.php";
    var requeststring = "groupingid="+selectedgroupingid;
    sendPostRequest(request, url, requeststring, getGroupingPermissionsResponse);
}

function getGroupingPermissionsResponse() {
    if (checkAjaxResponse(request)) {
        //alert("getgroupingpermissionsResponse");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        xml = request.responseXML;
        replaceText('editperm-groupingname', getFromXML(xml, 'name'));
        setCheckBoxValue('edit-viewowngroup', boolStringToBool(getFromXML(xml, 'viewowngroup')));
        setCheckBoxValue('edit-viewallgroupsmembers', boolStringToBool(getFromXML(xml, 'viewallgroupsmembers')));
        setCheckBoxValue('edit-viewallgroupsactivities', boolStringToBool(getFromXML(xml, 'viewallgroupsactivities')));
        setCheckBoxValue('edit-teachersgroupmark', boolStringToBool(getFromXML(xml, 'teachersgroupmark')));
        setCheckBoxValue('edit-teachersgroupview', boolStringToBool(getFromXML(xml, 'teachersgroupview')));
        setCheckBoxValue('edit-teachersoverride', boolStringToBool(getFromXML(xml, 'teachersoverride')));
    }
}
