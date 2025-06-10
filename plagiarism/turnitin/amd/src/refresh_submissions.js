/**
 * Javascript controller for refreshing the submissions.
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/refreshSubmissions
 */

define(['jquery'],
    function($) {
        return {
            refreshSubmissions: function() {
                $('.plagiarism_turnitin_refresh_grades').on('click', function() {
                    $('.plagiarism_turnitin_refresh_grades').hide();
                    $('.plagiarism_turnitin_refreshing_grades').show();

                    $.ajax({
                        type: "POST",
                        url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
                        dataType: "json",
                        data: {
                            action: "update_grade",
                            cmid: $('input[name="coursemodule"]').val(),
                            sesskey: M.cfg.sesskey
                        },
                        success: function() {
                            $('.plagiarism_turnitin_refresh_grades').show();
                            $('.plagiarism_turnitin_refreshing_grades').hide();
                        }
                    });
                });
            }
        };
    });