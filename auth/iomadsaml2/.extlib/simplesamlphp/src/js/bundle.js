import "es6-shim";
import ClipboardJS from "clipboard/dist/clipboard";
import hljs from  "highlight.js/lib/core";
import xml from "highlight.js/lib/languages/xml";
import php from "highlight.js/lib/languages/php";
import json from "highlight.js/lib/languages/json";

$(document).ready(function () {
    $('#language-selector').on('change', function () {
        $("#language-form").submit();
    });

    // side menu
    $('#menuLink').click(function (e) {
        e.preventDefault();
        $('#layout').toggleClass('active');
        $('#foot').toggleClass('active');
        $(this).toggleClass('active');
    });

    // expander boxes
    $('.expandable > .expander').on('click', function (e) {
        e.preventDefault();
        let target = $(e.currentTarget);
        target.parents('.expandable').toggleClass('expanded');
        target.blur();
    });

    // syntax highlight
    hljs.registerLanguage('xml', xml);
    hljs.registerLanguage('php', php);
    hljs.registerLanguage('json', json);
    $('.code-box-content.xml, .code-box-content.php, .code-box-content.json').each(function (i, block) {
        hljs.highlightBlock(block)
    });

    // clipboard
    let clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function (e) {
        setTimeout(function () {
            e.clearSelection();
        }, 150);
    });
});
