/**
 * Javascript controller for launching a Quickmark modal.
 *
 * @copyright Turnitin
 * @author 2024 Isaac Xiong <ixiong@turnitin.com>
 * @module plagiarism_turnitin/new_quickmark
 */

define(['jquery',
        'core/templates',
        'core/modal',
        'core/modal_events',
        'plagiarism_turnitin/modal_quickmark_launch',
    ],
    function($, Templates, Modal, ModalEvents, ModalQuickmarkLaunch) {
        return {
            newQuickmarkLaunch: function() {
                $('.plagiarism_turnitin_quickmark_manager_launch').on('click', function (event) {
                    event.preventDefault();
                    Modal.create({
                        type: ModalQuickmarkLaunch.TYPE,
                        template: ModalQuickmarkLaunch.TEMPLATE,
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