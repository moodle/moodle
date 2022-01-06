/**
 * This was largely copied from the "Topics" format in an early version of "Tiles" and is not changed for summer 2018 release
 * @type {*|{}}
 */
M.course = M.course || {};

M.course.format = M.course.format || {};

/* jshint camelcase:false */
// We ignore camel case as this is copied from an old core file.

/**
 * Get sections config for this format
 *
 * The section structure is:
 * <ul class="tiles">
 *  <li class="section">...</li>
 *  <li class="section">...</li>
 *   ...
 * </ul>
 *
 * @return {object} section list configuration
 */
M.course.format.get_config = function() {
    return {
        container_node: "ul",
        container_class: "tiles",
        section_node: "li",
        section_class: "section"
    };
};

// M.course.format.swap_sections from topics format has been deliberately omitted as it is not necessary for tiles.

/**
 * Process sections after ajax response
 * The actual move is carried out by course/yui/build/moodle-course-dragdrop
 *
 * @param {YUI} Y YUI3 instance
 * @param {array} sectionlist
 * @param {array} response ajax response
 * @param {string} sectionfrom first affected section
 * @param {string} sectionto last affected section
 */
M.course.format.process_sections = function(Y, sectionlist, response, sectionfrom, sectionto) {
    var CSS = {
        SECTIONNAME: "sectionname"
    },
    SELECTORS = {
        SECTIONLEFTSIDE: ".left .section-handle .icon",
        EDITACTVITIESMENU: ".right .section_action_menu a.editing_activities",
        EDITACTIVITIESLINK: ".tile_bar_text a.editactivities"
    };

    if (response.action === "move") {
        // If moving up swap around "sectionfrom" and "sectionto" so the that loop operates.
        if (sectionfrom > sectionto) {
            var temp = sectionto;
            sectionto = sectionfrom;
            sectionfrom = temp;
        }

        // Update titles and move icons in all affected sections.
        var ele, str, stridx, newstr;

        for (var i = sectionfrom; i <= sectionto; i++) {
            // Update section title.
            sectionlist.item(i).one("." + CSS.SECTIONNAME).setContent(response.sectiontitles[i]);
            // Update move icon.
            ele = sectionlist.item(i).one(SELECTORS.SECTIONLEFTSIDE);
            str = ele.getAttribute("alt");
            stridx = str.lastIndexOf(" ");
            newstr = str.substr(0, stridx + 1) + i;
            ele.setAttribute("alt", newstr);
            ele.setAttribute("title", newstr); // For FireFox as "alt" is not refreshed.

            // Added for "Tiles" - swap edit activities links (x2) so they point to right place.
            ele = sectionlist.item(i).one(SELECTORS.EDITACTVITIESMENU); // Right hand link.
            var ele2 = sectionlist.item(i).one(SELECTORS.EDITACTIVITIESLINK); // Left hand link.
            var url = ele.getAttribute("href");
            stridx = url.lastIndexOf("section=");
            var newurl = url.substr(0, stridx + 8) + i; // Number 8 is length of section.
            ele.setAttribute("href", newurl);
            ele2.setAttribute("href", newurl);
        }
    }
};
/* jshint camelcase:true */