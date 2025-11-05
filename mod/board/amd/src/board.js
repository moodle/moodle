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
 * A javascript module to handle the board.
 *
 * @author     Karen Holland <karen@brickfieldlabs.ie>
 * @copyrigt   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from "jquery";
import {get_strings as getStrings, get_string as getString} from "core/str";
import Ajax from "core/ajax";
import ModalCancel from "core/modal_cancel";
import ModalEvents from "core/modal_events";
import Notification from "core/notification";
import "mod_board/jquery.sortable.amd";
import Comments from "mod_board/comments";
import moveNotesDialog from "./movenotesdialog";
import moveColumnsDialog from "./movecolumnsdialog";
import AjaxFormModal from "mod_board/ajax_form/modal";
import Url from "core/url";

/**
 * Execute a ajax call to a mod_board ajax service.
 *
 * @param {string} method
 * @param {array} args
 * @param {method} callback
 * @param {method} failcallback
 * @private
 */
const _serviceCall = function(method, args, callback, failcallback) {
    Ajax.call([{
        methodname: 'mod_board_' + method,
        args: args,
        done: function(data) {
            callback(data);
        },
        fail: function(error) {
            Notification.exception(error);
            if (failcallback) {
                failcallback(error);
            }
        }
    }]);
};

/**
 * Indicates if this is a keycode we want to listend to for
 * aria purposes.
 *
 * @returns {boolean}
 * @param {number} key
 */
const isAriaTriggerKey = function(key) {
    return key == 13 || key == 32;
};

/**
 * Decodes text from html entities.
 *
 * @param {string} encodedText
 * @returns {*|jQuery}
 */
const decodeText = function(encodedText) {
    return $('<div />').html(encodedText).text();
};

/**
 * Handler for keypress and click actions.
 *
 * @param {object} elem
 * @param {function} callback
 * @returns {*}
 */
const handleAction = function(elem, callback) {
    return elem.on('click keypress', function(e) {
        if (e.type === 'keypress') {
            if (isAriaTriggerKey(e.keyCode)) {
                e.preventDefault();
            } else {
                return;
            }
        }

        callback();
        e.preventDefault();
    });
};

/**
 * The default function of the module, which does the setup of the page.
 *
 * @param {object} settings
 */
export default function(settings) {
    // An array of strings to load as a batch later.
    // Not necessary, but used to load all the strings in one ajax call.

    /* eslint camelcase: off */

    var strings = {
        default_column_heading: '',
        post_button_text: '',
        cancel_button_text: '',
        remove_note_title: '',
        remove_note_text: '',
        remove_column_title: '',
        note_changed_title: '',
        note_changed_text: '',
        note_deleted_text: '',
        column_deleted_text: '',
        rate_note_title: '',
        rate_note_text: '',
        rate_remove_note_text: '',
        Ok: '',
        "delete": '',
        Cancel: '',
        warning: '',
        modal_title_new: '',
        modal_title_edit: '',
        option_youtube: '',
        option_image: '',
        option_link: '',
        option_file: '',

        aria_newcolumn: '',
        aria_newpost: '',
        aria_deletecolumn: '',
        aria_updatecolumn: '',
        aria_movecolumn: '',
        aria_deletepost: '',
        aria_movepost: '',
        aria_editpost: '',
        aria_addmedia: '',
        aria_addmedianew: '',
        aria_deleteattachment: '',
        aria_postedit: '',
        aria_canceledit: '',
        aria_postnew: '',
        aria_cancelnew: '',
        aria_ratepost: '',

        invalid_youtube_url: '',
    };

    // Json decode the strings from the settings.
    var options = JSON.parse(settings.settings) || {};
    var board = options.board || {};

    const ATTACHMENT_VIDEO = 1,
          ATTACHMENT_IMAGE = 2,
          ATTACHMENT_LINK = 3,
          ATTACHMENT_FILE = 4,
          SORTBY_DATE = 1,
          SORTBY_RATING = 2,
          SORTBY_NONE = 3;

    var reloadTimer = null,
        lastHistoryId = null,
        isEditor = options.isEditor || false,
        usersCanEdit = options.usersCanEdit,
        userId = parseInt(options.userId) || -1,
        ownerId = parseInt(options.ownerId),
        groupId = parseInt(options.groupId),
        creatingNote = 0,
        creatingNoteModal = null,
        updatingNote = 0,
        updateNoteModal = null,
        isReadOnlyBoard = options.readonly || false,
        ratingenabled = options.ratingenabled,
        sortby = options.sortby || SORTBY_DATE,
        enableblanktarget = (parseInt(options.enableblanktarget) === 1);

    /**
     * Helper method to make calls to mod_board external services.
     *
     * @param {string} method
     * @param {array} args
     * @param {function} callback
     * @param {function} failcallback
     */
    var serviceCall = function(method, args, callback, failcallback) {
        if (method !== 'board_history') {
            stopUpdating();
        }
        _serviceCall(method, args, function() {
            if (callback) {
                callback.apply(null, arguments);
            }
            if (method !== 'board_history' && method !== 'get_board') {
                updateBoard(true);
            }
        }, failcallback);
    };

    /**
     * Returns the jquery element of a given note identifier.
     *
     * @param {number} ident
     * @returns {jQuery<HTMLElement>}
     */
    var getNote = function(ident) {
        return $(".board_note[data-ident='" + ident + "']");
    };

    /**
     * Returns the jquery element of the note text for the given note element.
     *
     * @method getNoteTextForNote
     * @param {object} note
     * @returns {*|jQuery}
     */
    var getNoteTextForNote = function(note) {
        return $(note).find(".mod_board_note_text");
    };

    /**
     * Returns the jquery element of the preview for the given note element.
     *
     * @method getNotePreviewForNote
     * @param {object} note
     * @returns {*|jQuery}
     */
    var getNotePreviewForNote = (note) => {
        return $(note).find(".mod_board_preview");
    };


    /**
     * Returns the jquery element of the note heading for the given note element.
     *
     * @method getNoteHeadingForNote
     * @param {object} note
     * @returns {*|jQuery}
     */
    var getNoteHeadingForNote = function(note) {
        return $(note).find(".mod_board_note_heading");
    };

    /**
     * Returns the jquery element of the note border for the given note element.
     *
     * @method getNoteBorderForNote
     * @param {object} note
     * @returns {*|jQuery}
     */
    var getNoteBorderForNote = function(note) {
        return $(note).find(".mod_board_note_border");
    };

    /**
     * Creates text identifier for a given node.
     *
     * @method textIdentifierForNote
     * @param {object} note
     * @returns {String}
     */
    var textIdentifierForNote = function(note) {
        return note.attr('data-identifier');
    };

    /**
     * Update the Aria info for a given note id.
     *
     * @method updateNoteAria
     * @param {number} noteId
     */
    var updateNoteAria = function(noteId) {
        var note = getNote(noteId),
            columnIdentifier = note.closest('.board_column').find('.mod_board_column_name').text();

        if (noteId) { // New post
            var noteIdentifier = decodeText(textIdentifierForNote(note)),
                deleteNoteString = strings.aria_deletepost.replace('{column}', columnIdentifier).replace('{post}', noteIdentifier);

            note.find('.delete_note').attr('aria-label', deleteNoteString).attr('title', deleteNoteString);

            var moveNoteString = strings.aria_movepost.replace('{post}', noteIdentifier);
            note.find('.move_note').attr('aria-label', moveNoteString).attr('title', moveNoteString);

            var editNoteString = strings.aria_editpost.replace('{post}', noteIdentifier);
            note.find('.edit_note').attr('aria-label', editNoteString).attr('title', editNoteString);

            note.find('.mod_board_rating').attr('aria-label', strings.aria_ratepost.replace('{column}',
                columnIdentifier).replace('{post}', noteIdentifier));
            note.find('.note_ariatext').html(noteIdentifier);
        }

    };

    /**
     * Update the Aria information for a given column id.
     *
     * @method updateColumnAria
     * @param {number} columnId
     */
    var updateColumnAria = function(columnId) {
        var column = $('.board_column[data-ident=' + columnId + ']'),
            columnIdentifier = column.find('.mod_board_column_name').text(),
            newNoteString = strings.aria_newpost.replace('{column}', columnIdentifier),
            moveColumnString = strings.aria_movecolumn.replace('{column}', columnIdentifier),
            deleteColumnString = strings.aria_deletecolumn.replace('{column}', columnIdentifier),
            updateColumnString = strings.aria_updatecolumn.replace('{column}', columnIdentifier);
        column.find('.newnote').attr('aria-label', newNoteString).attr('title', newNoteString);
        column.find('.mod_column_move').attr('aria-label', moveColumnString).attr('title', moveColumnString);
        column.find('.delete_column').attr('aria-label', deleteColumnString).attr('title', deleteColumnString);
        column.find('.update_column').attr('aria-label', updateColumnString).attr('title', updateColumnString);

        column.find(".board_note").each(function(index, note) {
            updateNoteAria($(note).data('ident'));
        });
    };

    /**
     * Stop the current note creating process.
     *
     * @method stopCreatingNote
     */
    const stopCreatingNote = function() {
        if (!creatingNote) {
            return;
        }

        if (creatingNoteModal) {
            creatingNoteModal.destroy();
        }

        creatingNote = 0;
        creatingNoteModal = null;
    };

    /**
     * Stop the current note updating process.
     *
     * @method stopUpdatingNote
     */
    const stopUpdatingNote = function() {
        if (!updatingNote) {
            return;
        }

        if (updateNoteModal) {
            updateNoteModal.destroy();
        }

        updatingNote = 0;
        updateNoteModal = null;
    };

    /**
     * Delete a given note, by identifier.
     *
     * @method deleteNote
     * @param {number} ident
     */
    var deleteNote = function(ident) {
        Notification.confirm(
            strings.remove_note_title, // Are you sure?
            strings.remove_note_text, // This will effect others.
            strings.delete,
            strings.Cancel,
            function() {
                serviceCall('delete_note', {id: ident}, function(result) {
                    if (result.status) {
                        lastHistoryId = result.historyid;
                        let note = getNote(ident);
                        if (sortby == SORTBY_NONE) {
                            let columnID = note.data('column');
                            let sortorder = note.data('sortorder');
                            sortAfterDelete(columnID, sortorder);
                        }
                        note.remove();
                    }
                });
            }
        );
    };

    /**
     * This function gets a board column as a jQuery element.
     * @param {number} columnID The column ID.
     * @returns {jQuery<HTMLElement>}
     */
    const getColumn = (columnID) => {
        return $(`.board_column[data-ident='${columnID}'] .board_column_content`);
    };

    const sortAfterDelete = (columnID, sortorder) => {
        let column = getColumn(columnID);
        let elements = column.children().filter((_, element) => {
            return parseInt($(element).data('sortorder')) > parseInt(sortorder);
        });
        elements.each((_, element) => {
            let so = $(element).data('sortorder');
            $(element).data('sortorder', so - 1);
        });
    };

    /**
     * Rate (star) a give note, by identifier.
     *
     * @method rateNote
     * @param {number} ident
     */
    var rateNote = function(ident) {
        if (!ratingenabled) {
            return;
        }
        if (isReadOnlyBoard) {
            return;
        }

        var note = getNote(ident),
            rating = note.find('.mod_board_rating');
        if (rating.data('disabled')) {
            return;
        }
        rating.data('disabled', true);

        serviceCall('can_rate_note', {id: ident}, function(result) {
            if (result.canrate) {
                const rateRemoveText = result.hasrated ? strings.rate_remove_note_text : strings.rate_note_text;
                Notification.confirm(
                    strings.rate_note_title,
                    rateRemoveText, // Are you sure?
                    strings.Ok,
                    strings.Cancel,
                    function() {
                        serviceCall('rate_note', {id: ident}, function(result) {
                            if (result.status) {
                                lastHistoryId = result.historyid;
                                rating.html(` ${result.rating} `);
                                if (sortby == SORTBY_RATING) {
                                    sortNotes(note.closest('.board_column_content'));
                                }
                            }
                            rating.data('disabled', false);
                        });
                    }
                ).then(function(rateModal) {
                    // Do this here, because it catches both cancel clicks, or someone clicking the X.
                    rateModal.getRoot().on(ModalEvents.hidden, function() {
                        rating.data('disabled', false);
                    });
                });
            }
        });
    };

    /**
     * This parses a youtube video ID from a URL. We can use this ID to
     * construct the embed URL.
     * @param {string} url The URL entered to the modal.
     * @returns {string | null} The youtube embed URL or null.
     */
    const getEmbedUrl = (url) => {
        // Thanks for the regex from: https://gist.github.com/rodrigoborgesdeoliveira/987683cfbfcc8d800192da1e73adc486.
        let regex = /(\/|%3D|v=)([0-9A-z-_]{11})([%#?&]|$)/;
        let videoID = url.match(regex);
        if (!videoID || videoID[2] === undefined || videoID[2].length !== 11) {
            return null;
        }
        return `https://www.youtube-nocookie.com/embed/${videoID[2]}`;
    };

    /**
     * Display the attachment preview for a note.
     *
     * @method previewAttachment
     * @param {object} note
     * @param {object} attachment
     */
    var previewAttachment = function(note, attachment) {
        let elem = note.find('.mod_board_preview');

        if (!getNoteTextForNote(note).html().length) {
            elem.addClass('mod_board_notext');
        } else {
            elem.removeClass('mod_board_notext');
        }

        elem.removeClass('wrapper_youtube');
        elem.removeClass('wrapper_image');
        elem.removeClass('wrapper_url');
        elem.removeClass('wrapper_file');

        if (attachment.url) {
            let preview = null;
            switch (parseInt(attachment.type)) {
                case ATTACHMENT_VIDEO: { // Youtube
                    let url = getEmbedUrl(attachment.url);
                    if (url === null) {
                        elem.html(strings.invalid_youtube_url);
                    } else {
                        elem.html('<iframe src="' + url +
                            '" class="mod_board_preview_element" frameborder="0" allow="accelerometer; autoplay; clipboard-write;' +
                            'encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><a href="#" ' +
                            'class="stretched-link" aria-hidden="true"></a>');
                        elem.addClass('wrapper_youtube').addClass('position-relative');
                    }
                    elem.show();
                    elem.addClass('wrapper_image');
                    elem.data('type', 1);
                    elem.data('info', attachment.info);
                }
                break;
                case ATTACHMENT_IMAGE: // Image file
                    preview = document.createElement('img');
                    preview.src = attachment.url;
                    preview.alt = decodeText(attachment.info);
                    preview.classList.add('mod_board_preview_element');
                    elem.html('');
                    elem.append(preview);
                    elem.addClass('wrapper_image');
                    elem.data('type', 2);
                    elem.data('info', attachment.info);
                    elem.show();
                break;
                case ATTACHMENT_LINK: // Url
                    preview = document.createElement('a');
                    preview.href = attachment.url;
                    preview.text = decodeText(attachment.info);
                    preview.classList.add('mod_board_preview_element');
                    if (enableblanktarget) {
                        preview.target = '_blank';
                    }
                    elem.html('');
                    elem.append(preview);
                    elem.addClass('wrapper_url');
                    elem.data('type', 3);
                    elem.data('info', attachment.info);
                    elem.show();
                break;
                case ATTACHMENT_FILE: // General file
                    preview = document.createElement('a');
                    preview.href = attachment.url;
                    preview.text = decodeText(attachment.info);
                    preview.classList.add('mod_board_preview_element');
                    elem.html('');
                    elem.append(preview);
                    elem.addClass('wrapper_file');
                    elem.data('type', 4);
                    elem.data('info', attachment.info);
                    elem.show();
                    break;
                default:
                    elem.html('');
                    elem.data('type', 0);
                    elem.data('info', '');
                    elem.hide();
            }
        } else {
            elem.html('');
            elem.hide();
        }
    };

    /**
     * Add a new note with the given information.
     *
     * @method addNote
     * @param {number} columnid
     * @param {number} ident
     * @param {String} identifier name of note
     * @param {string} heading
     * @param {string} content
     * @param {object} attachment
     * @param {object} owner
     * @param {number} sortorder
     * @param {string} rating
     */
    var addNote = function(columnid, ident, identifier, heading, content, attachment, owner, sortorder, rating) {
        var ismynote = owner.id == userId || !ident;
        var iseditable = isEditor || (ismynote && !isReadOnlyBoard);

        if (!ident) {
            // Nothing to do.
            return;
        }

        // Making space for this note if necessary in the sort order.
        if (sortby == SORTBY_NONE) {
            let children = $(`.board_column[data-ident='${columnid}'] .board_column_content`).children();
            let elements = children.filter((_, element) => {
                return parseInt($(element).data('sortorder')) >= parseInt(sortorder);
            });
            elements.each((_, element) => {
                let so = $(element).data('sortorder');
                $(element).data('sortorder', so + 1);
            });
        }

        var note = $('<div class="board_note" data-column="' + columnid + '" data-ident="' + ident +
            '" data-sortorder="' + sortorder + '"></div>');
        note.attr('data-identifier', identifier);

        if (ismynote) {
            note.addClass('mod_board_mynote');
        }
        if (iseditable) {
            note.addClass('mod_board_editablenote');
        }
        if (!ismynote && !iseditable) {
            note.addClass('mod_board_nosort');
        }

        var notecontent = $('<div class="mod_board_note_content"></div>'),
            notecontrols = $('<div class="mod_board_note_controls"></div>'),
            noteHeading = $('<div class="mod_board_note_heading" tabindex="0">' + (heading ? heading : '') + '</div>'),
            noteBorder = $('<div class="mod_board_note_border"></div>'),
            noteText = $('<div class="mod_board_note_text" tabindex="0">' + (content ? content : '') + '</div>'),
            noteAriaText = $('<div class="note_ariatext hidden" role="heading" aria-level="4" tabindex="0"></div>'),
            attachmentPreview = $('<div class="mod_board_preview"></div>');

        notecontent.append(noteHeading);
        notecontent.append(noteBorder);
        notecontent.append(noteText);
        notecontent.append(noteAriaText);

        notecontent.append(attachmentPreview);
        note.append(notecontent);

        var columnContent = $('.board_column[data-ident=' + columnid + '] .board_column_content');

        if (ratingenabled) {
            note.addClass('mod_board_rateablenote');
            var rateElement = $(`<div class="fa fa-star mod_board_rating" role="button" tabindex="0"> ${rating} </div>`);

            handleAction(rateElement, () => {
                rateNote(ident);
            });
            notecontrols.append(rateElement);
        }

        if (iseditable) {
            var removeElement = $('<div class="fa fa-remove delete_note" role="button" tabindex="0"></div>');
            handleAction(removeElement, () => {
                deleteNote(ident);
            });

            notecontrols.append(removeElement);

            if (usersCanEdit == 1 || isEditor) {
                var moveElement = $('<div class="mod_board_move fa fa-arrows move_note" role="button" tabindex="0"></div>');
                notecontrols.append(moveElement);
                moveNotesDialog.init(moveNote);
            }

            var editElement = $('<div class="mod_board_move fa fa-pencil edit_note" role="button" tabindex="0"></div>');
            notecontrols.append(editElement);
            handleAction(editElement, () => {
                showNoteUpdateModal(ident);
            });
            updateSortable();
        }
        previewAttachment(note, attachment);

        note.append(notecontrols);

        handleAction(notecontent, () => fullScreenNote(ident, notecontent));

        if (!noteHeading.html()) {
            noteHeading.hide();
            noteBorder.hide();
        }
        if (!noteText.html() && noteHeading.html()) {
            noteText.hide();
            noteBorder.hide();
        }

        var lastOne = columnContent.find(".board_note").last();

        if (lastOne.length) {
            note.insertAfter(lastOne);
        } else {
            columnContent.prepend(note);
        }
    };

    /**
     * Add a new column.
     *
     * @method addColumn
     * @param {object} ident
     * @param {string} name
     * @param {bool} locked
     * @param {array} notes
     * @param {string} colour
     */
    var addColumn = function(ident, name, locked, notes, colour) {
        let headerStyle = `style="border-top: 10px solid #${colour}"`;
        var iseditable = isEditor,
            column = $(`<div class="board_column board_column_hasdata" data-locked="${locked}"\
                 ${headerStyle} data-ident="${ident}"></div>`),
            columnHeader = $('<div class="board_column_header"></div>'),
            columnSort = $('<div class="mod_board_column_sort fa"></div>'),
            columnName = $('<div class="mod_board_column_name" tabindex="0" aria-level="3" role="heading">' + name + '</div>'),
            columnContent = $('<div class="board_column_content"></div>'),
            columnNewContent = $('<div class="board_column_newcontent"></div>');
        // Only add the sort button if it makes sense.
        if (sortby != SORTBY_NONE) {
            columnHeader.append(columnSort);
        }
        columnHeader.append(columnName);

        if (options.hideheaders) {
            columnName.addClass('d-none');
        }

        columnSort.on('click', function() {
            sortNotes(columnContent, true);
        });

        if (iseditable) {
            column.addClass('mod_board_editablecolumn');
            const lockIcon = locked ? 'fa-lock' : 'fa-unlock';
            const lockElement = $(`<div class="icon fa ${lockIcon} lock_column" role="button" tabindex="0"></div>`);
            const lockstring = locked ? 'aria_column_locked' : 'aria_column_unlocked';
            getString(lockstring, 'mod_board', decodeText(name)).done(function(str) {
                lockElement.attr('aria-label', str);
                lockElement.attr('title', str);
            });

            handleAction(lockElement, () => {
                const lockColumn = column.attr('data-locked') !== 'true';
                serviceCall('lock_column', {id: ident, status: lockColumn}, function(result) {
                    const columnName = column.find('.mod_board_column_name').text();
                    if (result.status) {
                        if (lockColumn) {
                            lockElement.removeClass('fa-unlock').addClass('fa-lock');
                            column.attr('data-locked', 'true');
                            column.find('.board_button.newnote').addClass('d-none');
                            getString('aria_column_locked', 'mod_board', columnName).done(function(str) {
                                lockElement.attr('aria-label', str);
                                lockElement.attr('title', str);
                            });
                        } else {
                            lockElement.removeClass('fa-lock').addClass('fa-unlock');
                            column.attr('data-locked', 'false');
                            column.find('.board_button.newnote').removeClass('d-none');
                            getString('aria_column_unlocked', 'mod_board', columnName).done(function(str) {
                                lockElement.attr('aria-label', str);
                                lockElement.attr('title', str);
                            });
                        }
                        lastHistoryId = result.historyid;
                        updateSortable();
                    }
                });
            });
            columnHeader.append(lockElement);

            var removeElement = $('<div class="icon fa fa-remove delete_column" role="button" tabindex="0"></div>');
            handleAction(removeElement, () => {
                Notification.confirm(
                    strings.remove_column_title, // Are you sure?
                    getString('remove_column_text', 'mod_board', getColumnName(ident)),
                    strings.delete,
                    strings.Cancel,
                    function() {
                        serviceCall('delete_column', {id: ident}, function(result) {
                            if (result.status) {
                                column.remove();
                                lastHistoryId = result.historyid;
                            }
                        });
                    }
                );
            });
            columnHeader.append(removeElement);

            columnHeader.addClass('icon-size-3');
            const moveElement = $('<div class="icon fa fa-arrows mod_column_move" role="button" tabindex="0"></div>');
            columnHeader.append(moveElement);
            moveColumnsDialog.init(moveColumn);

            var updateElement = $('<div class="icon fa fa-pencil update_column" role="button" tabindex="0"></div>');
            handleAction(updateElement, () => {
                showColumnUpdateModal(ident);
            });
            columnHeader.append(updateElement);
        }

        column.append(columnHeader);
        column.append(columnContent);
        column.append(columnNewContent);

        if (!isReadOnlyBoard) {
            const newNoteButton = $('<div class="board_button newnote" role="button" tabindex="0">' +
            '<div class="button_content"><span class="fa ' + options.noteicon + '"></span></div></div>');
            columnNewContent.append(newNoteButton);
            if (column.attr('data-locked') === 'true') {
                newNoteButton.addClass('d-none');
            }
            handleAction(columnNewContent.find('.newnote'), function() {
                showNoteCreateModal(ident);
            });
        }

        var lastOne = $(".mod_board .board_column_hasdata").last();
        if (lastOne.length) {
            column.insertAfter(lastOne);
        } else {
            $(".mod_board").append(column);
        }

        if (notes) {
            for (var index in notes) {
                let sortorder = sortby == 3 ? notes[index].sortorder : notes[index].timecreated;
                addNote(ident, notes[index].id, notes[index].identifier, notes[index].heading, notes[index].content,
                    {type: notes[index].type, info: notes[index].info, url: notes[index].url},
                    {id: notes[index].userid}, sortorder, notes[index].rating);
            }
        }
        sortNotes(columnContent);
        updateColumnAria(ident);
        if (isEditor || usersCanEdit == 1) {
            updateSortable();
        }
        if (isEditor) {
            columnSorting();
        }
    };

    /**
     * Gets the text name used in the heading of a column.
     * @param {number} id The ID data attribute on the column element.
     * @returns {string}
     */
    const getColumnName = (id) => {
        return $(`.board_column[data-ident='${id}']`).find('.mod_board_column_name').html();
    };

    /**
     * Add the new column button.
     *
     * @method addNewColumnButton
     */
    var addNewColumnButton = function() {
        var column = $('<div class="board_column_empty"></div>');
        column.append('<div class="board_button newcolumn" role="button" tabindex="0" aria-label="' +
            strings.aria_newcolumn + '" title="' + strings.aria_newcolumn + '"><div class="button_content"><span class="fa '
            + options.columnicon + '"></span></div></div>');

        handleAction(column.find('.newcolumn'), function() {
            showColumnCreateModal(board.id);
        });

        $(".mod_board").append(column);
    };

    /**
     * This selects the next heading colour from options based on the count of the
     * current columns. Length of decremented by one as the new column button is
     * also denoted as a column.
     * @returns {string} colour hex string.
     */
    const selectHeadingColour = () => {
        let colCount = $('.board_column').length - 1;
        let colourCount = options.colours.length;
        return options.colours[colCount % colourCount];
    };

    /**
     * Update a note with the provided information.
     *
     * @method updateNote
     * @param {object} note
     * @param {string} heading
     * @param {object} data
     */
    var updateNote = function(note, heading, data) {
        var noteHeading = getNoteHeadingForNote(note);
        var noteText = getNoteTextForNote(note);
        var noteBorder = getNoteBorderForNote(note);

        note.attr('data-identifier', data.identifier);
        noteText.html(data.content);
        noteHeading.html(data.heading);
        previewAttachment(note, data.attachment);
        updateNoteAria(data.id);

        // Reset the visibility state.
        noteHeading.show();
        noteBorder.show();
        noteText.show();
        if (!noteHeading.html()) {
            noteHeading.hide();
            noteBorder.hide();
        }
        if (!noteText.html() && noteHeading.html()) {
            noteText.hide();
            noteBorder.hide();
        }
    };

    /**
     * Fetch and process the recent board history.
     *
     * @method processBoardHistory
     */
    var processBoardHistory = function() {
        let payload = {id: board.id, ownerid: ownerId, groupid: groupId, since: lastHistoryId};
        serviceCall('board_history', payload, function(boardhistory) {
            for (var index in boardhistory) {
                var item = boardhistory[index];
                if (item.boardid != board.id) {
                    continue; // Hmm
                }

                var data = JSON.parse(item.content);
                if (item.action === 'add_note') {
                    let sortorder = sortby == 3 ? data.sortorder : data.timecreated;
                    addNote(data.columnid, data.id, data.identifier, data.heading, data.content, data.attachment,
                        {id: item.userid}, sortorder, data.rating);
                    updateNoteAria(data.id);
                    sortNotes($('.board_column[data-ident=' + data.columnid + '] .board_column_content'));
                } else if (item.action === 'update_note') {
                    let note = getNote(data.id);
                    if (note) {
                        let noteHeading = getNoteHeadingForNote(note);

                        if (updatingNote == data.id) {
                            Notification.confirm(
                                strings.note_changed_title, // Confirm.
                                strings.note_changed_text, // Are you sure?
                                strings.Ok,
                                strings.Cancel,
                                function() {
                                    stopUpdatingNote();
                                }
                            );
                        } else {
                            updateNote(note, noteHeading, data);
                        }
                    }
                } else if (item.action === 'delete_note') {
                    if (updatingNote == data.id) {
                        // eslint-disable-next-line promise/catch-or-return,promise/always-return
                        Notification.alert(strings.warning, strings.note_deleted_text).then(() => {
                            stopUpdatingNote();
                        });
                    }
                    let note = getNote(data.id);
                    if (sortby == SORTBY_NONE) {
                        let columnID = note.data('column');
                        let sortorder = note.data('sortorder');
                        sortAfterDelete(columnID, sortorder);
                    }
                    note.remove();

                } else if (item.action === 'add_column') {
                    addColumn(data.id, data.name, false, {}, selectHeadingColour());
                } else if (item.action === 'move_column') {
                    const board = $('.mod_board');
                    data.sortorder.forEach(column => {
                        const columnElement = board.find(`.board_column[data-ident='${column}']`);
                        columnElement.detach().appendTo(board);
                    });
                } else if (item.action === 'update_column') {
                    $(".board_column[data-ident='" + data.id + "'] .mod_board_column_name").html(data.name);
                    updateColumnAria(data.id);
                } else if (item.action === 'lock_column') {
                    $(".board_column[data-ident='" + data.id + "']").attr("data-locked", data.locked);
                    if (data.locked) {
                        $(".board_column[data-ident='" + data.id + "']").find('.board_button.newnote').addClass('d-none');
                    } else {
                        $(".board_column[data-ident='" + data.id + "']").find('.board_button.newnote').removeClass('d-none');
                    }
                    updateSortable();
                } else if (item.action === 'delete_column') {
                    var column = $(".board_column[data-ident='" + data.id + "']");
                    if (updatingNote && column.find('.board_note[data-ident="' + updatingNote + '"]').length) {
                        // eslint-disable-next-line promise/catch-or-return,promise/always-return
                        Notification.alert(strings.warning, strings.column_deleted_text).then(() => {
                            stopUpdatingNote();
                        });
                    }
                    if (creatingNote == data.id) {
                        // eslint-disable-next-line promise/catch-or-return,promise/always-return
                        Notification.alert(strings.warning, strings.column_deleted_text).then(() => {
                            stopCreatingNote();
                        });
                    }
                    column.remove();
                } else if (item.action === 'rate_note') {
                    var note = getNote(data.id);
                    note.find('.mod_board_rating').html(data.rating);
                    if (sortby == SORTBY_RATING) {
                        sortNotes(note.closest('.board_column_content'));
                    }
                }
                lastHistoryId = item.id;
            }

            updateBoard();
        });
    };

    /**
     * Trigger a board update.
     *
     * @method updateBoard
     * @param {boolean} instant
     */
    var updateBoard = function(instant) {
        if (instant) {
            processBoardHistory();
        } else if (options.history_refresh > 0) {
            if (reloadTimer) {
                stopUpdating();
            }
            reloadTimer = setTimeout(processBoardHistory, options.history_refresh * 1000);
        }
    };

    /**
     * Stop/prevent the board reload timer from firing.
     *
     * @method stopUpdating
     */
    var stopUpdating = function() {
        clearTimeout(reloadTimer);
        reloadTimer = null;
    };

    /**
     * Sort a set of notes.
     *
     * @sortNotes
     * @param {string} content
     * @param {boolean} toggle
     */
    var sortNotes = function(content, toggle) {
        var sortCol = $(content).parent().find('.mod_board_column_sort'),
            direction = $(content).data('sort');
        if (!direction) {
            if (sortby == SORTBY_RATING) {
                direction = 'desc';
            } else {
                direction = 'asc';
            }
        }
        if (toggle) {
            direction = direction === 'asc' ? 'desc' : 'asc';
        }

        if (direction === 'asc') {
            sortCol.removeClass('fa-angle-down');
            sortCol.addClass('fa-angle-up');
        } else {
            sortCol.removeClass('fa-angle-up');
            sortCol.addClass('fa-angle-down');
        }
        $(content).data('sort', direction);

        var desc,
            asc;
        if (sortby == SORTBY_DATE) {
            desc = function(a, b) {
                return $(b).data("sortorder") - $(a).data("sortorder");
            };
            asc = function(a, b) {
                return $(a).data("sortorder") - $(b).data("sortorder");
            };
        } else if (sortby == SORTBY_RATING) {
            desc = function(a, b) {
                return $(b).find('.mod_board_rating').text() - $(a).find('.mod_board_rating').text() ||
                $(b).data("sortorder") - $(a).data("sortorder");
            };
            asc = function(a, b) {
                return $(a).find('.mod_board_rating').text() - $(b).find('.mod_board_rating').text() ||
                $(a).data("sortorder") - $(b).data("sortorder");
            };
        } else if (sortby == SORTBY_NONE) {
            let sortElements = (a, b) => {
                return $(a).data("sortorder") - $(b).data("sortorder");
            };
            $('> .board_note', $(content)).sort(sortElements).appendTo($(content));
            return;
        }

        $('> .board_note', $(content)).sort(direction === 'asc' ? asc : desc).appendTo($(content));

    };

    /**
     * Update sorting of sortable content.
     *
     * @method updateSortable
     */
    var updateSortable = function() {
        let fromColumnID;
        $(".board_column[data-locked='false'] .board_column_content").sortable({
            connectWith: ".board_column[data-locked='false'] .board_column_content",
            cancel: ".mod_board_nosort",
            handle: ".move_note",
            start: function(_, ui) {
                fromColumnID = $(ui.item).closest('.board_column').data('ident');
            },
            stop: function(_, ui) {
                var note = $(ui.item),
                    tocolumn = note.closest('.board_column'),
                    elem = $(this),
                    noteid = note.data('ident'),
                    columnid = tocolumn.data('ident');
                let columnElements = tocolumn.find('.board_column_content').children();
                let sortorder = columnElements.index($(`.board_note[data-ident=${noteid}]`));
                let payload = {
                    id: noteid,
                    columnid: columnid,
                    sortorder: sortorder
                };
                moveNote(fromColumnID, payload, elem);
            }
        });
    };

    /**
     * Move a note to a new position / column.
     *
     * @param {Int} fromColumnID The column the note is being moved from.
     * @param {Object} payload The payload to send to the server.
     * @param {Domnode} elem The element clicked to trigger the move.
     */
    const moveNote = (fromColumnID, payload, elem) => {
        updateSortOrders(fromColumnID, payload.columnid, payload.id, payload.sortorder);

        serviceCall('move_note', payload, (result) => {
            if (result.status) {
                lastHistoryId = result.historyid;
                updateNoteAria(payload.id);
                updateBoard();
                sortNotes($(`.board_column[data-ident=${payload.columnid}] .board_column_content`));
            } else {
                if (elem) {
                    elem.sortable('cancel');
                }
            }
        });
    };

    /**
     * Enable column sorting
     */
    const columnSorting = () => {
        let movingColumnId;
        $(".mod_board").sortable({
            connectWith: ".mod_board",
            axis: "x",
            containment: ".mod_board_wrapper",
            cancel: ".mod_board_nosort",
            handle: ".mod_column_move",
            start: function(_, ui) {
                movingColumnId = $(ui.item).closest('.board_column').data('ident');
            },
            stop: function(_, ui) {
                let column = $(ui.item);
                let columns = $(".mod_board").find('.board_column');
                let sortorder = columns.index(column);
                let payload = {
                    id: movingColumnId,
                    sortorder: sortorder
                };
                moveColumn(payload);
            }
        });
    };

    /**
     * Move a column to a new position.
     *
     * @param {Object} payload The payload to send to the server.
     */
    const moveColumn = (payload) => {
        serviceCall('move_column', payload, false);
    };

    /**
     * Updates the inline data attributes necessary for rendering the lists
     * in the correct sort order. Note: the data attribute values updated by
     * jQuery are not reflected in DOM inspection but are still set.
     * @param {number} fromColumnID The column ID of the column to sort.
     * @param {number} toColumnID The column ID of the column to sort.
     * @param {number} noteID  The note ID that was moved.
     * @param {number} newSortOrder The new position of the note sort order.
     */
    const updateSortOrders = (fromColumnID, toColumnID, noteID, newSortOrder) => {
        let toColumn = $(`.board_column[data-ident=${toColumnID}] .board_column_content`);
        let movedNote = $(`.board_note[data-ident=${noteID}]`);
        let oldSortOrder = movedNote.data('sortorder');
        // Check whether it is the same column and then increment or decrement notes above or below
        // then set sortorder according to whether the sortorder has moved up or down.
        let toChildren = toColumn.children();
        if (fromColumnID == toColumnID) {
            toChildren.each((_, note) => {
                let sortOrder = $(note).data('sortorder');
                if (oldSortOrder < newSortOrder) {
                    if (sortOrder <= newSortOrder && sortOrder >= oldSortOrder) {
                        $(note).data('sortorder', sortOrder - 1);
                    }
                } else if (oldSortOrder > newSortOrder) {
                    if (sortOrder >= newSortOrder && sortOrder <= oldSortOrder) {
                        $(note).data('sortorder', sortOrder + 1);
                    }
                }
            });
        } else {
            let fromColumn = $(`.board_column[data-ident=${fromColumnID}] .board_column_content`);
            let fromChildren = fromColumn.children();
            toChildren.each((_, note) => {
                let sortOrder = $(note).data('sortorder');
                if (sortOrder >= newSortOrder) {
                    $(note).data('sortorder', sortOrder + 1);
                }
            });
            fromChildren.each((_, note) => {
                let sortOrder = $(note).data('sortorder');
                if (sortOrder > oldSortOrder) {
                    $(note).data('sortorder', sortOrder - 1);
                }
            });
        }
        movedNote.data('sortorder', newSortOrder);
    };

    /**
     * Show modal for note creation.
     *
     * @param {Number} columnId
     */
    const showNoteCreateModal = function(columnId) {
        const urlParams = {'columnid': columnId, 'ownerid': ownerId, 'groupid': groupId};
        const formUrl = Url.relativeUrl('/mod/board/note_create_ajax.php', urlParams, false);

        let submittedCallback = (result) => {
            creatingNote = 0;
            creatingNoteModal = null;

            lastHistoryId = result.historyid;
            addNote(columnId, result.note.id, result.note.identifier, result.note.heading, result.note.content,
                {type: result.note.type, info: result.note.info, url: result.note.url},
                {id: result.note.userid}, result.note.timecreated, result.note.rating);
            sortNotes($('.board_column[data-ident=' + columnId + '] .board_column_content'));
            updateNoteAria(result.note.id);
        };

        const modalConfig = {
            'formUrl': formUrl,
            'formSize': 'lg',
            'formSubmittedAction': submittedCallback,
        };

        // eslint-disable-next-line promise/catch-or-return,promise/always-return
        AjaxFormModal.create(modalConfig).then((modal) => {
            creatingNote = columnId;
            creatingNoteModal = modal;
            creatingNoteModal.getRoot().on(ModalEvents.hidden, () => {
                creatingNote = 0;
                creatingNoteModal = null;
            });
        });
    };

    /**
     * Show modal for column creation.
     *
     * @param {Number} boardID
     */
    const showColumnCreateModal = function(boardID) {
        const urlParams = {'boardid': boardID};
        const formUrl = Url.relativeUrl('/mod/board/column_create_ajax.php', urlParams, false);

        const modalConfig = {
            'formUrl': formUrl,
            'formSize': 'sm',
            'formSubmittedAction': 'reload',
        };

        AjaxFormModal.create(modalConfig);
    };

    /**
     * Show modal for column update.
     *
     * @param {Number} columnId
     */
    const showColumnUpdateModal = function(columnId) {
        const urlParams = {'id': columnId};
        const formUrl = Url.relativeUrl('/mod/board/column_update_ajax.php', urlParams, false);

        const modalConfig = {
            'formUrl': formUrl,
            'formSize': 'sm',
            'formSubmittedAction': 'reload',
        };

        AjaxFormModal.create(modalConfig);
    };

    /**
     * Show modal for note updates.
     *
     * @param {Number} noteId
     */
    const showNoteUpdateModal = function(noteId) {
        const urlParams = {'id': noteId};
        const formUrl = Url.relativeUrl('/mod/board/note_update_ajax.php', urlParams, false);

        let submittedCallback = (result) => {
            updatingNote = 0;
            updateNoteModal = null;

            // Updated existing note.
            const note = getNote(noteId);
            lastHistoryId = result.historyid;
            note.attr('data-identifier', result.note.identifier);
            getNoteTextForNote(note).html(result.note.content);
            getNoteHeadingForNote(note).html(result.note.heading);
            updateNoteAria(result.note.id);
            previewAttachment(note, {
                type: result.note.type,
                info: result.note.info, url: result.note.url
            });
        };

        const modalConfig = {
            'formUrl': formUrl,
            'formSize': 'lg',
            'formSubmittedAction': submittedCallback,
        };

        // eslint-disable-next-line promise/catch-or-return,promise/always-return
        AjaxFormModal.create(modalConfig).then((modal) => {
            updatingNote = noteId;
            updateNoteModal = modal;
            updateNoteModal.getRoot().on(ModalEvents.hidden, () => {
                updatingNote = 0;
                updateNoteModal = null;
            });
        });
    };

    /**
     * Show the note in a modal
     * @param {Int} ident The note id
     * @param {Object} notecontent The note content
     */
    var fullScreenNote = (ident, notecontent) => {
        const heading = getNoteHeadingForNote(notecontent).html();
        const modalBody = $(document.createElement('div'));
        modalBody.addClass('mod_board_note_content');
        const text = getNoteTextForNote(notecontent);
        if (text) {
            modalBody.append(text.clone());
        }
        const preview = getNotePreviewForNote(notecontent);
        if (preview) {
            modalBody.append(preview.clone());
        }

        // Adds the comments to a note.
        const commentArea = $(document.createElement('div'));
        commentArea.attr('data-region', 'comment-area');
        modalBody.append(commentArea);
        Comments.fetchFor(ident, commentArea);

        ModalCancel.create({
            title: heading,
            body: modalBody,
        }).then(function(modal) {
            modal.setLarge();
            getString('close_button_text', 'mod_board').done(function(str) {
                modal.setButtonText('cancel', str);
            });
            modal.show();
            // Handle hidden event.
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
            });
            return modal;
        }, this).catch(Notification.exception);
    };

    /**
     * Initialize board.
     *
     * @method init
     */
    var init = function() {
        serviceCall('get_board', {id: board.id, ownerid: ownerId, groupid: groupId}, function(columns) {
            // Init
            if (columns) {
                for (var index in columns) {
                    addColumn(
                        columns[index].id,
                        columns[index].name,
                        columns[index].locked,
                        columns[index].notes || {},
                        options.colours[columns[index].id % options.colours.length]
                    );
                }
            }

            if (isEditor) {
                addNewColumnButton();
            }

            lastHistoryId = board.historyid;

            if (isEditor) {
                updateSortable();
                columnSorting();
            }

            updateBoard();
        });
    };

    // Get strings
    var stringsInfo = [];
    for (var string in strings) {
        stringsInfo.push({key: string, component: 'mod_board'});
    }

    $.when(getStrings(stringsInfo)).done(function(results) {
        var index = 0;
        for (string in strings) {
            strings[string] = results[index++];
        }

        init();
    });
}
