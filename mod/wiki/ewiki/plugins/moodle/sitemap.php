<?php // $Id$

/*
    This plugin will create a sitemap rooted at the given location
    Written By: Jeffrey Engleman
*/

define("EWIKI_PAGE_SITEMAP", "SiteMap");
define("EWIKI_SITEMAP_DEPTH", 10);
$ewiki_t["en"]["INVALIDROOT"] = "You are not authorized to access the current root page so no sitemap can be created.";
$ewiki_t["en"]["SMFOR"] = "Site map for ";
$ewiki_t["en"]["VIEWSMFOR"] = "View site map for ";
$ewiki_plugins["page"][EWIKI_PAGE_SITEMAP]="ewiki_page_sitemap";
$ewiki_plugins["action"]['sitemap']="ewiki_page_sitemap";

if(!isset($ewiki_config["SiteMap"]["RootList"])){
  $ewiki_config["SiteMap"]["RootList"]=array(EWIKI_PAGE_INDEX);
}

/* 
  populates an array with all sites the current user is allowed to access
  calls the sitemap creation function.
  returns the sitemap to be displayed.
*/
function ewiki_page_sitemap($id=0, $data=0, $action=0){
  global $ewiki_config;

  //**code hijacked from page_pageindex.php**
  //creates a list of all of the valid wiki pages in the site
  $str_null=NULL;

  $a_validpages=ewiki_valid_pages(0,1);
  
  //**end of hijacked code**
  //$time_end=getmicrotime();

  //creates the title bar on top of page 
  if($id == EWIKI_PAGE_SITEMAP){
    $o = ewiki_make_title($id, ewiki_t($id), 2);  

    foreach($ewiki_config["SiteMap"]["RootList"] as $root){
      if(isset($a_validpages[$root])){
        $valid_root=TRUE;
        $str_rootid=$root;
        break;
      }
    }
    
  }else{
    $o = ewiki_make_title($id, ewiki_t("SMFOR")." ".$id, 2);    
    if(isset($a_validpages[$id])){
      $valid_root=TRUE;
      $str_rootid=$id;
    }    
  }

  $o .= "<p>".ewiki_t("VIEWSMFOR")." ";

  foreach($ewiki_config["SiteMap"]["RootList"] as $root){
    if(isset($a_validpages[$root])){
      $o.='<a href="'.ewiki_script('sitemap/',$root).'">'.$root.'</a> ';
    }
  }
  
  $o.="</p>";

  //checks to see if the user is allowed to view the root page
  if(!isset($a_validpages[$str_rootid])){
    $o .= ewiki_t("INVALIDROOT");
    return $o;
  }
  
  //$timesitemap=getmicrotime();
  $a_sitemap=ewiki_sitemap_create($str_rootid, $a_validpages, EWIKI_SITEMAP_DEPTH);

  $timer=array();
  $level=-1;
  $fordump=0;
  $str_formatted="<ul>\n<li><a href=\"".EWIKI_SCRIPT.$str_rootid."\">".$str_rootid."</a></li>";
  $fin_level=format_sitemap($a_sitemap, $str_rootid, $str_formatted, $level, $timer, $fordump);
  $str_formatted.="</ul>".str_pad("", $fin_level*6, "</ul>\n");
  $o.=$str_formatted;
  
  //$timesitemap_end=getmicrotime();
  
  //$o.="GetAll: ".($time_end-$time)."\n";
  //$o.="SiteMap: ".($timesitemap_end-$timesitemap)."\n";
  //$o.="Total: ".($timesitemap_end-$time);
  
  
  return($o);
    
}

function ewiki_valid_pages($bool_allowimages=0, $virtual_pages=0){
  //$time=getmicrotime();
  global $ewiki_plugins;
  $result = ewiki_database("GETALL", array("flags", "refs", "meta"));
  while ($row = $result->get()) {
    if (EWIKI_PROTECTED_MODE && EWIKI_PROTECTED_MODE_HIDING && !ewiki_auth($row["id"], $str_null, "view")) {
      continue;
    }   
    
    $isbinary= ($row["meta"]["class"]=="image"||$row["meta"]["class"]=="file")?true:false;
    
    if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT || ($bool_allowimages ? $isbinary : 0)) {
      $temp_refs=explode("\n",$row["refs"]);
      foreach($temp_refs as $key => $value) {
        if(empty($value)) {
          unset($temp_refs[$key]);
        }
      }
      if($isbinary){
        $a_validpages[$row["id"]]=$temp_array=array("refs" => $temp_refs, "type" => $row["meta"]["class"], "touched" => FALSE);
      } else {
        $a_validpages[$row["id"]]=$temp_array=array("refs" => $temp_refs, "type" => "page", "touched" => FALSE);
      }
      unset($temp_refs);
    }
  }

  if($virtual_pages){
    #-- include virtual pages to the sitemap.
    $virtual = array_keys($ewiki_plugins["page"]);
    foreach($virtual as $vp){
      if(!EWIKI_PROTECTED_MODE || !EWIKI_PROTECTED_MODE_HIDING || ewiki_auth($vp, $str_null, "view")){
        $a_validpages[$vp]=array("refs" => array(), "type" => "page", "touched" => FALSE);
      }
    }
  }
  return $a_validpages;
}

/*
  Adds each of the pages in the sitemap to an HTML list.  Each site is a clickable link.
*/
function format_sitemap($a_sitemap, $str_rootpage, &$str_formatted, &$prevlevel, &$timer, &$fordump){

  //get all children of the root format them and store in $str_formatted array
  $a_sitemap[$str_rootpage]["child"]= is_array($a_sitemap[$str_rootpage]["child"])?$a_sitemap[$str_rootpage]["child"]:array();
  if($a_sitemap[$str_rootpage]["child"]){
    while($str_child = current($a_sitemap[$str_rootpage]["child"])){
      $str_mark="";
      if($a_sitemap[$str_rootpage]["level"]>$prevlevel){
        $str_mark="<ul>\n";
      } 
      elseif ($a_sitemap[$str_rootpage]["level"]<$prevlevel){
        //markup length is 6 characters
        $str_mark=str_pad("", ($prevlevel-$a_sitemap[$str_rootpage]["level"])*6, "</ul>\n");
      }
      $prevlevel=$a_sitemap[$str_rootpage]["level"];
      if($fordump){
        $str_formatted.=($str_mark."<li><a href=\"".preg_replace(EWIKI_DUMP_FILENAME_REGEX, "", urlencode($str_child)).".html\">".$str_child."</a></li>\n");
      } else {
        $str_formatted.=($str_mark."<li><a href=\"".EWIKI_SCRIPT.$str_child."\">".$str_child."</a></li>\n");
      }
      array_shift($a_sitemap[$str_rootpage]["child"]);
      format_sitemap($a_sitemap, $str_child, $str_formatted, $prevlevel, $timer, $fordump);
    }
    return ($prevlevel+1);
  }
}


/*
  gets all children of the given root and stores them in the $a_children array
*/
function ewiki_page_listallchildren($str_root, &$a_children, &$a_sitemap, &$a_validpages, $i_level, $i_maxdepth, $i_flatmap){
  if(($i_level<$i_maxdepth) && is_array($a_validpages[$str_root]["refs"])){ //controls depth the sitemap will recurse into
    foreach($a_validpages[$str_root]["refs"] as $str_refs){
      if($str_refs){ //make sure $str_refs contains a value before doing anything
        if(isset($a_validpages[$str_refs])){ //test page validity
          if(!$a_validpages[$str_refs]["touched"]){ //check to see if page already exists
            if($i_flatmap){
              $a_sitemap[]=$str_refs;
            }
            $a_validpages[$str_refs]["touched"]=TRUE; //mark page as displayed
            $a_children[$str_refs]="";
            $a_currchildren[]=$str_refs;
          }
        }
      }
    }
    if(!$i_flatmap){
      if($a_currchildren){
        $a_sitemap[$str_root]=array("level" => $i_level, "child" => $a_currchildren);
      } else {
        $a_sitemap[$str_root]=array("level" => $i_level);
      }
    } 
  }
}   


/*
  Creates the sitemap. And sends the data to the format_sitemap function.
  Returns the HTML formatted sitemap.
*/
function ewiki_sitemap_create($str_rootid, $a_validpages, $i_maxdepth, $i_flatmap=0){
  //map starts out with a depth of 0
  $i_depth=0;
  $forcelevel=FALSE;

  //create entry for root in the sitemap array
  if(!$i_flatmap){
    $a_sitemap[$str_rootid]=array("parent" => "", "level" => $i_depth, "child" => $str_rootid);
  } else {
    $a_sitemap[]=$str_rootid;
  }
  //mark the root page as touched
  $a_validpages[$str_rootid]["touched"]=TRUE;
  //list all of the children of the root
  ewiki_page_listallchildren($str_rootid, $a_children, $a_sitemap, $a_validpages, $i_depth, $i_maxdepth, $i_flatmap);
  $i_depth++;    
    
  if($a_children){
    end($a_children);
    $str_nextlevel=key($a_children);
    reset($a_children);
    
    while($str_child = key($a_children)){
      //list all children of the current child
      ewiki_page_listallchildren($str_child, $a_children, $a_sitemap, $a_validpages, $i_depth, $i_maxdepth, $i_flatmap);
      
      //if the child is the next level marker...
      if($str_child==$str_nextlevel){
        //increment the level counter
        $i_depth++;
        //determine which child marks the end of this level
        end($a_children);
        $str_nextlevel=key($a_children);
        //reset the array counter to the beginning of the array
        reset($a_children);
        //we are done with this child...get rid of it 
      }
      array_shift($a_children);
    }
  }

  return $a_sitemap;
}
?>
