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
 * JavaScript library for the hotpot module.
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_hotpot = M.mod_hotpot || {};

M.mod_hotpot.fix_targets = {

    /**
     * prevent
     */
    prevent: function() {
        alert('fix targets');
    }
}

M.mod_hotpot.secure_window = {

    /**
     * init
     *
     * @param xxx Y
     */
    init: function(Y) {
        if (window.location.href.substring(0,4) == 'file') {
            window.location = 'about:blank';
        }
        Y.delegate('contextmenu', M.mod_hotpot.secure_window.prevent, document.body, '*');
        Y.delegate('mousedown', M.mod_hotpot.secure_window.prevent_mouse, document.body, '*');
        Y.delegate('mouseup', M.mod_hotpot.secure_window.prevent_mouse, document.body, '*');
        Y.delegate('dragstart', M.mod_hotpot.secure_window.prevent, document.body, '*');
        Y.delegate('selectstart', M.mod_hotpot.secure_window.prevent, document.body, '*');
        M.mod_hotpot.secure_window.clear_status;
        Y.on('beforeprint', function() {
            Y.one(document.body).setStyle('display', 'none');
        }, window);
        Y.on('afterprint', function() {
            Y.one(document.body).setStyle('display', 'block');
        }, window);
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'press:67,86,88+ctrl');
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'up:67,86,88+ctrl');
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'down:67,86,88+ctrl');
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'press:67,86,88+meta');
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'up:67,86,88+meta');
        Y.on('key', M.mod_hotpot.secure_window.prevent, '*', 'down:67,86,88+meta');
    },

    /**
     * clear_status
     */
    clear_status: function() {
        window.status = '';
        setTimeout(M.mod_hotpot.secure_window.clear_status, 10);
    },

    /**
     * prevent
     *
     * @param xxx e
     */
    prevent: function(e) {
        alert(M.str.hotpot.functiondisabledbysecuremode);
        e.halt();
    },

    /**
     * prevent_mouse
     *
     * @param xxx e
     */
    prevent_mouse: function(e) {
        if (e.button == 1 && /^(INPUT|TEXTAREA|BUTTON|SELECT|LABEL|A)$/i.test(e.target.get('tagName'))) {
            // Left click on a button or similar. No worries.
            return;
        }
        alert(M.str.hotpot.functiondisabledbysecuremode);
        e.halt();
    },

    /**
     * close
     *
     * @param xxx url
     * @param xxx delay
     */
    close: function(url, delay) {
        setTimeout(function() {
            if (window.opener) {
                window.opener.document.location.reload();
                window.close();
            } else {
                window.location.href = url;
            }
        }, delay*1000);
    }
};
