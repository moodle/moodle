<?php

abstract class convert_file_manager {
    public static function convert_course_files() {
    }
    
    public static function convert_file($converter, $filepath, $mod_context) {
        global $DB;

        // make a dummy record in the temp tabke to get auto gen FILEID
        $backupid = $converter->get_id();
        $dummy = new stdClass;
        $dummy->backupid = $backupid;
        $dummy->itemname = random_string(32);
        if(!$fileid = $DB->insert_record('backup_ids_temp', $dummy)) {
            // Replace with a converter_exception?
            $info = convert_helper::obj_to_readable($dummy);
            throw new Exception(sprintf("Could not insert dummy record with info: (%s)", $info));
        }
        
        // TODO: make this look at moddata as well
        $converter_path = $converter->get_convertdir();
        $oldpath = $converter->get_tempdir();
        $converted_course_files = "$oldpath/course_files";

        // Start processing the file now
        $pathinfo = pathinfo($filepath);
        // Try to get the base
        $base = str_replace($converted_course_files, '', $pathinfo['dirname']);
        // if the $base is the same as before, then it is root
        if($base == $pathinfo['dirname']) {
            $path = '/';
        } else if(is_dir($filepath)) {
            $path = '/'. $pathinfo['basename'] . '/';
        } else {
            $path = $base . '/';
        }  

        // TODO: Might need to spoof more fields
        $file_data = new stdClass;
        $file_data->id = $fileid;
        $file_data->contenthash = sha1_file($filepath);
        $file_data->mod_context = $mod_context;
        $file_data->filename = is_dir($filepath) ? '.' : $pathinfo['basename'];
        $file_data->filesize = is_dir($filepath) ? '0' : filesize($filepath);
        $file_data->mimetype = is_dir($filepath) ? '$@NULL@$' : mimeinfo('type', $pathinfo['basename']);
        $file_data->created = time();
        $file_data->modified = time();

        // Move in-memory file data to the real deal on the server
        $hashdir = substr($file_data->contenthash, 0, 2);
        $folders = array(
                    "$converted_path/files",
                    "$converted_path/files/$hashdir"
                  );
        if(!array_reduce($folders, function($in, $folder) {
            return $in and (file_exists($folder) or mkdir($folder));
        }, true)) {
            throw new Exception("Could not create temp dirs for file");
        }
        // Move file
        $new_path = $folders[1]."/$file_data->contenthash";
        if(!rename($filepath, $new_path) {
            throw new Exception(sprintf("Could not move file into %s", $new_path));
        }       

        // Time to insert this record now
        $fs_record = new stdClass;
        $fs_record->id = $fileid;
        $fs_record->backupid = $backupid;
        $fs_ercord->itemname = 'file';
        $fs_record->itemid = $fileid
        $fs_record->info = serialize($file_data);
        if(!$DB->update_record('backup_ids_temp', $fs_record)) {
            $info = convert_helper::obj_to_readable($fs_record);
            throw new Exception(sprintf("Could not update with real info: (%s)", $info));
        }

        // TODO: register inforef
        return true;
    }

    public static function get_temp_file_info($info) {
        global $DB;
        
    }
    
    public static function create_files_xml() {
    }
}
