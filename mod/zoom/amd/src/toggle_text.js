// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Toggles text to be shown when a user hits 'Show More' and
 * hides text when user hits 'Show Less'
 *
 * @package    mod_zoom
 * @copyright  2020 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { get_string as getString } from "core/str";

export const init = () => {
  const button = document.querySelector("#show-more-button");
  const body = document.querySelector("#show-more-body");
  button.addEventListener("click", async () => {
    if (body.style.display == "") {
      body.style.display = "none";
      button.innerHTML = await getString("meeting_invite_show", "mod_zoom");
    } else {
      body.style.display = "";
      button.innerHTML = await getString("meeting_invite_hide", "mod_zoom");
    }
  });
};
