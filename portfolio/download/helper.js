function submit_download_form() {
    f = YAHOO.util.Dom.get("redirectform");
    YAHOO.util.Dom.addClass(f.parentNode, "hide");
    f.submit();
}
