<?PHP // $Id$

//  Collect ratings, store them, then return to where we came from


    require("../../config.php");
    require("lib.php");

    if (isguest()) {
        error("Guests are not allowed to rate posts.", $_SERVER["HTTP_REFERER"]);
    }

    require_variable($id);  // The course these ratings are part of

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    if ($data = data_submitted("$CFG->wwwroot/mod/forum/discuss.php")) {    // form submitted

        foreach ($data as $post => $rating) {
            if ($post == "id") {
                continue;
            }
            if ($rating) {
                if (record_exists("forum_ratings", "userid", $USER->id, "post", $post)) {
                    error("You've rated this question before ($post)");
                } else {
                    unset($newrating);
                    $newrating->userid = $USER->id;
                    $newrating->time = time();
                    $newrating->post = $post;
                    $newrating->rating = $rating;

                    if (! insert_record("forum_ratings", $newrating)) {
                        error("Could not insert a new rating ($post = $rating)");
                    }
                }
            }
        }
        redirect($_SERVER["HTTP_REFERER"], get_string("ratingssaved", "forum"));

    } else {
        error("This page was not accessed correctly");
    }

?>
