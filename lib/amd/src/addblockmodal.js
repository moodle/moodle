// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Show an add block modal instead of doing it on a separate page.
 *
 * @module     core/addblockmodal
 * @class      addblockmodal
 * @package    core
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'core/templates', 'core/str', 'core/notification'],
       function($, ModalFactory, Templates, Str, Notification) {


    return /** @alias module:core/addblockmodal */ {
        /**
         * Global init function for this module.
         *
         * @method init
         * @param {Object} context The template context for rendering this modal body.
         */
        init: function(context) {
            var addblocklink = $('[data-key=addblock]');

            // We need the fetch the names of the blocks. It was too much to send in the page.
            Str.get_string('addblock').done(function(title) {

                var titlerequests = [];
                $.each(context.blocks, function(index, key) {
                    titlerequests[titlerequests.length] = {key: 'pluginname', component: 'block_' + key};
                });

                var blocks = [];
                Str.get_strings(titlerequests).done(function(titles) {
                    $.each(titles, function(index, title) {
                        blocks[blocks.length] = {name: context.blocks[index], title: title};
                    });

                    context.blocks = blocks;

                    ModalFactory.create({
                        title: title,
                        body: Templates.render('core/add_block_body', context),
                        type: 'CANCEL',
                    }, addblocklink);
                });
            }).fail(Notification.exception);
        }
    };
});
