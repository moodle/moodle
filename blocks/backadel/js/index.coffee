strings =
    name_missing: 'Please select a name for this query'
    term_missing: 'Please select at least one search term for this constraint'
    search_missing: 'Please select a saved search'

_ = (s) -> strings[s] + '<br />'

get_search_terms = (n) -> $("div[class^='c_#{ n }_search_term_']")

@count_constraints = -> $('div.constraint').length

@remove_constraint = ->
    if count_constraints() - 1
        $("div#c#{ count_constraints() - 1 }_constraint").remove()

@remove_search_term = (cons, term) ->
    $("div[id$='c#{ cons }_search_term_#{ term }']").remove()

@add_constraint = ->
    n = count_constraints()

    $('div#group_constraints').append("
      <div id = 'c#{ n }_constraint' class = 'constraint'>
        <select name = 'c#{ n }_criteria'>
          <option>Short name</option>
          <option>Fullname</option>
          <option>Course ID #</option>
          <option>Category</option>
        </select>
        <select name = 'c#{ n }_operator'>
          <option>is</option>
          <option>is not</option>
          <option>contains</option>
          <option>does not contain</option>
        </select>
        <span id = 'c#{ n }_search_term_0'>
          <input name = 'c#{ n }_search_term_0' type = 'text'/>
          <input id = 'c#{ n }_st_num' value = '1' type = 'hidden'/>
          <img src = 'images/add.svg' class = 'add_search_term icon'/>
        </span>
      </div>
    ")

@add_search_term = (n) ->
    st_num = $("input#c#{ n }_st_num").val()

    $("div#c#{ n }_constraint").append("
      <div id = 'c#{ n }_search_term_#{ st_num }' class = 'search_term'>
        <div class = 'search_term_or'>OR</div>
        <div class = 'search_term_input'>
          <input name = 'c#{ n }_search_term_#{ st_num }' type = 'text'/>
          <img src = 'images/delete.svg' class = 'remove_search_term icon'/>
        </div>
      </div>
    </div>
    ")

    $("input#c#{ n }_st_num").val(parseInt(st_num) + 1)

$('form#query').submit ->
    if $('input[name="c0_search_term_0"]').val() is ''
        $('#results_error').html(_('term_missing'))

        return false

    true

$('.delete_constraint').live('click', remove_constraint)

$('.add_constraint').live('click', add_constraint)

$('.add_search_term').live('click', ->
    add_search_term($(this).parent().attr('id').slice(1).split('_')[0])
)

$('.remove_search_term').live('click', ->
    cons = $(this).prev().attr('name').slice(1).split('_')[0]
    term = $(this).prev().attr('name').split('_')[3]

    remove_search_term(cons, term)
)
