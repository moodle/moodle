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
 * Video JS loader.
 *
 * This takes care of applying the filter on content which was dynamically loaded.
 *
 * @package    media_videojs
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/event'], function($, Event) {

    /**
     * Stores the method we need to execute on the first load of videojs module.
     */
    var onload;

    /**
     * Set-up.
     *
     * Adds the listener for the event to then notify video.js.
     * @param {Function} executeonload function to execute when media_videojs/video is loaded
     */
    var setUp = function(executeonload) {
        onload = executeonload;
        // Notify Video.js about the nodes already present on the page.
        notifyVideoJS(null, $('body'));
        // We need to call popover automatically if nodes are added to the page later.
        Event.getLegacyEvents().done(function(events) {
            $(document).on(events.FILTER_CONTENT_UPDATED, notifyVideoJS);
        });
    };

    /**
     * Notify video.js of new nodes.
     *
     * @param {Event} e The event.
     * @param {NodeList} nodes List of new nodes.
     */
    var notifyVideoJS = function(e, nodes) {
        var selector = '.mediaplugin_videojs';

        // Find the descendants matching the expected parent of the audio and video
        // tags. Then also addBack the nodes matching the same selector. Finally,
        // we find the audio and video tags contained in those parents. Kind thanks
        // to jQuery for the simplicity.
        nodes.find(selector)
            .addBack(selector)
            .find('audio, video').each(function() {
                var id = $(this).attr('id'),
                    config = $(this).data('setup'),
                    modules = ['media_videojs/video-lazy'];

                if (config.techOrder && config.techOrder.indexOf('youtube') !== -1) {
                    // Add YouTube to the list of modules we require.
                    modules.push('media_videojs/Youtube-lazy');
                }
                require(modules, function(videojs) {
                    if (onload) {
                        onload(videojs);
                        onload = null;
                    }
                    videojs(id, config);
                });
            });
    };

    return {
        setUp: setUp
    };

});
