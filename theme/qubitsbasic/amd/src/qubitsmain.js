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
 * Qubits Theme Main
 *
 * @package    qubitsmain
 * @copyright  2023 Qubits Dev Team - https://www.yardstickedu.com/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str'], function($, Str) {

    var QubitsMain = function(){
        console.log("TODO: we have to move all common internal scripts into this file")
        if($("#page-user-profile").length){
            this.showProfile();
        }
    }

    QubitsMain.prototype.showProfile = function(){
        console.log("Showprofile >>>> ")
        $('#page-header').attr('style','display: block !important');
    }

    QubitsMain.prototype.breadcrumbBackBtn = function(){
        $(document).on("click", "#mvbkbtn", function(){
            let cid = $(this).data("cid");
            let coursevurl = M.cfg.wwwroot+"/course/view.php?id="+cid;
            $(location).attr("href", coursevurl);
        });
        $(document).on("click", "#cvbkbtn", function(){
            let mycourseurl = M.cfg.wwwroot+"/my/courses.php";
            $(location).attr("href", mycourseurl);
        });
    }

    return {
        'init': function() {
            return new QubitsMain();
        }
    };

});