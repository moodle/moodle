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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str'],
        function($, ajax, templates, notification, str) {
    return /** @alias module:core/tag */ {

        /**
         * Initialises tag index page.
         *
         * @method initTagindexPage
         */
        initTagindexPage: function() {
            // Click handler for changing tag type.
            $('body').delegate('.tagarea[data-ta] a[data-quickload=1]', 'click', function(e) {
                e.preventDefault();
                var target = $( this ),
                    query = target.context.search.replace(/^\?/, ''),
                    tagarea = target.closest('.tagarea[data-ta]'),
                    args = query.split('&').reduce(function(s,c){var t=c.split('=');s[t[0]]=decodeURIComponent(t[1]);return s;},{});

                var promises = ajax.call([{
                    methodname: 'core_tag_get_tagindex',
                    args: { tagindex: args }
                }], true);

                $.when.apply($, promises)
                    .done( function(data) {
                        templates.render('core_tag/index', data).done(function(html) {
                            tagarea.replaceWith(html);
                        });
                    });
            });
        },

        /**
         * Initialises tag management page.
         *
         * @method initManagePage
         */
        initManagePage: function() {

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
            $('.tag-management-table').delegate('.tagisstandard', 'click', function(e) {
                e.preventDefault();
                var target = $( this ),
                    tagid = target.attr('data-id'),
                    currentvalue = target.attr('data-value'),
                    isstandard = (currentvalue === "1") ? 0 : 1;

                var promises = ajax.call([{
                    methodname: 'core_tag_update_tags',
                    args: { tags : [ { id : tagid , isstandard : isstandard } ] }
                }, {
                    methodname: 'core_tag_get_tags',
                    args: { tags : [ { id : tagid } ] }
                }], true);

                $.when.apply($, promises)
                    .done( function(updateresult, data) {
                        if (updateresult.warnings[0] === undefined && data.tags[0] !== undefined) {
                            templates.render('core_tag/tagisstandard', data.tags[0]).done(function(html) {
                                update_modified(target);
                                var parent = target.parent();
                                target.replaceWith(html);
                                parent.find('.tagisstandard').get(0).focus();
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
        },

        /**
         * Initialises tag collection management page.
         *
         * @method initManageCollectionsPage
         */
        initManageCollectionsPage: function() {
            $('body').on('updated', '[data-inplaceeditable]', function(e) {
                var ajaxreturn = e.ajaxreturn,
                    oldvalue = e.oldvalue,
                    areaid, collid, isenabled;
                if (ajaxreturn.component === 'core_tag' && ajaxreturn.itemtype === 'tagareaenable') {
                    areaid = $(this).attr('data-itemid');
                    $(".tag-collections-table ul[data-collectionid] li[data-areaid="+areaid+"]").addClass('hidden');
                    isenabled = ajaxreturn.value;
                    if (isenabled === '1') {
                        $(this).closest('tr').removeClass('dimmed_text');
                        collid = $(this).closest('tr').find('[data-itemtype="tagareacollection"]').attr("data-value");
                        $(".tag-collections-table ul[data-collectionid="+collid+"] li[data-areaid="+areaid+"]").removeClass('hidden');
                    } else {
                        $(this).closest('tr').addClass('dimmed_text');
                    }
                }
                if (ajaxreturn.component === 'core_tag' && ajaxreturn.itemtype === 'tagareacollection') {
                    areaid = $(this).attr('data-itemid');
                    $(".tag-collections-table ul[data-collectionid] li[data-areaid="+areaid+"]").addClass('hidden');
                    collid = $(this).attr('data-value');
                    isenabled = $(this).closest('tr').find('[data-itemtype="tagareaenable"]').attr("data-value");
                    if (isenabled === "1") {
                        $(".tag-collections-table ul[data-collectionid="+collid+"] li[data-areaid="+areaid+"]").removeClass('hidden');
                    }
                }
            });

            $('body').on('click', '.addtagcoll > a', function(e) {
                e.preventDefault();
                var href = $(this).attr('data-url') + '&sesskey=' + M.cfg.sesskey;
                str.get_strings([
                        {key : 'addtagcoll', component : 'tag'},
                        {key : 'name'},
                        {key : 'searchable', component : 'tag'},
                        {key : 'create'},
                        {key : 'cancel'},
                    ]).done(function(s) {
                        var el = $('<div><form id="addtagcoll_form" class="form-inline">'+
                            '<p><label for="addtagcoll_name"></label>: ' +
                            '<input id="addtagcoll_name" type="text"/><br/>' +
                            '<label for="addtagcoll_searchable"></label>: ' +
                            '<input id="addtagcoll_searchable" type="checkbox" value="1" checked/></p>' +
                            '<p class="mdl-align"><input type="submit" id="addtagcoll_submit"/>'+
                            '<input type="button" id="addtagcoll_cancel"/></p>' +
                            '</form></div>');
                        el.find('label[for="addtagcoll_name"]').html(s[1]);
                        el.find('label[for="addtagcoll_searchable"]').html(s[2]);
                        el.find('#addtagcoll_submit').attr('value', s[3]);
                        el.find('#addtagcoll_cancel').attr('value', s[4]);
                        var panel = new M.core.dialogue ({
                            draggable: true,
                            modal: true,
                            closeButton: true,
                            headerContent: s[0],
                            bodyContent: el.html()
                        });
                        panel.show();
                        $('#addtagcoll_form #addtagcoll_name').focus();
                        $('#addtagcoll_form #addtagcoll_cancel').on('click', function() {
                            panel.destroy();
                        });
                        $('#addtagcoll_form').on('submit', function() {
                            var name = $('#addtagcoll_form #addtagcoll_name').val();
                            var searchable = $('#addtagcoll_form #addtagcoll_searchable').prop('checked') ? 1 : 0;
                            if (String(name).length > 0) {
                                window.location.href = href + "&name=" + encodeURIComponent(name) + "&searchable=" + searchable;
                            }
                            return false;
                        });
                    }
                );

            });

            $('body').on('click', '.tag-collections-table .action_delete', function(e) {
                e.preventDefault();
                var href = $(this).attr('data-url') + '&sesskey=' + M.cfg.sesskey;
                str.get_strings([
                        {key : 'delete'},
                        {key : 'suredeletecoll', component : 'tag', param : $(this).attr('data-collname') },
                        {key : 'yes'},
                        {key : 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            window.location.href = href;
                        });
                    }
                );
            });
        }
    };
});