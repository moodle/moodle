/**
 * Javascript to insert the field tags into the textarea.
 * Used when editing a data template
 */
function insert_field_tags(selectlist) {
  if (typeof(currEditor) != 'undefined' && currEditor._editMode == 'wysiwyg') {
    // HTMLArea-specific
     currEditor.insertHTML(selectlist.options[selectlist.selectedIndex].value);
  } else {
    // For inserting when in HTMLArea code view or for normal textareas
     insertAtCursor(currTextarea, selectlist.options[selectlist.selectedIndex].value);
  }
}

/**
 * javascript for hiding/displaying advanced search form when viewing
 */
function showHideAdvSearch(checked) {
    var divs = document.getElementsByTagName('div');
    for(i=0;i<divs.length;i++) {
        if(divs[i].id.match('data_adv_form')) {
            if(checked) {
                divs[i].style.display = 'inline';
            }
            else {
                divs[i].style.display = 'none';
            }
        }
        else if (divs[i].id.match('reg_search')) {
            if (!checked) {
                divs[i].style.display = 'inline';
            }
            else {
                divs[i].style.display = 'none';
            }
        }
    }
}