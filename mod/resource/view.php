<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
 
    require_variable($id);    // Course Module ID

    if (!empty($CFG->forcelogin)) {
        require_login();
    }

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $resource = get_record("resource", "id", $cm->instance)) {
        error("Resource ID was incorrect");
    }

    require ("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $resourceinstance = new $resourceclass($id);

    $resourceinstance->display();

?>
