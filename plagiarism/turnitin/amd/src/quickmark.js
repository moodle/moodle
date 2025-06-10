/**
 * Javascript controller for launching a Quickmark modal.
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/quickmark
 */

define(['jquery',
        'core/templates',
        'core/modal_factory',
        'core/modal_events',
        'plagiarism_turnitin/modal_quickmark_launch'
    ],
    function($, Templates, ModalFactory, ModalEvents, ModalQuickmarkLaunch) {
        return {
            quickmarkLaunch: function() {
                $('.plagiarism_turnitin_quickmark_manager_launch').on('click', function (event) {
                    event.preventDefault();
                    ModalFactory.create({
                        type: ModalQuickmarkLaunch.TYPE,
                        templateContext: {
                            cmid: $('input[name="coursemodule"]').val(),
                            wwwroot: M.cfg.wwwroot
                        },
                        large: true
                    })
                        .then(function (modal) {
                            modal.show();
                        });
                });
            }
        };
    });