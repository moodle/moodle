<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //wiki mods

    //This is the "graphical" structure of the wiki mod:
    //
    //                       wiki
    //                     (CL,pk->id)
    //
    //                    wiki_entries
    //                     (pk->id, fk->wikiid)
    //
    //                    wiki_pages
    //                     (pk->pagename,version,wiki, fk->wiki)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
    function wiki_backup_mods($bf,$preferences) {
        global $CFG;

        $status = true;

        ////Iterate over wiki table
        if ($wikis = get_records ("wiki","course", $preferences->backup_course,"id")) {
            foreach ($wikis as $wiki) {
                if (backup_mod_selected($preferences,'wiki',$wiki->id)) {
                    wiki_backup_one_mod($bf,$preferences,$wiki);
                }
            }
        }
       
        return $status;
    }

    function wiki_backup_one_mod($bf,$preferences,$wiki) {

        $status = true;

        if (is_numeric($wiki)) {
            $wiki = get_record('wiki','id',$wiki);
        }
        
        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$wiki->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"wiki"));
        fwrite ($bf,full_tag("NAME",4,false,$wiki->name));
        fwrite ($bf,full_tag("SUMMARY",4,false,$wiki->summary));
        fwrite ($bf,full_tag("PAGENAME",4,false,$wiki->pagename));
        fwrite ($bf,full_tag("WTYPE",4,false,$wiki->wtype));
        fwrite ($bf,full_tag("EWIKIPRINTTITLE",4,false,$wiki->ewikiprinttitle));
        fwrite ($bf,full_tag("HTMLMODE",4,false,$wiki->htmlmode));
        fwrite ($bf,full_tag("EWIKIACCEPTBINARY",4,false,$wiki->ewikiacceptbinary));
        fwrite ($bf,full_tag("DISABLECAMELCASE",4,false,$wiki->disablecamelcase));
        fwrite ($bf,full_tag("SETPAGEFLAGS",4,false,$wiki->setpageflags));
        fwrite ($bf,full_tag("STRIPPAGES",4,false,$wiki->strippages));
        fwrite ($bf,full_tag("REMOVEPAGES",4,false,$wiki->removepages));
        fwrite ($bf,full_tag("REVERTCHANGES",4,false,$wiki->revertchanges));
        fwrite ($bf,full_tag("INITIALCONTENT",4,false,$wiki->initialcontent));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$wiki->timemodified));
        
        //backup entries and pages
        if (backup_userdata_selected($preferences,'wiki',$wiki->id)) {
            $status = backup_wiki_entries($bf,$preferences,$wiki->id, $preferences->mods["wiki"]->userinfo);
            $status = backup_wiki_files_instance($bf,$preferences,$wiki->id);
        }
        
        //End mod
        fwrite ($bf,end_tag("MOD",3,true));
        
        return $status;
    }

    function wiki_check_backup_mods_instances($instance,$backup_unique_code) {
        $info[$instance->id.'0'][0] = $instance->name;
        $info[$instance->id.'0'][1] = '';
        // wiki_check_backup_mods ignores userdata, so we do too.
        return $info;
    }

    ////Return an array of info (name,value)
    function wiki_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += wiki_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","wiki");
        $info[0][1] = count_records("wiki", "course", "$course");
        return $info;
    }

     //Backup wiki_entries contents (executed from wiki_backup_mods)
    function backup_wiki_entries ($bf,$preferences,$wiki, $userinfo) {

        global $CFG;

        $status = true;

        $wiki_entries = get_records("wiki_entries","wikiid",$wiki,"id");
        //If there are entries
        if ($wiki_entries) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ENTRIES",4,true));
            //Iterate over each entry
            foreach ($wiki_entries as $wik_ent) {
                //Entry start
                $status =fwrite ($bf,start_tag("ENTRY",5,true));

                fwrite ($bf,full_tag("ID",6,false,$wik_ent->id));
                fwrite ($bf,full_tag("GROUPID",6,false,$wik_ent->groupid));
                fwrite ($bf,full_tag("USERID",6,false,$wik_ent->userid));
                fwrite ($bf,full_tag("PAGENAME",6,false,$wik_ent->pagename));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$wik_ent->timemodified));

                //Now save entry pages
                $status = backup_wiki_pages($bf,$preferences,$wik_ent->id);

                //Entry end
                $status =fwrite ($bf,end_tag("ENTRY",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ENTRIES",4,true));
        }
        return $status;
    }

    //Write wiki_pages contents
    function backup_wiki_pages ($bf,$preferences,$entryid) {

        global $CFG;

        $status = true;

        $pages = get_records("wiki_pages","wiki",$entryid);
        if ($pages) {
            //Start tag
            $status =fwrite ($bf,start_tag("PAGES",6,true));
            //Iterate over each page
            foreach ($pages as $page) {
                $status =fwrite ($bf,start_tag("PAGE",7,true));

                fwrite ($bf,full_tag("ID",8,false,$page->id));
                fwrite ($bf,full_tag("PAGENAME",8,false,$page->pagename));
                fwrite ($bf,full_tag("VERSION",8,false,$page->version));
                fwrite ($bf,full_tag("FLAGS",8,false,$page->flags));
                fwrite ($bf,full_tag("CONTENT",8,false,$page->content));
                fwrite ($bf,full_tag("AUTHOR",8,false,$page->author));
                fwrite ($bf,full_tag("USERID",8,false,$page->userid));
                fwrite ($bf,full_tag("CREATED",8,false,$page->created));
                fwrite ($bf,full_tag("LASTMODIFIED",8,false,$page->lastmodified));
                fwrite ($bf,full_tag("REFS",8,false,str_replace("\n","$@LINEFEED@$",$page->refs)));
                fwrite ($bf,full_tag("META",8,false,$page->meta));
                fwrite ($bf,full_tag("HITS",8,false,$page->hits));

                $status =fwrite ($bf,end_tag("PAGE",7,true));
            }
            $status =fwrite ($bf,end_tag("PAGES",6,true));
        }
        return $status;
    }
    
    function backup_wiki_files_instance($bf,$preferences,$instanceid) {

        global $CFG;
        
        $status = true;
        
        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/wiki/",true);
        //Now copy the forum dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki/".$instanceid)) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/wiki/".$instanceid);
            }
        }

        return $status;
    }

    //Backup wiki binary files
    function backup_wiki_files($bf,$preferences) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the forum dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki")) {
                $handle = opendir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki");
                while (false!==($item = readdir($handle))) {
                    if ($item != '.' && $item != '..' && is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki/".$item)
                        && array_key_exists($item,$preferences->mods['wiki']->instances)
                        && !empty($preferences->mods['wiki']->instances[$item]->backup)) {
                        $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/wiki/".$item,
                                                   $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/wiki/",$item);
                    }
                }
            }
        }

        return $status;

    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function wiki_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of wikis
        $buscar="/(".$base."\/mod\/wiki\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@WIKIINDEX*$2@$',$content);

        //Link to wiki view by moduleid
        $buscar="/(".$base."\/mod\/wiki\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@WIKIVIEWBYID*$2@$',$result);

        return $result;
    }

?>
