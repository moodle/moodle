/*
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_iomad_company_admin/company_capabilities
  */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    return {
        init: function(companyid, templateid, roleid) {

            $(".capabilities-checkbox").on('change', function() {
                var capability = $(this).data("capability");
                var checked = $(this).prop('checked');
                var allow = (checked == 'true') ? 1 : 0;
                console.log(roleid);
                ajax.call([{
                    methodname : 'block_iomad_company_admin_restrict_capability',
                    args : {
                        capability : capability,
                        roleid : roleid,
                        companyid : companyid,
                        allow : allow,
                        templateid: templateid
                    },
                    fail: notification.exception
                }])
            })

        }
    };
});
