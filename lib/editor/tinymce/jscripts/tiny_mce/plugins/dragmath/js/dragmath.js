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
	    //
	    // Escape the expression
	    //
	    var text = '$$' + text + '$$';
	    ed.execCommand('mceInsertContent', false, text);

		tinyMCEPopup.close();
	}

	
	
};

tinyMCEPopup.onInit.add(DragMathDialog.init, DragMathDialog);
