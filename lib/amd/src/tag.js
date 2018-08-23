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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/modal_factory', 'core/modal_events'],
        function($, ajax, templates, notification, str, ModalFactory, ModalEvents) {
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
                var target = $(this),
                    query = target[0].search.replace(/^\?/, ''),
                    tagarea = target.closest('.tagarea[data-ta]'),
                    args = query.split('&').reduce(function(s, c) {
                      var t = c.split('=');
                      s[t[0]] = decodeURIComponent(t[1]);
                      return s;
                    }, {});

                var promises = ajax.call([{
                    methodname: 'core_tag_get_tagindex',
                    args: {tagindex: args}
                }], true);

                $.when.apply($, promises)
                    .done(function(data) {
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

            // Set cell 'time modified' to 'now' when any of the element is updated in this row.
            $('body').on('updated', '[data-inplaceeditable]', function(e) {
                str.get_string('selecttag', 'core_tag', e.ajaxreturn.value)
                    .then(function(s) {
                        return $('label[for="tagselect' + e.ajaxreturn.itemid + '"]').html(s);
                    })
                    .fail(notification.exception);
                str.get_string('now').done(function(s) {
                    $(e.target).closest('tr').find('td.col-timemodified').html(s);
                });
                if (e.ajaxreturn.itemtype === 'tagflag') {
                    var row = $(e.target).closest('tr');
                    if (e.ajaxreturn.value === '0') {
                        row.removeClass('flagged-tag');
                    } else {
                        row.addClass('flagged-tag');
                    }
                }
            });

            // Confirmation for single tag delete link.
            $('.tag-management-table').delegate('a.tagdelete', 'click', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                str.get_strings([
                        {key: 'delete'},
                        {key: 'confirmdeletetag', component: 'tag'},
                        {key: 'yes'},
                        {key: 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            window.location.href = href;
                        });
                    }
                );
            });

            // Confirmation for bulk tag delete button.
            $("#tag-management-delete").click(function(e) {
                var form = $(this).closest('form').get(0),
                    cnt = $(form).find("input[type=checkbox]:checked").length;
                if (!cnt) {
                    return;
                }
                var tempElement = $("<input type='hidden'/>").attr('name', this.name);
                e.preventDefault();
                str.get_strings([
                        {key: 'delete'},
                        {key: 'confirmdeletetags', component: 'tag'},
                        {key: 'yes'},
                        {key: 'no'},
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], function() {
                            tempElement.appendTo(form);
                            form.submit();
                        });
                    }
                );
            });

            // Confirmation for bulk tag combine button.
            $("#tag-management-combine").click(function(e) {
                e.preventDefault();
                var form = $(this).closest('form').get(0),
                    tags = $(form).find("input[type=checkbox]:checked");
                if (tags.length <= 1) {
                    str.get_strings([
                        {key: 'combineselected', component: 'tag'},
                        {key: 'selectmultipletags', component: 'tag'},
                        {key: 'ok'},
                    ]).done(function(s) {
                            notification.alert(s[0], s[1], s[2]);
                        }
                    );
                    return;
                }
                var tempElement = $("<input type='hidden'/>").attr('name', this.name);
                var saveButtonText = '';
                var tagOptions = [];
                tags.each(function() {
                    var tagid = $(this).val(),
                        tagname = $('.inplaceeditable[data-itemtype=tagname][data-itemid=' + tagid + ']').attr('data-value');
                    tagOptions.push({
                        id: tagid,
                        name: tagname
                    });
                });

                str.get_strings([
                    {key: 'combineselected', component: 'tag'},
                    {key: 'continue'}
                ]).then(function(langStrings) {
                    var modalTitle = langStrings[0];
                    saveButtonText = langStrings[1];
                    var templateContext = {
                        tags: tagOptions
                    };
                    return ModalFactory.create({
                        title: modalTitle,
                        body: templates.render('core_tag/combine_tags', templateContext),
                        type: ModalFactory.types.SAVE_CANCEL
                    });
                }).then(function(modal) {
                    modal.setSaveButtonText(saveButtonText);

                    // Handle save event.
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        e.preventDefault();

                        // Append this temp element in the form in the tags list, not the form in the modal. Confusing, right?!?
                        tempElement.appendTo(form);
                        // Get the selected tag from the modal.
                        var maintag = $('input[name=maintag]:checked', '#combinetags_form').val();
                        // Append this in the tags list form.
                        $("<input type='hidden'/>").attr('name', 'maintag').attr('value', maintag).appendTo(form);
                        // Submit the tags list form.
                        form.submit();
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        // Destroy when hidden.
                        modal.destroy();
                    });

                    modal.show();
                    // Tick the first option.
                    $('#combinetags_form input[type=radio]').first().focus().prop('checked', true);

                    return;

                }).catch(notification.exception);
            });

            // When user changes tag name to some name that already exists suggest to combine the tags.
            $('body').on('updatefailed', '[data-inplaceeditable][data-itemtype=tagname]', function(e) {
                var exception = e.exception; // The exception object returned by the callback.
                var newvalue = e.newvalue; // The value that user tried to udpated the element to.
                var tagid = $(e.target).attr('data-itemid');
                if (exception.errorcode === 'namesalreadybeeingused') {
                    e.preventDefault(); // This will prevent default error dialogue.
                    str.get_strings([
                        {key: 'nameuseddocombine', component: 'tag'},
                        {key: 'yes'},
                        {key: 'cancel'},
                    ]).done(function(s) {
                        notification.confirm(e.message, s[0], s[1], s[2], function() {
                            window.location.href = window.location.href + "&newname=" + encodeURIComponent(newvalue) +
                                "&tagid=" + encodeURIComponent(tagid) +
                                '&action=renamecombine&sesskey=' + M.cfg.sesskey;
                        });
                    });
                }
            });

            // Form for adding standard tags.
            $('body').on('click', 'a[data-action=addstandardtag]', function(e) {
                e.preventDefault();

                var saveButtonText = '';
                str.get_strings([
                    {key: 'addotags', component: 'tag'},
                    {key: 'continue'}
                ]).then(function(langStrings) {
                    var modalTitle = langStrings[0];
                    saveButtonText = langStrings[1];
                    var templateContext = {
                        actionurl: window.location.href,
                        sesskey: M.cfg.sesskey
                    };
                    return ModalFactory.create({
                        title: modalTitle,
                        body: templates.render('core_tag/add_tags', templateContext),
                        type: ModalFactory.types.SAVE_CANCEL
                    });
                }).then(function(modal) {
                    modal.setSaveButtonText(saveButtonText);

                    // Handle save event.
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        var tagsInput = $(e.currentTarget).find('#id_tagslist');
                        var name = tagsInput.val().trim();

                        // Set the text field's value to the trimmed value.
                        tagsInput.val(name);

                        // Add submit event listener to the form.
                        var tagsForm = $('#addtags_form');
                        tagsForm.on('submit', function(e) {
                            // Validate the form.
                            var form = $('#addtags_form');
                            if (form[0].checkValidity() === false) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                            form.addClass('was-validated');

                            // BS2 compatibility.
                            $('[data-region="tagslistinput"]').addClass('error');
                            var errorMessage = $('#id_tagslist_error_message');
                            errorMessage.removeAttr('hidden');
                            errorMessage.addClass('help-block');
                        });

                        // Try to submit the form.
                        tagsForm.submit();

                        return false;
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        // Destroy when hidden.
                        modal.destroy();
                    });

                    modal.show();

                    return;

                }).catch(notification.exception);
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
                    areaid, collid, isenabled;
                if (ajaxreturn.component === 'core_tag' && ajaxreturn.itemtype === 'tagareaenable') {
                    areaid = $(this).attr('data-itemid');
                    $(".tag-collections-table ul[data-collectionid] li[data-areaid=" + areaid + "]").hide();
                    isenabled = ajaxreturn.value;
                    if (isenabled === '1') {
                        $(this).closest('tr').removeClass('dimmed_text');
                        collid = $(this).closest('tr').find('[data-itemtype="tagareacollection"]').attr("data-value");
                        $(".tag-collections-table ul[data-collectionid=" + collid + "] li[data-areaid=" + areaid + "]").show();
                    } else {
                        $(this).closest('tr').addClass('dimmed_text');
                    }
                }
                if (ajaxreturn.component === 'core_tag' && ajaxreturn.itemtype === 'tagareacollection') {
                    areaid = $(this).attr('data-itemid');
                    $(".tag-collections-table ul[data-collectionid] li[data-areaid=" + areaid + "]").hide();
                    collid = $(this).attr('data-value');
                    isenabled = $(this).closest('tr').find('[data-itemtype="tagareaenable"]').attr("data-value");
                    if (isenabled === "1") {
                        $(".tag-collections-table ul[data-collectionid=" + collid + "] li[data-areaid=" + areaid + "]").show();
                    }
                }
            });

            $('body').on('click', '.addtagcoll > a', function(e) {
                e.preventDefault();
                var keys = [
                    {
                        key: 'addtagcoll',
                        component: 'tag'
                    },
                    {
                        key: 'create'
                    }
                ];

                var href = $(this).attr('data-url');
                var saveButtonText = '';
                str.get_strings(keys).then(function(langStrings) {
                    var modalTitle = langStrings[0];
                    saveButtonText = langStrings[1];
                    var templateContext = {
                        actionurl: href,
                        sesskey: M.cfg.sesskey
                    };
                    return ModalFactory.create({
                        title: modalTitle,
                        body: templates.render('core_tag/add_tag_collection', templateContext),
                        type: ModalFactory.types.SAVE_CANCEL
                    });
                }).then(function(modal) {
                    modal.setSaveButtonText(saveButtonText);

                    // Handle save event.
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        var collectionInput = $(e.currentTarget).find('#addtagcoll_name');
                        var name = collectionInput.val().trim();
                        // Set the text field's value to the trimmed value.
                        collectionInput.val(name);

                        // Add submit event listener to the form.
                        var form = $('#addtagcoll_form');
                        form.on('submit', function(e) {
                            // Validate the form.
                            if (form[0].checkValidity() === false) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                            form.addClass('was-validated');

                            // BS2 compatibility.
                            $('[data-region="addtagcoll_nameinput"]').addClass('error');
                            var errorMessage = $('#id_addtagcoll_name_error_message');
                            errorMessage.removeAttr('hidden');
                            errorMessage.addClass('help-block');
                        });

                        // Try to submit the form.
                        form.submit();

                        return false;
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        // Destroy when hidden.
                        modal.destroy();
                    });

                    modal.show();

                    return;

                }).catch(notification.exception);
            });

            $('body').on('click', '.tag-collections-table .action_delete', function(e) {
                e.preventDefault();
                var href = $(this).attr('data-url') + '&sesskey=' + M.cfg.sesskey;
                str.get_strings([
                        {key: 'delete'},
                        {key: 'suredeletecoll', component: 'tag', param: $(this).attr('data-collname')},
                        {key: 'yes'},
                        {key: 'no'},
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
