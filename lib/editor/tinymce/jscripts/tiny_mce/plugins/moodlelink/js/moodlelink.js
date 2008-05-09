tinyMCEPopup.requireLangPack();

var MoodleLinkDialog = {
		init : function(ed) {
		var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		tinyMCEPopup.resizeToInnerSize();
				
	},

	insert : function(file, title) {
		var ed = tinyMCEPopup.editor, t = this, f = document.forms[0];

		if (f.f_url.value === '') {
			ed.dom.remove(ed.selection.getNode());
			ed.execCommand('mceRepaint');
			tinyMCEPopup.close();
			return;
		}

		if (tinyMCEPopup.getParam("accessibility_warnings", 1)) {
			if (!f.f_alt.value) {
				tinyMCEPopup.editor.windowManager.confirm(tinyMCEPopup.getLang('moodleimage_dlg.missing_alt'), function(s) {
					if (s)
						t.insertAndClose();
				});

				return;
			}
		}

		t.insertAndClose();
	},

	insertAndClose : function() {
		var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, v, args = {}, el;

		// Fixes crash in Safari
		if (tinymce.isWebKit)
			ed.getWin().focus();

		tinymce.extend(args, {
			src : nl.f_url.value,
			width : nl.f_width.value,
			height : nl.f_height.value,
			alt : nl.f_alt.value,
			vspace : nl.f_vert.value,
		    hspace : nl.f_horiz.value,
			border : nl.f_border.value,
			align : getSelectValue(f, 'f_align')
		});

		el = ed.selection.getNode();

		if (el && el.nodeName == 'IMG') {
			ed.dom.setAttribs(el, args);
		} else {
			ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" src="javascript:;" />');
			ed.dom.setAttribs('__mce_tmp', args);
			ed.dom.setAttrib('__mce_tmp', 'id', '');
		}

		tinyMCEPopup.close();
	} 

	

	
};


tinyMCEPopup.onInit.add(MoodleLinkDialog.init, MoodleLinkDialog);
