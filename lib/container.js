/*
    findParentNode (start, elementName, elementClass, elementID)
    
    Travels up the DOM hierarchy to find a parent element with the
    specified tag name, class, and id. All conditions must be met,
    but any can be ommitted. Returns the BODY element if no match
    found.
*/
function findParentNode(el, elName, elClass, elId) {
    while(el.nodeName != 'BODY') {
        if(
            (!elName || el.nodeName == elName) &&
            (!elClass || el.className.indexOf(elClass) != -1) &&
            (!elId || el.id == elId))
        {
            break;
        }
        el = el.parentNode;
    }
    return el;
}

/*
    elementToggleHide (element, elementFinder)

    If elementFinder is not provided, toggles the "hidden" class for the specified element.
    If elementFinder is provided, then the "hidden" class will be toggled for the object
    returned by the function call elementFinder(element).

    If persistent == true, also sets a cookie for this.
*/
function elementToggleHide(el, persistent, elementFinder) {
    if(!elementFinder) {
        var obj = el;
    }
    else {
        var obj = elementFinder(el);
    }
    if(obj.className.indexOf('hidden') == -1) {
        obj.className += ' hidden';
        var shown = 0;
    }
    else {
        obj.className = obj.className.replace(new RegExp(' ?hidden'), '')
        var shown = 1;
    }

    if(persistent == true) {
        new cookie('hide:' + obj.id, 1, (shown ? -1 : 356), '/').set();
    }
}


function elementCookieHide(id) {
    var obj  = document.getElementById(id);
    var cook = new cookie('hide:' + id).read();
    if(cook != null) {
        elementToggleHide(obj, false);
    }
}

/*

Is there some specific reason for this function? I think pretty
much every browser worth considering supports getElementById() [Jon]

function getObj(id)
{
  if (document.getElementById)
  {
  	this.obj = document.getElementById(id);
	this.style = document.getElementById(id).style;
  }
  else if (document.all)
  {
	this.obj = document.all[id];
	this.style = document.all[id].style;
  }
  else if (document.layers)
  {
   	this.obj = document.layers[id];
   	this.style = document.layers[id];
  }
}
*/
