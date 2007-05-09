/**
 * This file contains various utility functions, primarily to get and set information on form.html
 * and to take information from XML documents and either return information from them or modifiy the 
 * form appropriately. 
 */


function onGroupingChange() {
    hideAllForms();
    showElement("groupeditform");
    if (!document.getElementById('groupings')) {
        alert('No groupings id element');
    } else {
        groupingselect = document.getElementById('groupings');
        selectedgroupingid = groupingselect.value;
        selectedgroupid = null;
        updateSelectedGrouping();
    }
    return false;
}

function onGroupChange() {
    hideAllForms();
    showElement("groupeditform");
    selectedgroupid = getSelectedGroup();
    updateSelectedGroup();
    return false;
}


function getSelectedGroupingName() {
    if (!document.getElementById('groupings')) {
        alert('No groupings id element');
        value = null;
    } else {
        groupingselect = document.getElementById('groupings');
        value = groupingselect.options[groupingselect.selectedIndex].firstChild.nodeValue;
    }
    return value;
}

function getSelectedGroupName() {
    if (!document.getElementById('groups')) {
        alert('No groups id element');
        value = null;
    } else {
        groupselect = document.getElementById('groups');
        value = groupselect.options[groupselect.selectedIndex].firstChild.nodeValue;
    }
    return value;
}

/*
 * Set the selected grouping on the form to the grouping whose id is selectedgroupingid
 */
function setSelectedGrouping() {
    if (selectedgroupingid == null) {
        selectedgroupingid = getFirstOption("groupings");
    }

    if (selectedgroupingid != null) {
        if (!document.getElementById('groupings')) {
            alert('No groupings id element');
        } else {
            groupingselect = document.getElementById('groupings');
            groupingselect.value = selectedgroupingid
        }
    }
}

/*
 * Get the id of the group that is currently selected
 */
function getSelectedGroup() {
    if (!document.getElementById('groups')) {
        alert('No groups id element');
        value = null;
    } else {
        groupselect = document.getElementById('groups');
        value = groupselect.value;
    }
    return value;
}

/*
 * Set the selected group on the form to the group whose id is selectedgroupid
 */
function setSelectedGroup() {
    if (selectedgroupid == null) {
        selectedgroupid = getFirstOption("groups");
    }

    if (selectedgroupid != null) {
        if (!document.getElementById('groups')) {
            alert('No groups id element');
        } else {
            groupselect = document.getElementById('groups');
            groupselect.value = selectedgroupid;
        }
    }
}


/*
 * Get the selected users to delete 
 */
function getSelectedUsers() {
    return getMultipleSelect("members")
}


 
/***************************************************************
 * Functions that just display information (and don't change the data in the database)
 **********************************************/ 

/**
 *  Updates the list of groupings, setting either a specified grouping as selected or 
 * the first grouping as selected. 
 */
function updateGroupings() {
    alert("updateGroupings called");
    var url = "getgroupings-xml.php";
    requeststring = 'courseid='+courseid+'&'+'sesskey='+sesskey;
    var transaction = YAHOO.util.Connect.asyncRequest('POST', url, 
            updateGroupingsResponseCallback, requeststring); 
    //sendPostRequest(updategroupingsrequest, url, requeststring, updateGroupingsResponse);
}

var updateGroupingsResponseCallback = 
{ 
  success:function(o) {

        // alert("updateGroupingsResponse called");
        var xmlDoc = o.responseXML;
        error = getFromXML(o.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        // alert(o.responseXML);
        var noofoptions = addOptionsFromXML("groupings", xmlDoc);

        // If the selected grouping is not set, set it to the first grouping in the list
        if(selectedgroupingid == null) {
            selectedgroupingid = getFirstOption("groupings");
            selectedgroupid = null;
        }

        // If there are no groupings, make sure the rest of the form is set up appropriately 
        // i.e. there should be any groups or members shown and various buttons should be disabled
        // If there are groupings, update the one that is selected and enable any buttons that
        // might have been disabled.
        if (noofoptions == 0) {
            removeOptions("groups");
            removeOptions("members");
            disableButton("showaddmembersform");
            disableButton("showcreategroupform");
            disableButton("showaddgroupstogroupingform");
        } else {
            updateSelectedGrouping();
            enableButton("showaddmembersform");
            enableButton("showcreategroupform");
            enableButton("showaddgroupstogroupingform");
        }
}, 
  failure:responseFailure, 
};




/** 
 * Updates the list of groups when groupingid is marked as selected
 * groupid can be null or a specified group - this is the group that gets marked as 
 * selectedgroupingid cannot be null. 
 */
function updateSelectedGrouping() {
    //alert("UpdateSelectedGrouping called");
    setSelectedGrouping();
    var url = "getgroupsingrouping-xml.php";
    requeststring = "groupingid="+selectedgroupingid;
    sendPostRequest(updateselectedgroupingsrequest, url, requeststring, updateSelectedGroupingResponse);
}

/**
 * The callback for the response to the request sent in updateSelectedGrouping() 
 */
function updateSelectedGroupingResponse() {
    if (checkAjaxResponse(updateselectedgroupingsrequest)) {
        //alert("updateSelectedGroupingResponse called");
        var xmlDoc = updateselectedgroupingsrequest.responseXML;
        error = getFromXML(updateselectedgroupingsrequest.responseXML, 'error');
        if (error != null) {
            alert(error);
        }
        // alert(updateselectedgroupingsrequest.responseText);
        var noofoptions = addOptionsFromXML("groups", xmlDoc);
        if (selectedgroupid == null) {
            selectedgroupid = getFirstOption("groups");
        }

        if (noofoptions == 0) {
            removeOptions("members");
            disableButton("showaddmembersform");
        } else {
            updateSelectedGroup(selectedgroupid);
            enableButton("showaddmembersform");
        }
    } 
}

/**
 *  Updates the members for the selected group - currently none marked as selected
 */
function updateSelectedGroup() {
    //alert("updateSelectedGroup");
    setSelectedGroup();
    var url = "getmembers-xml.php";
    var requeststring = "groupid="+selectedgroupid;
    sendPostRequest(updateselectedgrouprequest, url, requeststring, updateSelectedGroupResponse);
}

/**
 * The callback for the response to the request sent in updateSelectedGroup() 
 */
function updateSelectedGroupResponse() {
    if (checkAjaxResponse(updateselectedgrouprequest)) {
        var xmlDoc = updateselectedgrouprequest.responseXML;    
        //alert("updateSelectedGroupResponse");
        error = getFromXML(xmlDoc, 'error');
        if (error != null) {
            alert(error);
        }

        //alert(request.responseText);
        var noofoptions = addOptionsFromXML("members", xmlDoc);
    } 
}
