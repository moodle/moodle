/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add('datatype-number-parse', function(Y) {

/**
 * Parse number submodule.
 *
 * @module datatype
 * @submodule datatype-number-parse
 * @for DataType.Number
 */

var LANG = Y.Lang;

Y.mix(Y.namespace("DataType.Number"), {
    /**
     * Converts data to type Number.
     *
     * @method parse
     * @param data {String | Number | Boolean} Data to convert. The following
     * values return as null: null, undefined, NaN, "".
     * @return {Number} A number, or null.
     */
    parse: function(data) {
        var number = (data === null) ? data : +data;
        if(LANG.isNumber(number)) {
            return number;
        }
        else {
            Y.log("Could not parse data " + Y.dump(data) + " to type Number", "warn", "datatype-number");
            return null;
        }
    }
});

// Add Parsers shortcut
Y.namespace("Parsers").number = Y.DataType.Number.parse;


}, '3.2.0' );
