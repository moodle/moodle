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
        redirect($HTTP_REFERER, "Ratings saved");

    } else {
        error("This page was not accessed correctly");
    }

?>
