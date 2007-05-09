/**
 * This file contains various utility functions, primarily to get and set information on form.html
 * and to take information from XML documents and either return information from them or modifiy the 
 * form appropriately. 
 */

/*
 * Disable the button with the specified id
 */
function disableButton(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
    } else {
        var node = document.getElementById(id);
        node.disabled = true;
    }
}

/**
 * Enable the button with the specified id
 */
function enableButton(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
    } else {
        var node = document.getElementById(id);
        node.disabled = false;
    }
}

/**
 * Show the form with the specified id
 */
function showElement(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
    } else {
        document.getElementById(id).style.visibility = "visible";
    }
}

/** 
 * Hide the form with the specified id
 */
function hideElement(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
    } else {
        var node = document.getElementById(id);
        node.style.visibility = "hidden";
    }
}


/**
 * Hides all the extra forms in form.html
 */
function hideAllForms() {
    hideElement("addmembersform");
    hideElement("addgroupstogroupingform");
    hideElement("creategroupingform");
    hideElement("createautomaticgroupingform");
    hideElement("creategroupform");
    hideElement("editgroupingsettingsform");
    hideElement("editgroupingpermissionsform");
    hideElement("editgroupsettingsform");
    hideElement("groupeditform");
}

function onCancel() {
    hideAllForms();
    showElement("groupeditform");
    return false;
}


function addEvent(id, eventtype, fn){ 
    if (!document.getElementById(id)) {
        alert('No ' + id + ' element');
        return false;
    } else {
        obj = document.getElementById(id); 
    }

    if (obj.addEventListener) {
        obj.addEventListener(eventtype, fn, false );
    } else if (obj.attachEvent) {
        obj["e"+ eventtype +fn] = fn;
        obj[eventtype+fn] = function() { obj["e"+ eventtype +fn]( window.event ); }
        obj.attachEvent( "on"+ eventtype , obj[eventtype+fn] );
    } else {
        obj["on"+type] = obj["e"+ eventtype +fn];
    } 
}

/**
 * Gets the value of the first option in a select
 */
function getFirstOption(id) {
    if (document.getElementById(id)) {
        var node = document.getElementById(id);
        if (node.hasChildNodes()) {
            var children 
                firstoption = node.firstChild;
            if (firstoption.value) {
                value = firstoption.value;
            } else {
                value = null;
            }
        } else {
            value = null;
        }
    } else {
        value = null;
    }
    return value;
}

/* 
 *Turn the values from a multiple select to a comma-separated list
*/
function getMultipleSelect(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
    } else {
        node = document.getElementById(id);
    }
    var selected = ""

    for (var i = 0; i < node.options.length; i++) {
        if (node.options[i].selected) { 
            selected = selected + node.options[ i ].value + ",";
        }
    }
    // Remove the last comma - there must be a nicer way of doing this!
    // Maybe easier with regular expressions?
    var length = selected.length;
    if (selected.charAt(length - 1) == ',') {
        selected = selected.substring(0, length -1);
    }

    return selected;
}

/*
 * Creates an option in a select element with the specified id with the given name and value.
*/
function createOption(id, value, name) {
    var node = document.getElementById(id);
    var option = document.createElement("option");
    option.setAttribute("value", value);
    node.appendChild(option);
    var namenode = document.createTextNode(name);
    option.appendChild(namenode);
}

/*
 * Removes all the options from a select with a given id
*/
function removeOptions(id) {
    var node = document.getElementById(id);

    while (node.hasChildNodes())
    {
        node.removeChild(node.firstChild);
    }
}

/*
 * Takes an XML doc of the form <option><name></name><value></value><name></name><value></value></option>
 * And adds an option to the selected with the specified id
 * @param id The id of the select
 * @param xmlDoc The XML document
 * @return The number of options added
 */
function addOptionsFromXML(id, xmlDoc) {
    // Clear any options that are already there. 
    removeOptions(id);

    var optionelements = xmlDoc.getElementsByTagName('option');
    var nameelements = xmlDoc.getElementsByTagName('name');
    var valueelements = xmlDoc.getElementsByTagName('value');

    if (nameelements != null) {
        for (var i = 0; i < nameelements.length; i++) {
            var name = nameelements[i].firstChild.nodeValue;
            var value = valueelements[i].firstChild.nodeValue;
            createOption(id, value, name);
        }
        noofoptions = nameelements.length;
    } else {
        noofoptions = 0;
    }
    return noofoptions;
}

/*
 * Gets an error from an XML doc contain a tag of the form <error></error>
 * If it contains more than one such tag, it only return the value from the first one. 
 */
function getErrorFromXML(xmlDoc) {
    alert(xmlDoc.getElementsByTagName('error'));
    if (!xmlDoc.getElementsByTagName('error')) {
        value = null;
    } else {
        var errorelement = xmlDoc.getElementsByTagName('error')[0];
        var value = errorelement.firstChild.nodeValue;
    }
    return value;
}


function addChildrenFromXML(parentnode, xmlparentnode) {
    xmlChildNodes = xmlparentnode.childNodes;
    length = xmlChildNodes.length;
    for (i = 0; i < length; i++) {
        child = parentnode.appendChild(parentnode, xmlChildNodes[i]);
        addChildrenFromXML(child, xmlChildNodes[i])
    }
}

function getTextInputValue(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
            value = null;
    } else {
        textinput = document.getElementById(id);
        value = textinput.value;
    }
    return value;
}

function setTextInputValue(id, value) {
    if (!document.getElementById(id)) {
        showNoElementError(id); 
        value = null;
    } else {
        textinput = document.getElementById(id);
        textinput.value = value;
    }
}

function getCheckBoxValue(id) {
    if (!document.getElementById(id)) {
        showNoElementError(id); 
        value= null;
    } else {
        checkbox = document.getElementById(id);
        value = checkbox.checked;
    }
    return  boolToInt(value);
}

function boolStringToBool(boolstring) {
    if (boolstring == 'true') {
        return true;
    } else {
        return false;
    }
}

function boolToInt(boolean) {
    if (boolean) {
        return '1';
    } else if (boolean == false) {
        return '0';
    } else {
        return boolean;
    }
}

function setCheckBoxValue(id, checked) {
    if (!document.getElementById(id)) {
        showNoElementError(id); 
    } else {
        checkbox = document.getElementById(id);
        checkbox.checked = checked;
    }
}

function replaceText(id, text) {
    if (!document.getElementById(id)) {
        showNoElementError(id) 
            value = null;
    } else {
        element = document.getElementById(id);
        if (element.childNodes) {
            for (var i = 0; i < element.childNodes.length; i++) {
                var childNode = element.childNodes[i];
                element.removeChild(childNode);
            }
        }
        var textnode = document.createTextNode(text);
        element.appendChild(textnode);
    }
}


function getRadioValue(radioelement) {
    value = "";
    if (!radioelement) {
        value = "";
    }


    for(var i = 0; i < radioelement.length; i++) {
        if(radioelement[i].checked) {
            value =  radioelement[i].value;
        }
    }
    return value;
}

/*
 * Gets the groupid from an XML doc contain a tag of the form <groupid></groupid>
 * If it contains more than one such tag, it only return the value from the first one. 
 */
function getFromXML(xmlDoc, id) {
    if (!xmlDoc.getElementsByTagName(id)) {
        var value = null;
    } else if (xmlDoc.getElementsByTagName(id).length == 0) {
        var value = null;
    } else {
        var element = xmlDoc.getElementsByTagName(id)[0];
        if (!element.firstChild) {
            var value = '';
        } else {
            var value = element.firstChild.nodeValue;
        }
    }

    return value;
}

function showNoElementError(id) {
    alert('Error: No ' + id +' element');
}

function isPositiveInt(str) {
    isPosInt = true;

    var i = parseInt (str);

    if (isNaN (i)) {
        isPosInt = false;
    } 

    if (i < 0) {
        isPosInt = false;
        // Check not characters at the end of the number
    } 

    if (i.toString() != str) {
        isPosInt = false;
    }
    return isPosInt ;
}

