var MoodleEmoticonDialog = {

    init : function() {
        // register event handlers for the table rows
        tinymce.each(tinymce.DOM.select('tr.emoticoninfo', document), function(row) {

            tinymce.dom.Event.add(row, 'mouseover', function(e) {
                this.style.backgroundColor = 'white';
            }, row);

            tinymce.dom.Event.add(row, 'mouseout', function(e) {
                this.style.backgroundColor = 'transparent';
            }, row);

            tinymce.dom.Event.add(row, 'click', function(e) {
                var matches = /^emoticoninfo emoticoninfo-index-([0-9]+)$/.exec(this.className);
                if (matches.length != 2) {
                    // continue with the next row
                    return true;
                }
                var index = matches[1];
                MoodleEmoticonDialog.insert(index);
            }, row);

        });
    },

    insert : function(index) {
        emoticons = tinyMCEPopup.editor._emoticons;
        i = 0;
        for (var emoticon in emoticons) {
            if (i == index) {
                if (tinymce.isIE) {
                    tinyMCEPopup.restoreSelection();
                }
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, emoticons[emoticon]);
                tinyMCEPopup.close();
                return;
            }
            i++;
        }
    },

    highlight : function(row) {
        row.style.backgroundColor="white";
    },

    unhighlight : function(row) {
        row.style.backgroundColor="transparent";
    }

};

tinyMCEPopup.onInit.add(MoodleEmoticonDialog.init, MoodleEmoticonDialog);
