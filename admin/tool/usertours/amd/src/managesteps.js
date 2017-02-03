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
            ]).done(function(s) {
                notification.confirm(s[0], s[1], s[2], s[3], $.proxy(function() {
                    window.location = $(this).attr('href');
                }, e.currentTarget));
            });
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
