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
 * Select course categories for LTI tool.
 *
 * @module     mod_lti/coursecategory
 * @copyright  2023 Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.3
 */

define([], function() {

    document.addEventListener('click', event => {
        const checkedbox = event.target.closest(".lticoursecategories");

        if (checkedbox) {
            // Get checkbox status.
            const checkboxstatus = checkedbox.checked;

            // Check / Uncheck all child category checkboxes based on selected checkbox status.
            const categorycontainer = document.querySelector('#collapse' + checkedbox.value);
            if (categorycontainer) {
                const categorycontainercheckbox = categorycontainer.querySelectorAll('input[type="checkbox"]');
                for (let i = 0; i < categorycontainercheckbox.length; i++) {
                    categorycontainercheckbox[i].checked = checkboxstatus;
                }
            }

            const lticategorytree = document.querySelector('.modltitree');
            const ltitreecheckbox = lticategorytree.querySelectorAll('input[type="checkbox"]');
            let listvalue = '';
            for (let i = 0; i < ltitreecheckbox.length; i++) {
                if (ltitreecheckbox[i].checked) {
                    if (listvalue.length == 0) {
                        listvalue = ltitreecheckbox[i].value;
                    } else {
                        listvalue = listvalue + ',' + ltitreecheckbox[i].value;
                    }
                }
            }
            document.querySelector('input[name="lti_coursecategories"]').value = listvalue;
        }
    });

    /**
     * Get parent elements with class = accordion.
     *
     * @method getParents
     * @private
     * @param {string} elem DOM element.
     * @return {array} Parent elements.
     */
    function getParents(elem) {
        // Set up a parent array
        const parents = [];

        // Push each parent element to the array
        for (; elem && elem !== document; elem = elem.parentNode) {
            if (elem.classList.contains('accordion-group')) {
                parents.push(elem);
            }
        }

        // Return our parent array
        return parents;
    }

    return /** @alias module:mod_lti/coursecategory */ {

        /**
         * Initialise this module.
         * Loop through checkbox form elements starting with #cat-{N} and set it to checked
         * if {N} is found in the Selected category(s) list. Show / Hide the parent UL element.
         *
         * @param {string} selectedcategories Selected category(s).
         */
        init: function(selectedcategories) {
            if (selectedcategories.length) {
                const separator = ",";
                const values = selectedcategories.split(separator);

                for (let i = 0; i < values.length; i++) {
                    const categoryid = document.getElementById("cat-" + values[i]);
                    if (categoryid.value !== 0) {
                        categoryid.checked = true;
                    }
                    const parents = getParents(categoryid);
                    parents.forEach(function(element) {
                        const elem = element.querySelector('a.accordion-toggle');
                        const elembody = element.querySelector('.accordion-body');

                        if (elem && elem.classList.contains('collapsed')) {
                            elem.classList.remove('collapsed');
                        }
                        if (elembody) {
                            elembody.classList.remove('collapse');
                            elembody.classList.add('show');
                        }
                    });
                }
            }
        }
    };
});