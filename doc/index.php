<?PHP  // $Id$

    require("../config.php");

    optional_variable($file, "");     // docs file to view
    optional_variable($frame, "");    // docs file to view
    optional_variable($sub, "");      // sub-section (named anchor)
    optional_variable($lang, "");     // override current language

    if (!empty($lang)) {
        $SESSION->doclang = $lang;
        save_session("SESSION");
    }

    if (!empty($sub)) {
        $sub = "#$sub";
    } else {
        $sub = "";
    }

    if (empty($file)) {
        $include = false;
        if (empty($frame)) {
            $file = "intro.html";
        } else {
            $file = $frame;
        }
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
        <FRAME NAME="main" SRC="index.php?file=<?PHP echo "$file$sub"; ?>">
    </FRAMESET>
</FRAMESET>
