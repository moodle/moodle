/*
 * This script resizes everything to fit the window. Maybe a fixed iframe
 * would be better?
 */
 
// written by Dean Edwards, 2005
// with input from Tino Zijdel - crisp@xs4all.nl
// http://dean.edwards.name/weblog/2005/10/add-event/
function addEvent(element, type, handler)
{
	// Modification by Tanny O'Haley, http://tanny.ica.com to add the
	// DOMContentLoaded for all browsers.
	if (type == "DOMContentLoaded" || type == "domload")
	{
		addDOMLoadEvent(handler);
		return;
	}
	
	if (element.addEventListener)
		element.addEventListener(type, handler, false);
	else
	{
		if (!handler.$$guid) handler.$$guid = addEvent.guid++;
		if (!element.events) element.events = {};
		var handlers = element.events[type];
		if (!handlers)
		{
			handlers = element.events[type] = {};
			if (element['on' + type]) handlers[0] = element['on' + type];
			element['on' + type] = handleEvent;
		}
	
		handlers[handler.$$guid] = handler;
	}
}

addEvent.guid = 1;

function removeEvent(element, type, handler)
{
	if (element.removeEventListener)
		element.removeEventListener(type, handler, false);
	else if (element.events && element.events[type] && handler.$$guid)
		delete element.events[type][handler.$$guid];
}

function handleEvent(event)
{
	event = event || fixEvent(window.event);
	var returnValue = true;
	var handlers = this.events[event.type];

	for (var i in handlers)
	{
		if (!Object.prototype[i])
		{
			this.$$handler = handlers[i];
			if (this.$$handler(event) === false) returnValue = false;
		}
	}

	if (this.$$handler) this.$$handler = null;

	return returnValue;
}

function fixEvent(event)
{
	event.preventDefault = fixEvent.preventDefault;
	event.stopPropagation = fixEvent.stopPropagation;
	return event;
}
fixEvent.preventDefault = function()
{
	this.returnValue = false;
}
fixEvent.stopPropagation = function()
{
	this.cancelBubble = true;
}

// This little snippet fixes the problem that the onload attribute on the body-element will overwrite
// previous attached events on the window object for the onload event
if (!window.addEventListener)
{
	document.onreadystatechange = function()
	{
		if (window.onload && window.onload != handleEvent)
		{
			addEvent(window, 'load', window.onload);
			window.onload = handleEvent;
		}
	}
}

// End Dean Edwards addEvent.


// DF1.1 :: domFunction 
// *****************************************************
// DOM scripting by brothercake -- http://www.brothercake.com/
// GNU Lesser General Public License -- http://www.gnu.org/licenses/lgpl.html
//******************************************************

//DOM-ready watcher
function domFunction(f, a)
{
	//initialise the counter
	var n = 0;
	
	//start the timer
	var t = setInterval(function()
	{
		//continue flag indicates whether to continue to the next iteration
		//assume that we are going unless specified otherwise
		var c = true;

		//increase the counter
		n++;
	
		//if DOM methods are supported, and the body element exists
		//(using a double-check including document.body, for the benefit of older moz builds [eg ns7.1] 
		//in which getElementsByTagName('body')[0] is undefined, unless this script is in the body section)
		if(typeof document.getElementsByTagName != 'undefined' && (document.getElementsByTagName('body')[0] != null || document.body != null))
		{
			//set the continue flag to false
			//because other things being equal, we're not going to continue
			c = false;

			//but ... if the arguments object is there
			if(typeof a == 'object')
			{
				//iterate through the object
				for(var i in a)
				{
					//if its value is "id" and the element with the given ID doesn't exist 
					//or its value is "tag" and the specified collection has no members
					if
					(
						(a[i] == 'id' && document.getElementById(i) == null)
						||
						(a[i] == 'tag' && document.getElementsByTagName(i).length < 1)
					) 
					{ 
						//set the continue flag back to true
						//because a specific element or collection doesn't exist
						c = true; 

						//no need to finish this loop
						break; 
					}
				}
			}

			//if we're not continuing
			//we can call the argument function and clear the timer
			if(!c) { f(); clearInterval(t); }
		}
		
		//if the timer has reached 60 (so timeout after 15 seconds)
		//in practise, I've never seen this take longer than 7 iterations [in kde 3 
		//in second place was IE6, which takes 2 or 3 iterations roughly 5% of the time]
		if(n >= 60)
		{
			//clear the timer
			clearInterval(t);
		}
		
	}, 250);
};


// Here are my functions for adding the DOMContentLoaded event to browsers other
// than Mozilla.

// Array of DOMContentLoaded event handlers.
window.onDOMLoadEvents = new Array();

// Function that adds DOMContentLoaded listeners to the array.
function addDOMLoadEvent(listener) {
	window.onDOMLoadEvents[window.onDOMLoadEvents.length]=listener;
}

// Function to process the DOMContentLoaded events array.
function DOMContentLoadedInit() {
	// quit if this function has already been called
	if (arguments.callee.done) return;

	// flag this function so we don't do the same thing twice
	arguments.callee.done = true;

	// iterates through array of registered functions 
	for (var i=0; i<window.onDOMLoadEvents.length; i++) {
		var func = window.onDOMLoadEvents[i];
		func();
	}
}

// If Mozilla, use the built in DOMContentLoaded event.
if(document.addEventListener && !window.opera &&
  !(!document.all && document.childNodes && !navigator.taintEnabled)) {
	document.addEventListener("DOMContentLoaded", DOMContentLoadedInit, false);
} else {
	// Add it to the brothercake domFunction() interval function.
	var funcDOMContentLoadedInit = new domFunction(DOMContentLoadedInit);
	// Just in case window.onload happens first, add it there too.
	addEvent(window, "load", DOMContentLoadedInit);
}

// Usage:
// 	addDOMLoadEvent(YourFunctionName);
// or
//	addDOMLoadEvent(function() {
//		Code to run on page load.
//	});

// Usage:
// 	addEvent(object, event, YourFunctionName);
// or
//	addEvent(object, event, function() {
//		Code to run on page load.
//	});

 
 

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

function resizeiframe (hasNav) {    
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
    
    var totalHeight = headerHeight + 
                        footerHeight + 
                        imsnavHeight +
                        topMargin +
                        bottomMargin + 20; // +20 to save a minor vertical scroll always present!
                        
    

    if (hasNav == true) {
        var navBarWidth = document.getElementById('ims-menudiv').offsetWidth;
        var iframeWidth = (winWidth - navBarWidth - 30)+'px';
        document.getElementById('ims-menudiv').style.height = (winHeight - totalHeight)+'px';
    }
    else {
        var iframeWidth = (winWidth - 30)+'px';
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


