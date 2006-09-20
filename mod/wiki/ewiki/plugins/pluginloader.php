<?php

/*
   dynamic plugin loading
   ����������������������
   Will load plugins on demand, so they must not be included() one by one
   together with the core script. This is what commonly the "plugin idea"
   suggests, and only has minimal disadvantages.
   - This loader currently only handles "page" and "action" plugins,
     many other extensions must be activated as before (the other ones
     are real functionality enhancements and behaviour tweaks, so this
     approach really made no sense for them).
   - There is no security risk with this plugin loader extension, because
     it allows you to set which of the available plugins CAN be loaded
     on demand (all others must/can be included() as usual elsewhere).
   - This however requires administration of this plugins` configuration
     array, but that is not much more effort than maintaining a bunch of
     include() statements.
   - Is a small degree faster then including multiple plugin script files
     one by one. Alternatively you could also merge (cat, mkhuge) all
     wanted plugins into one script file so you get a speed improvement
     against multiple include() calls.
   - Use tools/mkpluginmap to create the initial plugin list.
*/



$ewiki_plugins["dl"]["action"] = array(
    "view" => array("", "", 0),
    "links" => array("", "", 0),
    "info" => array("", "", 0),
#    "edit" => array("spellcheck.php", "", 0),
#    "calendar" => array("calendar.php", "ewiki_page_calendar", 0),
#    "addpost" => array("contrib/aview_posts.php", "ewiki_add_post", 0),
#    "imageappend" => array("contrib/aview_imgappend.php", "ewiki_action_image_append", 0),
#    "extodo" => array("contrib/action_extracttodo.php", "ewiki_extract_todo", 0),
#    "addthread" => array("contrib/aview_threads.php", "ewiki_add_thread", 0),
#    "control" => array("admin/control.php", "ewiki_action_control_page", 0),
#    "pdf" => array("pdf.php", "ewiki_send_page_as_pdf", 0),
    "diff" => array("diff.php", "ewiki_page_stupid_diff", 0),
#    "diff" => array("diff_gnu.php", "ewiki_page_gnu_diff", 0),
#    "binary" => array("downloads.php", "ewiki_binary", 0),
#    "attachments" => array("downloads.php", "ewiki_action_attachments", 0),
    "like" => array("like_pages.php", "ewiki_page_like", 0),
#    "verdiff" => array("action_verdiff.php", "ewiki_action_verdiff", 0),
);

$ewiki_plugins["dl"]["page"] = array(
#    "PageCalendar" => array("calendar.php", "ewiki_page_calendar", 0),
#    "PageYearCalendar" => array("calendar.php", "ewiki_page_year_calendar", 0),
#    "WikiNews" => array("contrib/page_wikinews.php", "ewiki_page_wikinews", 0),
#    "WikiDump" => array("contrib/page_wikidump.php", "ewiki_page_wiki_dump_tarball", 0),
#    "README" => array("contrib/page_README.php", "ewiki_page_README", 0),
#    "README.de" => array("contrib/page_README.php", "ewiki_page_README", 0),
#    "plugins/auth/README.auth" => array("contrib/page_README.php", "ewiki_page_README", 0),
#    "Fortune" => array("contrib/page_fortune.php", "ewiki_page_fortune", 0),
#    "ScanDisk" => array("contrib/page_scandisk.php", "ewiki_page_scandisk", 0),
    "InterWikiMap" => array("contrib/page_interwikimap.php", "ewiki_page_interwikimap", 0),
#    "WikiUserLogin" => array("contrib/page_wikiuserlogin.php", "ewiki_page_wikiuserlogin", 0),
#    "PhpInfo" => array("contrib/page_phpinfo.php", "ewiki_page_phpinfo", 0),
#    "ImageGallery" => array("contrib/page_imagegallery.php", "ewiki_page_image_gallery", 0),
#    "SinceUpdatedPages" => array("contrib/page_since_updates.php", "ewiki_page_since_updates", 0),
#    "TextUpload" => array("contrib/page_textupload.php", "ewiki_page_textupload", 0),
    "HitCounter" => array("contrib/page_hitcounter.php", "ewiki_page_hitcounter", 0),
#    "SearchCache" => array("admin/page_searchcache.php", "ewiki_cache_generated_pages", 0),
#    "SearchAndReplace" => array("admin/page_searchandreplace.php", "ewiki_page_searchandreplace", 0),
#    "FileUpload" => array("downloads.php", "ewiki_page_fileupload", 0),
#    "FileDownload" => array("downloads.php", "ewiki_page_filedownload", 0),
    "PowerSearch" => array("page_powersearch.php", "ewiki_page_powersearch", 0),
#    "AboutPlugins" => array("page_aboutplugins.php", "ewiki_page_aboutplugins", 0),
    "OrphanedPages" => array("page_orphanedpages.php", "ewiki_page_orphanedpages", 0),
    "PageIndex" => array("page_pageindex.php", "ewiki_page_index", 0),
    "RandomPage" => array("page_randompage.php", "ewiki_page_random", 0),
    "WantedPages" => array("page_wantedpages.php", "ewiki_page_wantedpages", 0),
    "WordIndex" => array("page_wordindex.php", "ewiki_page_wordindex", 0),
);


#-- plugin glue
$ewiki_plugins["view_init"][] = "ewiki_dynamic_plugin_loader";


function ewiki_dynamic_plugin_loader(&$id, &$data, &$action) {

   global $ewiki_plugins, $ewiki_id, $ewiki_title, $ewiki_t,
          $ewiki_ring, $ewiki_author, $ewiki_config, $ewiki_auth_user,
          $ewiki_action;

   #-- check for entry
   if (empty($ewiki_plugins["page"][$id])) {
      $load = $ewiki_plugins["dl"]["page"][$id];
   }
   elseif (empty($ewiki_plugins["action"][$action])) {
      $load = $ewiki_plugins["dl"]["action"][$action];
   }

   #-- load plugin
   if ($load) {
      if (!is_array($load)) {
         $load = array($load, "");
      }
      if (!($pf=$load[1]) || !function_exists($pf)) {
         include(dirname(__FILE__)."/".$load[0]);
      }
   }

   #-- fake static pages
   foreach ($ewiki_plugins["dl"]["page"] as $name) {
      if (empty($ewiki_plugins["page"][$name])) {
         $ewiki_plugins["page"][$name] = "ewiki_dynamic_plugin_loader";
      }
   }

   #-- show action links
   foreach ($ewiki_plugins["dl"]["action"] as $action=>$uu) {
      foreach ($ewiki_config["dl"]["action_links"] as $where) {
         if ($title = $ewiki_config["dl"]["action_links"][$where][$action]) {
            $ewiki_config["action_links"][$where][$action] = $title;
         }
      }
   }

   return(NULL);
}


?>