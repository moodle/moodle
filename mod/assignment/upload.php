<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);          // Assignment ID

    if (!empty($_FILES['newfile'])) {
        $newfile = $_FILES['newfile'];
    }

    if (! $assignment = get_record("assignment", "id", $id)) {
        error("Not a valid assignment ID");
    }

    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }

    if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "assignment", "upload", "view.php?a=$assignment->id", "$assignment->id", $cm->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment  = get_string("modulename", "assignment");
    $strupload      = get_string("upload");

    print_header("$course->shortname: $assignment->name : $strupload", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strassignments</A> -> 
                  <A HREF=\"view.php?a=$assignment->id\">$assignment->name</A> -> $strupload", 
                  "", "", true);

    if ($submission = get_record("assignment_submissions", "assignment", $assignment->id, "userid", $USER->id)) {
        if ($submission->grade and !$assignment->resubmit) {
            error("You've already been graded - there's no point in uploading anything");
        }
    }

    if (! $dir = assignment_file_area($assignment, $USER)) {
        error("Sorry, an error in the system prevents you from uploading files: contact your teacher or system administrator");
    }

    if (empty($newfile)) {
        notify(get_string("uploadnofilefound", "assignment") );

    } else if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
        if ($newfile['size'] > $assignment->maxbytes) {
            notify(get_string("uploadfiletoobig", "assignment", $assignment->maxbytes));
        } else {
            $newfile_name = clean_filename($newfile['name']);
            if ($newfile_name) {
                if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                    chmod("$dir/$newfile_name", $CFG->directorypermissions);
                    assignment_delete_user_files($assignment, $USER, $newfile_name);
                    if ($submission) {
                        $submission->timemodified = time();
                        $submission->numfiles     = 1;
                        $submission->comment = addslashes($submission->comment);
                        if (update_record("assignment_submissions", $submission)) {
                            print_heading(get_string("uploadsuccess", "assignment", $newfile_name) );
                        } else {
                            notify(get_string("uploadfailnoupdate", "assignment"));
                        }
                    } else {
                        $newsubmission->assignment   = $assignment->id;
                        $newsubmission->userid       = $USER->id;
                        $newsubmission->timecreated  = time();
                        $newsubmission->timemodified = time();
                        $newsubmission->numfiles     = 1;
                        if (insert_record("assignment_submissions", $newsubmission)) {
                            print_heading(get_string("uploadsuccess", "assignment", $newfile_name) );
                        } else {
                            notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                        }
                    }
                } else {
                    notify(get_string("uploaderror", "assignment") );
                }
            } else {
                notify(get_string("uploadbadname", "assignment") );
            }
        }
    } else {
        notify(get_string("uploadnofilefound", "assignment") );
    }
    
    print_continue("view.php?a=$assignment->id");

    print_footer($course);

?>
