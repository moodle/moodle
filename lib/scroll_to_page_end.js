// keep the global scope clean
(function() {

    function scroll_to_page_end() {
        window.scrollTo(0, 5000000);
    };

    // load should be a document event, but most browsers use window
    if (window.addEventListener) {
        window.addEventListener('load', scroll_to_page_end, false);
    } else if (document.addEventListener) {
        document.addEventListener('load', scroll_to_page_end, false);
    } else if (window.attachEvent) {
        window.attachEvent('onload', scroll_to_page_end);
    }

})();
