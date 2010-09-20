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
                    // Clear the members list box.
                    while (membersComboEl.firstChild) {
                        membersComboEl.removeChild(membersComboEl.firstChild);
                    }
                }

                if (groupsComboEl && o.responseText) {
                    var groups = eval("("+o.responseText+")");

                    // Populate the groups list box.
                    for (var i=0; i<groups.length; i++) {
                        var optionEl = document.createElement("option");
                        optionEl.setAttribute("value", groups[i].id);
                        optionEl.title = groups[i].name;
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

    // Add onchange event to groups list box.
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
                    var roles = eval("("+o.responseText+")");

                    // Clear the members list box.
                    if (selectEl) {
                        while (selectEl.firstChild) {
                            selectEl.removeChild(selectEl.firstChild);
                        }
                    }
                    // Populate the members list box.
                    for (var i=0; i<roles.length; i++) {
                        var optgroupEl = document.createElement("optgroup");
                        optgroupEl.setAttribute("label",roles[i].name);

                        for(var j=0; j<roles[i].users.length; j++) {
                            var optionEl = document.createElement("option");
                            optionEl.setAttribute("value", roles[i].users[j].id);
                            optionEl.title = roles[i].users[j].name;
                            optionEl.innerHTML = roles[i].users[j].name;
                            optgroupEl.appendChild(optionEl);
                        }
                        selectEl.appendChild(optgroupEl);
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
 * The Add/Remove Users button also needs to be disabled/enabled
 * depending on whether or not a group is selected
 */
UpdatableMembersCombo.prototype.refreshMembers = function () {

    // Get group selector and check selection type
    var selectEl = document.getElementById("groups");
    var selectionCount=0,groupId=0;
    if( selectEl ) {
        for (var i = 0; i < selectEl.options.length; i++) {
            if(selectEl.options[i].selected) {
                selectionCount++;
                if(!groupId) {
                    groupId=selectEl.options[i].value;
                }
            }
        }
    }
    var singleSelection=selectionCount == 1;

    // Add the loader gif image (we only load for single selections)
    if(singleSelection) {
        createLoaderImg("membersloader", "memberslabel", this.wwwRoot);
    }

    // Update the label.
    var spanEl = document.getElementById("thegroup");
    if (singleSelection) {
        spanEl.innerHTML = selectEl.options[selectEl.selectedIndex].title;
    } else {
        spanEl.innerHTML = '&nbsp;';
    }

    // Clear the members list box.
    selectEl = document.getElementById("members");
    if (selectEl) {
        while (selectEl.firstChild) {
            selectEl.removeChild(selectEl.firstChild);
        }
    }

    document.getElementById("showaddmembersform").disabled = !singleSelection;
    document.getElementById("showeditgroupsettingsform").disabled = !singleSelection;
    document.getElementById("deletegroup").disabled = selectionCount == 0;

    if(singleSelection) {
        var sUrl = this.wwwRoot+"/group/index.php?id="+this.courseId+"&group="+groupId+"&act_ajax_getmembersingroup";
        YAHOO.util.Connect.asyncRequest("GET", sUrl, this.connectCallback, null);
    }
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

    loadingImg.setAttribute("src", M.util.image_url('moodle', '/i/ajaxloader'));
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

/**
 * Updates the current groups information shown about a user when a user is selected.
 *
 * @global {Array} userSummaries
 *      userSummaries is added to the page via /user/selector/lib.php - group_non_members_selector::print_user_summaries()
 *      as a global that can be used by this function.
 */
function updateUserSummary() {
    var selectEl = document.getElementById('addselect'),
        summaryDiv = document.getElementById('group-usersummary'),
        length = selectEl.length,
        selectCnt = 0,
        selectIdx = -1,
        i;

    for (i = 0; i < length; i++) {
        if (selectEl.options[i].selected) {
            selectCnt++;
            selectIdx = i;
        }
    }

    if (selectCnt == 1 && userSummaries[selectIdx]) {
        summaryDiv.innerHTML = userSummaries[selectIdx];
    } else {
        summaryDiv.innerHTML = '';
    }

    return true;
}

function init_add_remove_members_page(Y) {
    var add = Y.one('#add');
    var addselect = M.core_user.get_user_selector('addselect');
    add.set('disabled', addselect.is_selection_empty());
    addselect.on('user_selector:selectionchanged', function(isempty) {
        add.set('disabled', isempty);
    });

    var remove = Y.one('#remove');
    var removeselect = M.core_user.get_user_selector('removeselect');
    remove.set('disabled', removeselect.is_selection_empty());
    removeselect.on('user_selector:selectionchanged', function(isempty) {
        remove.set('disabled', isempty);
    });

    addselect = document.getElementById('addselect');
    addselect.onchange = updateUserSummary;
}