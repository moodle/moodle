<?PHP  // $Id$

    require("../config.php");

    $file  = optional_param('file', "", PARAM_FILE);  // docs file to view straight
    $frame = optional_param('frame', "", PARAM_FILE); // docs file to view in frame
    $sub   = optional_param('sub', "", PARAM_CLEAN);  // sub-section (named anchor)

    if ($CFG->forcelogin) {
        require_login();
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
