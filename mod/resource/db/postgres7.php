<?PHP // $Id$

function resource_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2004013101) {
        modify_database("", "INSERT INTO prefix_log_display VALUES ('resource', 'update', 'resource', 'name');");
        modify_database("", "INSERT INTO prefix_log_display VALUES ('resource', 'add', 'resource', 'name');");
    }

    if ($oldversion < 2004071000) {
        table_column("resource", "", "popup", "text", "", "", "", "", "alltext");
        if ($resources = get_records_select("resource", "type='3' OR type='5'", "", "id, alltext")) {
            foreach ($resources as $resource) {
                $resource->popup = addslashes($resource->alltext);
                $resource->alltext = "";
                if (!update_record("resource", $resource)) {
                    notify("Error updating popup field for resource id = $resource->id");
                }                
            }
        }
        require("$CFG->dirroot/course/lib.php");
        rebuild_course_cache();
    }
    
    if ($oldversion < 2004071300) {
        table_column("resource", "", "options", "varchar", "255", "", "", "", "popup");
    }
    
    if ($oldversion < 2004071303) {
        table_column("resource", "type", "type", "varchar", "30", "", "", "", "");

        modify_database("", "UPDATE prefix_resource SET type='reference' WHERE type='1';");
        modify_database("", "UPDATE prefix_resource SET type='url', options='frame' WHERE type='2';");
        modify_database("", "UPDATE prefix_resource SET type='file' WHERE type='3';");
        modify_database("", "UPDATE prefix_resource SET type='text', options='0' WHERE type='4';");
        modify_database("", "UPDATE prefix_resource SET type='url' WHERE type='5';");
        modify_database("", "UPDATE prefix_resource SET type='html' WHERE type='6';");
        modify_database("", "UPDATE prefix_resource SET type='program' WHERE type='7';");
        modify_database("", "UPDATE prefix_resource SET type='text', options='3' WHERE type='8';");
        modify_database("", "UPDATE prefix_resource SET type='directory' WHERE type='9';");
    }
    
    return true;
}


?>

