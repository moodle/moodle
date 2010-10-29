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
function set_col(col,gradecelloffset,lastheaderrow) {
    var table = document.getElementById('user-grades');

    //highlight the column header
    flip_vmarked(table,lastheaderrow,col);

    //add any grade cell offset (due to colspans) then iterate down the table
    col += gradecelloffset;
    for (var row = lastheaderrow + 1; row < table.rows.length; row++) {
        flip_vmarked(table,row,col);
    }
}

function flip_vmarked(table,row,col) {
    if (table.rows[row].cells[col]) {
        if (table.rows[row].cells[col].className.search(/vmarked/) != -1) {
            table.rows[row].cells[col].className = table.rows[row].cells[col].className.replace(' vmarked', '');
        } else {
            table.rows[row].cells[col].className += ' vmarked';
        }
    }
}
