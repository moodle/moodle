function onCreateAutomaticGrouping() {
    valid =  validateAutomaticGroupingForm();
    if (valid) {
        hideAllForms();
        showElement("groupeditform");
        createAutomaticGrouping();
    }

    return false;
}


/**
 * Adds an automatically generated grouping with the details as specified in the form
 */
function createAutomaticGrouping() {
    //alert("Called createAutomaticGrouping");
    var url = "createautomaticgrouping-xml.php";
    var requeststring = "noofstudents="+getTextInputValue('noofstudents')
        +"&noofgroups="+getTextInputValue('noofgroups')
        +"&distribevenly="+getCheckBoxValue('distribevenly')
        +"&alphabetical="+getCheckBoxValue('alphabetical')
        +"&generationtype="+getRadioValue(document.automaticgroupingform.generationtype)
        +"&name="+getTextInputValue('automaticgroupingname')
        +"&description="+getTextInputValue('edit-automaticgroupingdescription')
        +"&prefix="+getTextInputValue('groupprefix')
        +"&defaultgroupdescription="+getTextInputValue('edit-defaultgroupdescription');

    // alert(requeststring);
    sendPostRequest(request, url, requeststring, createAutomaticGroupingResponse);
}
 


 /**
  * The callback for the response to the request sent in createAutomaticGrouping() 
  * It sets the new grouping to be selected in the form. 
  */
 function createAutomaticGroupingResponse() {
    if (checkAjaxResponse(request)) {
        //alert("createAutomaticGroupingResponse");
        //alert(request.responseText);
        error = getFromXML(request.responseXML, 'error');
        if (error != null) {
            alert(error);
        } 
        selectedgroupingid = getFromXML(request.responseXML, 'groupingid');
        selectedgroupid = null;
        updateGroupings();
        hideElement("createautomaticgroupingform");
    }
 }

function validateAutomaticGroupingForm() {
    valid = true;
    generationtype = getRadioValue(document.automaticgroupingform.generationtype);
    noofstudents = getTextInputValue('noofstudents');
    noofgroups = getTextInputValue('noofgroups');
    groupingname = getTextInputValue('automaticgroupingname');

    if (generationtype == 'nostudents') {
        if (!isPositiveInt(noofstudents)) {
            alert('The number of students is not valid.');
            valid = false;
        } 
    } else {
        if (!isPositiveInt(noofgroups)) {
            alert('The number of groups is not valid.');
            valid = false;
        } 
    }

    if (groupingname == '') {
        alert('You must enter a name for the new grouping');
        valid = false;
    } 

    return valid;
}
