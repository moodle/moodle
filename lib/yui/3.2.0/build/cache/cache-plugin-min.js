/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("cache-plugin",function(B){function A(E){var D=E&&E.cache?E.cache:B.Cache,F=B.Base.create("dataSourceCache",D,[B.Plugin.Base]),C=new F(E);F.NS="tmpClass";return C;}B.mix(A,{NS:"cache",NAME:"cachePlugin"});B.namespace("Plugin").Cache=A;},"3.2.0",{requires:["cache-base"]});