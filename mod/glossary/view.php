<?PHP  // $Id$

/// This page prints a particular instance of glossary

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID, or

    optional_variable($l);     // letter to look for
    optional_variable($eid);  // Entry ID
    optional_variable($search, "");  // search string
    optional_variable($includedefinition);  // include definition in search function?

    optional_variable($currentview);  // browsing entries by categories?
    optional_variable($cat);  // categoryID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);
    if ( !$course->visible ) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id");

    $search = trim(strip_tags($search));
    if ($search and !$entryid ) {
	  $l = "";
        $searchterms = explode(" ", $search);    // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            }
        }
        $search = trim(implode(" ", $searchterms));
        $currentview = "";
    } elseif ( $eid ) {
         $search = "";
    }
    if ($l == "" and $search == "" and ($eid == "" or $eid == 0) ) {
    		$l = "A";
    } elseif ( $eid ) {
          $l = "";
    }
    if ( $currentview ) {
          $l = "";
          $currentview = strtolower($currentview);
          if ( $currentview ) {
               if ( $cat ) {
                    $category = get_record("glossary_categories","id",$cat);
               }
               if ( !$category ) {
                    $cat = "";
               }
          }
    }

/// Printing the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

     $strglossaries   = get_string("modulenameplural", "glossary");
     $strglossary     = get_string("modulename", "glossary");
     $strallcategories= get_string("allcategories", "glossary");
     $straddentry     = get_string("addentry", "glossary");
     $strnoentries    = get_string("noentries", "glossary");

    print_header("$course->shortname: $glossary->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> $glossary->name",
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary),
                  navmenu($course, $cm));

/// Printing the header of the glossary

    echo "<p><center><b>$glossary->name<p>" ;

    print_simple_box_start("center", "70%");
          echo "<table width=100% border=0><tr><td width=50% align=right>";
     	?>
     	<form method="POST" action="view.php">
     	  <? p(get_string("searchconcept","glossary")) ?> <input type="text" name="search" size="20" value=""> <br><? p(get_string("searchindefinition","glossary")) ?> <input type="checkbox" name="includedefinition" value="1">
     	  <input type="submit" value="Search" name="searchbutton">
     	  <input type="hidden" name="id" value="<? p($cm->id) ?>">
     	</form>
     	<?
          echo "</td><td valign=top align=right width=50%>";
           if (isteacher($course->id) or $glossary->studentcanpost) {
              $options = array ("id" => "$cm->id");
              print_single_button("edit.php", $options, $straddentry );
           }
           echo "</td></tr></table>";
    print_simple_box_end();

    echo "<p align=center>";
     $data[0]->link = "view.php?id=$id";
     $data[0]->caption = get_string("standardview","glossary");
     
     $data[1]->link = "view.php?id=$id&currentview=categories";
     $data[1]->caption = get_string("categoryview","glossary");

     if ( $currentview ) {
          $CurrentTab = 1;
     } else {
          $CurrentTab = 0;
     }
     print_tabbed_table_start($data, $CurrentTab, $tCFG);
     echo "<center>";
     if ( $currentview ) {
         glossary_print_categories_menu($course, $cm, $glossary, $category);
     } else {
         glossary_print_alphabet_menu($cm, $glossary, $l);
     
         if ($l) {
     		$CurrentLetter = "";
     	} elseif( $search ) {
     		echo "<h3>" . get_string("search") . ": $search</h3>";
     	}

          echo "<hr>";
     }
/// Printing the entries

	if ( $search ) {	// looking for a term
		$allentries = glossary_search_entries($searchterms, $glossary, $includedefinition);
	} elseif ( $eid ) {	// looking for an entry
		$allentries = get_records("glossary_entries", "id", $eid);
	} else {			// looking for terms that begin with a specify letter
		$allentries = get_records("glossary_entries", "glossaryid", $glossary->id,"concept ASC");
	}

    if ( $allentries ) {
        $DumpedDefinitions= 0;
        foreach ($allentries as $entry) {
            $DumpToScreen = 0;
            $FirstLetter = strtoupper( substr(ltrim($entry->concept),0,strlen($l) ) );
            if ( $l ) {
                if ( $l == "ALL" or $FirstLetter == $l) {
                    if ( $CurrentLetter != $FirstLetter[0] ) {
                         $CurrentLetter = $FirstLetter[0];

	                    if ( $glossary->displayformat == 0 ) {
	                        if ( $DumpedDefinitions > 0) {
	                            echo "</table></center><p>";
	                        }
	                        echo "\n<center><TABLE BORDER=0 CELLSPACING=0 width=95% valign=top cellpadding=10><tr><td align=center BGCOLOR=\"$THEME->cellheading2\">";
	                    }
	                    if ( $l == "ALL" ) {
                              echo "<b>$CurrentLetter</b>";
                         }

	                    if ( $glossary->displayformat == 0 ) {
	                        echo "\n</center></td></tr></TABLE></center>";
	                        if ( $DumpedDefinitions > 0) {
	                    		echo "\n<center><TABLE BORDER=1 CELLSPACING=0 width=95% valign=top cellpadding=10>";
	                    	}
	                    }
                 	}
                 	$DumpToScreen = 1;
                } elseif ( $l == "SPECIAL" and ord($FirstLetter) != ord("Ñ") and (ord($FirstLetter)<ord("A") or ord($FirstLetter)>ord("Z")) ) {
                   $DumpToScreen = 1;
                }
            } else {
                if ( $currentview ) {
                    if ( $category ) {
                         if ( record_exists("glossary_entries_categories","entryid",$entry->id, "categoryid",$category->id) ) {
                              $DumpToScreen = 1;
                         }
                    } else {
                         if ( ! record_exists("glossary_entries_categories","entryid",$entry->id) ) {
                              $DumpToScreen = 1;
                         }
                    }
                } else {
                    $DumpToScreen = 1;
                }
            }

            if ( $DumpToScreen ) {
                 $DumpedDefinitions++;

                 $concept = $entry->concept;
                 $definition = $entry->definition;

                 if ( $DumpedDefinitions == 1 ) {
	                 if ( $glossary->displayformat == 0 ) {
	                    echo "\n<center><TABLE BORDER=1 CELLSPACING=0 width=95% valign=top cellpadding=10>";
	                 }
                 }
                 if ($search) {
                       $entry->concept = highlight($search,$concept);
                       $entry->definition = highlight($search,$definition);
                 }
	             glossary_print_entry($course, $cm, $glossary, $entry,$currentview,$cat);

                 if ( $glossary->displayformat != 0 ) {
                 	echo "<p>";
                 }
            }
        }
    }
	if ( ! $DumpedDefinitions ) {
	   	print_simple_box_start("center", "70%","$THEME->cellheading");
		if ( !$search ) {
			echo "<center>$strnoentries</center>";
		} else {
			echo "<center>";
			print_string("searchhelp");
			echo "</center>";
		}
		print_simple_box_end();
	} else {
	    if ( $glossary->displayformat == 0 ) {
	        echo "\n</TABLE></center>";
	    }
	}

     echo "</center>";
     print_tabbed_table_end();

/// Finish the page
    print_footer($course);

?>
