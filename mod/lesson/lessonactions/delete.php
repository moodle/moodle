<?php

/******************* delete ************************************/

 	if (!isteacher($course->id)) {
	    error("Only teachers can look at this page");
	}

	confirm_sesskey();

	if (empty($_GET['pageid'])) {
	    error("Delete: pageid missing");
	}
	$pageid = required_param('pageid', PARAM_INT);
	if (!$thispage = get_record("lesson_pages", "id", $pageid)) {
	    error("Delete: page record not found");
	}

	print_string("deleting", "lesson");
	// first delete all the associated records...
	delete_records("lesson_attempts", "pageid", $pageid);
	// ...now delete the answers...
	delete_records("lesson_answers", "pageid", $pageid);
	// ..and the page itself
	delete_records("lesson_pages", "id", $pageid);

	// repair the hole in the linkage
	if (!$thispage->prevpageid) {
	    // this is the first page...
	    if (!$page = get_record("lesson_pages", "id", $thispage->nextpageid)) {
	        error("Delete: next page not found");
	    }
	    if (!set_field("lesson_pages", "prevpageid", 0, "id", $page->id)) {
	        error("Delete: unable to set prevpage link");
	    }
	} elseif (!$thispage->nextpageid) {
	    // this is the last page...
	    if (!$page = get_record("lesson_pages", "id", $thispage->prevpageid)) {
	        error("Delete: prev page not found");
	    }
	    if (!set_field("lesson_pages", "nextpageid", 0, "id", $page->id)) {
	        error("Delete: unable to set nextpage link");
	    }
	} else {
	    // page is in the middle...
	    if (!$prevpage = get_record("lesson_pages", "id", $thispage->prevpageid)) {
	        error("Delete: prev page not found");
	    }
	    if (!$nextpage = get_record("lesson_pages", "id", $thispage->nextpageid)) {
	        error("Delete: next page not found");
	    }
	    if (!set_field("lesson_pages", "nextpageid", $nextpage->id, "id", $prevpage->id)) {
	        error("Delete: unable to set next link");
	    }
	    if (!set_field("lesson_pages", "prevpageid", $prevpage->id, "id", $nextpage->id)) {
	        error("Delete: unable to set prev link");
	    }
	}
	redirect("view.php?id=$cm->id", get_string('deletedpage', 'lesson'));
?>