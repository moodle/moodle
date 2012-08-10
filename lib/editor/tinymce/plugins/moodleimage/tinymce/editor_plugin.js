/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.MoodleImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceMoodleImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/image.htm',
					width : 480 + parseInt(ed.getLang('advimage.delta_width', 0)),
					height : 385 + parseInt(ed.getLang('advimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('image', {
				title : 'advimage.image_desc',
				cmd : 'mceMoodleImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'Moodle image',
				author : 'Moodle.com based in AddImage by Moxiecode Systems AB',
				authorurl : 'http://moodle.org',
				infourl : 'https://github.com/moodle/custom-tinymce',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('moodleimage', tinymce.plugins.MoodleImagePlugin);
})();