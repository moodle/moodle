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

// A module name should be composed of:
// moodle-<component>-<module>[-skin]
var path = me.path,
    parts = me.name.replace(/^moodle-/,'').split('-', 3),
    modulename = parts.pop();

if (/(skin|core)/.test(modulename)) {
    // For these types, we need to remove the final word and set the type.
    modulename = parts.pop();
    me.type = 'css';
}

// Build the first part of the filename.
me.path = parts.join('-') + '/' + modulename + '/' + modulename;

// CSS is not minified, but all other types are.
if (me.type !== 'css') {
    me.path = me.path + '-min';
}

// Add the file extension.
me.path = me.path + '.' + me.type;
