// Miscellaneous core Javascript functions for Moodle

function popUpProperties(inobj) {
  op = window.open();
  op.document.open('text/plain');
  for (objprop in inobj) {
    op.document.write(objprop + ' => ' + inobj[objprop] + '\n');
  }
  op.document.close();
}

function fillmessagebox(text) {
  document.form.message.value = text;
}

function copyrichtext(textname) {
/// Legacy stub for old editor - to be removed soon
  return true;
}

function checkall() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++)
    void(el[i].checked=1)
}

function checknone() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++)
    void(el[i].checked=0)
}

function lockoptions(form, master, subitems) {
  // subitems is an array of names of sub items
  // requires that each item in subitems has a
  // companion hidden item in the form with the
  // same name but prefixed by "h"
  if (eval("document."+form+"."+master+".checked")) {
    for (i=0; i<subitems.length; i++) {
      unlockoption(form, subitems[i]);
    }
  } else {
    for (i=0; i<subitems.length; i++) {
      lockoption(form, subitems[i]);
    }
  }
  return(true);
}

function lockoption(form,item) {
  eval("document."+form+"."+item+".disabled=true");/* IE thing */
  eval("document."+form+".h"+item+".value=1");
}

function unlockoption(form,item) {
  eval("document."+form+"."+item+".disabled=false");/* IE thing */
  eval("document."+form+".h"+item+".value=0");
}

function submitFormById(id) {
    var theform = document.getElementById(id);
    if(!theform) {
        return false;
    }
    if(theform.tagName != 'FORM') {
        return false;
    }
    if(!theform.onsubmit || theform.onsubmit()) {
        return theform.submit();
    }
}

function select_all_in(elTagName, elId, elClass) {
    var inputs = document.getElementsByTagName('INPUT');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elId, elClass);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox') {
            inputs[i].checked = 'checked';
        }
    }
}

function deselect_all_in(elTagName, elId, elClass) {
    var inputs = document.getElementsByTagName('INPUT');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elId, elClass);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox') {
            inputs[i].checked = '';
        }
    }
}

function confirm_if(expr, message) {
    if(!expr) {
        return true;
    }
    return confirm(message);
}


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

function filterByParent(elCollection, parentFinder) {
    var filteredCollection = [];
    for(var i = 0; i < elCollection.length; ++i) {
        var findParent = parentFinder(elCollection[i]);
        if(findParent.nodeName != 'BODY') {
            filteredCollection.push(elCollection[i]);
        }
    }
    return filteredCollection;
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
