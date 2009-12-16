/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add('anim-node-plugin', function(Y) {

/**
 *  Binds an Anim instance to a Node instance
 * @module anim
 * @class Plugin.NodeFX
 * @extends Base
 * @submodule anim-node-plugin
 */

var NodeFX = function(config) {
    config = (config) ? Y.merge(config) : {};
    config.node = config.host;
    NodeFX.superclass.constructor.apply(this, arguments);
};

NodeFX.NAME = "nodefx";
NodeFX.NS = "fx";

Y.extend(NodeFX, Y.Anim);

Y.namespace('Plugin');
Y.Plugin.NodeFX = NodeFX;


}, '3.0.0' ,{requires:['node-pluginhost', 'anim-base']});
