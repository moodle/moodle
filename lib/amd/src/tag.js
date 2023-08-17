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
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */

import $ from 'jquery';
import {call as fetchMany} from 'core/ajax';
import * as Notification from 'core/notification';
import * as Templates from 'core/templates';
import {
    get_string as getString,
    get_strings as getStrings,
} from 'core/str';
import * as ModalEvents from 'core/modal_events';
import Pending from 'core/pending';
import SaveCancelModal from 'core/modal_save_cancel';
import Config from 'core/config';

const getTagIndex = (tagindex) => fetchMany([{
    methodname: 'core_tag_get_tagindex',
    args: {tagindex}
}])[0];

const getCheckedTags = (form) => form.querySelectorAll('input[data-togglegroup="tags-manage"][data-toggle="slave"]:checked');

const handleCombineRequest = async(tagManagementCombine) => {
    const pendingPromise = new Pending('core/tag:tag-management-combine');
    const form = tagManagementCombine.closest('form');
    const checkedTags = getCheckedTags(form);

    if (checkedTags.length <= 1) {
        // We need at least 2 tags to combine them.
        Notification.alert(
            getString('combineselected', 'tag'),
            getString('selectmultipletags', 'tag'),
            getString('ok'),
        );

        return;
    }

    const tags = Array.from(checkedTags.values()).map((tag) => {
        const namedElement = document.querySelector(`.inplaceeditable[data-itemtype=tagname][data-itemid="${tag.value}"]`);
        return {
            id: tag.value,
            name: namedElement.dataset.value,
        };
    });

    const modal = await SaveCancelModal.create({
        title: getString('combineselected', 'tag'),
        buttons: {
            save: getString('continue', 'core'),
        },
        body: Templates.render('core_tag/combine_tags', {tags}),
        show: true,
        removeOnClose: true,
    });

    // Handle save event.
    modal.getRoot().on(ModalEvents.save, (e) => {
        e.preventDefault();

        // Append this temp element in the form in the tags list, not the form in the modal. Confusing, right?!?
        const tempElement = document.createElement('input');
        tempElement.hidden = true;
        tempElement.name = tagManagementCombine.name;
        form.append(tempElement);

        // Get the selected tag from the modal.
        var maintag = $('input[name=maintag]:checked', '#combinetags_form').val();
        // Append this in the tags list form.
        $("<input type='hidden'/>").attr('name', 'maintag').attr('value', maintag).appendTo(form);
        // Submit the tags list form.
        form.submit();
    });

    await modal.getBodyPromise();
    // Tick the first option.
    const firstOption = document.querySelector('#combinetags_form input[type=radio]');
    firstOption.focus();
    firstOption.checked = true;

    pendingPromise.resolve();

    return;
};

const addStandardTags = async() => {
    var pendingPromise = new Pending('core/tag:addstandardtag');

    const modal = await SaveCancelModal.create({
        title: getString('addotags', 'tag'),
        body: Templates.render('core_tag/add_tags', {
            actionurl: window.location.href,
            sesskey: M.cfg.sesskey,
        }),
        buttons: {
            save: getString('continue', 'core'),
        },
        removeOnClose: true,
        show: true,
    });

    // Handle save event.
    modal.getRoot().on(ModalEvents.save, (e) => {
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

    await modal.getBodyPromise();
    pendingPromise.resolve();
};

const deleteSelectedTags = async(form) => {
    const checkedTags = getCheckedTags(form);
    if (!checkedTags.length) {
        return;
    }

    try {
        await Notification.saveCancelPromise(
            getString('delete'),
            getString('confirmdeletetags', 'tag'),
            getString('yes'),
            getString('no'),
        );

        // Append this temp element in the form in the tags list, not the form in the modal. Confusing, right?!?
        const tempElement = document.createElement('input');
        tempElement.hidden = true;
        tempElement.name = 'bulkdelete';
        form.append(tempElement);
        form.submit();
    } catch {
        return;
    }
};

const deleteSelectedTag = async(button) => {
    try {
        await Notification.saveCancelPromise(
            getString('delete'),
            getString('confirmdeletetag', 'tag'),
            getString('yes'),
            getString('no'),
        );

        window.location.href = button.href;
    } catch {
        return;
    }
};

const deleteSelectedCollection = async(button) => {
    try {
        await Notification.saveCancelPromise(
            getString('delete'),
            getString('suredeletecoll', 'tag', button.dataset.collname),
            getString('yes'),
            getString('no'),
        );

        const redirectTarget = new URL(button.dataset.url);
        redirectTarget.searchParams.set('sesskey', Config.sesskey);
        window.location.href = redirectTarget;
    } catch {
        return;
    }
};

const addTagCollection = async(link) => {
    const pendingPromise = new Pending('core/tag:initManageCollectionsPage-addtagcoll');
    const href = link.dataset.url;

    const modal = await SaveCancelModal.create({
        title: getString('addtagcoll', 'tag'),
        buttons: {
            save: getString('create', 'core'),
        },
        body: Templates.render('core_tag/add_tag_collection', {
            actionurl: href,
            sesskey: M.cfg.sesskey,
        }),
        removeOnClose: true,
        show: true,
    });

    // Handle save event.
    modal.getRoot().on(ModalEvents.save, (e) => {
        const collectionInput = $(e.currentTarget).find('#addtagcoll_name');
        const name = collectionInput.val().trim();
        // Set the text field's value to the trimmed value.
        collectionInput.val(name);

        // Add submit event listener to the form.
        const form = $('#addtagcoll_form');
        form.on('submit', function(e) {
            // Validate the form.
            if (form[0].checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.addClass('was-validated');

            // BS2 compatibility.
            $('[data-region="addtagcoll_nameinput"]').addClass('error');
            const errorMessage = $('#id_addtagcoll_name_error_message');
            errorMessage.removeAttr('hidden');
            errorMessage.addClass('help-block');
        });

        // Try to submit the form.
        form.submit();

        return false;
    });

    pendingPromise.resolve();
};

/**
 * Initialises tag index page.
 *
 * @method initTagindexPage
 */
export const initTagindexPage = async() => {
    document.addEventListener('click', async(e) => {
        const targetArea = e.target.closest('a[data-quickload="1"]');
        if (!targetArea) {
            return;
        }
        const tagArea = targetArea.closest('.tagarea[data-ta]');
        if (!tagArea) {
            return;
        }

        e.preventDefault();
        const pendingPromise = new Pending('core/tag:initTagindexPage');

        const query = targetArea.search.replace(/^\?/, '');
        const params = Object.fromEntries((new URLSearchParams(query)).entries());

        try {
            const data = await getTagIndex(params);
            const {html, js} = await Templates.renderForPromise('core_tag/index', data);
            Templates.replaceNode(tagArea, html, js);
        } catch (error) {
            Notification.exception(error);
        }
        pendingPromise.resolve();
    });
};

/**
 * Initialises tag management page.
 *
 * @method initManagePage
 */
export const initManagePage = () => {
    // Set cell 'time modified' to 'now' when any of the element is updated in this row.
    $('body').on('updated', '[data-inplaceeditable]', function(e) {
        var pendingPromise = new Pending('core/tag:initManagePage');

        getStrings([
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
        .always(pendingPromise.resolve)
        .catch(Notification.exception);

        if (e.ajaxreturn.itemtype === 'tagflag') {
            var row = $(e.target).closest('tr');
            if (e.ajaxreturn.value === '0') {
                row.removeClass('table-warning');
            } else {
                row.addClass('table-warning');
            }
        }
    });

    // Confirmation for bulk tag combine button.
    document.addEventListener('click', async(e) => {
        const tagManagementCombine = e.target.closest('#tag-management-combine');
        if (tagManagementCombine) {
            e.preventDefault();
            handleCombineRequest(tagManagementCombine);
        }

        if (e.target.closest('a[data-action="addstandardtag"]')) {
            e.preventDefault();
            addStandardTags();
        }

        const bulkActionDeleteButton = e.target.closest('#tag-management-delete');
        if (bulkActionDeleteButton) {
            e.preventDefault();
            deleteSelectedTags(bulkActionDeleteButton.closest('form'));
        }

        const rowDeleteButton = e.target.closest('.tagdelete');
        if (rowDeleteButton) {
            e.preventDefault();
            deleteSelectedTag(rowDeleteButton);
        }
    });

    // When user changes tag name to some name that already exists suggest to combine the tags.
    $('body').on('updatefailed', '[data-inplaceeditable][data-itemtype=tagname]', async(e) => {
        var exception = e.exception; // The exception object returned by the callback.
        var newvalue = e.newvalue; // The value that user tried to udpated the element to.
        var tagid = $(e.target).attr('data-itemid');
        if (exception.errorcode !== 'namesalreadybeeingused') {
            return;
        }
        e.preventDefault(); // This will prevent default error dialogue.

        try {
            await Notification.saveCancelPromise(
                getString('confirm'),
                getString('nameuseddocombine', 'tag'),
                getString('yes'),
                getString('cancel'),
            );

            // The Promise will resolve on 'Yes' button, and reject on 'Cancel' button.
            const redirectTarget = new URL(window.location);
            redirectTarget.searchParams.set('newname', newvalue);
            redirectTarget.searchParams.set('tagid', tagid);
            redirectTarget.searchParams.set('action', 'renamecombine');
            redirectTarget.searchParams.set('sesskey', Config.sesskey);

            window.location.href = redirectTarget;
        } catch {
            return;
        }
    });
};

/**
 * Initialises tag collection management page.
 *
 * @method initManageCollectionsPage
 */
export const initManageCollectionsPage = () => {
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

    document.addEventListener('click', async(e) => {
        const addTagCollectionNode = e.target.closest('.addtagcoll > a');
        if (addTagCollectionNode) {
            e.preventDefault();
            addTagCollection(addTagCollectionNode);
            return;
        }

        const deleteCollectionButton = e.target.closest('.tag-collections-table .action_delete');
        if (deleteCollectionButton) {
            e.preventDefault();
            deleteSelectedCollection(deleteCollectionButton);
        }
    });
};
