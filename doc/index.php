<?PHP  // $Id$

    require("../config.php");

    optional_variable($file, "");     // docs file to view
    optional_variable($frame, "");    // docs file to view
    optional_variable($sub, "");      // sub-section (named anchor)
    optional_variable($lang, "");     // override current language

    if ($CFG->forcelogin) {
        require_login();
    }

    if (!empty($lang)) {
        $SESSION->lang = $lang;
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

    if (! document_file($file, $include)) {
        error("Error 404 - File Not Found");
    }

    if ($include) {
        exit;
    }

?>

<head>
    <title><?php print_string("documentation")?></title>
</head>

<frameset rows="70,*">
    <frame name="top" src="top.php">
    <frameset cols="200,*">
        <frame name="contents" src="contents.php">
        <frame name="main" src="index.php?file=<?php echo "$file$sub"; ?>">
    </frameset>
</frameset>
