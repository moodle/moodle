YUI.add('moodle-backup-backupselectall', function(Y) {

// Namespace for the backup
M.core_backup = M.core_backup || {};

/**
 * Adds select all/none links to the top of the backup/restore/import schema page.
 */
M.core_backup.select_all_init = function(str) {
    var formid = null;

    var helper = function(e, check, type) {
        e.preventDefault();

        var len = type.length;
        Y.all('input[type="checkbox"]').each(function(checkbox) {
            var name = checkbox.get('name');
            if (name.substring(name.length - len) == type) {
                checkbox.set('checked', check);
            }
        });

        // At this point, we really need to persuade the form we are part of to
        // update all of its disabledIf rules. However, as far as I can see,
        // given the way that lib/form/form.js is written, that is impossible.
        if (formid && M.form) {
            M.form.updateFormState(formid);
        }
    };

    var html_generator = function(classname, idtype) {
        return '<div class="' + classname + '">' +
                    '<div class="fitem fitem_fcheckbox">' +
                        '<div class="fitemtitle">' + str.select + '</div>' +
                        '<div class="felement">' +
                            '<a id="backup-all-' + idtype + '" href="#">' + str.all + '</a> / ' +
                            '<a id="backup-none-' + idtype + '" href="#">' + str.none + '</a>' +
                        '</div>' +
                    '</div>' +
                '</div>';
    };

    var firstsection = Y.one('fieldset#coursesettings .fcontainer.clearfix .grouped_settings.section_level');
    if (!firstsection) {
        // This is not a relevant page.
        return;
    }
    if (!firstsection.one('.felement.fcheckbox')) {
        // No checkboxes.
        return;
    }

    formid = firstsection.ancestor('form').getAttribute('id');

    var withuserdata = false;
    Y.all('input[type="checkbox"]').each(function(checkbox) {
        var name = checkbox.get('name');
        if (name.substring(name.length - 9) == '_userdata') {
            withuserdata = '_userdata';
        } else if (name.substring(name.length - 9) == '_userinfo') {
            withuserdata = '_userinfo';
        }
    });

    var html = html_generator('include_setting section_level', 'included');
    if (withuserdata) {
        html += html_generator('normal_setting', 'userdata');
    }
    var links = Y.Node.create('<div class="grouped_settings section_level">' + html + '</div>');
    firstsection.insert(links, 'before');

    Y.one('#backup-all-included').on('click',  function(e) { helper(e, true,  '_included'); });
    Y.one('#backup-none-included').on('click', function(e) { helper(e, false, '_included'); });
    if (withuserdata) {
        Y.one('#backup-all-userdata').on('click',  function(e) { helper(e, true,  withuserdata); });
        Y.one('#backup-none-userdata').on('click', function(e) { helper(e, false, withuserdata); });
    }
}

}, '@VERSION@', {'requires':['base','node','event', 'node-event-simulate']});
