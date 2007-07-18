/* Python 2.3 syntax contributed by Gheorghe Milas */
dp.sh.Brushes.Python = function()
{
    var keywords =  'and assert break class continue def del elif else ' +
                    'except exec finally for from global if import in is ' +
                    'lambda not or pass print raise return try yield while';

    var special =  'None True False self cls class_'

    this.regexList = [
        { regex: dp.sh.RegexLib.SingleLinePerlComments, css: 'comment' },
        { regex: new RegExp("^\\s*@\\w+", 'gm'), css: 'decorator' },
        { regex: new RegExp("(['\"]{3})([^\\1])*?\\1", 'gm'), css: 'comment' },
        { regex: new RegExp('"(?!")(?:\\.|\\\\\\"|[^\\""\\n\\r])*"', 'gm'), css: 'string' },
        { regex: new RegExp("'(?!')*(?:\\.|(\\\\\\')|[^\\''\\n\\r])*'", 'gm'), css: 'string' },
        { regex: new RegExp("\\b\\d+\\.?\\w*", 'g'), css: 'number' },
        { regex: new RegExp(this.GetKeywords(keywords), 'gm'), css: 'keyword' },
        { regex: new RegExp(this.GetKeywords(special), 'gm'), css: 'special' },
        ];

    this.CssClass = 'dp-py';
	this.Style =	'.dp-py .builtins { color: #ff1493; }' +
					'.dp-py .magicmethods { color: #808080; }' +
					'.dp-py .exceptions { color: brown; }' +
					'.dp-py .types { color: brown; font-style: italic; }' +
					'.dp-py .commonlibs { color: #8A2BE2; font-style: italic; }';
}

dp.sh.Brushes.Python.prototype  = new dp.sh.Highlighter();
dp.sh.Brushes.Python.Aliases    = ['py', 'python'];
