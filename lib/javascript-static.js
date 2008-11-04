// Miscellaneous core Javascript functions for Moodle

function popupchecker(msg) {
    var testwindow = window.open('itestwin.html', '', 'width=1,height=1,left=0,top=0,scrollbars=no');
    if (testwindow == null)
        {alert(msg);}
    else {
        testwindow.close();
    }
}

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


function lockoptionsall(formid) {
    var form = document.forms[formid];
    var dependons = eval(formid+'items');
    var tolock = Array();
    for (var dependon in dependons) {
        // change for MooTools compatibility
        if (!dependons.propertyIsEnumerable(dependon)) {
            continue;
        }
        var master = form[dependon];
        if (master === undefined) {
            continue;
        }
        for (var condition in dependons[dependon]) {
            for (var value in dependons[dependon][condition]) {
                var lock;
                switch (condition) {
                  case 'notchecked':
                      lock = !master.checked; break;
                  case 'checked':
                      lock = master.checked; break;
                  case 'noitemselected':
                      lock = master.selectedIndex==-1; break;
                  case 'eq':
                      lock = master.value==value; break;
                  default:
                      lock = master.value!=value; break;
                }
                for (var ei in dependons[dependon][condition][value]) {
                    // change for MooTools compatibility
                    if (!window.webkit && (!dependons[dependon][condition][value].propertyIsEnumerable(ei))) {
                        continue;
                    }
                    var eltolock = dependons[dependon][condition][value][ei];
                    if (tolock[eltolock] != null){
                        tolock[eltolock] =
                                lock || tolock[eltolock];
                    } else {
                        tolock[eltolock] = lock;
                    }
                }
            }
        }
    }
    for (var el in tolock){
        // change for MooTools compatibility
        if (!tolock.propertyIsEnumerable(el)) {
            continue;
        }
        var formelement = form[el];
        if ((formelement === undefined) || (formelement.disabled === undefined)) {
            continue;
        }
        formelement.disabled = tolock[el];
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
        var master = form[dependon];
        if (master === undefined) {
            continue;
        }
        master.formid = formid;
        master.onclick  = function() {return lockoptionsall(this.formid);};
        master.onblur   = function() {return lockoptionsall(this.formid);};
        master.onchange = function() {return lockoptionsall(this.formid);};
    }
    for (var i = 0; i < form.elements.length; i++){
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
                  (elementClass && (typeof(child.className)=='string'))){
            var childClasses = child.className.split(/\s+/);
            for (var childClassIndex in childClasses){
                if (childClasses[childClassIndex]==elementClass){
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
    for (var elementIndex in elements){
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
    for (var i = 0; i < formelements.length; i++){
        if (formelements[i] && formelements[i].name && (formelements[i].name=='mform_showadvanced')){
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

function uncheckall() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++) {
    void(el[i].checked=0);
  }
}

function checkall() {
  void(d=document);
  void(el=d.getElementsByTagName('INPUT'));
  for(i=0;i<el.length;i++) {
    void(el[i].checked=1);
  }
}

function getElementsByClassName(oElm, strTagName, oClassNames){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var arrRegExpClassNames = new Array();
    if(typeof oClassNames == "object"){
        for(var i=0; i<oClassNames.length; i++){
            arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames[i].replace(/\-/g, "\\-") + "(\\s|$)"));
        }
    }
    else{
        arrRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames.replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<arrElements.length; j++){
        oElement = arrElements[j];
        bMatchesAll = true;
        for(var k=0; k<arrRegExpClassNames.length; k++){
            if(!arrRegExpClassNames[k].test(oElement.className)){
                bMatchesAll = false;
                break;
            }
        }
        if(bMatchesAll){
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}

function openpopup(url, name, options, fullscreen) {
    var fullurl = moodle_cfg.wwwroot + url;
    var windowobj = window.open(fullurl,name,options);
    if (fullscreen) {
        windowobj.moveTo(0,0);
        windowobj.resizeTo(screen.availWidth,screen.availHeight);
    }
    windowobj.focus();
    return false;
}

/* This is only used on a few help pages. */
emoticons_help = {
    inputarea: null,

    init: function(formname, fieldname, listid) {
        if (!opener || !opener.document.forms[formname]) {
            return;
        }
        emoticons_help.inputarea = opener.document.forms[formname][fieldname];
        if (!emoticons_help.inputarea) {
            return;
        }
        var emoticons = document.getElementById(listid).getElementsByTagName('li');
        for (var i = 0; i < emoticons.length; i++) {
            var text = emoticons[i].getElementsByTagName('img')[0].alt;
            YAHOO.util.Event.addListener(emoticons[i], 'click', emoticons_help.inserttext, text);
        }
    },

    inserttext: function(e, text) {
        text = ' ' + text + ' ';
        if (emoticons_help.inputarea.createTextRange && emoticons_help.inputarea.caretPos) {
            var caretPos = emoticons_help.inputarea.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
        } else {
            emoticons_help.inputarea.value  += text;
        }
        emoticons_help.inputarea.focus();
    }
}

/**
 * Makes a best effort to connect back to Moodle to update a user preference,
 * however, there is no mechanism for finding out if the update succeeded.
 *
 * Before you can use this function in your JavsScript, you must have called
 * user_preference_allow_ajax_update from moodlelib.php to tell Moodle that
 * the udpate is allowed, and how to safely clean and submitted values.
 *
 * @param String name the name of the setting to udpate.
 * @param String the value to set it to.
 */
function set_user_preference(name, value) {
    // Don't generate a script error if the library has not been loaded,
    // unless we are a Developer, in which case we want the error.
    if (YAHOO && YAHOO.util && YAHOO.util.Connect || moodle_cfg.developerdebug) {
        var url = moodle_cfg.wwwroot + '/lib/ajax/setuserpref.php?sesskey=' +
                moodle_cfg.sesskey + '&pref=' + encodeURI(name) + '&value=' + encodeURI(value);

        // If we are a developer, ensure that failures are reported.
        var callback = {};
        if (moodle_cfg.developerdebug) {
            callback.failure = function() {
                var a = document.createElement('a');
                a.href = url;
                a.classname = 'error';
                a.appendChild(document.createTextNode("Error updating user preference '" + name + "' using ajax. Clicking this link will repeat the Ajax call that failed so you can see the error."));
                document.body.insertBefore(a, document.body.firstChild);
            }
        }

        // Make the request.
        YAHOO.util.Connect.asyncRequest('GET', url, callback); 
    }
}

function moodle_initialise_body() {
    document.body.className += ' jsenabled';
}

/**
 * Oject to handle a collapsible region, see print_collapsible_region in weblib.php
 * @constructor
 * @param String id the HTML id for the div.
 * @param String userpref the user preference that records the state of this box. false if none.
 * @param Boolean startcollapsed whether the box should start collapsed.
 */
function collapsible_region(id, userpref, strtooltip) {
    // Record the pref name
    this.userpref = userpref;

    // Find the divs in the document.
    this.div = document.getElementById(id);
    this.innerdiv = document.getElementById(id + '_sizer');
    this.caption = document.getElementById(id + '_caption');
    this.caption.title = strtooltip;

    // Put the contents of caption in an <a> to make it focussable.
    var a = document.createElement('a');
    while (e = this.caption.firstChild) {
        a.appendChild(e);
    }
    a.href = '#';
    this.caption.appendChild(a);

    // Create the animation.
    this.animation = new YAHOO.util.Anim(this.div, {}, 0.3, YAHOO.util.Easing.easeBoth);

    // Get to the right initial state.
    if (this.div.className.match(/\bcollapsed\b/)) {
        this.collapsed = true;
        var region = YAHOO.util.Region.getRegion(this.caption);
        this.div.style.height = (region.bottom - region.top + 3) + 'px';
    }

    // Add the appropriate image.
    this.icon = document.createElement('img');
    this.icon.id = id + '_icon';
    this.icon.alt = '';
    if (this.collapsed) {
        this.icon.src = moodle_cfg.pixpath + '/t/collapsed.png';
    } else {
        this.icon.src = moodle_cfg.pixpath + '/t/expanded.png';
    }
    a.appendChild(this.icon);

    // Hook up the event handler.
    var self = this;
    YAHOO.util.Event.addListener(a, 'click', function(e) {self.handle_click(e);});

    // Handler for the animation finishing.
    this.animation.onComplete.subscribe(function() {self.handle_animation_complete();});
}

/**
 * The user preference that stores the state of this box.
 * @property userpref, innerdiv, captiondiv
 * @type String
 */
collapsible_region.prototype.userpref = null;

/**
 * The key divs that make up this 
 * @property div, innerdiv, captiondiv
 * @type HTMLDivElement
 */
collapsible_region.prototype.div = null;
collapsible_region.prototype.innerdiv = null;
collapsible_region.prototype.captiondiv = null;

/**
 * The key divs that make up this 
 * @property icon
 * @type HTMLImageElement
 */
collapsible_region.prototype.icon = null;

/**
 * Whether the region is currently collapsed.
 * @property collapsed
 * @type Boolean
 */
collapsible_region.prototype.collapsed = false;

/**
 * @property animation
 * @type YAHOO.util.Anim
 */
collapsible_region.prototype.animation = null;

/** When clicked, toggle the collapsed state, and trigger the animation. */
collapsible_region.prototype.handle_click = function(e) {
    // Toggle the state.
    this.collapsed = !this.collapsed;

    // Stop the click following the link.
    YAHOO.util.Event.stopEvent(e);

    // Animate to the appropriate size.
    if (this.animation.isAnimated()) {
        this.animation.stop();
    }
    if (this.collapsed) {
        var region = YAHOO.util.Region.getRegion(this.caption);
        var targetheight = region.bottom - region.top + 3;
    } else {
        var region = YAHOO.util.Region.getRegion(this.innerdiv);
        var targetheight = region.bottom - region.top + 2;
        this.div.className = this.div.className.replace(/\s*\bcollapsed\b\s*/, ' ');
    }
    this.animation.attributes.height = { to: targetheight, unit: 'px' };
    this.animation.animate();

    // Set the appropriate icon.
    if (this.collapsed) {
        this.icon.src = moodle_cfg.pixpath + '/t/collapsed.png';
    } else {
        this.icon.src = moodle_cfg.pixpath + '/t/expanded.png';
    }

    // Update the user preference.
    if (this.userpref) {
        set_user_preference(this.userpref, this.collapsed);
    }
}

/** When when the animation is finished, add the collapsed class name in relevant. */
collapsible_region.prototype.handle_animation_complete = function() {
    if (this.collapsed) {
        this.div.className += ' collapsed';
    }
}