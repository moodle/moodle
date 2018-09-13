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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


var NDY = YUI().use("node", function(Y) {
    var choicegroup_memberdisplay_click = function(e) {

        var names = Y.all('div.choicegroups-membersnames'),
            btnShowHide = Y.all('a.choicegroup-memberdisplay');

        btnShowHide.toggleClass('hidden');
        names.toggleClass('hidden');

        // Fix for Chrome where focus is not returned to the link after it is toggled.
        if (document.getElementsByClassName) {
            var elements = document.getElementsByClassName('choicegroup-membershow');
            if (elements[0].classList.contains('hidden')) {
                elements = document.getElementsByClassName('choicegroup-memberhide');
            }
            elements[0].focus();
        }
        e.preventDefault();

    };
    Y.on("click", choicegroup_memberdisplay_click, "a.choicegroup-memberdisplay");

    var choicegroup_descriptiondisplay_click = function(e) {

        var names = Y.all('div.choicegroups-descriptions'),
            btnShowHide = Y.all('a.choicegroup-descriptiondisplay');

        btnShowHide.toggleClass('hidden');
        names.toggleClass('hidden');

        // Fix for Chrome where focus is not returned to the link after it is toggled.
        if (document.getElementsByClassName) {
            var elements = document.getElementsByClassName('choicegroup-descriptionshow');
            if (elements[0].classList.contains('hidden')) {
                elements = document.getElementsByClassName('choicegroup-descriptionhide');
            }
            elements[0].focus();
        }
        e.preventDefault();

    };
    Y.on("click", choicegroup_descriptiondisplay_click, "a.choicegroup-descriptiondisplay");
    Y.delegate('click', function () {
        Y.one(".modchoicegroupsumbit").hide();
    }, Y.config.doc, "table.choicegroups input[id^='choiceid_'][type='radio'][checked]", this);
    Y.delegate('click', function () {
        Y.one(".modchoicegroupsumbit").show();
    }, Y.config.doc, "table.choicegroups input[id^='choiceid_'][type='radio']:not([checked])", this);
});
