// Miscellaneous core Javascript functions for Moodle

function popupchecker(msg) {
    var testwindow = window.open('itestwin.html', '', 'width=1,height=1,left=0,top=0,scrollbars=no');
    if (testwindow == null)
        {alert(msg);}
    else {
        testwindow.close();
    }
}

/*
function popUpProperties(inobj) {
/// Legacy function
  var op = window.open();
  op.document.open('text/plain');
  for (objprop in inobj) {
    op.document.write(objprop + ' => ' + inobj[objprop] + '\n');
  }
  op.document.close();
}

function fillmessagebox(text) {
/// Legacy function
  document.form.message.value = text;
}

function copyrichtext(textname) {
/// Legacy stub for old editor - to be removed soon
  return true;
}
*/

function checkall() {
  var el = document.getElementsByTagName('input');
  for(var i=0; i<el.length; i++) {
    if(el[i].type == 'checkbox') {
      el[i].checked = true;
    }
  }
}

function checknone() {
  var el = document.getElementsByTagName('input');
  for(var i=0; i<el.length; i++) {
    if(el[i].type == 'checkbox') {
      el[i].checked = false;
    }
  }
}

function lockoptions(formid, master, subitems) {
  // Subitems is an array of names of sub items.
  // Optionally, each item in subitems may have a
  // companion hidden item in the form with the
  // same name but prefixed by "h".
  var form = document.forms[formid];

  if (eval("form."+master+".checked")) {
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
  eval("form."+item+".disabled=true");/* IE thing */
  if(form.elements['h'+item]) {
    eval("form.h"+item+".value=1");
  }
}

function unlockoption(form,item) {
  eval("form."+item+".disabled=false");/* IE thing */
  if(form.elements['h'+item]) {
    eval("form.h"+item+".value=0");
  }
}

/**
 * Get the value of the 'virtual form element' with a particular name. That is,
 * abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @return the value of that element.
 */
function get_form_element_value(form, name) {
    var element = form[name];
    if (!element) {
        return null;
    }
    if (element.tagName) {
        // Ordinarly thing like a select box.
        return element.value;
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = element[j];
        if (el.checked) {
            return el.value;
        }
    }
    return null;
}


/**
 * Set the disabled state of the 'virtual form element' with a particular name.
 * This abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @param disabled the disabled state to set.
 */
function set_form_element_disabled(form, name, disabled) {
    var element = form[name];
    if (!element) {
        return;
    }
    if (element.tagName) {
        // Ordinarly thing like a select box.
        element.disabled = disabled;
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = element[j];
        el.disabled = disabled;
    }
}

/**
 * Set the hidden state of the 'virtual form element' with a particular name.
 * This abstracts away the difference between a normal form element, like a select
 * which is a single HTML element with a .value property, and a set of radio
 * buttons, which is several HTML elements.
 *
 * @param form a HTML form.
 * @param master the name of an element in that form.
 * @param hidden the hidden state to set.
 */
function set_form_element_hidden(form, name, hidden) {
    var element = form[name];
    if (!element) {
        return;
    }
    if (element.tagName) {
        var el = findParentNode(element, 'DIV', 'fitem', false);
        if (el!=null) {
            el.style.display = hidden ? 'none' : '';
            el.style.visibility = hidden ? 'hidden' : '';
        }
    }
    // Array of things, like radio buttons.
    for (var j = 0; j < element.length; j++) {
        var el = findParentNode(element[j], 'DIV', 'fitem', false);
        if (el!=null) {
            el.style.display = hidden ? 'none' : '';
            el.style.visibility = hidden ? 'hidden' : '';
        }
    }
}

function lockoptionsall(formid) {
    var form = document.forms[formid];
    var dependons = eval(formid + 'items');
    var tolock = [];
    var tohide = [];
    for (var dependon in dependons) {
        // change for MooTools compatibility
        if (!dependons.propertyIsEnumerable(dependon)) {
            continue;
        }
        if (!form[dependon]) {
            continue;
        }
        for (var condition in dependons[dependon]) {
            for (var value in dependons[dependon][condition]) {
                var lock;
                var hide = false;
                switch (condition) {
                  case 'notchecked':
                      lock = !form[dependon].checked; break;
                  case 'checked':
                      lock = form[dependon].checked; break;
                  case 'noitemselected':
                      lock = form[dependon].selectedIndex == -1; break;
                  case 'eq':
                      lock = get_form_element_value(form, dependon) == value; break;
                  case 'hide':
                      // hide as well as disable
                      hide = true; break;
                  default:
                      lock = get_form_element_value(form, dependon) != value; break;
                }
                for (var ei in dependons[dependon][condition][value]) {
                    var eltolock = dependons[dependon][condition][value][ei];
                    if (hide) {
                        tohide[eltolock] = true;
                    }
                    if (tolock[eltolock] != null) {
                        tolock[eltolock] = lock || tolock[eltolock];
                    } else {
                        tolock[eltolock] = lock;
                    }
                }
            }
        }
    }
    for (var el in tolock) {
        // change for MooTools compatibility
        if (!tolock.propertyIsEnumerable(el)) {
            continue;
        }
        set_form_element_disabled(form, el, tolock[el]);
        if (tohide.propertyIsEnumerable(el)) {
            set_form_element_hidden(form, el, tolock[el]);
        }
    }
    return true;
}

function lockoptionsallsetup(formid) {
    var form = document.forms[formid];
    var dependons = eval(formid+'items');
    for (var dependon in dependons) {
        // change for MooTools compatibility
        if (!dependons.propertyIsEnumerable(dependon)) {
            continue;
        }
        var masters = form[dependon];
        if (!masters) {
            continue;
        }
        if (masters.tagName) {
            // If master is radio buttons, we get an array, otherwise we don't.
            // Convert both cases to an array for convinience.
            masters = [masters];
        }
        for (var j = 0; j < masters.length; j++) {
            master = masters[j];
            master.formid = formid;
            master.onclick  = function() {return lockoptionsall(this.formid);};
            master.onblur   = function() {return lockoptionsall(this.formid);};
            master.onchange = function() {return lockoptionsall(this.formid);};
        }
    }
    for (var i = 0; i < form.elements.length; i++) {
        var formelement = form.elements[i];
        if (formelement.type=='reset') {
            formelement.formid = formid;
            formelement.onclick  = function() {this.form.reset();return lockoptionsall(this.formid);};
            formelement.onblur   = function() {this.form.reset();return lockoptionsall(this.formid);};
            formelement.onchange = function() {this.form.reset();return lockoptionsall(this.formid);};
        }
    }
    return lockoptionsall(formid);
}


function submitFormById(id) {
    var theform = document.getElementById(id);
    if(!theform) {
        return false;
    }
    if(theform.tagName.toLowerCase() != 'form') {
        return false;
    }
    if(!theform.onsubmit || theform.onsubmit()) {
        return theform.submit();
    }
}

function select_all_in(elTagName, elClass, elId) {
    var inputs = document.getElementsByTagName('input');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elClass, elId);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
            inputs[i].checked = 'checked';
        }
    }
}

function deselect_all_in(elTagName, elClass, elId) {
    var inputs = document.getElementsByTagName('INPUT');
    inputs = filterByParent(inputs, function(el) {return findParentNode(el, elTagName, elClass, elId);});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
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
    while(el.nodeName.toUpperCase() != 'BODY') {
        if(
            (!elName || el.nodeName.toUpperCase() == elName) &&
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
    findChildNode (start, elementName, elementClass, elementID)

    Travels down the DOM hierarchy to find all child elements with the
    specified tag name, class, and id. All conditions must be met,
    but any can be ommitted.
    Doesn't examine children of matches.
*/
function findChildNodes(start, tagName, elementClass, elementID, elementName) {
    var children = new Array();
    for (var i = 0; i < start.childNodes.length; i++) {
        var classfound = false;
        var child = start.childNodes[i];
        if((child.nodeType == 1) &&//element node type
                  (elementClass && (typeof(child.className)=='string'))) {
            var childClasses = child.className.split(/\s+/);
            for (var childClassIndex in childClasses) {
                if (childClasses[childClassIndex]==elementClass) {
                    classfound = true;
                    break;
                }
            }
        }
        if(child.nodeType == 1) { //element node type
            if  ( (!tagName || child.nodeName == tagName) &&
                (!elementClass || classfound)&&
                (!elementID || child.id == elementID) &&
                (!elementName || child.name == elementName))
            {
                children = children.concat(child);
            } else {
                children = children.concat(findChildNodes(child, tagName, elementClass, elementID, elementName));
            }
        }
    }
    return children;
}
/*
    elementSetHide (elements, hide)

    Adds or removes the "hide" class for the specified elements depending on boolean hide.
*/
function elementShowAdvanced(elements, show) {
    for (var elementIndex in elements) {
        element = elements[elementIndex];
        element.className = element.className.replace(new RegExp(' ?hide'), '')
        if(!show) {
            element.className += ' hide';
        }
    }
}

function showAdvancedInit(addBefore, nameAttr, buttonLabel, hideText, showText) {
    var showHideButton = document.createElement("input");
    showHideButton.type = 'button';
    showHideButton.value = buttonLabel;
    showHideButton.name = nameAttr;
    showHideButton.moodle = {
        hideLabel: hideText,
        showLabel: showText
    };
    YAHOO.util.Event.addListener(showHideButton, 'click', showAdvancedOnClick);
    el = document.getElementById(addBefore);
    el.parentNode.insertBefore(showHideButton, el);
}

function showAdvancedOnClick(e) {
    var button = e.target ? e.target : e.srcElement;

    var toSet=findChildNodes(button.form, null, 'advanced');
    var buttontext = '';
    if (button.form.elements['mform_showadvanced_last'].value == '0' ||  button.form.elements['mform_showadvanced_last'].value == '' ) {
        elementShowAdvanced(toSet, true);
        buttontext = button.moodle.hideLabel;
        button.form.elements['mform_showadvanced_last'].value = '1';
    } else {
        elementShowAdvanced(toSet, false);
        buttontext = button.moodle.showLabel;
        button.form.elements['mform_showadvanced_last'].value = '0';
    }
    var formelements = button.form.elements;
    // Fixed MDL-10506
    for (var i = 0; i < formelements.length; i++) {
        if (formelements[i] && formelements[i].name && (formelements[i].name=='mform_showadvanced')) {
            formelements[i].value = buttontext;
        }
    }
    //never submit the form if js is enabled.
    return false;
}

function unmaskPassword(id) {
  var pw = document.getElementById(id);
  var chb = document.getElementById(id+'unmask');

  try {
    // first try IE way - it can not set name attribute later
    if (chb.checked) {
      var newpw = document.createElement('<input type="text" name="'+pw.name+'">');
    } else {
      var newpw = document.createElement('<input type="password" name="'+pw.name+'">');
    }
    newpw.attributes['class'].nodeValue = pw.attributes['class'].nodeValue;
  } catch (e) {
    var newpw = document.createElement('input');
    newpw.setAttribute('name', pw.name);
    if (chb.checked) {
      newpw.setAttribute('type', 'text');
    } else {
      newpw.setAttribute('type', 'password');
    }
    newpw.setAttribute('class', pw.getAttribute('class'));
  }
  newpw.id = pw.id;
  newpw.size = pw.size;
  newpw.onblur = pw.onblur;
  newpw.onchange = pw.onchange;
  newpw.value = pw.value;
  pw.parentNode.replaceChild(newpw, pw);
}

/*
    elementToggleHide (element, elementFinder)

    If elementFinder is not provided, toggles the "hidden" class for the specified element.
    If elementFinder is provided, then the "hidden" class will be toggled for the object
    returned by the function call elementFinder(element).

    If persistent == true, also sets a cookie for this.
*/
function elementToggleHide(el, persistent, elementFinder, strShow, strHide) {
    if(!elementFinder) {
        var obj = el;  //el:container
        el = document.getElementById('togglehide_'+obj.id);
    }
    else {
        var obj = elementFinder(el);  //el:button.
    }
    if(obj.className.indexOf('hidden') == -1) {
        obj.className += ' hidden';
        if (el.src) {
            el.src = el.src.replace('switch_minus', 'switch_plus');
            el.alt = strShow;
            el.title = strShow;
        }
        var shown = 0;
    }
    else {
        obj.className = obj.className.replace(new RegExp(' ?hidden'), '');
        if (el.src) {
            el.src = el.src.replace('switch_plus', 'switch_minus');
            el.alt = strHide;
            el.title = strHide;
        }
        var shown = 1;
    }

    if(persistent == true) {
        new cookie('hide:' + obj.id, 1, (shown ? -1 : 356), '/').set();
    }
}

function elementCookieHide(id, strShow, strHide) {
    var obj  = document.getElementById(id);
    var cook = new cookie('hide:' + id).read();
    if(cook != null) {
        elementToggleHide(obj, false, null, strShow, strHide);
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
    All this is here just so that IE gets to handle oversized blocks
    in a visually pleasing manner. It does a browser detect. So sue me.
*/

function fix_column_widths() {
    var agt = navigator.userAgent.toLowerCase();
    if ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1)) {
        fix_column_width('left-column');
        fix_column_width('right-column');
    }
}

function fix_column_width(colName) {
    if(column = document.getElementById(colName)) {
        if(!column.offsetWidth) {
            setTimeout("fix_column_width('" + colName + "')", 20);
            return;
        }

        var width = 0;
        var nodes = column.childNodes;

        for(i = 0; i < nodes.length; ++i) {
            if(nodes[i].className.indexOf("sideblock") != -1 ) {
                if(width < nodes[i].offsetWidth) {
                    width = nodes[i].offsetWidth;
                }
            }
        }

        for(i = 0; i < nodes.length; ++i) {
            if(nodes[i].className.indexOf("sideblock") != -1 ) {
                nodes[i].style.width = width + 'px';
            }
        }
    }
}


/*
   Insert myValue at current cursor position
 */
function insertAtCursor(myField, myValue) {
    // IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    // Mozilla/Netscape support
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}


/*
        Call instead of setting window.onload directly or setting body onload=.
        Adds your function to a chain of functions rather than overwriting anything
        that exists.
*/
function addonload(fn) {
    var oldhandler=window.onload;
    window.onload=function() {
        if(oldhandler) oldhandler();
            fn();
    }
}
