/**
 * Javascript module for handling the migration of an assignment.
 *
 * @package   turnitintooltwo
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module mod_turnitintooltwo/migration_tool_migrate
 */

define(['jquery'],
    function($) {
        return {
            migration_tool_migrate: function(courseid, turnitintoolid) {
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    url: M.cfg.wwwroot + "/mod/turnitintooltwo/ajax.php",
                    "data": {action: "begin_migration", courseid: courseid, turnitintoolid: turnitintoolid, sesskey: M.cfg.sesskey},
                    success: function(data) {
                        window.location.href = M.cfg.wwwroot + "/mod/turnitintooltwo/view.php?id="+data.id;
                    },
                    error: function(error) {
                        var data = error.responseJSON;
                        $('#turnitintool_style')
                            .prepend('<div id="full-error" class="box generalbox noticebox">' +
                                data.error + ' ' + data.message + '</div>');
                    }
                });
            }
        };
    }
);