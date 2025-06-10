/**
 * Javascript controller for launching a Peermark modal.
 *
 * @copyright Turnitin
 * @author 2024 Isaac Xiong <ixiong@turnitin.com>
 * @module plagiarism_turnitin/new_peermark
 */

define(['jquery',
        'core/templates',
        'core/modal',
        'core/modal_events',
        'plagiarism_turnitin/modal_peermark_manager_launch',
        'plagiarism_turnitin/modal_peermark_reviews_launch'
    ],
    function($, Templates, Modal, ModalEvents, ModalPeermarkManagerLaunch, ModalPeermarkReviewsLaunch) {
        return {
            newPeermarkLaunch: function() {
                var that = this;
                $('.peermark_manager_launch').on('click', function(event) {
                    event.preventDefault();
                    that.peermarkModalCreate(ModalPeermarkManagerLaunch);
                });

                $(document).on('click', '.peermark_reviews_pp_launch', function() {
                    that.peermarkModalCreate(ModalPeermarkReviewsLaunch);
                });
            },
            peermarkModalCreate: function(modalType) {

                if ($('input[name="coursemodule"]').val()) {
                    var cmid = $('input[name="coursemodule"]').val();
                } else {
                    var urlParams = new URLSearchParams(window.location.search);
                    var cmid = urlParams.get('id');
                }
                Modal.create({
                    type: modalType.TYPE,
                    template: modalType.TEMPLATE,
                    templateContext: {
                        cmid: cmid,
                        wwwroot: M.cfg.wwwroot
                    },
                    large: true
                })
                    .then(function (modal) {
                        modal.show();
                    });
            }
        };
    });