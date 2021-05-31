/*
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_iomad_company_admin/admin
  */

define(['jquery', 'theme_boost/tab'], function($, tab) {
    return {
        init: function() {

            // Store ID of clicked tab on Dashboard
            $(".nav-link").on("click", function() {
                var id = $(this).attr("id");
                try {
                    localStorage.setItem("iomad-dashboard-tab", id);
                } catch (e) {
                    return;
                }
            });

            // Recover clicked tab on Dashboard
            try {
                var id = localStorage.getItem("iomad-dashboard-tab");
            } catch (e) {
                return;
            }
            if (id) {
                $('#' + id).tab('show');
            }

        }
    };
});
