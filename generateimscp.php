<?PHP // $Id: generateimscp.php,v 1.1 2007/02/11 12:18:23 stronk7 Exp $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Antonio Vicent          http://ludens.es      //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot . '/backup/lib.php');
require_once($CFG->dirroot . '/lib/filelib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID

if ($CFG->forcelogin) {
    require_login();
}

if (!$cm = get_record('course_modules', 'id', $id)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

if ($course->category) {
    require_login($course->id);
}

if (!$cm->visible and !isteacher($course->id)) {
    notice(get_string('activityiscurrentlyhidden'));
}
 
if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

if (!isteacher($course->id)) {
    error('Only teachers are allowed to generate IMS CP packages');
}

$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');
$strtop  = get_string('top', 'book');

add_to_log($course->id, 'book', 'generateimscp', 'generateimscp.php?id='.$cm->id, $book->id, $cm->id);

/// Get all the chapters
    $chapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum');

/// Generate the manifest and all the contents
    chapters2imsmanifest($chapters, $book, $cm);

/// Now zip everything
    make_upload_directory('temp');
    $zipfile = $CFG->dataroot . "/temp/". time() . '.zip';
    $files = get_directory_list($CFG->dataroot . "/$cm->course/moddata/book/$book->id", basename($zipfile), false, true, true);
    foreach ($files as $key => $value) {
        $files[$key] = $CFG->dataroot . "/$cm->course/moddata/book/$book->id/" . $value;
    }
    zip_files($files, $zipfile);
/// Now delete all the temp dirs
    delete_dir_contents($CFG->dataroot . "/$cm->course/moddata/book/$book->id");
/// Now serve the file
    send_file($zipfile, clean_filename($book->name) . '.zip', 86400, 0, false, true);

/**
 * This function will create the default imsmanifest plus contents for the book chapters passed as array
 * Everything will be created under the book moddata file area *
 */
function chapters2imsmanifest ($chapters, $book, $cm) {

    global $CFG;

/// Init imsmanifest and others
    $imsmanifest = '';
    $imsitems = '';
    $imsresources = '';

/// Moodle and Book version
    $moodle_release = $CFG->release;
    $moodle_version = $CFG->version;
    $book_version   = get_field('modules', 'version', 'name', 'book');

/// Load manifest header
    $imsmanifest .= '<?xml version="1.0" encoding="UTF-8"?>
<!-- This package has been created with Moodle ' . $moodle_release . ' (' . $moodle_version . '), Book module version ' . $book_version . ' - http://moodle.org -->
<!-- One idea and implementation by Eloy Lafuente (stronk7) and Antonio Vicent (C) 2001-3001 -->
<manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" identifier="MANIFEST-' . md5($CFG->wwwroot . '-' . $book->course . '-' . $book->id) . '" xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd">
  <organizations default="MOODLE-' . $book->course . '-' . $book->id . '">
    <organization identifier="MOODLE-' . $book->course . '-' . $book->id . '" structure="hierarchical">
      <title>' . htmlspecialchars($book->name) . '</title>';

/// Create the temp directory
    $moddir = "$cm->course/moddata/book/$book->id";
    make_upload_directory($moddir);

/// For each chapter, create the corresponding directory and save contents there

/// To store the prev level (book only have 0 and 1)
    $prevlevel = null;
    foreach ($chapters as $chapter) {
    /// Calculate current level ((book only have 0 and 1)
        $currlevel = empty($chapter->subchapter) ? 0 : 1;
    /// Based upon prevlevel and current one, decide what to close
        if ($prevlevel !== null) {
        /// Calculate the number of spaces (for visual xml-text formating)
            $prevspaces = substr('                ', 0, $currlevel * 2);

        /// Same level, simply close the item
            if ($prevlevel == $currlevel) {
                $imsitems .= $prevspaces . '        </item>' . "\n";
            }
        /// Bigger currlevel, nothing to close
        /// Smaller currlevel, close both the current item and the parent one
            if ($prevlevel > $currlevel) {
                $imsitems .= '          </item>' . "\n";
                $imsitems .= '        </item>' . "\n";
            }
        }
    /// Update prevlevel
        $prevlevel = $currlevel;

    /// Calculate the number of spaces (for visual xml-text formating)
        $currspaces = substr('                ', 0, $currlevel * 2);

    /// Create the static html file + local attachments (images...)
        $chapterdir = "$moddir/$chapter->pagenum";
        make_upload_directory($chapterdir);
        $chaptercontent = chapter2html($chapter, $book->course, $book->id);
        file_put_contents($CFG->dataroot . "/" . $chapterdir . "/index.html", $chaptercontent->content);
    /// Add the imsitems
        $imsitems .= $currspaces .'        <item identifier="ITEM-' . $book->course . '-' . $book->id . '-' . $chapter->pagenum .'" isvisible="true" identifierref="RES-' . $book->course . '-' . $book->id . '-' . $chapter->pagenum . '">
 ' . $currspaces . '         <title>' . htmlspecialchars($chapter->title) . '</title>' . "\n";

    /// Add the imsresources
    /// First, check if we have localfiles
        $localfiles = '';
        if ($chaptercontent->localfiles) {
            foreach ($chaptercontent->localfiles as $localfile) {
                $localfiles .= "\n" . '      <file href="' . $chapter->pagenum . '/' . $localfile . '" />';
            }
        }
    /// Now add the dependency to css
        $cssdependency = "\n" . '      <dependency identifierref="RES-' . $book->course . '-'  . $book->id . '-css" />';
    /// Now build the resources section
        $imsresources .= '    <resource identifier="RES-' . $book->course . '-'  . $book->id . '-' . $chapter->pagenum . '" type="webcontent" xml:base="' . $chapter->pagenum . '/" href="index.html">
      <file href="' . $chapter->pagenum . '/index.html" />' . $localfiles . $cssdependency . '
    </resource>' . "\n";
    }

/// Close items (the latest chapter)
/// Level 1, close 1
    if ($currlevel == 0) {
        $imsitems .= '        </item>' . "\n";
    }
/// Level 2, close 2
    if ($currlevel == 1) {
        $imsitems .= '          </item>' . "\n";
        $imsitems .= '        </item>' . "\n";
    }

/// Define the css common resource
$cssresource = '    <resource identifier="RES-' . $book->course . '-'  . $book->id . '-css" type="webcontent" xml:base="css/" href="styles.css">
      <file href="css/styles.css" />
    </resource>' . "\n";

/// Add imsitems to manifest
    $imsmanifest .= "\n" . $imsitems;
/// Close the organization
    $imsmanifest .= "    </organization>
  </organizations>";
/// Add resources to manifest
    $imsmanifest .= "\n  <resources>\n" . $imsresources . $cssresource . "  </resources>";
/// Close manifest
    $imsmanifest .= "\n</manifest>\n";

    file_put_contents($CFG->dataroot . "/" . $moddir . '/imsmanifest.xml', $imsmanifest );

/// Now send the css resource
    make_upload_directory("$moddir/css");
    file_put_contents($CFG->dataroot . "/" . $moddir . "/css/styles.css", file_get_contents("$CFG->dirroot/mod/book/generateimscp.css"));
}

/**
 * This function will create one chaptercontent object, with the contents converted to html and 
 * one array of local images to be included
 */
function chapter2html($chapter, $courseid, $bookid) {

    global $CFG;

    $content = '';
    $content .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
    $content .= '<html>' . "\n";
    $content .= '<head>' . "\n";
    $content .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n";
    $content .= '<link rel="stylesheet" type="text/css" href="../css/styles.css" />' . "\n";
    $content .= '<title>' . $chapter->title . '</title>' . "\n";
    $content .= '</head>' . "\n";
    $content .= '<body>' . "\n";
    $content .= '<h1 id="header">' . $chapter->title . '</h1>' ."\n";
    $options->noclean = true;
    $content .= format_text($chapter->content, '', $options, $courseid) . "\n";
    $content .= '</body>' . "\n";
    $content .= '</html>' . "\n";

/// Now look for course-files in contents
    $search = array($CFG->wwwroot.'/file.php/'.$courseid,
                    $CFG->wwwroot.'/file.php?file=/'.$courseid);
    $replace = array('$@FILEPHP@$','$@FILEPHP@$');
    $content = str_replace($search, $replace, $content);

    $regexp = '/\$@FILEPHP@\$(.*?)"/is';
    $localfiles = array();
    $basefiles = array();
    preg_match_all($regexp, $content, $list);

    if ($list) {
    /// Build the array of local files
        foreach (array_unique($list[1]) as $key => $value) {
            $localfiles['<#'. $key . '#>'] = $value;
            $basefiles['<#'. $key . '#>'] = basename($value);
        /// Copy files to current chapter directory
            if (file_exists($CFG->dataroot . '/' . $courseid . '/' . $value)) {
                copy($CFG->dataroot . '/' . $courseid . '/' . $value, $CFG->dataroot . '/' . $courseid . '/moddata/book/' . $bookid . '/' . $chapter->pagenum . '/' . basename ($value));
            }
        }
    /// Replace contents by keys
        $content = str_replace($localfiles, array_keys($localfiles), $content);
    /// Replace keys by basefiles
        $content = str_replace(array_keys($basefiles), $basefiles, $content);
    /// Delete $@FILEPHP@$
        $content = str_replace('$@FILEPHP@$', '', $content);
    }

/// Build the final object needed to have all the info in order to create the manifest
    $object = new stdClass;
    $object->content = $content;
    $object->localfiles = $basefiles;

    return $object;
}

?>
