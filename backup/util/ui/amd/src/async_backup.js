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
    var checkdelay = 5000; //  How often we check for progress updates.
    var backupid; //  The backup id to get the progress for.
    var contextid; //  The course this backup progress is for.
    var restoreurl; //  The URL to view course restores.
    var typeid; //  The type of operation backup or restore.
    var backupintervalid; //  The id of the setInterval function.
    var allbackupintervalid; //  The id of the setInterval function.

    /**
     * Helper function to update UI components.
     *
     * @param {string} backupid The id to match elements on.
     * @param {number} percentage The completion percentage to apply.
     */
    function updateElement(backupid, percentage) {
        var percentagewidth = Math.round(percentage) + '%';
        var elementbar = $('#' + backupid + '_bar');
        var percentagetext = percentage.toFixed(2) + '%';

        // Set progress bar percentage indicators
        elementbar.attr('aria-valuenow', percentagewidth);
        elementbar.css('width', percentagewidth);
        elementbar.text(percentagetext);
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
     * Update the Moodle user interface with the progress of
     * the backup process.
     *
     * @param {object} progress The progress and status of the process.
     */
    function updateProgress(progress) {
        var percentage = progress.progress * 100;
        var elementbar = $('#' + backupid + '_bar');
        var elementstatus = $('#' + backupid + '_status');
        var elementdetail = $('#' + backupid + '_detail');
        var elementbutton = $('#' + backupid + '_button');
        var stringRequests;

        if (progress.status == STATUS_EXECUTING) {
            // Process is in progress.
            // Add in progress class color to bar
            elementbar.addClass('bg-success');

            updateElement(backupid, percentage);

            // Change heading
            var strProcessing = 'async' + typeid + 'processing';
            Str.get_string(strProcessing, 'backup').then(function(title) {
                elementstatus.text(title);
                return title;
            }).catch(function() {
                notification.exception(new Error('Failed to load string: backup ' + strProcessing));
            });

        } else if (progress.status == STATUS_FINISHED_ERR) {
            // Process completed with error.

            // Add in fail class color to bar
            elementbar.addClass('bg-danger');

            // Remove in progress class color to bar
            elementbar.removeClass('bg-success');

            updateElement(backupid, 100);

            // Change heading and text
            var strStatus = 'async' + typeid + 'error';
            var strStatusDetail = 'async' + typeid + 'errordetail';
            stringRequests = [
                {key: strStatus, component: 'backup'},
                {key: strStatusDetail, component: 'backup'}
            ];
            Str.get_strings(stringRequests).then(function(strings) {
                elementstatus.text(strings[0]);
                elementdetail.text(strings[1]);

                return strings;
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
            elementbar.addClass('bg-success');

            updateElement(backupid, 100);

            // Change heading and text
            var strComplete = 'async' + typeid + 'complete';
            Str.get_string(strComplete, 'backup').then(function(title) {
                elementstatus.text(title);
                return title;
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

                        return strings;
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

                    return strings;
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
     * all the pending processes.
     *
     * @param {object} progress The progress and status of the process.
     */
    function updateProgressAll(progress) {
        progress.forEach(function(element) {
            var percentage = element.progress * 100;
            var backupid = element.backupid;
            var elementbar = $('#' + backupid + '_bar');
            var type = element.operation;

            if (element.status == STATUS_EXECUTING) {
                // Process is in element.

                // Add in element class color to bar
                elementbar.addClass('bg-success');

                updateElement(backupid, percentage);

            } else if (element.status == STATUS_FINISHED_ERR) {
                // Process completed with error.

                // Add in fail class color to bar
                elementbar.addClass('bg-danger');
                elementbar.addClass('complete');

                // Remove in element class color to bar
                $('#' + backupid + '_bar').removeClass('bg-success');

                updateElement(backupid, 100);

            } else if (element.status == STATUS_FINISHED_OK) {
                // Process completed successfully.

                // Add in element class color to bar
                elementbar.addClass('bg-success');
                elementbar.addClass('complete');

                updateElement(backupid, 100);

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
        }])[0].done(function(response) {
            // We have the progress now update the UI.
            updateProgress(response[0]);
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
            }])[0].done(function(response) {
                updateProgressAll(response);
            });
        } else {
            clearInterval(allbackupintervalid); // No more progress bars to update, stop checking.
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
