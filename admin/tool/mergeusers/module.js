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
 * @author Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 Servei de Recursos Educatius (http://www.sre.urv.cat)
 */

M.tool_mergeusers = {
    init_select_table: function (Y) {
        Y.use('node', function (Y) {
            radiobuttons = Y.all('#merge_users_tool_user_select_table input');
            radiobuttons.each(function (node){
                node.on('click', function(e) {

                    current = e.currentTarget.get('name');
                    if ( current == 'olduser' ) {
                        target = 'newuser';
                        lastselected = Y.one('input[name=selectedolduser]');
                        lastvalue = lastselected.get('value');
                    } else {
                        target = 'olduser';
                        lastselected = Y.one('input[name=selectednewuser]');
                        lastvalue = lastselected.get('value');
                    }

                    // first disable sibling radio button
                    id = e.currentTarget.get('value');
                    radiobutton = Y.one('#' + target + id);
                    radiobutton.setAttribute('disabled', 'disabled');

                    // after that, reenable old sibling radio button
                    if ( lastvalue != "" && lastvalue != id) {
                        last = Y.one("#"+target+lastvalue);
                        last.removeAttribute('disabled');
                    }
                    lastselected.set('value', id);
                });
            });
        });
    }
}
