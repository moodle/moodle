<?php

defined('MOODLE_INTERNAL') || die();

@define("EWIKI_VERSION", "R1.01d");

/*

  ErfurtWiki - an embedable, fast and user-friendly wiki engine
  ---------
  This is Public Domain (no license, no warranty); but feel free
  to redistribute it under GPL or anything else you like.

  http://erfurtwiki.sourceforge.net/
  Mario Salzer <mario*erphesfurt·de> and many others(tm)


  Use it from inside yoursite.php like that:
  <html><body>...
  <?php
     include("ewiki.php");
     echo ewiki_page();
  ?>

*/

#-- you could also establish a mysql connection in here, of course:
// mysql_connect(":/var/run/mysqld/mysqld.sock", "user", "pw")
// and mysql_query("USE mydatabase");


        #-------------------------------------------------------- config ---

        #-- I'm sorry for that, but all the @ annoy me
        error_reporting(0x0000377 & error_reporting());
#   error_reporting(E_ALL^E_NOTICE);

    #-- the position of your ewiki-wrapper script
    define("EWIKI_SCRIPT", "?id=");     # relative/absolute to docroot
#   define("EWIKI_SCRIPT_URL", "http://...?id=");       # absolute URL

        #-- change to your needs (site lang)
    //define("EWIKI_NAME", "ErfurtWiki");
    define("EWIKI_PAGE_INDEX", "ErfurtWiki");
    define("EWIKI_PAGE_NEWEST", "NewestPages");
    define("EWIKI_PAGE_SEARCH", "SearchPages");
    define("EWIKI_PAGE_HITS", "MostVisitedPages");
    define("EWIKI_PAGE_VERSIONS", "MostOftenChangedPages");
    define("EWIKI_PAGE_UPDATES", "UpdatedPages");

    #-- default settings are good settings - most often ;)
        #- look & feel
    define("EWIKI_PRINT_TITLE", 1);     # <h2>WikiPageName</h2> on top
    define("EWIKI_SPLIT_TITLE", 0);     # <h2>Wiki Page Name</h2>
    define("EWIKI_CONTROL_LINE", 1);    # EditThisPage-link at bottom
    define("EWIKI_LIST_LIMIT", 20);     # listing limit
        #- behaviour
    define("EWIKI_AUTO_EDIT", 1);       # edit box for non-existent pages
    define("EWIKI_EDIT_REDIRECT", 1);   # redirect after edit save
    define("EWIKI_DEFAULT_ACTION", "view"); # (keep!)
    define("EWIKI_CASE_INSENSITIVE", 1);    # wikilink case sensitivity
    define("EWIKI_HIT_COUNTING", 1);
    define("UNIX_MILLENNIUM", 1000000000);
        #- rendering
    define("EWIKI_ALLOW_HTML", 0);      # often a very bad idea
    define("EWIKI_HTML_CHARS", 1);      # allows for &#200;
    define("EWIKI_ESCAPE_AT", 1);       # "@" -> "&#x40;"
        #- http/urls
    define("EWIKI_HTTP_HEADERS", 1);    # most often a good thing
    define("EWIKI_NO_CACHE", 1);        # browser+proxy shall not cache
    define("EWIKI_URLENCODE", 1);       # disable when _USE_PATH_INFO
    define("EWIKI_URLDECODE", 1);
    define("EWIKI_USE_PATH_INFO", 1  &&!strstr($_SERVER["SERVER_SOFTWARE"],"Apache"));
    define("EWIKI_USE_ACTION_PARAM", 1);
    define("EWIKI_ACTION_SEP_CHAR", "/");
    define("EWIKI_UP_PAGENUM", "n");    # _UP_ means "url parameter"
    define("EWIKI_UP_PAGEEND", "e");
    define("EWIKI_UP_BINARY", "binary");
    define("EWIKI_UP_UPLOAD", "upload");
        #- other stuff
        //define("EWIKI_DEFAULT_LANG", "en");
        define("EWIKI_CHARSET", "UTF-8");
    #- user permissions
    define("EWIKI_PROTECTED_MODE", 0);  # disable funcs + require auth
    define("EWIKI_PROTECTED_MODE_HIDING", 0);  # hides disallowed actions
    define("EWIKI_AUTH_DEFAULT_RING", 3);   # 0=root 1=priv 2=user 3=view
    define("EWIKI_AUTO_LOGIN", 1);      # [auth_query] on startup

    #-- allowed WikiPageNameCharacters

#### BEGIN MOODLE CHANGES - to remove auto-camelcase linking.
//    global $moodle_disable_camel_case;
//    if ($moodle_disable_camel_case) {
//        define("EWIKI_CHARS_L", "");
//        define("EWIKI_CHARS_U", "");
//    }
//    else {
//#### END MOODLE CHANGES
//
//    define("EWIKI_CHARS_L", "a-z_µ¤$\337-\377");
//    define("EWIKI_CHARS_U", "A-Z0-9\300-\336");
//
//#### BEGIN MOODLE CHANGES
//    }
//#### END MOODLE CHANGES
//
//    define("EWIKI_CHARS", EWIKI_CHARS_L.EWIKI_CHARS_U);
//
// COMMENTED BY PIGUI BECOUSE OF MIGRATION

        #-- database
    define("EWIKI_DB_TABLE_NAME", "ewiki");      # MySQL / ADOdb
    define("EWIKI_DBFILES_DIRECTORY", "/tmp");   # see "db_flat_files.php"
    define("EWIKI_DBA", "/tmp/ewiki.dba");       # see "db_dba.php"
    define("EWIKI_DBQUERY_BUFFER", 512*1024);    # 512K
    define("EWIKI_INIT_PAGES", "./init-pages");  # for initialization

    define("EWIKI_DB_F_TEXT", 1<<0);
    define("EWIKI_DB_F_BINARY", 1<<1);
    define("EWIKI_DB_F_DISABLED", 1<<2);
    define("EWIKI_DB_F_HTML", 1<<3);
    define("EWIKI_DB_F_READONLY", 1<<4);
    define("EWIKI_DB_F_WRITEABLE", 1<<5);
    define("EWIKI_DB_F_APPENDONLY", 1<<6);  #nyi
    define("EWIKI_DB_F_SYSTEM", 1<<7);
    define("EWIKI_DB_F_PART", 1<<8);
    define("EWIKI_DB_F_TYPE", EWIKI_DB_F_TEXT | EWIKI_DB_F_BINARY | EWIKI_DB_F_DISABLED | EWIKI_DB_F_SYSTEM | EWIKI_DB_F_PART);
    define("EWIKI_DB_F_ACCESS", EWIKI_DB_F_READONLY | EWIKI_DB_F_WRITEABLE | EWIKI_DB_F_APPENDONLY);
    define("EWIKI_DB_F_COPYMASK", EWIKI_DB_F_TYPE | EWIKI_DB_F_ACCESS);

    define("EWIKI_DBFILES_NLR", '\\n');
    define("EWIKI_DBFILES_ENCODE", 0 || (DIRECTORY_SEPARATOR != "/"));
    define("EWIKI_DBFILES_GZLEVEL", "2");

    #-- internal
    define("EWIKI_ADDPARAMDELIM", (strstr(EWIKI_SCRIPT,"?") ? "&" : "?"));

    #-- binary content (images)
    define("EWIKI_SCRIPT_BINARY", /*"/binary.php?binary="*/  ltrim(strtok(" ".EWIKI_SCRIPT,"?"))."?".EWIKI_UP_BINARY."="  );
    define("EWIKI_CACHE_IMAGES", 1  &&!headers_sent());
    define("EWIKI_IMAGE_MAXSIZE", 64 *1024);
    define("EWIKI_IMAGE_MAXWIDTH", 3072);
    define("EWIKI_IMAGE_MAXHEIGHT", 2048);
    define("EWIKI_IMAGE_MAXALLOC", 1<<19);
    define("EWIKI_IMAGE_RESIZE", 1);
    define("EWIKI_IMAGE_ACCEPT", "image/jpeg,image/png,image/gif,application/x-shockwave-flash");
    define("EWIKI_IDF_INTERNAL", "internal://");
    define("EWIKI_ACCEPT_BINARY", 0);   # for arbitrary binary data files

    #-- misc
        define("EWIKI_TMP", $_SERVER["TEMP"] ? $_SERVER["TEMP"] : "/tmp");
    define("EWIKI_LOGLEVEL", -1);       # 0=error 1=warn 2=info 3=debug
    define("EWIKI_LOGFILE", "/tmp/ewiki.log");

    #-- plugins (tasks mapped to function names)
    $ewiki_plugins["database"][] = "ewiki_database_mysql";
    $ewiki_plugins["edit_preview"][] = "ewiki_page_edit_preview";
    $ewiki_plugins["render"][] = "ewiki_format";
    $ewiki_plugins["init"][-5] = "ewiki_localization";
    $ewiki_plugins["init"][-1] = "ewiki_binary";
        $ewiki_plugins["handler"][-105] = "ewiki_eventually_initialize";
        $ewiki_plugins["handler"][] = "ewiki_intermap_walking";
    $ewiki_plugins["view_append"][-1] = "ewiki_control_links";
        $ewiki_plugins["view_final"][-1] = "ewiki_add_title";
        $ewiki_plugins["page_final"][] = "ewiki_http_headers";
        $ewiki_plugins["page_final"][99115115] = "ewiki_page_css_container";
    $ewiki_plugins["edit_form_final"][] = "ewiki_page_edit_form_final_imgupload";
        $ewiki_plugins["format_block"]["pre"][] = "ewiki_format_pre";
        $ewiki_plugins["format_block"]["code"][] = "ewiki_format_pre";
        $ewiki_plugins["format_block"]["htm"][] = "ewiki_format_html";
        $ewiki_plugins["format_block"]["html"][] = "ewiki_format_html";
        $ewiki_plugins["format_block"]["comment"][] = "ewiki_format_comment";


    #-- internal pages
    $ewiki_plugins["page"][EWIKI_PAGE_NEWEST] = "ewiki_page_newest";
    $ewiki_plugins["page"][EWIKI_PAGE_SEARCH] = "ewiki_page_search";
    if (EWIKI_HIT_COUNTING) $ewiki_plugins["page"][EWIKI_PAGE_HITS] = "ewiki_page_hits";
    $ewiki_plugins["page"][EWIKI_PAGE_VERSIONS] = "ewiki_page_versions";
    $ewiki_plugins["page"][EWIKI_PAGE_UPDATES] = "ewiki_page_updates";

    #-- page actions
    $ewiki_plugins["action"]["edit"] = "ewiki_page_edit";
    $ewiki_plugins["action_always"]["links"] = "ewiki_page_links";
    $ewiki_plugins["action"]["info"] = "ewiki_page_info";
    $ewiki_plugins["action"]["view"] = "ewiki_page_view";

    #-- helper vars ---------------------------------------------------
    $ewiki_config["idf"]["url"] = array("http://", "mailto:", "internal://", "ftp://", "https://", "irc://", "telnet://", "news://", "chrome://", "file://", "gopher://", "httpz://");
    $ewiki_config["idf"]["img"] = array(".jpeg", ".png", ".jpg", ".gif", ".j2k");
    $ewiki_config["idf"]["obj"] = array(".swf", ".svg");

    #-- entitle actions
    $ewiki_config["action_links"]["view"] = @array_merge(array(
        "edit" => "EDITTHISPAGE",   # ewiki_t() is called on these
        "links" => "BACKLINKS",
        "info" => "PAGEHISTORY",
        "like" => "LIKEPAGES",
    ), @$ewiki_config["action_links"]["view"]
        );
    $ewiki_config["action_links"]["info"] = @array_merge(array(
        "view" => "browse",
        "edit" => "fetchback",
    ), @$ewiki_config["action_links"]["info"]
        );

        #-- variable configuration settings (go into '$ewiki_config')
        $ewiki_config_DEFAULTSTMP = array(
           "edit_thank_you" => 1,
           "edit_box_size" => "70x15",
           "print_title" => EWIKI_PRINT_TITLE,
           "split_title" => EWIKI_SPLIT_TITLE,
           "control_line" => EWIKI_CONTROL_LINE,
           "list_limit" => EWIKI_LIST_LIMIT,
           "script" => EWIKI_SCRIPT,
           "script_url" => (defined("EWIKI_SCRIPT_URL")?EWIKI_SCRIPT_URL:NULL),
           "script_binary" => EWIKI_SCRIPT_BINARY,
    #-- heart of the wiki -- don't try to read this! ;)

           "wiki_pre_scan_regex" => '/
        (?<![~!])
        ((?:(?:\w+:)*['.wiki_get_define('EWIKI_CHARS_U').']+['.wiki_get_define('EWIKI_CHARS_L').']+){2,}[\w\d]*)
        |\^([-'.wiki_get_define('EWIKI_CHARS_L').wiki_get_define('EWIKI_CHARS_U').']{3,})
        |\[ (?:"[^\]\"]+" | \s+ | [^:\]#]+\|)*  ([^\|\"\[\]\#]+)  (?:\s+ | "[^\]\"]+")* [\]\#]
        |(\w{3,9}:\/\/[^?#\s\[\]\'\"\)\,<]+)    /x',

           "wiki_link_regex" => "\007 [!~]?(
        \#?\[[^<>\[\]\n]+\] |
        \^[-".wiki_get_define('EWIKI_CHARS_U').wiki_get_define('EWIKI_CHARS_L')."]{3,} |
        \b([\w]{3,}:)*([".wiki_get_define('EWIKI_CHARS_U')."]+[".wiki_get_define('EWIKI_CHARS_L')."]+){2,}\#?[\w\d]* |
        ([a-z]{2,9}://|mailto:)[^\s\[\]\'\"\)\,<]+ |
        \w[-_.+\w]+@(\w[-_\w]+[.])+\w{2,}   ) \007x",

    #-- rendering ruleset
           "wm_indent" => '',
           "wm_table_defaults" => 'cellpadding="2" border="1" cellspacing="0"',
           "wm_whole_line" => array(),
           "htmlentities" => array(
        "&" => "&amp;",
        ">" => "&gt;",
        "<" => "&lt;",
           ),
           "wm_source" => array(
        "%%%" => "<br />",
        "\t" => "        ",
        "\n;:" => "\n      ",   # workaround, replaces the old ;:
           ),
           "wm_list" => array(
        "-" => array('ul type="square"', "", "li"),
        "*" => array('ul type="circle"', "", "li"),
        "#" => array("ol", "", "li"),
        ":" => array("dl", "dt", "dd"),
    #<out># ";" => array("dl", "dt", "dd"),
           ),
           "wm_style" => array(
        "'''''" => array("<b><i>", "</i></b>"),
        "'''" => array("<b>", "</b>"),
        "___" => array("<i><b>", "</b></i>"),
        "''" => array("<em>", "</em>"),
        "__" => array("<strong>", "</strong>"),
        "^^" => array("<sup>", "</sup>"),
        "==" => array("<tt>", "</tt>"),
    #<off># "***" => array("<b><i>", "</i></b>"),
    #<off># "###" => array("<big><b>", "</b></big>"),
        "**" => array("<b>", "</b>"),
        "##" => array("<big>", "</big>"),
        "µµ" => array("<small>", "</small>"),
           ),
           "wm_start_end" => array(
           ),
    #-- rendering plugins
           "format_block" => array(
        "html" => array("&lt;html&gt;", "&lt;/html&gt;", "html", 0x0000),
        "htm" => array("&lt;htm&gt;", "&lt;/htm&gt;", "html", 0x0003),
        "code" => array("&lt;code&gt;", "&lt;/code&gt;", false, 0x0000),
        "pre" => array("&lt;pre&gt;", "&lt;/pre&gt;", false, 0x003F),
        "comment" => array("\n&lt;!--", "--&gt;", false, 0x0030),
        #  "verbatim" => array("&lt;verbatim&gt;", "&lt;/verbatim&gt;", false, 0x0000),
           ),
           "format_params" => array(
        "scan_links" => 1,
        "html" => EWIKI_ALLOW_HTML,
        "mpi" => 1,
           ),
        );
        foreach ($ewiki_config_DEFAULTSTMP as $set => $val) {
           if (!isset($ewiki_config[$set])) {
              $ewiki_config[$set] = $val;
           }
           elseif (is_array($val)) foreach ($val as $vali=>$valv) {
              if (is_int($vali)) {
                 $ewiki_config[$set][] = $valv;
              }
              elseif (!isset($ewiki_config[$set][$vali])) {
                 $ewiki_config[$set][$vali] = $valv;
              }
           }
        }
        $ewiki_config_DEFAULTSTMP = $valv = $vali = $val = NULL;

    #-- init stuff, autostarted parts
    ksort($ewiki_plugins["init"]);
    if ($pf_a = $ewiki_plugins["init"]) foreach ($pf_a as $pf) {
           // Binary Handling starts here
           #### MOODLE CHANGE TO BE COMPATIBLE WITH PHP 4.1
           #if(headers_sent($file,$line)) {
           #  print_error('headersent');
           /*if(headers_sent()) {
             print_error('headersent');
           }*/
           $pf($GLOBALS);
        }
    unset($ewiki_plugins["init"]);

    #-- text  (never remove the "C" or "en" sections!)
        #
    $ewiki_t["C"] = @array_merge(@$ewiki_t["C"], array(
           "DATE" => "%a, %d %b %G %T %Z",
       "EDIT_TEXTAREA_RESIZE_JS" => '<a href="javascript:ewiki_enlarge()" style="text-decoration:none">+</a><script type="text/javascript"><!--'."\n".'function ewiki_enlarge() {var ta=document.getElementById("ewiki_content");ta.style.width=((ta.cols*=1.1)*10).toString()+"px";ta.style.height=((ta.rows*=1.1)*30).toString()+"px";}'."\n".'//--></script>',
        ));
        #
    $ewiki_t["en"] = @array_merge(@$ewiki_t["en"], array(
       "EDITTHISPAGE" => "EditThisPage",
           "APPENDTOPAGE" => "Add to",
       "BACKLINKS" => "BackLinks",
       "PAGESLINKINGTO" => "Pages linking to \$title",
       "PAGEHISTORY" => "PageInfo",
       "INFOABOUTPAGE" => "Information about page",
       "LIKEPAGES" => "Pages like this",
       "NEWESTPAGES" => "Newest Pages",
       "LASTCHANGED" => "last changed on %c",
       "DOESNOTEXIST" => "This page does not yet exist, please click on EditThisPage if you'd like to create it.",
       "DISABLEDPAGE" => "This page is currently not available.",
       "ERRVERSIONSAVE" => "Sorry, while you edited this page someone else
        did already save a changed version. Please go back to the
        previous screen and copy your changes to your computers
        clipboard to insert it again after you reload the edit
        screen.",
       "ERRORSAVING" => "An error occoured while saving your changes. Please try again.",
       "THANKSFORCONTRIBUTION" => "Thank you for your contribution!",
       "CANNOTCHANGEPAGE" => "This page cannot be changed.",
       "OLDVERCOMEBACK" => "Make this old version come back to replace the current one",
       "PREVIEW" => "Preview",
       "SAVE" => "Save",
       "CANCEL_EDIT" => "CancelEditing",
       "UPLOAD_PICTURE_BUTTON" => "upload picture &gt;&gt;&gt;",
       "EDIT_FORM_1" => "<a href=\"".EWIKI_SCRIPT."GoodStyle\">GoodStyle</a> is to
        write what comes to your mind. Don't care about how it
        looks too much now. You can add <a href=\"".EWIKI_SCRIPT."WikiMarkup\">WikiMarkup</a>
        also later if you think it is necessary.<br />",
       "EDIT_FORM_2" => "<br />Please do not write things, which may make other
        people angry. And please keep in mind that you are not all that
        anonymous in the internet (find out more about your computers
        '<a href=\"http://google.com/search?q=my+computers+IP+address\">IP address</a>' at Google).",
       "BIN_IMGTOOLARGE" => "Image file is too large!",
       "BIN_NOIMG" => "This is no image file (inacceptable file format)!",
       "FORBIDDEN" => "You are not authorized to access this page.",
    ));
        #
        $ewiki_t["es"] = @array_merge(@$ewiki_t["es"], array(
           "EDITTHISPAGE" => "EditarEstaPágina",
           "BACKLINKS" => "EnlacesInversos",
           "PAGESLINKINGTO" => "Páginas enlazando \$title",
           "PAGEHISTORY" => "InfoPágina",
           "INFOABOUTPAGE" => "Información sobre la página",
           "LIKEPAGES" => "Páginas como esta",
           "NEWESTPAGES" => "Páginas más nuevas",
           "LASTCHANGED" => "última modificación %d/%m/%Y a las %H:%M",
           "DOESNOTEXIST" => "Esta página aún no existe, por favor eliga EditarEstaPágina si desea crearla.",
           "DISABLEDPAGE" => "Esta página no está disponible en este momento.",
           "ERRVERSIONSAVE" => "Disculpe, mientras editaba esta página alguién más
        salvó una versión modificada. Por favor regrese a
        a la pantalla anterior y copie sus cambios a su computador
        para insertalos nuevamente después de que cargue
        la pantalla de edición.",
           "ERRORSAVING" => "Ocurrió un error mientras se salvavan sus cambios. Por favor intente de nuevo.",
           "THANKSFORCONTRIBUTION" => "Gracias por su contribución!",
           "CANNOTCHANGEPAGE" => "Esta página no puede ser modificada.",
           "OLDVERCOMEBACK" => "Hacer que esta versión antigua regrese a remplazar la actual",
           "PREVIEW" => "Previsualizar",
           "SAVE" => "Salvar",
           "CANCEL_EDIT" => "CancelarEdición",
           "UPLOAD_PICTURE_BUTTON" => "subir gráfica &gt;&gt;&gt;",
           "EDIT_FORM_1" => "<a href=\"".EWIKI_SCRIPT."BuenEstilo\">BuenEstilo</a> es
        escribir lo que viene a su mente. No se preocupe mucho
        por la apariencia. También puede agregar <a href=\"".EWIKI_SCRIPT."ReglasDeMarcadoWiki\">ReglasDeMarcadoWiki</a>
        más adelante si piensa que es necesario.<br />",
           "EDIT_FORM_2" => "<br />Por favor no escriba cosas, que puedan
        enfadar a otras personas. Y por favor tenga en mente que
        usted no es del todo anónimo en Internet
        (encuentre más sobre
        '<a href=\"http://google.com/search?q=my+computers+IP+address\">IP address</a>' de su computador con Google).",
           "BIN_IMGTOOLARGE" => "¡La gráfica es demasiado grande!",
           "BIN_NOIMG" => "¡No es un archivo con una gráfica (formato de archivo inaceptable)!",
           "FORBIDDEN" => "No está autorizado para acceder a esta página.",
        ));
        #
    $ewiki_t["de"] = @array_merge(@$ewiki_t["de"], array(
       "EDITTHISPAGE" => "DieseSeiteÄndern",
           "APPENDTOPAGE" => "Ergänze",
       "BACKLINKS" => "ZurückLinks",
       "PAGESLINKINGTO" => "Verweise zur Seite \$title",
       "PAGEHISTORY" => "SeitenInfo",
       "INFOABOUTPAGE" => "Informationen über Seite",
       "LIKEPAGES" => "Ähnliche Seiten",
       "NEWESTPAGES" => "Neueste Seiten",
       "LASTCHANGED" => "zuletzt geändert am %d.%m.%Y um %H:%M",
       "DISABLEDPAGE" => "Diese Seite kann momentan nicht angezeigt werden.",
       "ERRVERSIONSAVE" => "Entschuldige, aber während Du an der Seite
        gearbeitet hast, hat bereits jemand anders eine geänderte
        Fassung gespeichert. Damit nichts verloren geht, browse bitte
        zurück und speichere Deine Änderungen in der Zwischenablage
        (Bearbeiten->Kopieren) um sie dann wieder an der richtigen
        Stelle einzufügen, nachdem du die EditBoxSeite nocheinmal
        geladen hast.<br />
        Vielen Dank für Deine Mühe.",
       "ERRORSAVING" => "Beim Abspeichern ist ein Fehler aufgetreten. Bitte versuche es erneut.",
       "THANKSFORCONTRIBUTION" => "Vielen Dank für Deinen Beitrag!",
       "CANNOTCHANGEPAGE" => "Diese Seite kann nicht geändert werden.",
       "OLDVERCOMEBACK" => "Diese alte Version der Seite wieder zur Aktuellen machen",
       "PREVIEW" => "Vorschau",
       "SAVE" => "Speichern",
       "CANCEL_EDIT" => "ÄnderungenVerwerfen",
       "UPLOAD_PICTURE_BUTTON" => "Bild hochladen &gt;&gt;&gt;",
       "EDIT_FORM_1" => "<a href=\"".EWIKI_SCRIPT."GuterStil\">GuterStil</a> ist es,
        ganz einfach das zu schreiben, was einem gerade in den
        Sinn kommt. Du solltest dich jetzt noch nicht so sehr
        darum kümmern, wie die Seite aussieht. Du kannst später
        immernoch zurückkommen und den Text mit <a href=\"".EWIKI_SCRIPT."FormatierungsRegeln\">WikiTextFormatierungsRegeln</a>
        aufputschen.<br />",
       "EDIT_FORM_2" => "<br />Bitte schreib keine Dinge, die andere Leute
        verärgern könnten. Und bedenke auch, daß es schnell auf
        dich zurückfallen kann wenn du verschiedene andere Dinge sagst (mehr Informationen zur
        '<a href=\"http://google.de/search?q=computer+IP+adresse\">IP Adresse</a>'
        deines Computers findest du bei Google).",
    ));

    #-- InterWiki:Links
    $ewiki_config["interwiki"] = @array_merge(
    @$ewiki_config["interwiki"],
    array(
           "javascript" => "",  # this actually protects from javascript: links
           "url" => "",
#          "self" => "this",
           "this" => EWIKI_SCRIPT,  # better was absolute _URL to ewiki wrapper
           "jump" => ""
    ));
    // BEGIN MOODLE CHANGES - disable interwiki liks by default
    // can be enabled with $CFG->wiki_allow_interwiki = true . MDL-19460
    global $CFG;
    if (!empty($CFG->wiki_allow_interwiki)) {
       $ewiki_config["interwiki"] = @array_merge(
           $ewiki_config["interwiki"],
           array (
               "ErfurtWiki" => "http://erfurtwiki.sourceforge.net/?id=",
               "InterWiki" => "InterWikiSearch",
               "InterWikiSearch" => "http://sunir.org/apps/meta.pl?",
               "Wiki" => "WardsWiki",
               "WardsWiki" => "http://www.c2.com/cgi/wiki?",
               "WikiFind" => "http://c2.com/cgi/wiki?FindPage&amp;value=",
               "WikiPedia" => "http://www.wikipedia.com/wiki.cgi?",
               "MeatBall" => "MeatballWiki",
               "MeatballWiki" => "http://www.usemod.com/cgi-bin/mb.pl?",
               "UseMod"       => "http://www.usemod.com/cgi-bin/wiki.pl?",
               "PhpWiki" => "http://phpwiki.sourceforge.net/phpwiki/index.php3?",
               "LinuxWiki" => "http://linuxwiki.de/",
               "OpenWiki" => "http://openwiki.com/?",
               "Tavi" => "http://andstuff.org/tavi/",
               "TWiki" => "http://twiki.sourceforge.net/cgi-bin/view/",
               "MoinMoin" => "http://www.purl.net/wiki/moin/",
               "Google" => "http://google.com/search?q=",
               "ISBN" => "http://www.amazon.com/exec/obidos/ISBN=",
               "icq" => "http://www.icq.com/"
    ));
    }
    // END MOODLE CHANGES





#-------------------------------------------------------------------- main ---

/*  this is the main function, which you should preferrably call to
    integrate the ewiki into your web site; it chains to most other
    parts and plugins (including the edit box);
    if you do not supply the requested pages "$id" we will fetch it
    from the pre-defined possible URL parameters.
*/
function ewiki_page($id=false) {

   global $ewiki_links, $ewiki_plugins, $ewiki_ring, $ewiki_t, $ewiki_errmsg;
   #-- output var
   $o = "";

   #-- selected page
    $action  = optional_param('action', EWIKI_DEFAULT_ACTION);
    $content = optional_param('content', false);
    $version = optional_param('version', false);

   if (!strlen($id)) {
      $id = ewiki_id();
   }
   $id = format_string($id,true);
   #-- page action
   if ($delim = strpos($id, EWIKI_ACTION_SEP_CHAR)) {
      $action = substr($id, 0, $delim);
      $id = substr($id, $delim + 1);
   }
   elseif (!EWIKI_USE_ACTION_PARAM) {
      $action = EWIKI_DEFAULT_ACTION;
   }
   $GLOBALS["ewiki_id"] = $id;
   $GLOBALS["ewiki_title"] = ewiki_split_title($id);
   $GLOBALS["ewiki_action"] = $action;

   #-- fetch from db
   $dquery = array(
      "id" => $id
   );
   if (!$content && ($dquery["version"] = $version)) {
      $dquery["forced_version"] = $dquery["version"];
   }
   $data = @array_merge($dquery, ewiki_database("GET", $dquery));

   #-- stop here if page is not marked as _TEXT,
   #   perform authentication then, and let only administrators proceed
   if (!empty($data["flags"]) && (($data["flags"] & EWIKI_DB_F_TYPE) != EWIKI_DB_F_TEXT)) {
      if (($data["flags"] & EWIKI_DB_F_BINARY) && ($pf = $ewiki_plugins["handler_binary"][0])) {
         return($pf($id, $data, $action)); //_BINARY entries handled separately
      }
      elseif (!EWIKI_PROTECTED_MODE || !ewiki_auth($id, $data, $action, 0, 1) && ($ewiki_ring!=0)) {
         return(ewiki_t("DISABLEDPAGE"));
      }
   }

   #-- pre-check if actions exist
   $pf_page = ewiki_array($ewiki_plugins["page"], $id);

   #-- edit <form> for non-existent pages
   if (($action==EWIKI_DEFAULT_ACTION) && empty($data["content"]) && empty($pf_page)) {
      if (EWIKI_AUTO_EDIT) {
         $action = "edit";
      }
      else {
         $data["content"] = ewiki_t("DOESNOTEXIST");
      }
   }
   #-- more initialization
   if ($pf_a = @$ewiki_plugins["page_init"]) {
      ksort($pf_a);
      foreach ($pf_a as $pf) {
         $o .= $pf($id, $data, $action);
      }
      unset($ewiki_plugins["page_init"]);
   }
   $pf_page = ewiki_array($ewiki_plugins["page"], $id);

   #-- require auth
   if (EWIKI_PROTECTED_MODE) {
      if (!ewiki_auth($id, $data, $action, $ring=false, $force=EWIKI_AUTO_LOGIN)) {
         return($o.=$ewiki_errmsg);
      }
   }

   #-- handlers
   $handler_o = "";
   if ($pf_a = @$ewiki_plugins["handler"]) {
      ksort($pf_a);
      foreach ($pf_a as $pf) {
         if ($handler_o = $pf($id, $data, $action)) { break; }
   }  }

   #-- finished by handler
   if ($handler_o) {
      $o .= $handler_o;
   }
   #-- actions that also work for static and internal pages
   elseif (($pf = @$ewiki_plugins["action_always"][$action]) && function_exists($pf)) {
      $o .= $pf($id, $data, $action);
   }
   #-- internal pages
   elseif ($pf_page && function_exists($pf_page)) {
      $o .= $pf_page($id, $data, $action);
   }
   #-- page actions
   else {
      $pf = @$ewiki_plugins["action"][$action];

      #-- fallback to "view" action
      if (empty($pf) || !function_exists($pf)) {

         $pf = "ewiki_page_view";
         $action = "view";     // we could also allow different (this is a
         // catch-all) view variants, but this would lead to some problems
      }

      $o .= $pf($id, $data, $action);
   }

   #-- error instead of page?
   if (empty($o) && $ewiki_errmsg) {
      $o = $ewiki_errmsg;
   }

   #-- html post processing
   if ($pf_a = $ewiki_plugins["page_final"]) {
      ksort($pf_a);
      foreach ($pf_a as $pf) {
         if ($action == 'edit' and $pf == 'ewiki_html_tag_balancer') {
            continue; // balancer breaks htmlarea buttons
         }
         $pf($o, $id, $data, $action);
      }
   }

   (EWIKI_ESCAPE_AT) && ($o = str_replace("@", "&#x40;", $o));

   return($o);
}



#-- HTTP meta headers
function ewiki_http_headers(&$o, $id, &$data, $action) {
   global $ewiki_t;
   if (EWIKI_HTTP_HEADERS && !headers_sent()) {
      if (!empty($data)) {
         if ($uu = @$data["id"]) @header('Content-Disposition: inline; filename="' . urlencode($uu) . '.html"');
         if ($uu = @$data["version"]) @header('Content-Version: ' . $uu);
         if ($uu = @$data["lastmodified"]) @header('Last-Modified: ' . gmstrftime($ewiki_t["C"]["DATE"], $uu));
      }
      if (EWIKI_NO_CACHE) {
         header('Expires: ' . gmstrftime($ewiki_t["C"]["DATE"], UNIX_MILLENNIUM));
         header('Pragma: no-cache');
         header('Cache-Control: no-cache, private, must-revalidate');
         # change to "C-C: cache, must-revalidate" ??
         # private only for authenticated users / _PROT_MODE
      }
      #-- ETag
      if ($data["version"] && ($etag=ewiki_etag($data)) || ($etag=md5($o))) {
         $weak = "W/" . urlencode($id) . "." . $data["version"];
         header("ETag: \"$etag\"");     ###, \"$weak\"");
      }
   }
}
function ewiki_etag(&$data) {
   return(  urlencode($data["id"]) . ":" . dechex($data["version"]) . ":ewiki:" .
            dechex(crc32($data["content"]) & 0x7FFFBFFF)  );
}



#-- encloses whole page output with a descriptive <div>
function ewiki_page_css_container(&$o, &$id, &$data, &$action) {
   $o = "<div class=\"wiki $action "
      . strtr($id, ' ./ --_!"§$%&()=?²³{[]}`+#*;:,<>| @µöäüÖÄÜß¤^°«»\'\\',
                   '-  -----------------------------------------------')
      . "\">\n"
      . $o . "\n</div>\n";
}



function ewiki_split_title ($id='', $split=EWIKI_SPLIT_TITLE, $entities=1) {
   strlen($id) or ($id = $GLOBALS["ewiki_id"]);
   if ($split) {
      $id = preg_replace("/([".wiki_get_define('EWIKI_CHARS_L')."])([".wiki_get_define('EWIKI_CHARS_U')."]+)/", "$1 $2", $id);
   }
   return($entities ? s($id) : $id);
}



function ewiki_add_title(&$html, $id, &$data, $action, $go_action="links") {
   $html = ewiki_make_title($id, '', 1, $action, $go_action) . $html;
}


function ewiki_make_title($id='', $title='', $class=3, $action="view", $go_action="links", $may_split=1) {

   global $ewiki_config, $ewiki_plugins, $ewiki_title, $ewiki_id;

   #-- advanced handler
   if ($pf = @$ewiki_plugins["make_title"][0]) {
      return($pf($title, $class, $action, $go_action, $may_split));
   }
   #-- disabled
   elseif (!$ewiki_config["print_title"]) {
      return("");
   }

   #-- get id
   if (empty($id)) {
      $id = $ewiki_id;
   }

   #-- get title
   if (!strlen($title)) {
      $title = $ewiki_title;  // already in &html; format
   }
   elseif ($ewiki_config["split_title"] && $may_split) {
      $title = ewiki_split_title($title, $ewiki_config["split_title"], 0&($title!=$ewiki_title));
   }
   else {
      $title = s($title);
   }

   #-- title mangling
   if ($pf_a = @$ewiki_plugins["title_transform"]) {
      foreach ($pf_a as $pf) { $pf($id, $title, $go_action); }
   }

   #-- clickable link or simple headline
   if ($class <= $ewiki_config["print_title"]) {
      if ($uu = @$ewiki_config["link_title_action"][$action]) {
         $go_action = $uu;
      }
      if ($uu = @$ewiki_config["link_title_url"]) {
         $href = $uu;
         unset($ewiki_config["link_title_url"]);
      }
      else {
         $href = ewiki_script($go_action, $id);
      }
      $o = '<a href="' . $href . '">' . ($title) . '</a>';
   }
   else {
      $o = $title;
   }

   return('<h2 class="page title">' . $o . '</h2>'."\n");
}




function ewiki_page_view($id, &$data, $action, $all=1) {

   global $ewiki_plugins, $ewiki_config;
   $o = "";

   $thanks = optional_param('thankyou', '', PARAM_CLEAN);

   #-- render requested wiki page  <-- goal !!!
   $render_args = array(
      "scan_links" => 1,
      "html" => (EWIKI_ALLOW_HTML||(@$data["flags"]&EWIKI_DB_F_HTML)),
   );
   $o .= $ewiki_plugins["render"][0] ($data["content"], $render_args);
   if (!$all) {
      return($o);
   }
   #### MOODLE CHANGE
   /// Add Moodle filters to text porion of wiki.
   global $moodle_format;   // from wiki/view.php
   $o = format_text($o, $moodle_format);
   $o.= "<br /><br />";

   #-- control line + other per-page info stuff
   if ($pf_a = $ewiki_plugins["view_append"]) {
      ksort($pf_a);
      foreach ($pf_a as $n => $pf) { $o .= $pf($id, $data, $action); }
   }
   if ($pf_a = $ewiki_plugins["view_final"]) {
      ksort($pf_a);
      foreach ($pf_a as $n => $pf) { $pf($o, $id, $data, $action); }
   }

   if (!empty($thanks) && $ewiki_config["edit_thank_you"]) {
      $o = ewiki_t("THANKSFORCONTRIBUTION") . $o;
   }


   if (EWIKI_HIT_COUNTING) {
      ewiki_database("HIT", $data);
   }

   return($o);
}




#-------------------------------------------------------------------- util ---


/*  retrieves "$id/$action" string from URL / QueryString / PathInfo,
    change this in conjunction with ewiki_script() to customize your URLs
    further whenever desired
*/
function ewiki_id() {
   ($id = optional_param("id", '', PARAM_CLEAN)) or
   ($id = optional_param("name", '', PARAM_CLEAN)) or
   ($id = optional_param("page", '', PARAM_CLEAN)) or
   ($id = optional_param("file", '', PARAM_CLEAN)) or
   (EWIKI_USE_PATH_INFO) and ($id = ltrim(@$_SERVER["PATH_INFO"], "/")) or
   (!isset($_REQUEST["id"])) and ($id = trim(strtok($_SERVER["QUERY_STRING"], "&")));
   if (!strlen($id) || ($id=="id=")) {
      $id = EWIKI_PAGE_INDEX;
   }
   (EWIKI_URLDECODE) && ($id = urldecode($id));
   return($id);
}




/*  replaces EWIKI_SCRIPT, works more sophisticated, and
    bypasses various design flaws
    - if only the first parameter is used (old style), it can contain
      a complete "action/WikiPage" - but this is ambigutious
    - else $asid is the action, and $id contains the WikiPageName
    - $ewiki_config["script"] will now be used in favour of the constant
    - needs more work on _BINARY, should be a separate function
*/
## MOODLE-CHANGE: $asid="", Knows the devil why....
function ewiki_script($asid="", $id=false, $params="", $bin=0, $html=1, $script=NULL) {
   global $ewiki_config, $ewiki_plugins;

   #-- get base script url from config vars
   if (empty($script)) {
      $script = &$ewiki_config[!$bin?"script":"script_binary"];
   }


   #-- separate $action and $id for old style requests
   if ($id === false) {
      if (strpos($asid, EWIKI_ACTION_SEP_CHAR) !== false) {
         $asid = strtok($asid, EWIKI_ACTION_SEP_CHAR);
         $id = strtok("\000");
      }
      else {
         $id = $asid;
         $asid = "";
      }
   }

   #-- prepare params
   if (is_array($params)) {
      $uu = $params;
      $params = "";
      if ($uu) foreach ($uu as $k=>$v) {
         $params .= (strlen($params)?"&":"") . rawurlencode($k) . "=" . rawurlencode($v);
      }
   }
   #-- action= parameter
   if (EWIKI_USE_ACTION_PARAM >= 2) {
      $params = "action=$asid" . (strlen($params)?"&":"") . $params;
      $asid = "";
   }

   #-- workaround slashes in $id
   if (empty($asid) && (strpos($id, EWIKI_ACTION_SEP_CHAR) !== false) && !$bin) {
      $asid = "view";
   }
   /*paranoia*/ $asid = trim($asid, EWIKI_ACTION_SEP_CHAR);

   #-- make url
   if (EWIKI_URLENCODE) {
      $id = urlencode($id);
      $asid = urlencode($asid);
   }
   else {
      # only urlencode &, %, ? for example
   }
   $url = $script;
   if ($asid) {
      $id = $asid . EWIKI_ACTION_SEP_CHAR . $id;  #= "action/PageName"
   }
   if (strpos($url, "%s") !== false) {
      $url = str_replace("%s", $id, $url);
   }
   else {
      $url .= $id;
   }

   #-- add url params
   if (strlen($params)) {
      $url .= (strpos($url,"?")!==false ? "&":"?") . $params;
   }
   #-- fin
   if ($html) {
      //Don't replace & if it's part of encoded character (bug 2209)
      $url = preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/","&amp;", $url);
   } else {
      //This is going to be used in some header or meta redirect, so It cannot use &amp; (bug 2620)
      $url = preg_replace('/&amp;/', '&', $url);
   }
   return($url);
}


/*  this ewiki_script() wrapper is used to generate URLs to binary
    content in the ewiki database
*/
function ewiki_script_binary($asid, $id=false, $params=array(), $upload=0) {

   $upload |= is_string($params) && strlen($params) || count($params);

   #-- generate URL directly to the plainly saved data file,
   #   see also plugins/binary_store

   if (defined("EWIKI_DB_STORE_URL") && !$upload) {
      $url = EWIKI_DB_STORE_URL . rawurlencode($id);
   }

   #-- else get standard URL (thru ewiki.php) from ewiki_script()
   else {
      $url = ewiki_script($asid, $id, $params, "_BINARY=1");
   }

   return($url);
}


/*  this function returns the absolute ewiki_script url, if EWIKI_SCRIPT_URL
    is set, else it guesses the value
*/
function ewiki_script_url() {

   global $ewiki_action, $ewiki_id, $ewiki_config;

   $scr_template = $ewiki_config["script"];
   $scr_current = ewiki_script($ewiki_action, $ewiki_id);
   $req_uri = $_SERVER["REQUEST_URI"];

   if ($url = $ewiki_config["script_url"]) {
      return($url);
   }
   elseif (strpos($req_uri, $scr_current) !== false) {
      $url = str_replace($req_uri, $scr_current, $scr_template);
   }
   elseif (strpos($req_uri, "?") && (strpos($scr_template, "?") !== false)) {
      $url = substr($req_uri, 0, strpos($req_uri, "?"))
           . substr($scr_template, strpos($scr_template, "?"));
   }
   elseif (strpos($req_uri, $sn = $_SERVER["SCRIPT_NAME"])) {
      $url = $sn . "?id=";
   }
   else {
      return(NULL);   #-- could not guess it
   }

   #$url = "http://" . $_SERVER["SERVER_NAME"] . $url;
   return($url);
}




#------------------------------------------------------------ page plugins ---



function ewiki_page_links($id, &$data, $action) {
   $o = ewiki_make_title($id, ewiki_t("PAGESLINKINGTO", array("title"=>$id)), 1, $action, "", "_MAY_SPLIT=1");
   if ($pages = ewiki_get_backlinks($id)) {
      $o .= ewiki_list_pages($pages);
   } else {
      $o .= ewiki_t("This page isn't linked from anywhere else.");
   }
   return($o);
}

function ewiki_get_backlinks($id) {
   $result = ewiki_database("SEARCH", array("refs" => $id));
   $pages = array();
   while ($row = $result->get(0, 0x0020)) {
      if ( strpos($row["refs"], "\n$id\n") !== false) {
         $pages[] = $row["id"];
      }
   }
   return($pages);
}

function ewiki_get_links($id) {
   if ($data = ewiki_database("GET", array("id"=>$id))) {
      $refs = explode("\n", trim($data["refs"]));
      $r = array();
      foreach (ewiki_database("FIND", $refs) as $id=>$exists) {
         if ($exists) {
            $r[] = $id;
         }
      }
      return($r);
   }
}


function ewiki_list_pages($pages=array(), $limit=EWIKI_LIST_LIMIT,
                          $value_as_title=0, $pf_list=false)
{
   global $ewiki_plugins;
   $o = "";

   $is_num = !empty($pages[0]);
   $lines = array();
   $n = 0;

   foreach ($pages as $id=>$add_text) {

      $title = $id;
      $params = "";

      if (is_array($add_text)) {
         list($id, $params, $title, $add_text) = $add_text;
      }
      elseif ($is_num) {
         $id = $title = $add_text;
         $add_text = "";
      }
      elseif ($value_as_title) {
         $title = $add_text;
         $add_text = "";
      }

      $lines[] = '<a href="' . ewiki_script("", $id, $params) . '">' . s($title) . '</a> ' . $add_text;

      if (($limit > 0)  &&  ($n++ >= $limit)) {
         break;
      }
   }

   if ($pf_a = @$ewiki_plugins["list_transform"])
   foreach ($pf_a as $pf_transform) {
      $pf_transform($lines);
   }

   if (($pf_list) || ($pf_list = @$ewiki_plugins["list_pages"][0])) {
      $o = $pf_list($lines);
   }
   elseif($lines) {
      $o = "&middot; " . implode("<br />\n&middot; ", $lines) . "<br />\n";
   }

   return($o);
}




function ewiki_page_ordered_list($orderby="created", $asc=0, $print, $title) {

   $o = ewiki_make_title("", $title, 2, ".list", "links", 0);

   $sorted = array();
   $result = ewiki_database("GETALL", array($orderby));

   while ($row = $result->get()) {
      $row = ewiki_database("GET", array(
         "id" => $row["id"],
         ($asc >= 0 ? "version" : "uu") => 1  // version 1 is most accurate for {hits}
      ));
      #-- text page?
      if (EWIKI_DB_F_TEXT == ($row["flags"] & EWIKI_DB_F_TYPE)) {
         #-- viewing allowed?
         if (!EWIKI_PROTECTED_MODE || !EWIKI_PROTECTED_MODE_HIDING || ewiki_auth($row["id"], $row, "view")) {
            $sorted[$row["id"]] = $row[$orderby];
         }
      }
   }

   if ($asc != 0) { arsort($sorted); }
   else { asort($sorted); }

   foreach ($sorted as $name => $value) {
      if (empty($value)) { $value = "0"; }
   ##### BEGIN MOODLE ADDITION #####
      #$sorted[$name] = strftime(str_replace('%n', $value, $print), $value);
      if($print=="LASTCHANGED") {
        $value=strftime("%c",$value);
      }
      $sorted[$name] = get_string(strtolower($print),"wiki",$value);
   ##### BEGIN MOODLE ADDITION #####
   }
   $o .= ewiki_list_pages($sorted);

   return($o);
}



function ewiki_page_newest($id=0, $data=0) {
   return( ewiki_page_ordered_list("created", 1, "LASTCHANGED", ewiki_t(EWIKI_PAGE_NEWEST)) );
}

function ewiki_page_updates($id=0, $data=0) {
   return( ewiki_page_ordered_list("lastmodified", -1, "LASTCHANGED", ewiki_t(EWIKI_PAGE_UPDATES)) );
}

function ewiki_page_hits($id=0, $data=0) {
   ##### BEGIN MOODLE ADDITION #####
   return( ewiki_page_ordered_list("hits", 1, "hits", ewiki_t(EWIKI_PAGE_HITS)) );
}

function ewiki_page_versions($id=0, $data=0) {
   return( ewiki_page_ordered_list("version", -1, "changes", ewiki_t(EWIKI_PAGE_VERSIONS)) );
   ##### END MOODLE ADDITION #####
}







function ewiki_page_search($id, &$data, $action) {

   global $CFG;

   $q = optional_param('q', '', PARAM_CLEAN);
   $o = ewiki_make_title($id, $id, 2, $action);

   if ($q == '') {

      $o .= '<form action="' . ewiki_script("", $id) . '" method="post">';
      $o .= '<fieldset class="invisiblefieldset">';
      $o .= '<input name="q" size="30" /><br /><br />';
      $o .= '<input type="submit" value="'.$id.'" />';
      $o .= '</fieldset>';
      $o .= '</form>';
   }
   else {
      $found = array();

      if ($CFG->unicodedb) {
          $q = preg_replace('/\s*[\W]+\s*/u', ' ', $q);
      } else {
          $q = preg_replace('/\s*[^\w]+\s*/', ' ', $q);
      }
      foreach (explode(" ", $q) as $search) {

         if (empty($search)) { continue; }

         $result = ewiki_database("SEARCH", array("content" => $search));

         while ($row = $result->get()) {

            #-- show this entry in page listings?
            if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($row["id"], $row, "view")) {
               continue;
            }

            $found[] = $row["id"];
         }
      }

      $o .= ewiki_list_pages($found);
   }

   return($o);
}








function ewiki_page_info($id, &$data, $action) {

   global $ewiki_plugins, $ewiki_config, $ewiki_links;
   global $CFG, $COURSE, $DB, $OUTPUT;  // MOODLE HACK

   $pnum = optional_param(EWIKI_UP_PAGENUM, 0, PARAM_INT);
   $pend = optional_param(EWIKI_UP_PAGEEND, 0, PARAM_INT);

   $o = ewiki_make_title($id, ewiki_t("INFOABOUTPAGE")." '{$id}'", 2, $action,"", "_MAY_SPLIT=1");

   $flagnames = array(
      "TEXT", "BIN", "DISABLED", "HTML", "READONLY", "WRITEABLE",
      "APPENDONLY", "SYSTEM",
   );
   $show = array(
      "version", "author", "userid", "created",
      "lastmodified", "refs",
      "flags", "meta", "content"
   );

   #-- versions to show
   $v_start = $data["version"];
   if ( $pnum && ($pnum<=$v_start) ) {
      $v_start = $pnum;
   }
   $v_end = $v_start - $ewiki_config["list_limit"] + 1;
   if ( $pend && ($pend<=$v_start) ) {
      $v_end = $pend;
   }
   $v_end = max($v_end, 1);

   #-- go
   # the very ($first) entry is rendered more verbosely than the others
   for ($v=$v_start,$first=1; ($v>=$v_end); $v--,$first=0) {

      $current = ewiki_database("GET", array("id"=>$id, "version"=>$v));

      if (!strlen(trim($current["id"])) || !$current["version"] || !strlen(trim($current["content"]))) {
         continue;
      }

      $o .= '<table  class="version-info" cellpadding="2" cellspacing="1">' . "\n";

      #-- additional info-actions
      $commands = '';
      foreach ($ewiki_config["action_links"]["info"] as $thisaction=>$title)
      if (@$ewiki_plugins["action"][$thisaction] || @$ewiki_plugins["action_always"][$thisaction]) {
   ##### BEGIN MOODLE ADDITION #####
         if ($commands) {
             $commands .= '&nbsp;&nbsp;';
         }
         $commands .= '<a href="' .
           ewiki_script($thisaction, $id, array("version"=>$current["version"])) .
           '">' . get_string($title,"wiki") . '</a>';
   ##### END MOODLE ADDITION #####
      }

      #-- print page database entry
      foreach($show as $i) {

         $value = @$current[$i];

         #-- show database {fields} differently
         if ($i == "meta") {
            continue;  // MOODLE DOESN'T USE IT
            $str = "";
            if ($first && $value) { foreach ($value as $n=>$d) {
               $str .= s("$n: $d") . "<br />\n";
            } }
            $value = $str;
         }
         elseif ($value >= UNIX_MILLENNIUM) {    #-- {lastmodified}, {created}
            #### BEGIN MOODLE CHANGE
            $value=userdate($value);
            #$value = strftime("%c", $value);
            #### END MOODLE CHANGE
         }
         elseif ($i == "content") {
            continue;  // MOODLE DOESN'T CARE
            $value = strlen(trim($value)) . " bytes";
            $i = "content size";
         }
         elseif ($first && ($i == "refs") && !(EWIKI_PROTECTED_MODE && (EWIKI_PROTECTED_MODE_HIDING>=2))) {
            $a = explode("\n", trim($value));
            $ewiki_links = ewiki_database("FIND", $a);
            ewiki_merge_links($ewiki_links);
            foreach ($a as $n=>$link) {
               $a[$n] = ewiki_link_regex_callback(array("$link"), "force_noimg");
            }
            $value = trim(implode(", ", $a));
            if (!$value) {
                continue;
            }
         }
         elseif (strpos($value, "\n") !== false) {       #-- also for {refs}
            $value = str_replace("\n", ", ", trim($value));
            if (!$value) {
                continue;
            }
         }
         elseif ($i == "version") {
            $value = '<a href="' .
               ewiki_script("", $id, array("version"=>$value)) . '">' .
               $value . '</a> '."($commands)";
         }
         elseif ($i == "flags") {
            continue;  // MOODLE DOESN'T USE IT
            $fstr = "";
            for ($n = 0; $n < 32; $n++) {
              if ($value & (1 << $n)) {
                 if (! ($s=$flagnames[$n])) { $s = "UU$n"; }
                 $fstr .= $s . " ";
              }
            }
            $value = $fstr;
         }
         elseif ($i == "author") {
            continue;
            $ewiki_links=1;
            $value = preg_replace_callback("/((\w+:)?([".wiki_get_define('EWIKI_CHARS_U')."]+[".wiki_get_define('EWIKI_CHARS_L')."]+){2,}[\w\d]*)/", "ewiki_link_regex_callback", $value);
         }
         elseif ($i == "userid") {
             $i = 'author';
             if ($user = $DB->get_record('user', array('id'=>$value))) {
                 if (!isset($COURSE->id)) {
                     $COURSE->id = SITEID;
                 }
                 $picture = $OUTPUT->user_picture($user);
                 $value = $picture . html_writer::link("$CFG->wwwroot/user/view.php?id=$user->id&course=$COURSE->id", fullname($user));
             } else {
                 continue;
                 //$value = @$current['author'];
             }
         }

   ##### BEGIN MOODLE ADDITION #####
         $o .= '<tr class="page-'.$i.'"><td style="vertical-align:top;text-align:right;white-space: nowrap;"><b>' .ewiki_t($i). ':</b></td>' .
               '<td>' . $value . "</td></tr>\n";
   ##### END MOODLE ADDITION #####

      }

      $o .= "</table><br /><br />\n";

   }

   #-- page result split
   if ($v >= 1) {
      $o .= "<br />\n".get_string('showversions','wiki').' '.ewiki_chunked_page($action, $id, -1, $v, 1, 0, 0) . "\n <br />";
   }

   return($o);
}




function ewiki_chunked_page($action, $id, $dir=-1, $start=10, $end=1, $limit=0, $overlap=0.25, $collapse_last=0.67) {

   global $ewiki_config;

   if (empty($limit)) {
      $limit = $ewiki_config["list_limit"];
   }
   if ($overlap < 1) {
      $overlap = (int) ($limit * $overlap);
   }

   $p = "";
   $n = $start;

   while ($n) {

      $n -= $dir * $overlap;

      $e = $n + $dir * ($limit + $overlap) + 1;

      if ($dir<0) {
         $e = max(1, $e);
         if ($e <= $collapse_last * $limit) {
            $e = 1;
         }
      }
      else {
         $e = min($end, $e);
         if ($e >= $collapse_last * $limit) {
            $e = $end;
         }
      }

      $o .= ($o?" &middot; ":"")
         . '<a href="'.ewiki_script($action, $id, array(EWIKI_UP_PAGENUM=>$n, EWIKI_UP_PAGEEND=>$e))
         . '">'. "$n-$e" . '</a>';

      if (($n=$e-1) < $end) {
         $n = false;
      }
   }

   return('<span class="chunked-result">'. $o .'</span>');
}






function ewiki_page_edit($id, $data, $action) {

   global $ewiki_links, $ewiki_author, $ewiki_plugins, $ewiki_ring, $ewiki_errmsg;

   $content = optional_param('content', '', PARAM_CLEAN);
   $version = optional_param('version', '', PARAM_CLEAN);
   $preview = optional_param('preview', false, PARAM_BOOL);
   $save    = optional_param('save', false, PARAM_BOOL);

   $hidden_postdata = array();

   #-- previous version come back
   if (@$data["forced_version"]) {

      $current = ewiki_database("GET", array("id"=>$id));
      $data["version"] = $current["version"];
      unset($current);

    /// Is this done for somewhere else?
      $_REQUEST['content'] = $_POST['content'] = $_GET['content'] = null;
      $_REQUEST['version'] = $_POST['version'] = $_GET['version'] = null;
      $content = '';
      $version = '';
   }

   #-- edit hacks
   if ($pf_a = @$ewiki_plugins["edit_hook"]) foreach ($pf_a as $pf) {
      if ($output = $pf($id, $data, $hidden_postdata)) {
         return($output);
      }
   }

   #-- permission checks
   if (isset($ewiki_ring)) {
      $ring = $ewiki_ring;
   } else {
      $ring = 3;
   }
   $flags = @$data["flags"];
   if (!($flags & EWIKI_DB_F_WRITEABLE)) {

      #-- perform auth
      $edit_ring = (EWIKI_PROTECTED_MODE>=2) ? (2) : (NULL);
      if (EWIKI_PROTECTED_MODE && !ewiki_auth($id, $data, $action, $edit_ring, "FORCE")) {
         return($ewiki_errmsg);
      }

      #-- flag checking
      if (($flags & EWIKI_DB_F_READONLY) and ($ring >= 2)) {
         return(ewiki_t("CANNOTCHANGEPAGE"));
      }
      if (($flags) and (($flags & EWIKI_DB_F_TYPE) != EWIKI_DB_F_TEXT) and ($ring >= 1)) {
         return(ewiki_t("CANNOTCHANGEPAGE"));
      }
   }

   #-- "Edit Me"
   $o = ewiki_make_title($id, ewiki_t("EDITTHISPAGE").(" '{$id}'"), 2, $action, "", "_MAY_SPLIT=1");

   #-- preview
   if ($preview) {
      $o .= $ewiki_plugins["edit_preview"][0]($data);
   }

   #-- save
   if ($save) {


         #-- normalize to UNIX newlines
         $content = str_replace("\015\012", "\012", $content);
         $content = str_replace("\015", "\012", $content);

         #-- check for concurrent version saving
         $error = 0;
         if ((@$data["version"] >= 1) && ($data["version"] != $version) || ($version < 1)) {

            $pf = $ewiki_plugins["edit_patch"][0];

            if (!$pf || !$pf($id, $data)) {
               $error = 1;
               $o .= ewiki_t("ERRVERSIONSAVE") . "<br /><br />";
            }

         }
         if (!$error) {

            #-- new pages` flags
            if (! ($set_flags = @$data["flags"] & EWIKI_DB_F_COPYMASK)) {
               $set_flags = 1;
            }
            if (EWIKI_ALLOW_HTML) {
               $set_flags |= EWIKI_DB_F_HTML;
            }

            #-- mk db entry
            $save = array(
               "id" => $id,
               "version" => @$data["version"] + 1,
               "flags" => $set_flags,
               "content" => $content,
               "created" => ($uu=@$data["created"]) ? $uu : time(),
               "meta" => ($uu=@$data["meta"]) ? $uu : "",
               "hits" => ($uu=@$data["hits"]) ? $uu : "0",
            );
            ewiki_data_update($save);

            #-- edit storage hooks
            if ($pf_a = @$ewiki_plugins["edit_save"]) {
               foreach ($pf_a as $pf) {
                  $pf($save, $data);
               }
            }

            #-- save
            if (!$save || !ewiki_database("WRITE", $save)) {

               $o .= $ewiki_errmsg ? $ewiki_errmsg : ewiki_t("ERRORSAVING");

            }
            else {
               #-- prevent double saving, when ewiki_page() is re-called
               $_REQUEST = $_GET = $_POST = array();

               $o = ewiki_t("THANKSFORCONTRIBUTION") . "<br /><br />";
               $o .= ewiki_page($id);

               if (EWIKI_EDIT_REDIRECT) {
                  $url = ewiki_script("", $id, "thankyou=1", 0, 0, EWIKI_HTTP_HEADERS?ewiki_script_url():0);

                  if (EWIKI_HTTP_HEADERS && !headers_sent()) {
                     header("Status: 303 Redirect for GET");
                     header("Location: $url");
                     #header("URI: $url");
                     #header("Refresh: 0; URL=$url");
                  }
                  else {
                     $o .= '<meta http-equiv="Refresh" content="0; URL='.s($url).'">';
                  }
               }

            }

         }

         //@REWORK
         // header("Reload-Location: " . ewiki_script("", $id, "", 0, 0, ewiki_script_url()) );

   }
   else {
      #-- Edit <form>
      $o .= ewiki_page_edit_form($id, $data, $hidden_postdata);

      #-- additional forms
      if ($pf_a = $ewiki_plugins["edit_form_final"]) foreach ($pf_a as $pf) {
         $pf($o, $id, $data, $action);
      }
   }

   return($o);
}


function ewiki_data_update(&$data, $author="") {
   global $USER, $ewiki_links;

   #-- add backlinks entry
   ewiki_scan_wikiwords($data["content"], $ewiki_links, "_STRIP_EMAIL=1");
   $data["refs"] = "\n\n".implode("\n", array_keys($ewiki_links))."\n\n";
   $data["lastmodified"] = time();
   $data["author"] = ewiki_author($author);
   if (isset($USER->id)) {
       $data["userid"] = $USER->id;
   }
}



#-- edit <textarea>
function ewiki_page_edit_form(&$id, &$data, &$hidden_postdata) {
   global $ewiki_plugins, $ewiki_config, $moodle_format;

   $content = optional_param('content', '', PARAM_CLEAN);
   $version = optional_param('version', '', PARAM_CLEAN);

   $o='';

   #-- previously edited, or db fetched content
   if ($content || $version) {
      $data = array(
         "version" => $version,
         "content" => $content
      );
   }
   else {
      if (empty($data["version"])) {
         $data["version"] = 1;
      }
      @$data["content"] .= "";
   }

   #-- normalize to DOS newlines
   $data["content"] = str_replace("\015\012", "\012", $data["content"]);
   $data["content"] = str_replace("\015", "\012", $data["content"]);
   $data["content"] = str_replace("\012", "\015\012", $data["content"]);

   $hidden_postdata["version"] = &$data["version"];

   #-- edit textarea/form
   // deleted name="ewiki", can not find the reference, and it's breaking xhtml
   $o .= ewiki_t("EDIT_FORM_1")
       . '<form method="post" enctype="multipart/form-data" action="'
       . ewiki_script("edit", $id) . '" '
       . ' accept-charset="'.EWIKI_CHARSET.'">' . "\n";
   $o .= '<div>';
   #-- additional POST vars
   foreach ($hidden_postdata as $name => $value) {
       $o .= '<input type="hidden" name="'.$name.'" value="'.$value.'" />'."\n";
   }

   ($cols = strtok($ewiki_config["edit_box_size"], "x*/,;:")) && ($rows = strtok("x, ")) || ($cols=70) && ($rows=15);
   global $ewiki_use_editor, $ewiki_editor_content;
   $ewiki_editor_content=1;
   if($ewiki_use_editor) {
     ob_start();
     $usehtmleditor = can_use_html_editor();
     echo '<table><tr><td>';
     if ($usehtmleditor) { //clean and convert before editing
         $options = new stdClass();
         $options->smiley = false;
         $options->filter = false;
         $oldtext = format_text(ewiki_format($data["content"]), $moodle_format, $options);
     } else {
         $oldtext = ewiki_format($data["content"]);
     }
     print_textarea($usehtmleditor, $rows, $cols, 680, 400, "content", $oldtext);
     echo '</td></tr></table>';

     $o .= ob_get_contents();
     ob_end_clean();

   } else {
   ##### END MOODLE ADDITION #####

     $o .= '<textarea wrap="soft" id="ewiki_content" name="content" rows="'.$rows.'" cols="'.$cols.'">'
        . s($data["content"]) . "</textarea>"
        . $GLOBALS["ewiki_t"]["C"]["EDIT_TEXTAREA_RESIZE_JS"];

   ##### BEGIN MOODLE ADDITION #####
   }
   ##### END MOODLE ADDITION #####

   #-- more <input> elements before the submit button
   if ($pf_a = $ewiki_plugins["edit_form_insert"]) foreach ($pf_a as $pf) {
      $o .= $pf($id, $data, $action);
   }

   ##### BEGIN MOODLE ADDITION (Cancel Editing into Button) #####
   $o .= "\n<br />\n"
      . '<input type="submit" name="save" value="'. ewiki_t("SAVE") . '" />'."\n"
      . '<input type="submit" name="preview" value="'. ewiki_t("PREVIEW") . '" />' . "\n"
      . '<input type="submit" name="canceledit" value="'. ewiki_t("CANCEL_EDIT") . '" />' . "\n";
#      . ' &nbsp; <a href="'. ewiki_script("", $id) . '">' . ewiki_t("CANCEL_EDIT") . '</a>';
   ##### END MOODLE ADDITION #####

   #-- additional form elements
   if ($pf_a = $ewiki_plugins["edit_form_append"]) foreach ($pf_a as $pf) {
      $o .= $pf($id, $data, $action);
   }

   $o .= "\n</div></form>\n";
   //   . ewiki_t("EDIT_FORM_2");  // MOODLE DELETION

   return('<div class="edit-box">'. $o .'</div>');
}



#-- pic upload form
function ewiki_page_edit_form_final_imgupload(&$o, &$id, &$data, &$action) {
   if (EWIKI_SCRIPT_BINARY && EWIKI_UP_UPLOAD && EWIKI_IMAGE_MAXSIZE) {
      $o .= "\n<br />\n". '<div class="image-upload">'
      . '<form action='
      . '"'. ewiki_script_binary("", EWIKI_IDF_INTERNAL, "", "_UPLOAD=1") .'"'
      . ' method="post" enctype="multipart/form-data" target="_upload">'
      . '<fieldset class="invisiblefieldset">'
      . '<input type="hidden" name="MAX_FILE_SIZE" value="'.EWIKI_IMAGE_MAXSIZE.'" />'
      . '<input type="file" name="'.EWIKI_UP_UPLOAD.'"'
      . (defined("EWIKI_IMAGE_ACCEPT") ? ' accept="'.EWIKI_IMAGE_ACCEPT.'" />' : " />")
      . '<input type="hidden" name="'.EWIKI_UP_BINARY.'" value="'.EWIKI_IDF_INTERNAL.'" />'
      . '&nbsp;&nbsp;&nbsp;'
      . '<input type="submit" value="'.ewiki_t("UPLOAD_PICTURE_BUTTON").'" />'
      . '</fieldset></form></div>'. "\n";
  }
}


function ewiki_page_edit_preview(&$data) {
#### BEGIN MOODLE CHANGES
   global $moodle_format;
   $preview_text=$GLOBALS["ewiki_plugins"]["render"][0](optional_param("content", null, PARAM_CLEAN), 1, EWIKI_ALLOW_HTML || (@$data["flags"]&EWIKI_DB_F_HTML));
   return( '<div class="preview">'
           . "<hr noshade>"
           . "<div class='mdl-right'>" . ewiki_t("PREVIEW") . "</div><hr noshade><br />\n"
           . format_text($preview_text, $moodle_format)
           . "<br /><br /><hr noshade><br />"
           . "</div>"
   );
#### END MOODLE CHANGES
}


function ewiki_control_links($id, &$data, $action) {

   global $ewiki_plugins, $ewiki_ring, $ewiki_config;
   $action_links = & $ewiki_config["action_links"][$action];

   #-- disabled
   if (!$ewiki_config["control_line"]) {
      return("");
   }

   $o = "\n"
      . '<div class="mdl-right action-links control-links">'
      . "\n<br />\n"
      . "<hr noshade>" . "\n";

   if (@$data["forced_version"]) {

      $o .= '<form action="' . ewiki_script("edit", $id) . '" method="post">' .
            '<fieldset class="invisiblefieldset">'.
            '<input type="hidden" name="edit" value="old" />' .
            '<input type="hidden" name="version" value="'.$data["forced_version"].'" />' .
            '<input type="submit" value="' . ewiki_t("OLDVERCOMEBACK") . '" /></form> ';
   }
   else {
      foreach ($action_links as $action => $title)
      if (!empty($ewiki_plugins["action"][$action]) || !empty($ewiki_plugins["action_always"][$action]) || strpos($action, ":/"))
      {
         if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($id, $data, $action))  { continue; }

         $o .= '<a href="'
            . (strpos($action, ":/") ? $action : ewiki_script($action, $id))
            . '">' . ewiki_t($title) . '</a> &middot; ';
      }
   }

   if ($data["lastmodified"] >= UNIX_MILLENNIUM) {
      $o .= '<small>' . strftime(ewiki_t("LASTCHANGED"), @$data["lastmodified"]) . '</small>';
   }

   $o .= "</div>\n";

   return($o);
}





# ============================================================= rendering ===





########  ###   ###  #########  ###  ###   ###  #######
########  ####  ###  #########  ###  ####  ###  #######
###       ##### ###  ###             ##### ###  ###
######    #########  ###  ####  ###  #########  ######
######    #########  ###  ####  ###  #########  ######
###       ### #####  ###   ###  ###  ### #####  ###
########  ###  ####  #########  ###  ###  ####  #######
########  ###   ###  #########  ###  ###   ###  #######


/*
   The _format() function transforms $wiki_source pages into <html> strings,
   also calls various markup and helper plugins during the transformation
   process. The $params array can activate various features and extensions.
   only accepts UNIX newlines!
*/
function ewiki_format ( $wiki_source, $params = array()) {
   global $ewiki_links, $ewiki_plugins, $ewiki_config;

   #-- state vars
   $params = @array_merge($ewiki_config["format_params"], $params);
   $s = array(
      "in" => 0,         # current input $iii[] block array index
      "para" => "",
      "line" => "",
      "post" => "",      # string to append after current line/paragraph
      "line_i" => 0,
      "lines" => array(),
      "list" => "",      # lists
      "tbl" => 0,        # open table?
      "indent" => 0,     # indentation
      "close" => array(),
   );
   #-- aliases
   $in = &$s["in"];
   $line = &$s["line"];
   $lines = &$s["lines"];
   $para = &$s["para"];
   $post = &$s["post"];
   $list = &$s["list"];

   #-- input and output arrays
   if ($wiki_source[0] == "<") {            # also prepend an empty line
      $wiki_source = "\n" . $wiki_source;    # for faster strpos() searchs
   }
   $iii = array(
      0 => array(
         0 => $wiki_source."\n",    # body + empty line
         1 => 0x0FFF,               # flags (0x1=WikiMarkup, 0x2=WikiLinks, 0x100=BlockPlugins)
         2 => "core",               # block plugin name
      )
   );
   $ooo = array(
   );
   unset($wiki_source);

   #-- plugins
   $pf_tbl = @$ewiki_plugins["format_table"][0];
   $pf_line = @$ewiki_plugins["format_line"];

   #-- wikimarkup (wm)
   $htmlentities = $ewiki_config["htmlentities"];
   $wm_indent = &$ewiki_config["wm_indent"];
   $wm_table_defaults = &$ewiki_config["wm_table_defaults"];
   $wm_source = &$ewiki_config["wm_source"];
   $wm_list = &$ewiki_config["wm_list"];
   $wm_list_chars = implode("", array_keys($wm_list));
   $wm_style = &$ewiki_config["wm_style"];
   $wm_start_end = &$ewiki_config["wm_start_end"];

   #-- eleminate html
   $iii[0][0] = strtr($iii[0][0], $htmlentities);
   unset($htmlentities["&"]);

   #-- pre-processing plugins (working on wiki source)
   if ($pf_source = $ewiki_plugins["format_source"]) {
      foreach ($pf_source as $pf) $pf($iii[0][0]);
   }

   #-- simple markup
   $iii[0][0] = strtr($iii[0][0], $wm_source);

   #-- separate input into blocks ------------------------------------------
   foreach ($ewiki_config["format_block"] as $btype => $binfo) {

      #-- disabled block plugin?
      if ($binfo[2] && !$params[$binfo[2]])  {
         continue;
      }

      #-- traverse $iii[]
      $in = -1;
      while ((++$in) < count($iii)) {

         #-- search fragment delimeters
         if ($iii[$in][1] & 0x0100)
         while (
            ($c = & $iii[$in][0]) &&
            (($l = strpos($c, $binfo[0])) !== false) &&
            ($r = strpos($c, $binfo[1], $l))   )
         {
            $l_len = strlen($binfo[0]);
            $r_len = strlen($binfo[1]);

            $repl = array();
            // pre-text
            if (($l > 0) && trim($text = substr($c, 0, $l))) {
               $repl[] = array($text, 0xFFFF, "core");
            }
            // the extracted part
            if (trim($text = substr($c, $l+$l_len, $r-$l-$r_len))) {
               $repl[] = array($text, $binfo[3], "$btype");
            }
            // rest
            if (($r+$r_len < strlen($c)) && trim($text = substr($c, $r+$r_len))) {
               $repl[] = array($text, 0xFFFF, "core");
            }
            array_splice($iii, $in, 1, $repl);

            $in += 1;
         }
      }
   }

   #-- run format_block plugins
   $in = -1;
   while ((++$in) < count($iii)) {
      if (($btype = $iii[$in][2]) && ($pf_a = $ewiki_plugins["format_block"][$btype])) {
         $c = &$iii[$in][0];
         foreach ($pf_a as $pf) {
            # current buffer $c and pointer $in into $iii[] and state $s
            $pf($c, $in, $iii, $s, $btype);
         }
      }
   }

   #-- wiki markup ------------------------------------------------------
   $para = "";
   $in = -1;
   while ((++$in) < count($iii)) {
      #-- wikimarkup
      if ($iii[$in][1] & 0x0001) {

         #-- input $lines buffer, and output buffer $ooo array
         $lines = explode("\n", $iii[$in][0]);
         $ooo[$in] = array(
            0 => "",
            1 => $iii[$in][1]
         );
         $out = &$ooo[$in][0];
         $s["block"] = ($iii[$in][2] != "core");  # disables indentation & paragraphs

         #-- walk through wiki source lines
         $line_max = count($lines);
         foreach ($lines as $s["line_i"]=>$line) {
//echo "<pre>line={$s[line_i]}:".htmlspecialchars($line).":".htmlspecialchars($line[0])."</pre>";

            #-- empty lines separate paragraphs
            if (!strlen($line)) {
               ewiki_format_close_para($ooo, $s);
               ewiki_format_close_tags($ooo, $s);
               if (!$s["block"]) {
                  $out .= "\n";
               }
            }
            #-- horiz bar
            if (!strncmp($line, "----", 4)) {
               $out .= "<hr noshade>\n";
               continue;
            }
            #-- html comment
            #if (!strncmp($line, "&lt;!--", 7)) {
            #   $out .= "<!-- " . htmlentities(str_replace("--", "__", substr($line, 7))) . " -->\n";
            #   continue;
            #}

            ($c0 = $line[0])
            or ($c0 = "\000");

            #-- tables
            ### MOODLE CHANGE: TRIM
            if (($c0 == "|") && ($line[strlen(trim($line))-1] == "|")) {
               if (!$s["tbl"]) {
                  ewiki_format_close_para($ooo, $s);
                  ewiki_format_close_tags($ooo, $s);
                  $s["list"] = "";
               }
               $line = substr($line, 1, -1);
               if ($pf_tbl) {
                  $pf_tbl($line, $ooo, $s);
               }
               else {
                  if (!$s["tbl"]) {
                     $out .= "<table " . $wm_table_defaults . ">\n";
                     $s["close"][] = "\n</table>";
                  }
                  $line = "<tr>\n<td>" . str_replace("|", "</td>\n<td>", $line) . "</td>\n</tr>";
               }
               $s["tbl"] = 1;
               $para = false;
            }
            elseif ($s["tbl"]) {
               $s["tbl"] = 0;
            }


            #-- headlines
            if (($c0 == "!") && ($excl = strspn($line, "!"))) {
               if ($excl > 3) {
                  $excl = 3;
               }
               $line = substr($line, $excl);
               $excl = 5 - $excl;
               $line = "<h$excl>" . $line . "</h$excl>";
               if ($para) {
                  ewiki_format_close_para($ooo, $s);
               }
               ewiki_format_close_tags($ooo, $s);
               $para = false;
            }

            #-- whole-line markup
            # ???


            #-- indentation (space/tab markup)
            $n_indent = 0;
            if (!$list && (!$s["block"]) && ($n_indent = strspn($line, " "))) {
               $n_indent = (int) ($n_indent / 2.65);
               while ($n_indent > $s["indent"]) {
                  $out .= $wm_indent;
                  $s["indent"]++;
               }
            }
            while ($n_indent < $s["indent"]) {
               $out .= "";
               $s["indent"]--;
            }


            #-- text style triggers
            foreach ($wm_style as $find=>$replace) {
               $find_len = strlen($find);
               $loop = 20;
               while(($loop--) && (($l = strpos($line, $find)) !== false) && ($r = strpos($line, $find, $l + $find_len))) {
                  $line = substr($line, 0, $l) . $replace[0] .
                          substr($line, $l + strlen($find), $r - $l - $find_len) .
                          $replace[1] . substr($line, $r + $find_len);
               }
            }


            #-- list markup
            if (isset($wm_list[$c0])) {
               if (!$list) {
                  ewiki_format_close_para($ooo, $s);
                  ewiki_format_close_tags($ooo, $s);
               }
               $new_len = strspn($line, $wm_list_chars);
               $new_list = substr($line, 0, $new_len);
               $old_len = strlen($list);
               $lchar = $new_list[$new_len-1];
               list($lopen, $ltag1, $ltag2) = $wm_list[$lchar];

               #-- cut line
               $line = substr($line, $new_len);
               $lspace = "";
               $linsert = "";
               if ($ltag1) {
                  $linsert = "<$ltag1>" . strtok($line, $lchar) . "</$ltag1> ";
                  $line = strtok("\000");
               }

               #-- add another <li>st entry
               if ($new_len == $old_len) {
                  $lspace = str_repeat("  ", $new_len);
                  $out .=  "</$ltag2>\n" . $lspace . $linsert . "<$ltag2>";
               }
               #-- add list
               elseif ($new_len > $old_len) {
                  while ($new_len > ($old_len=strlen($list))) {
                     $lchar = $new_list[$old_len];
                     $list .= $lchar;
                     list($lopen, $ltag1, $ltag2) = $wm_list[$lchar];
                     $lclose = strtok($lopen, " ");
                     $lspace = str_repeat("  ", $new_len);

                     $out .= "\n$lspace<$lopen>\n" . "$lspace". $linsert . "<$ltag2>";
                     $s["close"][] = "$lspace</$lclose>";
                     $s["close"][] = "$lspace</$ltag2>";
                  }
               }
               #-- close lists
               else {
                  while ($new_len < ($old_len=strlen($list))) {
                     $remove = $old_len-$new_len;
                     ewiki_format_close_tags($ooo, $s, 2*$remove);
                     $list = substr($list, 0, -$remove);
                  }
                  if ($new_len) {
                     $lspace = str_repeat("  ", $new_len);
                     $out .= "$lspace</$ltag2>\n" . $lspace . $linsert . "<$ltag2>";
                  }
               }

               $list = $new_list;
               $para = false;
            }
            elseif ($list) {
               if ($c0 == " ") {
                  $para = false;
               }
               else {
                  ewiki_format_close_tags($ooo, $s);
                  $list = "";
               }
            }


            #-- start-end markup
            foreach ($wm_start_end as $d) {
               $len0 = strlen($d[0]);
               $loop = 20;
               while(($loop--) && (($l = strpos($line, $d[0])) !== false) && ($r = strpos($line, $d[1], $l + $len0))) {
                  $len1 = strlen($d[1]);
                  $line = substr($line, 0, $l) . $d[2] .
                          substr($line, $l + $len0, $r - $l - $len0) .
                          $replace[1] . substr($line, $r + $len1);
               }
            }

            #-- call wiki source formatting plugins that work on current line
            if ($pf_line) {
               foreach ($pf_line as $pf) $pf($out, $line, $post);
            }



            #-- add formatted line to page-output
            $line .= $post;
            if ($para === false) {
               $out .= $line;
               $para = "";
            }
            else {
               $para .= $line . "\n";
            }

         }

         #-- last block, or next not WikiSource?
         if (!isset($iii[$in+1]) || !($iii[$in+1][1] & 0x0011)) {
            ewiki_format_close_para($ooo, $s);
            ewiki_format_close_tags($ooo, $s);
         }
      }
      #-- copy as is into output buffer
      else {
         $ooo[$in] = $iii[$in];
      }
      $iii[$in] = array();
   }

   #-- wiki linking ------------------------------------------------------
   $scan_src = "";
   for ($in=0; $in<count($ooo); $in++) {
      if (EWIKI_HTML_CHARS && ($ooo[$in][1] & 0x0004)) {  # html character entities
         $ooo[$in][0] = str_replace("&amp;#", "&#", $ooo[$in][0]);
      }
      if ($ooo[$in][1] & 0x0022) {
         #-- join together multiple WikiSource blocks
         while (isset($ooo[$in+1]) && ($ooo[$in][1] & 0x0002) && ($ooo[$in+1][1] & 0x0002)) {
            $ooo[$in] = array(
               0 => $ooo[$in][0] . "\n" . $ooo[$in+1][0],
               1 => $ooo[$in][1] | $ooo[$in+1][1],
            );
            array_splice($ooo, $in+1, 1);
         }
      }
      $scan_src .= $ooo[$in][0];
   }


   #-- pre-scan
   if (1+$params["scan_links"]) {
      ewiki_scan_wikiwords($scan_src, $ewiki_links);
   }
   if ($pf_linkprep = $ewiki_plugins["format_prepare_linking"]) {
      foreach ($pf_linkprep as $pf) $pf($scan_src);
   }
   $scan_src = NULL;

   #-- finally the link-detection-regex
   for ($in=0; $in<count($ooo); $in++) {
      if ($ooo[$in][1] & 0x0002) {
      ##### BEGIN MOODLE ADDITION #####
      # No WikiLinks in Editor
      #################################
        global $ewiki_use_editor, $ewiki_editor_content;
        if(!($ewiki_use_editor && $ewiki_editor_content)) {
      ##### END MOODLE ADDITION #####
          ewiki_render_wiki_links($ooo[$in][0]);
      ##### BEGIN MOODLE ADDITION #####
         }
      ##### END MOODLE ADDITION #####
      }
   }

   #-- fin: combine all blocks into html string ----------------------------
   $html = "";
   for ($in=0; $in<count($ooo); $in++) {
      $html .= $ooo[$in][0] . "\n";
      $ooo[$in] = 0;
   }
   #-- call post processing plugins
   if ($pf_final = $ewiki_plugins["format_final"]) {
      foreach ($pf_final as $pf) $pf($html);
   }
   return($html);
}



function ewiki_format_close_para(&$ooo, &$s) {
   $out = &$ooo[$s["in"]][0];
   #-- output text block
   if (trim($s["para"])) {
      if (!$s["block"]) {
         #### MOODLE CHANGES
         global $ewiki_use_editor;
         if(!$ewiki_use_editor) {
           $s["para"] = "\n<p>\n" . ltrim($s["para"], "\n") . "</p>\n";
         }
         #### MOODLE CHANGES
      }
      #-- paragraph formation plugins
      if ($pf_a = $GLOBALS["ewiki_plugins"]["format_para"]) {
         foreach ($pf_a as $pf) {
            $pf($s["para"], $ooo, $s);
         }
      }
      $out .= $s["para"];
      $s["para"] = "";
   }
   #-- indentation
   while ($s["indent"]) {
      $out .= "";
      $s["indent"]--;
   }
}


function ewiki_format_close_tags(&$ooo, &$s, $count=100) {
   $out = &$ooo[$s["in"]][0];
   if (!is_array($s) || !is_array($s["close"])) {
      die("\$s is garbaged == $s!!");
   }
   while (($count--) && ($add = array_pop($s["close"]))) {
      $out .= $add . "\n";
   }
}


function ewiki_format_pre(&$str, &$in, &$iii, &$s, $btype) {
   $str = "<pre class=\"markup $btype\">" . $str . "</pre>";
}


function ewiki_format_html(&$str, &$in, &$iii, &$s) {
   $he = array_reverse($GLOBALS["ewiki_config"]["htmlentities"]);
   $str = strtr($str, array_flip($he));
   $str = "<span class=\"markup html\">" . $str . "\n</span>\n";
}


function ewiki_format_comment(&$str, &$in, &$iii, &$s, $btype) {
   $str = "<!-- "  . str_replace("--", "¯¯", $str) . " -->";
}




/* unclean pre-scanning for WikiWords in a page,
   pre-query to the db */
function ewiki_scan_wikiwords(&$wiki_source, &$ewiki_links, $se=0) {

   global $ewiki_config;

   #-- find matches
   preg_match_all($ewiki_config["wiki_pre_scan_regex"], $wiki_source, $uu);
   $uu = @array_merge($uu[1], $uu[2], $uu[3], $uu[4], (array)@$uu[5]);

   #-- clean up list, trim() spaces (allows more unclean regex) - page id unification
   foreach ($uu as $i=>$id) {
      $uu[$i] = trim($id);
   }
   unset($uu[""]);
   $uu = array_unique($uu);

   #-- query db
   $ewiki_links = ewiki_database("FIND",  $uu);

   #-- strip email adresses
   if ($se) {
      foreach ($ewiki_links as $c=>$uu) {
         if (strpos($c, "@") && (strpos($c, ".") || strpos($c, ":"))) {
            unset($ewiki_links[$c]);
         }
      }
   }

}



/* regex on page content,
   handled by callback (see below)
*/
function ewiki_render_wiki_links(&$o) {

   global $ewiki_links, $ewiki_config, $ewiki_plugins;

   #-- merge with dynamic pages list
   ewiki_merge_links($ewiki_links);

   #-- replace WikiWords
   $link_regex = &$ewiki_config["wiki_link_regex"];
   $o = preg_replace_callback($link_regex, "ewiki_link_regex_callback", $o);

   #-- cleanup
   unset($ewiki_links);
}


/*
   combines with page plugin list,
   and makes all case-insensitive
*/
function ewiki_merge_links(&$ewiki_links) {
   global $ewiki_plugins;
#### BEGIN MOODLE CHANGES
     global $ewiki_link_case;
     $ewiki_link_case=array();
#### END MOODLE CHANGES
     if ($ewiki_links !== true) {
      foreach ($ewiki_plugins["page"] as $page=>$uu) {
         $ewiki_links[$page] = 1;
      }
#### BEGIN MOODLE CHANGES
     foreach($ewiki_links as $page => $uu) {
       if($uu) {
         $ewiki_link_case[strtolower($page)]=$page;
       }
     }
#### END MOODLE CHANGES
      $ewiki_links = ewiki_array($ewiki_links);
   }
}




/* link rendering (p)regex callback
   (ooutch, this is a complicated one)
*/
function ewiki_link_regex_callback($uu, $force_noimg=0) {
   global $DB, $CFG;
   #print "<pre>"; print_r($uu); print "</pre>";
   global $ewiki_links, $ewiki_plugins, $ewiki_config, $ewiki_id;

   $str = trim($uu[0]);
   $type = array();
   $states = array();

   #-- link bracket '[' escaped with '!' or '~'
   if (($str[0] == "!") || ($str[0] == "~")) {
      return(substr($str, 1));
   }
   if ($str[0] == "#") {
      $states["define"] = 1;
      $str = substr($str, 1);
   }
   if ($str[0] == "[") {
      $states["brackets"] = 1;
      $str = substr($str, 1, -1);
   }

   #-- explicit title given via [ title | WikiLink ]
   $href = $title = strtok($str, "|");
   if ($uu = strtok("|")) {
      $parts = explode('|', $str);
      $title = $parts[1] . ' | ' .$parts[0];
      $states["titled"] = 1;
   }
   #-- title and href swapped: swap back
   if (strpos("://", $title) || strpos($title, ":") && !strpos($href, ":")) {
      $uu = $title; $title = $href; $href = $uu;
   }

   #-- new entitling scheme [ url "title" ]
   if ((($l=strpos($str, '"')) < ($r=strrpos($str, '"'))) && ($l!==false) ) {
      $title = substr($str, $l + 1, $r - $l - 1);
      $href = substr($str, 0, $l) . substr($str, $r + 1);
      $states["titled"] = 1;
    }

   #-- strip spaces
   $spaces_l = ($href[0]==" ") ?1:0;
   $spaces_r = ($href[strlen($href)-1]==" ") ?1:0;
   $title = ltrim(trim($title), "^");
   $href = ltrim(trim($href), "^");

   #-- strip_htmlentities()
   if (1&&    (strpos($href, "&")!==false) && strpos($href, ";")) {
      foreach (array("&lt;"=>"<", "&gt;"=>">", "&amp;"=>"&") as $f=>$t) {
         $href = str_replace($f, $t, $href);
      }
   }

   #-- anchors
   $href2 = "";
   if (($p = strrpos($href, "#")) && ($p) && ($href[$p-1] != "&")) {
      $href2 = trim(substr($href, $p));
      $href = trim(substr($href, 0, $p));
   }
   elseif ($p === 0) {
      $states["define"] = 1;
   }
   if ($href == ".") {
      $href = $ewiki_id;
   }

   #-- for case-insensitivines
   $href_i = EWIKI_CASE_INSENSITIVE ? strtolower($href) : ($href);

   #-- injected URLs
   if (strpos($inj_url = $ewiki_links[$href_i], "://")) {
      if ($href==$title) { $href = $inj_url; }
   }

   #-- interwiki links
   if (strpos($href, ":") && ($uu = ewiki_interwiki($href, $type))) {
      $href = $uu;
      $str = "<a href=\"$href$href2\">$title</a>";
   }
   #-- action:WikiLinks
   elseif ($ewiki_plugins["action"][$a=strtolower(strtok($href, ":"))]) {
      $type = array($a, "action", "wikipage");
      $str = '<a href="' . ewiki_script($a, strtok("\000")) . '">' . $title . '</a>';
   }
   #-- page anchor definitions, if ($href[0]=="#")
   elseif (@$states["define"]) {
      $type = array("anchor");
      if ($title==$href) { $title="&nbsp;"; }
      $str = '<a name="' . s(ltrim($href, "#")) . '">' . ltrim($title, "#") . '</a>';
   }
   #-- inner page anchor jumps
   elseif (strlen($href2) && ($href==$ewiki_id) || ($href[0]=="#") && ($href2=&$href)) {
      $type = array("jump");
      $str = '<a href="' . s($href2) . '">' . $title . '</a>';
   }
   #-- ordinary internal WikiLinks
   elseif (($ewiki_links === true) || @$ewiki_links[$href_i]) {
      $type = array("wikipage");
#### BEGIN MOODLE CHANGES
      global $ewiki_link_case;
      $href_realcase=array_key_exists($href_i,$ewiki_link_case) ? $ewiki_link_case[$href_i] : $href;
      $str = '[[' . $title . ']]';
#### END MOODLE CHANGES
   }
   #-- guess for mail@addresses, convert to URI if
   elseif (strpos($href, "@") && !strpos($href, ":")) {
      $type = array("email");
      $href = "mailto:" . $href;
   }
   #-- not found fallback
   else {
      $str = "";
      #-- a plugin may take care
      if ($pf_a = $ewiki_plugins["link_notfound"]) {
         foreach ($pf_a as $pf) {
            if ($str = $pf($title, $href, $href2, $type)) {
               break;
         }  }
      }

      #-- (QuestionMarkLink to edit/ action)
      if (!$str) {
         $type = array("notfound");
         //$str = '<span class="NotFound"><b>' . $title . '</b><a href="' .
         //    ewiki_script("", $href) . '">?</a></span>';
         $str = '[['. $title . ']]';
      }
   }

   #-- convert standard URLs
   foreach ($ewiki_config["idf"]["url"] as $find)
    if (strpos($href, $find)===0) {
      $type[-2] = "url";
      $type[-1] = strtok($find, ":");

      #-- URL plugins
      if ($pf_a = $ewiki_plugins["link_url"]) foreach ($pf_a as $pf) {
         if ($str = $pf($href, $title)) { break 2; }
      }
      $meta = @$ewiki_links[$href];

      #-- check for image files
      $ext = substr($href, strrpos($href,"."));
      $nocache = strpos($ext, "no");
      $ext = strtok($ext, "?&#");
      $obj = in_array($ext, $ewiki_config["idf"]["obj"]);
      $img = $obj || in_array($ext, $ewiki_config["idf"]["img"]);

      #-- internal:// references (binary files)
      if (EWIKI_SCRIPT_BINARY && ((strpos($href, EWIKI_IDF_INTERNAL)===0)  ||
          EWIKI_IMAGE_MAXSIZE && EWIKI_CACHE_IMAGES && $img && !$nocache) )
      {
          $type = array("binary");
          //$href = ewiki_script_binary("", $href);
          //##### BEGIN MOODLE ADDITION #####
          $pattern = 'internal://';
          $matches = array();
          $filename = str_replace($pattern, '', $href);
          $filename = clean_param($filename, PARAM_FILE);
          $href = "@@PLUGINFILE@@/$filename";
          //##### END MOODLE ADDITION #####
      }

      #-- output html reference
      if (!$img || $force_noimg || !$states["brackets"] || (strpos($href, EWIKI_IDF_INTERNAL) === 0)) {
         $str = '<a href="' . $href . '">' . $title . '</a>';
      }
      #-- img tag
      else {
         if (is_string($meta)) {
            $meta = unserialize($meta);
         }
         $type = array("image");
         #-- uploaded images size
         $x = $meta["width"];
         $y = $meta["height"];
         if ($p = strpos('?', $href)) {      #-- width/height given in url
            parse_str(str_replace("&amp;", "&", substr($href, $p)), $meta);
            ($uu = $meta["x"] . $meta["width"]) and ($x = $uu);
            ($uu = $meta["y"] . $meta["height"]) and ($y = $uu);
            if ($scale = $meta["r"] . $meta["scale"]) {
               ($p = strpos($scale, "%")) and ($scale = strpos($scale, 0, $p) / 100);
               $x *= $scale; $y *= $scale;
            }
         }
         $align = array('', ' align="right"', ' align="left"', ' align="center"');
         $align = $align[$spaces_l + 2*$spaces_r];
         $str = ($obj ? '<embed width="70%"' : '<img') . ' src="' . $href . '"' .
                ' alt="' . ($title) . '"' .
                (@$states["titled"] ? ' title="' . ($title) . '"' : '').
        ($x && $y ? " width=\"$x\" height=\"$y\"" : "") .
                $align . " />" . ($obj ? "</embed>" : "");
                                            # htmlentities($title)
      }

      break;
   }

   #-- icon/transform plugins
   ksort($type);
   if ($pf_a = @$ewiki_plugins["link_final"]) {
      foreach ($pf_a as $pf) { $pf($str, $type, $href, $title); }
   }

   return($str);
}


/*
   Returns URL if it encounters an InterWiki:Link or workalike.
*/
function ewiki_interwiki($href, &$type) {
   global $ewiki_config, $ewiki_plugins;

   if (strpos($href, ":") and !strpos($href, "//") and ($p1 = strtok($href, ":"))) {

      $page = strtok("\000");

      if (!empty($ewiki_config["interwiki"]) && (($p1 = ewiki_array($ewiki_config["interwiki"], $p1)) !== NULL)) {
         $type = array("interwiki", $uu);
         while ($p1_alias = $ewiki_config["interwiki"][$p1]) {
             $type[] = $p1;
             $p1 = $p1_alias;
         }
         if (!strpos($p1, "%s")) {
             $p1 .= "%s";
         }
         $href = str_replace("%s", $page, $p1);
         return($href);
      }
      elseif ($pf = $ewiki_plugins["intermap"][$p1]) {
         return($pf($p1, $page));
      }
   }
}


/*
   implements FeatureWiki:InterMapWalking
*/
function ewiki_intermap_walking($id, &$data, $action) {
   if (empty($data["version"]) && ($href = ewiki_interwiki($id, $uu))) {
      header("Location: $href");
      return("<a href=\"$href\">$href</a>");
   }
}



# =========================================================================



#####    ##  ##   ##    ##    #####   ##  ##
######   ##  ###  ##   ####   ######  ##  ##
##  ##   ##  ###  ##  ######  ##  ##  ##  ##
#####    ##  #### ##  ##  ##  ######  ######
#####    ##  #######  ######  ####     ####
##  ###  ##  ## ####  ######  #####     ##
##  ###  ##  ##  ###  ##  ##  ## ###    ##
######   ##  ##  ###  ##  ##  ##  ##    ##
######   ##  ##   ##  ##  ##  ##  ##    ##




/*  fetch & store
*/
function ewiki_binary($break=0) {
   global $ewiki_plugins;
   global $USER;   // MOODLE

   $id = optional_param(EWIKI_UP_BINARY, '', PARAM_CLEAN);

   #-- reject calls
   if (!strlen($id) || !EWIKI_IDF_INTERNAL) {
      return(false);
   }
   if (headers_sent()) die("ewiki-binary configuration error");

   #-- upload requests
   $upload_file = @$_FILES[EWIKI_UP_UPLOAD];
   $add_meta = array();
   if ($orig_name = @$upload_file["name"]) {
      $add_meta["Content-Location"] = urlencode($orig_name);
      $add_meta["Content-Disposition"] = 'inline; filename="'.urlencode(basename("remote://$orig_name")).'"';
   }

   #-- what are we doing here?
   if (($id == EWIKI_IDF_INTERNAL) && ($upload_file)) {
      $do = "upload";
   }
   else {
      $data = ewiki_database("GET", array("id" => $id));
      $flags = @$data["flags"];
      if (EWIKI_DB_F_BINARY == ($flags & EWIKI_DB_F_TYPE)) {
         $do = "get";
      }
      elseif (empty($data["version"]) and EWIKI_CACHE_IMAGES) {
         $do = "cache";
      }
      else {
         $do = "nop";
      }
   }

   #-- auth only happens when enforced with _PROTECTED_MODE_XXL setting
   #   (authentication for inline images in violation of the WWW spirit)
   if ((EWIKI_PROTECTED_MODE>=5) && !ewiki_auth($id, $data, "binary-{$do}")) {
      $_REQUEST['id'] = $_POST['id'] = $_GET['id'] = "view/BinaryPermissionError";
      return("view/BinaryPermissionError");
   }

   #-- upload an image
   if ($do == "upload"){
      $id = ewiki_binary_save_image($upload_file["tmp_name"], "", $return=0, $add_meta);
      @unlink($upload_file["tmp_name"]);
      ($title = trim($orig_name, "/")) && ($title = preg_replace("/[^-._\w\d]+/", "_", substr(substr($orig_name, strrpos($title, "/")), 0, 20)))
      && ($title = '"'.$title.'"') || ($title="");

      if ($id) {
         echo<<<EOF
<html><head><title>File/Picture Upload</title><script type="text/javascript"><!--
 opener.document.forms["ewiki"].elements["content"].value += "\\nUPLOADED PICTURE: [$id$title]\\n";
 window.setTimeout("self.close()", 5000);
//--></script></head><body bgcolor="#440707" text="#FFFFFF">Your uploaded file was saved as<br /><big><b>
[$id]
</b></big>.<br /><br /><noscript>Please copy this &uarr; into the text input box:<br />select/mark it with your mouse, press [Ctrl]+[Insert], go back<br />to the previous screen and paste it into the textbox by pressing<br />[Shift]+[Insert] inside there.</noscript></body></html>
EOF;
      }
   }

   #-- request for contents from the db
   elseif ($do == "get") {
#### CHANGED FOR MOODLE
   if (EWIKI_HIT_COUNTING) {
      $tmp["id"]=$id;
      ewiki_database("HIT", $tmp);
   }
#### CHANGED FOR MOODLE

      #-- send http_headers from meta
      if (is_array($data["meta"])) {
         foreach ($data["meta"] as $hdr=>$val) {
            if (($hdr[0] >= "A") && ($hdr[0] <= "Z")) {
               header("$hdr: $val");
            }
         }
      }

      #-- fetch from binary store
      if ($pf_a = $ewiki_plugins["binary_get"]) {
#### CHANGED FOR MOODLE
        foreach ($pf_a as $pf) { $pf($id, $data["meta"]); }

#### END CHANGED FOR MOODLE
      }

      #-- else fpassthru
      echo $data["content"];
   }

   #-- fetch & cache requested URL,
   elseif ($do == "cache") {

      #-- check for standard protocol names, to prevent us from serving
      #   evil requests for '/etc/passwd.jpeg' or '../.htaccess.gif'
      if (preg_match('@^\w?(http|ftp|https|ftps|sftp)\w?://@', $id)) {

         #-- generate local copy
         $filename = tempnam(EWIKI_TMP, "ewiki.local.temp.");
         if (($i = fopen($id, "rb")) && ($o = fopen($filename, "wb"))) {

            while (!feof($i)) {
               fwrite($o, fread($i, 65536));
            }

            fclose($i);
            fclose($o);

            $add_meta = array(
               "Content-Location" => urlencode($id),
               "Content-Disposition" => 'inline; filename="'.urlencode(basename($id)).'"'
            );

            $result = ewiki_binary_save_image($filename, $id, "RETURN", $add_meta);
         }
      }

      #-- deliver
      if ($result && !$break) {
         ewiki_binary($break=1);
      }
      #-- mark URL as unavailable
      else {
         $data = array(
            "id" => $id,
            "version" => 1,
            "flags" => EWIKI_DB_F_DISABLED,
            "lastmodified" => time(),
            "created" => time(),
            "author" => ewiki_author("ewiki_binary_cache"),
            "userid" => $USER->id,
            "content" => "",
            "meta" => array("Status"=>"404 Absent"),
         );
         ewiki_database("WRITE", $data);
         header("Location: $id");
         ewiki_log("imgcache: did not find '$id', and marked it now in database as DISABLED", 2);
      }

   }

   #-- "we don't sell this!"
   else {
      if (strpos($id, EWIKI_IDF_INTERNAL) === false) {
         header("Status: 301 Located SomeWhere Else");
         header("Location: $id");
      }
      else {
         header("Status: 404 Absent");
         header("X-Broken-URI: $id");
      }
   }

   // you should not remove this one, it is really a good idea to use it!
   die();
}






function ewiki_binary_save_image($filename, $id="", $return=0,
$add_meta=array(), $accept_all=EWIKI_ACCEPT_BINARY, $care_for_images=1)
{
   global $ewiki_plugins;
   global $USER;   // MOODLE

   #-- break on empty files
   if (!filesize($filename)) {
      return(false);
   }

   #-- check for image type and size
   $mime_types = array(
      "application/octet-stream",
      "image/gif",
      "image/jpeg",
      "image/png",
      "application/x-shockwave-flash"
   );
   $ext_types = array(
      "bin", "gif", "jpeg", "png", "swf"
   );
   list($width, $height, $mime_i, $uu) = getimagesize($filename);
   (!$mime_i) && ($mime_i=0) || ($mime = $mime_types[$mime_i]);

   #-- images expected
   if ($care_for_images) {

      #-- mime type
      if (!$mime_i && !$accept_all || !filesize($filename)) {
         ewiki_die(ewiki_t("BIN_NOIMG"), $return);
         return;
      }

      #-- resize image
      if (strpos($mime,"image/")!==false) {
      if ($pf_a = $ewiki_plugins["image_resize"]) {
      foreach ($pf_a as $pf) {
      if (EWIKI_IMAGE_RESIZE && (filesize($filename) > EWIKI_IMAGE_MAXSIZE)) {
         $pf($filename, $mime, $return);
         clearstatcache();
      }}}}

      #-- reject image if too large
      if (strlen($content) > EWIKI_IMAGE_MAXSIZE) {
         ewiki_die(ewiki_t("BIN_IMGTOOLARGE"), $return);
         return;
      }

      #-- again check mime type and image sizes
      list($width, $height, $mime_i, $uu) = getimagesize($filename);
      (!$mime_i) && ($mime_i=0) || ($mime = $mime_types[$mime_i]);

   }
   ($ext = $ext_types[$mime_i]) or ($ext = $ext_types[0]);

   #-- binary files
   if ((!$mime_i) && ($pf = $ewiki_plugins["mime_magic"][0])) {
      if ($tmp = $pf($content)) {
         $mime = $tmp;
      }
   }
   if (!strlen($mime)) {
      $mime = $mime_types[0];
   }

   #-- store size of binary file
   $add_meta["size"] = filesize($filename);
   $content = "";

   #-- handler for (large/) binary content?
   if ($pf_a = $ewiki_plugins["binary_store"]) {
      foreach ($pf_a as $pf) {
         $pf($filename, $id, $add_meta, $ext);
      }
   }

   #-- read file into memory (2MB), to store it into the database
   if ($filename) {
      $f = fopen($filename, "rb");
      $content = fread($f, 1<<21);
      fclose($f);
   }

   #-- generate db file name
   if (empty($id)) {
      $md5sum = md5($content);
      $id = EWIKI_IDF_INTERNAL . $md5sum . ".$ext";
      ewiki_log("generated md5sum '$md5sum' from file content");
   }

   #-- prepare meta data
   $meta = @array_merge(array(
      "class" => $mime_i ? "image" : "file",
      "Content-Type" => $mime,
      "Pragma" => "cache",
   ), $add_meta);
   if ($mime_i) {
      $meta["width"] = $width;
      $meta["height"] = $height;
   }

   #-- database entry
   $data = array(
      "id" => $id,
      "version" => "1",
      "author" => ewiki_author(),
      "userid" => $USER->id,
      "flags" => EWIKI_DB_F_BINARY | EWIKI_DB_F_READONLY,
      "created" => time(),
      "lastmodified" => time(),
      "meta" => &$meta,
      "content" => &$content,
   );

   #-- write if not exist
   $exists = ewiki_database("FIND", array($id));
   if (! $exists[$id] ) {
      $result = ewiki_database("WRITE", $data);
      ewiki_log("saving of '$id': " . ($result ? "ok" : "error"));
   }
   else {
      ewiki_log("binary_save_image: '$id' was already in the database", 2);
   }

   return($id);
}




# =========================================================================


####     ####  ####   ########     ########
#####   #####  ####  ##########   ##########
###### ######  ####  ####   ###   ####    ###
#############        ####        ####
#############  ####   ########   ####
#### ### ####  ####    ########  ####
####  #  ####  ####        ####  ####
####     ####  ####  ###   ####  ####    ###
####     ####  ####  #########    ##########
####     ####  ####   #######      ########



/* yes! it is not neccessary to annoy users with country flags, if the
   http already provides means to determine preferred languages!
*/
function ewiki_localization() {

   global $ewiki_t, $ewiki_plugins;

   $deflangs = ','.@$_ENV["LANGUAGE"] . ','.@$_ENV["LANG"]
             . ",".wiki_get_define('EWIKI_DEFAULT_LANG') . ",en,C";

   foreach (explode(",", @$_SERVER["HTTP_ACCEPT_LANGUAGE"].$deflangs) as $l) {

      $l = strtok($l, ";");
      $l = strtok($l, "-"); $l = strtok($l, "_"); $l = strtok($l, ".");
      $l = trim($l);

      $ewiki_t["languages"][] = strtolower($l);
   }
}




/* poor mans gettext, $repl is an array of string replacements to get
   applied to the fetched text chunk,
   "$const" is either an entry from $ewiki_t[] or a larger text block
   containing _{text} replacement braces of the form "_{...}"
*/
function ewiki_t($const, $repl=array(), $pref_langs=array()) {
   ##### BEGIN MOODLE ADDITION #####
   $replacechars=array("." => "",
                       "," => "",
                       "!" => "",
                       ";" => "",
                       ":" => "",
                       "'" => "",
                       '"' => "",
                       "-" => "",
                       "_" => "",
                       " " => "",
                       "+" => "");

   $translation=get_string(strtolower(strtr($const,$replacechars)),"wiki",$repl);
   return $translation;
   ##### END MOODLE ADDITION #####

/*   global $ewiki_t;

   #-- use default language wishes
   if (empty($pref_langs)) {
      $pref_langs = $ewiki_t["languages"];
   }

   #-- large text snippet replaceing
   if (strpos($const, "_{") !== false) {
      while ( (($l=strpos($const,"_{")) || ($l===0)) && ($r=strpos($const,"}",$l)) ) {
         $const = substr($const, 0, $l)
                . ewiki_t(substr($const, $l+2, $r-$l-2))
                . substr($const,$r+1);
      }
   }

   #-- just one string
   else foreach ($pref_langs as $l) {

      if (is_string($r = @$ewiki_t[$l][$const]) || ($r = @$ewiki_t[$l][strtoupper($const)])) {

         foreach ($repl as $key=>$value) {
            if ($key[0] != '$') {
               $key = '$'.$key;
            }
            $r = str_replace($key, $value, $r);
         }
         return($r);

      }
   }

   return($const);*/
}






function ewiki_log($msg, $error_type=3) {

   if ((EWIKI_LOGLEVEL >= 0) && ($error_type <= EWIKI_LOGLEVEL)) {

      $msg = time() . " - " .
             getremoteaddr() . ":" . $_SERVER["REMOTE_PORT"] . " - " .
             $_SERVER["REQUEST_METHOD"] . " " . $_SERVER["REQUEST_URI"] . " - " .
             strtr($msg, "\n\r\000\377\t\f", "\r\r\r\r\t\f") . "\n";
      error_log($msg, 3, EWIKI_LOGFILE);
   }
}




function ewiki_die($msg, $return=0) {
   ewiki_log($msg, 1);
   if ($return) {
      return($GLOBALS["ewiki_error"] = $msg);
   }
   else {
      die($msg);
   }
}



function ewiki_array_hash(&$a) {
   return(count($a) . ":" . implode(":", array_keys(array_slice($a, 0, 3))));
}



/* provides an case-insensitive in_array replacement to search a page name
   in a list of others;
   the supplied $array WILL be lowercased afterwards, unless $dn was set
*/
function ewiki_in_array($value, &$array, $dn=0, $ci=EWIKI_CASE_INSENSITIVE) {

   static $as = array();

   #-- work around pass-by-reference
   if ($dn && $ci) {   $dest = array();   }
              else {   $dest = &$array;   }

   #-- make everything lowercase
   if ($ci) {
      $value = strtolower($value);
      if (empty($as[ewiki_array_hash($array)])) {  // prevent working on the
         foreach ($array as $i=>$v) {              // same array multiple times
            $dest[$i] = strtolower($v);
         }
         $as[ewiki_array_hash($dest)] = 1;
      }
   }

   #-- search in values
   return(in_array($value, $dest));
}



/* case-insensitively retrieves an entry from an $array,
   or returns the given $array lowercased if $key was obmitted
*/
function ewiki_array($array, $key=false, $am=1, $ci=EWIKI_CASE_INSENSITIVE) {

   #-- make everything lowercase
   if ($ci) {
      $key = strtolower($key);

      $r = array();
      foreach ($array as $i=>$v) {
         $i = strtolower($i);
         if (!$am || empty($r[$i])) {
            $r[$i] = $v;
         }
         else {
            $r[$i] .= $v;   //RET: doubling for images`meta won't happen
         }          // but should be "+" here for integers
      }
      $array = &$r;
   }

   #-- search in values
   if ($key) {
      return(@$array[$key]);
   }
   else {
      return($array);
   }
}






function ewiki_author($defstr="") {

   $author = @$GLOBALS["ewiki_author"];
   ($ip = getremoteaddr()) or ($ip = "127.0.0.0");
   ($port = $_SERVER["REMOTE_PORT"]) or ($port = "null");
   $hostname = $ip;
   $remote = (($ip != $hostname) ? $hostname . " " : "")
           . $ip . ":" . $port;

   (empty($author)) && (
      ($author = $defstr) ||
      ($author = $_SERVER["HTTP_FROM"]) ||  // RFC2068 sect 14.22
      ($author = $_SERVER["PHP_AUTH_USER"])
   );

   (empty($author))
      && ($author = $remote)
      || ($author = $author . " (" . $remote . ")" );

   return($author);
}





/*  Returns a value of (true) if the currently logged in user (this must
    be handled by one of the plugin backends) is authenticated to do the
    current $action, or to view the current $id page.
  - alternatively just checks current authentication $ring permission level
  - errors are returned via the global $ewiki_errmsg
*/
function ewiki_auth($id, &$data, $action, $ring=false, $request_auth=0) {

   global $ewiki_plugins, $ewiki_ring, $ewiki_author, $ewiki_errmsg;
   $ok = true;
   $ewiki_errmsg="";

#echo "_a($id,dat,$action,$ring,$request_auth)<br />\n";

   if (EWIKI_PROTECTED_MODE) {

      #-- set required vars
      if (!isset($ewiki_ring)) {
         $ewiki_ring = (int)EWIKI_AUTH_DEFAULT_RING;
      }
      if ($ring===false) {
         $ring = NULL;
      }

      #-- plugins to call
      $pf_login = @$ewiki_plugins["auth_query"][0];
      $pf_perm = $ewiki_plugins["auth_perm"][0];

      #-- nobody is currently logged in, so try to fetch username,
      #   the login <form> is not yet enforced
      if ($pf_login && empty($ewiki_auth_user)) {
         $pf_login($data, 0);
      }

      #-- check permission for current request (page/action/ring)
      if ($pf_perm) {

         #-- via _auth handler
         $ok = $pf_perm($id, $data, $action, $ring, $request_auth);

         #-- if it failed, we really depend on the login <form>,
         #   and then recall the _perm plugin
         if ($pf_login && (($request_auth >= 2) || !$ok && $request_auth && (empty($ewiki_auth_user) || EWIKI_AUTO_LOGIN) && empty($ewiki_errmsg))) {
//@FIXME: complicated if()  - strip empty(errmsg) ??
            $pf_login($data, $request_auth);
            $ok = $pf_perm($id, $data, $action, $ring, $request_auth=0);
         }
      }
      else {
         $ok = !isset($ring) || isset($ring) && ($ewiki_ring <= $ring);
      }

      #-- return error string
      if (!$ok && empty($ewiki_errmsg)) {
         $ewiki_errmsg = ewiki_t("FORBIDDEN");
      }
   }

   return($ok);
}


/*
   Queries all registered ["auth_userdb"] plugins for the given
   username, and compares password to against "db" value, sets
   $ewiki_ring and returns(true) if valid.
*/
function ewiki_auth_user($username, $password) {
  global $ewiki_ring, $ewiki_errmsg, $ewiki_auth_user, $ewiki_plugins, $ewiki_author;

  if (empty($username)) {
     return(false);
  }
  if (($password[0] == "$") || (strlen($password) > 12)) {
     ewiki_log("_auth_userdb: password was transmitted in encoded form, or is just too long (login attemp for user '$username')", 2);
     return(false);
  }

  if ($pf_u = $ewiki_plugins["auth_userdb"])
  foreach ($pf_u as $pf) {

     if (function_exists($pf) && ($entry = $pf($username, $password))) {

        #-- get and compare password
        if ($entry = (array) $entry) {
           $enc_pw = $entry[0];
        }
        $success = false
                || ($enc_pw == substr($password, 0, 12))
                || ($enc_pw == md5($password))
                || ($enc_pw == crypt($password, substr($enc_pw, 0, 2)))
                || function_exists("sha1") && ($enc_pw == sha1($password));
        $success &= $enc_pw != "*";

        #-- return if it matches
        if ($success) {
           if (isset($entry[1])) {
              $ewiki_ring = (int)($entry[1]);
           } else {
              $ewiki_ring = 2;  //(EWIKI_AUTH_DEFAULT_RING - 1);
           }
           if (empty($ewiki_author)) {
              ($ewiki_author = $entry[2]) or
              ($ewiki_author = $username);
           }
           return($success && ($ewiki_auth_user=$username));
        }
     }
  }

  if ($username || $password) {
     ewiki_log("_auth_userdb: wrong password supplied for user '$username', not verified against any userdb", 3);
     $ewiki_errmsg = "wrong username and/or password";
#     ewiki_auth($uu, $uu, $uu, $uu, 2);
  }
  return(false);
}





/*  reads all files from "./init-pages/" into the database,
    when ewiki is run for the very first time and the FrontPage
    does not yet exist in the database
*/
function ewiki_eventually_initialize(&$id, &$data, &$action) {

   global $USER;

   #-- initialize database only if frontpage missing
   if (($id==EWIKI_PAGE_INDEX) && ($action=="edit") && empty($data["version"])) {

      ewiki_database("INIT", array());
#### BEGIN MOODLE CHANGE
      $path=EWIKI_INIT_PAGES;
      if (!empty($path)) {
        if ($dh = @opendir($path=EWIKI_INIT_PAGES)) {
         while (false !== ($filename = readdir($dh))) {
#### MOODLE CHANGE TO SOLVE BUG #3830. Original doesn't support dots in names.
    //Orig->if (preg_match('/^(['.EWIKI_CHARS_U.']+['.EWIKI_CHARS_L.']+\w*)+/', $filename)) {
            if ($filename == clean_filename($filename) && !is_dir($path.'/'.$filename)) {
#### END OF MOODLE CHANGE TO SOLVE BUG #3830. Original doesn't support dots in names.
               $found = ewiki_database("FIND", array($filename));
               if (! $found[$filename]) {
                  $content = implode("", file("$path/$filename"));
                  ewiki_scan_wikiwords($content, $ewiki_links, "_STRIP_EMAIL=1");
                  $refs = "\n\n" . implode("\n", array_keys($ewiki_links)) . "\n\n";
                  $save = array(
                     "id" => "$filename",
                     "version" => "1",
                     "flags" => "1",
                     "content" => $content,
                     "author" => ewiki_author("ewiki_initialize"),
                     "userid" => $USER->id,
                     "refs" => $refs,
                     "lastmodified" => filemtime("$path/$filename"),
                     "created" => filectime("$path/$filename")   // (not exact)
                  );
                  ewiki_database("WRITE", $save);
               }
            }
         }
         closedir($dh);
        }
        else {
         echo "<b>ewiki error</b>: could not read from directory ". realpath($path) ."<br />\n";
        }
      }
#### END MOODLE CHANGE

      #-- try to view/ that newly inserted page
      if ($data = ewiki_database("GET", array("id"=>$id))) {
         $action = "view";
      }
   }
}




#---------------------------------------------------------------------------



########     ###    ########    ###    ########     ###     ######  ########
########     ###    ########    ###    ########     ###     ######  ########
##     ##   ## ##      ##      ## ##   ##     ##   ## ##   ##    ## ##
##     ##   ## ##      ##      ## ##   ##     ##   ## ##   ##    ## ##
##     ##  ##   ##     ##     ##   ##  ##     ##  ##   ##  ##       ##
##     ##  ##   ##     ##     ##   ##  ##     ##  ##   ##  ##       ##
##     ## ##     ##    ##    ##     ## ########  ##     ##  ######  ######
##     ## ##     ##    ##    ##     ## ########  ##     ##  ######  ######
##     ## #########    ##    ######### ##     ## #########       ## ##
##     ## #########    ##    ######### ##     ## #########       ## ##
##     ## ##     ##    ##    ##     ## ##     ## ##     ## ##    ## ##
##     ## ##     ##    ##    ##     ## ##     ## ##     ## ##    ## ##
########  ##     ##    ##    ##     ## ########  ##     ##  ######  ########
########  ##     ##    ##    ##     ## ########  ##     ##  ######  ########




/*  wrapper
*/
function ewiki_database($action, $args, $sw1=0, $sw2=0, $pf=false) {

   #-- normalize (fetch bad parameters)
   if (($action=="GET") && !is_array($args) && is_string($args)) {
      $args = array("id" => $args);
   }

   #-- treat special
   switch ($action) {

      case "GETALL":
         $args = array_unique(@array_merge($args, array("flags", "version")));
         $args = array_diff($args, array("id"));
         break;

      case "SEARCH":
#         unset($args["version"]);
#         unset($args["flags"]);
         break;

      default:
         break;
   }

   #-- handle {meta} sub array as needed
   if (is_array(@$args["meta"])) {
      $args["meta"] = serialize($args["meta"]);
   }

   #-- database plugin
   if (($pf) || ($pf = @$GLOBALS["ewiki_plugins"]["database"][0])) {
      $r = $pf($action, $args, $sw1, $sw2);
   }
   else {
      ewiki_log("DB layer: no backend!", 0);
      $r = false;
   }

   #-- database layer generation 2 abstraction
   if (is_array($r) && (($action=="SEARCH") || ($action=="GETALL"))) {
      $z = new ewiki_dbquery_result(array_keys($args));
      foreach ($r as $id=>$row) {
         $z->add($row);
      }
      $r = $z;
   }

   #-- extract {meta} sub array
   if (is_array($r) && !is_array(@$r["meta"]) && strlen(@$r["meta"])) {
      $r["meta"] = unserialize($r["meta"]);
   }

   return($r);
}



/*  returned for SEARCH and GETALL queries, as those operations are
    otherwise too memory exhaustive
*/
class ewiki_dbquery_result {

   var $keys = array();
   var $entries = array();
   var $buffer = EWIKI_DBQUERY_BUFFER;
   var $size = 0;

   function ewiki_dbquery_result($keys) {
      $keys = @array_merge($keys, array(-50=>"id", "version", "flags"));
      $this->keys = array_unique($keys);
   }

   function add($row) {
      if (is_array($row)) {
         if ($this->buffer) {
            $this->size += strlen(serialize($row));
            $this->buffer = $this->size <= EWIKI_DBQUERY_BUFFER;
         }
         else {
            $row = $row["id"];
         }
      }
      $this->entries[] = $row;
   }

   function get($all=0, $flags=0x00) {
      $row = array();

      $prot_hide = ($flags&0x0020) && EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING;
      do {
         if (count($this->entries)) {

            #-- fetch very first entry from $entries list
            $r = array_shift($this->entries);

            #-- finish if buffered entry
            if (is_array($r) && !$all) {
               $row = $r;
            }
            #-- else refetch complete entry from database
            else {
               if (is_array($r)) {
                  $r = $r["id"];
               }
               $r = ewiki_database("GET", array("id"=>$r));
               if (!$all) {
                  foreach ($this->keys as $key) {
                     $row[$key] = $r[$key];
                  }
               } else {
                  $row = $r;
               }
            }
            unset($r);
         }
         else {
            return(NULL);  // no more entries
         }

         #-- expand {meta} field
         if (is_array($row) && is_string(@$row["meta"])) {
            $row["meta"] = unserialize($row["meta"]);
         }

         #-- drop unwanted results
         if ($prot_hide && !ewiki_auth($row["id"], $row, $ewiki_action)) {
            $row = array();
         }
      } while ($prot_hide && empty($row));

      return($row);
   }

   function count() {
      return(count($this->entries));
   }
}



/*  MySQL database backend
    (default)
    Note: this is of course an abuse of the relational database scheme,
    but neccessary for real db independence and abstraction
*/
function ewiki_database_mysql($action, &$args, $sw1, $sw2) {

   #-- reconnect to the database (if multiple are used)
   #<off>#  mysql_ping($GLOBALS["db"]);

   #-- result array
   $r = array();

   switch($action) {

      /*  Returns database entry as array for the page whose name was given
          with the "id" key in the $args array, usually fetches the latest
          version of a page, unless a specific "version" was requested in
          the $args array.
      */
      case "GET":
         $id = "'" . mysql_escape_string($args["id"]) . "'";
         ($version = 0 + @$args["version"]) and ($version = "AND (version=$version)") or ($version="");
         $result = mysql_query("SELECT * FROM " . EWIKI_DB_TABLE_NAME
            . " WHERE (pagename=$id) $version  ORDER BY version DESC  LIMIT 1"
         );
         if ($result && ($r = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $r["id"] = $r["pagename"];
            unset($r["pagename"]);
         }
         if (strlen($r["meta"])) {
            $r["meta"] = @unserialize($r["meta"]);
         }
         break;



      /*  Increases the hit counter for the page name given in $args array
          with "id" index key.
      */
      case "HIT":
         mysql_query("UPDATE " . EWIKI_DB_TABLE_NAME . " SET hits=(hits+1) WHERE pagename='" . mysql_escape_string($args["id"]) . "'");
         break;



      /*  Stores the $data array into the database, while not overwriting
          existing entries (using WRITE); returns 0 on failure and 1 if
          saved correctly.
      */
      case "OVERWRITE":     // fall-through
         $COMMAND = "REPLACE";

      case "WRITE":
         $args["pagename"] = $args["id"];
         unset($args["id"]);

         if (is_array($args["meta"])) {
            $args["meta"] = serialize($args["meta"]);
         }

         $sql1 = $sql2 = "";
         foreach ($args as $index => $value) {
            if (is_int($index)) {
               continue;
            }
            $a = ($sql1 ? ', ' : '');
            $sql1 .= $a . $index;
            $sql2 .= $a . "'" . mysql_escape_string($value) . "'";
         }

         strlen(@$COMMAND) || ($COMMAND = "INSERT");

         $result = mysql_query("$COMMAND INTO " . EWIKI_DB_TABLE_NAME .
            " (" . $sql1 . ") VALUES (" . $sql2 . ")"
         );

         return($result && mysql_affected_rows() ?1:0);
         break;



      /*  Checks for existence of the WikiPages whose names are given in
          the $args array. Returns an array with the specified WikiPageNames
          associated with values of "0" or "1" (stating if the page exists
          in the database). For images/binary db entries returns the "meta"
          field instead of an "1".
      */
      case "FIND":
         $sql = "";
         foreach (array_values($args) as $id) if (strlen($id)) {
            $r[$id] = 0;
            $sql .= ($sql ? " OR " : "") .
                    "(pagename='" . mysql_escape_string($id) . "')";
         }
         $result = mysql_query($sql = "SELECT pagename AS id, meta FROM " .
            EWIKI_DB_TABLE_NAME . " WHERE $sql "
         );
         while ($result && ($row = mysql_fetch_row($result))) {
            $r[$row[0]] = strpos($row[1], 's:5:"image"') ? $row[1] : 1;
         }
         break;



      /*  Returns an array of __all__ pages, where each entry is made up
          of the fields from the database requested with the $args array,
          e.g. array("flags","meta","lastmodified");
      */
      case "GETALL":
         $result = mysql_query("SELECT pagename AS id, ".
            implode(", ", $args) .
            " FROM ". EWIKI_DB_TABLE_NAME .
            " GROUP BY id, version DESC"
         );
         $r = new ewiki_dbquery_result($args);
         $drop = "";
         while ($result && ($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
               $drop = $i;
               $r->add($row);
            }
         }
         break;



      /*  Returns array of database entries (also arrays), where the one
          specified column matches the specified content string, for example
          $args = array("content" => "text...piece")
          is not guaranteed to only search/return the latest version of a page
      */
      case "SEARCH":
         $field = implode("", array_keys($args));
         $content = strtolower(implode("", $args));
         if ($field == "id") { $field = "pagename"; }

         $result = mysql_query("SELECT pagename AS id, version, flags" .
            (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") .
            " FROM " . EWIKI_DB_TABLE_NAME .
            " WHERE LOCATE('" . mysql_escape_string($content) . "', LCASE($field)) " .
            " GROUP BY id, version DESC"
         );
         $r = new ewiki_dbquery_result(array("id","version",$field));
         $drop = "";
         while ($result && ($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
               $drop = $i;
               $r->add($row);
            }
         }
         break;



      case "DELETE":
         $id = mysql_escape_string($args["id"]);
         $version = $args["version"];
         mysql_query("DELETE FROM " . EWIKI_DB_TABLE_NAME ."
            WHERE pagename='$id' AND version=$version");
         break;



      case "INIT":
         mysql_query("CREATE TABLE " . EWIKI_DB_TABLE_NAME ."
            (pagename VARCHAR(160) NOT NULL,
            version INTEGER UNSIGNED NOT NULL DEFAULT 0,
            flags INTEGER UNSIGNED DEFAULT 0,
            content MEDIUMTEXT,
            author VARCHAR(100) DEFAULT 'ewiki',
            userid INTEGER UNSIGNED DEFAULT 0,
            created INTEGER UNSIGNED DEFAULT ".time().",
            lastmodified INTEGER UNSIGNED DEFAULT 0,
            refs MEDIUMTEXT,
            meta MEDIUMTEXT,
            hits INTEGER UNSIGNED DEFAULT 0,
            PRIMARY KEY id (pagename, version) )
            ");
         echo mysql_error();
         break;

      default:
   }

   return($r);
}

?>
