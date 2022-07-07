// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Client-side JavaScript for group management interface.
 * @copyright vy-shane AT moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_group
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
}


/**
 * Class UpdatableMembersCombo
 */
function UpdatableMembersCombo(wwwRoot, courseId) {
    this.wwwRoot = wwwRoot;
    this.courseId = courseId;

    this.connectCallback = {
        success: function(t, o) {

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

        failure: function() {
            removeLoaderImgs("membersloader", "memberslabel");
        }

    };

    // Hide the updatemembers input since AJAX will take care of this.
    var updatemembers = Y.one('#updatemembers');
    if (updatemembers) {
        updatemembers.hide();
    }
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
        var self = this;
        YUI().use('io', function (Y) {
            Y.io(sUrl, {
                method: 'GET',
                context: this,
                on: self.connectCallback
            });
        });
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

    loadingImg.setAttribute("src", M.util.image_url('/i/ajaxloader', 'moodle'));
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
        if (loader) {
            parentEl.removeChild(loader);
        }
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
