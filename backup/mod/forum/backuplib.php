<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //forum mods

    //This is the "graphical" structure of the forum mod:
    //
    //                           forum                                      
    //                        (CL,pk->id)
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        subscriptions                    forum_discussions
    //    (UL,pk->id, fk->forum)             (UL,pk->id, fk->forum)
    //                                               |
    //                                               |
    //                                               |
    //                                           forum_posts
    //                             (UL,pk->id,fk->discussion,nt->parent,files) 
    //                                               |
    //                                               |
    //                                               |
    //                                          forum_ratings
    //                                      (UL,pk->id,fk->post)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function forum_backup_mods() {
        print "hola";
    }

   ////Return an array of info (name,value)
   function forum_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","forum");
        if ($ids = forum_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Subscriptions
            $info[1][0] = get_string("subscriptions","forum");
            if ($ids = forum_subscription_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
            //Discussions
            $info[2][0] = get_string("discussions","forum");
            if ($ids = forum_discussion_ids_by_course ($course)) {
                $info[2][1] = count($ids);
            } else {
                $info[2][1] = 0;
            }
            //Posts
            $info[3][0] = get_string("posts","forum");
            if ($ids = forum_post_ids_by_course ($course)) {
                $info[3][1] = count($ids);
            } else {
                $info[3][1] = 0;
            }
            //Ratings
            $info[4][0] = get_string("ratings","forum");
            if ($ids = forum_rating_ids_by_course ($course)) {
                $info[4][1] = count($ids);
            } else {
                $info[4][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of forums id
    function forum_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}forum a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of forum subscriptions id
    function forum_subscription_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.forum
                                 FROM {$CFG->prefix}forum_subscriptions s,
                                      {$CFG->prefix}forum a
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id");
    }

    //Returns an array of forum discussions id
    function forum_discussion_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.forum      
                                 FROM {$CFG->prefix}forum_discussions s,    
                                      {$CFG->prefix}forum a 
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id"); 
    }

    //Returns an array of forum posts id
    function forum_post_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT p.id , p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s,
                                      {$CFG->prefix}forum a
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id AND
                                       p.discussion = s.id");
    }

    //Returns an array of ratings posts id      
    function forum_rating_ids_by_course ($course) {      

        global $CFG;

        return get_records_sql ("SELECT r.id, r.post, p.discussion, s.forum
                                 FROM {$CFG->prefix}forum_ratings r,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum_discussions s,
                                      {$CFG->prefix}forum a    
                                 WHERE a.course = '$course' AND
                                       s.forum = a.id AND   
                                       p.discussion = s.id AND
                                       r.post = p.id");
    }
?>
