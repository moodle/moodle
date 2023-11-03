/**
 * Standard Report wrapper for Moodle. It calls the central JS file for Report plugin,
 * Also it includes JS libraries like Select2,Datatables and Highcharts
 * @module     block_learnerscript/report
 * @class      report
 * @package    block_learnerscript
 * @copyright  2017 Naveen kumar <naveen@eabyas.in>
 * @since      3.3
 */
define(['jquery', 'block_learnerscript/timeme'], function($, TimeMe) {
    var trackModule2;
    document.cookie = "time_timeme = 0 ;path=/";

    return trackModule2 = {
        timeme: function() {
            TimeMe.initialize({
                currentPageName: "", // Current page.
                idleTimeoutInSeconds: 10, // Stop recording time due to inactivity.
            });
            setInterval(function() {
                timeSpentOnPage = TimeMe.getTimeOnCurrentPageInSeconds();
                document.cookie = "time_timeme =" + timeSpentOnPage + ";path=/";
            }, 500);
            document.cookie = "time_timeme = 0 ;path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
        }
    };
});