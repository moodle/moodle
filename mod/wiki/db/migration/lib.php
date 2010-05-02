<?php
function wiki_ewiki_2_html($oldentry, $oldpage, $oldwiki) {
    global $CFG, $wiki_entry, $moodle_disable_camel_case, $ewiki_plugins, $ewiki_config, $moodle_format;

    $wiki_entry = $oldentry;

    $moodle_disable_camel_case = ($oldwiki->disablecamelcase == 1);

        // Block of dinamic ewiki defines
    wiki_set_define("EWIKI_NAME", $wiki_entry->pagename);
    wiki_set_define("EWIKI_DEFAULT_LANG", current_language());
    if ($moodle_disable_camel_case) {
        wiki_set_define("EWIKI_CHARS_L", "");
        wiki_set_define("EWIKI_CHARS_U", "");
    } else {
        wiki_set_define("EWIKI_CHARS_L", "a-z_µ¤$\337-\377");
        wiki_set_define("EWIKI_CHARS_U", "A-Z0-9\300-\336");
    }

    wiki_set_define("EWIKI_CHARS", wiki_get_define('EWIKI_CHARS_L') . wiki_get_define('EWIKI_CHARS_U'));

    require_once($CFG->dirroot . '/mod/wiki/db/migration/wiki/ewikimoodlelib.php');
    require_once($CFG->dirroot . '/mod/wiki/db/migration/wiki/ewiki/ewiki.php');

    if ($oldwiki->htmlmode == 0) {
        # No HTML
        $ewiki_config["htmlentities"] = array(); // HTML is managed by moodle
        $moodle_format = FORMAT_TEXT;
    }
    if ($oldwiki->htmlmode == 1) {
        # Safe HTML
        include_once($CFG->dirroot . "/mod/wiki/db/migration/wiki/ewiki/plugins/moodle/moodle_rescue_html.php");
        $moodle_format = FORMAT_HTML;
    }
    if ($oldwiki->htmlmode == 2) {
        # HTML Only
        $moodle_format = FORMAT_HTML;
        $ewiki_use_editor = 1;
        $ewiki_config["htmlentities"] = array(); // HTML is allowed
        $ewiki_config["wiki_link_regex"] = "\007 [!~]?(
                    \#?\[[^<>\[\]\n]+\] |
                    \^[-" .
            wiki_get_define('EWIKI_CHARS_U') . wiki_get_define('EWIKI_CHARS_L') . "]{3,} |
                    \b([\w]{3,}:)*([" .
            wiki_get_define('EWIKI_CHARS_U') . "]+[" . wiki_get_define('EWIKI_CHARS_L') . "]+){2,}\#?[\w\d]* |
                    \w[-_.+\w]+@(\w[-_\w]+[.])+\w{2,}   ) \007x";
    }

    $content = ewiki_format($oldpage->content);

    return format_text($content, $moodle_format);

}

function wiki_set_define($key, $value) {
    global $ewikidefines;

    $ewikidefines[$key] = $value;
}

function wiki_get_define($key) {
    global $ewikidefines;

    return $ewikidefines[$key];
}

