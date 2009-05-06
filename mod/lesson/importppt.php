<?php // $Id$
/**
 * This is a very rough importer for powerpoint slides
 * Export a powerpoint presentation with powerpoint as html pages
 * Do it with office 2002 (I think?) and no special settings
 * Then zip the directory with all of the html pages 
 * and the zip file is what you want to upload
 * 
 * The script supports book and lesson.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once("../../config.php");
    require_once("locallib.php");

    $id     = required_param('id', PARAM_INT);         // Course Module ID
    $pageid = optional_param('pageid', '', PARAM_INT); // Page ID
    global $matches;
    
    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    
    // allows for adaption for multiple modules
    if(! $modname = get_field('modules', 'name', 'id', $cm->module)) {
        error("Could not find module name");
    }

    if (! $mod = get_record($modname, "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:edit', $context);

    $strimportppt = get_string("importppt", "lesson");
    $strlessons = get_string("modulenameplural", "lesson");

    $navigation = build_navigation($strimportppt, $cm);
    print_header_simple("$strimportppt", " $strimportppt", $navigation);

    if ($form = data_submitted()) {   /// Filename

        if (empty($_FILES['newfile'])) {      // file was just uploaded
            notify(get_string("uploadproblem") );
        }

        if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
            notify(get_string("uploadnofilefound") );

        } else {  // Valid file is found
            
            if ($rawpages = readdata($_FILES, $course->id, $modname)) {  // first try to reall all of the data in
                $pageobjects = extract_data($rawpages, $course->id, $mod->name, $modname); // parse all the html files into objects
                clean_temp(); // all done with files so dump em
                                
                $mod_create_objects = $modname.'_create_objects';  
                $mod_save_objects = $modname.'_save_objects'; 
                
                $objects = $mod_create_objects($pageobjects, $mod->id);  // function to preps the data to be sent to DB
                
                if(! $mod_save_objects($objects, $mod->id, $pageid)) {  // sends it to DB
                    error("could not save");
                }
            } else {
                error('could not get data');
            }

            echo "<hr>";
            print_continue("$CFG->wwwroot/mod/$modname/view.php?id=$cm->id");
            print_footer($course);
            exit;
        }
    }

    /// Print upload form

    print_heading_with_help($strimportppt, "importppt", "lesson");

    print_simple_box_start("center");
    echo "<form id=\"theform\" enctype=\"multipart/form-data\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
    echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />\n";
    echo "<table cellpadding=\"5\">";

    echo "<tr><td align=\"right\">";
    print_string("upload");
    echo ":</td><td>";
    echo "<input name=\"newfile\" type=\"file\" size=\"50\" />";
    echo "</td></tr><tr><td>&nbsp;</td><td>";
    echo "<input type=\"submit\" name=\"save\" value=\"".get_string("uploadthisfile")."\" />";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);
    
// START OF FUNCTIONS

function readdata($file, $courseid, $modname) {
// this function expects a zip file to be uploaded.  Then it parses
// outline.htm to determine the slide path.  Then parses each
// slide to get data for the content

    global $CFG;

    // create an upload directory in temp
    make_upload_directory('temp/'.$modname);   

    $base = $CFG->dataroot."/temp/$modname/";

    $zipfile = $_FILES["newfile"]["name"];
    $tempzipfile = $_FILES["newfile"]["tmp_name"];
    
    // create our directory
    $path_parts = pathinfo($zipfile);
    $dirname = substr($zipfile, 0, strpos($zipfile, '.'.$path_parts['extension'])); // take off the extension
    if (!file_exists($base.$dirname)) {
        mkdir($base.$dirname, $CFG->directorypermissions);
    }

    // move our uploaded file to temp/lesson
    move_uploaded_file($tempzipfile, $base.$zipfile);

    // unzip it!
    unzip_file($base.$zipfile, $base, false);
    
    $base = $base.$dirname;  // update the base
    
    // this is the file where we get the names of the files for the slides (in the correct order too)
    $outline = $base.'/outline.htm';
    
    $pages = array();
    
    if (file_exists($outline) and is_readable($outline)) {
        $outlinecontents = file_get_contents($outline);
        $filenames = array();
        preg_match_all("/javascript:GoToSld\('(.*)'\)/", $outlinecontents, $filenames);  // this gets all of our files names

        // file $pages with the contents of all of the slides
        foreach ($filenames[1] as $file) {
            $path = $base.'/'.$file;
            if (is_readable($path)) {
                $pages[$path] = file_get_contents($path);
            } else {
                return false;
            }
        }        
    } else {
        // cannot find the outline, so grab all files that start with slide        
        $dh  = opendir($base);
        while (false !== ($file = readdir($dh))) {  // read throug the directory
           if ('slide' == substr($file, 0, 5)) {  // check for name (may want to check extension later)
                $path = $base.'/'.$file;
                if (is_readable($path)) {
                    $pages[$path] = file_get_contents($path);
                } else {
                    return false;
                }
            }
        }

        ksort($pages);  // order them by file name
    }
    
    if (empty($pages)) {
        return false;
    }
    
    return $pages;
}

function extract_data($pages, $courseid, $lessonname, $modname) {
    // this function attempts to extract the content out of the slides
    // the slides are ugly broken xml.  and the xml is broken... yeah...
    
    global $CFG;
    global $matches;

    $extratedpages = array();
    
    // directory for images
    make_mod_upload_directory($courseid); // make sure moddata is made
    make_upload_directory($courseid.'/moddata/'.$modname, false);  // we store our images in a subfolder in here 
    
    $imagedir = $CFG->dataroot.'/'.$courseid.'/moddata/'.$modname;
    
    require_once($CFG->libdir .'/filelib.php');
    $imagelink = get_file_url($courseid.'/moddata/'.$modname);
    
    // try to make a unique subfolder to store the images
    $lessonname = str_replace(' ', '_', $lessonname); // get rid of spaces
    $i = 0;
    while(true) {
        if (!file_exists($imagedir.'/'.$lessonname.$i)) {
            // ok doesnt exist so make the directory and update our paths
            mkdir($imagedir.'/'.$lessonname.$i, $CFG->directorypermissions);
            $imagedir = $imagedir.'/'.$lessonname.$i;
            $imagelink = $imagelink.'/'.$lessonname.$i;
            break;
        }
        $i++;
    }
    
    foreach ($pages as $file => $content) {
        // to make life easier on our preg_match_alls, we strip out all tags except
        // for div and img (where our content is).  We want div because sometimes we
        // can identify the content in the div based on the div's class
        
        $tags = '<div><img>'; // should also allow <b><i>
        $string = strip_tags($content,$tags);
        //echo s($string);

        $matches = array();
        // this will look for a non nested tag that is closed
        // want to allow <b><i>(maybe more) tags but when we do that
        // the preg_match messes up.
        preg_match_all("/(<([\w]+)[^>]*>)([^<\\2>]*)(<\/\\2>)/", $string, $matches);
        //(<([\w]+)[^>]*>)([^<\\2>]*)(<\/\\2>)  original pattern
        //(<(div+)[^>]*>)[^(<div*)](<\/div>) work in progress

        $path_parts = pathinfo($file);      
        $file = substr($path_parts['basename'], 0, strpos($path_parts['basename'], '.')); // get rid of the extension

        $imgs = array();
        // this preg matches all images
        preg_match_all("/<img[^>]*(src\=\"(".$file."\_image[^>^\"]*)\"[^>]*)>/i", $string, $imgs);

        // start building our page
        $page = new stdClass;
        $page->title = '';
        $page->contents = array();
        $page->images = array();
        $page->source = $path_parts['basename']; // need for book only

        // this foreach keeps the style intact.  Found it doesn't help much.  But if you want back uncomment
        // this foreach and uncomment the line with the comment imgstyle in it.  Also need to comment out
        // the $page->images[]... line in the next foreach
        /*foreach ($imgs[1] as $img) { 
            $page->images[] = '<img '.str_replace('src="', "src=\"$imagelink/", $img).' />';
        }*/
        foreach ($imgs[2] as $img) {
            copy($path_parts['dirname'].'/'.$img, $imagedir.'/'.$img);
            $page->images[] = "<img src=\"$imagelink/$img\" title=\"$img\" />";  // comment out this line if you are using the above foreach loop
        }
        for($i = 0; $i < count($matches[1]); $i++) { // go through all of our div matches
    
            $class = isolate_class($matches[1][$i]); // first step in isolating the class      
        
            // check for any static classes
            switch ($class) {
                case 'T':  // class T is used for Titles
                    $page->title = $matches[3][$i];
                    break;
                case 'B':  // I would guess that all bullet lists would start with B then go to B1, B2, etc
                case 'B1': // B1-B4 are just insurance, should just hit B and all be taken care of
                case 'B2':
                case 'B3':
                case 'B4':
                    $page->contents[] = build_list('<ul>', $i, 0);  // this is a recursive function that will grab all the bullets and rebuild the list in html
                    break;
                default:
                    if ($matches[3][$i] != '&#13;') {  // odd crap generated... sigh
                        if (substr($matches[3][$i], 0, 1) == ':') {  // check for leading :    ... hate MS ...
                            $page->contents[] = substr($matches[3][$i], 1);  // get rid of :
                        } else {
                            $page->contents[] = $matches[3][$i];
                        }
                    }
                    break;
            }
        }
        /*if (count($page->contents) == 0) {  // didnt find anything, grab everything
                                            // potential to pull in a lot of crap
            for($i = 0; $i < count($matches[1]); $i++) {        
                //if($class = isolate_class($matches[1][$i])) { 
                    //if ($class == 'O') {
                        if ($matches[3][$i] != '&#13;') {  // odd crap generated... sigh
                            if (substr($matches[3][$i], 0, 1) == ':') {  // check for leading :    ... hate MS ...
                                $page->contents[] = substr($matches[3][$i], 1);  // get rid of :
                            } else {
                                $page->contents[] = $matches[3][$i];
                            }
                        }
                    //}
                //}
            }
        }*/
        // add the page to the array;
        $extratedpages[] = $page;
        
    } // end $pages foreach loop
    
    return $extratedpages;
}

/**
A recursive function to build a html list
*/
function build_list($list, &$i, $depth) {
    global $matches; // not sure why I global this...
    
    while($i < count($matches[1])) {
    
        $class = isolate_class($matches[1][$i]);

        if (strstr($class, 'B')) {  // make sure we are still working with bullet classes
            if ($class == 'B') {
                $this_depth = 0;  // calling class B depth 0
            } else {
                // set the depth number.  So B1 is depth 1 and B2 is depth 2 and so on
                $this_depth = substr($class, 1);
                if (!is_numeric($this_depth)) {
                    error("Depth not parsed!");
                }
            }
            if ($this_depth < $depth) {
                // we are moving back a level in the nesting
                break;
            }
            if ($this_depth > $depth) {
                // we are moving in a lvl in nesting
                $list .= '<ul>';
                $list = build_list($list, $i, $this_depth);
                // once we return back, should go to the start of the while
                continue;
            }
            // no depth changes, so add the match to our list
            if ($cleanstring = ppt_clean_text($matches[3][$i])) {
                $list .= '<li>'.ppt_clean_text($matches[3][$i]).'</li>';
            }
            $i++;
        } else {
            // not a B class, so get out of here...
            break;
        }
    }
    // end the list and return it
    $list .= '</ul>';
    return $list;
    
}

/**
Given an html tag, this function will 
*/
function isolate_class($string) {
    if($class = strstr($string, 'class=')) { // first step in isolating the class
        $class = substr($class, strpos($class, '=')+1);  // this gets rid of <div blawblaw class=  there are no "" or '' around the class name   ...sigh...
        if (strstr($class, ' ')) {
            // spaces found, so cut off everything off after the first space
            return substr($class, 0, strpos($class, ' '));
        } else {
            // no spaces so nothing else in the div tag, cut off the >
            return substr($class, 0, strpos($class, '>'));
        }
    } else {
        // no class defined in the tag
        return '';
    }
}

/**
This function strips off the random chars that ppt puts infront of bullet lists
*/
function ppt_clean_text($string) {
    $chop = 1; // default: just a single char infront of the content
    
    // look for any other crazy things that may be infront of the content
    if (strstr($string, '&lt;') and strpos($string, '&lt;') == 0) {  // look for the &lt; in the sting and make sure it is in the front
        $chop = 4;  // increase the $chop
    }
    // may need to add more later....
    
    $string = substr($string, $chop);
    
    if ($string != '&#13;') {
        return $string;
    } else {
        return false;
    }
}

/**
    Clean up the temp directory
*/
function clean_temp() {
    global $CFG;
    // this function is broken, use it to clean up later
    // should only clean up what we made as well because someone else could be importing ppt as well
    //delDirContents($CFG->dataroot.'/temp/lesson');    
}

/**
    Creates objects an object with the page and answers that are to be inserted into the database
*/
function lesson_create_objects($pageobjects, $lessonid) {

    $branchtables = array();
    $branchtable = new stdClass;
    
    // all pages have this info
    $page->lessonid = $lessonid;
    $page->prevpageid = 0;
    $page->nextpageid = 0;
    $page->qtype = LESSON_BRANCHTABLE;
    $page->qoption = 0;
    $page->layout = 1;
    $page->display = 1;
    $page->timecreated = time();
    $page->timemodified = 0;
    
    // all answers are the same
    $answer->lessonid = $lessonid;
    $answer->jumpto = LESSON_NEXTPAGE;
    $answer->grade = 0;
    $answer->score = 0;
    $answer->flags = 0;
    $answer->timecreated = time();
    $answer->timemodified = 0;
    $answer->answer = "Next";
    $answer->response = "";

    $answers[] = clone($answer);

    $answer->jumpto = LESSON_PREVIOUSPAGE;
    $answer->answer = "Previous";
    
    $answers[] = clone($answer);
    
    $branchtable->answers = $answers;
    
    $i = 1;
    
    foreach ($pageobjects as $pageobject) {     
        $temp = prep_page($pageobject, $i);  // makes our title and contents
        $page->title = $temp->title;
        $page->contents = $temp->contents;
        $branchtable->page = clone($page);  // add the page
        $branchtables[] = clone($branchtable);  // add it all to our array
        $i++;
    }

    return $branchtables;
}

/**
    Creates objects an chapter object that is to be inserted into the database
*/
function book_create_objects($pageobjects, $bookid) {

    $chapters = array();
    $chapter = new stdClass;
    
    // same for all chapters
    $chapter->bookid = $bookid;
    $chapter->pagenum = count_records('book_chapters', 'bookid', $bookid)+1;
    $chapter->timecreated = time();
    $chapter->timemodified = time();
    $chapter->subchapter = 0;

    $i = 1; 
    foreach ($pageobjects as $pageobject) {
        $page = prep_page($pageobject, $i);  // get title and contents
        $chapter->importsrc = addslashes($pageobject->source); // add the source
        $chapter->title = $page->title;
        $chapter->content = $page->contents;
        $chapters[] = $chapter; 
        
        // increment our page number and our counter
        $chapter->pagenum = $chapter->pagenum + 1;
        $i++;
    }

    return $chapters;
}

/**
    Builds the title and content strings from an object
*/
function prep_page($pageobject, $count) {
    if ($pageobject->title == '') {
        $page->title = "Page $count";  // no title set so make a generic one
    } else {
        $page->title = addslashes($pageobject->title);      
    }
    
    $page->contents = '';
    
    // nab all the images first
    foreach ($pageobject->images as $image) {
        $image = str_replace("\n", '', $image);
        $image = str_replace("\r", '', $image);
        $image = str_replace("'", '"', $image);  // imgstyle
                    
        $page->contents .= addslashes($image);
    }
    // go through the contents array and put <p> tags around each element and strip out \n which I have found to be uneccessary
    foreach ($pageobject->contents as $content) {
        $content = str_replace("\n", '', $content);
        $content = str_replace("\r", '', $content);
        $content = str_replace('&#13;', '', $content);  // puts in returns?
        $content = '<p>'.$content.'</p>';
        $page->contents .= addslashes($content);
    }
    return $page;
}

/**
    Saves the branchtable objects to the DB
*/
function lesson_save_objects($branchtables, $lessonid, $after) {
    // first set up the prevpageid and nextpageid
    if ($after == 0) { // adding it to the top of the lesson
        $prevpageid = 0;
        // get the id of the first page.  If not found, then no pages in the lesson
        if (!$nextpageid = get_field('lesson_pages', 'id', 'prevpageid', 0, 'lessonid', $lessonid)) {
            $nextpageid = 0;
        }
    } else {
        // going after an actual page
        $prevpageid = $after;
        $nextpageid = get_field('lesson_pages', 'nextpageid', 'id', $after);
    }
    
    foreach ($branchtables as $branchtable) {
        
        // set the doubly linked list
        $branchtable->page->nextpageid = $nextpageid;
        $branchtable->page->prevpageid = $prevpageid;
        
        // insert the page
        if(!$id = insert_record('lesson_pages', $branchtable->page)) {
            error("insert page");
        }
    
        // update the link of the page previous to the one we just updated
        if ($prevpageid != 0) {  // if not the first page
            if (!set_field("lesson_pages", "nextpageid", $id, "id", $prevpageid)) {
                error("Insert page: unable to update next link $prevpageid");
            }
        }

        // insert the answers
        foreach ($branchtable->answers as $answer) {
            $answer->pageid = $id;
            if(!insert_record('lesson_answers', $answer)) {
                error("insert answer $id");
            }
        }
        
        $prevpageid = $id;
    }
    
    // all done with inserts.  Now check to update our last page (this is when we import between two lesson pages)
    if ($nextpageid != 0) {  // if the next page is not the end of lesson
        if (!set_field("lesson_pages", "prevpageid", $id, "id", $nextpageid)) {
            error("Insert page: unable to update next link $prevpageid");
        }
    }
    
    return true;
}

/**
    Save the chapter objects to the database
*/
function book_save_objects($chapters, $bookid, $pageid='0') {
    // nothing fancy, just save them all in order
    foreach ($chapters as $chapter) {
        if (!$chapter->id = insert_record('book_chapters', $chapter)) {
            error('Could not update your book');
        }
    }
    return true;
}

?>
