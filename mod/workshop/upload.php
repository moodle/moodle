<?PHP  // $Id: upload.php, v1.0 30th April 2003

    require("../../config.php");
    require("lib.php");

    require_variable($a);          // workshop ID

    $newfile = $HTTP_POST_FILES["newfile"];

    if (! $workshop = get_record("workshop", "id", $a)) {
        error("Not a valid workshop ID");
    }

    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    add_to_log($course->id, "workshop", "submit", "view.php?a=$workshop->id", "$workshop->id");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strupload      = get_string("upload");

    print_header("$course->shortname: $workshop->name : $strupload", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strworkshops</A> -> 
                  <A HREF=\"view.php?a=$workshop->id\">$workshop->name</A> -> $strupload", 
                  "", "", true);
 /****
    if ($submissions = workshop_get_submissions($workshop, $USER)) {
        if ($submission->grade and !$workshop->resubmit) {
            error("You've already been graded - there's no point in uploading anything");
        }
    }
****/
	if (!$title = $_POST['title']) {
		notify(get_string("notitlegiven", "workshop") );
		}
	else {	
		if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
			if ($newfile['size'] > $workshop->maxbytes) {
				notify(get_string("uploadfiletoobig", "assignment", $workshop->maxbytes));
				} 
			else {
				$newfile_name = clean_filename($newfile['name']);
				if ($newfile_name) {
					$newsubmission->workshopid   = $workshop->id;
					$newsubmission->userid         = $USER->id;
					$newsubmission->title  = $title;
					$newsubmission->timecreated  = time();
					if (!$newsubmission->id = insert_record("workshop_submissions", $newsubmission)) {
						error("Workshop upload: Failure to create new submission record!");
						}
					if (! $dir = workshop_file_area($workshop, $newsubmission)) {
						error("Sorry, an error in the system prevents you from uploading files: contact your teacher or system administrator");
						}
					if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
						print_heading(get_string("uploadsuccess", "assignment", $newfile_name) );
						}
					else {
						notify(get_string("uploaderror", "assignment") );
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
    print_continue("view.php?a=$workshop->id");

    print_footer($course);

?>
