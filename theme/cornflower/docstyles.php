<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles

    if (!isset($themename)) {
        $themename = NULL;
    }

    $nomoodlecookie = true;
    require_once("../../config.php");
    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);

///
/// You can hardcode colours in this file if you
/// don't care about this.

?>
body {
    background-color:#FFFFFF;
}

h1, h2, h3 {
    background-color:transparent;
    color:#000000;
}

h1 {
    font-size: 2em; 
    margin: .67em 0;
}

h2 {
    font-size: 1.5em;
    margin: .75em 0;
}

h3 {
    font-size: 1.17em;
    margin: .83em 0;
}


li {
	margin-bottom: 10px;
}

ul {
	margin-top: 10px;
}

.question {
    font-size: medium;
    font-weight: bold;
    border: 1px dotted;
    padding: 10px;
    background-color: #EEEEEE;
}

.answer {
    font-size: medium;
    border: none;
    padding-left: 40px;
}

.normaltext {
	font-size: medium;
	border: none;
	margin-left: 30px;
}

.answercode {
    font-family: "Courier New", Courier, mono;
    font-size: small;
    border: none;
    padding-left: 60px;
}

.questionlink {
    font-size: medium;
    border: none;
    padding-left: 40px;
}

.examplecode {
	font-family: "Courier New", Courier, mono;
	font-size: small;
	border: thin dashed #999999;
	background-color: #FBFBFB;
	margin: auto;
	padding: 30px;
	height: auto;
	width: auto;
}

.spaced {
	margin-bottom: 30px;
}
