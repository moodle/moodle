/**
 * Javascript controller for launching the EULA modal.
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/eulaLaunch
 */

define(['jquery',
        'core/templates',
        'core/modal_factory',
        'core/modal_events',
        'plagiarism_turnitin/modal_eula_launch'
    ],
    function($, Templates, ModalFactory, ModalEvents, ModalEulaLaunch) {
        return {
            eulaLaunch: function() {
                var turnitinEulaClass = $(".pp_turnitin_eula");
                turnitinEulaClass.show();

                // Show the 'accept EULA' prompt for new in-page forum replies.
                $(document).on('mod_forum-post-created', '.forum-post-container', function (event, newid) {
                    var turnitinEulaClass = $("#post-content-" + newid + " .pp_turnitin_eula");
                    turnitinEulaClass.show();
                });

                $(document).on('click', '.pp_turnitin_eula_link', function() {
                    ModalFactory.create({
                        type: ModalEulaLaunch.TYPE,
                        templateContext: {
                            cmid: $('input[name="coursemodule"]').val(),
                            wwwroot: M.cfg.wwwroot
                        },
                        large: true
                    })
                        .then(function (modal) {
                            modal.show();
                            modal.getRoot().find('.modal').addClass('tii_pp_modal_eula');
                            modal.getRoot().find('.modal-content').addClass('tii_pp_modal_eula_content');
                        });
                });

                // Hide the submission form if the user has never accepted or declined the Turnitin EULA.
                if ($(".pp_turnitin_eula_ignored").length > 0) {
                    if ($('.editsubmissionform').length > 0) {
                        $('.editsubmissionform').hide();
                    }
                    if (turnitinEulaClass.siblings('.mform').length > 0) {
                        turnitinEulaClass.siblings('.mform').hide();
                    }
                }
            }
        };
    });