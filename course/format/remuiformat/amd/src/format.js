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
 * Enhancements to all components for easy course accessibility.
 *
 * @module     format/remuiformat
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    function init(availableFormats) {
        $(document).ready(function() {
            var sectionlayout_val;
            var sectionbackground_val;
            var layout_value = $("#id_remuicourseformat").val();
            window.localStorage.setItem('coursedisplay', $("#id_coursedisplay").val());
            // Hide and show the course settings on course format selection.
            $("#id_remuicourseformat").change(function() {
                var layout_value = $("#id_remuicourseformat").val();
                // CARD.
                if (layout_value == availableFormats.REMUI_CARD_FORMAT.format) {
                    $("#id_coursedisplay option[value='0']").hide();
                    $('#id_coursedisplay').val(1).trigger('change');
                    $("#id_remuiteacherdisplay").parent().parent().hide();
                    $("#id_remuidefaultsectionview").parent().parent().hide();
                    $("#id_remuienablecardbackgroundimg").parent().parent().show();
                    sectionbackground_val = $("#id_remuienablecardbackgroundimg").val();
                    if (sectionbackground_val == 0) {
                        $("#id_remuidefaultsectiontheme").parent().parent().hide();
                    } else {
                        $("#id_remuidefaultsectiontheme").parent().parent().show();
                    }
                    // LIST.
                } else {
                    $("#id_coursedisplay option[value='0']").show();
                    var oldcoursedisplay = window.localStorage.getItem('coursedisplay');
                    $('#id_coursedisplay').val(oldcoursedisplay).trigger('change');
                    $("#id_remuiteacherdisplay").parent().parent().show();
                    $("#id_remuienablecardbackgroundimg").parent().parent().hide();
                    $("#id_remuidefaultsectiontheme").parent().parent().hide();
                }
                sectionlayout_val = $("#id_coursedisplay").val();
                if (sectionlayout_val == 1) {
                    $("#id_remuidefaultsectionview").parent().parent().hide();
                } else {
                    $("#id_remuidefaultsectionview").parent().parent().show();
                }
            }).trigger('change');

            // CARD.
            if (layout_value == availableFormats.REMUI_CARD_FORMAT.format) {
                $("#id_coursedisplay option[value='0']").hide();
                $("#id_remuiteacherdisplay").parent().parent().hide();
                $("#id_remuidefaultsectionview").parent().parent().hide();
                sectionbackground_val = $("#id_remuienablecardbackgroundimg").val();
                if (sectionbackground_val == 0) {
                    $("#id_remuidefaultsectiontheme").parent().parent().hide();
                } else {
                    $("#id_remuidefaultsectiontheme").parent().parent().show();
                }
                // LIST.
            } else {
                $("#id_remuiteacherdisplay").parent().parent().show();
                sectionlayout_val = $("#id_coursedisplay").val();
                if (sectionlayout_val == 1) {
                    $("#id_remuidefaultsectionview").parent().parent().hide();
                }
                $("#id_remuienablecardbackgroundimg").parent().parent().hide();
                $("#id_remuidefaultsectiontheme").parent().parent().hide();
            }
            $("#id_coursedisplay").change(function() {
                sectionlayout_val = $("#id_coursedisplay").val();
                if (sectionlayout_val == 1) {
                    $("#id_remuidefaultsectionview").parent().parent().hide();
                } else {
                    $("#id_remuidefaultsectionview").parent().parent().show();
                }
            });

            $("#id_remuienablecardbackgroundimg").change(function() {
                sectionbackground_val = $("#id_remuienablecardbackgroundimg").val();
                if (sectionbackground_val == 0) {
                    $("#id_remuidefaultsectiontheme").parent().parent().hide();
                } else {
                    $("#id_remuidefaultsectiontheme").parent().parent().show();
                }
            });

        });
    }

    // Must return the init function.
    return {
        init: init
    };
});
