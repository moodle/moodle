/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
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
            return null;
        }
    }
});

// Add Parsers shortcut
Y.namespace("Parsers").number = Y.DataType.Number.parse;


}, '3.4.1' );
