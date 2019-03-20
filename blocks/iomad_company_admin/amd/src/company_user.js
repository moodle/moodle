/*
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_iomad_company_admin/company_user
  */

define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/str'],
    function($, ajax, notification, ModalFactory, ModalEvents, str) {
    return {
        init: function() {

            // License option updated
            $('#licenseidselector').on('change', function() {
                console.log('Got it!')
            })
        }
    };
});
