/**
 * Tour management code.
 *
 * @module     tool_usertours/managetours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
define(
['jquery', 'core/ajax', 'core/str', 'core/notification'],
function($, ajax, str, notification) {
    var manager = {
        /**
         * Confirm removal of the specified tour.
         *
         * @method  removeTour
         * @param   {EventFacade}   e   The EventFacade
         */
        removeTour: function(e) {
            e.preventDefault();
            var targetUrl = $(e.currentTarget).attr('href');
            str.get_strings([
                {
                    key:        'confirmtourremovaltitle',
                    component:  'tool_usertours'
                },
                {
                    key:        'confirmtourremovalquestion',
                    component:  'tool_usertours'
                },
                {
                    key:        'yes',
                    component:  'moodle'
                },
                {
                    key:        'no',
                    component:  'moodle'
                }
            ])
            .then(function(s) {
                notification.confirm(s[0], s[1], s[2], s[3], function() {
                    window.location = targetUrl;
                });

                return;
            })
            .catch();
        },

        /**
         * Setup the tour management UI.
         *
         * @method          setup
         */
        setup: function() {
            $('body').delegate('[data-action="delete"]', 'click', manager.removeTour);
        }
    };

    return /** @alias module:tool_usertours/managetours */ {
        /**
         * Setup the tour management UI.
         *
         * @method          setup
         */
        setup: manager.setup
    };
});
