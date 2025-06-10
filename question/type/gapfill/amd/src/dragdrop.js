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
 * JavaScript code for the gapfill question type.
 *
 * @copyright  2020 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = (singleuse) => {
  Window.singleuse = singleuse;
  var draggables = document.querySelectorAll('span[class*="draggable"]');
  draggables.forEach(function(e) {
    e.addEventListener('dragstart', dragStart);
  });
  document.querySelectorAll('input[class*="droptarget"]').forEach(function(e) {
    e.addEventListener("dblclick", function() {
      if (Window.singleuse) {
        dragShow(this);
      }
      this.value = "";
    });

    e.addEventListener("drop", drop);

  });
  /**
   *
   * @param {*} that
   */
  function dragShow(that) {
    var targetVal = that.value;
    draggables.forEach(function(e) {
      if (e.innerText.trim() === targetVal.trim()) {
        e.classList.remove("hide");
        that.value = "";
      }
    });
  }

  /**
   * Stops strange things happening on ios drop event
   * @param {*} e
   */
  document.addEventListener('dragover', function(e) {
    e.preventDefault();
  });


  /**
   *
   * @param {*} e
   */
  function drop(e) {
    dragShow(this);
    e.target.value = e.dataTransfer.getData('text/plain');
    var sourceId = e.dataTransfer.getData("sourceId");
    var sourceEl = document.getElementById(sourceId);
    if (Window.singleuse) {
      sourceEl.classList.add('hide');
      e.preventDefault();
    }
    e.preventDefault();
  }
  /**
   *
   * @param {*} e
   */
   function dragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.innerText);
    e.dataTransfer.setData('sourceId', this.id);
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.dropEffect = "move";
  }
};

