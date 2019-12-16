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
define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/notification',
    'core/str',
    'core/modal_factory',
    'core/modal_events',
    'core/pending',
], function(
    $,
    ajax,
    templates,
    notification,
    str,
    ModalFactory,
    ModalEvents,
    Pending
) {
    return /** @alias module:core/tag */ {

        /**
         * Initialises tag index page.
         *
         * @method initTagindexPage
         */
        initTagindexPage: function() {
            // Click handler for changing tag type.
            $('body').delegate('.tagarea[data-ta] a[data-quickload=1]', 'click', function(e) {
                var pendingPromise = new Pending();

                e.preventDefault();
                var target = $(this);
                var query = target[0].search.replace(/^\?/, '');
                var tagarea = target.closest('.tagarea[data-ta]');
                var args = query.split('&').reduce(function(s, c) {
                      var t = c.split('=');
                      s[t[0]] = decodeURIComponent(t[1]);
                      return s;
                    }, {});

                ajax.call([{
                    methodname: 'core_tag_get_tagindex',
                    args: {tagindex: args}
                }])[0]
                .then(function(data) {
                    return templates.render('core_tag/index', data);
                })
                .then(function(html, js) {
                    templates.replaceNode(tagarea, html, js);
                    return;
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
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
                var pendingPromise = new Pending('core/tag:initManagePage');

                str.get_strings([
                    {
                        key: 'selecttag',
                        component: 'core_tag',
                    },
                    {
                        key: 'now',
                        component: 'core',
                    },
                ])
                .then(function(result) {
                    $('label[for="tagselect' + e.ajaxreturn.itemid + '"]').html(result[0]);
                    $(e.target).closest('tr').find('td.col-timemodified').html(result[1]);

                    return;
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);

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
                var pendingPromise = new Pending('core/tag:tagdelete');

                e.preventDefault();
                var href = $(this).attr('href');
                str.get_strings([
                        {key: 'delete', component: 'core'},
                        {key: 'confirmdeletetag', component: 'tag'},
                        {key: 'yes', component: 'core'},
                        {key: 'no', component: 'core'},
                ])
                .then(function(s) {
                    return notification.confirm(s[0], s[1], s[2], s[3], function() {
                        window.location.href = href;
                    });
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });

            // Confirmation for bulk tag delete button.
            $("#tag-management-delete").click(function(e) {
                var form = $(this).closest('form').get(0);

                var cnt = $(form).find("input[type=checkbox]:checked").length;
                if (!cnt) {
                    return;
                }

                var pendingPromise = new Pending('core/tag:tag-management-delete');
                var tempElement = $("<input type='hidden'/>").attr('name', this.name);
                e.preventDefault();
                str.get_strings([
                    {key: 'delete'},
                    {key: 'confirmdeletetags', component: 'tag'},
                    {key: 'yes'},
                    {key: 'no'},
                ])
                .then(function(s) {
                    return notification.confirm(s[0], s[1], s[2], s[3], function() {
                        tempElement.appendTo(form);
                        form.submit();
                    });
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });

            // Confirmation for bulk tag combine button.
            $("#tag-management-combine").click(function(e) {
                var pendingPromise = new Pending('core/tag:tag-management-combine');

                e.preventDefault();
                var form = $(this).closest('form').get(0);
                var tags = $(form).find("input[type=checkbox]:checked");

                if (tags.length <= 1) {
                    str.get_strings([
                        {key: 'combineselected', component: 'tag'},
                        {key: 'selectmultipletags', component: 'tag'},
                        {key: 'ok'},
                    ])
                    .then(function(s) {
                        return notification.alert(s[0], s[1], s[2]);
                    })
                    .then(pendingPromise.resolve)
                    .catch(notification.exception);

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
                ])
                .then(function(langStrings) {
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
                })
                .then(function(modal) {
                    modal.setSaveButtonText(saveButtonText);

                    return modal;
                })
                .then(function(modal) {

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

                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });

            // When user changes tag name to some name that already exists suggest to combine the tags.
            $('body').on('updatefailed', '[data-inplaceeditable][data-itemtype=tagname]', function(e) {
                var pendingPromise = new Pending('core/tag:tag-management-combine-exists');

                var exception = e.exception; // The exception object returned by the callback.
                var newvalue = e.newvalue; // The value that user tried to udpated the element to.
                var tagid = $(e.target).attr('data-itemid');
                if (exception.errorcode === 'namesalreadybeeingused') {
                    e.preventDefault(); // This will prevent default error dialogue.
                    str.get_strings([
                        {key: 'nameuseddocombine', component: 'tag'},
                        {key: 'yes'},
                        {key: 'cancel'},
                    ])
                    .then(function(s) {
                        return notification.confirm(e.message, s[0], s[1], s[2], function() {
                            window.location.href = window.location.href + "&newname=" + encodeURIComponent(newvalue) +
                                "&tagid=" + encodeURIComponent(tagid) +
                                '&action=renamecombine&sesskey=' + M.cfg.sesskey;
                        });
                    })
                    .then(pendingPromise.resolve)
                    .catch(notification.exception);
                }
            });

            // Form for adding standard tags.
            $('body').on('click', 'a[data-action=addstandardtag]', function(e) {
                var pendingPromise = new Pending();
                e.preventDefault();

                return ModalFactory.create({
                    title: str.get_string('addotags', 'tag'),
                    body: templates.render('core_tag/add_tags', {
                        actionurl: window.location.href,
                        sesskey: M.cfg.sesskey
                    }),
                    type: ModalFactory.types.SAVE_CANCEL
                })
                .then(function(modal) {
                    modal.setSaveButtonText(str.get_string('continue', 'core'));

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

                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });
        },

        /**
         * Initialises tag collection management page.
         *
         * @method initManageCollectionsPage
         */
        initManageCollectionsPage: function() {
            $('body').on('updated', '[data-inplaceeditable]', function(e) {
                var pendingPromise = new Pending('core/tag:initManageCollectionsPage-updated');

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

                pendingPromise.resolve();
            });

            $('body').on('click', '.addtagcoll > a', function(e) {
                var pendingPromise = new Pending('core/tag:initManageCollectionsPage-addtagcoll');

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
                str.get_strings(keys)
                .then(function(langStrings) {
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
                })
                .then(function(modal) {
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

                    return modal;

                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });

            $('body').on('click', '.tag-collections-table .action_delete', function(e) {
                var pendingPromise = new Pending('core/tag:initManageCollectionsPage-action_delete');

                e.preventDefault();
                var href = $(this).attr('data-url') + '&sesskey=' + M.cfg.sesskey;
                str.get_strings([
                    {key: 'delete'},
                    {key: 'suredeletecoll', component: 'tag', param: $(this).attr('data-collname')},
                    {key: 'yes'},
                    {key: 'no'},
                ])
                .then(function(s) {
                    return notification.confirm(s[0], s[1], s[2], s[3], function() {
                        window.location.href = href;
                    });
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });
        }
    };
});
