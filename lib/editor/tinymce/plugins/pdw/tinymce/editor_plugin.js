/**
 * PDW Toggle Toolbars v1.2
 * Url: http://www.neele.name
 * Author: Guido Neele
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * Based on TinyMCE Wordpress plugin (Kitchen Sink)
 *
 * Changes V1.1.1 --> V1.2
 *
 * heeae made some modifications and posted his work on Sourceforge. I thought the cookie support
 * was really handy so I updated the script.
 * http://sourceforge.net/tracker/?func=detail&atid=738747&aid=2904683&group_id=103281
 *
 * 1. Added cookie support.
 * 2. Some optimization
 * 3. Bug fix of fire ifr.clientHeight in FF 3 ( to DOM.getStyles)
 *
 * Thanks heeae!
 *
 * Changes V1.1 --> V1.1.1
 *
 * Bugfix for Firefox 3.6. Caused error while loading script.
 *
 * Added lines 72 - 76:
 *
 * obj = ed.controlManager.get(tbIds[j]);
 * if(typeof obj =="undefined") {
 * 		continue;
 * }
 * id = obj.id;
 *
 * instead of:
 *
 * try {
 *		id = ed.controlManager.get(tbIds[j]).id;
 * }
 * catch(e) {
 * //if(typeof id == "undefined") continue;
 * 		continue;
 * }
 *
 * Thanks Anton for fixing this bug
 *
 */

(function() {
	var DOM = tinymce.DOM;
	tinymce.PluginManager.requireLangPack('pdw');

	tinymce.create('tinymce.plugins.pdw', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			var t = this, tbIds = new Array(), toolbars = new Array(), i;

			// Split toolbars
			toolbars = (ed.settings.pdw_toggle_toolbars).split(',');

			for(i = 0; i < toolbars.length; i++){
				tbIds[i] = ed.getParam('', 'toolbar' + (toolbars[i]).replace(' ',''));
			}

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mcePDWToggleToolbars', function() {

				var cm = ed.controlManager, id, j, Cookie = tinymce.util.Cookie, Toggle_PDW, Toggle = Cookie.getHash("TinyMCE_toggle") || new Object();
				for(j = 0; j < tbIds.length; j++){

					obj = ed.controlManager.get(tbIds[j]);
                    if(typeof obj =="undefined") {
                        continue;
                    }
                    id = obj.id;

					if (DOM.isHidden(id)) {
						Toggle_PDW = 0;
                        var e = document.getElementById(id);
                        if (e) {
                            e.style.display = 'table';
                            t._resizeIframe(ed, tbIds[j], -26);
                        }

					} else {
						Toggle_PDW = 1;
                        var e = document.getElementById(id);
                        if (e) {
                            e.style.display = 'none';
                        }
						t._resizeIframe(ed, tbIds[j], 26);
					}
				}
				cm.setActive('pdw_toggle', Toggle_PDW);
				ed.settings.pdw_toggle_on = Toggle_PDW;
				Toggle[ed.id] = Toggle_PDW;
				Cookie.setHash("TinyMCE_toggle", Toggle);
			});

			// Register pdw_toggle button
			ed.addButton('pdw_toggle', {
				title : ed.getLang('pdw.desc', 0),
				cmd : 'mcePDWToggleToolbars',
				image : url + '/img/toolbars.png'
			});

			ed.onPostRender.add(function(){
				var toggle = tinymce.util.Cookie.getHash("TinyMCE_toggle") || new Object();
				var run = false;

				// Check if value is stored in cookie
				if(toggle[ed.id] == null){
					// No cookie so check if the setting pdw_toggle_on is set to 1 then hide toolbars and set button active
					run = ed.settings.pdw_toggle_on == 1 ? true : false;
				} else if(toggle[ed.id] == 1){
					run = true;
				}

				if (run) {

					var cm = ed.controlManager, tdId, id;

					for(i = 0; i < toolbars.length; i++){
						tbId = ed.getParam('', 'toolbar' + (toolbars[i]).replace(' ',''));
						id = ed.controlManager.get(tbId).id;
						cm.setActive('pdw_toggle', 1);
						DOM.hide(id);
						t._resizeIframe(ed, tbId, 26);
					}
				}
			});
		},

		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, tb_id, dy) {
		    var ifr = ed.getContentAreaContainer().firstChild;
            var parcon = ed.getContainer(); //Parent container
            var tablecon = ed.getContainer().childNodes[1]; // Table container
            var textarea = DOM.get(ed.id);
            var rows = textarea ? textarea.getAttribute('rows') : 3;

            // This set of changes addresses MDL-42481.
            // Moodle collapses form sections by setting display to none on the fcontainer element.
            // In order to calculate the offsetWidth, the iframe must be visible within the DOM, otherwise it's offsetWidth is
            // calculate as 0px.
            // We attempt to find any collapsed element, uncollapse them, then calculate the width and height, and finally
            // collapse them again.
            var collapsedContainer = DOM.getParent(ifr, 'fieldset.collapsed');
            if (collapsedContainer) {
                DOM.removeClass(collapsedContainer, 'collapsed');
            }
            // For very small text areas - allow the editable region to be smaller than the size of the toolbars.
            if (rows >= 3) {
                DOM.setStyle(ifr, 'height',DOM.getSize(ifr).h + dy); // Resize iframe
                ed.theme.deltaHeight += dy; // For resize cookie
            }

            // Set all the containers to the same width
            DOM.setStyle(textarea, 'width','100%');
            DOM.setStyle(parcon, 'width','100%');
            DOM.setStyle(tablecon, 'width','100%');
            DOM.setStyle(ifr, 'width','100%');


            if (collapsedContainer) {
                // We have a collapsedContainer, so collapse it again.
                DOM.addClass(collapsedContainer, 'collapsed');
            }

		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'PDW Toggle Toolbars',
				author : 'Guido Neele',
				authorurl : 'http://www.neele.name/',
				infourl : 'http://www.neele.name/pdw_toggle_toolbars',
				version : "1.2"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('pdw', tinymce.plugins.pdw);
})();

