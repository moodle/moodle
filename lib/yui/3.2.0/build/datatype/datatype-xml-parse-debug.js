/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
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



}, '3.2.0' );
