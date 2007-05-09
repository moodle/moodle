

function onCreateGrouping() {
    valid =  validateCreateGroupingForm();
    if (valid) {
        hideAllForms();
        showElement("groupeditform");
        createGrouping();
    }

    return false;
}

/**
 * Creates a new grouping for the course. 
 */
function createGrouping() {
    // alert("Called createGrouping");
    var url = "creategrouping-xml.php";
    var requeststring = "groupingname="+getTextInputValue('newgroupingname')
        +"&description="+getTextInputValue('edit-newgroupingdescription');
    sendPostRequest(request, url, requeststring, createGroupingResponse);
 }
 
 /**
  * The callback for the response to the request sent in createGrouping() 
  * It sets the new grouping as selected in the form. 
  */
 function createGroupingResponse() {
    if (checkAjaxResponse(request)) {
    // alert("createGroupingResponse");
    // alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        selectedgroupingid = getFromXML(request.responseXML, 'groupingid');
        selectedgroupid = null;
        updateGroupings();
        hideElement("creategroupingform");
    }
 }

function validateCreateGroupingForm() {
    valid = true;
    groupingname = getTextInputValue('newgroupingname');

    if (groupingname == '') {
        alert('You must enter a name for the new grouping');
        valid = false;
    } 
    return valid;
}



