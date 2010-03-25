
function customise_dock_for_theme() {
    if (!M.core_dock) {
        return false;
    }
    // Throw a lightbox for the navigation boxes
    M.core_dock.cfg.panel.modal = true;
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
    return true;
}