tinyMCEPopup.requireLangPack();

var DragMathDialog = {
    init : function(ed) {
    },

    insert : function(file, title) {
        var ed = tinyMCEPopup.editor;
        var tex = document.dragmath.getMathExpression();

        // convert < and > to entities
        tex = tex.replace('<', '&lt;');
        tex = tex.replace('>', '&gt;');

        if (tinymce.isIE) {tinyMCEPopup.restoreSelection();}

        ed.execCommand('mceInsertContent', false, tex);

        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(DragMathDialog.init, DragMathDialog);
