/**
 * Javascript controller for launching the migration tool modal.
 *
 * @package   turnitintooltwo
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module mod_turnitintooltwo/migration_tool_launch
 */

define(['jquery',
        'core/templates',
        'core/modal_factory',
        'core/modal_events',
        'mod_turnitintooltwo/modal_migration_tool',
        'mod_turnitintooltwo/migration_tool_migrate'
    ],
    function($, Templates, ModalFactory, ModalEvents, ModalMigrationTool, MigrationToolMigrate) {
        return {
            migration_tool_launch: function() {
                // Check whether this assignment has been migrated in this session and redirect if so.
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": M.cfg.wwwroot + "/mod/turnitintooltwo/ajax.php",
                    "data": {
                        action: "check_migrated",
                        turnitintoolid: $("#migrate_type").data("turnitintoolid"),
                        sesskey: M.cfg.sesskey
                    },
                    "success": function(data) {
                        if (data.migrated === true) {
                            window.location.href = M.cfg.wwwroot + "/mod/turnitintooltwo/view.php?id="+data.v2id;
                        } else {
                            ModalFactory.create({
                                type: ModalMigrationTool.TYPE
                            })
                            .then(function(modal) {
                                modal.show();

                                // During automatic migration, we don't need to ask to migrate.
                                if ($('#migrate_type').data("migratetype") === 2) {
                                    $('.asktomigrate').hide();
                                    $('.migrating').show();

                                    MigrationToolMigrate.migration_tool_migrate(
                                        $("#migrate_type").data("courseid"),
                                        $("#migrate_type").data("turnitintoolid")
                                    );
                                }
                            });
                        }
                    }
                });
            }
        };
    }
);