/**
 * $Id$
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.emoticonsPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceemoticon', function() {
				ed.windowManager.open({
					file : url + '/emoticons.php',
					width : 300 + parseInt(ed.getLang('emoticons.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('emoticons.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('emoticons', {title : 'emoticons.emoticons_desc', cmd : 'mceemoticon', image : url + '/img/smiley.gif'});
		},

		getInfo : function() {
			return {
				longname : 'emoticons',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/emoticons',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('emoticons', tinymce.plugins.emoticonsPlugin);
})();