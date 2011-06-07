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
 * Javascript helper function for wiki
 *
 * @package   mod-wiki
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_wiki = {};

M.mod_wiki.init = function(Y, args) {
    var WikiHelper = function(args) {
        WikiHelper.superclass.constructor.apply(this, arguments);
    }
    WikiHelper.NAME = "WIKI";
    WikiHelper.ATTRS = {
        options: {},
        lang: {}
    };
    Y.extend(WikiHelper, Y.Base, {
        initializer: function(args) {
        }
    });
    new WikiHelper(args);
};
M.mod_wiki.renew_lock = function(Y, args) {
    function renewLock() {
        var args = {};
        args['sesskey'] = M.cfg.sesskey;
        args['pageid'] = wiki.pageid;
        if (wiki.section) {
            args['section'] = wiki.section;
        }
        var callback = {};
        YAHOO.util.Connect.asyncRequest('GET', 'lock.php?' + build_querystring(args), callback);
    }
    setInterval(renewLock, wiki.renew_lock_timeout * 1000);
}
M.mod_wiki.history = function(Y, args) {
    var compare = false;
    var comparewith = false;
    var radio  = document.getElementsByName('compare');
    var radio2 = document.getElementsByName('comparewith');
    for(var i=0; i<radio.length;i++){
          if(radio[i].checked){
            compare = true;
        }
        if(!comparewith){
            radio[i].disabled=true;
            radio2[i].disabled=false;
        } else if(!compare && comparewith){
            radio[i].disabled=false;
            radio2[i].disabled=false;
        } else {
            radio[i].disabled=false;
            radio2[i].disabled=true;
        }

        if(radio2[i].checked){
            comparewith = true;
        }
    }
}

M.mod_wiki.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree = new YAHOO.widget.TreeView(htmlid);

        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }

        tree.render();
    });
};
