/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datatype-xml-format",function(b){var a=b.Lang;b.mix(b.namespace("DataType.XML"),{format:function(c){try{if(!a.isUndefined(XMLSerializer)){return(new XMLSerializer()).serializeToString(c);}}catch(d){if(c&&c.xml){return c.xml;}else{return(a.isValue(c)&&c.toString)?c.toString():"";}}}});},"3.4.0");