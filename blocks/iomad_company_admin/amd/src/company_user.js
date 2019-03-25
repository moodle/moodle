/*
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_iomad_company_admin/company_user
  */

define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/str', 'core/templates'],
    function($, ajax, notification, ModalFactory, ModalEvents, str, templates) {
    return {
        init: function() {

            // License option updated
            $('#licenseidselector').on('change', function() {
                var licenseid = $(this).val();
                if (licenseid == 0) {
                    $('#licensecoursescontainer').addClass('invisible');
                    return;
                }

                // Get license details
                ajax.call([{
                    methodname: 'block_iomad_company_admin_get_license_from_id',
                    args: {
                        licenseid: licenseid
                    },
                    done: function(licensedata) {
                        $('#licensecoursescontainer').removeClass('invisible');

                        templates.render('block_iomad_company_admin/licensecourseselector', licensedata)
                            .then(function(html) {
                                $("#licensecourseselector").html(html);

                            })
                            .fail(notification.exception);

                        templates.render('block_iomad_company_admin/licensedetails', licensedata)
                            .then(function(html) {
                                $("#licensedetails").html(html);
                            })
                            .fail(notification.exception);

                    },
                    fail: notification.exception
                }]);
            });
        }
    };
});
