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
 * Allows status form elements to be modified.
 *
 * @module    mod_attendance
 * @author    Sumaiya Javed <sumaiya.javed@catalyst.net.nz>
 * @copyright 2017 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    return {
        init: function() {
         const selectboxes = document.querySelectorAll('.custom-select');
         selectboxes.forEach(box => {
            var studentbox = 'student' + box.name;
            if (box.value == 1) {
                document.getElementsByName(studentbox)[0].style.display = 'block';
            }
            box.addEventListener('change', function handleChange() {
              if (box.value == 1) {
                document.getElementsByName(studentbox)[0].style.display = 'block';
                document.getElementsByName(studentbox)[0].placeholder = "Minutes";
                document.getElementsByName(studentbox)[0].value = '';
              }
              if (box.value == 0) {
                document.getElementsByName(studentbox)[0].style.display = 'none';
                document.getElementsByName(studentbox)[0].value = 0;
              }
              if (box.value == '') {
                document.getElementsByName(studentbox)[0].value = '';
              }
            });
          });
        }
    };
});

