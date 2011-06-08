/**
 * @author Dongsheng Cai <dongsheng@moodle.com>
 */

(function() {
	var each = tinymce.each;

	tinymce.PluginManager.requireLangPack('moodlemedia');

	tinymce.create('tinymce.plugins.MoodlemediaPlugin', {
		init : function(ed, url) {
			var t = this;
			
			t.editor = ed;
			t.url = url;

			// Register commands
			ed.addCommand('mceMoodleMedia', function() {
				ed.windowManager.open({
					file : url + '/moodlemedia.htm',
					width : 430 + parseInt(ed.getLang('media.delta_width', 0)),
					height : 470 + parseInt(ed.getLang('media.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('moodlemedia', {
                    title : 'Moodle Media', 
                    image : url + '/img/icon.gif',
                    cmd : 'mceMoodleMedia'});

		},

		_parse : function(s) {
			return tinymce.util.JSON.parse('{' + s + '}');
		},

		getInfo : function() {
			return {
				longname : 'Moodle media',
				author : 'Dongsheng Cai <dongsheng@moodle.com>',
				version : "1.0"
			};
		}

	});

	// Register plugin
	tinymce.PluginManager.add('moodlemedia', tinymce.plugins.MoodlemediaPlugin);
})();
