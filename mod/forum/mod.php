<?PHP  // $Id$

/////////////////////////////////////////////////////////////
//
// MOD.PHP - contains functions to add, update and delete
//           an instance of this module
//           
//           Generally called from /course/mod.php
//
/////////////////////////////////////////////////////////////

function add_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    global $CFG;

    $forum->timemodified = time();

    if (! $forum->id = insert_record("forum", $forum)) {
        return false;
    }

    if ($forum->type == "single") {  // Create related discussion.
        include_once("$CFG->dirroot/mod/forum/lib.php");

        $discussion->course   = $forum->course;
        $discussion->forum    = $forum->id;
        $discussion->name     = $forum->name;
        $discussion->intro    = $forum->intro;
        $discussion->assessed = $forum->assessed;

        if (! forum_add_discussion($discussion)) {
            error("Could not add the discussion for this forum");
        }
    }
    add_to_log($forum->course, "forum", "add", "index.php?f=$forum->id", "$forum->id");

    return $forum->id;
}


function update_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $forum->timemodified = time();
    $forum->id = $forum->instance;

    if ($forum->type == "single") {  // Update related discussion and post.
        if (! $discussion = get_record("forum_discussions", "forum", $forum->id)) {
            if ($discussions = get_records("forum_discussions", "forum", $forum->id, "timemodified ASC")) {
                notify("Warning! There is more than one discussion in this forum - using the most recent");
                $discussion = array_pop($discussions);
            } else {
                error("Could not find the discussion in this forum");
            }
        }
        if (! $post = get_record("forum_posts", "id", $discussion->firstpost)) {
            error("Could not find the first post in this forum discussion");
        }

        $post->subject  = $forum->name;
        $post->message  = $forum->intro;
        $post->modified = $forum->timemodified;

        if (! update_record("forum_posts", $post)) {
            error("Could not update the first post");
        }

        $discussion->name = $forum->name;

        if (! update_record("forum_discussions", $discussion)) {
            error("Could not update the discussion");
        }
    }

    if (update_record("forum", $forum)) {
        add_to_log($forum->course, "forum", "update", "index.php?f=$forum->id", "$forum->id");
        return true;
    } else {
        return false;
    }
}


function delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    global $CFG;

    include_once("$CFG->dirroot/mod/forum/lib.php");
    
    if (! $forum = get_record("forum", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($discussions = get_records("forum_discussions", "forum", $forum->id)) {
        foreach ($discussions as $discussion) {
            if (! forum_delete_discussion($discussion)) {
                $result = false;
            }
        }
    }

    if (! delete_records("forum_subscriptions", "forum", "$forum->id")) {
        $result = false;
    }

    if (! delete_records("forum", "id", "$forum->id")) {
        $result = false;
    }

    return $result;
}


?>
