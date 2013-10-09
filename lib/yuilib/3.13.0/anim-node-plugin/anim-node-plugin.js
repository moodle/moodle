/*
YUI 3.13.0 (build 508226d)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add('anim-node-plugin', function (Y, NAME) {

/**
 *  Binds an Anim instance to a Node instance
 * @module anim
 * @class Plugin.NodeFX
 * @extends Anim
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


}, '3.13.0', {"requires": ["node-pluginhost", "anim-base"]});
