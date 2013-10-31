window.Perficio = window.Perficio || {};

Perficio.onSelectEmailVar = function(select) {
    if (select.selectedIndex > 0) {
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, select.options[select.selectedIndex].value);
        select.selectedIndex = 0;
    }
};
