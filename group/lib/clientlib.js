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

    this.connectCallback = {

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

        	    if (groupsComboEl && o.responseText) {
        	        var groups = eval("("+o.responseText+")");

        	        // Populate the groups combo box.
                    for (var i=0; i<groups.length; i++) {
                        var optionEl = document.createElement("option");
                        optionEl.setAttribute("value", groups[i].id);
                        optionEl.innerHTML = groups[i].name;
                        groupsComboEl.appendChild(optionEl);
                    }
                }
        	}
        	// Remove the loader gif image.
        	removeLoaderImgs("groupsloader", "groupslabel");
        },

        failure: function(o) {
            removeLoaderImgs("membersloader", "memberslabel");
            this.currentTransId = null;
        }

    };

    // Add onchange event to groups combo box.
    // Okay, this is not working in IE. The onchange is never fired...
    // I'm hard coding the onchange in ../index.php. Not ideal, but it works
    // then. vyshane AT moodle DOT com.
    /*
    groupsComboEl = document.getElementById("groups");
    if (groupsComboEl) {
        groupsComboEl.setAttribute("onchange", "membersCombo.refreshMembers(this.options[this.selectedIndex].value);");
    }
    */

    // Hide the updategroups input since AJAX will take care of this.
    YAHOO.util.Dom.setStyle("updategroups", "display", "none");
}

/**
 * When a grouping is selected, we need to update the groups.
 */
UpdatableGroupsCombo.prototype.refreshGroups = function (groupingId) {
    // Add the loader gif image.
    createLoaderImg("groupsloader", "groupslabel", this.wwwRoot);

    // Clear the groups combo box.
    var selectEl = document.getElementById("groups");
    if (selectEl) {
        while (selectEl.firstChild) {
            selectEl.removeChild(selectEl.firstChild);
        }
    }

    var sUrl = this.wwwRoot+"/group/index.php?id="+this.courseId+"&grouping="+groupingId+"&act_ajax_getgroupsingrouping";
    YAHOO.util.Connect.asyncRequest('GET', sUrl, this.connectCallback, null);
};



/**
 * Class UpdatableMembersCombo
 */
function UpdatableMembersCombo(wwwRoot, courseId) {
    this.wwwRoot = wwwRoot;
    this.courseId = courseId;

    this.connectCallback = {
        success: function(o) {

        	if (o.responseText !== undefined) {
                var selectEl = document.getElementById("members");

        	    if (selectEl && o.responseText) {
        	        var members = eval("("+o.responseText+")");

        	        // Populate the members combo box.
                    for (var i=0; i<members.length; i++) {
                        var optionEl = document.createElement("option");
                        optionEl.setAttribute("value", members[i].id);
                        optionEl.innerHTML = members[i].name;
                        selectEl.appendChild(optionEl);
                    }
                }
        	}
        	// Remove the loader gif image.
        	removeLoaderImgs("membersloader", "memberslabel");
        },

        failure: function(o) {
            removeLoaderImgs("membersloader", "memberslabel");
        }

    };

    // Hide the updatemembers input since AJAX will take care of this.
    YAHOO.util.Dom.setStyle("updatemembers", "display", "none");
}

/**
 * When a group is selected, we need to update the members.
 */
UpdatableMembersCombo.prototype.refreshMembers = function (groupId) {
    // Add the loader gif image.
    createLoaderImg("membersloader", "memberslabel", this.wwwRoot);

    // Clear the members combo box.
    var selectEl = document.getElementById("members");
    if (selectEl) {
        while (selectEl.firstChild) {
            selectEl.removeChild(selectEl.firstChild);
        }
    }

    var sUrl = this.wwwRoot+"/group/index.php?id="+this.courseId+"&group="+groupId+"&act_ajax_getmembersingroup";
    YAHOO.util.Connect.asyncRequest("GET", sUrl, this.connectCallback, null);
};



var createLoaderImg = function (elClass, parentId, wwwRoot) {
    var parentEl = document.getElementById(parentId);
    if (!parentEl) {
        return false;
    }
    if (document.getElementById("loaderImg")) {
        // A loader image already exists.
        return false;
    }
    var loadingImg = document.createElement("img");

    loadingImg.setAttribute("src", wwwRoot+"/pix/i/ajaxloader.gif");
    loadingImg.setAttribute("class", elClass);
    loadingImg.setAttribute("alt", "Loading");
    loadingImg.setAttribute("id", "loaderImg");
    parentEl.appendChild(loadingImg);

    return true;
};


var removeLoaderImgs = function (elClass, parentId) {
    var parentEl = document.getElementById(parentId);
    if (parentEl) {
        var loader = document.getElementById("loaderImg");
        parentEl.removeChild(loader);
    }
};
