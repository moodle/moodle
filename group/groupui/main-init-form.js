/*
 * This file contains all the functions called when the pages loads and also all the functions that are called
 * on events such as clicking buttons in the forms for the form.html page. 
 * 
 * This script requires functions from ajax.js and form-access.js
 * 
 * This code also assumes you have a basic understanding of how Ajax works - if
 * you don't, it won't make much sense! 
*/



// Create XMLHttpRequest objects to use 
var request = createRequest();
var updategroupingsrequest = createRequest();
var updateselectedgroupingsrequest = createRequest();
var updateselectedgrouprequest = createRequest();

// The selectedgroupingid should always be set to the current selected groupingid and the
// selectedgroupid should always be set to the current selected groupid. We initialise them to 
// be null at the start, but they'll get set when the page loads. 
var selectedgroupingid = null;
var selectedgroupid = null;

// When the page has loaded called the initPage function
window.onload = initPage;

/**
 *  The initPage function updates the groupings, groups and members in all the selects appropriately 
 *and adds the right javascript events to all the buttons etc. 
 */
function initPage() {
    // Check that we're using a recent enough version of javascript
    if (!document.getElementById) {
        return false;
    }
    updateGroupings();

    addEvent('groupings', 'change', onGroupingChange);
    addEvent('groups', 'change', onGroupChange);
    addEvent('deletegrouping', 'click', onDeleteGrouping);
    addEvent('deletegroup', 'click', onDeleteGroup);
    addEvent('removegroup', 'click', onRemoveGroup);
    addEvent('removemembers', 'click', onRemoveMembers);
    addEvent('showaddmembersform', 'click', onShowAddMembersForm);
    addEvent('showaddgroupstogroupingform', 'click', onShowAddGroupsToGroupingForm);
    addEvent('showcreategroupingform', 'click', onShowCreateGroupingForm);
    addEvent('showcreategroupform', 'click', onShowCreateGroupForm);
    addEvent('showeditgroupsettingsform', 'click', onShowEditGroupSettingsForm);
    addEvent('showeditgroupingsettingsform', 'click', onShowEditGroupingSettingsForm);
    addEvent('showeditgroupingpermissionsform', 'click', onShowEditGroupingPermissionsForm);
    addEvent('showcreateautomaticgroupingform', 'click', onShowAutomaticGroupingForm);
    addEvent('printerfriendly', 'click', onPrinterFriendly);
    addEvent('createautomaticgrouping', 'click', onCreateAutomaticGrouping);
    addEvent('cancelcreateautomaticgrouping', 'click', onCancel);
    addEvent('addgroupstogrouping', 'click', onAddGroupsToGrouping);
    addEvent('canceladdgroupstogrouping', 'click', onCancel);
    addEvent('creategroup', 'click', onCreateGroup);
    addEvent('cancelcreategroup', 'click', onCancel);
    addEvent('creategrouping', 'click', onCreateGrouping);
    addEvent('cancelcreategrouping', 'click', onCancel);
    addEvent('addmembers', 'click', onAddMembers);
    addEvent('canceladdmembers', 'click', onCancel);
    addEvent('showall', 'change', onShowAll);
    addEvent('editgroupsettings', 'click', onEditGroupSettingsSave);
    addEvent('canceleditgroupsettings', 'click', onCancel);
    addEvent('editgroupingsettings', 'click', onEditGroupingSettingsSave);
    addEvent('canceleditgroupingsettings', 'click', onCancel);
    addEvent('editgroupingpermissions', 'click', onEditGroupingPermissionsSave);
    addEvent('canceleditgroupingpermissions', 'click', onCancel);
}
