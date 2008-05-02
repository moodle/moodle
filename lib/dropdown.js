/****
Author: Jerome Mouneyrac
Bug Reference: http://tracker.moodle.org/browse/MDL-14439
IE and Opera fire the onchange when ever you move into a dropdwown list with the keyboard.
These functions fix this problem.
****/

/*
global variables

Note:
if I didn't use global variables, we would need to pass them as parameter:  
=> in initSelect(): 
   I would write "theSelect.onchange = selectChanged(...);"
   This code causes a javascript error on IE. (not firefox)
so I had to write theSelect.onchange = selectChanged; It's why I use global variables .
Because I use global variables, I didn't put this code in javascript-static.js.
This file is loaded in javascript.php.
*/ 
var select_formid;
var select_targetwindow;

//we redefine all user actions on the dropdown list
//onfocus, onchange, onkeydown, and onclick
function initSelect(formId,targetWindow)
{
    //initialise global variables
    select_formid=formId;
    select_targetwindow=targetWindow;

    var theSelect = document.getElementById(select_formid+"_jump");

    theSelect.changed = false;

    selectFocussed();

    theSelect.onchange = selectChanged;
    theSelect.onkeydown = selectKeyed;
    theSelect.onclick = selectClicked;
    
    return true;
}

function selectChanged(theElement)
{
    var theSelect;
    
    if (theElement && theElement.value)
    {
        theSelect = theElement;
    }
    else
    {
        theSelect = this;
    }
    
    if (!theSelect.changed)
    {
        return false;
    }

    //here is the onchange redirection
    select_targetwindow.location=document.getElementById(select_formid).jump.options[document.getElementById(select_formid).jump.selectedIndex].value;                                
    
    return true;
}

function selectClicked()
{
    this.changed = true;
}

function selectFocussed()
{
    this.initValue = this.value;
    
    return true;
}

//we keep Firefox behaviors: onchange is fired when we press "Enter", "Esc", or "Tab"" keys.
//note that is probably not working on Mac (keyCode could be different)
function selectKeyed(e)
{
    var theEvent;
    var keyCodeTab = "9";
    var keyCodeEnter = "13";
    var keyCodeEsc = "27";
    
    if (e)
    {
        theEvent = e;
    }
    else
    {
        theEvent = event;
    }

    if ((theEvent.keyCode == keyCodeEnter || theEvent.keyCode == keyCodeTab) && this.value != this.initValue)
    {
        this.changed = true;
        selectChanged(this);
    }
    else if (theEvent.keyCode == keyCodeEsc)
    {
        this.value = this.initValue;
    }
    else
    {
        this.changed = false;
    }
    
    return true;
}