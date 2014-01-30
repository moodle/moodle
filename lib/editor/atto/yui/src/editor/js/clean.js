/**
 * Class for cleaning ugly HTML.
 * Rewritten JS from jquery-clean plugin.
 *
 * @module editor_atto
 * @chainable
 */
function cleanHTML() {
    var cleaned = this.getHTML();

    // What are we doing ?
    // We are cleaning random HTML from all over the shop into a set of useful html suitable for content.
    // We are allowing styles etc, but not e.g. font tags, class="MsoNormal" etc.

    var rules = [
        // Source: "http://stackoverflow.com/questions/2875027/clean-microsoft-word-pasted-text-using-javascript"
        // Source: "http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work"

        // Remove all HTML comments.
        {regex: /<!--[\s\S]*?-->/gi, replace: ""},
        // Source: "http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php"
        // Remove <?xml>, <\?xml>.
        {regex: /<\\?\?xml[^>]*>/gi, replace: ""},
        // Remove <o:blah>, <\o:blah>.
        {regex: /<\/?\w+:[^>]*>/gi, replace: ""}, // e.g. <o:p...
        // Remove MSO-blah, MSO:blah (e.g. in style attributes)
        {regex: /\s*MSO[-:][^;"']*;?/gi, replace: ""},
        // Remove empty spans
        {regex: /<span[^>]*>(&nbsp;|\s)*<\/span>/gi, replace: ""},
        // Remove class="Msoblah"
        {regex: /class="Mso[^"]*"/gi, replace: ""},

        // Source: "http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html"
        // Remove forbidden tags for content, title, meta, style, st0-9, head, font, html, body.
        {regex: /<(\/?title|\/?meta|\/?style|\/?st\d|\/?head|\/?font|\/?html|\/?body|!\[)[^>]*?>/gi, replace: ""},

        // Source: "http://www.tim-jarrett.com/labs_javascript_scrub_word.php"
        // Replace extended chars with simple text.
        {regex: new RegExp(String.fromCharCode(8220), 'gi'), replace: '"'},
        {regex: new RegExp(String.fromCharCode(8216), 'gi'), replace: "'"},
        {regex: new RegExp(String.fromCharCode(8217), 'gi'), replace: "'"},
        {regex: new RegExp(String.fromCharCode(8211), 'gi'), replace: '-'},
        {regex: new RegExp(String.fromCharCode(8212), 'gi'), replace: '--'},
        {regex: new RegExp(String.fromCharCode(189), 'gi'), replace: '1/2'},
        {regex: new RegExp(String.fromCharCode(188), 'gi'), replace: '1/4'},
        {regex: new RegExp(String.fromCharCode(190), 'gi'), replace: '3/4'},
        {regex: new RegExp(String.fromCharCode(169), 'gi'), replace: '(c)'},
        {regex: new RegExp(String.fromCharCode(174), 'gi'), replace: '(r)'},
        {regex: new RegExp(String.fromCharCode(8230), 'gi'), replace: '...'}
    ];

    var i = 0, rule;

    for (i = 0; i < rules.length; i++) {
        rule = rules[i];
        cleaned = cleaned.replace(rule.regex, rule.replace);
    }

    this.setHTML(cleaned);
    return this;
}

Y.Node.addMethod("cleanHTML", cleanHTML);
Y.NodeList.importMethod(Y.Node.prototype, "cleanHTML");
