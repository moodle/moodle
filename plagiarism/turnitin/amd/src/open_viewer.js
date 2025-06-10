/**
 * Javascript controller for opening the originality report viewreport

 * @copyright 2019 Turnitin
 * @author Charlotte Spinks <cspinks@turnitin.com>
 * @module plagiarism_turnitin/open_viewer
 */

define(['jquery',
        'core/notification'
       ], function($) {
    return {
        origreport_open: function() {
            var that = this;
            $(document).off('click', '.pp_origreport_open').on('click', '.pp_origreport_open', function() {
                var classList = $(this).attr('class').replace(/\s+/,' ').split(' ');

                for (var i = 0; i < classList.length; i++) {
                    if (classList[i].indexOf('origreport_') !== -1 && classList[i] != 'pp_origreport_open') {
                        var classStr = classList[i].split("_");
                        that.openDV("origreport", classStr[1], classStr[2]);
                    }
                }
             });
        },

        grademark_open: function() {
            var that = this;
            $(document).off('click', '.pp_grademark_open').on('click', '.pp_grademark_open', function() {
                var classList = $(this).attr('class').replace(/\s+/,' ').split(' ');

                for (var i = 0; i < classList.length; i++) {
                    if (classList[i].indexOf('grademark_') !== -1 && classList[i] != 'pp_grademark_open') {
                        var classStr = classList[i].split("_");
                        that.openDV("grademark", classStr[1], classStr[2]);
                    }
                }
            });
        },

        // Open the DV in a new window in such a way as to not be blocked by popups.
        openDV: function(dvtype, submissionid, coursemoduleid) {
          var that = this;
          var dvWindow = window.open('', 'turnitin_viewer');

          var loading = '<div class="tii_dv_loading" style="text-align:center;">';
          var icon = '/plagiarism/turnitin/pix/turnitin-icon.png';
          loading += '<img src="' + M.cfg.wwwroot + icon +'" style="width:100px; height: 100px">';
          loading += '<p style="font-family: Arial, Helvetica, sans-serif;">' + M.str.plagiarism_turnitin.loadingdv + '</p>';
          loading += '</div>';
          $(dvWindow.document.body).html(loading);

          // Get html to launch DV.
          $.ajax({
              type: "POST",
              url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
              dataType: "json",
              data: {
                  action: "get_dv_html",
                  submissionid: submissionid,
                  dvtype: dvtype,
                  cmid: coursemoduleid,
                  sesskey: M.cfg.sesskey
              },
              success: function(data) {
                  $(dvWindow.document.body).html(loading + data);
                  dvWindow.document.forms[0].submit();
                  dvWindow.document.close();

                  that.checkDVClosed(submissionid, coursemoduleid, dvWindow);
              }
          });
        },

        checkDVClosed: function(submissionid, coursemoduleid, dvWindow) {
            var that = this;

            if (dvWindow.closed) {
                that.refreshScores(submissionid, coursemoduleid);
            } else {
                setTimeout( function(){
                    that.checkDVClosed(submissionid, coursemoduleid, dvWindow);
                }, 500);
            }
        },

        refreshScores: function(submission_id, coursemoduleid) {
            var refreshStartTime = new Date().getTime();
            $.ajax({
                type: "POST",
                url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
                dataType: "json",
                data: {
                    action: "update_grade",
                    submission: submission_id,
                    cmid: coursemoduleid,
                    sesskey: M.cfg.sesskey
                },
                success: function() {
                    var requestDuration = new Date().getTime() - refreshStartTime;
                    if (requestDuration < 3000) {
                        window.location = window.location + '';
                    } else {
                        Notification.addNotification({
                          message: M.str.plagiarism_turnitin.turnitin_score_refresh_alert,
                          type: "warning"
                        });
                    }
                }
            });
        }
    };
});
