define(['jquery', 'core/modal_factory','core/modal_events', 'core/fragment', 'core/config', 'core/str', 'core/toast'], 
function($, ModalFactory, ModalEvents, Fragment, Config, Str, Toast) {
    
    $("a.qbitsenrlusr").on('click', function(e) {
      var clickedLink = $(e.currentTarget);
      let contextId = clickedLink.data('context-id');
      let siteId = clickedLink.data("site-id");
      let enfrgHtml = Fragment.loadFragment('local_qubitscourse', 'enrol_users_form', contextId, {"siteId" : siteId});
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: 'Enrol Users',
            body: enfrgHtml,
            large: true
        })
        .then(function(modal) {
            var root = modal.getRoot();
            root.on(ModalEvents.save, function(e) {
                var elementid = clickedLink.data('context-id');
                e.preventDefault();
                modal.getRoot().find('form').submit();
            });

            modal.getRoot().on('submit', 'form', e => {
                e.preventDefault();
                submitFormAjax(modal);
            });

            modal.getRoot().on(ModalEvents.hidden, () => {
                modal.destroy();
            });
            modal.show();
       });
    });

    const Selectors = {
        cohortSelector: "#id_cohortlist",
        triggerButtons: ".enrolusersbutton.enrol_manual_plugin [type='submit']",
        unwantedHiddenFields: "input[value='_qf__force_multiselect_submission']",
        buttonWrapper: '[data-region="wrapper"]',
    };
    
    const submitFormAjax = (modal) => {
        const form = modal.getRoot().find('form');
        form.get(0).querySelectorAll(Selectors.unwantedHiddenFields).forEach(hiddenField => hiddenField.remove());
        modal.hide();
        modal.destroy();
    
        $.ajax(
            `${Config.wwwroot}/local/qubitsuser/enrol_manual_ajax.php?${form.serialize()}`,
            {
                type: 'GET',
                processData: false,
                contentType: "application/json",
            }
        )
        .then(response => {
            if (response.error) {
                throw new Error(response.error);
            }
    
            return response.count;
        })
        .then(count => {
            return Promise.all([
                Str.get_string('totalenrolledusers', 'enrol', count)
            ]);
        })
        .then(([notificationBody]) => notificationBody)
        .then(notificationBody => Toast.add(notificationBody))
        .catch(error => {
            Notification.addNotification({
                message: error.message,
                type: 'error',
            });
        });
    };

});

