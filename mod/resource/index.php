<?PHP // $Id$

    require_once("../../config.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    if ($course->category) {
        require_login($course->id);
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    add_to_log($course->id, "resource", "view all", "index.php?id=$course->id", "");

    $strresource = get_string("modulename", "resource");
    $strresources = get_string("modulenameplural", "resource");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strsummary = get_string("summary");
    $strlastmodified = get_string("lastmodified");

    print_header("$course->shortname: $strresources", "$course->fullname", "$navigation $strresources", 
                 "", "", true, "", navmenu($course));

    if ($course->format == "weeks" or $course->format == "topics") {
        $sortorder = "cw.section ASC";
    } else {
        $sortorder = "m.timemodified DESC";
    }

    if (! $resources = get_all_instances_in_course("resource", $course->id, $sortorder)) {
        notice("There are no resources", "../../course/view.php?id=$course->id");
        exit;
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strsummary);
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strsummary);
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strlastmodified, $strname, $strsummary);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($resources as $resource) {

        $tt = "";
        if ($course->format == "weeks" or $course->format == "topics") {
            if ($resource->section) {
                $tt = "$resource->section";
            }
        } else {
            $tt = "<FONT SIZE=1>".userdate($resource->timemodified);
        }
        if (!$resource->visible) {
           //Show dimmed if the mod is hidden
           $table->data[] = array ($tt, "<A class=\"dimmed\" HREF=\"view.php?id=$resource->coursemodule\">$resource->name</A>",
                                   text_to_html($resource->summary) );
        } else {
           //Show normal if the mod is visible
           $table->data[] = array ($tt, "<A HREF=\"view.php?id=$resource->coursemodule\">$resource->name</A>",
                                   text_to_html($resource->summary) );
        }
    }

    echo "<BR>";

    print_table($table);

    print_footer($course);

 
?>

