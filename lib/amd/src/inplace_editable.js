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
define(['jquery',
        'core/ajax',
        'core/templates',
        'core/notification',
        'core/str',
        'core/config',
        'core/url',
        'core/form-autocomplete',
        'core/pending',
    ],
    function($, ajax, templates, notification, str, cfg, url, autocomplete, Pending) {

    $('body').on('click keypress', '[data-inplaceeditable] [data-inplaceeditablelink]', function(e) {
        if (e.type === 'keypress' && e.keyCode !== 13) {
            return;
        }
        var editingEnabledPromise = new Pending('autocomplete-start-editing');
        e.stopImmediatePropagation();
        e.preventDefault();
        var target = $(this),
            mainelement = target.closest('[data-inplaceeditable]');

        var addSpinner = function(element) {
            element.addClass('updating');
            var spinner = element.find('img.spinner');
            if (spinner.length) {
                spinner.show();
            } else {
                spinner = $('<img/>')
                        .attr('src', url.imageUrl('i/loading_small'))
                        .addClass('spinner').addClass('smallicon')
                    ;
                element.append(spinner);
            }
        };

        var removeSpinner = function(element) {
            element.removeClass('updating');
            element.find('img.spinner').hide();
        };

        var updateValue = function(mainelement, value) {
            var pendingId = [
                    mainelement.attr('data-itemid'),
                    mainelement.attr('data-component'),
                    mainelement.attr('data-itemtype'),
                ].join('-');
            var pendingPromise = new Pending(pendingId);

            addSpinner(mainelement);
            ajax.call([{
                methodname: 'core_update_inplace_editable',
                args: {
                    itemid: mainelement.attr('data-itemid'),
                    component: mainelement.attr('data-component'),
                    itemtype: mainelement.attr('data-itemtype'),
                    value: value,
                },
            }])[0]
            .then(function(data) {
                return templates.render('core/inplace_editable', data)
                .then(function(html, js) {
                    var oldvalue = mainelement.attr('data-value');
                    var newelement = $(html);
                    templates.replaceNode(mainelement, newelement, js);
                    newelement.find('[data-inplaceeditablelink]').focus();
                    newelement.trigger({
                        type: 'updated',
                        ajaxreturn: data,
                        oldvalue: oldvalue,
                    });

                    return;
                });
            })
            .then(function() {
                return pendingPromise.resolve();
            })
            .fail(function(ex) {
                var e = $.Event('updatefailed', {
                        exception: ex,
                        newvalue: value
                    });
                removeSpinner(mainelement);
                M.util.js_complete(pendingId);
                mainelement.trigger(e);
                if (!e.isDefaultPrevented()) {
                    notification.exception(ex);
                }
            });
        };

        var turnEditingOff = function(el) {
            el.find('input').off();
            el.find('select').off();
            el.html(el.attr('data-oldcontent'));
            el.removeAttr('data-oldcontent');
            el.removeClass('inplaceeditingon');
            el.find('[data-inplaceeditablelink]').focus();
        };

        var turnEditingOffEverywhere = function() {
            $('span.inplaceeditable.inplaceeditingon').each(function() {
                turnEditingOff($(this));
            });
        };

        var uniqueId = function(prefix, idlength) {
            var uniqid = prefix,
                i;
            for (i = 0; i < idlength; i++) {
                uniqid += String(Math.floor(Math.random() * 10));
            }
            // Make sure this ID is not already taken by an existing element.
            if ($("#" + uniqid).length === 0) {
                return uniqid;
            }
            return uniqueId(prefix, idlength);
        };

        var turnEditingOnText = function(el) {
            str.get_string('edittitleinstructions').done(function(s) {
                var instr = $('<span class="editinstructions">' + s + '</span>').
                        attr('id', uniqueId('id_editinstructions_', 20)),
                    inputelement = $('<input type="text"/>').
                        attr('id', uniqueId('id_inplacevalue_', 20)).
                        attr('value', el.attr('data-value')).
                        attr('aria-describedby', instr.attr('id')).
                        addClass('ignoredirty').
                        addClass('form-control'),
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
                        var val = inputelement.val();
                        turnEditingOff(el);
                        updateValue(el, val);
                    }
                    if ((e.type === 'keyup' && e.keyCode === 27) || e.type === 'focusout') {
                        // We need 'keyup' event for Escape because keypress does not work with Escape.
                        turnEditingOff(el);
                    }
                });
            });
        };

        var turnEditingOnToggle = function(el, newvalue) {
            turnEditingOff(el);
            updateValue(el, newvalue);
        };

        var turnEditingOnSelect = function(el, options) {
            var i,
                inputelement = $('<select></select>').
                    attr('id', uniqueId('id_inplacevalue_', 20)).
                    addClass('custom-select'),
                lbl = $('<label class="accesshide">' + mainelement.attr('data-editlabel') + '</label>')
                    .attr('for', inputelement.attr('id'));
            for (i in options) {
                inputelement
                    .append($('<option>')
                    .attr('value', options[i].key)
                    .html(options[i].value));
            }
            inputelement.val(el.attr('data-value'));

            el.html('')
                .append(lbl)
                .append(inputelement);

            inputelement.focus();
            inputelement.select();
            inputelement.on('keyup change focusout', function(e) {
                if (cfg.behatsiterunning && e.type === 'focusout') {
                    // Behat triggers focusout too often.
                    return;
                }
                if (e.type === 'change') {
                    var val = inputelement.val();
                    turnEditingOff(el);
                    updateValue(el, val);
                }
                if ((e.type === 'keyup' && e.keyCode === 27) || e.type === 'focusout') {
                    // We need 'keyup' event for Escape because keypress does not work with Escape.
                    turnEditingOff(el);
                }
            });
        };

        var turnEditingOnAutocomplete = function(el, args) {
            var i,
                inputelement = $('<select></select>').
                    attr('id', uniqueId('id_inplacevalue_', 20)).
                    addClass('form-autocomplete-original-select').
                    addClass('custom-select'),
                lbl = $('<label class="accesshide">' + mainelement.attr('data-editlabel') + '</label>')
                    .attr('for', inputelement.attr('id')),
                options = args.options,
                attributes = args.attributes,
                saveelement = $('<a href="#"></a>'),
                cancelelement = $('<a href="#"></a>');

            for (i in options) {
                inputelement
                    .append($('<option>')
                    .attr('value', options[i].key)
                    .html(options[i].value));
            }
            if (attributes.multiple) {
                inputelement.attr('multiple', 'true');
            }
            inputelement.val(JSON.parse(el.attr('data-value')));

            str.get_string('savechanges', 'core').then(function(s) {
                return templates.renderPix('e/save', 'core', s);
            }).then(function(html) {
                saveelement.append(html);
                return;
            }).fail(notification.exception);

            str.get_string('cancel', 'core').then(function(s) {
                return templates.renderPix('e/cancel', 'core', s);
            }).then(function(html) {
                cancelelement.append(html);
                return;
            }).fail(notification.exception);

            el.html('')
                .append(lbl)
                .append(inputelement)
                .append(saveelement)
                .append(cancelelement);

            inputelement.focus();
            inputelement.select();
            autocomplete.enhance(inputelement,
                                 attributes.tags,
                                 attributes.ajax,
                                 attributes.placeholder,
                                 attributes.caseSensitive,
                                 attributes.showSuggestions,
                                 attributes.noSelectionString)
                .then(function() {
                // Focus on the enhanced combobox.
                el.find('[role=combobox]').focus();
                // Stop eslint nagging.
                return;
            }).fail(notification.exception);

            inputelement.on('keyup', function(e) {
                if ((e.type === 'keyup' && e.keyCode === 27) || e.type === 'focusout') {
                    // We need 'keyup' event for Escape because keypress does not work with Escape.
                    turnEditingOff(el);
                }
            });
            saveelement.on('click', function(e) {
                var val = JSON.stringify(inputelement.val());
                // We need to empty the node to destroy all event handlers etc.
                inputelement.empty();
                turnEditingOff(el);
                updateValue(el, val);
                e.preventDefault();
            });
            cancelelement.on('click', function(e) {
                // We need to empty the node to destroy all event handlers etc.
                inputelement.empty();
                turnEditingOff(el);
                e.preventDefault();
            });
        };

        var turnEditingOn = function(el) {
            el.addClass('inplaceeditingon');
            el.attr('data-oldcontent', el.html());

            var type = el.attr('data-type');
            var options = el.attr('data-options');

            if (type === 'toggle') {
                turnEditingOnToggle(el, options);
            } else if (type === 'select') {
                turnEditingOnSelect(el, $.parseJSON(options));
            } else if (type === 'autocomplete') {
                turnEditingOnAutocomplete(el, $.parseJSON(options));
            } else {
                turnEditingOnText(el);
            }
        };

        // Turn editing on for the current element and register handler for Enter/Esc keys.
        turnEditingOffEverywhere();
        turnEditingOn(mainelement);
        editingEnabledPromise.resolve();

    });

    return {};
});
