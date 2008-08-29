<!--
function scorm_get_element_style(obj, prop, cssProp) {
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

function scorm_resize (cwidth, cheight) {

    var winwidth = 0, winheight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        winwidth = window.innerWidth;
        winheight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        winwidth = document.documentElement.clientWidth;
        winheight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
        winwidth = document.body.clientWidth;
        winheight = document.body.clientHeight;
    }
                              
    var header = document.getElementById('header');   
    var content = document.getElementById('content');
    var headerheight = 0;
    if (content) {
        headerheight = content.offsetTop;
    }
    
    var footer = document.getElementById('footer');
    var imsnavbar = document.getElementById('tochead');
    var scormtop = document.getElementById('scormtop');
    var footerheight = 0;
    var imsnavheight = 0;
    var scormtopheight = 0;
    if (footer) {
        footerheight = footer.offsetHeight + parseInt(scorm_get_element_style(footer, 'marginTop', 'margin-top')) + parseInt(scorm_get_element_style(footer, 'marginBottom', 'margin-bottom'));
    }
    if (imsnavbar) {
        imsnavheight = imsnavbar.offsetHeight;
    }
    if (scormtop) {
        scormtopheight = scormtop.offsetHeight;
    }

    var topmargin = parseInt(scorm_get_element_style(document.getElementsByTagName('body')[0], 'marginTop', 'margin-top'));
    var bottommargin = parseInt(scorm_get_element_style(document.getElementsByTagName('body')[0], 'marginBottom', 'margin-bottom'));
    
    var totalheight = headerheight + 
                        footerheight + 
                        scormtopheight+
                        topmargin +
                        bottommargin+10; // +10 to save a minor vertical scroll always present!

    var totalheighttoc = totalheight+imsnavheight;
    // override total height with configured height if it is defined
    if (cheight > 0) {
      winheight = cheight;
    }
    var finalheighttoc = winheight - totalheighttoc;
    if (finalheighttoc <= 0) {
        finalheighttoc = winheight;
    }                        
    var finalheight = winheight - totalheight;
    if (finalheight <= 0) {
        finalheight = winheight;
    }                        
    var toctree = document.getElementById('toctree');
    if (toctree != null){
        var toctreeHeight = toctree.offsetHeight;
        document.getElementById('toctree').style.height = finalheighttoc + 'px';
        var scoframe2 = document.getElementById('scoframe1');
        document.getElementById('scormobject').style.height = finalheight + 'px';
    }else{
        var scoframe2 = document.getElementById('scoframe1');
        document.getElementById('scormobject').style.height = finalheight + 'px';
    }

	// resize the content container too to move the footer below the SCORM content
	var contenti3 = document.getElementById('content-i3');
	if (contenti3) {
    	contenti3.style.height = (winheight - totalheight + 30) + 'px';
	} else {
	   document.getElementById('content').style.height = (finalheight + 30) + 'px';
    }
     // resize the content container too to move the footer below the SCORM content
    var contenti3 = document.getElementById('content-i3');
    if (contenti3) {
        contenti3.style.height = (finalheight + 30) + 'px';
    } else {
        document.getElementById('content').style.height = (finalheight + 30) + 'px';
    }
}
-->