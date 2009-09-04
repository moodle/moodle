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

//add function to clear the list of context associations
function emptyAssocList() {
    var modassoc = document.getElementById('id_modassoc');
    var options = modassoc.getElementsByTagName('option');
    while (options.length > 0) {
        var option = options[0];
        modassoc.removeChild(option);
    }
}

//add function for adding an element to the list of context associations
function addModAssoc(name, id) {
    var modassoc = document.getElementById('id_modassoc');
    newoption = document.createElement('option');
    newoption.text = name;
    newoption.value = id;

    try {
        modassoc.add(newoption, null);  //standard, broken in IE
    } catch(ex) {
        modassoc.add(newoption);
    }
}

//add function to add associations for a particular course
function addCourseAssociations() {
    var courses = document.getElementById('id_courseassoc');
    var course = courses.options[courses.selectedIndex].value;
    var modassoc = document.getElementById('id_modassoc');
    var newoption = null;

    emptyAssocList(); 
    
    for (var mycourse in blog_edit_form_modnames) {
        if (mycourse == course) {
            for (var modid in blog_edit_form_modnames[mycourse]) {
                addModAssoc(blog_edit_form_modnames[mycourse][modid], modid);
            }
        }
    }
}

function select_initial_course() {
    var course = document.getElementById('id_courseassoc');
    var mods = document.getElementById('id_modassoc');
    var i = 0;
    var j = 0;
    emptyAssocList();

    for (i = 0; i < course.length; i++) {
        if (course.options[i].value == blog_edit_existing.courseassoc) {
            course.selectedIndex = i;
            addCourseAssociations();
            
            for (j = 0; j < mods.length; j++) {
                for (var modid in blog_edit_existing.modassoc) {
                    if (mods.options[j].value == blog_edit_existing.modassoc[modid]) {
                        mods.options[j].selected = true;
                    }
                }
            }
        }
    }
}

