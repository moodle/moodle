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
 * Form listing Javascript.
 *
 * It mainly handles loading the main content div when cliking on a tab/row.
 * @copyright 2012 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.form_listing = {};
M.form_listing.Y = null;
M.form_listing.instances = [];

/**
 * This function is called for each listing form on page.
 *
 * @param {Array} params :  {int} hiddeninputid - the id of the hidden input element
 *                          {int} elementid - the id of the full form element
 *                          {Array} items - items has for key the value return by the form, and for content an array with two attributs: mainhtml and rowhtml.
 *                          {string} hideall - button label to hide all tabs(rows).
 *                          {string} showall - button label to show all tabs(rows).
 *                          {string} inputname - the name of the input element
 *                          {string} currentvalue - the currently selected tab(row)
 */
M.form_listing.init = function(Y, params) {
    if (params && params.hiddeninputid && params.elementid) {

        // Replace the radio buttons by a hidden input.
        Y.one('#formlistinginputcontainer_' + params.inputname).setHTML('<input name='+params.inputname+' type=hidden id='+params.hiddeninputid+' value='+params.currentvalue+' />');

        var caption = Y.one('#'+params.elementid+'_caption');
        var allitems = Y.one('#'+params.elementid+'_all');
        var selecteditem = Y.one('#'+params.elementid+'_main');
        var hiddeninput = Y.one('#'+params.hiddeninputid);

        // Do not display the listing by default.
        var show = 0;
        allitems.hide();

        // Refresh the main item + set the hidden input to its value.
        var selectitem = function(e) {
            var index = this.get('id').replace(params.elementid+'_all_',"");
            hiddeninput.set('value', items[index]);
            selecteditem.setHTML(params.items[items[index]].mainhtml);
        }

        // Caption Onlick event to display/hide the listing.
        var onclick = function(e) {
            if (!show) {
                allitems.show(true);
                show = 1;
                caption.setHTML(params.hideall);
            } else {
                allitems.hide(true);
                show = 0;
                caption.setHTML(params.showall);
            }
        };

        caption.on('click', onclick);

        // Fill the item rows with html + add event.
        // PS: we need to save the items into a temporary "items[]" array because params.items keys could be url.
        // This temporary items[] avoid not working calls like Y.one('#myitems_http:www.google.com').
        var items = [];
        var itemindex = 0;
        for (itemid in params.items) {
            items[itemindex] = itemid;

            // Add the row.
            allitems.append("<div id="+params.elementid+'_all_'+itemindex+" class='formlistingrow'>" + params.items[itemid].rowhtml + "</div>");

            // Add click event to the row.
            Y.one('#'+params.elementid+'_all_'+itemindex).on('click', selectitem);

            itemindex = itemindex + 1;
        }
    }
};
