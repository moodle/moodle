<?PHP //$Id$

// Deletes the moodledata directory, COMPLETELY!!
// BE VERY CAREFUL USING THIS!

    require_once("../config.php");

    require_login();

    if (!isadmin()) {
        error("You must be admin to use this script!");
    }

    $deletedir = $CFG->dataroot;   // The directory to delete!

    if (!$sure) {
        notice_yesno ("Are you completely sure you want to delete everything inside the directory $deletedir ?", "delete.php?sure=yes", "index.php");
        exit;
    }

    if (!$reallysure) {
        notice_yesno ("Are you REALLY REALLY completely sure you want to delete everything inside the directory $deletedir (this includes all user images, and any other course files that have been created) ?", "delete.php?sure=yes&reallysure=yes", "index.php");
        exit;
    }

    /// OK, here goes ...

    delete_subdirectories($deletedir);

    echo "<H1 align=center>Done!</H1>";
    print_continue($CFG->wwwroot);
    exit;


function delete_subdirectories($rootdir) {

    $dir = opendir($rootdir);

    while ($file = readdir($dir)) {
        if ($file != "." and $file != "..") {
            $fullfile = "$rootdir/$file";
            if (filetype($fullfile) == "dir") {
                delete_subdirectories($fullfile);
                echo "Deleting $fullfile ... ";
                if (rmdir($fullfile)) {
                    echo "Done.<BR>";
                } else {
                    echo "FAILED.<BR>";
                }
            } else {
                echo "Deleting $fullfile ... ";
                if (unlink("$fullfile")) {
                    echo "Done.<BR>";
                } else {
                    echo "FAILED.<BR>";
                }
            }
        }
    }
    closedir($dir);
}
  
?>
