<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles
    $nomoodlecookie = true;

    require_once("../../config.php");
    $themename = optional_param('themename', NULL, PARAM_SAFEDIR);

    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);

///
/// You can hardcode colours in this file if you
/// don't care about this.

?>
body {
    background-color:#FFFFFF;
}
p, a {
    font-size:small;
}

h1, h2, h3 {
    padding-left:0px;
    background-color:transparent;
    color:#555555;
}

h1 {
    font-size:1.7em; 
    margin:0.5em 0 0;
}

h2 {
    font-size:1.4em;
    margin:0.5em 0 0;
}

h3 {
    font-size:1.2em;
    margin:0.5em 0 0;
}


li {
    margin-bottom: 10px;
}

ul {
    margin-top: 10px;
}

.question {
    font-size: medium;
    font-weight: normal;
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
    margin-top: 0.5em;
    padding: 30px;
    height: auto;
    width: auto;
}

.spaced {
    margin-bottom: 30px;
}
