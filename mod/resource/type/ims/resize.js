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
                              
    var header = document.getElementById('header');
    var divs = document.getElementsByTagName('div');
    var n = divs.length;
    
    
    var content = document.getElementById('content');
    var headerHeight = 0;
    if (content) {
        headerHeight = content.offsetTop;
    }
    
    var footer = document.getElementById('footer');
    var imsnavbar = document.getElementById('ims-nav-bar');
    var footerHeight = 0;
    var imsnavHeight = 0;
    if (footer) {
        footerHeight = footer.offsetHeight + parseInt(getElementStyle(footer, 'marginTop', 'margin-top')) + parseInt(getElementStyle(footer, 'marginBottom', 'margin-bottom'));
    }
    if (imsnavbar) {
        imsnavHeight = imsnavbar.offsetHeight;
    }

    var topMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginTop', 'margin-top'));
    var bottomMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginBottom', 'margin-bottom'));
    var leftMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginLeft', 'margin-left'));
    var rightMargin = parseInt(getElementStyle(document.getElementsByTagName('body')[0], 'marginRight', 'margin-right'));

    var totalHeight = headerHeight +
                      footerHeight +
                      imsnavHeight +
                      topMargin +
                      bottomMargin;

    if (hasNav == true) {
        var navBarWidth = document.getElementById('ims-menudiv').offsetWidth;
        var iframeWidth = (winWidth - navBarWidth - leftMargin - rightMargin - 4)+'px'; // -4 for two divs borders I hope
        document.getElementById('ims-menudiv').style.height = (winHeight - totalHeight)+'px';
    }
    else {
        var iframeWidth = (winWidth - leftMargin - rightMargin - 2)+'px'; // -2 for one div borders I hope
    }

    if (hasNav == true) {
        document.getElementById('ims-contentframe').style.height = (winHeight - totalHeight)+'px';
        document.getElementById('ims-contentframe').style.width = iframeWidth;
    } else {
        document.getElementById('ims-contentframe-no-nav').style.height = (winHeight - totalHeight)+'px';
        document.getElementById('ims-contentframe-no-nav').style.width = iframeWidth;
    }
    document.getElementById('ims-containerdiv').style.height = (winHeight - totalHeight)+'px';
}


