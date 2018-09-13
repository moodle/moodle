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
 * mod/hotpot/attempt/hp/feedback.js
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//FEEDBACK = new Array();
//FEEDBACK[0] = ''; // url of feedback page/script
//FEEDBACK[1] = ''; // array of array('teachername', 'value');
//FEEDBACK[2] = ''; // 'student name' [formmail only]
//FEEDBACK[3] = ''; // 'student email' [formmail only]
//FEEDBACK[4] = ''; // window width
//FEEDBACK[5] = ''; // window height
//FEEDBACK[6] = ''; // 'Send a message to teacher' [prompt/button text]
//FEEDBACK[7] = ''; // 'Title'
//FEEDBACK[8] = ''; // 'Teacher'
//FEEDBACK[9] = ''; // 'Message'
//FEEDBACK[10] = ''; // 'Close this window'

/**
 * hpFeedback
 */
function hpFeedback() {
    if (FEEDBACK[0]) {
        var url = '';
        var html = '';
        if (FEEDBACK[1] && FEEDBACK[2]) { // formmail
            html += '<html><body>'
                + '<form action="' + FEEDBACK[0] + '" method="POST">'
                + '<table border="0">'
                + '<tr><th valign="top" align="right">' + FEEDBACK[7] + ':</th><td>' + document.title + '</td></tr>'
                + '<tr><th valign="top" align="right">' + FEEDBACK[8] + ': </th><td>'
            ;
            if (typeof(FEEDBACK[1])=='string') {
                html += FEEDBACK[1] + hpHiddenField('recipient', FEEDBACK[1], ',', true);
            } else if (typeof(FEEDBACK[1])=='object') {
                var i_max = FEEDBACK[1].length;
                if (i_max==1) { // one teacher
                    html += FEEDBACK[1][0][0] + hpHiddenField('recipient', FEEDBACK[1][0][0]+' &lt;'+FEEDBACK[1][0][1]+'&gt;', ',', true);
                } else if (i_max>1) { // several teachers
                    html += '<select name="recipient">';
                    for (var i=0; i<i_max; i++) {
                        html += '<option value="'+FEEDBACK[1][i][1]+'">' + FEEDBACK[1][i][0] + '</option>';
                    }
                    html += '</select>';
                }
            }
            html += '</td></tr>'
                + '<tr><th valign="top" align="right">' + FEEDBACK[9] + ':</th>'
                + '<td><TEXTAREA name="message" rows="10" cols="40"></TEXTAREA></td></tr>'
                + '<tr><td>&nbsp;</td><td><input type="submit" value="' + FEEDBACK[6] + '">'
                + hpHiddenField('realname', FEEDBACK[2], ',', true)
                + hpHiddenField('email', FEEDBACK[3], ',', true)
                + hpHiddenField('subject', document.title, ',', true)
                + hpHiddenField('title', document.title, ',', true)
                + hpHiddenField('return_link_title', FEEDBACK[10], ',', true)
                + hpHiddenField('return_link_url', 'javascript:self.close()', ',', true)
                + '</td></tr></table></form></body></html>'
            ;
        } else if (FEEDBACK[1]) { // url only
            if (typeof(FEEDBACK[1])=='object') {
                var i_max = FEEDBACK[1].length;
                if (i_max>1) { // several teachers
                    html += '<html><body>'
                        + '<form action="' + FEEDBACK[0] + '" method="POST" onsubmit="this.action+=this.recipient.options[this.recipient.selectedIndex].value">'
                        + '<table border="0">'
                        + '<tr><th valign="top" align="right">' + FEEDBACK[7] + ':</th><td>' + document.title + '</td></tr>'
                        + '<tr><th valign="top" align="right">' + FEEDBACK[8] + ': </th><td>'
                    ;
                    html += '<select name="recipient">';
                    for (var i=0; i<i_max; i++) {
                        html += '<option value="'+FEEDBACK[1][i][1]+'">' + FEEDBACK[1][i][0] + '</option>';
                    }
                    html += '</select>';
                    html += '</td></tr>'
                        + '<tr><td>&nbsp;</td><td><input type="submit" value="' + FEEDBACK[6] + '">'
                        + '</td></tr></table></form></body></html>'
                    ;
                } else if (i_max==1) { // one teacher
                    url = FEEDBACK[0] + FEEDBACK[1][0][1];
                }
            } else if (typeof(FEEDBACK[1])=='string') {
                url = FEEDBACK[0] + FEEDBACK[1];
            }
        } else {
            url = FEEDBACK[0];
        }
        if (url || html) {
            var w = openWindow(url, 'feedback', FEEDBACK[4], FEEDBACK[5], 'RESIZABLE,SCROLLBARS', html);
            if (! w) {
                 // unable to open popup window
                alert('Please enable pop-up windows on your browser');
            }
        }
    }
}

/**
 * hpHiddenField
 *
 * @param xxx name
 * @param xxx value
 * @param xxx comma
 * @param xxx forceHTML
 * @return xxx
 */
function hpHiddenField(name, value, comma, forceHTML) {
    return '<input type=hidden name="' + name + '" value="' + escape(value) + '">';
}

/**
 * openWindow
 *
 * @param xxx url
 * @param xxx name
 * @param xxx width
 * @param xxx height
 * @param xxx attributes
 * @param xxx html
 * @return xxx
 */
function openWindow(url, name, width, height, attributes, html) {
    // set height, width and attributes
    if (window.screen && width && height) {
        var W = screen.availWidth;
        var H = screen.availHeight;
        width = Math.min(width, W);
        height = Math.min(height, H);
        attributes = ''
            + (attributes ? (attributes+',') : '')
            + 'WIDTH='+width+',HEIGHT='+height
        ;
    }
    // create global hpWindows object, if necessary
    if (! window.hpWindows) window.hpWindows = new Array();
    // initialize window object
    var w = null;
    // has a window with this name been opened before?
    if (name && hpWindows[name]) {
        // http://www.webreference.com/js/tutorial1/exist.html
        if (hpWindows[name].open && ! hpWindows[name].closed) {
            w = hpWindows[name];
            w.focus();
        } else {
            hpWindows[name] = null;
        }
    }
    // check window is not already open
    if (w==null) {
        // workaround for "Access is denied" errors in IE when offline
        // based on an idea seen at http://www.devshed.com/Client_Side/JavaScript/Mini_FAQ
        var ie_offline = (document.all && location.protocol=='file:');
        // try and open the new window
        w = window.open((ie_offline ? '' : url), name, attributes);
        // check window opened OK (user may have prevented popups)
        if (w) {
            // center the window
            if (window.screen && width && height) {
                w.moveTo((W-width)/2, (H-height)/2);
            }
            // add content, if required
            if (html) {
                with (w.document) {
                    clear();
                    open();
                    write(html);
                    close();
                }
            } else if (url && ie_offline) {
                w.location = url;
            }
            if (name) hpWindows[name] = w;
        }
    }
    return w;
}
