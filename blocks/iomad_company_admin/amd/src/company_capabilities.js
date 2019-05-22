/*
 * @package    block_iomad_company_admin
 * @copyright  2019 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module block_iomad_company_admin/company_capabilities
  */

define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/str'],
    function($, ajax, notification, ModalFactory, ModalEvents, str) {
    return {
        init: function(companyid, templateid, roleid) {

            // Set/unset capability checkbox
            $(".capabilities-checkbox").on('change', function() {
                var capability = $(this).data("capability");
                var checked = $(this).prop('checked');
                ajax.call([{
                    methodname : 'block_iomad_company_admin_restrict_capability',
                    args : {
                        capability : capability,
                        roleid : roleid,
                        companyid : companyid,
                        allow : checked,
                        templateid: templateid
                    },
                    fail: notification.exception
                }]);
            });

            // Template delete button.
            // Note - have to manually show modal as modal events don't save the original
            // clicked link :(
            $('a.template-delete').on('click', function(e) {
                var clickedLink = $(e.currentTarget);
                var strings = [
                    {
                        key: 'deleteroletemplatefull',
                        component: 'block_iomad_company_admin'
                    },
                    {
                        key: 'deleteroletemplate',
                        component: 'block_iomad_company_admin'
                    },
                    {
                        key: 'delete'
                    }
                ];
                str.get_strings(strings).then(function(strresults) {
                    ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        title: strresults[1] + '  "<b>' + clickedLink.data('name') + '</b>"',
                        body: strresults[0],
                        })
                        .then(function(modal) {
                            modal.setSaveButtonText(strresults[2]);
                            var root = modal.getRoot();
                            root.on(ModalEvents.save, function() {
                                var templateid = clickedLink.data('templateid');
                                ajax.call([{
                                    methodname: 'block_iomad_company_admin_capability_delete_template',
                                    args : {
                                        templateid: templateid
                                    },
                                    done: function() {
                                        location.reload();
                                    },
                                    fail: notification.exception
                                }]);
                            });
                            modal.show();
                        });
                    });
            });
        }
    };
});
