/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add('json-parse', function (Y, NAME) {

var _JSON = Y.config.global.JSON;

Y.namespace('JSON').parse = function (obj, reviver, space) {
    return _JSON.parse((typeof obj === 'string' ? obj : obj + ''), reviver, space);
};


}, '3.9.0', {"requires": ["yui-base"]});
