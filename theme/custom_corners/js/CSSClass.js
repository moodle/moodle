/**
 * CSSClass.js: utilities for manipulating the CSS class of an HTML element.
 * 
 * This module defines a single global symbol named CSSClass.  This object
 * contains utility functions for working with the class attribute (className
 * property) of HTML elements.  All functions take two arguments: the element
 * e being tested or manipulated and the CSS class c that is to be tested,
 * added, or removed.  If element e is a string, it is taken as an element
 * id and passed to document.getElementById().
 */
var CSSClass = {};  // Create our namespace object

// Return true if element e is a member of the class c; false otherwise
CSSClass.is = function(e, c) {
    if (typeof e == "string") e = document.getElementById(e); // element id

    // Before doing a regexp search, optimize for a couple of common cases.
    var classes = e.className;
    if (!classes) return false;    // Not a member of any classes
    if (classes == c) return true; // Member of just this one class

    // Otherwise, use a regular expression to search for c as a word by itself
    // \b in a regular expression requires a match at a word boundary.
    return e.className.search("\\b" + c + "\\b") != -1;
};

// Add class c to the className of element e if it is not already there.
CSSClass.add = function(e, c) {
    if (typeof e == "string") e = document.getElementById(e); // element id
    if (CSSClass.is(e, c)) return; // If already a member, do nothing
    if (e.className) c = " " + c;  // Whitespace separator, if needed
    e.className += c;              // Append the new class to the end
};

// Remove all occurrences (if any) of class c from the className of element e
CSSClass.remove = function(e, c) {
    if (typeof e == "string") e = document.getElementById(e); // element id
    // Search the className for all occurrences of c and replace with "".
    // \s* matches any number of whitespace characters.
    // "g" makes the regular expression match any number of occurrences
    e.className = e.className.replace(new RegExp("\\b"+ c+"\\b\\s*", "g"), "");
};