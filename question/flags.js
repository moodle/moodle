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
 * JavaScript library for dealing with the question flags.
 *
 * This script, and the YUI libraries that it needs, are inluded by
 * the $PAGE->requires->js calls in question_get_html_head_contributions in lib/questionlib.php.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.core_question_flags = {
    flagattributes: null,
    actionurl: null,
    listeners: [],

    init: function(Y, actionurl, flagattributes) {
        M.core_question_flags.flagattributes = flagattributes;
        M.core_question_flags.actionurl = actionurl;

        Y.all('div.questionflag').each(function(flagdiv, i) {
            var checkbox = flagdiv.one('input[type=checkbox]');
            if (!checkbox) {
                return;
            }

            var input = Y.Node.create('<input type="hidden" />');
            input.set('id', checkbox.get('id'));
            input.set('name', checkbox.get('name'));
            input.set('value', checkbox.get('checked') ? 1 : 0);

            // Create an image input to replace the img tag.
            var image = Y.Node.create('<input type="image" class="questionflagimage" />');
            M.core_question_flags.update_flag(input, image);

            checkbox.remove();
            flagdiv.one('label').remove();
            flagdiv.append(input);
            flagdiv.append(image);
        });

        Y.delegate('click', function(e) {
            var input = this.previous('input');
            input.set('value', 1 - input.get('value'));
            M.core_question_flags.update_flag(input, this);
            var postdata = this.previous('input.questionflagpostdata').get('value') +
                    input.get('value');

            e.halt();
            Y.io(M.core_question_flags.actionurl , {method: 'POST', 'data': postdata});
            M.core_question_flags.fire_listeners(postdata);
        }, document.body, 'input.questionflagimage');
    },

    update_flag: function(input, image) {
        image.setAttrs(M.core_question_flags.flagattributes[input.get('value')]);
    },

    add_listener: function(listener) {
        M.core_question_flags.listeners.push(listener);
    },

    fire_listeners: function(postdata) {
        for (var i = 0; i < M.core_question_flags.listeners.length; i++) {
            M.core_question_flags.listeners[i](
                postdata.match(/\bqubaid=(\d+)\b/)[1],
                postdata.match(/\bslot=(\d+)\b/)[1],
                postdata.match(/\bnewstate=(\d+)\b/)[1]
            );
        }
    }
};
