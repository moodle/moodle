/**
 * Javascript controller for the Rubric Manager launcher
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/modal_rubric_manager_launch
 */

define(
    [
        'jquery',
        'core/ajax',
        'core/notification',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_registry',
        'core/modal_events'
    ],
    function($, Ajax, Notification, CustomEvents, Modal, ModalRegistry, ModalEvents) {

        var registered = false;
        var SELECTORS = {
            HIDE_BUTTON: '[data-action="hide"]',
            MODAL: '[data-region="modal"]'
        };

        /**
         * Constructor for the Modal.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ModalRubricManagerLaunch = function(root) {
            Modal.call(this, root);
        };

        ModalRubricManagerLaunch.TYPE = 'plagiarism_turnitin-modal_rubric_manager_launch';
        ModalRubricManagerLaunch.TEMPLATE = 'plagiarism_turnitin/modal_rubric_manager_launch';
        ModalRubricManagerLaunch.prototype = Object.create(Modal.prototype);
        ModalRubricManagerLaunch.prototype.constructor = ModalRubricManagerLaunch;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalRubricManagerLaunch.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            // On cancel, then hide the modal.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE_BUTTON, function(e, data) {
                var cancelEvent = $.Event(ModalEvents.cancel);
                this.getRoot().trigger(cancelEvent, this);

                if (!cancelEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();

                    refreshRubricSelect();
                }
            }.bind(this));

            // On clicking outside the modal, refresh the Rubrics.
            this.getRoot().click(function(e) {
                if (!$(e.target).closest(SELECTORS.MODAL).length) {
                    refreshRubricSelect();
                }
            }.bind(this));
        };

        /**
         * Get the rubrics belonging to a user from Turnitin and refresh menu accordingly.
         */
        function refreshRubricSelect() {
            var currentRubric = $('#id_plagiarism_rubric').val();
            $.ajax({
                type: "POST",
                url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
                dataType: 'json',
                data: {
                    action: "refresh_rubric_select", assignment: $('input[name="instance"]').val(),
                    modulename: $('input[name="modulename"]').val(), course: $('input[name="course"]').val()
                },
                success: function (data) {
                    $($('#id_plagiarism_rubric')).empty();
                    var options = data;
                    $.each(options, function (i, val) {
                        if (!$.isNumeric(i) && i !== "") {

                            var optgroup = $('<optgroup>');
                            optgroup.attr('label', i);

                            $.each(val, function (j, rubric) {
                                var option = $("<option></option>");
                                option.val(j);
                                option.text(rubric);

                                optgroup.append(option);
                            });

                            $('#id_plagiarism_rubric').append(optgroup);

                        } else {
                            $($('#id_plagiarism_rubric')).append($('<option>', {
                                value: i,
                                text: val
                            }));
                        }
                    });

                    $('#id_plagiarism_rubric' + ' option[value="' + currentRubric + '"]').attr("selected", "selected");
                }
            });
        }

        ModalRubricManagerLaunch.refreshRubricSelect = refreshRubricSelect;

        // Automatically register with the modal registry the first time this module is imported so that
        // you can create modals of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ModalRubricManagerLaunch.TYPE,
                ModalRubricManagerLaunch,
                'plagiarism_turnitin/modal_rubric_manager_launch');
            registered = true;
        }

        return ModalRubricManagerLaunch;
    }
);