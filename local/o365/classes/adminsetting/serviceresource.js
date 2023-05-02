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
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$(function() {

    $.fn.serviceresource = function(options) {
        var defaultopts = {
            url: 'localhost',
            setting: '',
            strvalid: '',
            iconvalid: '',
            strinvalid: '',
            iconinvalid: '',
            iconloading: '',
            strerror: '',
            strdetecting: '',
            strdetect: ''
        };
        var opts = $.extend({}, defaultopts, options);
        var main = this;
        var checktimeout = null;
        var checkrequest = null;
        this.detectbutton = this.find('button.detect');
        this.input = this.find('input.maininput');

        this.queuecheck = function(q) {
            if (q !== '') {
                if (checktimeout != null) {
                    clearTimeout(checktimeout);
                }
                checktimeout = setTimeout(function() { main.checksetting(q); }, 500);
            }
        }

        this.abortsearch = function() {
            if (checkrequest && checkrequest.readyState != 4) {
                checkrequest.abort();
            }
        }

        this.checksetting = function(val) {
            main.abortsearch();
            checkrequest = $.ajax({
                url: opts.url,
                type: 'GET',
                data: {
                    mode: 'checkserviceresource',
                    setting: opts.setting,
                    value: val
                },
                dataType: 'json',
                success: function(resp) {
                    if (typeof(resp.success) != 'undefined') {
                        if (resp.success === true && typeof(resp.data) != 'undefined' && typeof(resp.data.valid) != 'undefined') {
                            if (resp.data.valid === true) {
                                main.successmessage(opts.strvalid);
                            } else {
                                main.errormessage(opts.strinvalid);
                            }
                            return true;
                        }

                        if (resp.success === false && typeof(resp.errormessage) != 'undefined') {
                            main.errormessage(resp.errormessage);
                            return true;
                        }
                    }

                    main.errormessage(opts.strerror);
                    return true;
                },
                error: function(data) {
                    main.errormessage(opts.strerror);
                }
            });
        }

        this.detectsetting = function() {
            this.detectbutton.html(opts.strdetecting);
            $.ajax({
                url: opts.url,
                type: 'GET',
                data: {
                    mode: 'detectserviceresource',
                    setting: opts.setting
                },
                dataType: 'json',
                success: function(resp) {
                    main.detectbutton.html(opts.strdetect);
                    if (typeof(resp.success) != 'undefined') {
                        if (resp.success === true && typeof(resp.data) != 'undefined' && typeof(resp.data.settingval) === 'string') {
                            main.input.val(resp.data.settingval);
                            main.successmessage(opts.strvalid);
                            return true;
                        }

                        if (resp.success === false && typeof(resp.errormessage) != 'undefined') {
                            main.errormessage(resp.errormessage);
                            return true;
                        }
                    }

                    main.errormessage(opts.strerror);
                    return true;
                },
                error: function(data) {
                    main.detectbutton.html(opts.strdetect);
                    main.errormessage(opts.strerror);
                }
            });
        }

        this.successmessage = function(message) {
            main.find('.local_o365_statusmessage').show().removeClass('alert-error').removeClass('alert-info').addClass('alert-success');
            main.find('.local_o365_statusmessage').find('img.smallicon').replaceWith(opts.iconvalid);
            main.find('.local_o365_statusmessage').find('.statusmessage').html(message);
        }

        this.errormessage = function(message) {
            main.find('.local_o365_statusmessage').show().removeClass('alert-success').removeClass('alert-info').addClass('alert-error');
            main.find('.local_o365_statusmessage').find('img.smallicon').replaceWith(opts.iconinvalid);
            main.find('.local_o365_statusmessage').find('.statusmessage').html(message);
        }

        this.input.on('input', function(e) {
            main.queuecheck($(this).val());
        });

        this.detectbutton.click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            main.detectsetting();
        });
    }

});