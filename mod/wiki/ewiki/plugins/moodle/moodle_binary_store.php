<?php

/*
   This plugin intercepts some of the binary handling functions to
   store uploaded files (as is) into a dedicated directory.
   Because the ewiki database abstraction layer was not designed to
   hold large files (because it reads records in one chunk), you may need
   to use this, else large files may break.

   WARNING: this is actually a hack and not a database layer extension,
   so it will only work with the ewiki.php script itself. The database
   administration tools are not aware of this agreement and therefor
   cannot (for example) backup the externally stored data files!
   If you later choose to disable this extension, the uploaded (and thus
   externally stored) files then cannot be accessed any longer, of course.

   - You must load this plugin __before__ the main script, because the
     binary stuff in ewiki.php always engages automatically.
   - The store directory can be the same as for dbff (filenames differ).
   - All the administration tools/ are not aware of this hack, so __you__
     must take care, when it comes to creating backups.
*/


#-- config
define("EWIKI_DB_STORE_DIRECTORY", "/tmp");	// where to save binary files
define("EWIKI_DB_STORE_MINSIZE", 0);		// send smaller files into db
define("EWIKI_DB_STORE_MAXSIZE", 32 <<20);	// 32MB max per file (but
          // there is actually no way to upload such large files via HTTP)

#  define("EWIKI_DB_STORE_URL", "http://example.com/wiki/files/store/");
          // allows clients to directly access stored plain data files,
          // without redirection through ewiki.php, RTFM


#-- glue
$ewiki_plugins["binary_store"][] = "moodle_binary_store_file";
$ewiki_plugins["binary_get"][] = "moodle_binary_store_get_file";


function moodle_binary_get_path($id, $meta, $course, $wiki, $userid, $groupid) {
    global $CFG;
    $entry=wiki_get_entry($wiki, $course, $userid, $groupid);
    if(!$entry) {
      error("Cannot get entry."); 
    }
    
    $dir=make_upload_directory("$course->id/$CFG->moddata/wiki/$wiki->id/$entry->id/".$meta["section"]);
    if(substr($id, 0, strlen(EWIKI_IDF_INTERNAL))!=EWIKI_IDF_INTERNAL) {
      error("Binary entry does not start with ".EWIKI_IDF_INTERNAL.substr($id, 0, strlen(EWIKI_IDF_INTERNAL)));
    }
    $id = substr($id,strlen(EWIKI_IDF_INTERNAL));
    $id = clean_filename($id);
  
    return "$dir/$id";
}


#-- upload
function moodle_binary_store_file(&$filename, &$id, &$meta, $ext=".bin") {
    # READ-Only
    global $_FILES, $CFG, $course, $wiki, $groupid, $userid, $ewiki_title, $cm;
    if(!$wiki->ewikiacceptbinary) {
      error("This wiki does not accept binaries");
      return 0;
    }
    
    $maxbytes = get_max_upload_file_size();
    
    $entry=wiki_get_entry($wiki, $course, $userid, $groupid);
    if(!$entry->id) {
      error("Cannot get entry.");
    }
    
    $newfile = $_FILES["upload"];
    if(!$id) {
      $newfilename = clean_filename($newfile['name']);
      $id = EWIKI_IDF_INTERNAL.$newfilename;
    }
    $dir=make_upload_directory("$course->id/$CFG->moddata/wiki/$wiki->id/$entry->id/$ewiki_title");
    if ($maxbytes and $newfile['size'] > $maxbytes) {
        return 0;
    }
    if (! $newfilename) {
        notify("This file had a weird filename and couldn't be uploaded");
    } else {
        if (move_uploaded_file($filename, "$dir/$newfilename")) {
            chmod("$dir/$newfilename", $CFG->directorypermissions);
            $meta["binary_store"]=$ewiki_title;
            $filename="";
            return true;
        } else {
            notify("An error happened while saving the file on the server");
            return false;
        }
    }
    return false;
      
                   
/*   if (($meta["size"] >= EWIKI_DB_STORE_MINSIZE) && ($meta["size"] <= EWIKI_DB_STORE_MAXSIZE)) {

      #-- generate internal://md5sum
      if (empty($id)) {
         $md5sum = md5_file($filename);
         $id = EWIKI_IDF_INTERNAL . $md5sum . ".$ext";
         ewiki_log("generated md5sum '$md5sum' from file content");
      }

      #-- move file to dest. location
      $dbfname = EWIKI_DB_STORE_DIRECTORY."/".rawurlencode($id);
      if (@rename($filename, $dbfname) || copy($filename, $dbfname) && unlink($filename)) {
         $filename = "";
         $meta["binary_store"] = 1;
         return(true);
      }
      else {
         ewiki_log("file store error with '$dbfname'", 0);
      }
   }

   return(false);*/
}


#-- download
function moodle_binary_store_get_file($id, &$meta) {
   # READ-Only
   global $CFG, $cm, $course, $wiki, $groupid, $userid;

    #-- check for file
    if(!$wiki->ewikiacceptbinary) {
      error("This wiki does not accept binaries");
      return 0;
    }
    
    
    $filepath=moodle_binary_get_path($id, $meta, $course, $wiki, $userid, $groupid);
    if (file_exists($filepath)) {
            readfile($filepath);
      return(true);
    } else {
      return(false);
    }
      //$dbfname = EWIKI_DB_STORE_DIRECTORY."/".rawurlencode($id);
      //if (file_exists($dbfname)) {
      //   readfile($dbfname);
      //   return(true);
      //}
      //else {
      //   return(false);
      //}

}
?>