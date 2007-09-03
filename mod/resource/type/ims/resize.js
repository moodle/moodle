/*
 * This script resizes everything to fit the iframe 
 * of the ims-cp resource type. Credits goes to Andrew Walker.
 */

function getElementStyle(obj, prop, cssProp) {
    var ret = '';
    
    if (obj.currentStyle) {
        ret = obj.currentStyle[prop];
    } else if (document.defaultView && document.defaultView.getComputedStyle) {
        var compStyle = document.defaultView.getComputedStyle(obj, null);
        ret = compStyle.getPropertyValue(cssProp);
    }
    
    if (ret == 'auto') ret = '0';
    return ret;
}

function resizeiframe (hasNav, customCorners) {

/// Calculate window width and height
    var winWidth = 0, winHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        winWidth = window.innerWidth;
        winHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        winWidth = document.documentElement.clientWidth;
        winHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
        winWidth = document.body.clientWidth;
        winHeight = document.body.clientHeight;
    }

/// Calculate margins
    var topMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginTop', 'margin-top'));
    var bottomMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginBottom', 'margin-bottom'));
    var leftMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginLeft', 'margin-left'));
    var rightMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginRight', 'margin-right'));

/// Calculate heights
    var header = document.getElementById('content');
    var headerHeight = 0;
    if (header) {
        headerHeight = header.offsetTop + parseInt(getElementStyle(header, 'marginTop', 'margin-top')) + parseInt(getElementStyle(header, 'marginBottom', 'margin-bottom'));
    }

    var contentbt = document.getElementById('content-bt');
    var contentbtHeight = 0;
    if (contentbt) {
        contentbtHeight = contentbt.offsetHeight + parseInt(getElementStyle(contentbt, 'marginTop', 'margin-top')) + parseInt(getElementStyle(contentbt, 'marginBottom', 'margin-bottom'));
    }

    var contentbb = document.getElementById('content-bb');
    var contentbbHeight = 0;
    if (contentbb) {
        contentbbHeight = contentbb.offsetHeight + parseInt(getElementStyle(contentbb, 'marginTop', 'margin-top')) + parseInt(getElementStyle(contentbb, 'marginBottom', 'margin-bottom'));
    }

    var navbar = document.getElementById('ims-nav-bar');
    var navbarHeight = 0;
    if (navbar) {
        navbarHeight = navbar.offsetHeight + parseInt(getElementStyle(navbar, 'marginTop', 'margin-top')) + parseInt(getElementStyle(navbar, 'marginBottom', 'margin-bottom'));;
    }

    var footer = document.getElementById('footer');
    var footerHeight = 0;
    if (footer) {
        footerHeight = footer.offsetHeight + parseInt(getElementStyle(footer, 'marginTop', 'margin-top')) + parseInt(getElementStyle(footer, 'marginBottom', 'margin-bottom'));
    }


/// Calculate the used height
    var usedHeight = headerHeight +
                     contentbtHeight +
                     navbarHeight +
                     contentbbHeight +
                     footerHeight +
                     bottomMargin + 15; /// Plus 15 points to avoid the wrong vertical scroll bar on some browsers

/// Calculate widths
    var menu = document.getElementById('ims-menudiv');
    var menuWidth = 0;
    var menuLeft = 0;
    if (menu) {
        menuLeft = menu.offsetLeft;
        menuWidth = menu.offsetWidth + parseInt(getElementStyle(menu, 'marginLeft', 'margin-left')) + parseInt(getElementStyle(menu, 'marginRight', 'margin-right')) + 2; /// +2 to leave 1px menu borders
    }

    var container = document.getElementById('ims-containerdiv');
    var containerWidth = 0;
    if (container) {
        containerWidth = container.offsetWidth - 2; /// -2 to leave some right margin in the container div
    }

/// Calculate the used width
    var usedWidth = winWidth - containerWidth + menuWidth;

/// Set contentframe dimensions
    if (hasNav == true) {
        document.getElementById('ims-contentframe').style.height = (winHeight - usedHeight)+'px';
        document.getElementById('ims-contentframe').style.width = (winWidth - usedWidth)+'px';
        document.getElementById('ims-contentframe').style.left = (menuLeft + menuWidth)+'px';
    } else {
        document.getElementById('ims-contentframe-no-nav').style.height = (winHeight - usedHeight)+'px';
        document.getElementById('ims-contentframe-no-nav').style.width = (winWidth - usedWidth)+'px';
    }

/// Set containerdiv dimensions
    document.getElementById('ims-containerdiv').style.height = (winHeight - usedHeight)+'px';
}


