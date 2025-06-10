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
 * @package local_o365
 * @author Enovation
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$(function () {

    $.fn.moodlesetup = function (options) {
        var defaultopts = {
            url: 'localhost',
            iconsuccess: '',
            iconinfo: '',
            iconerror: '',

            strcheck: 'Check Moodle settings',
            strchecking: 'Checking...',
            strnoinfo: 'Something went wrong. Please contact your system administrator.',


        };
        var opts = $.extend({}, defaultopts, options);
        var main = this;
        this.setupmoodlebtn = this.find('button.setupmoodle');


        /**
         * Render an error box.
         *
         * @param string content HTML to use as box body.
         * @return object jQuery object representing rendered box.
         */
        this.rendererrorbox = function (content) {
            var box = $('<div></div>').addClass('alert-error alert local_o365_statusmessage');
            box.append(opts.iconerror);
            box.append('<span style="inline-block">' + content + '</span>');
            return box;
        }

        /**
         * Render an info box.
         *
         * @param string content HTML to use as box body.
         * @return object jQuery object representing rendered box.
         */
        this.renderinfobox = function (content) {
            var box = $('<div></div>').addClass('alert-info alert local_o365_statusmessage');
            box.append(opts.iconinfo);
            box.append('<span style="inline-block">' + content + '</span>');
            return box;
        }

        /**
         * Render an success box.
         *
         * @param string content HTML to use as box body.
         * @return object jQuery object representing rendered box.
         */
        this.rendersuccessbox = function (content) {
            var box = $('<div></div>').addClass('alert-success alert local_o365_statusmessage');
            box.append(opts.iconsuccess);
            box.append('<span style="inline-block">' + content + '</span>');
            return box;
        }

        /**
         * Update tool display.
         *
         * @param string|object content HTML or jQuery object to display.
         */
        this.updatedisplay = function (content) {
            main.find('.results').html(content);
        }

        /**
         * Render all results.
         *
         * @param object results Results object.
         */
        this.renderresults = function (results) {
            var content = $('<div class="local_o365_adminsetting_moodlesetup_results"></div>');
            if (results === false) {
                content.append(main.renderinfobox(opts.strnoinfo));
                main.updatedisplay(content);
                return true;
            }
            if (typeof (results.success) != 'undefined') {
                if (results.success === true && typeof (results.data) != 'undefined') {
                    results.data.errormessages.forEach(function(message) {
                        content.append(main.rendererrorbox(message));
                    });
                    results.data.success.forEach(function(message) {
                        content.append(main.rendersuccessbox(message));
                    });
                    results.data.info.forEach(function(message) {
                        content.append(main.renderinfobox(message));
                    });
                    main.updatedisplay(content);
                    return true;
                }
                if (results.success === false && typeof (results.data.errormessages) != 'undefined') {
                    results.data.errormessages.forEach(function(message) {
                        content.append(main.rendererrorbox(message));
                    });
                    main.updatedisplay(content);
                    return true;
                }
            }

            content.append(main.rendererrorbox(opts.strerrorcheck));
            main.updatedisplay(content);
            return true;
        }

        this.checksetup = function () {
            this.setupmoodlebtn.html(opts.strchecking);
            $.ajax({
                url: opts.url,
                type: 'GET',
                data: {
                    mode: 'checkteamsmoodlesetup'
                },
                dataType: 'json',
                success: function (resp) {
                    main.setupmoodlebtn.html(opts.strcheck);
                    main.renderresults(resp);
                },
                error: function (data, errorThrown, textStatus) {
                    main.setupmoodlebtn.html(opts.strcheck);
                    var content = main.rendererrorbox(opts.strerrorcheck + ' (' + textStatus + ')');
                    main.updatedisplay(content);
                }
            });
        }

        this.init = function () {
            if (typeof (opts.lastresults) !== 'undefined') {
                main.renderresults(opts.lastresults);
            }
            this.setupmoodlebtn.click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                main.checksetup();
            });
        }
        this.init();
    }

});
