/**
 *
 *
 */
// jshint unused:false, undef:false

function push_to_clipboard(spanobjid, button) {

    var range, selection, spanobj;

    spanobj = document.getElementById(spanobjid);
    if (window.getSelection && document.createRange) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(spanobj);
        selection.removeAllRanges();
        selection.addRange(range);
    } else if (document.selection && document.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(spanobj);
        range.select();
    }

    ret = document.execCommand('copy');

    button.style.bgcolor = '#00FF00';
}