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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    https://intelliboard.net/
 */
define([
    'jquery',
    'core/log',
    'local_intellidata/tracking_repository',
    'local_intellidata/cookies_helper'
],
function(
    $,
    log,
    TrackingRepository,
    CookiesHelper
) {

    var PARAMS = {
        AJAXFREQUENCY: 30,
        INACTIVITY: 60,
        PERIOD: 1000,
        INTERVAL: null,
        PAGE: '',
        PARAM: '',
        MEDIATRACK: 0,
    };

    var TRACKING = {
        COUNTER: 0,
        AJAXCOUNTER: 0,
        WARNINGTIME: 0,
        LOGOUTTIME: 0
    };

    var registerParams = function(params) {
        PARAMS.INACTIVITY = params.inactivity || PARAMS.INACTIVITY;
        PARAMS.AJAXFREQUENCY = params.ajaxfrequency || PARAMS.AJAXFREQUENCY;
        PARAMS.PERIOD = params.period || PARAMS.PERIOD;
        PARAMS.PAGE = params.page || PARAMS.PAGE;
        PARAMS.PARAM = params.param || PARAMS.PARAM;
        PARAMS.MEDIATRACK = params.mediatrack || PARAMS.MEDIATRACK;
        log.debug('IntelliData: Set Params', [PARAMS, TRACKING]);
    };

    var registerEventListeners = function() {
        $(document).on("mousemove", clearCounter);
        $(document).on("keypress", clearCounter);
        $(document).on("scroll", clearCounter);
        $(window).on("unload", resetParams);
    };

    var clearCounter = function() {
        TRACKING.COUNTER = 0;
        TRACKING.WARNINGTIME = 0;
        TRACKING.LOGOUTTIME = 0;
    };

    var resetParams = function() {
        CookiesHelper.setCookie('intellidatapage', PARAMS.PAGE);
        CookiesHelper.setCookie('intellidataparam', PARAMS.PARAM);
        CookiesHelper.setCookie('intellidatatime', PARAMS.AJAXFREQUENCY);
    };

    var initTracking = function() {
        setInterval(track, PARAMS.PERIOD);
        log.debug('IntelliData: Start Tracking', [PARAMS, TRACKING]);
    };

    var track = function() {
        if (PARAMS.MEDIATRACK) {
            var status = mediaTracking();
            if (status && !document.hidden) {
                clearCounter();
            }
        }
        if (TRACKING.COUNTER <= PARAMS.INACTIVITY) {
            TRACKING.COUNTER++;
            TRACKING.AJAXCOUNTER++;

            if (TRACKING.AJAXCOUNTER == PARAMS.AJAXFREQUENCY && PARAMS.AJAXFREQUENCY) {
                TrackingRepository.sendRequest(PARAMS.PAGE, PARAMS.PARAM);
                TRACKING.AJAXCOUNTER = 0;
            }
        }
    };

    var mediaTracking = function(){
        var media = [];
        var status = false;
        var internal = document.querySelectorAll('audio,video');
        var frames = document.querySelectorAll('iframe');
        if (frames.length) {
            frames.forEach(function(frame) {
                var elements = frame.contentWindow.document.querySelectorAll('audio,video');
                if (elements.length) {
                    elements.forEach(function(element) {
                        media.push(element);
                    });
                }
            });
        }
        if (internal.length) {
            internal.forEach(function(element) {
                media.push(element);
            });
        }

        if (media.length) {
            media.forEach(function(element) {
                if (!element.paused) {
                    status = true;
                }
            });
        }

        return status;
    };

    return {
        init: function(params) {
            registerParams(params);
            registerEventListeners();
            initTracking();
        }
    };
});
