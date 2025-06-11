define(['jquery', 'core/templates', 'core/ajax', 'core/notification', 'core/str', 'core/modal_factory', 'core/modal_events'],
    function($, templates, ajax, notification, Str, ModalFactory, ModalEvents) {
        return {
            init: function() {
                var trigger = $('#deleteuserfeedback');
                var gradeid = $(trigger).attr('gradeid');
                var contextid = $(trigger).attr('contextid');
                var userid = $(trigger).attr('userid');
                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: Str.get_string('deletefeedbackconfirm', 'assignfeedback_onenote'),
                    body: Str.get_string('deletefeedbackconfirmdetail', 'assignfeedback_onenote'),
                }, trigger)
                    .done(function(modal) {
                        modal.getRoot().on(ModalEvents.save, function(e) {
                            // Stop the default save button behaviour which is to close the modal.
                            e.preventDefault();
                            var requests = ajax.call([{
                                methodname: 'mod_assign_feedback_onenote_delete',
                                args: {contextid: contextid, gradeid: gradeid, userid: userid}
                            }]);

                            requests[0].done(function() {
                                location.reload();
                            }).fail(notification.exception);
                        });
                    });
            }
        };
    }
);
