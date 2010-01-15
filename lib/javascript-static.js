// Miscellaneous core Javascript functions for Moodle
// Global instance of YUI3
var Y = null;
function popupchecker(msg) {
    var testwindow = window.open('', '', 'width=1,height=1,left=0,top=0,scrollbars=no');
    if (!testwindow) {
        alert(msg);
    } else {
        testwindow.close();
    }
}

function checkall() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            inputs[i].checked = true;
        }
    }
}

function checknone() {
    var inputs = document.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type == 'checkbox') {
            inputs[i].checked = false;
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

/**
 * Helper function mainly for drop-down menus' onchange events,
 * submits the form designated by args.id. If args.selectid is also
 * given, it only submits the form if the selected <option> is not
 * the first one (usually the "Choose..." option)
 * Example usage of the html_select component with this function:
 * <pre>
 * $select = new html_select();
 * $select->options = array('delete' => get_string('delete'));
 * $select->name = 'action';
 * $select->button->label = get_string('withselected', 'quiz');
 * $select->id = 'menuaction';
 * $select->add_action('change', 'submit_form_by_id', array('id' => 'attemptsform', 'selectid' => 'menuaction'));
 * echo $OUTPUT->select($select);
 * </pre>
 */
function submit_form_by_id(e, args) {
    var theform = document.getElementById(args.id);
    if (!theform) {
        return false;
    }
    if (theform.tagName.toLowerCase() != 'form') {
        return false;
    }
    if (args.selectid) {
        var select = document.getElementById(args.selectid);
        if (select.selectedIndex == 0) {
            return false;
        }
    }
    return theform.submit();
}

/**
 * Either check, or uncheck, all checkboxes inside the element with id is
 * @param id the id of the container
 * @param checked the new state, either '' or 'checked'.
 */
function select_all_in_element_with_id(id, checked) {
    var container = document.getElementById(id);
    if (!container) {
        return;
    }
    var inputs = container.getElementsByTagName('input');
    for (var i = 0; i < inputs.length; ++i) {
        if (inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
            inputs[i].checked = checked;
        }
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
    while (el.nodeName.toUpperCase() != 'BODY') {
        if ((!elName || el.nodeName.toUpperCase() == elName) &&
            (!elClass || el.className.indexOf(elClass) != -1) &&
            (!elId || el.id == elId)) {
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
        hideLabel: mstr.form.hideadvanced,
        showLabel: mstr.form.showadvanced
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

/**
 * Search a Moodle form to find all the fdate_time_selector and fdate_selector
 * elements, and add date_selector_calendar instance to each.
 */
function init_date_selectors(firstdayofweek) {
    var els = YAHOO.util.Dom.getElementsByClassName('fdate_time_selector', 'fieldset');
    for (var i = 0; i < els.length; i++) {
        new date_selector_calendar(els[i], firstdayofweek);
    }
    els = YAHOO.util.Dom.getElementsByClassName('fdate_selector', 'fieldset');
    for (i = 0; i < els.length; i++) {
        new date_selector_calendar(els[i], firstdayofweek);
    }
}

/**
 * Constructor for a JavaScript object that connects to a fdate_time_selector
 * or a fdate_selector in a Moodle form, and shows a popup calendar whenever
 * that element has keyboard focus.
 * @param el the fieldset class="fdate_time_selector" or "fdate_selector".
 */
function date_selector_calendar(el, firstdayofweek) {
    // Ensure that the shared div and calendar exist.
    if (!date_selector_calendar.panel) {
        date_selector_calendar.panel = new YAHOO.widget.Panel('date_selector_calendar_panel',
                {visible: false, draggable: false});
        var div = document.createElement('div');
        date_selector_calendar.panel.setBody(div);
        date_selector_calendar.panel.render(document.body);

        YAHOO.util.Event.addListener(document, 'click', date_selector_calendar.document_click);
        date_selector_calendar.panel.showEvent.subscribe(function() {
            date_selector_calendar.panel.fireEvent('changeContent');
        });
        date_selector_calendar.panel.hideEvent.subscribe(date_selector_calendar.release_current);

        date_selector_calendar.calendar = new YAHOO.widget.Calendar(div,
                {iframe: false, hide_blank_weeks: true, start_weekday: firstdayofweek});
        date_selector_calendar.calendar.renderEvent.subscribe(function() {
            date_selector_calendar.panel.fireEvent('changeContent');
            date_selector_calendar.delayed_reposition();
        });
    }

    this.fieldset = el;
    var controls = el.getElementsByTagName('select');
    for (var i = 0; i < controls.length; i++) {
        if (/\[year\]$/.test(controls[i].name)) {
            this.yearselect = controls[i];
        } else if (/\[month\]$/.test(controls[i].name)) {
            this.monthselect = controls[i];
        } else if (/\[day\]$/.test(controls[i].name)) {
            this.dayselect = controls[i];
        } else {
            YAHOO.util.Event.addFocusListener(controls[i], date_selector_calendar.cancel_any_timeout, this);
            YAHOO.util.Event.addBlurListener(controls[i], this.blur_event, this);
        }
    }
    if (!(this.yearselect && this.monthselect && this.dayselect)) {
        throw 'Failed to initialise calendar.';
    }
    YAHOO.util.Event.addFocusListener([this.yearselect, this.monthselect, this.dayselect], this.focus_event, this);
    YAHOO.util.Event.addBlurListener([this.yearselect, this.monthselect, this.dayselect], this.blur_event, this);

    this.enablecheckbox = el.getElementsByTagName('input')[0];
    if (this.enablecheckbox) {
        YAHOO.util.Event.addFocusListener(this.enablecheckbox, this.focus_event, this);
        YAHOO.util.Event.addListener(this.enablecheckbox, 'change', this.focus_event, this);
        YAHOO.util.Event.addBlurListener(this.enablecheckbox, this.blur_event, this);
    }
}

/** The pop-up calendar that contains the calendar. */
date_selector_calendar.panel = null;

/** The shared YAHOO.widget.Calendar used by all date_selector_calendars. */
date_selector_calendar.calendar = null;

/** The date_selector_calendar that currently owns the shared stuff. */
date_selector_calendar.currentowner = null;

/** Used as a timeout when hiding the calendar on blur - so we don't hide the calendar
 * if we are just jumping from on of our controls to another. */
date_selector_calendar.hidetimeout = null;

/** Timeout for repositioning after a delay after a change of months. */
date_selector_calendar.repositiontimeout = null;

/** Member variables. Pointers to various bits of the DOM. */
date_selector_calendar.prototype.fieldset = null;
date_selector_calendar.prototype.yearselect = null;
date_selector_calendar.prototype.monthselect = null;
date_selector_calendar.prototype.dayselect = null;
date_selector_calendar.prototype.enablecheckbox = null;

date_selector_calendar.cancel_any_timeout = function() {
    if (date_selector_calendar.hidetimeout) {
        clearTimeout(date_selector_calendar.hidetimeout);
        date_selector_calendar.hidetimeout = null;
    }
    if (date_selector_calendar.repositiontimeout) {
        clearTimeout(date_selector_calendar.repositiontimeout);
        date_selector_calendar.repositiontimeout = null;
    }
}

date_selector_calendar.delayed_reposition = function() {
    if (date_selector_calendar.repositiontimeout) {
        clearTimeout(date_selector_calendar.repositiontimeout);
        date_selector_calendar.repositiontimeout = null;
    }
    date_selector_calendar.repositiontimeout = setTimeout(date_selector_calendar.fix_position, 500);
}

date_selector_calendar.fix_position = function() {
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.panel.cfg.setProperty('context', [date_selector_calendar.currentowner.fieldset, 'bl', 'tl']);
    }
}

date_selector_calendar.release_current = function() {
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.currentowner.release_calendar();
    }
}

date_selector_calendar.prototype.focus_event = function(e, me) {
    date_selector_calendar.cancel_any_timeout();
    if (me.enablecheckbox == null || me.enablecheckbox.checked) {
        me.claim_calendar();
    } else {
        if (date_selector_calendar.currentowner) {
            date_selector_calendar.currentowner.release_calendar();
        }
    }
}

date_selector_calendar.prototype.blur_event = function(e, me) {
    date_selector_calendar.hidetimeout = setTimeout(date_selector_calendar.release_current, 300);
}

date_selector_calendar.prototype.handle_select_change = function(e, me) {
    me.set_date_from_selects();
}

date_selector_calendar.document_click = function(event) {
    if (date_selector_calendar.currentowner) {
        var currentcontainer = date_selector_calendar.currentowner.fieldset;
        var eventarget = YAHOO.util.Event.getTarget(event);
        if (YAHOO.util.Dom.isAncestor(currentcontainer, eventarget)) {
            setTimeout(function() {date_selector_calendar.cancel_any_timeout()}, 100);
        } else {
            date_selector_calendar.currentowner.release_calendar();
        }
    }
}

date_selector_calendar.prototype.claim_calendar = function() {
    date_selector_calendar.cancel_any_timeout();
    if (date_selector_calendar.currentowner == this) {
        return;
    }
    if (date_selector_calendar.currentowner) {
        date_selector_calendar.currentowner.release_calendar();
    }

    if (date_selector_calendar.currentowner != this) {
        this.connect_handlers();
    }
    date_selector_calendar.currentowner = this;

    date_selector_calendar.calendar.cfg.setProperty('mindate', new Date(this.yearselect.options[0].value, 0, 1));
    date_selector_calendar.calendar.cfg.setProperty('maxdate', new Date(this.yearselect.options[this.yearselect.options.length - 1].value, 11, 31));
    this.fieldset.insertBefore(date_selector_calendar.panel.element, this.yearselect.nextSibling);
    this.set_date_from_selects();
    date_selector_calendar.panel.show();
    var me = this;
    setTimeout(function() {date_selector_calendar.cancel_any_timeout()}, 100);
}

date_selector_calendar.prototype.set_date_from_selects = function() {
    var year = parseInt(this.yearselect.value);
    var month = parseInt(this.monthselect.value) - 1;
    var day = parseInt(this.dayselect.value);
    date_selector_calendar.calendar.select(new Date(year, month, day));
    date_selector_calendar.calendar.setMonth(month);
    date_selector_calendar.calendar.setYear(year);
    date_selector_calendar.calendar.render();
    date_selector_calendar.fix_position();
}

date_selector_calendar.prototype.set_selects_from_date = function(eventtype, args) {
    var date = args[0][0];
    var newyear = date[0];
    var newindex = newyear - this.yearselect.options[0].value;
    this.yearselect.selectedIndex = newindex;
    this.monthselect.selectedIndex = date[1] - this.monthselect.options[0].value;
    this.dayselect.selectedIndex = date[2] - this.dayselect.options[0].value;
}

date_selector_calendar.prototype.connect_handlers = function() {
    YAHOO.util.Event.addListener([this.yearselect, this.monthselect, this.dayselect], 'change', this.handle_select_change, this);
    date_selector_calendar.calendar.selectEvent.subscribe(this.set_selects_from_date, this, true);
}

date_selector_calendar.prototype.release_calendar = function() {
    date_selector_calendar.panel.hide();
    date_selector_calendar.currentowner = null;
    YAHOO.util.Event.removeListener([this.yearselect, this.monthselect, this.dayselect], this.handle_select_change);
    date_selector_calendar.calendar.selectEvent.unsubscribe(this.set_selects_from_date, this);
}

function filterByParent(elCollection, parentFinder) {
    var filteredCollection = [];
    for (var i = 0; i < elCollection.length; ++i) {
        var findParent = parentFinder(elCollection[i]);
        if (findParent.nodeName.toUpperCase != 'BODY') {
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
/**
 * Replacement for getElementsByClassName in browsers that aren't cool enough
 *
 * Relying on the built-in getElementsByClassName is far, far faster than
 * using YUI.
 *
 * Note: the third argument used to be an object with odd behaviour. It now
 * acts like the 'name' in the HTML5 spec, though the old behaviour is still
 * mimicked if you pass an object.
 *
 * @param {Node} oElm The top-level node for searching. To search a whole
 *                    document, use `document`.
 * @param {String} strTagName filter by tag names
 * @param {String} name same as HTML5 spec
 */
function getElementsByClassName(oElm, strTagName, name) {
    // for backwards compatibility
    if(typeof name == "object") {
        var names = new Array();
        for(var i=0; i<name.length; i++) names.push(names[i]);
        name = names.join('');
    }
    // use native implementation if possible
    if (oElm.getElementsByClassName && Array.filter) {
        if (strTagName == '*') {
            return oElm.getElementsByClassName(name);
        } else {
            return Array.filter(oElm.getElementsByClassName(name), function(el) {
                return el.nodeName.toLowerCase() == strTagName.toLowerCase();
            });
        }
    }
    // native implementation unavailable, fall back to slow method
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var arrRegExpClassNames = new Array();
    var names = name.split(' ');
    for(var i=0; i<names.length; i++) {
        arrRegExpClassNames.push(new RegExp("(^|\\s)" + names[i].replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<arrElements.length; j++) {
        oElement = arrElements[j];
        bMatchesAll = true;
        for(var k=0; k<arrRegExpClassNames.length; k++) {
            if(!arrRegExpClassNames[k].test(oElement.className)) {
                bMatchesAll = false;
                break;
            }
        }
        if(bMatchesAll) {
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}

function openpopup(event, args) {

    YAHOO.util.Event.preventDefault(event);

    var fullurl = args.url;
    if (!args.url.match(/https?:\/\//)) {
        fullurl = moodle_cfg.wwwroot + args.url;
    }
    var windowobj = window.open(fullurl,args.name,args.options);
    if (!windowobj) {
        return true;
    }
    if (args.fullscreen) {
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
function collapsible_region(id, userpref, strtooltip, collapsedicon, expandedicon) {
    // Record the pref name
    this.userpref = userpref;
    this.collapsedicon = collapsedicon;
    this.expandedicon = expandedicon;

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
        var self = this;
        setTimeout(function() {
            var region = YAHOO.util.Region.getRegion(self.caption);
            self.div.style.height = (region.bottom - region.top + 3) + 'px';
        }, 10);
    }

    // Add the appropriate image.
    this.icon = document.createElement('img');
    this.icon.id = id + '_icon';
    this.icon.alt = '';
    if (this.collapsed) {
        this.icon.src = this.collapsedicon;
    } else {
        this.icon.src = this.expandedicon;
    }
    a.appendChild(this.icon);

    // Hook up the event handler.
    YAHOO.util.Event.addListener(a, 'click', this.handle_click, null, this);

    // Handler for the animation finishing.
    this.animation.onComplete.subscribe(function() {self.handle_animation_complete();});
}

/**
 * The user preference that stores the state of this box.
 * @property userpref
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
        this.icon.src =this.collapsedicon;
    } else {
        this.icon.src = this.expandedicon;
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

/**
 * Oject to handle expanding and collapsing blocks when an icon is clicked on.
 * @constructor
 * @param String id the HTML id for the div.
 * @param String userpref the user preference that records the state of this block.
 * @param String visibletooltip tool tip/alt to show when the block is visible.
 * @param String hiddentooltip tool tip/alt to show when the block is hidden.
 * @param String visibleicon URL of the icon to show when the block is visible.
 * @param String hiddenicon URL of the icon to show when the block is hidden.
 */
function block_hider(id, userpref, visibletooltip, hiddentooltip, visibleicon, hiddenicon) {
    // Find the elemen that is the block.
    this.block = document.getElementById(id);
    var title_div = YAHOO.util.Dom.getElementsByClassName('title', 'div', this.block);
    if (!title_div || !title_div[0]) {
        return this;
    }
    title_div = title_div[0];
    this.ishidden = YAHOO.util.Dom.hasClass(this.block, 'hidden');

    // Record the pref name
    this.userpref = userpref;
    this.visibletooltip = visibletooltip;
    this.hiddentooltip = hiddentooltip;
    this.visibleicon = visibleicon;
    this.hiddenicon = hiddenicon;

    // Add the icon.
    this.icon = document.createElement('input');
    this.icon.type = 'image';
    this.icon.className = 'hide-show-image';
    this.update_state();
    title_div.insertBefore(this.icon, title_div.firstChild);

    // Hook up the event handler.
    YAHOO.util.Event.addListener(this.icon, 'click', this.handle_click, null, this);
}

/** Handle click on a block show/hide icon. */
block_hider.prototype.handle_click = function(e) {
    YAHOO.util.Event.stopEvent(e);
    this.ishidden = !this.ishidden;
    this.update_state();
    set_user_preference(this.userpref, this.ishidden);
}

/** Set the state of the block show/hide icon to this.ishidden. */
block_hider.prototype.update_state = function () {
    if (this.ishidden) {
        YAHOO.util.Dom.addClass(this.block, 'hidden');
        this.icon.alt = this.hiddentooltip;
        this.icon.title = this.hiddentooltip;
        this.icon.src = this.hiddenicon;
    } else {
        YAHOO.util.Dom.removeClass(this.block, 'hidden');
        this.icon.alt = this.visibletooltip;
        this.icon.title = this.visibletooltip;
        this.icon.src = this.visibleicon;
    }
}

/** Close the current browser window. */
function close_window(e) {
    YAHOO.util.Event.preventDefault(e);
    self.close();
}

/**
 * Close the current browser window, forcing the window/tab that opened this
 * popup to reload itself. */
function close_window_reloading_opener() {
    if (window.opener) {
        window.opener.location.reload(1);
        close_window();
        // Intentionally, only try to close the window if there is some evidence we are in a popup.
    }
}

/**
 * Used in a couple of modules to hide navigation areas when using AJAX
 */
function hide_item(itemid) {
    var item = document.getElementById(itemid);
    if (item) {
        item.style.display = "none";
    }
}

function show_item(itemid) {
    var item = document.getElementById(itemid);
    if (item) {
        item.style.display = "";
    }
}

function destroy_item(itemid) {
    var item = document.getElementById(itemid);
    if (item) {
        item.parentNode.removeChild(item);
    }
}
/**
 * Tranfer keyboard focus to the HTML element with the given id, if it exists.
 * @param controlid the control id.
 */
function focuscontrol(controlid) {
    var control = document.getElementById(controlid);
    if (control) {
        control.focus();
    }
}

/**
 * Transfers keyboard focus to an HTML element based on the old style style of focus
 * This function should be removed as soon as it is no longer used
 */
function old_onload_focus(formid, controlname) {
    if (document.forms[formid] && document.forms[formid].elements && document.forms[formid].elements[controlname]) {
        document.forms[formid].elements[controlname].focus();
    }
}

function scroll_to_end() {
    window.scrollTo(0, 5000000);
}

var scrolltoendtimeout;
function repeatedly_scroll_to_end() {
    scrolltoendtimeout = setInterval(scroll_to_end, 50);
}

function cancel_scroll_to_end() {
    if (scrolltoendtimeout) {
        clearTimeout(scrolltoendtimeout);
        scrolltoendtimeout = null;
    }
}

function create_UFO_object(eid) {
    UFO.create(FO, eid);
}
function build_querystring(obj) {
    if (typeof obj !== 'object') {
        return null;
    }
    var list = [];
    for(var k in obj) {
        k = encodeURIComponent(k);
        var value = obj[k];
        if(obj[k] instanceof Array) {
            for(var i in value) {
                list.push(k+'[]='+encodeURIComponent(value[i]));
            }
        } else {
            list.push(k+'='+encodeURIComponent(value));
        }
    }
    return list.join('&');
}

function stripHTML(str) {
    var re = /<\S[^><]*>/g;
    var ret = str.replace(re, "");
    return ret;
}

function json_decode(json) {
    try {
        var obj = YAHOO.lang.JSON.parse(json);
    } catch (e) {
        alert(e.toString() + "\n" + stripHTML(json));
    }
    return obj;
}

function json_encode(data) {
    try {
        var json = YAHOO.lang.JSON.stringify(data);
    } catch (e) {
        alert(e.toString());
    }
    return json;
}

/**
 * Finds all help icons on the page and initiates YUI tooltips for
 * each of them, which load a truncated version of the help's content
 * on-the-fly asynchronously
 */
function init_help_icons() {
    // var logger = new YAHOO.widget.LogReader(document.body, {draggable: true});

    var iconspans = YAHOO.util.Dom.getElementsByClassName('helplink', 'span');

    var tooltip = new YAHOO.widget.Tooltip('help_icon_tooltip', {
        context: iconspans,
        showdelay: 1000,
        hidedelay: 150,
        autodismissdelay: 50000,
        underlay: 'none',
        zIndex: '1000'
    });

    // remove all titles, they would obscure the YUI tooltip
    for (var i = 0; i < iconspans.length; i++) {
    	iconspans[i].getElementsByTagName('a')[0].title = '';
    }

    tooltip.contextTriggerEvent.subscribe(
        function(type, args) {
            // Fetch help page contents asynchronously
            // Load spinner icon while content is loading
            var spinner = document.createElement('img');
            spinner.src = moodle_cfg.loadingicon;

            this.cfg.setProperty('text', spinner);

            var context = args[0];
            context.title = '';

            var link = context.getElementsByTagName('a')[0];
            var thistooltip = this;
            var ajaxurl = link.href + '&fortooltip=1';


            var callback = {
                success: function(o) {
                    thistooltip.cfg.setProperty('text', o.responseText);
                },
                failure: function(o) {
                    var debuginfo = o.statusText;
                    if (moodle_cfg.developerdebug) {
                        o.statusText += ' (' + ajaxurl + ')';
                    }
                    thistooltip.cfg.setProperty('text', debuginfo);
                }
            };

            var conn = YAHOO.util.Connect.asyncRequest("get", ajaxurl, callback);
        }
    );
}

/**
 * Prints a confirmation dialog in the style of DOM.confirm().
 * @param object event A DOM event
 * @param string message The message to show in the dialog
 * @param string url The URL to forward to if YES is clicked. Disabled if fn is given
 * @param function fn A JS function to run if YES is clicked.
 */
function confirm_dialog(event, args) {
    var message = args.message;
    var target = this;
    target.args = args;
    YAHOO.util.Event.preventDefault(event);

    var simpledialog = new YAHOO.widget.SimpleDialog('confirmdialog',
        { width: '300px',
          fixedcenter: true,
          modal: true,
          visible: false,
          draggable: false
        }
    );

    simpledialog.setHeader(mstr.admin.confirmation);
    simpledialog.setBody(message);
    simpledialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);

    this.handle_cancel = function() {
        this.hide();
    };

    this.handle_yes = function() {
        this.hide();

        if (target.args.callback) {
            // args comes from PHP, so callback will be a string, needs to be evaluated by JS
            var callback = eval('('+target.args.callback+')');
            callback.apply(this);
        }

        if (target.tagName.toLowerCase() == 'a') {
            window.location = target.href;
        } else if (target.tagName.toLowerCase() == 'input') {
            var parentelement = target.parentNode;
            while (parentelement.tagName.toLowerCase() != 'form' && parentelement.tagName.toLowerCase() != 'body') {
                parentelement = parentelement.parentNode;
            }
            if (parentelement.tagName.toLowerCase() == 'form') {
                parentelement.submit();
            }
        } else if(moodle_cfg.developerdebug) {
            alert("Element of type " + target.tagName + " is not supported by the confirm_dialog function. Use A or INPUT");
        }
    };

    var buttons = [ { text: mstr.moodle.cancel, handler: this.handle_cancel, isDefault: true },
                    { text: mstr.moodle.yes, handler: this.handle_yes } ];

    simpledialog.cfg.queueProperty('buttons', buttons);

    simpledialog.render(document.body);
    simpledialog.show();
    return simpledialog;
}

function dialog_callback() {
    console.debug(this);
    console.debug(this.args);
}
Number.prototype.fixed=function(n){
    with(Math)
        return round(Number(this)*pow(10,n))/pow(10,n);
}
function update_progress_bar (id, width, pt, msg, es){
    var percent = pt*100;
    var status = document.getElementById("status_"+id);
    var percent_indicator = document.getElementById("pt_"+id);
    var progress_bar = document.getElementById("progress_"+id);
    var time_es = document.getElementById("time_"+id);
    status.innerHTML = msg;
    percent_indicator.innerHTML = percent.fixed(2) + '%';
    if(percent == 100) {
        progress_bar.style.background = "green";
        time_es.style.display = "none";
    } else {
        progress_bar.style.background = "#FFCC66";
        if (es == Infinity){
            time_es.innerHTML = "Initializing...";
        }else {
            time_es.innerHTML = es.fixed(2)+" sec";
            time_es.style.display
                = "block";
        }
    }
    progress_bar.style.width = width + "px";

}

function frame_breakout(e, properties) {
    this.setAttribute('target', properties.framename);
}

function get_image_url(imagename, component) {
    var url = moodle_cfg.wwwroot + '/theme/image.php?theme=' + moodle_cfg.theme + '&image=' + imagename;

    if (moodle_cfg.themerev > 0) {
        url = url + '&rev=' + moodle_cfg.themerev;
    }

    if (component != '' && component != 'moodle' && component != 'core') {
        url = url + '&component=' + component;
    }

    return url;
}



// ===== Deprecated core Javascript functions for Moodle ====
//       DO NOT USE!!!!!!!
// Do not put this stuff in separate file because it only adds extra load on servers! 

function submitFormById(id) {
    submit_form_by_id(null, {id: id});
}


/**
 * START OF BLOCKS CODE
 * This code can be included in the footer instead of the header if we ever
 * have a static JS file that will be loaded in the footer.
 * Once this is done we will then also be able to remove the blocks.dock.init
 * function and call
 */

/**
 * This namespace will contain all of content (functions, classes, properties)
 * for the block system
 * @namespace
 */
var blocks = blocks || {};
blocks.setup_generic_block = function(uid) {
    Y.use('base','dom','io','node', 'event-custom', function() {
        var block = new blocks.genericblock(uid);
        block.init();
    });
}

/**
 * The dock namespace: Contains all things dock related
 * @namespace
 */
blocks.dock = {
    count:0,        // The number of dock items through the page life
    exists:false,   // True if the dock exists
    items:[],       // An array of dock items
    node:null,      // The YUI node for the dock itself
    earlybinds:[],  // Events added before the dock was augmented to support events
    /**
     * Strings used by the dock/dockitems
     * @namespace
     */
    strings:{
        addtodock : '[[addtodock]]',
        undockitem : '[[undockitem]]',
        undockall : '[[undockall]]'
    },
    /**
     * Configuration parameters used during the initialisation and setup
     * of dock and dock items.
     * This is here specifically so that themers can override core parameters and
     * design aspects without having to re-write navigation
     * @namespace
     */
    cfg:{
        buffer:10,                          // Buffer used when containing a panel
        position:'left',                    // position of the dock
        orientation:'vertical',             // vertical || horizontal determines if we change the title
        /**
         * Display parameters for the dock
         * @namespace
         */
        display:{
            spacebeforefirstitem: 10        // Space between the top of the dock and the first item
        },
        /**
         * CSS classes to use with the dock
         * @namespace
         */
        css: {
            dock:'dock',                    // CSS Class applied to the dock box
            dockspacer:'dockspacer',        // CSS class applied to the dockspacer
            controls:'controls',            // CSS class applied to the controls box
            body:'has_dock',                // CSS class added to the body when there is a dock
            dockeditem:'dockeditem',        // CSS class added to each item in the dock
            dockedtitle:'dockedtitle',      // CSS class added to the item's title in each dock
            activeitem:'activeitem'         // CSS class added to the active item
        },
        /**
         * Configuration options for the panel that items are shown in
         * @namespace
         */
        panel: {
            close:false,                    // Show a close button on the panel
            draggable:false,                // Make the panel draggable
            underlay:"none",                // Use a special underlay
            modal:false,                    // Throws a lightbox if set to true
            keylisteners:null,              // An array of keylisterners to attach
            visible:false,                  // Visible by default
            effect: null,                   // An effect that should be used with the panel
            monitorresize:false,            // Monitor the resize of the panel
            context:null,                   // Sets up contexts for the panel
            fixedcenter:false,              // Always displays the panel in the center of the screen
            zIndex:null,                    // Sets a specific z index for the panel
            constraintoviewport: false,     // Constrain the panel to the viewport
            autofillheight:'body'           // Which container element should fill out empty space
        }
    },
    /**
     * Augments the classes as required and processes early bindings
     */
    init:function() {
        Y.use('event-custom','event', 'node', function(Y){
            // Give the dock item class the event properties/methods
            Y.augment(blocks.dock.item, Y.EventTarget);
            Y.augment(blocks.dock, Y.EventTarget, true);
            // Re-apply early bindings properly now that we can
            blocks.dock.apply_binds();
        });
    },
    /**
     * Adds a dock item into the dock
     * @function
     * @param {blocks.dock.item} item
     */
    add:function(item) {
        item.id = this.count;
        this.count++;
        this.items[item.id] = item;
        this.draw();
        this.items[item.id].draw();
        this.fire('dock:itemadded', item);
    },
    /**
     * Draws the dock
     * @function
     * @return bool
     */
    draw:function() {
        if (this.node !== null) {
            return true;
        }
        this.fire('dock:drawstarted');
        this.node = Y.Node.create('<div id="dock" class="'+blocks.dock.cfg.css.dock+' '+blocks.dock.cfg.css.dock+'_'+blocks.dock.cfg.position+'_'+blocks.dock.cfg.orientation+'"></div>');
        this.node.appendChild(Y.Node.create('<div class="'+blocks.dock.cfg.css.dockspacer+'" style="height:'+blocks.dock.cfg.display.spacebeforefirstitem+'px"></div>'));
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            this.node.setStyle('height', this.node.get('winHeight')+'px');
        }

        var dockcontrol = Y.Node.create('<div class="'+blocks.dock.cfg.css.controls+'"></div>');
        var removeall = Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+blocks.dock.strings.undockall+'" title="'+blocks.dock.strings.undockall+'" />');
        removeall.on('removeall|click', this.remove_all, this);
        dockcontrol.appendChild(removeall);
        this.node.appendChild(dockcontrol);

        Y.one(document.body).appendChild(this.node);
        Y.one(document.body).addClass(blocks.dock.cfg.css.body);
        this.fire('dock:drawcompleted');
        return true;
    },
    /**
     * Removes the node at the given index and puts it back into conventional page sturcture
     * @function
     * @param {int} uid Unique identifier for the block
     * @return {boolean}
     */
    remove:function(uid) {
        if (!this.items[uid]) {
            return false;
        }
        this.items[uid].remove();
        this.fire('dock:itemremoved', uid);
        this.count--;
        if (this.count===0) {
            this.fire('dock:toberemoved');
            this.items = [];
            this.node.remove();
            this.node = null;
            this.fire('dock:removed');
        }
        return true;
    },
    /**
     * Removes all nodes and puts them back into conventional page sturcture
     * @function
     * @return {boolean}
     */
    remove_all:function() {
        for (var i in this.items) {
            this.items[i].remove();
            this.items[i] = null;
        }
        Y.fire('dock:toberemoved');
        this.items = [];
        this.node.remove();
        this.node = null;
        Y.fire('dock:removed');
        return true;
    },
    /**
     * Resizes the active item
     * @function
     * @param {Event} e
     */
    resize:function(e){
        for (var i in this.items) {
            if (this.items[i].active) {
                this.items[i].resize_panel(e);
            }
        }
    },
    /**
     * Hides all [the active] items
     * @function
     */
    hide_all:function() {
        for (var i in this.items) {
            this.items[i].hide();
        }
    },
    /**
     * This smart little function allows developers to attach event listeners before
     * the dock has been augmented to allows event listeners.
     * Once the augmentation is complete this function will be replaced with the proper
     * on method for handling event listeners.
     * Finally apply_binds needs to be called in order to properly bind events.
     * @param {string} event
     * @param {function} callback
     */
    on : function(event, callback) {
        this.earlybinds.push({event:event,callback:callback});
    },
    /**
     * This function takes all early binds and attaches them as listeners properly
     * This should only be called once augmentation is complete.
     */
    apply_binds : function() {
        for (var i in this.earlybinds) {
            var bind = this.earlybinds[i];
            this.on(bind.event, bind.callback);
        }
        this.earlybinds = [];
    },
    /**
     * Namespace containing methods and properties that will be prototyped
     * to the generic block class and possibly overriden by themes
     * @namespace
     */
    abstract_block_class : {

        id : null,                  // The block instance id
        cachedcontentnode : null,   // The cached content node for the actual block
        blockspacewidth : null,     // The width of the block's original container
        skipsetposition : false,    // If true the user preference isn't updated

        /**
         * This function should be called within the block's constructor and is used to
         * set up the initial controls for swtiching block position as well as an initial
         * moves that may be required.
         *
         * @param {YUI.Node} node The node that contains all of the block's content
         */
        init : function(node) {
            if (!node) {
                node = Y.one('#inst'+this.id);
                if (!node) {
                    return;
                }
            }

            var commands = node.one('.header .title .commands');
            if (!commands) {
                commands = Y.Node.create('<div class="commands"></div>');
                if (node.one('.header .title')) {
                    node.one('.header .title').append(commands);
                }
            }

            var moveto = Y.Node.create('<a class="moveto customcommand requiresjs"></a>');
            moveto.append(Y.Node.create('<img src="'+get_image_url('t/dock_to_block', 'moodle')+'" alt="'+blocks.dock.strings.undockitem+'" title="'+blocks.dock.strings.undockitem+'" />'));
            if (location.href.match(/\?/)) {
                moveto.set('href', location.href+'&dock='+this.id);
            } else {
                moveto.set('href', location.href+'?dock='+this.id);
            }
            commands.append(moveto);
            commands.all('a.moveto').on('movetodock|click', this.move_to_dock, this);

            node.all('.customcommand').each(function(){
                this.remove();
                commands.appendChild(this);
            });

            // Move the block straight to the dock if required
            if (node.hasClass('dock_on_load')) {
                node.removeClass('dock_on_load')
                this.skipsetposition = true;
                this.move_to_dock();
            }
        },

        /**
         * This function is reponsible for moving a block from the page structure onto the
         * dock
         * @param {event}
         */
        move_to_dock : function(e) {
            if (e) {
                e.halt(true);
            }

            var node = Y.one('#inst'+this.id);
            var blockcontent = node.one('.content');
            if (!blockcontent) {
                return;
            }

            this.cachedcontentnode = node;

            node.all('a.moveto').each(function(moveto){
                Y.Event.purgeElement(Y.Node.getDOMNode(moveto), false, 'click');
                if (moveto.hasClass('customcommand')) {
                    moveto.all('img').each(function(movetoimg){
                        movetoimg.setAttribute('src', get_image_url('t/dock_to_block', 'moodle'));
                        movetoimg.setAttribute('alt', blocks.dock.strings.undockitem);
                        movetoimg.setAttribute('title', blocks.dock.strings.undockitem);
                    }, this);
                }
            }, this);

            var placeholder = Y.Node.create('<div id="content_placeholder_'+this.id+'"></div>');
            node.replace(Y.Node.getDOMNode(placeholder));
            node = null;

            this.resize_block_space(placeholder);

            var blocktitle = Y.Node.getDOMNode(this.cachedcontentnode.one('.title h2')).cloneNode(true);
            blocktitle.innerHTML = blocktitle.innerHTML.replace(/([a-zA-Z0-9])/g, "$1<br />");

            var commands = this.cachedcontentnode.all('.title .commands');
            var blockcommands = Y.Node.create('<div class="commands"></div>');
            if (commands.size() > 0) {
                blockcommands = commands.item(0);
            }

            // Create a new dock item for the block
            var dockitem = new blocks.dock.item(this.id, blocktitle, blockcontent, blockcommands);
            // Wire the draw events to register remove events
            dockitem.on('dockeditem:drawcomplete', function(e){
                // check the contents block [editing=off]
                this.contents.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    blocks.dock.remove(this.id)
                }, this);
                // check the commands block [editing=on]
                this.commands.all('a.moveto').on('returntoblock|click', function(e){
                    e.halt();
                    blocks.dock.remove(this.id)
                }, this);
            }, dockitem);
            
            // Register an event so that when it is removed we can put it back as a block
            dockitem.on('dock:itemremoved', this.return_to_block, this, dockitem);
            blocks.dock.add(dockitem);

            if (!this.skipsetposition) {
                // save the users preference
                set_user_preference('docked_block_instance_'+this.id, 1);
            } else {
                this.skipsetposition = false;
            }
        },

        /**
         * Resizes the space that contained blocks if there were no blocks left in
         * it. e.g. if all blocks have been moved to the dock
         * @param {Y.Node} node
         */
        resize_block_space : function(node) {
            node = node.ancestor('.block-region');
            if (node) {
                if (node.all('.sideblock').size() === 0 && this.blockspacewidth === null) {
                    // If the node has no children then we can shrink it
                    this.blockspacewidth = node.getStyle('width');
                    node.setStyle('width', '0px');
                } else if (this.blockspacewidth !== null) {
                    // Otherwise if it contains children and we have saved a width
                    // we can reapply the width
                    node.setStyle('width', this.blockspacewidth);
                    this.blockspacewidth = null;
                }
            }
        },

        /**
         * This function removes a block from the dock and puts it back into the page
         * structure.
         * @param {blocks.dock.class.item}
         */
        return_to_block : function(dockitem) {
            var placeholder = Y.one('#content_placeholder_'+this.id);
            this.cachedcontentnode.appendChild(dockitem.contents);
            placeholder.replace(Y.Node.getDOMNode(this.cachedcontentnode));
            this.cachedcontentnode = Y.one('#'+this.cachedcontentnode.get('id'));

            this.resize_block_space(this.cachedcontentnode);

            this.cachedcontentnode.all('a.moveto').each(function(moveto){
                Y.Event.purgeElement(Y.Node.getDOMNode(moveto), false, 'click');
                moveto.on('movetodock|click', this.move_to_dock, this);
                if (moveto.hasClass('customcommand')) {
                    moveto.all('img').each(function(movetoimg){
                        movetoimg.setAttribute('src', get_image_url('t/block_to_dock', 'moodle'));
                        movetoimg.setAttribute('alt', blocks.dock.strings.addtodock);
                        movetoimg.setAttribute('title', blocks.dock.strings.addtodock);
                    }, this);
                }
             }, this);

            var commands = this.cachedcontentnode.all('.commands');
            var blocktitle = this.cachedcontentnode.all('.title');

            if (commands.size() === 1 && blocktitle.size() === 1) {
                commands.item(0).remove();
                blocktitle.item(0).append(commands.item(0));
            }

            this.cachedcontentnode = null;
            set_user_preference('docked_block_instance_'+this.id, 0);
            return true;
        }
    },
    /**
     * This namespace contains the generic properties, methods and events
     * that will be bound to the blocks.dock.item class.
     * These can then be overriden to customise the way dock items work/display
     * @namespace
     */
    abstract_item_class : {
        
        id : null,              // The unique id for the item
        name : null,            // The name of the item
        title : null,           // The title of the item
        contents : null,        // The content of the item
        commands : null,        // The commands for the item
        active : false,         // True if the item is being shown
        panel : null,           // The YUI2 panel the item will be shown in
        preventhide : false,    // If true the next call to hide will be ignored
        cfg : null,             // The config options for this item by default blocks.cfg

        /**
         * Initialises all of the items events
         * @function
         */
        init_events : function() {
            this.publish('dockeditem:drawstart', {prefix:'dockeditem'});
            this.publish('dockeditem:drawcomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:showstart', {prefix:'dockeditem'});
            this.publish('dockeditem:showcomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:hidestart', {prefix:'dockeditem'});
            this.publish('dockeditem:hidecomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:resizestart', {prefix:'dockeditem'});
            this.publish('dockeditem:resizecomplete', {prefix:'dockeditem'});
            this.publish('dockeditem:itemremoved', {prefix:'dockeditem'});
        },

        /**
         * This function draws the item on the dock
         */
        draw : function() {
            this.fire('dockeditem:drawstart');
            var dockitemtitle = Y.Node.create('<div id="dock_item_'+this.id+'_title" class="'+this.cfg.css.dockedtitle+'"></div>');
            dockitemtitle.append(this.title);
            var dockitem = Y.Node.create('<div id="dock_item_'+this.id+'" class="'+this.cfg.css.dockeditem+'"></div>');
            if (blocks.dock.count === 1) {
                dockitem.addClass('firstdockitem');
            }
            dockitem.append(dockitemtitle);
            if (this.commands.hasChildNodes) {
                this.contents.appendChild(this.commands);
            }
            blocks.dock.node.append(dockitem);

            var position = dockitemtitle.getXY();
            position[0] += parseInt(dockitemtitle.get('offsetWidth'));
            if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 8) {
                position[0] -= 2;
            }
            this.panel = new YAHOO.widget.Panel('dock_item_panel_'+this.id, {
                close:this.cfg.panel.close,
                draggable:this.cfg.panel.draggable,
                underlay:this.cfg.panel.underlay,
                modal: this.cfg.panel.modal,
                keylisteners: this.cfg.panel.keylisteners,
                visible:this.cfg.panel.visible,
                effect:this.cfg.panel.effect,
                monitorresize:this.cfg.panel.monitorresize,
                context: this.cfg.panel.context,
                fixedcenter: this.cfg.panel.fixedcenter,
                zIndex: this.cfg.panel.zIndex,
                constraintoviewport: this.cfg.panel.constraintoviewport,
                xy:position,
                autofillheight:this.cfg.panel.autofillheight});
            this.panel.showEvent.subscribe(this.resize_panel, this, true);
            this.panel.setBody(Y.Node.getDOMNode(this.contents));
            this.panel.render(blocks.dock.node);
            dockitem.on('showitem|mouseover', this.show, this);
            this.fire('dockeditem:drawcomplete');
        },
        /**
         * This function removes the node and destroys it's bits
         * @param {Event} e
         */
        remove : function (e) {
            this.hide(e);
            Y.one('#dock_item_'+this.id).remove();
            this.panel.destroy();
            this.fire('dock:itemremoved');
        },
        /**
         * This function toggles makes the item active and shows it
         * @param {event}
         */
        show : function(e) {
            blocks.dock.hide_all();
            this.fire('dockeditem:showstart');
            this.panel.show(e, this);
            this.active = true;
            Y.one('#dock_item_'+this.id+'_title').addClass(this.cfg.css.activeitem);
            Y.detach('mouseover', this.show, Y.one('#dock_item_'+this.id));
            Y.one('#dock_item_panel_'+this.id).on('dockpreventhide|click', function(){this.preventhide=true;}, this);
            Y.one('#dock_item_'+this.id).on('dockhide|click', this.hide, this);
            Y.get(window).on('dockresize|resize', this.resize_panel, this);
            Y.get(document.body).on('dockhide|click', this.hide, this);
            this.fire('dockeditem:showcomplete');
            return true;
        },
        /**
         * This function hides the item and makes it inactive
         * @param {event}
         */
        hide : function(e) {
            // Ignore this call is preventhide is true
            if (this.preventhide===true) {
                this.preventhide = false;
            } else if (this.active) {
                this.fire('dockeditem:hidestart');
                this.active = false;
                Y.one('#dock_item_'+this.id+'_title').removeClass(this.cfg.css.activeitem);
                Y.one('#dock_item_'+this.id).on('showitem|mouseover', this.show, this);
                Y.get(window).detach('dockresize|resize');
                Y.get(document.body).detach('dockhide|click');
                this.panel.hide(e, this);
                this.fire('dockeditem:hidecomplete');
            }
        },
        /**
         * This function checks the size and position of the panel and moves/resizes if
         * required to keep it within the bounds of the window.
         */
        resize_panel : function() {
            this.fire('dockeditem:resizestart');
            var panelbody = Y.one(this.panel.body);
            var buffer = this.cfg.buffer;
            var screenheight = parseInt(Y.get(document.body).get('winHeight'));
            var panelheight = parseInt(panelbody.get('offsetHeight'));
            var paneltop = parseInt(this.panel.cfg.getProperty('y'));
            var titletop = parseInt(Y.one('#dock_item_'+this.id+'_title').getY());
            var scrolltop = window.pageYOffset || document.body.scrollTop || 0;

            // This makes sure that the panel is the same height as the dock title to
            // begin with
            if (paneltop > (buffer+scrolltop) && paneltop > (titletop+scrolltop)) {
                this.panel.cfg.setProperty('y', titletop+scrolltop);
            }

            // This makes sure that if the panel is big it is moved up to ensure we don't
            // have wasted space above the panel
            if ((paneltop+panelheight)>(screenheight+scrolltop) && paneltop > buffer) {
                paneltop = (screenheight-panelheight-buffer);
                if (paneltop<buffer) {
                    paneltop = buffer;
                }
                this.panel.cfg.setProperty('y', paneltop+scrolltop);
            }

            // This makes the panel constrain to the screen's height if the panel is big
            if (paneltop <= buffer && ((panelheight+paneltop*2) > screenheight || panelbody.hasClass('oversized_content'))) {
                this.panel.cfg.setProperty('height', screenheight-(buffer*2));
                panelbody.setStyle('height', (screenheight-(buffer*3))+'px');
                panelbody.addClass('oversized_content');
            }
            this.fire('dockeditem:resizecomplete');
        }
    }
};

/**
 * This class represents a generic block
 * @class genericblock
 * @constructor
 * @param {int} uid
 */
blocks.genericblock = function(uid){
    // Save the unique id as the blocks id
    if (uid && this.id==null) {
        this.id = uid;
    }
};
/** Properties */
blocks.genericblock.prototype.name =                    blocks.dock.abstract_block_class.name;
blocks.genericblock.prototype.cachedcontentnode =       blocks.dock.abstract_block_class.cachedcontentnode;
blocks.genericblock.prototype.blockspacewidth =         blocks.dock.abstract_block_class.blockspacewidth;
blocks.genericblock.prototype.skipsetposition =         blocks.dock.abstract_block_class.skipsetposition;
/** Methods **/
blocks.genericblock.prototype.init =                    blocks.dock.abstract_block_class.init;
blocks.genericblock.prototype.move_to_dock =            blocks.dock.abstract_block_class.move_to_dock;
blocks.genericblock.prototype.resize_block_space =      blocks.dock.abstract_block_class.resize_block_space;
blocks.genericblock.prototype.return_to_block =         blocks.dock.abstract_block_class.return_to_block;

/**
 * This class represents an item in the dock
 * @class item
 * @constructor
 * @param {int} uid The unique ID for the item
 * @param {Y.Node} title
 * @param {Y.Node} contents
 * @param {Y.Node} commands
 */
blocks.dock.item = function(uid, title, contents, commands){
    if (uid && this.id==null) this.id = uid;
    if (title && this.title==null) this.title = title;
    if (contents && this.contents==null) this.contents = contents;
    if (commands && this.commands==null) this.commands = commands;
    this.init_events();
}
/** Properties */
blocks.dock.item.prototype.id =                 blocks.dock.abstract_item_class.id;
blocks.dock.item.prototype.name =               blocks.dock.abstract_item_class.name;
blocks.dock.item.prototype.title =              blocks.dock.abstract_item_class.title;
blocks.dock.item.prototype.contents =           blocks.dock.abstract_item_class.contents;
blocks.dock.item.prototype.commands =           blocks.dock.abstract_item_class.commands;
blocks.dock.item.prototype.active =             blocks.dock.abstract_item_class.active;
blocks.dock.item.prototype.panel =              blocks.dock.abstract_item_class.panel;
blocks.dock.item.prototype.preventhide =        blocks.dock.abstract_item_class.preventhide;
blocks.dock.item.prototype.cfg =                blocks.dock.cfg;
/** Methods **/
blocks.dock.item.prototype.init_events =        blocks.dock.abstract_item_class.init_events;
blocks.dock.item.prototype.draw =               blocks.dock.abstract_item_class.draw;
blocks.dock.item.prototype.remove =             blocks.dock.abstract_item_class.remove;
blocks.dock.item.prototype.show =               blocks.dock.abstract_item_class.show;
blocks.dock.item.prototype.hide =               blocks.dock.abstract_item_class.hide;
blocks.dock.item.prototype.resize_panel =       blocks.dock.abstract_item_class.resize_panel;

///////////////// END OF BLOCKS CODE \\\\\\\\\\\\\\\\\\\\\\
