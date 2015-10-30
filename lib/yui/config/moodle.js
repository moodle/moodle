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
// moodle-<component>-<module>[-<submodule>][-skin]
var parts = me.name.replace(/^moodle-/,'').split('-'),
    component = parts.shift(),
    module = parts[0],
    min = '-min';

if (/-(skin|core)$/.test(me.name)) {
    // For these types, we need to remove the final word and set the type.
    parts.pop();
    me.type = 'css';

    // CSS is not minified - clear the min option.
    min = '';
}

if (module) {
    // Determine the filename based on the remaining parts.
    var filename = parts.join('-');

    // Build the first part of the filename.
    me.path = component + '/' + module + '/' + filename + min + '.' + me.type;
} else {
    // This is a hangup from the old ways of writing Modules.
    // We will start to warn about this once we have removed all core components of this form.
    me.path = component + '/' + component + '.' + me.type;
}
