function submit_download_form() {
YUI().use('yui2-dom', function(Y) {
    f = Y.YUI2.util.Dom.get("redirectform");
    Y.YUI2.util.Dom.addClass(f.parentNode, "hide");
    f.submit();
});
}
