<?PHP  // $Id$

/// This page prints a particular instance of glossary

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID, or

    optional_variable($l);     // letter to look for
    optional_variable($eid);  // Entry ID
    optional_variable($search, "");  // search string
    optional_variable($includedefinition);  // include definition in search function?

    if ($l == "" and $search == "" and $eid == "") {
    		$l = "A";
    }

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
    } elseif ( $eid ) {
	  $search = "";
    }

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

    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id");

/// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strglossaries   = get_string("modulenameplural", "glossary");
    $strglossary     = get_string("modulename", "glossary");
    $strselectletter = get_string("selectletter", "glossary");
    $strspecial      = get_string("special", "glossary");
    $strallentries   = get_string("allentries", "glossary");
    $strnoentries    = get_string("noentries", "glossary");
    $straddentry     = get_string("addentry", "glossary");
    $streditentry    = get_string("editentry", "glossary");
    $strdeleteentry  = get_string("deleteentry", "glossary");

    print_header("$course->shortname: $glossary->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> $glossary->name",
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary),
                  navmenu($course, $cm));

/// Print the main part of the page

/// Printing the navigation links (letters to look for)

    echo "<p><center><b>$glossary->name<p>" ;

    if ( !$course->visible ) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    print_simple_box_start("center", "70%");
      echo "<CENTER>$strselectletter";

	?>
	<form method="POST" action="view.php">
	  <? p(get_string("searchconcept","glossary")) ?> <input type="text" name="search" size="20" value=""> <br><? p(get_string("searchindefinition","glossary")) ?> <input type="checkbox" name="includedefinition" value="1">
	  <input type="submit" value="Search" name="searchbutton">
	  <input type="hidden" name="id" value="<? p($cm->id) ?>">
	</form>
	<?

      echo "<p><a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$id&l=SPECIAL\">$strspecial</a> | ";

      $middle = (int) ( (ord("Z") - ord("A")) / 2) ;
      for ($i = ord("A"); $i <= ord("Z"); $i++) {
         echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$id&l=" . chr($i) . "\">" . chr($i) . "</a>";
         if ( $i - ord("A") - 1 != $middle ) {
            echo " | ";
         } else {
            echo "<br>";
         }

         if ($i == ord("N") ) {
            echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$id&l=Ñ\">Ñ</a> | ";
         }

      }

      echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$id&l=ALL\">$strallentries</a></p>";

      if (isteacher($course->id) or $glossary->studentcanpost) {
         $options = array ("id" => "$cm->id");
         echo "<CENTER>";
         print_single_button("edit.php", $options, $straddentry );
         echo "</CENTER>";
      }

    print_simple_box_end();

    echo "<p align=center>";
    if ($l) {
		$CurrentLetter = "";
		if ($l == "ALL" or $l == "SPECIAL") {
			if ( $l == "ALL" ) {
	 			echo "<h2>$strallentries</h2><p>";
			} elseif ($l == "SPECIAL") {
	 			echo "<h2>$strspecial</h2><p>";
			}
		}
	} elseif( $search ) {
		echo get_string("search") . ": $search";
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
            $FirstLetter = strtoupper( ltrim( $entry->concept[0] ) );
            if ( $l ) {
                if ( $l == "ALL" or $FirstLetter == $l) {
                    if ( $CurrentLetter != $FirstLetter ) {
                         $CurrentLetter = $FirstLetter;

	                    if ( $glossary->displayformat == 2 ) {
	                        if ( $DumpedDefinitions != 1) {
	                            echo "</table></center><p>";
	                        }
	                        echo "\n<center><TABLE BORDER=0 CELLSPACING=0 width=70% valign=top cellpadding=10><tr><td align=center BGCOLOR=\"$THEME->cellheading\"><b>";
	                    }
	                    echo $CurrentLetter;

	                    if ( $glossary->displayformat == 2 ) {
	                        echo "\n</b></center></td></tr></TABLE></center>";
	                        if ( $DumpedDefinitions != 1) {
	                    		echo "\n<center><TABLE BORDER=1 CELLSPACING=0 width=70% valign=top cellpadding=10>";
	                    	}
	                    }
                 	}
                 	$DumpToScreen = 1;
                } elseif ( $l == "SPECIAL" and ord($FirstLetter) != ord("Ñ") and (ord($FirstLetter)<ord("A") or ord($FirstLetter)>ord("Z")) ) {
                   $DumpToScreen = 1;
                }
            } else {
                $DumpToScreen = 1;
            }

            if ( $DumpToScreen ) {
                 $DumpedDefinitions++;

                 $concept = $entry->concept;
                 $definition = $entry->definition;

                 if ( $DumpedDefinitions == 1 ) {
	                 if ( $glossary->displayformat == 2 ) {
	                    echo "\n<center><TABLE BORDER=1 CELLSPACING=0 width=70% valign=top cellpadding=10>";
	                 }
                 }
                 if ($search) {
                       $entry->concept = highlight($search,$concept);
                       $entry->definition = highlight($search,$definition);
                 }
	             glossary_print_entry($course, $cm, $glossary, $entry);

				 if ( $glossary->displayformat != 2 ) {
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
	    if ( $glossary->displayformat == 2 ) {
	        echo "\n</TABLE></center>";
	    }
	}

/// Finish the page
    print_footer($course);

?>