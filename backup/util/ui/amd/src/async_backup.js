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
 * This module updates the UI during an asynchronous
 * backup or restore process.
 *
 * @module     backup/util/async_backup
 * @package    core
 * @copyright  2018 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.7
 */
define(['jquery', 'core/ajax', 'core/str', 'core/notification', 'core/templates'],
        function($, ajax, Str, notification, Templates) {

    /**
     * Module level constants.
     *
     * Using var instead of const as ES6 isn't fully supported yet.
     */
    var STATUS_EXECUTING = 800;
    var STATUS_FINISHED_ERR = 900;
    var STATUS_FINISHED_OK = 1000;

    /**
     * Module level variables.
     */
    var Asyncbackup = {};
    var checkdelayoriginal = 15000; // This is the default time to use.
    var checkdelay = 15000; // How often we should check for progress updates.
    var checkdelaymultipler = 1.5; // If a request fails this multiplier will be used to increase the checkdelay value
    var backupid; //  The backup id to get the progress for.
    var contextid; //  The course this backup progress is for.
    var restoreurl; //  The URL to view course restores.
    var typeid; //  The type of operation backup or restore.
    var backupintervalid; //  The id of the setInterval function.
    var allbackupintervalid; //  The id of the setInterval function.
    var allcopyintervalid; //  The id of the setInterval function.
    var timeout = 2000; // Timeout for ajax requests.

    /**
     * Helper function to update UI components.
     *
     * @param {string} backupid The id to match elements on.
     * @param {string} type The type of operation, backup or restore.
     * @param {number} percentage The completion percentage to apply.
     */
    function updateElement(backupid, type, percentage) {
        var percentagewidth = Math.round(percentage) + '%';
        var elementbar = document.querySelectorAll("[data-" + type + "id=" + CSS.escape(backupid) + "]")[0];
        var percentagetext = percentage.toFixed(2) + '%';

        // Set progress bar percentage indicators
        elementbar.setAttribute('aria-valuenow', percentagewidth);
        elementbar.style.width = percentagewidth;
        elementbar.innerHTML = percentagetext;
    }

    /**
     * Updates the interval we use to check for backup progress.
     *
     * @param {Number} intervalid The id of the interval
     * @param {Function} callback The function to use in setInterval
     * @param {Number} value The specified interval (in milliseconds)
     * @returns {Number}
     */
    function updateInterval(intervalid, callback, value) {
        clearInterval(intervalid);
        return setInterval(callback, value);
    }

    /**
     * Update backup table row when an async backup completes.
     *
     * @param {string} backupid The id to match elements on.
     */
    function updateBackupTableRow(backupid) {
        var statuscell = $('#' + backupid + '_bar').parent().parent();
        var tablerow = statuscell.parent();
        var cellsiblings = statuscell.siblings();
        var timecell = cellsiblings[1];
        var timevalue = $(timecell).text();
        var filenamecell = cellsiblings[0];
        var filename = $(filenamecell).text();

        ajax.call([{
            // Get the table data via webservice.
            methodname: 'core_backup_get_async_backup_links_backup',
            args: {
                'filename': filename,
                'contextid': contextid
            },
        }])[0].done(function(response) {
            // We have the data now update the UI.
            var context = {
                    filename: filename,
                    time: timevalue,
                    size: response.filesize,
                    fileurl: response.fileurl,
                    restoreurl: response.restoreurl
                    };

            Templates.render('core/async_backup_progress_row', context).then(function(html, js) {
                Templates.replaceNodeContents(tablerow, html, js);
                return;
            }).fail(function() {
                notification.exception(new Error('Failed to load table row'));
                return;
            });
        });
    }

    /**
     * Update restore table row when an async restore completes.
     *
     * @param {string} backupid The id to match elements on.
     */
    function updateRestoreTableRow(backupid) {
        var statuscell = $('#' + backupid + '_bar').parent().parent();
        var tablerow = statuscell.parent();
        var cellsiblings = statuscell.siblings();
        var coursecell = cellsiblings[0];
        var timecell = cellsiblings[1];
        var timevalue = $(timecell).text();

        ajax.call([{
            // Get the table data via webservice.
            methodname: 'core_backup_get_async_backup_links_restore',
            args: {
                'backupid': backupid,
                'contextid': contextid
            },
        }])[0].done(function(response) {
         // We have the data now update the UI.
            var resourcename = $(coursecell).text();
            var context = {
                    resourcename: resourcename,
                    restoreurl: response.restoreurl,
                    time: timevalue
                    };

            Templates.render('core/async_restore_progress_row', context).then(function(html, js) {
                Templates.replaceNodeContents(tablerow, html, js);
                return;
            }).fail(function() {
                notification.exception(new Error('Failed to load table row'));
                return;
            });
        });
    }

    /**
     * Update copy table row when an course copy completes.
     *
     * @param {string} backupid The id to match elements on.
     */
    function updateCopyTableRow(backupid) {
        var elementbar = document.querySelectorAll("[data-restoreid=" + CSS.escape(backupid) + "]")[0];
        var restorecourse = elementbar.closest('tr').children[1];
        var coursename = restorecourse.innerHTML;
        var courselink = document.createElement('a');
        var elementbarparent = elementbar.closest('td');
        var operation = elementbarparent.previousElementSibling;

        // Replace the prgress bar.
        Str.get_string('complete').then(function(content) {
            operation.innerHTML = content;
            return;
        }).catch(function() {
            notification.exception(new Error('Failed to load string: complete'));
            return;
        });

        Templates.render('core/async_copy_complete_cell', {}).then(function(html, js) {
            Templates.replaceNodeContents(elementbarparent, html, js);
            return;
        }).fail(function() {
            notification.exception(new Error('Failed to load table cell'));
            return;
        });

        // Update the destination course name to a link to that course.
        ajax.call([{
            methodname: 'core_backup_get_async_backup_links_restore',
            args: {
                'backupid': backupid,
                'contextid': 0
            },
        }])[0].done(function(response) {
            courselink.setAttribute('href', response.restoreurl);
            courselink.innerHTML = coursename;
            restorecourse.innerHTML = null;
            restorecourse.appendChild(courselink);

            return;
        }).fail(function() {
            notification.exception(new Error('Failed to update table row'));
            return;
        });
    }

    /**
     * Update the Moodle user interface with the progress of
     * the backup process.
     *
     * @param {object} progress The progress and status of the process.
     */
    function updateProgress(progress) {
        var percentage = progress.progress * 100;
        var type = 'backup';
        var elementbar = document.querySelectorAll("[data-" + type + "id=" + CSS.escape(backupid) + "]")[0];
        var elementstatus = $('#' + backupid + '_status');
        var elementdetail = $('#' + backupid + '_detail');
        var elementbutton = $('#' + backupid + '_button');
        var stringRequests;

        if (progress.status == STATUS_EXECUTING) {
            // Process is in progress.
            // Add in progress class color to bar.
            elementbar.classList.add('bg-success');

            updateElement(backupid, type, percentage);

            // Change heading.
            var strProcessing = 'async' + typeid + 'processing';
            Str.get_string(strProcessing, 'backup').then(function(title) {
                elementstatus.text(title);
                return;
            }).catch(function() {
                notification.exception(new Error('Failed to load string: backup ' + strProcessing));
            });

        } else if (progress.status == STATUS_FINISHED_ERR) {
            // Process completed with error.

            // Add in fail class color to bar.
            elementbar.classList.add('bg-danger');

            // Remove in progress class color to bar.
            elementbar.classList.remove('bg-success');

            updateElement(backupid, type, 100);

            // Change heading and text.
            var strStatus = 'async' + typeid + 'error';
            var strStatusDetail = 'async' + typeid + 'errordetail';
            stringRequests = [
                {key: strStatus, component: 'backup'},
                {key: strStatusDetail, component: 'backup'}
            ];
            Str.get_strings(stringRequests).then(function(strings) {
                elementstatus.text(strings[0]);
                elementdetail.text(strings[1]);

                return;
            })
            .catch(function() {
                notification.exception(new Error('Failed to load string'));
                return;
            });

            $('.backup_progress').children('span').removeClass('backup_stage_current');
            $('.backup_progress').children('span').last().addClass('backup_stage_current');

            // Stop checking when we either have an error or a completion.
            clearInterval(backupintervalid);

        } else if (progress.status == STATUS_FINISHED_OK) {
            // Process completed successfully.

            // Add in progress class color to bar
            elementbar.classList.add('bg-success');

            updateElement(backupid, type, 100);

            // Change heading and text
            var strComplete = 'async' + typeid + 'complete';
            Str.get_string(strComplete, 'backup').then(function(title) {
                elementstatus.text(title);
                return;
            }).catch(function() {
                notification.exception(new Error('Failed to load string: backup ' + strComplete));
            });

            if (typeid == 'restore') {
                ajax.call([{
                    // Get the table data via webservice.
                    methodname: 'core_backup_get_async_backup_links_restore',
                    args: {
                        'backupid': backupid,
                        'contextid': contextid
                    },
                }])[0].done(function(response) {
                    var strDetail = 'async' + typeid + 'completedetail';
                    var strButton = 'async' + typeid + 'completebutton';
                    var stringRequests = [
                        {key: strDetail, component: 'backup', param: response.restoreurl},
                        {key: strButton, component: 'backup'}
                    ];
                    Str.get_strings(stringRequests).then(function(strings) {
                        elementdetail.html(strings[0]);
                        elementbutton.text(strings[1]);
                        elementbutton.attr('href', response.restoreurl);

                        return;
                    })
                    .catch(function() {
                        notification.exception(new Error('Failed to load string'));
                        return;
                    });

                });
            } else {
                var strDetail = 'async' + typeid + 'completedetail';
                var strButton = 'async' + typeid + 'completebutton';
                stringRequests = [
                    {key: strDetail, component: 'backup', param: restoreurl},
                    {key: strButton, component: 'backup'}
                ];
                Str.get_strings(stringRequests).then(function(strings) {
                    elementdetail.html(strings[0]);
                    elementbutton.text(strings[1]);
                    elementbutton.attr('href', restoreurl);

                    return;
                })
                .catch(function() {
                    notification.exception(new Error('Failed to load string'));
                    return;
                });

            }

            $('.backup_progress').children('span').removeClass('backup_stage_current');
            $('.backup_progress').children('span').last().addClass('backup_stage_current');

            // Stop checking when we either have an error or a completion.
            clearInterval(backupintervalid);
        }
    }

    /**
     * Update the Moodle user interface with the progress of
     * all the pending processes for backup and restore operations.
     *
     * @param {object} progress The progress and status of the process.
     */
    function updateProgressAll(progress) {
        progress.forEach(function(element) {
            var percentage = element.progress * 100;
            var backupid = element.backupid;
            var type = element.operation;
            var elementbar = document.querySelectorAll("[data-" + type + "id=" + CSS.escape(backupid) + "]")[0];

            if (element.status == STATUS_EXECUTING) {
                // Process is in element.

                // Add in element class color to bar
                elementbar.classList.add('bg-success');

                updateElement(backupid, type, percentage);

            } else if (element.status == STATUS_FINISHED_ERR) {
                // Process completed with error.

                // Add in fail class color to bar
                elementbar.classList.add('bg-danger');
                elementbar.classList.add('complete');

                // Remove in element class color to bar
                elementbar.classList.remove('bg-success');

                updateElement(backupid, type, 100);

            } else if (element.status == STATUS_FINISHED_OK) {
                // Process completed successfully.

                // Add in element class color to bar
                elementbar.classList.add('bg-success');
                elementbar.classList.add('complete');

                updateElement(backupid, type, 100);

                // We have a successful backup. Update the UI with download and file details.
                if (type == 'backup') {
                    updateBackupTableRow(backupid);
                } else {
                    updateRestoreTableRow(backupid);
                }

            }

        });
    }

    /**
     * Update the Moodle user interface with the progress of
     * all the pending processes for copy operations.
     *
     * @param {object} progress The progress and status of the process.
     */
    function updateProgressCopy(progress) {
        progress.forEach(function(element) {
            var percentage = element.progress * 100;
            var backupid = element.backupid;
            var type = element.operation;
            var elementbar = document.querySelectorAll("[data-" + type + "id=" + CSS.escape(backupid) + "]")[0];

            if (type == 'restore') {
                 let restorecell = elementbar.closest('tr').children[3];
                 Str.get_string('restore').then(function(content) {
                     restorecell.innerHTML = content;
                     return;
                 }).catch(function() {
                     notification.exception(new Error('Failed to load string: restore'));
                 });
            }

            if (element.status == STATUS_EXECUTING) {
                // Process is in element.

                // Add in element class color to bar
                elementbar.classList.add('bg-success');

                updateElement(backupid, type, percentage);

            } else if (element.status == STATUS_FINISHED_ERR) {
                // Process completed with error.

                // Add in fail class color to bar
                elementbar.classList.add('bg-danger');
                elementbar.classList.add('complete');

                // Remove in element class color to bar
                elementbar.classList.remove('bg-success');

                updateElement(backupid, type, 100);

            } else if ((element.status == STATUS_FINISHED_OK) && (type == 'restore')) {
                // Process completed successfully.

                // Add in element class color to bar
                elementbar.classList.add('bg-success');
                elementbar.classList.add('complete');

                updateElement(backupid, type, 100);

                // We have a successful copy. Update the UI link to copied course.
                updateCopyTableRow(backupid);
            }

        });
    }

    /**
     * Get the progress of the backup process via ajax.
     */
    function getBackupProgress() {
        ajax.call([{
            // Get the backup progress via webservice.
            methodname: 'core_backup_get_async_backup_progress',
            args: {
                'backupids': [backupid],
                'contextid': contextid
            },
        }], true, true, false, timeout)[0].done(function(response) {
            // We have the progress now update the UI.
            updateProgress(response[0]);
            checkdelay = checkdelayoriginal;
            backupintervalid = updateInterval(backupintervalid, getBackupProgress, checkdelayoriginal);
        }).fail(function() {
            checkdelay = checkdelay * checkdelaymultipler;
            backupintervalid = updateInterval(backupintervalid, getBackupProgress, checkdelay);
        });
    }

    /**
     * Get the progress of all backup processes via ajax.
     */
    function getAllBackupProgress() {
        var backupids = [];
        var progressbars = $('.progress').find('.progress-bar').not('.complete');

        progressbars.each(function() {
            backupids.push((this.id).substring(0, 32));
        });

        if (backupids.length > 0) {
            ajax.call([{
                // Get the backup progress via webservice.
                methodname: 'core_backup_get_async_backup_progress',
                args: {
                    'backupids': backupids,
                    'contextid': contextid
                },
            }], true, true, false, timeout)[0].done(function(response) {
                updateProgressAll(response);
                checkdelay = checkdelayoriginal;
                allbackupintervalid = updateInterval(allbackupintervalid, getAllBackupProgress, checkdelayoriginal);
            }).fail(function() {
                checkdelay = checkdelay * checkdelaymultipler;
                allbackupintervalid = updateInterval(allbackupintervalid, getAllBackupProgress, checkdelay);
            });
        } else {
            clearInterval(allbackupintervalid); // No more progress bars to update, stop checking.
        }
    }

    /**
     * Get the progress of all copy processes via ajax.
     */
    function getAllCopyProgress() {
        var copyids = [];
        var progressbars = $('.progress').find('.progress-bar').not('.complete');

        progressbars.each(function() {
            let progressvars = {
                    'backupid': this.dataset.backupid,
                    'restoreid': this.dataset.restoreid,
                    'operation': this.dataset.operation,
            };
            copyids.push(progressvars);
        });

        if (copyids.length > 0) {
            ajax.call([{
                // Get the copy progress via webservice.
                methodname: 'core_backup_get_copy_progress',
                args: {
                    'copies': copyids
                },
            }], true, true, false, timeout)[0].done(function(response) {
                updateProgressCopy(response);
                checkdelay = checkdelayoriginal;
                allcopyintervalid = updateInterval(allcopyintervalid, getAllCopyProgress, checkdelayoriginal);
            }).fail(function() {
                checkdelay = checkdelay * checkdelaymultipler;
                allcopyintervalid = updateInterval(allcopyintervalid, getAllCopyProgress, checkdelay);
            });
        } else {
            clearInterval(allcopyintervalid); // No more progress bars to update, stop checking.
        }
    }

    /**
     * Get status updates for all backups.
     *
     * @public
     * @param {number} context The context id.
     */
    Asyncbackup.asyncBackupAllStatus = function(context) {
        contextid = context;
        allbackupintervalid = setInterval(getAllBackupProgress, checkdelay);
    };

    /**
     * Get status updates for all course copies.
     *
     * @public
     */
    Asyncbackup.asyncCopyAllStatus = function() {
        allcopyintervalid = setInterval(getAllCopyProgress, checkdelay);
    };

    /**
     * Get status updates for backup.
     *
     * @public
     * @param {string} backup The backup record id.
     * @param {number} context The context id.
     * @param {string} restore The restore link.
     * @param {string} type The operation type (backup or restore).
     */
    Asyncbackup.asyncBackupStatus = function(backup, context, restore, type) {
        backupid = backup;
        contextid = context;
        restoreurl = restore;

        if (type == 'backup') {
            typeid = 'backup';
        } else {
            typeid = 'restore';
        }

        // Remove the links from the progress bar, no going back now.
        $('.backup_progress').children('a').removeAttr('href');

        //  Periodically check for progress updates and update the UI as required.
        backupintervalid = setInterval(getBackupProgress, checkdelay);

      };

      return Asyncbackup;
});
