<?PHP // $Id$

//  Collect ratings, store them, then return to where we came from


    require("../../config.php");
    require("lib.php");

    if (isguest()) {
        error("Guests are not allowed to rate posts.", $HTTP_REFERER);
    }

    require_variable($id);  // The course these ratings are part of

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    if (isset($HTTP_POST_VARS)) {    // form submitted

        foreach ($HTTP_POST_VARS as $post => $rating) {
            if ($post == "id") {
                continue;
            }
            if ($rating) {
                if ($check = get_record_sql("SELECT COUNT(*) as count FROM forum_ratings 
                                        WHERE user='$USER->id' AND post='$post'")){
                    if ($check->count == 0) {
                        $timenow = time();
                        if (!$rs = $db->Execute("INSERT DELAYED INTO forum_ratings 
                                            SET user='$USER->id', post='$post', time='$timenow', rating='$rating'")){
                            error("Could not insert a new rating ($post = $rating)");
                        }
                        
                    } else {
                        error("You've rated this question before ($post)");
                    }
                }
            }
        }
        redirect($HTTP_REFERER, "Ratings saved");

    } else {
        error("This page was not accessed correctly");
    }

?>
