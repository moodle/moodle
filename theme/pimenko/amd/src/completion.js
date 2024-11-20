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
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'theme_boost/bootstrap/tooltip'], function($, str) {
    let completioncheck = $('#completioncheck');
    let modulename = completioncheck.attr("data-modulename");
    let tooltipAjxY = str.get_string('completion-tooltip-manual-y', 'theme_pimenko', modulename);
    let tooltipAjxN = str.get_string('completion-tooltip-manual-n', 'theme_pimenko', modulename);

    let checkbox = document.getElementsByTagName("input");

    let handle_success = function(res, o) {
        if (o !== 'success') {
            // TODO: localize
            alert('An error occurred when attempting to save your tick mark.\n\n(' + o.responseText + '.)');
        }
    };
    let handle_failure = function(res, o) {
        alert('Failed: An error occurred when attempting to save your tick mark.\n\n(' + o.responseText + '.)');
    };

    let toggle = function(e) {
        e.preventDefault();
        let form = e.target;
        $.post({
            url: M.cfg.wwwroot + '/course/togglecompletion.php',
            data: 'id=' + form.getAttribute("data-id") +
                '&completionstate=' + form.getAttribute("data-completionstate") +
                '&fromajax=1&sesskey=' + M.cfg.sesskey
        }).done(handle_success).fail(handle_failure);
    };

    return {
        init: function() {
            for (let i = 0; i < checkbox.length; i++) {
                if (checkbox[i].type === "checkbox" && checkbox[i].className.includes("completioncheck")) {
                    let className = "checkbox-parent";
                    if (checkbox[i].checked) {
                        className += ' active';
                    }
                    if (checkbox[i].disabled) {
                        className += ' disable';
                    }
                    checkbox[i].parentNode.className = className;

                    checkbox[i].onchange = function(event) {
                        toggle(event);
                        let className = "checkbox-parent";
                        if (event.target.getAttribute("data-completionstate") === "1") {
                            className += ' active';
                            event.target.setAttribute("data-completionstate", 0);
                            $.when(tooltipAjxY).done(function(tooltipTexty) {
                                $(event.target.parentNode).tooltip('hide')
                                    .attr('data-original-title', tooltipTexty)
                                    .tooltip('show');
                            });
                        } else {
                            event.target.setAttribute("data-completionstate", 1);
                            $.when(tooltipAjxN).done(function(tooltipTextn) {
                                $(event.target.parentNode).tooltip('hide')
                                    .attr('data-original-title', tooltipTextn)
                                    .tooltip('show');
                            });

                        }
                        if (event.target.disabled) {
                            className += ' disable';
                        }
                        event.target.parentNode.className = className;
                    };
                }
            }

            let help = Y.one('#completionprogressid');
            if (help && !(Y.one('form.togglecompletion') || Y.one('.autocompletion'))) {
                help.setStyle('display', 'none');
            }
        }
    };
});
