/**
 * Javascript controller for resubmissions.
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/resendSubmission
 */

define(['jquery'], function($) {
    return {
        resendSubmission: function() {
            // Create new event for submission to be re-sent to Turnitin.
            $(document).on('click', '.plagiarism_turnitin_resubmit_link', function() {
                $(this).hide();
                $(this).siblings('.pp_resubmitting').removeClass('hidden');
                var that = $(this);

                var submissionid = $(this).prop('id').split("_")[2];
                var forumpost = $('#content_' + submissionid).html();
                var forumdata = $('#forumdata_' + submissionid).html();

                $.ajax({
                    type: "POST",
                    url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
                    dataType: "json",
                    data: {
                        action: "resubmit_event",
                        submissionid: submissionid,
                        forumpost: forumpost,
                        forumdata: forumdata,
                        sesskey: M.cfg.sesskey
                    },
                    success: function() {
                        that.siblings('.turnitin_status').removeClass('hidden');
                        that.siblings('.pp_resubmitting').addClass('hidden');
                    },
                    error: function() {
                        that.show();
                        that.siblings('.pp_resubmitting').addClass('hidden');
                    }
                });

                return false;
            });
        }
    };
});
