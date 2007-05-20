<?PHP // $Id: lib.php,v 1.2 2007/05/20 06:00:30 skodak Exp $

define('NUM_NONE',     '0');
define('NUM_NUMBERS',  '1');
define('NUM_BULLETS',  '2');
define('NUM_INDENTED', '3');


if (!isset($CFG->book_tocwidth)) {
    set_config("book_tocwidth", 180);  // default toc width
}

function book_get_numbering_types() {
    return array (NUM_NONE       => get_string('numbering0', 'book'),
                  NUM_NUMBERS    => get_string('numbering1', 'book'),
                  NUM_BULLETS    => get_string('numbering2', 'book'),
                  NUM_INDENTED   => get_string('numbering3', 'book') );
}

/// Library of functions and constants for module 'book'

function book_add_instance($book) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $book->timecreated = time();
    $book->timemodified = $book->timecreated;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }
    if (!isset($book->disableprinting)) {
        $book->disableprinting = 0;
    }

    return insert_record('book', $book);
}


function book_update_instance($book) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $book->timemodified = time();
    $book->id = $book->instance;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }
    if (!isset($book->disableprinting)) {
        $book->disableprinting = 0;
    }

    # May have to add extra stuff in here #

    return update_record('book', $book);
}


function book_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $book = get_record('book', 'id', $id)) {
        return false;
    }

    $result = true;

    delete_records('book_chapters', 'bookid', $book->id);

    if (! delete_records('book', 'id', $book->id)) {
        $result = false;
    }

    return $result;
}


function book_get_types() {
    global $CFG;

    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = 'book';
    $type->typestr = get_string('modulename', 'book');
    $types[] = $type;

    return $types;
}
function book_user_outline($course, $user, $mod, $book) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    $return = null;
    return $return;
}

function book_user_complete($course, $user, $mod, $book) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function book_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in book activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false
}

function book_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function book_grades($bookid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    return NULL;
}

function book_get_participants($bookid) {
//Must return an array of user records (all data) who are participants
//for a given instance of book. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.

    return false;
}

function book_scale_used ($bookid,$scaleid) {
//This function returns if a scale is being used by one book
//it it has support for grading and scales. Commented code should be
//modified if necessary. See forum, glossary or journal modules
//as reference.

    $return = false;

    //$rec = get_record('book','id',$bookid,'scale',"-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other book functions go here.  Each of them must have a name that
/// starts with book_

//check chapter ordering and
//make sure subchapter is not first in book
//hidden chapter must have all subchapters hidden too
function book_check_structure($bookid) {
    if ($chapters = get_records('book_chapters', 'bookid', $bookid, 'pagenum', 'id, pagenum, subchapter, hidden')) {
        $first = true;
        $hidesub = true;
        $i = 1;
        foreach($chapters as $ch) {
            if ($first and $ch->subchapter) {
                $ch->subchapter = 0;
            }
            $first = false;
            if (!$ch->subchapter) {
                $hidesub = $ch->hidden;
            } else {
                $ch->hidden = $hidesub ? true : $ch->hidden;
            }
            $ch->pagenum = $i;
            update_record('book_chapters', $ch);
            $i++;
        }
    }
}


/// prepare button to turn chapter editing on - connected with course editing
function book_edit_button($id, $courseid, $chapterid) {
    global $CFG, $USER;


    if (isteacheredit($courseid)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = '0';
        } else {
            $string = get_string("turneditingon");
            $edit = '1';
        }
        return '<form method="get" action="'.$CFG->wwwroot.'/mod/book/view.php"><div>'.
               '<input type="hidden" name="id" value="'.$id.'" />'.
               '<input type="hidden" name="chapterid" value="'.$chapterid.'" />'.
               '<input type="hidden" name="edit" value="'.$edit.'" />'.
               '<input type="submit" value="'.$string.'" /></div></form>';
    } else {
        return '';
    }
}

/// general function for logging to table
function book_log($str1, $str2, $level = 0) {
    switch ($level) {
        case 1:
            echo '<tr><td><span class="dimmed_text">'.$str1.'</span></td><td><span class="dimmed_text">'.$str2.'</span></td></tr>';
            break;
        case 2:
            echo '<tr><td><span style="color: rgb(255, 0, 0);">'.$str1.'</span></td><td><span style="color: rgb(255, 0, 0);">'.$str2.'</span></td></tr>';
            break;
        default:
            echo '<tr><td>'.$str1.'</class></td><td>'.$str2.'</td></tr>';
            break;
    }
}

//=================================================
// import functions
//=================================================

/// normalize relative links (= remove ..)
function book_prepare_link($ref) {
    if ($ref == '') {
        return '';
    }
    $ref = str_replace('\\','/',$ref); //anti MS hack
    $cnt = substr_count($ref, '..');
    for($i=0; $i<$cnt; $i++) {
        $ref = ereg_replace('[^/]+/\.\./', '', $ref);
    }
    //still any '..' left?? == error! error!
    if (substr_count($ref, '..') > 0) {
        return '';
    }
    if (ereg('[\|\`]', $ref)) {  // check for other bad characters
        return '';
    }
    return $ref;
}

/// read chapter content from file
function book_read_chapter($base, $ref) {
    $file = $base.'/'.$ref;
    if (filesize($file) <= 0 or !is_readable($file)) {
        book_log($ref, get_string('error'), 2);
        return;
    }
    //first read data
    $handle = fopen($file, "rb");
    $contents = fread($handle, filesize($file));
    fclose($handle);
    //extract title
    if (preg_match('/<title>([^<]+)<\/title>/i', $contents, $matches)) {
        $chapter->title = $matches[1];
    } else {
        $chapter->title = $ref;
    }
    //extract page body
    if (preg_match('/<body[^>]*>(.+)<\/body>/is', $contents, $matches)) {
        $chapter->content = $matches[1];
    } else {
        book_log($ref, get_string('error'), 2);
        return;
    }
    book_log($ref, get_string('ok'));
    $chapter->importsrc = $ref;
    //extract page head
    if (preg_match('/<head[^>]*>(.+)<\/head>/is', $contents, $matches)) {
        $head = $matches[1];
        if (preg_match('/charset=([^"]+)/is', $head, $matches)) {
            $enc = $matches[1];
            $textlib = textlib_get_instance();
            $chapter->content = $textlib->convert($chapter->content, $enc, current_charset());
            $chapter->title = $textlib->convert($chapter->title, $enc, current_charset());
        }
        if (preg_match_all('/<link[^>]+rel="stylesheet"[^>]*>/i', $head, $matches)) { //dlnsk extract links to css
            for($i=0; $i<count($matches[0]); $i++){
                $chapter->content = $matches[0][$i]."\n".$chapter->content;
            }
        }
    }
    return $chapter;
}

///relink images and relative links
function book_relink($id, $bookid, $courseid) {
    global $CFG;
    if ($CFG->slasharguments) {
        $coursebase = $CFG->wwwroot.'/file.php/'.$courseid;
    } else {
        $coursebase = $CFG->wwwroot.'/file.php?file=/'.$courseid;
    }
    $chapters = get_records('book_chapters', 'bookid', $bookid, 'pagenum', 'id, pagenum, title, content, importsrc');
    $originals = array();
    foreach($chapters as $ch) {
        $originals[$ch->importsrc] = $ch;
    }
    foreach($chapters as $ch) {
        $rel = substr($ch->importsrc, 0, strrpos($ch->importsrc, '/')+1);
        $base = $coursebase.strtr(urlencode($rel), array("%2F" => "/"));  //for better internationalization (dlnsk) 
        $modified = false;
        //image relinking
        if ($ch->importsrc && preg_match_all('/(<img[^>]+src=")([^"]+)("[^>]*>)/i', $ch->content, $images)) {
            for($i = 0; $i<count($images[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $images[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$images[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $images[0][$i];
                    $newtag = $images[1][$i].$link.$images[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $images[2][$i].' --> '.$link);
                }
            }
        }
        //css relinking (dlnsk)
        if ($ch->importsrc && preg_match_all('/(<link[^>]+href=")([^"]+)("[^>]*>)/i', $ch->content, $csslinks)) {
            for($i = 0; $i<count($csslinks[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $csslinks[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$csslinks[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $csslinks[0][$i];
                    $newtag = $csslinks[1][$i].$link.$csslinks[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $csslinks[2][$i].' --> '.$link);
                }
            }
        }
        //general embed relinking - flash and others??
        if ($ch->importsrc && preg_match_all('/(<embed[^>]+src=")([^"]+)("[^>]*>)/i', $ch->content, $embeds)) {
            for($i = 0; $i<count($embeds[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $embeds[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$embeds[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $embeds[0][$i];
                    $newtag = $embeds[1][$i].$link.$embeds[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $embeds[2][$i].' --> '.$link);
                }
            }
        }
        //flash in IE <param name=movie value="something" - I do hate IE!
        if ($ch->importsrc && preg_match_all('/<param[^>]+name\s*=\s*"?movie"?[^>]*>/i', $ch->content, $params)) {
            for($i = 0; $i<count($params[0]); $i++) {
                if (preg_match('/(value=\s*")([^"]+)(")/i', $params[0][$i], $values)) {
                    if (!preg_match('/[a-z]+:/i', $values[2])) { // not absolute link
                        $link = book_prepare_link($base.$values[2]);
                        if ($link == '') {
                            continue;
                        }
                        $newvalue = $values[1].$link.$values[3];
                        $newparam = str_replace($values[0], $newvalue, $params[0][$i]);
                        $ch->content = str_replace($params[0][$i], $newparam, $ch->content);
                        $modified = true;
                        book_log($ch->title, $values[2].' --> '.$link);
                    }
                }
            }
        }
        //java applet - add code bases if not present!!!!
        if ($ch->importsrc && preg_match_all('/<applet[^>]*>/i', $ch->content, $applets)) {
            for($i = 0; $i<count($applets[0]); $i++) {
                if (!stripos($applets[0][$i], 'codebase')) {
                    $newapplet = str_ireplace('<applet', '<applet codebase="."', $applets[0][$i]);
                    $ch->content = str_replace($applets[0][$i], $newapplet, $ch->content);
                    $modified = true;
                }
            }
        }
        //relink java applet code bases
        if ($ch->importsrc && preg_match_all('/(<applet[^>]+codebase=")([^"]+)("[^>]*>)/i', $ch->content, $codebases)) {
            for($i = 0; $i<count($codebases[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $codebases[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$codebases[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $codebases[0][$i];
                    $newtag = $codebases[1][$i].$link.$codebases[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $codebases[2][$i].' --> '.$link);
                }
            }
        }
        //relative link conversion
        if ($ch->importsrc && preg_match_all('/(<a[^>]+href=")([^"^#]*)(#[^"]*)?("[^>]*>)/i', $ch->content, $links)) {
            for($i = 0; $i<count($links[0]); $i++) {
                if ($links[2][$i] != ''                         //check for inner anchor links
                && !preg_match('/[a-z]+:/i', $links[2][$i])) { //not absolute link
                    $origtag = $links[0][$i];
                    $target = book_prepare_link($rel.$links[2][$i]); //target chapter
                    if ($target != '' && array_key_exists($target, $originals)) {
                        $o = $originals[$target];
                        $newtag = $links[1][$i].$CFG->wwwroot.'/mod/book/view.php?id='.$id.'&chapterid='.$o->id.$links[3][$i].$links[4][$i];
                        $newtag = preg_replace('/target=[^\s>]/i','', $newtag);
                        $ch->content = str_replace($origtag, $newtag, $ch->content);
                        $modified = true;
                        book_log($ch->title, $links[2][$i].$links[3][$i].' --> '.$CFG->wwwroot.'/mod/book/view.php?id='.$id.'&chapterid='.$o->id.$links[3][$i]);
                    } else if ($target!='' && (!preg_match('/\.html$|\.htm$/i', $links[2][$i]))) { // other relative non html links converted to download links
                        $target = book_prepare_link($base.$links[2][$i]);
                        $origtag = $links[0][$i];
                        $newtag = $links[1][$i].$target.$links[4][$i];
                        $ch->content = str_replace($origtag, $newtag, $ch->content);
                        $modified = true;
                        book_log($ch->title, $links[2][$i].' --> '.$target);
                    }
                }
            }
        }
        if ($modified) {
            $ch->title = addslashes($ch->title);
            $ch->content = addslashes($ch->content);
            $ch->importsrc = addslashes($ch->importsrc);
            if (!update_record('book_chapters', $ch)) {
                error('Could not update your book');
            }
        }
    }
}

?>
