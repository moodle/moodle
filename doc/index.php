<?PHP  // $Id$

    require("../config.php");

    optional_variable($file, "intro.html");    // file in this directory to view

    $file = clean_filename($file);

    if (!file_exists($file)) {
        error("404 - File not found");
    }

?>

<HEAD>
    <TITLE>Moodle Documentation</TITLE>
</HEAD>

<FRAMESET ROWS="70,*">
    <FRAME NAME="top" SRC="top.php">
    <FRAMESET COLS="200,*">
        <FRAME NAME="contents" SRC="contents.php">
        <FRAME NAME="main" SRC="<?PHP echo "$file"; ?>">
    </FRAMESET>
</FRAMESET>
