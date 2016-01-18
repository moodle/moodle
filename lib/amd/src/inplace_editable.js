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
 * AJAX helper for the inline editing a value.
 *
 * This script is automatically included from template core/inplace_editable
 * It registers a click-listener on [data-inplaceeditablelink] link (the "inplace edit" icon),
 * then replaces the displayed value with an input field. On "Enter" it sends a request
 * to web service core_update_inplace_editable, which invokes the specified callback.
 * Any exception thrown by the web service (or callback) is displayed as an error popup.
 *
 * @module     core/inplace_editable
 * @package    core
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/config'],
        function($, ajax, templates, notification, str, cfg) {

    $('body').on('click keypress', '[data-inplaceeditable] [data-inplaceeditablelink]', function(e) {
        if (e.type === 'keypress' && e.keyCode !== 13) {
            return;
        }
        e.stopImmediatePropagation();
        e.preventDefault();
        var target = $(this),
            mainelement = target.closest('[data-inplaceeditable]');

        var update_value = function(mainelement, value) {
            var promises = ajax.call([{
                methodname: 'core_update_inplace_editable',
                args: { itemid : mainelement.attr('data-itemid'),
                    component : mainelement.attr('data-component') ,
                    itemtype : mainelement.attr('data-itemtype') ,
                    value : value }
            }], true);

            $.when.apply($, promises)
                .done( function(data) {
                    var oldvalue = mainelement.attr('data-value');
                    templates.render('core/inplace_editable', data).done(function(html, js) {
                        templates.replaceNode(mainelement, html, js);
                        mainelement.find('[data-inplaceeditablelink]').focus();
                    });
                    mainelement.trigger({type: 'updated', ajaxreturn: data, oldvalue: oldvalue});
                }).fail(function(ex) {
                    var e = $.Event('updatefailed', { exception: ex, newvalue: value });
                    mainelement.trigger(e);
                    if (!e.isDefaultPrevented()) {
                        notification.exception(ex);
                    }
                });
        };

        var turn_editing_off = function(el) {
            var input = el.find('input');
            input.off();
            el.html(el.attr('data-oldcontent'));
            el.removeAttr('data-oldcontent');
            el.removeClass('inplaceeditingon');
        };

        var turn_editing_off_everywhere = function() {
            $('span.inplaceeditable.inplaceeditingon').each(function() {
                turn_editing_off($( this));
            });
        };

        var unique_id = function(prefix, idlength) {
            var uniqid = prefix;
            for (var i = 0; i < idlength; i++) {
                uniqid += String(Math.floor(Math.random() * 10));
            }
            // Make sure this ID is not already taken by an existing element.
            if ($("#" + uniqid).length === 0) {
                return uniqid;
            }
            return unique_id(prefix, idlength);
        };

        var turn_editing_on = function(el) {
            el.addClass('inplaceeditingon');
            el.attr('data-oldcontent', el.html());

            str.get_string('edittitleinstructions').done(function(s) {
                var instr = $('<span class="editinstructions">' + s + '</span>').
                        attr('id', unique_id('id_editinstructions_', 20)),
                    inputelement = $('<input type="text"/>').
                        attr('id', unique_id('id_inplacevalue_', 20)).
                        attr('value', el.attr('data-value')).
                        attr('aria-describedby', instr.attr('id')),
                    lbl = $('<label class="accesshide">' + mainelement.attr('data-editlabel') + '</label>').
                        attr('for', inputelement.attr('id'));
                el.html('').append(instr).append(lbl).append(inputelement);

                inputelement.focus();
                inputelement.select();
                inputelement.on('keyup keypress focusout', function(e) {
                    if (cfg.behatsiterunning && e.type === 'focusout') {
                        // Behat triggers focusout too often.
                        return;
                    }
                    if (e.type === 'keypress' && e.keyCode === 13) {
                        // We need 'keypress' event for Enter because keyup/keydown would catch Enter that was
                        // pressed in other fields.
                        update_value(el, inputelement.val());
                        turn_editing_off(el);
                    }
                    if ((e.type === 'keyup' && e.keyCode === 27) || e.type === 'focusout') {
                        // We need 'keyup' event for Escape because keypress does not work with Escape.
                        turn_editing_off(el);
                    }
                });
            });
        };

        // Turn editing on for the current element and register handler for Enter/Esc keys.
        turn_editing_off_everywhere();
        turn_editing_on(mainelement);

    });

    return {};
});