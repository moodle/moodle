var suggest_cache = new Array;

String.prototype.score = function (abbreviation,offset) {
    if (suggest_cache['abv'] != abbreviation) {
        suggest_cache['abv'] = abbreviation;
        var words = abbreviation.split(/\s/);
        suggest_cache['len'] = words.length;
        suggest_cache.re = new Array;

        //words.each();
        for (var i = 0; i < suggest_cache['len']; ++i) {
            suggest_cache['re'][i] = new Array();
            // /\b<x>/ doesn't work when <x> i a non-ascii - oddly enough \s does ...
            suggest_cache['re'][i]['initialword'] = new RegExp("^" + words[i], "i");
            suggest_cache['re'][i]['word'] = new RegExp("[\\s-()_]" + words[i], "i");
        }
    }

    for (var j = 0; j < suggest_cache['len']; ++j) {
        if (!(this.match(suggest_cache['re'][j]['initialword']) || this.match(suggest_cache['re'][j]['word']))) {
            return 0;
        }
    }
    return 1;
}
