M.tinymce_managefiles = M.tinymce_managefiles || {}
M.tinymce_managefiles.analysefiles = function(Y) {
    var form = Y.one('#tinymce_managefiles_manageform'),
        usedfiles, missingfiles = '', i;
    if (!form || !window.parent || !window.parent.tinyMCE.activeEditor) {
        return;
    }
    usedfiles = window.parent.tinyMCE.activeEditor.execCommand('mceManageFilesUsedFiles')
    var delfilesfieldset = form.one('#deletefiles,#id_deletefiles')
    for (i in usedfiles) {
        if (!delfilesfieldset.one('.felement.fcheckbox input[name="deletefile[' + usedfiles[i] + ']"]')) {
            missingfiles += '<li>' + usedfiles[i] + '</li>';
        }
    }
    if (missingfiles !== '') {
        form.addClass('hasmissingfiles')
        form.one('.managefilesstatus').setContent(M.util.get_string('hasmissingfiles', 'tinymce_managefiles') + ' <ul>' + missingfiles + '</ul>').addClass('error');
    }
    delfilesfieldset.all('.felement.fcheckbox').each(function(el) {
        var chb = el.one('input[type=checkbox]'),
            match = /^deletefile\[(.*)\]$/.exec(chb.get('name'));
        if (match && usedfiles.indexOf(match[1]) === -1) {
            el.addClass('isunused')
            form.addClass('hasunusedfiles')
        }
    });
    if (missingfiles === '' && !form.hasClass('hasunusedfiles')) {
        form.one('.managefilesstatus').setContent(M.util.get_string('allfilesok', 'tinymce_managefiles'));
    }
}
