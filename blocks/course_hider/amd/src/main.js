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
 * Course Hider Tool
 *
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {

    let allnone = document.getElementById('course_hider_all');
    if (allnone !== null) {

        allnone.addEventListener("change", (event) => {
            const container = document.querySelector("div.block_course_hider_container");
            const matches = container.getElementsByClassName("course_hider_toggle");
            matches.forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        });
    }

    const checkers = document.querySelector(".course_hider_toggle");
    if (checkers !== null) {
        checkers.addEventListener("change", (event) => {
            // If All/None is checked and one of the elements is unchecked then toggle All/None.
            if (event.target.checked == false) {
                document.getElementById('course_hider_all').checked = false;
            } else {
                // One was clicked to true, are they all true?
                const container = document.querySelector("div.block_course_hider_container");
                const matches = container.getElementsByClassName("course_hider_toggle");
                var itstrue = true;
                matches.forEach(checkbox => {
                    if (checkbox.checked == false) {
                        itstrue = false;
                        return;
                    }
                });

                if (itstrue == true) {
                    document.getElementById('course_hider_all').checked = true;
                }
            }
        });
    }

    const form = document.getElementById("course_hider_courses_selected");
    if (form !== null) {
        form.addEventListener('submit', (event) => {
            event.preventDefault(); // prevent the default form submission

            // Let's get the lock value
            const lock = document.getElementById('id_lockcourses');
            const selected_lock = lock.options[lock.selectedIndex];
            const selectedLockValue = selected_lock.value;

            const hide = document.getElementById('id_hidecourses');
            const selected_hide = lock.options[hide.selectedIndex];
            const selectedHideValue = selected_hide.value;
            const inputs = form.querySelectorAll('input.course_hider_toggle');

            const formData = new FormData();
            const inputValues = [];

            formData.append("btnexecute", true);
            formData.append("lock", selectedLockValue);
            formData.append("hide", selectedHideValue);
            inputs.forEach((input) => {
                if (input.checked) {
                    inputValues.push(input.value);
                }
            });

            formData.append("courses", inputValues);

            // Resubmit the form
            const xhr = new XMLHttpRequest();
            xhr.open("POST", form.action, true);
            xhr.responseType = "document";
            xhr.send(formData);

            xhr.onload = function() {
                document.body.getElementsByClassName('block_course_hider_container')[0].innerHTML =
                    xhr.responseXML.body.getElementsByClassName('block_course_hider_container')[0].innerHTML;
            };
        });
    }
};
