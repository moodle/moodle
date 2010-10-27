var MoodleEmoticonDialog = {

    insert : function(html) {
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, html);
        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(MoodleEmoticonDialog.init, MoodleEmoticonDialog);
