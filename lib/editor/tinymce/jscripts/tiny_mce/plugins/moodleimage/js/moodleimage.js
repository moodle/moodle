tinyMCEPopup.requireLangPack();

var MoodleImageDialog = {
		init : function(ed) {
		var f = document.forms[0], nl = f.elements, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		course_id = tinyMCEPopup.getParam('moodleimage_course_id');
		tinyMCEPopup.resizeToInnerSize();
		if (n.nodeName == 'IMG') {
			nl.f_url.value = dom.getAttrib(n, 'src');
			nl.f_width.value = dom.getAttrib(n, 'width');
			nl.f_height.value = dom.getAttrib(n, 'height');
			nl.f_alt.value = dom.getAttrib(n, 'alt');
			nl.f_vert.value = this.getAttrib(n, 'vspace');
			nl.f_horiz.value = this.getAttrib(n, 'hspace');
			nl.f_border.value = this.getAttrib(n, 'border');
			selectByValue(f, 'f_align', this.getAttrib(n, 'align'));
			window.ipreview.location.replace('preview.php?id='+ course_id +'&imageurl='+ nl.f_url.value);
		}
		
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
	}, 

	changeHeight : function() {
		var f = document.forms[0], tp, t = this;

		//if (!f.constrain.checked || !t.preloadImg) {
			//t.updateStyle();
			//return;
		//}

		if (f.width.value == "" || f.height.value == "")
			return;

		tp = (parseInt(f.width.value) / parseInt(t.preloadImg.width)) * t.preloadImg.height;
		f.height.value = tp.toFixed(0);
		//t.updateStyle();
	},

	changeWidth : function() {
		var f = document.forms[0], tp, t = this;

		//if (!f.constrain.checked || !t.preloadImg) {
			//t.updateStyle();
			//return;
		//}

		if (f.f_width.value == "" || f.f_height.value == "")
			return;

		tp = (parseInt(f.f_height.value) / parseInt(t.preloadImg.height)) * t.preloadImg.width;
		f.width.value = tp.toFixed(0);
		//t.updateStyle();
	},
	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if (ed.settings.inline_styles) {
			switch (at) {
				case 'align':
					if (v = dom.getStyle(e, 'float'))
						return v;

					if (v = dom.getStyle(e, 'vertical-align'))
						return v;

					break;

				case 'hspace':
					v = dom.getStyle(e, 'margin-left')
					v2 = dom.getStyle(e, 'margin-right');
					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'vspace':
					v = dom.getStyle(e, 'margin-top')
					v2 = dom.getStyle(e, 'margin-bottom');
					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'border':
					v = 0;

					tinymce.each(['top', 'right', 'bottom', 'left'], function(sv) {
						sv = dom.getStyle(e, 'border-' + sv + '-width');

						// False or not the same as prev
						if (!sv || (sv != v && v !== 0)) {
							v = 0;
							return false;
						}

						if (sv)
							v = sv;
					});

					if (v)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;
			}
		}

		if (v = dom.getAttrib(e, at))
			return v;

		return '';
	}

	
};


tinyMCEPopup.onInit.add(MoodleImageDialog.init, MoodleImageDialog);
