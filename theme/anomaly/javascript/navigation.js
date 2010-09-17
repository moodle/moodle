/**
 * Customises the dock for the anomaly theme and does some other cool stuff
 */
function customise_dock_for_theme() {
    // If we don't have M.core_dock or Y then bail
    if (!M.core_dock) {
        return false;
    }
    // Change the defautl remove all icon to work with our black dock
    M.core_dock.cfg.display.removeallicon = M.util.image_url('dock_removeall', 'theme');

    // On draw completed add the ability to move the dock to from the left to the right
    M.core_dock.on('dock:drawcompleted', anomaly.dock.enable_side_switching, anomaly.dock);
    // When the dock is first drawn check to see if it should be moved
    M.core_dock.on('dock:drawstarted', anomaly.dock.check_initial_position, anomaly.dock);
    // Corrects the panel x position for the theme
    M.core_dock.on('dock:itemadded', function(item) {
        item.on('dockeditem:showstart', anomaly.dock.correct_panel_x_position, anomaly.dock, item);
        item.on('dockeditem:resizecomplete', anomaly.dock.correct_panel_x_position, anomaly.dock, item);
    });

    // Override the default fix_title_orientation method with our anomaly method
    // this will use SVG and rotate the text if possible.
    M.core_dock.genericblock.prototype.fix_title_orientation = anomaly.dock.fix_title_orientation;
    M.core_dock.genericblock.prototype.resize_block_space = anomaly.dock.resize_block_space;
    return true;
}

var anomaly = (function(){
    return {
        namespaces : {
            svg : 'http://www.w3.org/2000/svg'
        },
        dock : {
            enable_side_switching : function() {
                var movedock = M.core_dock.Y.Node.create('<img src="'+M.util.image_url('movedock', 'theme')+'" />');
                var c = M.core_dock.node.one('.controls');
                c.insertBefore(M.core_dock.Y.Node.create('<br />'), c.one('img'));
                c.insertBefore(movedock, c.one('br'));
                movedock.on('click', this.switch_dock_side);
            },
            correct_panel_x_position : function(item) {
                var dockoffset = M.core_dock.Y.one('#dock_item_'+item.id+'_title').get('offsetWidth');
                var panelwidth = M.core_dock.Y.one(item.panel.body).get('offsetWidth');
                var screenwidth = parseInt(M.core_dock.Y.get(document.body).get('winWidth'));
                switch (M.core_dock.cfg.position) {
                    case 'left':
                        item.panel.cfg.setProperty('x', dockoffset);
                        break;
                    case 'right':
                        item.panel.cfg.setProperty('x', (screenwidth-panelwidth-dockoffset-5));
                        break;
                }
            },
            switch_dock_side : function () {
                var oldorientation = M.core_dock.cfg.orientation;
                var oldclass = M.core_dock.cfg.css.dock+'_'+M.core_dock.cfg.position+'_'+oldorientation;
                switch (M.core_dock.cfg.position) {
                    case 'right':
                        M.core_dock.cfg.position = 'left';
                        M.core_dock.cfg.orientation = 'vertical';
                        break;
                    case 'left':
                        M.core_dock.cfg.position = 'right';
                        M.core_dock.cfg.orientation = 'vertical';
                        break;
                }
                var newclass = M.core_dock.cfg.css.dock+'_'+M.core_dock.cfg.position+'_'+M.core_dock.cfg.orientation;
                M.core_dock.node.replaceClass(oldclass, newclass);
                M.core_dock.Y.Cookie.set('dock_position', M.core_dock.cfg.position);
            },
            check_initial_position : function () {
                var cookieposition = M.core_dock.Y.Cookie.get('dock_position');
                if (cookieposition && cookieposition != 'null' && cookieposition !== M.core_dock.cfg.position) {
                    var oldclass = M.core_dock.cfg.css.dock+'_'+M.core_dock.cfg.position+'_'+M.core_dock.cfg.orientation;
                    M.core_dock.cfg.position = cookieposition;
                    if (M.core_dock.node) {
                        var newclass = M.core_dock.cfg.css.dock+'_'+M.core_dock.cfg.position+'_'+M.core_dock.cfg.orientation;
                        M.core_dock.node.replaceClass(oldclass, newclass);
                    }
                }
            },
            fix_title_orientation : function (node) {
                if (M.core_dock.cfg.orientation == 'vertical') {
                    return anomaly.transform.make_vertical_text(node);
                }
                return node;
            },
            resize_block_space : function (node) {
                var blockregions = {
                    pre: {hasblocks:true,c:'side-pre-only'},
                    post: {hasblocks:true,c:'side-post-only'},
                    noblocksc:'noblocks'
                };
                M.core_dock.Y.all('div.block-region').each(function(blockregion){
                    if (blockregion.hasClass('side-pre') && blockregion.all('.block').size() == 0) {
                        blockregions.pre.hasblocks = false;
                    } else if (blockregion.hasClass('side-post') && blockregion.all('.block').size() == 0) {
                        blockregions.post.hasblocks = false;
                    }
                });
                if (blockregions.pre.hasblocks && blockregions.post.hasblocks) {
                    // No classes required both regions have blocks
                    M.core_dock.Y.one(document.body).removeClass(blockregions.pre.c).removeClass(blockregions.post.c).removeClass(blockregions.noblocksc);
                } else if (blockregions.pre.hasblocks) {
                    // side-pre-only required: remove any other classes
                    M.core_dock.Y.one(document.body).addClass(blockregions.pre.c).removeClass(blockregions.post.c).removeClass(blockregions.noblocksc);
                } else if (blockregions.post.hasblocks) {
                    // side-post-only required: remove any other classes
                    M.core_dock.Y.one(document.body).removeClass(blockregions.pre.c).addClass(blockregions.post.c).removeClass(blockregions.noblocksc);
                } else {
                    // All blocks have been docked: add noblocks remove side-xxx-only's if set
                    M.core_dock.Y.one(document.body).removeClass(blockregions.pre.c).removeClass(blockregions.post.c).addClass(blockregions.noblocksc);
                }
                return '200px';
            }
        },
        transform : {
            make_vertical_text : function(node) {

                if (YAHOO.env.ua.ie > 0) {
                    if (YAHOO.env.ua.ie > 7) {
                        node.setAttribute('style', 'writing-mode: tb-rl; filter: flipV flipH;');
                    } else {
                        node.innerHTML = node.innerHTML.replace(/(.)/g, "$1<br />");
                    }
                    return node;
                }

                var test = M.core_dock.Y.Node.create('<div><span>'+node.firstChild.nodeValue+'</span></div>');
                M.core_dock.Y.one(document.body).append(test);
                var height = test.one('span').get('offsetWidth');
                test.remove();

                var txt = document.createElementNS(anomaly.namespaces.svg, 'text');
                txt.setAttribute('x', '0');
                txt.setAttribute('y', '0');
                txt.setAttribute('transform','rotate(90, 5, 5)');
                txt.appendChild(document.createTextNode(node.firstChild.nodeValue));

                var svg = document.createElementNS(anomaly.namespaces.svg, 'svg');
                svg.setAttribute('version', '1.1');
                svg.setAttribute('height', height);
                svg.setAttribute('width', 30);
                svg.appendChild(txt);

                var div = document.createElement(node.nodeName);
                div.appendChild(svg);

                return div;
            }
        }
    }
})();