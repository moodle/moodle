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
 * AJAX helper for the tag management page.
 *
 * @module     core/tag
 * @package    core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/config'],
        function($, ajax, templates, notification, str, cfg) {
    return /** @alias module:core/tag */ {

        /**
         * Initialises handlers for AJAX methods.
         *
         * @method init
         */
        init_manage_page: function() {

            var update_modified = function(el) {
                var row = el.closest('tr').get(0);
                if (row) {
                    var td = $(row).find('td.col-timemodified').get(0);
                    str.get_string('now').done(function(s) {
                        $(td).html(s);
                    });
                }
            };

            // Click handler for changing tag type.
            $('.tag-management-table').delegate('.tagtype', 'click', function(e) {
                e.preventDefault();
                var target = $( this ),
                    tagid = target.attr('data-id'),
                    currentvalue = target.attr('data-value'),
                    official = (currentvalue === "1") ? 0 : 1;

                var promises = ajax.call([{
                    methodname: 'core_tag_update_tags',
                    args: { tags : [ { id : tagid , official : official } ] }
                }, {
                    methodname: 'core_tag_get_tags',
                    args: { tags : [ { id : tagid } ] }
                }], true);

                $.when.apply($, promises)
                    .done( function(updateresult, data) {
                        if (updateresult.warnings[0] === undefined && data.tags[0] !== undefined) {
                            templates.render('core_tag/tagtype', data.tags[0]).done(function(html) {
                                update_modified(target);
                                var parent = target.parent();
                                target.replaceWith(html);
                                parent.find('.tagtype').get(0).focus();
                            });
                        }
                    });
            });

            // Click handler for flagging/resetting tag flag.
            $('.tag-management-table').delegate('.tagflag', 'click', function(e) {
                e.preventDefault();
                var target = $( this ),
                    tagid = target.attr('data-id'),
                    currentvalue = target.attr('data-value'),
                    flag = (currentvalue === "0") ? 1 : 0;

                var promises = ajax.call([{
                    methodname: 'core_tag_update_tags',
                    args: { tags : [ { id : tagid , flag : flag } ] }
                }, {
                    methodname: 'core_tag_get_tags',
                    args: { tags : [ { id : tagid } ] }
                }], true);

                $.when.apply($, promises)
                    .done( function(updateresult, data) {
                        if (updateresult.warnings[0] === undefined && data.tags[0] !== undefined) {
                            var row = target.closest('tr').get(0);
                            if (row) {
                                if (data.tags[0].flag) {
                                    $(row).addClass('flagged-tag');
                                } else {
                                    $(row).removeClass('flagged-tag');
                                }
                            }
                            templates.render('core_tag/tagflag', data.tags[0]).done(function(html) {
                                update_modified(target);
                                var parent = target.parent();
                                target.replaceWith(html);
                                parent.find('.tagflag').get(0).focus();
                            });
                        }
                    });
            });

            // Confirmation for single tag delete link.
            $('.tag-management-table').delegate('a.tagdelete', 'click', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                str.get_strings([
                        {key : 'delete'},
                        {key : 'confirmdeletetag', component : 'tag'},
                        {key : 'yes'},
                        {key : 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            window.location.href = href;
                        });
                    }
                );
            });

            // Confirmation for bulk tag delete button.
            $("#tag-management-delete").click(function(e){
                var form = $(this).closest('form').get(0),
                    cnt = $(form).find("input[type=checkbox]:checked").length;
                if (!cnt) {
                    return false;
                }
                e.preventDefault();
                str.get_strings([
                        {key : 'delete'},
                        {key : 'confirmdeletetags', component : 'tag'},
                        {key : 'yes'},
                        {key : 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            form.submit();
                        });
                    }
                );
            });

            // Edit tag name.
            $('.tag-management-table').delegate('.tagnameedit', 'click keypress', function(e) {
                if (e.type === 'keypress' && e.keyCode !== 13) {
                    return;
                }
                e.stopImmediatePropagation();
                e.preventDefault();
                var target = $(this),
                    tdelement = $( target.closest('td').get(0) ),
                    inputelement = $( tdelement.find('input').get(0) ),
                    tagid = target.attr('data-id');

                var change_name = function(tagid, newname) {
                    var promises = ajax.call([{
                        methodname: 'core_tag_update_tags',
                        args: { tags : [ { id : tagid , rawname : newname } ] }
                    }, {
                        methodname: 'core_tag_get_tags',
                        args: { tags : [ { id : tagid } ] }
                    }], true);

                    $.when.apply($, promises)
                        .done( function(updateresult, data) {
                            if (updateresult.warnings[0] !== undefined) {
                                str.get_string('error').done(function(s) {
                                    notification.alert(s, updateresult.warnings[0].message);
                                });
                            } else if (data.tags[0] !== undefined) {
                                templates.render('core_tag/tagname', data.tags[0]).done(function(html) {
                                    update_modified(tdelement);
                                    tdelement.html(html);
                                    $(tdelement.find('.tagnameedit').get(0)).focus();
                                });
                            }
                        });
                };

                var turn_editing_off = function() {
                    $('.tag-management-table td.tageditingon').each(function() {
                        var td = $( this ),
                            input = $( td.find('input').get(0) );
                        input.off();
                        td.removeClass('tageditingon');
                        // Reset input value to the one that was there before editing.
                        input.val(td.attr('data-value'));
                    });
                };

                // Turn editing on for the current element and register handler for Enter/Esc keys.
                turn_editing_off();
                tdelement.addClass('tageditingon');
                tdelement.attr('data-value', inputelement.val());
                inputelement.select();
                inputelement.on('keypress focusout', function(e) {
                    if (cfg.behatsiterunning && e.type === 'focusout') {
                        // Behat triggers focusout too often.
                        return;
                    }
                    if (e.type === 'keypress' && e.keyCode === 13) {
                        change_name(tagid, inputelement.val());
                        turn_editing_off();
                    }
                    if ((e.type === 'keypress' && e.keyCode === 27) || e.type === 'focusout') {
                        turn_editing_off();
                    }
                });
            });
        }
    };
});