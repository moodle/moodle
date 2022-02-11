// Create a new plugin which registers a custom code highlighter
CKEDITOR.plugins.add('customCodeHighlighter', {
  afterInit: function (editor) {
    var highlighter = new CKEDITOR.plugins.codesnippet.highlighter({
      languages: {
        apache: 'Apache',
        bash: 'Bash',
        coffeescript: 'CoffeeScript',
        cpp: 'C++',
        cs: 'C#',
        css: 'CSS',
        diff: 'Diff',
        html: 'HTML',
        http: 'HTTP',
        ini: 'INI',
        java: 'Java',
        javascript: 'JavaScript',
        json: 'JSON',
        makefile: 'Makefile',
        markdown: 'Markdown',
        nginx: 'Nginx',
        objectivec: 'Objective-C',
        perl: 'Perl',
        php: 'PHP',
        python: 'Python',
        ruby: 'Ruby',
        sql: 'SQL',
        vbscript: 'VBScript',
        xhtml: 'XHTML',
        xml: 'XML'
      },
      init: function (ready) {
        // Here we should load any required resources
        ready();
      },
      highlighter: function (code, language, callback) {
        // Here we are highlighting the code and returning it.
        /**
         * Note: Since we're not adding any highlighting we have to
         * encode the html so that the html is not being run.
         */
        callback(CKEDITOR.tools.htmlEncode(code));
      }
    });
    editor.plugins.codesnippet.setHighlighter(highlighter);
  }
});
