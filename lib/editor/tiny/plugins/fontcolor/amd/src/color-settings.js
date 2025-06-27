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
 * Javascript functions to add/remove a row for one color entry that contains
 * two input fields for color name and color hex value. This is suited for and
 * works with the template settings_config_color.mustache only.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The add button that was clicked. This button is in the last row. Therefore, it creates
 * a clone of the entire row. In the cloned row we need to update the input elements (id and
 * name attribute -> increase the tailing sequence number by 1, empty any value attributes).
 * Also, in the cloned row the event must be attached to the button.
 * For the existing row that was cloned, the button must be changed from an add to a delete
 * button (change class and content of the button from + -> -).
 * @param {Node} element
 */
const insertRow = element => {
  const currRow = element.parentNode.parentNode;
  const newRow = currRow.cloneNode(true);
  const parts = newRow.querySelector('input').getAttribute('name').split('_');
  const num = parseInt(parts[parts.length - 1]);
  const newNum = num + 1;
  const re = new RegExp('_' + num.toString() + '$');
  newRow.querySelectorAll('input').forEach(input => {
    ['name', 'id', 'value'].forEach((attr) => {
      if (attr === 'value') {
        input.value = '';
        return;
      }
      let content = input.getAttribute(attr).replace(re, '_' + newNum.toString());
      input.setAttribute(attr, content);
    });
  });
  currRow.parentNode.insertBefore(newRow, currRow.nextSibling);
  const button = currRow.querySelector('button');
  button.classList.remove('add');
  button.classList.add('del');
  button.innerHTML = '-';
  // eslint-disable-next-line
  jscolor.install(newRow);
  newRow.querySelector('button').addEventListener('click', function(e) {
    handleRow(e);
  });
};

/**
 * Remove the current row with input field for color name and value and the remove button itself.
 * @param {Node} element the button that was clicked to remove the current line.
 */
const deleteRow = element => {
  element.parentNode.parentNode.remove();
};

const handleRow = event => {
  event.preventDefault();
  if (event.target.classList.contains('del')) {
    deleteRow(event.target);
  } else if (event.target.classList.contains('add')) {
    insertRow(event.target);
  }
};

/**
 * Initialize event handlers for all buttons inside the input fields for one color setting.
 * @param {string} name of settings field, for which the js handling is needed.
 */
export const init = name => {
  const root = document.querySelector('.' + name);
  if (!root) {
    return;
  }
  const buttons = root.getElementsByTagName('button');
  for (let i = 0; i < buttons.length; i++) {
    buttons.item(i).addEventListener('click', function(e) {
      handleRow(e);
    });
  }
};