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
 * Javascript helper function for IMS Content Package module.
 *
 * @package   mod-scorm
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

mod_scorm_launch_next_sco = null;
mod_scorm_launch_prev_sco = null;
mod_scorm_activate_item = null;

M.mod_scorm = {};

M.mod_scorm.init = function(Y, hide_nav, hide_toc, toc_title, window_name, launch_sco, scoes_nav) {
    var scorm_disable_toc = false;
    var scorm_hide_nav = true;
    var scorm_hide_toc = true;
    if (hide_toc == 0) {
        if (hide_nav != 1) {
            scorm_hide_nav = false;
        }
        scorm_hide_toc = false;
    } else if (hide_toc == 3) {
        scorm_disable_toc = true;
    }

    scoes_nav = JSON.parse(scoes_nav);
    var scorm_layout_widget;
    var scorm_current_node;
    var scorm_buttons = [];
    var scorm_bloody_labelclick = false;
    var scorm_nav_panel;

    Y.use('yui2-resize', 'yui2-dragdrop', 'yui2-container', 'yui2-button', 'yui2-layout', 'yui2-treeview', 'yui2-json', 'yui2-event', function(Y) {

        Y.YUI2.widget.TextNode.prototype.getContentHtml = function() {
            var sb = [];
            sb[sb.length] = this.href ? '<a' : '<span';
            sb[sb.length] = ' id="' + Y.YUI2.lang.escapeHTML(this.labelElId) + '"';
            sb[sb.length] = ' class="' + Y.YUI2.lang.escapeHTML(this.labelStyle) + '"';
            if (this.href) {
                sb[sb.length] = ' href="' + Y.YUI2.lang.escapeHTML(this.href) + '"';
                sb[sb.length] = ' target="' + Y.YUI2.lang.escapeHTML(this.target) + '"';
            }
            if (this.title) {
                sb[sb.length] = ' title="' + Y.YUI2.lang.escapeHTML(this.title) + '"';
            }
            sb[sb.length] = ' >';
            sb[sb.length] = this.label;
            sb[sb.length] = this.href?'</a>':'</span>';
            return sb.join("");
        };

        var scorm_activate_item = function(node) {
            if (!node) {
                return;
            }
            scorm_current_node = node;
            scorm_current_node.highlight();

            // remove any reference to the old API
            if (window.API) {
                window.API = null;
            }
            if (window.API_1484_11) {
                window.API_1484_11 = null;
            }
            var url_prefix = M.cfg.wwwroot + '/mod/scorm/loadSCO.php?';
            var el_old_api = document.getElementById('scormapi123');
            if (el_old_api) {
                el_old_api.parentNode.removeChild(el_old_api);
            }

            if (node.title) {
                var el_scorm_api = document.getElementById("external-scormapi");
                el_scorm_api.parentNode.removeChild(el_scorm_api);
                el_scorm_api = document.createElement('script');
                el_scorm_api.setAttribute('id','external-scormapi');
                el_scorm_api.setAttribute('type','text/javascript');
                var pel_scorm_api = document.getElementById('scormapi-parent');
                pel_scorm_api.appendChild(el_scorm_api);
                var api_url = M.cfg.wwwroot + '/mod/scorm/loaddatamodel.php?' + node.title;
                document.getElementById('external-scormapi').src = api_url;
            }

            var content = new Y.YUI2.util.Element('scorm_content');
            try {
                // first try IE way - it can not set name attribute later
                // and also it has some restrictions on DOM access from object tag
                if (window_name || node.title == null) {
                    var obj = document.createElement('<iframe id="scorm_object" src="">');
                    if (window_name) {
                        var mine = window.open('','','width=1,height=1,left=0,top=0,scrollbars=no');
                        if(! mine) {
                             alert(M.str.scorm.popupsblocked);
                        }
                        mine.close()
                    }
                }
                else {
                    var obj = document.createElement('<iframe id="scorm_object" src="'+url_prefix + node.title+'">');
                }
                // fudge IE7 to redraw the screen
                if (Y.YUI2.env.ua.ie > 5 && Y.YUI2.env.ua.ie < 8) {
                    obj.attachEvent("onload", scorm_resize_parent);
                }
            } catch (e) {
                var obj = document.createElement('object');
                obj.setAttribute('id', 'scorm_object');
                obj.setAttribute('type', 'text/html');
                if (!window_name && node.title != null) {
                    obj.setAttribute('data', url_prefix + node.title);
                }
                if (window_name) {
                    var mine = window.open('','','width=1,height=1,left=0,top=0,scrollbars=no');
                    if(! mine) {
                         alert(M.str.scorm.popupsblocked);
                    }
                    mine.close()
                }
            }
            var old = Y.YUI2.util.Dom.get('scorm_object');
            if (old) {
                if(window_name) {
                    var cwidth = scormplayerdata.cwidth;
                    var cheight = scormplayerdata.cheight;
                    var poptions = scormplayerdata.popupoptions;
                    scorm_openpopup(M.cfg.wwwroot + "/mod/scorm/loadSCO.php?" + node.title, window_name, poptions, cwidth, cheight);
                } else {
                    content.replaceChild(obj, old);
                }
            } else {
                content.appendChild(obj);
            }

            scorm_resize_frame();

            var left = scorm_layout_widget.getUnitByPosition('left');
            if (left.expanded) {
                scorm_current_node.focus();
            }
            if (scorm_hide_nav == false) {
                scorm_fixnav();
            }
        };

        mod_scorm_activate_item = scorm_activate_item;

        /**
         * Enables/disables navigation buttons as needed.
         * @return void
         */
        var scorm_fixnav = function() {
            scorm_buttons[0].set('disabled', (scorm_skipprev(scorm_current_node) == null || scorm_skipprev(scorm_current_node).title == null ||
                        scoes_nav[launch_sco].hideprevious == 1));
            scorm_buttons[1].set('disabled', (scorm_prev(scorm_current_node) == null || scorm_prev(scorm_current_node).title == null ||
                        scoes_nav[launch_sco].hideprevious == 1));
            scorm_buttons[2].set('disabled', (scorm_up(scorm_current_node) == null) || scorm_up(scorm_current_node).title == null);
            scorm_buttons[3].set('disabled', (((scorm_next(scorm_current_node) == null || scorm_next(scorm_current_node).title == null) &&
                        (scoes_nav[launch_sco].flow != 1)) || (scoes_nav[launch_sco].hidecontinue == 1)));
            scorm_buttons[4].set('disabled', (scorm_skipnext(scorm_current_node) == null || scorm_skipnext(scorm_current_node).title == null ||
                        scoes_nav[launch_sco].hidecontinue == 1));
        };

        var scorm_resize_parent = function() {
            // fudge  IE7 to redraw the screen
            parent.resizeBy(-10, -10);
            parent.resizeBy(10, 10);
            var ifr = Y.YUI2.util.Dom.get('scorm_object');
            if (ifr) {
                ifr.detachEvent("onload", scorm_resize_parent);
            }
        };

        var scorm_resize_layout = function(alsowidth) {
            if (window_name) {
                return;
            }

            if (alsowidth) {
                scorm_layout_widget.setStyle('width', '');
                var newwidth = scorm_get_htmlelement_size('content', 'width');
            }
            // make sure that the max width of the TOC doesn't go to far

            var left = scorm_layout_widget.getUnitByPosition('left');
            var maxwidth = parseInt(Y.YUI2.util.Dom.getStyle('scorm_layout', 'width'));
            left.set('maxWidth', (maxwidth - 50));
            var cwidth = left.get('width');
            if (cwidth > (maxwidth - 1)) {
                left.set('width', (maxwidth - 50));
            }

            scorm_layout_widget.setStyle('height', '100%');
            var center = scorm_layout_widget.getUnitByPosition('center');
            center.setStyle('height', '100%');

            // calculate the rough new height
            newheight = Y.YUI2.util.Dom.getViewportHeight() -5;
            if (newheight < 600) {
                newheight = 600;
            }
            scorm_layout_widget.set('height', newheight);

            scorm_layout_widget.render();
            scorm_resize_frame();

            if (scorm_nav_panel) {
                scorm_nav_panel.align('bl', 'bl');
            }
        };

        var scorm_get_htmlelement_size = function(el, prop) {
            var val = Y.YUI2.util.Dom.getStyle(el, prop);
            if (val == 'auto') {
                if (el.get) {
                    el = el.get('element'); // get real HTMLElement from YUI element
                }
                val = Y.YUI2.util.Dom.getComputedStyle(Y.YUI2.util.Dom.get(el), prop);
            }
            return parseInt(val);
        };

        var scorm_resize_frame = function() {
            var obj = Y.YUI2.util.Dom.get('scorm_object');
            if (obj) {
                var content = scorm_layout_widget.getUnitByPosition('center').get('wrap');
                // basically trap IE6 and 7
                if (Y.YUI2.env.ua.ie > 5 && Y.YUI2.env.ua.ie < 8) {
                    if( obj.style.setAttribute ) {
                        obj.style.setAttribute("cssText", 'width: ' +(content.offsetWidth - 6)+'px; height: ' + (content.offsetHeight - 10)+'px;');
                    }
                    else {
                        obj.style.setAttribute('width', (content.offsetWidth - 6)+'px', 0);
                        obj.style.setAttribute('height', (content.offsetHeight - 10)+'px', 0);
                    }
                }
                else {
                    obj.style.width = (content.offsetWidth)+'px';
                    obj.style.height = (content.offsetHeight - 10)+'px';
                }
            }
        };

        // Handle AJAX Request
        var scorm_ajax_request = function(url, datastring) {
            var myRequest = NewHttpReq();
            var result = DoRequest(myRequest, url + datastring);
            return result;
        };

        var scorm_up = function(node, update_launch_sco) {
            var node = scorm_tree_node.getHighlightedNode();
            if (node.depth > 0 && typeof scoes_nav[launch_sco].parentscoid != 'undefined') {
                var parentscoid = scoes_nav[launch_sco].parentscoid;
                node.parent.title = scoes_nav[parentscoid].url;
                if (update_launch_sco) {
                    launch_sco = parentscoid;
                }
                return node.parent;
            }
            return null;
        };

        var scorm_lastchild = function(node) {
            if (node.children.length) {
                return scorm_lastchild(node.children[node.children.length-1]);
            } else {
                return node;
            }
        };

        var scorm_prev = function(node, update_launch_sco) {
            if (node.previousSibling && node.previousSibling.children.length &&
                    typeof scoes_nav[launch_sco].prevscoid != 'undefined') {
                var node = scorm_lastchild(node.previousSibling);
                if (node) {
                    var prevscoid = scoes_nav[launch_sco].prevscoid;
                    node.title = scoes_nav[prevscoid].url;
                    if (update_launch_sco) {
                        launch_sco = prevscoid;
                    }
                    return node;
                } else {
                    return null;
                }
            }
            return scorm_skipprev(node, update_launch_sco);
        };

        var scorm_skipprev = function(node, update_launch_sco) {
            if (node.previousSibling && typeof scoes_nav[launch_sco].prevsibling != 'undefined') {
                var prevsibling = scoes_nav[launch_sco].prevsibling;
                node.previousSibling.title = scoes_nav[prevsibling].url;
                if (update_launch_sco) {
                    launch_sco = prevsibling;
                }
                return node.previousSibling;
            } else if (node.depth > 0 && typeof scoes_nav[launch_sco].parentscoid != 'undefined') {
                var parentscoid = scoes_nav[launch_sco].parentscoid;
                node.parent.title = scoes_nav[parentscoid].url;
                if (update_launch_sco) {
                    launch_sco = parentscoid;
                }
                return node.parent;
            }
            return null;
        };

        var scorm_next = function(node, update_launch_sco) {
            if (node === false) {
                return scorm_tree_node.getRoot().children[0];
            }
            if (node.children.length && typeof scoes_nav[launch_sco].nextscoid != 'undefined') {
                var node = node.children[0];
                var nextscoid = scoes_nav[launch_sco].nextscoid;
                node.title = scoes_nav[nextscoid].url;
                if (update_launch_sco) {
                    launch_sco = nextscoid;
                }
                return node;
            }
            return scorm_skipnext(node, update_launch_sco);
        };

        var scorm_skipnext = function(node, update_launch_sco) {
            if (node.nextSibling && typeof scoes_nav[launch_sco].nextsibling != 'undefined') {
                var nextsibling = scoes_nav[launch_sco].nextsibling;
                node.nextSibling.title = scoes_nav[nextsibling].url;
                if (update_launch_sco) {
                    launch_sco = nextsibling;
                }
                return node.nextSibling;
            } else if (node.depth > 0 && typeof scoes_nav[launch_sco].parentscoid != 'undefined') {
                var parentscoid = scoes_nav[launch_sco].parentscoid;
                if (update_launch_sco) {
                    launch_sco = parentscoid;
                }
                return scorm_skipnext(node.parent, update_launch_sco);
            }
            return null;
        };

        // Launch prev sco
        var scorm_launch_prev_sco = function() {
                var result = null;
                if (scoes_nav[launch_sco].flow == 1) {
                var datastring = scoes_nav[launch_sco].url + '&function=scorm_seq_flow&request=backward';
                result = scorm_ajax_request(M.cfg.wwwroot + '/mod/scorm/datamodels/sequencinghandler.php?', datastring);
                mod_scorm_seq = encodeURIComponent(result);
                result = JSON.parse (result);
                if (typeof result.nextactivity.id != undefined) {
                        var node = scorm_prev(scorm_tree_node.getHighlightedNode())
                        if (node == null) {
                                // Avoid use of TreeView for Navigation
                                node = scorm_tree_node.getHighlightedNode();
                        }
                        node.title = scoes_nav[result.nextactivity.id].url;
                        launch_sco = result.nextactivity.id;
                        scorm_activate_item(node);
                        scorm_fixnav();
                } else {
                        scorm_activate_item(scorm_prev(scorm_tree_node.getHighlightedNode(), true));
                }
             } else {
                 scorm_activate_item(scorm_prev(scorm_tree_node.getHighlightedNode(), true));
             }
        };

        // Launch next sco
        var scorm_launch_next_sco = function () {
                var result = null;
                if (scoes_nav[launch_sco].flow == 1) {
                var datastring = scoes_nav[launch_sco].url + '&function=scorm_seq_flow&request=forward';
                result = scorm_ajax_request(M.cfg.wwwroot + '/mod/scorm/datamodels/sequencinghandler.php?', datastring);
                mod_scorm_seq = encodeURIComponent(result);
                result = JSON.parse (result);
                if (typeof result.nextactivity.id != undefined) {
                        var node = scorm_next(scorm_tree_node.getHighlightedNode())
                        if (node == null) {
                                // Avoid use of TreeView for Navigation
                                node = scorm_tree_node.getHighlightedNode();
                        }
                        node.title = scoes_nav[result.nextactivity.id].url;
                        launch_sco = result.nextactivity.id;
                        scorm_activate_item(node);
                        scorm_fixnav();
                } else {
                        scorm_activate_item(scorm_next(scorm_tree_node.getHighlightedNode(), true));
                }
             } else {
                 scorm_activate_item(scorm_next(scorm_tree_node.getHighlightedNode(), true));
             }
        };

        mod_scorm_launch_prev_sco = scorm_launch_prev_sco;
        mod_scorm_launch_next_sco = scorm_launch_next_sco;

        // layout
        Y.YUI2.widget.LayoutUnit.prototype.STR_COLLAPSE = M.str.moodle.hide;
        Y.YUI2.widget.LayoutUnit.prototype.STR_EXPAND = M.str.moodle.show;

        if (scorm_disable_toc) {
            scorm_layout_widget = new Y.YUI2.widget.Layout('scorm_layout', {
                minWidth: 255,
                minHeight: 600,
                units: [
                    { position: 'left', body: 'scorm_toc', header: toc_title, width: 0, resize: true, gutter: '0px 0px 0px 0px', collapse: false},
                    { position: 'center', body: '<div id="scorm_content"></div>', gutter: '0px 0px 0px 0px', scroll: true}
                ]
            });
        } else {
            scorm_layout_widget = new Y.YUI2.widget.Layout('scorm_layout', {
                minWidth: 255,
                minHeight: 600,
                units: [
                    { position: 'left', body: 'scorm_toc', header: toc_title, width: 250, resize: true, gutter: '2px 5px 5px 2px', collapse: true, minWidth:250, maxWidth: 590},
                    { position: 'center', body: '<div id="scorm_content"></div>', gutter: '2px 5px 5px 2px', scroll: true}
                ]
            });
        }

        scorm_layout_widget.render();
        var left = scorm_layout_widget.getUnitByPosition('left');
        if (!scorm_disable_toc) {
            left.on('collapse', function() {
                scorm_resize_frame();
            });
            left.on('expand', function() {
                scorm_resize_frame();
            });
        }
        // ugly resizing hack that works around problems with resizing of iframes and objects
        left._resize.on('startResize', function() {
            var obj = Y.YUI2.util.Dom.get('scorm_object');
            obj.style.display = 'none';
        });
        left._resize.on('endResize', function() {
            var obj = Y.YUI2.util.Dom.get('scorm_object');
            obj.style.display = 'block';
            scorm_resize_frame();
        });

        // hide the TOC if that is the default
        if (!scorm_disable_toc) {
            if (scorm_hide_toc == true) {
               left.collapse();
            }
        }
        // TOC tree
        var tree = new Y.YUI2.widget.TreeView('scorm_tree');
        scorm_tree_node = tree;
        tree.singleNodeHighlight = true;
        tree.subscribe('labelClick', function(node) {
            if (node.title == '' || node.title == null) {
                return; //this item has no navigation
            }
            scorm_activate_item(node);
            if (node.children.length) {
                scorm_bloody_labelclick = true;
            }
        });
        if (!scorm_disable_toc) {
            tree.subscribe('collapse', function(node) {
                if (scorm_bloody_labelclick) {
                    scorm_bloody_labelclick = false;
                    return false;
                }
            });
            tree.subscribe('expand', function(node) {
                if (scorm_bloody_labelclick) {
                    scorm_bloody_labelclick = false;
                    return false;
                }
            });
        }
        tree.expandAll();
        tree.render();

        // navigation
        if (scorm_hide_nav == false) {
            scorm_nav_panel = new Y.YUI2.widget.Panel('scorm_navpanel', { visible:true, draggable:true, close:false, xy: [250, 450],
                                                                    autofillheight: "body"} );
            scorm_nav_panel.setHeader(M.str.scorm.navigation);

            //TODO: make some better&accessible buttons
            scorm_nav_panel.setBody('<span id="scorm_nav"><button id="nav_skipprev">&lt;&lt;</button><button id="nav_prev">&lt;</button><button id="nav_up">^</button><button id="nav_next">&gt;</button><button id="nav_skipnext">&gt;&gt;</button></span>');
            scorm_nav_panel.render();
            scorm_buttons[0] = new Y.YUI2.widget.Button('nav_skipprev');
            scorm_buttons[1] = new Y.YUI2.widget.Button('nav_prev');
            scorm_buttons[2] = new Y.YUI2.widget.Button('nav_up');
            scorm_buttons[3] = new Y.YUI2.widget.Button('nav_next');
            scorm_buttons[4] = new Y.YUI2.widget.Button('nav_skipnext');
            scorm_buttons[0].on('click', function(ev) {
                scorm_activate_item(scorm_skipprev(scorm_tree_node.getHighlightedNode(), true));
            });
            scorm_buttons[1].on('click', function(ev) {
                scorm_launch_prev_sco();
            });
            scorm_buttons[2].on('click', function(ev) {
                scorm_activate_item(scorm_up(scorm_tree_node.getHighlightedNode(), true));
            });
            scorm_buttons[3].on('click', function(ev) {
                scorm_launch_next_sco();
            });
            scorm_buttons[4].on('click', function(ev) {
                scorm_activate_item(scorm_skipnext(scorm_tree_node.getHighlightedNode(), true));
            });
            scorm_nav_panel.render();
        }

        // finally activate the chosen item
        var scorm_first_url = tree.getRoot().children[0];
        scorm_first_url.title = scoes_nav[launch_sco].url;
        scorm_activate_item(scorm_first_url);

        // resizing
        scorm_resize_layout(false);

        // fix layout if window resized
        window.onresize = function() {
            scorm_resize_layout(true);
        };
    });
};