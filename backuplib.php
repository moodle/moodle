<?PHP // $Id: backuplib.php,v 1.1 2006/03/12 18:39:59 skodak Exp $
    //This php script contains all the stuff to backup/restore
    //book mods

    //This is the 'graphical' structure of the book mod:
    //
    //                       book
    //                     (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                     book_chapters
    //               (CL,pk->id, fk->bookid)
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
    function book_backup_mods($bf,$preferences) {
        global $CFG;

        $status = true;

        ////Iterate over book table
        if ($books = get_records ('book', 'course', $preferences->backup_course, 'id')) {
            foreach ($books as $book) {
                if (backup_mod_selected($preferences,'book',$book->id)) {
                    $status = book_backup_one_mod($bf,$preferences,$book);
                }
            }
        }
        return $status;
    }

    function book_backup_one_mod($bf,$preferences,$book) {

        global $CFG;

        if (is_numeric($book)) {
            $book = get_record('book','id',$book);
        }

        $status = true;

        //Start mod
        fwrite ($bf,start_tag('MOD',3,true));
        //Print book data
        fwrite ($bf,full_tag('ID',4,false,$book->id));
        fwrite ($bf,full_tag('MODTYPE',4,false,'book'));
        fwrite ($bf,full_tag('NAME',4,false,$book->name));
        fwrite ($bf,full_tag('SUMMARY',4,false,$book->summary));
        fwrite ($bf,full_tag('NUMBERING',4,false,$book->numbering));
        fwrite ($bf,full_tag('DISABLEPRINTING',4,false,$book->disableprinting));
        fwrite ($bf,full_tag('CUSTOMTITLES',4,false,$book->customtitles));
        fwrite ($bf,full_tag('TIMECREATED',4,false,$book->timecreated));
        fwrite ($bf,full_tag('TIMEMODIFIED',4,false,$book->timemodified));
        //back up the chapters
        $status = backup_book_chapters($bf,$preferences,$book);
        //End mod
        $status = fwrite($bf,end_tag('MOD',3,true));

        return $status;
    }

    //Backup book_chapters contents (executed from book_backup_mods)
    function backup_book_chapters($bf,$preferences,$book) {

        global $CFG;

        $status = true;
        //Print book's chapters
        if ($chapters = get_records('book_chapters', 'bookid', $book->id, 'id')) {
            //Write start tag
            $status =fwrite ($bf,start_tag('CHAPTERS',4,true));
            foreach ($chapters as $ch) {
                //Start chapter
                fwrite ($bf,start_tag('CHAPTER',5,true));
                //Print chapter data
                fwrite ($bf,full_tag('ID',6,false,$ch->id));
                fwrite ($bf,full_tag('PAGENUM',6,false,$ch->pagenum));
                fwrite ($bf,full_tag('SUBCHAPTER',6,false,$ch->subchapter));
                fwrite ($bf,full_tag('TITLE',6,false,$ch->title));
                fwrite ($bf,full_tag('CONTENT',6,false,$ch->content));
                fwrite ($bf,full_tag('HIDDEN',6,false,$ch->hidden));
                fwrite ($bf,full_tag('TIMECREATED',6,false,$ch->timecreated));
                fwrite ($bf,full_tag('TIMEMODIFIED',6,false,$ch->timemodified));
                fwrite ($bf,full_tag('IMPORTSRC',6,false,$ch->importsrc));
                //End chapter
                $status = fwrite ($bf,end_tag('CHAPTER',5,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag('CHAPTERS',4,true));
        }
        return $status;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function book_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        $result = $content;

        //Link to the list of books
        $buscar="/(".$base."\/mod\/book\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@BOOKINDEX*$2@$',$result);

        //Link to book's specific chapter
        $buscar="/(".$base."\/mod\/book\/view.php\?id\=)([0-9]+)\&chapterid\=([0-9]+)/";
        $result= preg_replace($buscar,'$@BOOKCHAPTER*$2*$3@$',$result);

        //Link to book's first chapter
        $buscar="/(".$base."\/mod\/book\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@BOOKSTART*$2@$',$result);

        return $result;
    }


    ////Return an array of info (name,value)
    function book_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += book_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }

         //First the course data
         $info[0][0] = get_string('modulenameplural','book');
         $info[0][1] = count_records('book', 'course', $course);

         //No user data for books ;-)

         return $info;
    }

    ////Return an array of info (name,value)
    function book_check_backup_mods_instances($instance,$backup_unique_code) {
         $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
         $info[$instance->id.'0'][1] = '';

         return $info;
    }

?>
