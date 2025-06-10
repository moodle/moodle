/* TODO needs to be converted to AMD and converted to vanila JS */

var editor_querysql = null;
var editor_remotequerysql = null;

M.block_configurable_reports = {


    init: function(Y) {
        this.Y = Y;

        // Documentation can be found @ http://codemirror.net/
        editor_querysql = CodeMirror.fromTextArea(document.getElementById('id_querysql'), {
            mode: "text/x-mysql",
            rtlMoveVisually: true,
            indentWithTabs: true,
            smartIndent: true,
            lineNumbers: true,
            matchBrackets: true,
            autofocus: true,
        });

        editor_remotequerysql = CodeMirror.fromTextArea(document.getElementById('id_remotequerysql'), {
            mode: "text/x-mysql",
            rtlMoveVisually: true,
            indentWithTabs: true,
            smartIndent: true,
            lineNumbers: true,
            matchBrackets: true,
        });

    },

    loadReportCategories: function(Y) {
        this.Y = Y;

        select_reportcategories = Y.one('#id_crreportcategories');
        Y.io(M.cfg.wwwroot + '/blocks/configurable_reports/repository.php', {
            data: 'action=listreports&sesskey=' + M.cfg.sesskey,
            context: this,
            method: "GET",
            on: {
                success: function(id, o) {
                    var response = Y.JSON.parse(o.responseText);

                    for (var prop in response) {
                        if (response.hasOwnProperty(prop)) {
                            option = Y.Node.create('<option value=' + response[prop]["path"] + '>' + response[prop]["name"] + '</option>');
                            select_reportcategories.appendChild(option);
                        }
                    }

                },
                failure: function(id, o) {
                    // TODO use strings.
                    window.alert('Repository unreachable');
                }
            }
        });

    },

    onchange_crreportcategories: function(select_element) {
        var Y = this.Y;

        select_reportnames = Y.one('#id_crreportnames');

        Y.io(M.cfg.wwwroot + '/blocks/configurable_reports/repository.php', {
            data: 'action=listcategory&category=' + select_element[select_element.selectedIndex].value + '&sesskey=' + M.cfg.sesskey,
            context: this,
            method: "GET",
            on: {
                success: function(id, o) {
                    var response = Y.JSON.parse(o.responseText);
                    select_reportnames.get('childNodes').remove();
                    option = Y.Node.create('<option value="-1">...</option>');
                    select_reportnames.appendChild(option);

                    for (var prop in response) {
                        if (response.hasOwnProperty(prop)) {
                            option = Y.Node.create('<option value=' + response[prop]["git_url"] + '>' + response[prop]["name"] + '</option>');
                            select_reportnames.appendChild(option);
                        }
                    }
                },
                failure: function(id, o) {
                    window.alert('Repository unreachable');
                }
            }
        });
    },

    onchange_crreportnames: function(select_element) {
        var Y = this.Y;

        var path = select_element[select_element.selectedIndex].value;
        location.href = location.href + "&importurl=" + encodeURIComponent(path);
    },

    onchange_reportcategories: function(select_element) {
        var Y = this.Y;

        select_reportsincategory = Y.one('#id_reportsincategory');
        select_reportsincategory.setStyle('visibility', 'hidden');
        Y.io(M.cfg.wwwroot + '/blocks/configurable_reports/list_reports_in_category.php', {
            data: 'category=' + select_element[select_element.selectedIndex].value + '&sesskey=' + M.cfg.sesskey,
            context: this,
            method: "GET",
            on: {
                success: function(id, o) {
                    var response = Y.JSON.parse(o.responseText);
                    var list = Y.Node.create('<select>');
                    option = Y.Node.create('<option value="-1">Choose...</option>');
                    list.appendChild(option);

                    for (var prop in response) {
                        if (response.hasOwnProperty(prop)) {
                            option = Y.Node.create('<option value=' + response[prop]["fullname"] + '>' + response[prop]["name"] + '</option>');
                            list.appendChild(option);
                        }
                    }
                    select_reportsincategory.setStyle('visibility', 'visible');
                    list.setAttribute('id', 'id_reportsincategory');
                    list.setAttribute('name', 'reportsincategory');
                    list.setAttribute('onchange', 'M.block_configurable_reports.onchange_reportsincategory(this,"' + this.sesskey + '")');
                    select_reportsincategory.replace(list);
                },
                failure: function(id, o) {
                    if (o.statusText != 'abort') {
                        select_reportsincategory.setStyle('visibility', 'hidden');
                    }
                }
            }
        });
    },

    onchange_reportsincategory: function(select_element) {
        var Y = this.Y;

        textarea_reportsincategory = Y.one('#id_remotequerysql');
        Y.io(M.cfg.wwwroot + '/blocks/configurable_reports/get_remote_report.php', {
            data: 'reportname=' + select_element[select_element.selectedIndex].value + '&sesskey=' + M.cfg.sesskey,
            context: this,
            method: "GET",
            on: {
                success: function(id, o) {
                    var response = Y.JSON.parse(o.responseText);

                    // Use regular textarea element.
                    textarea_reportsincategory.set('value', response);

                    // Use codemirror editor.
                    editor_remotequerysql.setValue(response);
                },
                failure: function(id, o) {
                    if (o.statusText != 'abort') {
                        select_reportsincategory.setStyle('visibility', 'hidden');
                    }
                }
            }
        });
    }
}

function menuplugin(event, args) {
    location.href = args.url + document.getElementById('menuplugin').value;
}