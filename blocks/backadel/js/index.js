(function() {
    var get_search_terms, strings, _;
    strings = {
        name_missing: 'Please select a name for this query',
        term_missing: 'Please select at least one search term for this constraint',
        search_missing: 'Please select a saved search'
    };
    _ = function(s) {
        return strings[s] + '<br />';
    };
    get_search_terms = function(n) {
        return $("div[class^='c_" + n + "_search_term_']");
    };
    this.count_constraints = function() {
        return $('div.constraint').length;
    };
    this.remove_constraint = function() {
        if (count_constraints() - 1) {
            return $("div#c" + (count_constraints() - 1) + "_constraint").remove();
        }
    };
    this.remove_search_term = function(cons, term) {
        return $("div[id$='c" + cons + "_search_term_" + term + "']").remove();
    };
    this.add_constraint = function() {
        var n;
        n = count_constraints();
        return $('div#group_constraints').append("            <div id = 'c" + n + "_constraint' class = 'constraint'>                <select name = 'c" + n + "_criteria'>                    <option>Short name</option>                    <option>Fullname</option>                    <option>Course ID #</option>                    <option>Category</option>                </select>                <select name = 'c" + n + "_operator'>                    <option>is</option>                    <option>is not</option>                    <option>contains</option>                    <option>does not contain</option>                </select>                <span id = 'c" + n + "_search_term_0'>                    <input name = 'c" + n + "_search_term_0' type = 'text'/>                    <input id = 'c" + n + "_st_num' value = '1' type = 'hidden'/>                    <img src = 'images/add.svg' class = 'add_search_term icon'/>                </span>            </div>        ");
    };
    this.add_search_term = function(n) {
        var st_num;
        st_num = $("input#c" + n + "_st_num").val();
        $("div#c" + n + "_constraint").append("            <div id = 'c" + n + "_search_term_" + st_num + "' class = 'search_term'>                <div class = 'search_term_or'>OR</div>                <div class = 'search_term_input'>                    <input name = 'c" + n + "_search_term_" + st_num + "' type = 'text'/>                    <img src = 'images/delete.svg' class = 'remove_search_term icon'/>                </div>            </div>        </div>        ");
        return $("input#c" + n + "_st_num").val(parseInt(st_num) + 1);
    };
    $('form#query').submit(function() {
        if ($('input[name="c0_search_term_0"]').val() === '') {
            $('#results_error').html(_('term_missing'));
            return false;
        }
        return true;
    });
    $('.delete_constraint').live('click', remove_constraint);
    $('.add_constraint').live('click', add_constraint);
    $('.add_search_term').live('click', function() {
        return add_search_term($(this).parent().attr('id').slice(1).split('_')[0]);
    });
    $('.remove_search_term').live('click', function() {
        var cons, term;
        cons = $(this).prev().attr('name').slice(1).split('_')[0];
        term = $(this).prev().attr('name').split('_')[3];
        return remove_search_term(cons, term);
    });
}).call(this);
