<?PHP  // $Id$

    require("../config.php");

    optional_variable($file, "");    // docs file to view

    if (empty($file)) {
        $include = false;
        $file = "intro.html";
    } else {
        $include = true;
    }

    document_file($file, $include);

    if ($include) {
        exit;
    }

?>

<HEAD>
    <TITLE>Moodle Documentation</TITLE>
</HEAD>

<FRAMESET ROWS="70,*">
    <FRAME NAME="top" SRC="top.php">
    <FRAMESET COLS="200,*">
        <FRAME NAME="contents" SRC="contents.php">
        <FRAME NAME="main" SRC="index.php?file=<?PHP echo "$file"; ?>">
    </FRAMESET>
</FRAMESET>
