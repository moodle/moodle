
function customise_dock_for_theme() {
    if (!M.core_dock) {
        return false;
    }
    // Throw a lightbox for the navigation boxes
    M.core_dock.cfg.panel.modal = true;
    /**
     * This function converts the title of each docked block to make it vertical.
     *
     * In the case of browsers that support svg (scalable vector graphics) an SVG is
     * generated to rotate the text. In the case of IE we have to settle for making
     * it letter over letter.
     *
     * @param {Y.Node} node
     */
    M.core_dock.genericblock.prototype.fix_title_orientation = function(node) {
        if (YAHOO.env.ua.ie > 0) {
            if (YAHOO.env.ua.ie > 7) {
                // IE8 can flip the text via CSS
                node.setAttribute('style', 'writing-mode: tb-rl; filter: flipV flipH;');
            } else {
                // IE < 7 can't do anything cool, just settle to stacked letters
                node.innerHTML = node.innerHTML.replace(/(.)/g, "$1<br />");
            }
            return node;
        }
        // Cool, we can use SVG!
        var test = M.core_dock.Y.Node.create('<div><span>'+node.firstChild.nodeValue+'</span></div>');
        M.core_dock.Y.one(document.body).append(test);
        var height = test.one('span').get('offsetWidth');
        test.remove();

        var txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        txt.setAttribute('x', '0');
        txt.setAttribute('y', '0');
        txt.setAttribute('transform','rotate(90, 5, 5)');
        txt.appendChild(document.createTextNode(node.firstChild.nodeValue));

        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('version', '1.1');
        svg.setAttribute('height', height);
        svg.setAttribute('width', 30);
        svg.appendChild(txt);

        var div = document.createElement(node.nodeName);
        div.appendChild(svg);

        return div;
    }
    /**
     * This function makes sure that if all blocks from one side or the other are docked
     * the gap that is left behind if closed up. This is done of course by using classes.
     */
    M.core_dock.genericblock.prototype.resize_block_space = function() {
        var blockregions = {
            pre: {
                hasblocks : (M.core_dock.Y.one('#region-pre') && M.core_dock.Y.one('#region-pre').all('.block').size() > 0),
                c : 'side-pre-only'
            },
            post: {
                hasblocks : (M.core_dock.Y.one('#region-post') && M.core_dock.Y.one('#region-post').all('.block').size() > 0),
                c : 'side-post-only'
            },
            noblocksc:'content-only'
        }
        
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
    return true;
}