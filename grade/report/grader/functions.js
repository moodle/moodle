/** highlight/unset the row of a table **/
function set_row(idx) {
    var table = document.getElementById('user-grades');
    var rowsize = table.rows[idx].cells.length;
    for (var i = 0; i < rowsize; i++) {
        if (table.rows[idx].cells[i]) {
            if (table.rows[idx].cells[i].className.search(/hmarked/) != -1) {
                table.rows[idx].cells[i].className = table.rows[idx].cells[i].className.replace(' hmarked', '');
            } else {
                table.rows[idx].cells[i].className += ' hmarked';
            }
        }
    }
}

/** highlight/unset the column of a table **/
function set_col(idx) {
    var table = document.getElementById('user-grades');
    for (var i = 1; i < table.rows.length; i++) {
        if (table.rows[i].cells[idx]) {
            if (table.rows[i].cells[idx].className.search(/vmarked/) != -1) {
                table.rows[i].cells[idx].className = table.rows[i].cells[idx].className.replace(' vmarked', '');
            } else {
                table.rows[i].cells[idx].className += ' vmarked';
            }
        }
    }
}