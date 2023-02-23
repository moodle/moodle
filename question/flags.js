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

            var input = Y.Node.create('<input type="hidden" class="questionflagvalue" />');
            input.set('id', checkbox.get('id'));
            input.set('name', checkbox.get('name'));
            input.set('value', checkbox.get('checked') ? 1 : 0);

            var ariaPressed = checkbox.get('checked') ? 'true' : 'false';
            var toggle = Y.Node.create('<a ' +
                'tabindex="0" ' +
                'class="aabtn" ' +
                'role="button" ' +
                'aria-pressed="' + ariaPressed + '">' +
                    '.' +
                '</a>');
            M.core_question_flags.update_flag(input, toggle);

            checkbox.remove();
            flagdiv.one('label').remove();
            flagdiv.append(input);
            flagdiv.append(toggle);
        });

        Y.delegate('click', function(e) {
            e.halt();
            M.core_question_flags.process(this);
        }, document.body, 'div.questionflag');
        Y.delegate('key', function(e) {
            e.halt();
            if (e.keyCode == 13) {
                M.core_question_flags.process(this);
            }
        }, document.body, 'down:enter, space', 'div.questionflag');
        Y.delegate('key', function(e) {
            e.halt();
            M.core_question_flags.process(this);
        }, document.body, 'up:space', 'div.questionflag');
    },

    update_flag: function(input, toggle) {
        var value = input.get('value');
        toggle.setContent(
            '<img class="questionflagimage" src="' + M.core_question_flags.flagattributes[value].src + '" alt="" />' +
            M.core_question_flags.flagattributes[value].text
        );
        toggle.set('aria-pressed', parseInt(value) ? 'true' : 'false');
        toggle.set('aria-label', M.core_question_flags.flagattributes[value].alt);
        if (M.core_question_flags.flagattributes[value].title != M.core_question_flags.flagattributes[value].text) {
            toggle.set('title', M.core_question_flags.flagattributes[value].title);
        } else {
            toggle.removeAttribute('title');
        }
    },

    /**
     * Process the change of flag status.
     *
     * @param {Y.Node} target The root element
     */
    process: function(target) {
        var input = target.one('input.questionflagvalue');
        input.set('value', 1 - input.get('value'));
        M.core_question_flags.update_flag(input, target.one('[aria-pressed]'));
        var postdata = target.one('input.questionflagpostdata').get('value') +
            input.get('value');

        Y.io(M.core_question_flags.actionurl, {method: 'POST', 'data': postdata});
        M.core_question_flags.fire_listeners(postdata);
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
