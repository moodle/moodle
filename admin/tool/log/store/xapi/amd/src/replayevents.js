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
/*
 * @package    logstore_xapi
 * @copyright  2020 Learning Pool Ltd <http://learningpool.com>
 * @author     Záborski László <laszlo.zaborski@learningpool.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/str', 'core/config', 'core/notification', 'core/templates', 'jquery', 'jqueryui'],
    function(str, mdlcfg, notification, templates, $) {

        /**
         * Store a sum.
         */
        var countedEvents = 0;

        /**
         * Store allowed resend state.
         */
        var canResend;

        /**
         * Store restrict resend state.
         */
        var canNotResend;

        /**
         * Selector changed.
         */
        var selectorChanged = false;

        /**
         * Name of resend button.
         */
        var labelResendButton = 'resendevents';

        /**
         * Title of confirmation window.
         */
        var labelConfirmTitle = 'confirmresendeventsheader';

        /**
         * Content of confirmation window.
         */
        var labelConfirmContent = 'confirmresendevents';

        /**
         * Store load HTML snippet.
         */
        var loadHTML = '';

        /**
         * Store replay HTML snippet.
         */
        var replayHTML = '';

        /**
         * Store done HTML snippet.
         */
        var doneHTML = '';

        /**
         * Store failed HTML snippet.
         */
        var failedHTML = '';

        /**
         * Store jquery selectors.
         */
        var SELECTORS = {
            CHECKBOXES: '#xapierrorlog_form .form-check-input',
            CHECKBOX_DATEFROM: '#xapierrorlog_form #id_datefrom_enabled',
            CHECKBOX_DATETO: '#xapierrorlog_form #id_dateto_enabled',
            FORM: '#xapierrorlog_form .mform',
            PAGE_LINKS: '.pagination .page-item .page-link',
            REPLAY_EVENTS: '#xapierrorlog_data .reply-event',
            SELECTS: '#xapierrorlog_form .custom-select',
            SELECT_CONTEXT: '#xapierrorlog_form #id_eventcontext',
            SELECT_ERRORTYPE: '#xapierrorlog_form #id_errortype',
            SELECT_EVENTNAME: '#xapierrorlog_form #id_eventnames',
            SELECT_RESPONSE: '#xapierrorlog_form #id_response',
            SELECT_DATAFROM: '#xapierrorlog_form #id_datefrom .custom-select',
            SELECT_DATATO: '#xapierrorlog_form #id_dateto .custom-select',
            SEND_BUTTON: '#xapierrorlog_form #id_resendselected',
            SEND_CAN_DO: '#xapierrorlog_form input[name^="resend"]',
            SEND_ID: '#xapierrorlog_form input[name^="id"]',
            SUBMIT_FORM: '#xapierrorlog_form #id_submitbutton',
        };

        /**
         * Added prefix to the replay event id.
         */
        var REPLAY_EVENT_ID_PREFIX = 'reply-event-id-';

        var replayevents = {

            /**
             * Initialisation method called by php js_call_amd()
             * @param {Number} counts The number of events.
             * @param {Number} notResend Whether or not to resend the events.
             * @param {Number} Resend Appears to be unused. TODO: Remove.
             */
            init: function(counts, notResend, Resend) {
                countedEvents = counts;
                canNotResend = notResend;
                canResend = Resend;

                // Set resend variable always to not allowed state.
                $(SELECTORS.SEND_CAN_DO).val(canNotResend);

                // Set labels.
                if ($(SELECTORS.SEND_ID).val() == 1) {
                    labelConfirmTitle = 'confirmsendeventsheader';
                    labelConfirmContent = 'confirmsendevents';
                    labelResendButton = 'sendevents';
                }

                this.registerOnChangeSelectEvents();
                this.updateResend();
                this.registerResendEvent();

                this.addReplyEvents();
             },

            /**
             * Register reply an individual event listeners.
             */
            addReplyEvents: function() {
                if ($(SELECTORS.REPLAY_EVENTS).length == 0) {
                    return;
                }
                this.generateLoadHTML();
                this.generateDoneHTML();
                this.generateFailedHTML();
                this.generateReplayHTML();
                this.registerReplyEventListeners();
            },

            /**
             * Register reply an individual event listeners.
             */
            registerReplyEventListeners: function() {
                var self = this;

                $(SELECTORS.REPLAY_EVENTS).click(function(e) {
                    e.stopPropagation();
                    e.preventDefault();

                    self.disableFormControls();
                    self.disablePagination();

                    var element = $(this);

                    element.off('click');
                    element.addClass('disabled');

                    var id = element.attr('id');
                    var eventId = id.replace(REPLAY_EVENT_ID_PREFIX, '');

                    self.doReplayEvent(eventId);
                });
            },

            /**
             * Replay an individual event using ajax.
             * @param {Number} eventId The event id.
             */
            doReplayEvent: function(eventId) {
                var url = mdlcfg.wwwroot + '/admin/tool/log/store/xapi/ajax/moveback_to_log.php';
                var eventIds = [eventId];
                var self = this;
                var element = $('#' + REPLAY_EVENT_ID_PREFIX + eventId);
                var historical = $(SELECTORS.SEND_ID).val();

                element.empty();
                element.append(loadHTML);
                element.removeClass('reply-event');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'events': eventIds,
                        'historical': historical,
                        'sesskey': M.cfg.sesskey
                    },
                    success: function(data) {
                        element.empty();

                        if (data.success) {
                            element.append(doneHTML);
                            countedEvents--;
                            self.updateResend();
                        } else {
                            element.append(failedHTML);
                        }
                        self.enableFormControls();
                        self.enablePagination();
                    },
                    fail: function(ex) {
                        notification.exception(ex);

                        element.empty();
                        element.append(failedHTML);
                        self.enableFormControls();
                        self.enablePagination();
                    }
                });
            },

            /**
             * Disable resend event when select has been changed.
             */
            registerOnChangeSelectEvents: function() {
                var self = this;

                $(SELECTORS.SELECTS).change(function() {
                    selectorChanged = true;
                    self.disableResend();
                });
            },

            /**
             * Register click on resend button.
             */
            registerResendEvent: function() {
                var self = this;

                if (selectorChanged) {
                    return;
                }

                $(SELECTORS.SEND_BUTTON).click(function() {
                    self.disableFormControls();
                    self.disablePagination();

                    str.get_strings([
                        {
                            key: labelConfirmTitle,
                            component: 'logstore_xapi'
                        },
                        {
                            key: labelConfirmContent,
                            component: 'logstore_xapi',
                            param: {
                                count: countedEvents
                            }
                        },
                        {
                            key: 'yes',
                            component: 'moodle'
                        },
                        {
                            key: 'no',
                            component: 'moodle'
                        }
                    ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3],
                            function() {
                                $(SELECTORS.SEND_CAN_DO).val(canResend);
                                self.enableFormControls();
                                $(SELECTORS.FORM).submit();
                            },
                            function() {
                                self.enableFormControls();
                                self.enablePagination();
                            }
                        );
                    });
                });
            },

            /**
             * Update Send/Resend button label.
             */
            updateResend: function() {
                var element = $(SELECTORS.SEND_BUTTON);
                var self = this;

                str.get_strings([
                    {
                        key: labelResendButton,
                        component: 'logstore_xapi',
                        param: {
                            count: countedEvents
                        }
                    }
                ]).done(function(resend) {
                    element.attr('Value', countedEvents);
                    element.html(resend);

                    if (countedEvents != 0 && selectorChanged === false) {
                        self.enableResend();
                    }
                });
            },

            /**
             * Disable given elements.
             * @param {Array} elements The elements to disable.
             */
            disableElements: function(elements) {
                elements.addClass("disabled");
                elements.attr("disabled", "disabled");
                elements.prop('disabled', true);
            },

            /**
             * Enable given elements.
             * @param {Array} elements The elements to enable.
             */
            enableElements: function(elements) {
                elements.removeClass("disabled");
                elements.prop('disabled', false);
                elements.removeAttr("disabled");
            },

            /**
             * Disable page links.
             */
            disablePagination: function() {
                if ($(SELECTORS.PAGE_LINKS).length == 0) {
                    return;
                }

                var elements = $(SELECTORS.PAGE_LINKS);

                this.disableElements(elements);
                elements.bind('click', function(e) {
                    e.preventDefault();
                });
            },

            /**
             * Enable page links.
             */
            enablePagination: function() {
                if ($(SELECTORS.PAGE_LINKS).length == 0) {
                    return;
                }
                var elements = $(SELECTORS.PAGE_LINKS);

                this.enableElements(elements);
                elements.unbind('click');
            },

            /**
             * Disable Resend button.
             */
            disableResend: function() {
                this.disableElements($(SELECTORS.SEND_BUTTON));
            },

            /**
             * Enable Resend button.
             */
            enableResend: function() {
                this.enableElements($(SELECTORS.SEND_BUTTON));
            },

            /**
             * Disable submit form control.
             */
            disableReplyEvents: function() {
                $(SELECTORS.REPLAY_EVENTS).off('click');
                this.disableElements($(SELECTORS.REPLAY_EVENTS));
            },

            /**
             * Enable submit form control.
             */
            enableReplyEvents: function() {
                this.enableElements($(SELECTORS.REPLAY_EVENTS));
                this.registerReplyEventListeners();
            },

            /**
             * Disable submit form control.
             */
            disableFormSubmit: function() {
                this.disableElements($(SELECTORS.SUBMIT_FORM));
            },

            /**
             * Enable submit form control.
             */
            enableFormSubmit: function() {
                this.enableElements($(SELECTORS.SUBMIT_FORM));
            },

            /**
             * Disable form selects.
             */
            disableFormSelects: function() {
                this.disableElements($(SELECTORS.SELECTS));
            },

            /**
             * Enable form selects.
             */
            enableFormSelects: function() {
                this.enableElements($(SELECTORS.SELECT_ERRORTYPE));
                this.enableElements($(SELECTORS.SELECT_EVENTNAME));
                this.enableElements($(SELECTORS.SELECT_RESPONSE));
                this.enableElements($(SELECTORS.SELECT_CONTEXT));

                if ($(SELECTORS.CHECKBOX_DATEFROM).is(':checked')) {
                    this.enableElements($(SELECTORS.SELECT_DATAFROM));
                }

                if ($(SELECTORS.CHECKBOX_DATETO).is(':checked')) {
                    this.enableElements($(SELECTORS.SELECT_DATATO));
                }
            },

            /**
             * Disable form checkboxes.
             */
            disableFormCheckboxes: function() {
                this.disableElements($(SELECTORS.CHECKBOXES));
            },

            /**
             * Enable form checkboxes.
             */
            enableFormCheckboxes: function() {
                this.enableElements($(SELECTORS.CHECKBOXES));
            },

            /**
             * Disable form controls.
             */
            disableFormControls: function() {
                this.disableFormSubmit();
                this.disableFormCheckboxes();
                this.disableFormSelects();
                this.disableResend();
            },

            /**
             * Disable form controls.
             */
            enableFormControls: function() {
                this.enableFormCheckboxes();
                this.enableFormSelects();
                this.enableFormSubmit();
                this.enableResend();
            },

            /**
             * Generate load icon.
             */
            generateLoadHTML: function() {
                str.get_strings([
                    {
                        key: 'loadinghelp',
                        component: 'moodle'
                    }
                ]).done(function(loadStr) {
                    loadHTML = '<span aria-hidden="true"' +
                        ' class="fa fa-spinner fa-spin fa-pulse"' +
                        ' title="' + loadStr + '"></span>' +
                        '<span class="sr-only">' + loadStr + '</span>';
                });
            },

            /**
             * Generate done icon.
             */
            generateDoneHTML: function() {
                str.get_strings([
                    {
                        key: 'success',
                        component: 'moodle'
                    }
                ]).done(function(doneStr) {
                    doneHTML = '<span aria-hidden="true"' +
                        ' class="fa fa-check"' +
                        ' title="' + doneStr + '"></span>' +
                        '<span class="sr-only">' + doneStr + '</span>';
                });
            },

            /**
             * Generate failed icon.
             */
            generateFailedHTML: function() {
                str.get_strings([
                    {
                        key: 'failed',
                        component: 'logstore_xapi'
                    }
                ]).done(function(failedStr) {
                    failedHTML = '<span aria-hidden="true"' +
                        ' class="fa fa-remove"' +
                        ' title="' + failedStr + '"></span>' +
                        '<span class="sr-only">' + failedStr + '</span>';
                });
            },

            /**
             * Generate replay icon.
             */
            generateReplayHTML: function() {
                str.get_strings([
                    {
                        key: 'replayevent',
                        component: 'logstore_xapi'
                    }
                ]).done(function(replayStr) {
                    replayHTML = '<span aria-hidden="true"' +
                        ' class="fa fa-repeat"' +
                        ' title="' + replayStr + '"></span>' +
                        '<span class="sr-only">' + replayStr + ' </span>';
                    $(SELECTORS.REPLAY_EVENTS).append(replayHTML);
                });
            },
        };

        return replayevents;
});

