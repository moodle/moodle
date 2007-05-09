

function onShowAddMembersForm() {
    hideAllForms();
    showElement("addmembersform");
    updateNonMembers();
    groupname = getSelectedGroupName();
    replaceText('selectedgroup', groupname);
    return false;
}

function onShowAddGroupsToGroupingForm() {
    hideAllForms();
    showElement("addgroupstogroupingform");
    updateGroupsNotInGrouping();
    groupingname = getSelectedGroupingName();
    replaceText('selectedgroupingforaddinggroups', groupingname);
    return false;
}

function onShowCreateGroupingForm() {
    hideAllForms();
    showElement("creategroupingform");
    return false;
}

function onShowCreateGroupForm() {
    hideAllForms();
    showElement("creategroupform");
    groupingname = getSelectedGroupingName();
    replaceText('selectedgroupingforcreatinggroup', groupingname);
    return false;
}

function onShowEditGroupSettingsForm() {
    hideAllForms();
    showElement("editgroupsettingsform");
    getGroupSettings();
    return false;
}

function onShowEditGroupingPermissionsForm() {
    hideAllForms();
    showElement("editgroupingpermissionsform");
    getGroupingPermissions();
    return false;
}

function onShowEditGroupingSettingsForm() {
    hideAllForms();
    showElement("editgroupingsettingsform");
    getGroupingSettings();
    return false;
}


function onShowAutomaticGroupingForm() {
    hideAllForms();
    showElement("createautomaticgroupingform");
    return false;
}

function onPrinterFriendly() {
    document.location.href = "printgrouping.php?courseid="+courseid+"&groupingid="+selectedgroupingid;
    return false;
}
