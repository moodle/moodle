/**
 * enables highlight and marking of rows in data tables
 *
 */
function markRowsInit() {
    // for every table row ...
    var rows = document.getElementById('user-grades').getElementsByTagName('tr');
    for ( var i = 0; i < rows.length; i++ ) {
        // ... with the class 'odd' or 'even' ...
        // ... and to mark the row on click ...
        rows[i].onmousedown = function() {
            
            if (this.className.search(/marked/) != -1) {
                this.className = this.className.replace(' marked', '');   
            } else {
                this.className += ' marked';
            }
        }
    }
}
window.onload=markRowsInit;