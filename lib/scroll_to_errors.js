// keep the global scope clean
(function() {

    var id = null;
    var warnings = null;
    var continueBtn = null;

    var getElementPosY = function(obj) {
        var y = 0;
        while (obj.offsetParent) {
            y += obj.offsetTop;
            obj = obj.offsetParent;
        }
        return y;
    };

    // ugh, find scroll position in 3 possible ways
    var getScrollY = function() {
        return self.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
    };

    var initScroll = function(obj) {
        // if we scroll to the warning div itself the sql that caused the warning will be off the top of the page 
        // so we can look through the page for a preceeding div and base the scroll position on that        
        var prevDiv = findPreviousSibling(obj, 'div');
        // if the warning div doesn't have a previous sibling div scroll to the top of the page instead
        var y = (prevDiv) ? getElementPosY(prevDiv) + prevDiv.offsetHeight : 0;

        if (id) {
            clearInterval(id);
        }

        // scroll with a little bit of easing, I guess it's a matter of personal taste whether it would be 
        // better to scroll the page directly to the point using window.scrollTo(0, y). But I think easing 
        // makes it a little clearer to the user that they're scrolling through the page :-)
        id = setInterval(function() {
            var ys = getScrollY();
            // the stuff on arguments.callee is a check to stop scrolling if we've reached the end of the page 
            // and can't scroll any further
            if ((arguments.callee.oldPos && arguments.callee.oldPos == ys) || Math.abs(y - ys) < 5) {
                arguments.callee.oldPos = null;
                window.scrollTo(0, y);
                clearInterval(id);
                id = null;
            } else {
                window.scrollTo(0, Math.round(ys + ((y - ys) / 2)));
            }
            arguments.callee.oldPos = ys;
        }, 60);
    };

    // return nodes with a class name that matches regexp - if individual is set we're only looking 
    // for a single node
    var filterNodesByClassName = function(nodes, regexp, individual) {
        var filtered = [];
        var n = nodes.length;
        for (var i = 0; i < n; ++i) {
            if (nodes[i].className && nodes[i].className.match(regexp)) {
                if (individual) {
                    return nodes[i];
                }
                filtered.push(nodes[i]);
            }
        }
        return filtered;
    };

    // look through the previous siblings of an element and find the first one with a given node name
    var findPreviousSibling = function(obj, nodeName) {
        while (obj = obj.previousSibling) {
            if (obj.nodeName.toLowerCase() == nodeName) {
                return obj;
            }
        }
        return false;
    };

    // create the links to scroll around the page. warningIndex is used to look up the element in the 
    // warnings array that should be scrolled to
    var createWarningSkipLink = function(linkText, warningIndex, style) {
        var link = document.createElement('a');
        link.href = 'javascript:;';
        link.warningIndex = warningIndex;
        link.appendChild(document.createTextNode(linkText));
        link.onclick = function() {
             initScroll(warnings[this.warningIndex]);
        };

        if (style) {
            for (var x in style) {
                link.style[x] = style[x];
            }
        }
        return link;
    };
   

    var checkWarnings = function() {
        // look for div tags with the class name notifyproblem
        warnings = filterNodesByClassName(document.getElementsByTagName('div'), /(^|\b)notifyproblem(\b|$)/);
        // and find the continue button
        continueBtn = filterNodesByClassName(document.getElementsByTagName('div'), /(^|\b)continuebutton(\b|$)/, true);
                
        var n = warnings.length; // find how many warnings
        warnings[warnings.length] = continueBtn; // then add the continue button to the array

        var link;
        var statusOk = false;
        for (var i = 0; i < n; ++i) {
            // add a "next" link to all warnings except the last one on the page
            if (i < n - 1) {
                link = createWarningSkipLink('Scroll to next warning', i + 1, {paddingLeft: '1em'});
            } else { 
                // on the last link add a link to go to the continue button
                link = createWarningSkipLink('Scroll to continue button', i + 1, {paddingLeft: '1em'});
            }
            warnings[i].appendChild(link);
            // and add a "previous" link to all except the first
            if (i > 0) {
                link = createWarningSkipLink('Scroll to previous warning', i - 1, {paddingRight: '1em'});
                warnings[i].insertBefore(link, warnings[i].firstChild);
            }
        }
        
        
        var contentDiv = document.getElementById('content');
        if (contentDiv) {
            // create a message to display at the top of the page, with a link to the first warning
            // or to the continue button if there were no warnings on the page
            var p = document.createElement('p');
            if (n > 0) {
                var warningText = (n == 1) ? 'warning' : 'warnings';
                link = createWarningSkipLink('Scroll to the first warning', 0);
                p.appendChild(document.createTextNode('This script generated ' + n + ' ' + warningText + ' - '));
                p.className = 'notifyproblem';
            } else {
                link = createWarningSkipLink('Scroll to the continue button', 0);
                p.appendChild(document.createTextNode('No warnings - '));
                p.className = 'notifysuccess';
                statusOk = true;
            }
            p.appendChild(link);
            contentDiv.insertBefore(p, contentDiv.firstChild);
        }
        
        // automatically scroll to the first warning or continue button
        initScroll(warnings[0]);
        if (statusOk && installautopilot) {//global JS variable
            document.forms[0].submit();//auto submit
        }
    };

    // load should be a document event, but most browsers use window 
    if (window.addEventListener) {
        window.addEventListener('load', checkWarnings, false);
    } else if (document.addEventListener) {
        document.addEventListener('load', checkWarnings, false);
    } else if (window.attachEvent) {
        // sometimes IE doesn't report scrollTop correctly (it might be a quirk of this specific page, I don't know)
        // but using scrollTo once to begin makes sure things work
        window.scrollTo(0, 1);
        window.attachEvent('onload', checkWarnings);
    }

})();