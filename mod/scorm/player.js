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

function scorm_openpopup(url,name,options,width,height) {
    if (width <= 100) {
        width = Math.round(screen.availWidth * width / 100);
    }
    if (height <= 100) {
        height = Math.round(screen.availHeight * height / 100);
    }
    options += ",width=" + width + ",height=" + height;

    windowobj = window.open(url,name,options);
    if (!windowobj) {
        return;
    }
    if ((width == 100) && (height == 100)) {
        // Fullscreen
        windowobj.moveTo(0,0);
    }
    windowobj.focus();
    return windowobj;
}