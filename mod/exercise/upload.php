<?PHP  // $Id: upload.php, v1.0 30th April 2003

    require("../../config.php");
    require("lib.php");

    require_variable($id);          // course module ID
    $timenow = time();

    $newfile = $HTTP_POST_FILES["newfile"];

    // get some esential stuff...
	if (! $cm = get_record("course_modules", "id", $id)) {
		error("Course Module ID was incorrect");
	}

	if (! $course = get_record("course", "id", $cm->course)) {
		error("Course is misconfigured");
	}

	if (! $exercise = get_record("exercise", "id", $cm->instance)) {
		error("Course module is incorrect");
	}

    require_login($course->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");
    $strupload      = get_string("upload");

    print_header("$course->shortname: $exercise->name : $strupload", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strexercises</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$exercise->name</A> -> $strupload", 
                  "", "", true);

    // check that this is not a "rapid" second submission, caused by using the back button
    // only check if a student, teachers may want to submit a set of exercise variants
    if (isstudent($course->id)) {
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            // returns all submissions, newest on first
            foreach ($submissions as $submission) {
                if ($submission->timecreated > $timenow - $CFG->maxeditingtime) {
                    // ignore this submission
                    redirect("view.php?id=$cm->id");
                    print_footer($course);
                    exit();
                }
            }
        }
    }
                    
    // check existence of title
    if (!$title = $_POST['title']) {
		notify(get_string("notitlegiven", "exercise") );
		}
	else {	
		if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
			if ($newfile['size'] > $exercise->maxbytes) {
				notify(get_string("uploadfiletoobig", "assignment", $exercise->maxbytes));
				} 
			else {
				$newfile_name = clean_filename($newfile['name']);
				if ($newfile_name) {
					$newsubmission->exerciseid   = $exercise->id;
					if (isteacher($course->id)) {
						// it's an exercise submission, flag it as such
						$newsubmission->userid         = 0;
						$newsubmission->isexercise = 1;  // it's a description of an exercise
						}
					else {
						$newsubmission->userid = $USER->id;
						}
					$newsubmission->title  = $title;
					$newsubmission->timecreated  = $timenow;
                    if ($timenow > $exercise->deadline) {
                        $newsubmission->late = 1;
                    }
					if (!$newsubmission->id = insert_record("exercise_submissions", $newsubmission)) {
						error("exercise upload: Failure to create new submission record!");
						}
					if (! $dir = exercise_file_area($exercise, $newsubmission)) {
						error("Sorry, an error in the system prevents you from uploading files: contact your teacher or system administrator");
						}
					if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                        add_to_log($course->id, "exercise", "submit", "view.php?id=$cm->id", "$exercise->id");
						print_heading(get_string("uploadsuccess", "assignment", $newfile_name) );
						}
					else {
						notify(get_string("uploaderror", "assignment") );
						}
					// clear resubmit flags
					if (!set_field("exercise_submissions", "resubmit", 0, "exerciseid", $exercise->id, "userid", $USER->id)) {
						error("Exercise Upload: unable to reset resubmit flag");
						}
					} 
				else {
					notify(get_string("uploadbadname", "assignment") );
					}
				}
			}
		else {
			notify(get_string("uploadnofilefound", "assignment") );
			}
		}
    print_continue("view.php?id=$cm->id");

    print_footer($course);

?>
