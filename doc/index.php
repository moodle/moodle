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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php print_string("documentation")?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php print_string("thischarset") ?>" />
</head>

<frameset rows="70,*">
    <frame name="top" src="top.php" />
    <frameset cols="200,*">
        <frame name="contents" src="contents.php" />
        <frame name="main" src="index.php?file=<?php echo "$file$sub"; ?>" />
    </frameset>
</frameset>
</html>
