<?PHP  // $Id$

/// This page allows to edit entries categories for a particular instance of glossary

    require_once("../../config.php");
    require_once("lib.php");

     require_variable($id);    // Course Module ID, or
     optional_variable($mode);  // cat
     optional_variable($hook);  // category ID
     optional_variable($action);  // what to do
     optional_variable($usedynalink);  // category ID
     optional_variable($confirm);  // confirm the action

     optional_variable($name);  // confirm the action

     $action = strtolower($action);
    	
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

     if ( !isteacher($course->id) ) {
        error("You must be a teacher to use this page.");
     }

//    add_to_log($course->id, "glossary", "edit categories", "view.php?id=$cm->id", "$glossary->id", $cm->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strglossaries   = get_string("modulenameplural", "glossary");
    $strglossary     = get_string("modulename", "glossary");

    print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> <A HREF=view.php?id=$cm->id&tab=GLOSSARY_CATEGORY_VIEW>$glossary->name</a> -> " . get_string("categories","glossary"),
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary),
                  navmenu($course, $cm));

     if ( $hook ) {
          $category = get_record("glossary_categories","id",$hook);

          if ( $action == "edit" ) {
               if ( $confirm ) {
                    $action = "";
                    $cat->id = $hook;
                    $cat->name = $name;
                    $cat->usedynalink = $usedynalink;

                    if ( !update_record("glossary_categories", $cat) ) {
     				    error("Weird error. The category was not updated.");
     				    redirect("editcategories.php?id=$cm->id");
                    } else {
                        add_to_log($course->id, "glossary", "edit category", "editcategories.php?id=$cm->id", $hook,$cm->id);
                    }
               } else {
                    echo "<p align=\"center\">" . get_string("edit"). " " . get_string("category","glossary") . "<font size=\"3\">";

                    $name = $category->name;
                    $usedynalink = $category->usedynalink;
                    require "editcategories.html";
                    print_footer();
                    die;
               }
          } elseif ( $action == "delete" ) {
               if ( $confirm ) {
               
				    delete_records("glossary_entries_categories","categoryid", $hook);
                    delete_records("glossary_categories","id", $hook);
				
				    print_simple_box_start("center","40%", "#FFBBBB");
				    echo "<center>" . get_string("categorydeleted","glossary") ."</center>";
				    echo "</center>";
				    print_simple_box_end();
				
         			print_footer($course);

                    add_to_log($course->id, "glossary", "delete category", "editcategories.php?id=$cm->id", $hook,$cm->id);
                    
     			    redirect("editcategories.php?id=$cm->id");
               } else {
                    echo "<p align=\"center\">" . get_string("delete"). " " . get_string("category","glossary") . "<font size=\"3\">";

     			    print_simple_box_start("center","40%", "#FFBBBB");
     			    echo "<center><b>$category->name</b><br>";
     			
     			    $num_entries = count_records("glossary_entries_categories","categoryid",$category->id);
                    if ( $num_entries ) {
                         print_string("deletingnoneemptycategory","glossary");
                    }
                    echo "<p>";
                    print_string("areyousuredelete","glossary");
                    
                    ?>
                    <form name="form" method="post" action="editcategories.php">

                    <input type="hidden" name=id 		   value="<?php p($cm->id) ?>">
                    <input type="hidden" name=action      value="delete">
                    <input type="hidden" name=confirm     value="1">
                    <input type="hidden" name=mode         value="<?php echo $mode ?>">
                    <input type="hidden" name=hook         value="<?php echo $hook ?>">
                    <table border=0 widTH=100><tr><td align=right width=50%>
                    <input type="submit" value=" <?php print_string("yes")?> ">
                    </form>
                    </td><td align=left width=50%>
                    <?php
                    unset($options);
                    $options = array ("id" => $id);
                    print_single_button("editcategories.php", $options, get_string("no") );
                    echo "</td></tr></table>";
     			    echo "</center>";
     			    print_simple_box_end();
               }
          }
     } elseif ( $action == "add" ) {
          if ( $confirm ) {
               $dupcategory = get_record("glossary_categories","lcase(name)",strtolower($name),"glossaryid",$glossary->id);
               if ( $dupcategory ) {
                    echo "<p align=\"center\">" . get_string("add"). " " . get_string("category","glossary") . "<font size=\"3\">";
                    
 				    print_simple_box_start("center","40%", "#FFBBBB");
				    echo "<center>" . get_string("duplicatedcategory","glossary") ."</center>";
				    echo "</center>";
				    print_simple_box_end();
				
         			print_footer($course);

     			    redirect("editcategories.php?id=$cm->id&action=add&&name=$name");

               } else {
                    $action = "";
                    $cat->name = $name;
                    $cat->usedynalink = $usedynalink;
                    $cat->glossaryid = $glossary->id;

                    if ( ! $cat->id = insert_record("glossary_categories", $cat) ) {
     				    error("Weird error. The category was not inserted.");
              			
          			    redirect("editcategories.php?id=$cm->id");
                    } else {
                        add_to_log($course->id, "glossary", "add category", "editcategories.php?id=$cm->id", $cat->id,$cm->id);
                    }
             }
          } else {
               echo "<p align=\"center\">" . get_string("add"). " " . get_string("category","glossary") . "<font size=\"3\">";
               $name="";
               require "editcategories.html";
          }
     }
     
     if ( $action ) {
          print_footer();
          die;
     }

?>


<div align="center">

<FORM name="theform" method="post" action="editcategories.php">
<table width="40%" class=generalbox cellpadding=5 bgcolor="<?php p($THEME->cellheading)?>">
        <tr>
          <td width="90%" align="center"><b>
          <?php p(get_string("categories","glossary")) ?></b></td>
          <td width="10%" align="center"><b>
          <?php p(get_string("action")) ?></b></td>
        </tr>
        <tr><td width=100% colspan=2  bgcolor="<?php p($THEME->cellheading2)?>">
        
        <table width=100%>
<?php
     $categories = get_records("glossary_categories","glossaryid",$glossary->id,"name ASC");

     if ( $categories ) {
          foreach ($categories as $category) {
               $num_entries = count_records("glossary_entries_categories","categoryid",$category->id);
             ?>
             <tr bgcolor="<?php p($THEME->cellheading2)?>">
               <td width="90%" align="left">
               <?php
                    echo "<b>$category->name</b> <font size=-1>($num_entries " . get_string("entries","glossary") . ")</font>";
               ?>
               </td>
               <td width="10%" align="center"><b>
               <?php
          		echo "<a href=\"editcategories.php?id=$cm->id&action=delete&mode=cat&hook=$category->id\"><img  alt=\"" . get_string("delete") . "\"src=\"../../pix/t/delete.gif\" height=11 width=11 border=0></a> ";
          	  	echo "<a href=\"editcategories.php?id=$cm->id&action=edit&mode=cat&hook=$category->id\"><img  alt=\"" . get_string("edit") . "\" src=\"../../pix/t/edit.gif\" height=11 width=11 border=0></a>";
               ?>
               </b></td>
             </tr>
             
             <?php
          }
     }
?>
        </table>
        
        </td>
        <tr>
        <td width=100% colspan=2  align=center bgcolor="<?php p($THEME->cellheading2)?>">
            <?php
            
             $options['id'] = $cm->id;
             $options['action'] = "add";
             
             echo "<table border=0><tr><td align=right>";
             echo print_single_button("editcategories.php", $options, get_string("add") . " " . get_string("category","glossary"), "get");
             echo "</td><td align=left>";
             unset($options['action']);
             $options['mode'] = 'cat';
             $options['hook'] = $hook;
             echo print_single_button("view.php", $options, get_string("back","glossary") );
             echo "</td></tr>";
             echo "</tablee>";

            ?>
        </td>
        </tr>
        </table>

</table>
</p>


</form>

<?php print_footer() ?>
