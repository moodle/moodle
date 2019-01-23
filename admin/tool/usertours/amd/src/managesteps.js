/**
 * Step management code.
 *
 * @module     tool_usertours/managesteps
 * @class      managesteps
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
define(
['jquery', 'core/str', 'core/notification'],
function($, str, notification) {
    var manager = {
        /**
         * Confirm removal of the specified step.
         *
         * @method  removeStep
         * @param   {EventFacade}   e   The EventFacade
         */
        removeStep: function(e) {
            e.preventDefault();
            var targetUrl = $(e.currentTarget).attr('href');
            str.get_strings([
                {
                    key:        'confirmstepremovaltitle',
                    component:  'tool_usertours'
                },
                {
                    key:        'confirmstepremovalquestion',
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
         * Setup the step management UI.
         *
         * @method          setup
         */
        setup: function() {

            $('body').delegate('[data-action="delete"]', 'click', manager.removeStep);
        }
    };

    return /** @alias module:tool_usertours/managesteps */ {
        /**
         * Setup the step management UI.
         *
         * @method          setup
         */
        setup: manager.setup
    };
});
