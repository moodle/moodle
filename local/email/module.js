function InsertAtCaret(myValue) {

    return $(".insertatcaretactive").each(function(i) {
        if (typeof this.value === 'undefined') {
            var sel, range, html;
            if (window.getSelection) {
                sel = window.getSelection();
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();
                    range.insertNode( document.createTextNode(myValue) );
                }
            } else if (document.selection && document.selection.createRange) {
                document.selection.createRange().text = myValue;
            }
        } else {
            if (document.selection) {
                //For browsers like Internet Explorer
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if (this.selectionStart || this.selectionStart == '0') {
                //For browsers like Firefox and Webkit based
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        }
    })
}


$(".inputholder").on("click", function() {
    $(".insertatcaretactive").removeClass("insertatcaretactive");
    var EditorInput = $(this).parent().find("#id_subject");
    $(EditorInput).addClass("insertatcaretactive");
});

$(".fitem_id_body_editor").on("click", function() {
    var EditorInput = $(this).parent().find("#id_body_editoreditable");
    $(".insertatcaretactive").removeClass("insertatcaretactive");
    $(EditorInput).addClass("insertatcaretactive");
});

$(".fitem_id_signature_editor").on("click", function() {
    var EditorInput = $(this).parent().find("#id_signature_editoreditable");
    $(".insertatcaretactive").removeClass("insertatcaretactive");
    $(EditorInput).addClass("insertatcaretactive");
});

$('.clickforword').mousedown(function(e) {
    e.preventDefault(); //to prevent the default behaviour of a tag
    InsertAtCaret("{" + this.text + "}" );
});

// Deal with edit allow button.
$('.emailclicktoedit').click(function(e) {
    e.preventDefault(); //to prevent the default behaviour of a tag
    $('#isediting').val(1);
    $('#id_emailto').prop('disabled', false);
    $('#id_emailtoother').prop('disabled', false);
    $('#id_emailfrom').prop('disabled', false);
    $('#id_emailfromother').prop('disabled', false);
    $('#id_emailcc').prop('disabled', false);
    $('#id_emailccother').prop('disabled', false);
    $('#id_emailreplyto').prop('disabled', false);
    $('#id_emailreplytoother').prop('disabled', false);
    $('#id_subject').prop('disabled', false);
    $('#id_body_editor').prop('disabled', false);
    $('#id_companylogo').prop('disabled', false);
    $('#id_signature_editor').prop('disabled', false);
    $('#id_save').prop('disabled', false);
    $('#id_edit').prop('disabled', true);
});