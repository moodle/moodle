/**
 * Client-side JavaScript for group management interface.
 * @author vy-shane AT moodle.com 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */


/**
 * Class UpdatableGroupsCombo
 */
function UpdatableGroupsCombo(wwwRoot, courseId) {
    this.wwwRoot = wwwRoot;
    this.courseId = courseId;

    this.callback = {
        success: function(o) {

        	if (o.responseText !== undefined) {
                var groupsComboEl = document.getElementById("groups");
                var membersComboEl = document.getElementById("members");

                if (membersComboEl) {
                    // Clear the members combo box.
                    while (membersComboEl.firstChild) {
                        membersComboEl.removeChild(membersComboEl.firstChild);
                    }
                }

        	    if (groupsComboEl) {
        	        // Clear the groups combo box.
                    while (groupsComboEl.firstChild) {
                        groupsComboEl.removeChild(groupsComboEl.firstChild);
                    }
            	    if (o.responseText) {
            	        var groups = eval('('+o.responseText+')');

            	        // Populate the groups combo box.
                        for (var i=0; i<groups.length; i++) {
                            var optionEl = document.createElement("option");
                            optionEl.setAttribute("value", groups[i].id);
                            optionEl.setAttribute("onclick",
                                "membersCombo.refreshMembers("+groups[i].id+")");
                            optionEl.innerHTML = groups[i].name;
                            groupsComboEl.appendChild(optionEl);
                        }
            	    }
                }
        	}
        }
    }
    
    // Hide the updategroups input since AJAX will take care of this.
    var updateGroupsButton = document.getElementById("updategroups");
    updateGroupsButton.setAttribute("style", "display:none;");
}

/**
 * When a grouping is selected, we need to update the groups.
 */
UpdatableGroupsCombo.prototype.refreshGroups = function (groupingId) {
    var sUrl = this.wwwRoot+"/group/index.php?id="+this.courseId+"&grouping="
                        +groupingId+"&act_ajax_getgroupsingrouping";
    YAHOO.util.Connect.asyncRequest('GET', sUrl, this.callback, null);
}



/**
 * Class UpdatableMembersCombo
 */
function UpdatableMembersCombo(wwwRoot, courseId) {
    this.wwwRoot = wwwRoot;
    this.courseId = courseId;

    this.callback = {
        success: function(o) {

        	if (o.responseText !== undefined) {
                var selectEl = document.getElementById("members");

        	    if (selectEl) {
        	        // Clear the members combo box.
                    while (selectEl.firstChild) {
                        selectEl.removeChild(selectEl.firstChild);
                    }
            	    if (o.responseText) {
            	        var members = eval('('+o.responseText+')');

            	        // Populate the members combo box.
                        for (var i=0; i<members.length; i++) {
                            var optionEl = document.createElement("option");
                            optionEl.setAttribute("value", members[i].id);
                            optionEl.innerHTML = members[i].firstname+" "+members[i].lastname;
                            selectEl.appendChild(optionEl);
                        }
            	    }
                }
        	}
        }
    }
    
    // Hide the updatemembers input since AJAX will take care of this.
    var updateMembersButton = document.getElementById("updatemembers");
    updateMembersButton.setAttribute("style", "display:none;");
}

/**
 * When a group is selected, we need to update the members.
 */
UpdatableMembersCombo.prototype.refreshMembers = function (groupId) {
    var sUrl = this.wwwRoot+"/group/index.php?id="+this.courseId+"&group="
                        +groupId+"&act_ajax_getmembersingroup";
    YAHOO.util.Connect.asyncRequest('GET', sUrl, this.callback, null);
}



