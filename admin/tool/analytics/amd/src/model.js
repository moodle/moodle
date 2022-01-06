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
 * AMD module for model actions confirmation.
 *
 * @module     tool_analytics/model
 * @copyright  2017 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/log', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/templates'],
    function($, Str, log, Notification, ModalFactory, ModalEvents, Templates) {

    /**
     * List of actions that require confirmation and confirmation message.
     */
    var actionsList = {
        clear: {
            title: {
                key: 'clearpredictions',
                component: 'tool_analytics'
            }, body: {
                key: 'clearmodelpredictions',
                component: 'tool_analytics'
            }

        },
        'delete': {
            title: {
                key: 'delete',
                component: 'tool_analytics'
            }, body: {
                key: 'deletemodelconfirmation',
                component: 'tool_analytics'
            }
        }
    };

    /**
     * Returns the model name.
     *
     * @param {Object} actionItem The action item DOM node.
     * @return {String}
     */
    var getModelName = function(actionItem) {
        var wrap = $(actionItem).closest('[data-model-name]');

        if (wrap.length) {
            return wrap.attr('data-model-name');

        } else {
            log.error('Unexpected DOM error - unable to obtain the model name');
            return '';
        }
    };

    /** @alias module:tool_analytics/model */
    return {

        /**
         * Displays a confirm modal window before executing the action.
         *
         * @param {String} actionId
         * @param {String} actionType
         */
        confirmAction: function(actionId, actionType) {
            $('[data-action-id="' + actionId + '"]').on('click', function(ev) {
                ev.preventDefault();

                var a = $(ev.currentTarget);

                if (typeof actionsList[actionType] === "undefined") {
                    log.error('Action "' + actionType + '" is not allowed.');
                    return;
                }

                var reqStrings = [
                    actionsList[actionType].title,
                    actionsList[actionType].body
                ];
                reqStrings[1].param = getModelName(a);

                var stringsPromise = Str.get_strings(reqStrings);
                var modalPromise = ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL});

                $.when(stringsPromise, modalPromise).then(function(strings, modal) {
                    modal.setTitle(strings[0]);
                    modal.setBody(strings[1]);
                    modal.setSaveButtonText(strings[0]);
                    modal.getRoot().on(ModalEvents.save, function() {
                        window.location.href = a.attr('href');
                    });
                    modal.show();
                    return modal;
                }).fail(Notification.exception);
            });
        },

        /**
         * Displays evaluation mode and time-splitting method choices.
         *
         * @param  {String}  actionId
         * @param  {Boolean} trainedOnlyExternally
         */
        selectEvaluationOptions: function(actionId, trainedOnlyExternally) {
            $('[data-action-id="' + actionId + '"]').on('click', function(ev) {
                ev.preventDefault();

                var a = $(ev.currentTarget);

                var timeSplittingMethods = $(this).attr('data-timesplitting-methods');

                var stringsPromise = Str.get_strings([
                    {
                        key: 'evaluatemodel',
                        component: 'tool_analytics'
                    }, {
                        key: 'evaluate',
                        component: 'tool_analytics'
                    }
                ]);
                var modalPromise = ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL});
                var bodyPromise = Templates.render('tool_analytics/evaluation_options', {
                    trainedexternally: trainedOnlyExternally,
                    timesplittingmethods: JSON.parse(timeSplittingMethods)
                });

                $.when(stringsPromise, modalPromise).then(function(strings, modal) {


                    modal.getRoot().on(ModalEvents.hidden, modal.destroy.bind(modal));

                    modal.setTitle(strings[0]);
                    modal.setSaveButtonText(strings[1]);
                    modal.setBody(bodyPromise);

                    modal.getRoot().on(ModalEvents.save, function() {

                        // Evaluation mode.
                        var evaluationMode = $("input[name='evaluationmode']:checked").val();
                        if (evaluationMode == 'trainedmodel') {
                            a.attr('href', a.attr('href') + '&mode=trainedmodel');
                        }

                        // Selected time-splitting id.
                        var timeSplittingMethod = $("#id-evaluation-timesplitting").val();
                        a.attr('href', a.attr('href') + '&timesplitting=' + timeSplittingMethod);

                        window.location.href = a.attr('href');
                        return;
                    });

                    modal.show();
                    return modal;
                }).fail(Notification.exception);
            });
        },

        /**
         * Displays export options.
         *
         * We have two main options: export training data and export configuration.
         * The 2nd option has an extra option: include the trained algorithm weights.
         *
         * @param  {String}  actionId
         * @param  {Boolean} isTrained
         */
        selectExportOptions: function(actionId, isTrained) {
            $('[data-action-id="' + actionId + '"]').on('click', function(ev) {
                ev.preventDefault();

                var a = $(ev.currentTarget);

                if (!isTrained) {
                    // Export the model configuration if the model is not trained. We can't export anything else.
                    a.attr('href', a.attr('href') + '&action=exportmodel&includeweights=0');
                    window.location.href = a.attr('href');
                    return;
                }

                var stringsPromise = Str.get_strings([
                    {
                        key: 'export',
                        component: 'tool_analytics'
                    }
                ]);
                var modalPromise = ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL});
                var bodyPromise = Templates.render('tool_analytics/export_options', {});

                $.when(stringsPromise, modalPromise).then(function(strings, modal) {

                    modal.getRoot().on(ModalEvents.hidden, modal.destroy.bind(modal));

                    modal.setTitle(strings[0]);
                    modal.setSaveButtonText(strings[0]);
                    modal.setBody(bodyPromise);

                    modal.getRoot().on(ModalEvents.save, function() {

                        var exportOption = $("input[name='exportoption']:checked").val();

                        if (exportOption == 'exportdata') {
                            a.attr('href', a.attr('href') + '&action=exportdata');

                        } else {
                            a.attr('href', a.attr('href') + '&action=exportmodel');
                            if ($("#id-includeweights").is(':checked')) {
                                a.attr('href', a.attr('href') + '&includeweights=1');
                            } else {
                                a.attr('href', a.attr('href') + '&includeweights=0');
                            }
                        }

                        window.location.href = a.attr('href');
                        return;
                    });

                    modal.show();
                    return modal;
                }).fail(Notification.exception);
            });
        }
    };
});
