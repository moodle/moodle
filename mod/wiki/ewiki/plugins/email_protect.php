<?php

 # This plugin protects email addresses from getting seen by spambots,
 # by the cost of additonal effort for real persons, who really want
 # to mail someone.
 #
 # It is __really safe__ because it protects addresses with an request
 # <FORM> before the real email address gets shown on a page (it seems
 # impossible to me, that there are already all that intelligent spambots
 # available, which can automatically fill out a <form> to access the
 # following page).
 # The 'cipher' method is really unimportant, when it comes to tricking
 # automated harvesters.
 #
 # Additionally it generates faked/trap email addresses to annoy the
 # marketing mafia.
 

 #-- change these from time to time:
 define("EWIKI_PAGE_EMAIL", "ProtectedEmail");
 define("EWIKI_UP_ENCEMAIL", "encoded_email");
 define("EWIKI_UP_NOSPAMBOT", "i_am_no_spambot");
 define("EWIKI_UP_REQUESTLV", "rl");
 define("EWIKI_FAKE_EMAIL_LOOP", 5);
 $ewiki_config["feedbots_tarpits"] = "@spamassassin.taint.org,@123webhosting.org,@e.mailsiphon.com,@heypete.com,@ncifcrf.gov";
 $ewiki_config["feedbots_badguys"] = "@riaa.com,@whitehouse.gov,@aol.com,@microsoft.com";

 #-- text, translations
 $ewiki_t["en"]["PROTE0"] = "Protected Email Address";
 $ewiki_t["en"]["PROTE1"] = "The email address you've clicked on is protected by this form, so it won't get found by <a href=\"http://google.com/search?q=spambots\">spambots</a> (automated search engines, which crawl the net for addresses just for the entertainment of the marketing mafia).";
 $ewiki_t["en"]["PROTE2"] = "The page you're going to edit contains at least one email address. To protect it we must ensure that no spambot reaches the edit box (with the email address in cleartext).";
 $ewiki_t["en"]["PROTE4"] = "I'm no spambot, really!";
 $ewiki_t["en"]["PROTE5"] = "<b>generate more faked email addresses</b>";
 $ewiki_t["en"]["PROTE6"] = "the email address you've clicked on is:";
 $ewiki_t["en"]["PROTE7"] = "<b>spammers, please eat these:</b>";

 $ewiki_t["de"]["PROTE0"] = "Geschützte EMail-Adresse";
 $ewiki_t["de"]["PROTE1"] = "Die EMail-Adresse, die du angeklickt hast, wird durch dieses Formular vor <a href=\"http://google.com/search?q=spambots\">spambots</a> (automatisierte Suchwerkzeuge, die das Netz zur Freude der MarketingMafia nach Adressen abgrasen) beschützt.";
 $ewiki_t["de"]["PROTE2"] = "Die Seite, die du ändern willst, enthält momentan wenigstens eine EMail-Adresse. Um diese zu schützen müssen wir sicherstellen, daß kein Spambot an die Edit-Box kommt (weil dort die Adresse ja im Klartext steht).";
 $ewiki_t["de"]["PROTE4"] = "Ich bin wirklich kein Spambot!";
 $ewiki_t["de"]["PROTE5"] = "<b>noch mehr fingierte Adressen anzeigen</b>";
 $ewiki_t["de"]["PROTE6"] = "die EMail-Adresse die du angeklickt hast lautet:";
 $ewiki_t["de"]["PROTE7"] = "<b>Liebe Spammer, bitte freßt das:</b>";

 #-- plugin glue
 $ewiki_plugins["link_url"][] = "ewiki_email_protect_link";
 $ewiki_plugins["page"][EWIKI_PAGE_EMAIL] = "ewiki_email_protect_form";
 $ewiki_plugins["edit_hook"][] = "ewiki_email_protect_edit_hook";
 $ewiki_plugins["page_final"][] = "ewiki_email_protect_enctext";



 function ewiki_email_protect_enctext(&$html, $id, $data, $action) {

    $a_secure = array("info", "diff");

    if (in_array($action, $a_secure)) {

       $html = preg_replace('/([-_+\w\d.]+@[-\w\d.]+\.[\w]{2,5})\b/me',
               '"<a href=\"".ewiki_email_protect_encode("\1",2).
                "\">".ewiki_email_protect_encode("\1",0)."</a>"',
               $html);
    }
 }


 /* ewiki_format() callback function to replace mailto: links with
  * encoded redirection URLs
  */
 function ewiki_email_protect_link(&$href, &$title) {

     if (substr($href, 0, 7) == "mailto:") {

         $href = substr($href, 7);

         $href = ewiki_email_protect_encode($href, 2);
         $title = ewiki_email_protect_encode($title, 0);
     }
 }



 /* the edit box for every page must be protected as well - else all
  * mail addresses would still show up in the wikimarkup (cleartext)
  */
 function ewiki_email_protect_edit_hook($id, &$data, &$hidden_postdata) {

    $hidden_postdata[EWIKI_UP_NOSPAMBOT] = 1;

    if (empty($_REQUEST[EWIKI_UP_NOSPAMBOT])
        && strpos($data["content"], "@")
        && preg_match('/\w\w@([-\w]+\.)+\w\w/', $data["content"])   )
    {
       $url = ewiki_script("edit", $id);
       $o = ewiki_email_protect_form($id, $data, "edit", "PROTE2", $url);
       return($o);
    }

    if (!empty($_POST[EWIKI_UP_NOSPAMBOT]) && empty($_COOKIE[EWIKI_UP_NOSPAMBOT]) && EWIKI_HTTP_HEADERS) {
       setcookie(EWIKI_UP_NOSPAMBOT, "grant_access", time()+7*24*3600, "/");
    }

 }



 /* this places a <FORM METHOD="POST"> in between the WikiPage with the
  * encoded mail address URL and the page with the clearly readable
  * mailto: string
  */
 function ewiki_email_protect_form($id, $data=0, $action=0, $text="PROTE1", $url="") {

    if ($url || ($email = @$_REQUEST[EWIKI_UP_ENCEMAIL])) {

          $html = "<h3>" . ewiki_t("PROTE0") . "</h3>\n";

          if (empty($_REQUEST[EWIKI_UP_NOSPAMBOT])) {  #// from GET,POST,COOKIE

             (empty($url)) and ($url = ewiki_script("", EWIKI_PAGE_EMAIL));

             $html .= ewiki_t($text) . "<br /><br /><br />\n";

             $html .= '<form action="' . $url .
                      '" method="POST" enctype="multipart/form-data" encoding="iso-8859-1">';
             $html .= '<fieldset class="invisiblefieldset">';
             $html .= '<input type="hidden" name="'.EWIKI_UP_ENCEMAIL.'" value="' . $email . '" />';
             foreach (array_merge($_GET, $_POST) as $var=>$value) {
                if (($var != "id") && ($var != EWIKI_UP_ENCEMAIL) && ($var != EWIKI_UP_NOSPAMBOT)) {
                   $html .= '<input type="hidden" name="' . s($var) . '" value="' . s($value) . '" />';
                }
             }
             $html .= '<input type="checkbox" name="'.EWIKI_UP_NOSPAMBOT.'" value="true" /> ' . ewiki_t("PROTE4") . '<br /><br />';
             $html .= '<input type="submit" name="go" /></fieldset></form><br /><br />';

             if (EWIKI_FAKE_EMAIL_LOOP) {
                $html .= "\n" . ewiki_t("PROTE7") . "<br />\n";
                $html .= ewiki_email_protect_feedbots();
             }

          }
          else {

             $email = ewiki_email_protect_encode($email, -1);

             $html .= ewiki_t("PROTE6") . "<br />";
             $html .= '<a href="mailto:' . $email . '">' . $email . '</a>';

             if (EWIKI_HTTP_HEADERS && empty($_COOKIE[EWIKI_UP_NOSPAMBOT])) {
                setcookie(EWIKI_UP_NOSPAMBOT, "grant_access", time()+7*24*3600, "/");
             }

          }

    }

    return($html);
 }



 /* security really does not depend on how good "encoding" is, because
  * bots cannot automatically guess that one is actually used
  */
 function ewiki_email_protect_encode($string, $func) {

    switch ($func) {

       case 0:  // garbage shown email address
          if (strpos($string, "mailto:") === 0) {
             $string = substr($string, 7);
          }
          while (($rd = strrpos($string, ".")) > strpos($string, "@")) {
             $string = substr($string, 0, $rd);
          }
          $string = strtr($string, "@.-_", "»·±¯");
          break;

       case 1:  // encode
          $string = str_rot17($string);
          $string = base64_encode($string);
          break;

       case -1:  // decode
          $string = base64_decode($string);
          $string = str_rot17($string);
          break;       

       case 2:  // url
          $string = ewiki_script("", EWIKI_PAGE_EMAIL,
             array(EWIKI_UP_ENCEMAIL => ewiki_email_protect_encode($string, 1))
          );
          break;

    }

    return($string);
 }



 /* this is a non-portable string encoding fucntion which ensures, that
  * encoded strings can only be decoded when requested by the same client
  * or user in the same dialup session (IP address must match)
  * feel free to exchange the random garbage string with anything else
  */
 function str_rot17($string) {
    if (!defined("STR_ROT17")) {
       $i = @$_SERVER["SERVER_SOFTWARE"] .
            @$_SERVER["HTTP_USER_AGENT"] .
            getremoteaddr();
       $i .= 'MxQXF^e-0OKC1\\s{\"?i!8PRoNnljHf65`Eb&A(\':g[D}_|S#~3hG>*9yvdI%<=.urcp/@$ZkqL,TWBw]a;72UzYJ)4mt+ V';
       $f = "";
       while (strlen($i)) {
          if (strpos($f, $i[0]) === false) {
             $f .= $i[0];
          }
          $i = substr($i, 1);
       }
       define("STR_ROT17", $f);
    }
    return(strtr($string, STR_ROT17, strrev(STR_ROT17)));
 }



 /* this function emits some html with random (fake) email addresses
  * and spambot traps
  */
 function ewiki_email_protect_feedbots() {

    global $ewiki_config;

    $html = "";
    srand(time()/17-1000*microtime());

    #-- spamtraps, and companys/orgs fighting for spammers rights
    $domains = explode(",",
       $ewiki_config["feedbots_tarpits"]. "," .$ewiki_config["feedbots_badguys"]
    );
    $traps = explode(" ", "blockme@relays.osirusoft.com simon.templar@rfc1149.net james.bond@ada-france.org anton.dvorak@ada.eu.org amandahannah44@hotmail.com usenet@fsck.me.uk meatcan2@beatrice.rutgers.edu heystupid@artsackett.com listme@dsbl.org bill@caradoc.org spamtrap@spambouncer.org spamtrap@woozle.org gfy@spamblocked.com listme@blacklist.woody.ch tarpit@lathi.net");
    $word_parts = explode(" ", "er an Ma ar on in el en le ll Ca ne ri De Mar Ha Br La Co St Ro ie Sh Mc re or Be li ra Al la al Da Ja il es te Le ha na Ka Ch is Ba nn ey nd He tt ch Ho Ke Ga Pa Wi Do st ma Mi Sa Me he to Car ro et ol ck ic Lo Mo ni ell Gr Bu Bo Ra ia de Jo El am An Re rt at Pe Li Je She Sch ea Sc it se Cha Har Sha Tr as ng rd rr Wa so Ki Ar Bra th Ta ta Wil be Cl ur ee ge ac ay au Fr ns son Ge us nt lo ti ss Cr os Hu We Cor Di ton Ri ke Ste Du No me Go Va Si man Bri ce Lu rn ad da ill Gi Th and rl ry Ros Sta sh To Se ett ley ou Ne ld Bar Ber lin ai Mac Dar Na ve no ul Fa ann Bur ow Ko rs ing Fe Ru Te Ni hi ki yn ly lle Ju Del Su mi Bl di lli Gu ine do Ve Gar ei Hi vi Gra Sto Ti Hol Vi ed ir oo em Bre Man ter Bi Van Bro Col id Fo Po Kr ard ber sa Con ick Cla Mu Bla Pr Ad So om io ho ris un her Wo Chr Her Kat Mil Tre Fra ig Mel od nc yl Ale Jer Mcc Lan lan si Dan Kar Mat Gre ue rg Fi Sp ari Str Mer San Cu rm Mon Win Bel Nor ut ah Pi gh av ci Don ot dr lt ger co Ben Lor Fl Jac Wal Ger tte mo Er ga ert tr ian Cro ff Ver Lin Gil Ken Che Jan nne arr va ers all Cal Cas Hil Han Dor Gl ag we Ed Em ran han Cle im arl wa ug ls ca Ric Par Kel Hen Nic len sk uc ina ste ab err Or Am Mor Fer Rob Luc ob Lar Bea ner pe lm ba ren lla der ec ric Ash Ant Fre rri Den Ham Mic Dem Is As Au che Leo nna rin enn Mal Jam Mad Mcg Wh Ab War Ol ler Whi Es All For ud ord Dea eb nk Woo tin ore art Dr tz Ly Pat Per Kri Min Bet rie Flo rne Joh nni Ce Ty Za ins eli ye rc eo ene ist ev Der Des Val And Can Shi ak Gal Cat Eli May Ea rk nge Fu Qu nie oc um ath oll bi ew Far ich Cra The Ran ani Dav Tra Sal Gri Mos Ang Ter mb Jay les Kir Tu hr oe Tri lia Fin mm aw dy cke itt ale wi eg est ier ze ru sc My lb har ka mer sti br ya Gen Hay a b c d e f g h i j k l m n o p q r s t u v w x y z");
    $word_delims = explode(" ", "0 1 2 3 3 3 4 5 5 6 7 8 9 - - - - - - - _ _ _ _ _ _ _ . . . . . . .");
    $n_dom = count($domains)-1;
    $n_trp = count($traps)-1;
    $n_wpt = count($word_parts)-1;
    $n_wdl = count($word_delims)-1;

    for ($n = 1; $n < EWIKI_FAKE_EMAIL_LOOP; $n++) {

       // email name part
       $m = "";
       while (strlen($m) < rand(3,17)) {
          $a = $word_parts[nat_rand($n_wpt)];
          if (!empty($m)) {
             $a = strtolower($a);
             if (rand(1,9)==5) {
                $m .= $word_delims[rand(0,$n_wdl)];
             }
          }
          $m .= $a;
       }

       // add domain
       switch ($dom = $domains[rand(0, $n_dom)]) {

          case "123webhosting.org":
             $m = strtr(".", "-", getremoteaddr())."-".$_SERVER["SERVER_NAME"]."-".time();
             break;

          default:
       }
       $m .= $dom;

       $html .= '<a href="mailto:'.$m.'">'.$m.'</a>'.",\n";
    }

    $html .= '<a href="mailto:'.$traps[rand(0, $n_trp)].'">'.$traps[rand(0, $n_trp)].'</a>';

    if (($rl = 1 + @$_REQUEST[EWIKI_UP_REQUESTLV]) < EWIKI_FAKE_EMAIL_LOOP) {
       $html .= ",\n" . '<br /><a href="' .
             ewiki_script("", EWIKI_PAGE_EMAIL,
               array(
                  EWIKI_UP_ENCEMAIL=>ewiki_email_protect_encode($m, 1),
                  EWIKI_UP_REQUESTLV=>"$rl"
               )
             ) . '">' . ewiki_t("PROTE5") . '</a><br />' . "\n";
       ($rl > 1) && sleep(3);
    }

    sleep(1);
    return($html);
 }



 function nat_rand($max, $dr=0.5) {
    $x = $max+1;
    while ($x > $max) {
       $x = rand(0, $max * 1000)/100;
       $x = $x * $dr + $x * $x / 2 * (1-$dr) / $max;
    }
    return((int)$x);
 }


?>
