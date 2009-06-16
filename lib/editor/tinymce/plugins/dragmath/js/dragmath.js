tinyMCEPopup.requireLangPack();

var DragMathDialog = {
		init : function(ed) {
		
		},
		
	

	insert : function(file, title) {
		
	    var ed = tinyMCEPopup.editor;
		var mathExpression = document.dragmath.getMathExpression();
	    //
	    // TBD any massaging needed here?
	    //
	    var text = mathExpression;
	    // convert < and > to entities
	    text = text.replace('<', '&lt;');
	    text = text.replace('>', '&gt;');
	    //
	    // Escape the expression
	    //
	    text = '$$' + text + '$$';
	    ed.execCommand('mceInsertContent', false, text);

		tinyMCEPopup.close();
	}

	
	
};

tinyMCEPopup.onInit.add(DragMathDialog.init, DragMathDialog);
