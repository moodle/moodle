/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add('datatype-xml-parse', function(Y) {

/**
 * Parse XML submodule.
 *
 * @module datatype
 * @submodule datatype-xml-parse
 * @for DataType.XML
 */

var LANG = Y.Lang;

Y.mix(Y.namespace("DataType.XML"), {
    /**
     * Converts data to type XMLDocument.
     *
     * @method parse
     * @param data {String} Data to convert.
     * @return {XMLDoc} XML Document.
     */
    parse: function(data) {
        var xmlDoc = null;
        if(LANG.isString(data)) {
            try {
                if(!LANG.isUndefined(DOMParser)) {
                    xmlDoc = new DOMParser().parseFromString(data, "text/xml");
                }
            }
            catch(e) {
                try {
                    if(!LANG.isUndefined(ActiveXObject)) {
                            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                            xmlDoc.async = false;
                            xmlDoc.loadXML(data);
                    }
                }
                catch(ee) {
                    Y.log(ee.message + " (Could not parse data " + Y.dump(data) + " to type XML Document)", "warn", "datatype-xml");
                }
            }
        }
        
        if( (LANG.isNull(xmlDoc)) || (LANG.isNull(xmlDoc.documentElement)) || (xmlDoc.documentElement.nodeName === "parsererror") ) {
            Y.log("Could not parse data " + Y.dump(data) + " to type XML Document", "warn", "datatype-xml");
        }
        
        return xmlDoc;
    }
});

// Add Parsers shortcut
Y.namespace("Parsers").xml = Y.DataType.XML.parse;




}, '3.0.0' );

YUI.add('datatype-xml-format', function(Y) {

/**
 * Format XML submodule.
 *
 * @module datatype
 * @submodule datatype-xml-format
 */

/**
 * XML submodule.
 *
 * @module datatype
 * @submodule datatype-xml
 */

/**
 * DataType.XML provides a set of utility functions to operate against XML documents.
 *
 * @class DataType.XML
 * @static
 */
var LANG = Y.Lang;

Y.mix(Y.namespace("DataType.XML"), {
    /**
     * Converts data to type XMLDocument.
     *
     * @method format
     * @param data {XMLDoc} Data to convert.
     * @return {String} String.
     */
    format: function(data) {
        try {
            if(!LANG.isUndefined(XMLSerializer)) {
                return (new XMLSerializer()).serializeToString(data);
            }
        }
        catch(e) {
            if(data && data.xml) {
                return data.xml;
            }
            else {
                Y.log("Could not format data " + Y.dump(data) + " from type XML", "warn", "datatype-xml");
                return (LANG.isValue(data) && data.toString) ? data.toString() : "";
            }
        }
    }
});




}, '3.0.0' );



YUI.add('datatype-xml', function(Y){}, '3.0.0' ,{use:['datatype-xml-parse', 'datatype-xml-format']});

