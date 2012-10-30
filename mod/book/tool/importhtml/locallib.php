<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * HTML import lib
 *
 * @package    booktool_importhtml
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/mod/book/locallib.php');

/**
 * Import HTML pages packaged into one zip archive
 *
 * @param stored_file $package
 * @param string $type type of the package ('typezipdirs' or 'typezipfiles')
 * @param stdClass $book
 * @param context_module $context
 * @param bool $verbose
 */
function toolbook_importhtml_import_chapters($package, $type, $book, $context, $verbose = true) {
    global $DB, $OUTPUT;

    $fs = get_file_storage();
    $chapterfiles = toolbook_importhtml_get_chapter_files($package, $type);
    $packer = get_file_packer('application/zip');
    $fs->delete_area_files($context->id, 'mod_book', 'importhtmltemp', 0);
    $package->extract_to_storage($packer, $context->id, 'mod_book', 'importhtmltemp', 0, '/');
    // $datafiles = $fs->get_area_files($context->id, 'mod_book', 'importhtmltemp', 0, 'id', false);
    // echo "<pre>";p(var_export($datafiles, true));

    $chapters = array();

    if ($verbose) {
        echo $OUTPUT->notification(get_string('importing', 'booktool_importhtml'), 'notifysuccess');
    }
    if ($type == 0) {
        $chapterfile = reset($chapterfiles);
        if ($file = $fs->get_file_by_hash("$context->id/mod_book/importhtmltemp/0/$chapterfile->pathname")) {
            $htmlcontent = toolbook_importhtml_fix_encoding($file->get_content());
            $htmlchapters = toolbook_importhtml_parse_headings(toolbook_importhtml_parse_body($htmlcontent));
            // TODO: process h1 as main chapter and h2 as subchapters
        }
    } else {
        foreach ($chapterfiles as $chapterfile) {
            if ($file = $fs->get_file_by_hash(sha1("/$context->id/mod_book/importhtmltemp/0/$chapterfile->pathname"))) {
                $chapter = new stdClass();
                $htmlcontent = toolbook_importhtml_fix_encoding($file->get_content());

                $chapter->bookid        = $book->id;
                $chapter->pagenum       = $DB->get_field_sql('SELECT MAX(pagenum) FROM {book_chapters} WHERE bookid = ?', array($book->id)) + 1;
                $chapter->importsrc     = '/'.$chapterfile->pathname;
                $chapter->content       = toolbook_importhtml_parse_styles($htmlcontent);
                $chapter->content       .= toolbook_importhtml_parse_body($htmlcontent);
                $chapter->title         = toolbook_importhtml_parse_title($htmlcontent, $chapterfile->pathname);
                $chapter->contentformat = FORMAT_HTML;
                $chapter->hidden        = 0;
                $chapter->timecreated   = time();
                $chapter->timemodified  = time();
                if (preg_match('/_sub(\/|\.htm)/i', $chapter->importsrc)) { // If filename or directory ends with *_sub treat as subchapters
                    $chapter->subchapter = 1;
                } else {
                    $chapter->subchapter = 0;
                }

                $chapter->id = $DB->insert_record('book_chapters', $chapter);
                $chapters[$chapter->id] = $chapter;

                add_to_log($book->course, 'book', 'add chapter', 'view.php?id='.$context->instanceid.'&chapterid='.$chapter->id, $chapter->id, $context->instanceid);
            }
        }
    }

    if ($verbose) {
        echo $OUTPUT->notification(get_string('relinking', 'booktool_importhtml'), 'notifysuccess');
    }
    $allchapters = $DB->get_records('book_chapters', array('bookid'=>$book->id), 'pagenum');
    foreach ($chapters as $chapter) {
        // find references to all files and copy them + relink them
        $matches = null;
        if (preg_match_all('/(src|codebase|name|href)\s*=\s*"([^"]+)"/i', $chapter->content, $matches)) {
            $file_record = array('contextid'=>$context->id, 'component'=>'mod_book', 'filearea'=>'chapter', 'itemid'=>$chapter->id);
            foreach ($matches[0] as $i => $match) {
                $filepath = dirname($chapter->importsrc).'/'.$matches[2][$i];
                $filepath = toolbook_importhtml_fix_path($filepath);

                if (strtolower($matches[1][$i]) === 'href') {
                    // skip linked html files, we will try chapter relinking later
                    foreach ($allchapters as $target) {
                        if ($target->importsrc === $filepath) {
                            continue 2;
                        }
                    }
                }

                if ($file = $fs->get_file_by_hash(sha1("/$context->id/mod_book/importhtmltemp/0$filepath"))) {
                    if (!$oldfile = $fs->get_file_by_hash(sha1("/$context->id/mod_book/chapter/$chapter->id$filepath"))) {
                        $fs->create_file_from_storedfile($file_record, $file);
                    }
                    $chapter->content = str_replace($match, $matches[1][$i].'="@@PLUGINFILE@@'.$filepath.'"', $chapter->content);
                }
            }
            $DB->set_field('book_chapters', 'content', $chapter->content, array('id'=>$chapter->id));
        }
    }
    unset($chapters);

    $allchapters = $DB->get_records('book_chapters', array('bookid'=>$book->id), 'pagenum');
    foreach ($allchapters as $chapter) {
        $newcontent = $chapter->content;
        $matches = null;
        if (preg_match_all('/(href)\s*=\s*"([^"]+)"/i', $chapter->content, $matches)) {
            foreach ($matches[0] as $i => $match) {
                if (strpos($matches[2][$i], ':') !== false or strpos($matches[2][$i], '@') !== false) {
                    // it is either absolute or pluginfile link
                    continue;
                }
                $chapterpath = dirname($chapter->importsrc).'/'.$matches[2][$i];
                $chapterpath = toolbook_importhtml_fix_path($chapterpath);
                foreach ($allchapters as $target) {
                    if ($target->importsrc === $chapterpath) {
                        $newcontent = str_replace($match, 'href="'.new moodle_url('/mod/book/view.php',
                                array('id'=>$context->instanceid, 'chapter'=>$target->id)).'"', $newcontent);
                    }
                }
            }
        }
        if ($newcontent !== $chapter->content) {
            $DB->set_field('book_chapters', 'content', $newcontent, array('id'=>$chapter->id));
        }
    }

    add_to_log($book->course, 'course', 'update mod', '../mod/book/view.php?id='.$context->instanceid, 'book '.$book->id);
    $fs->delete_area_files($context->id, 'mod_book', 'importhtmltemp', 0);

    // update the revision flag - this takes a long time, better to refetch the current value
    $book = $DB->get_record('book', array('id'=>$book->id));
    $DB->set_field('book', 'revision', $book->revision+1, array('id'=>$book->id));
}

/**
 * Parse the headings of the imported package of type 'typeonefile'
 * (currently unsupported)
 *
 * @param string $html html content to parse
 * @todo implement this once the type 'typeonefile' is enabled
 */
function toolbook_importhtml_parse_headings($html) {
}

/**
 * Parse the links to external css sheets of the imported html content
 *
 * @param string $html html content to parse
 * @return string all the links to external css sheets
 */
function toolbook_importhtml_parse_styles($html) {
    $styles = '';
    if (preg_match('/<head[^>]*>(.+)<\/head>/is', $html, $matches)) {
        $head = $matches[1];
        if (preg_match_all('/<link[^>]+rel="stylesheet"[^>]*>/i', $head, $matches)) { // Extract links to css.
            for ($i=0; $i<count($matches[0]); $i++) {
                $styles .= $matches[0][$i]."\n";
            }
        }
    }
    return $styles;
}

/**
 * Normalize paths to be absolute
 *
 * @param string $path original path with MS/relative separators
 * @return string the normalized and cleaned absolute path
 */
function toolbook_importhtml_fix_path($path) {
    $path = str_replace('\\', '/', $path); // anti MS hack
    $path = '/'.ltrim($path, './'); // dirname() produces . for top level files + our paths start with /

    $cnt = substr_count($path, '..');
    for ($i=0; $i<$cnt; $i++) {
        $path = preg_replace('|[^/]+/\.\./|', '', $path, 1);
    }

    $path = clean_param($path, PARAM_PATH);
    return $path;
}

/**
 * Convert some html content to utf8, getting original encoding from html headers
 *
 * @param string $html html content to convert
 * @return string html content converted to utf8
 */
function toolbook_importhtml_fix_encoding($html) {
    if (preg_match('/<head[^>]*>(.+)<\/head>/is', $html, $matches)) {
        $head = $matches[1];
        if (preg_match('/charset=([^"]+)/is', $head, $matches)) {
            $enc = $matches[1];
            return textlib::convert($html, $enc, 'utf-8');
        }
    }
    return iconv('UTF-8', 'UTF-8//IGNORE', $html);
}

/**
 * Extract the body from any html contents
 *
 * @param string $html the html to parse
 * @return string the contents of the body
 */
function toolbook_importhtml_parse_body($html) {
    $matches = null;
    if (preg_match('/<body[^>]*>(.+)<\/body>/is', $html, $matches)) {
        return $matches[1];
    } else {
        return '';
    }
}

/**
 * Extract the title of any html content, getting it from the title tag
 *
 * @param string $html the html to parse
 * @param string $default default title to apply if no title is found
 * @return string the resulting title
 */
function toolbook_importhtml_parse_title($html, $default) {
    $matches = null;
    if (preg_match('/<title>([^<]+)<\/title>/i', $html, $matches)) {
        return $matches[1];
    } else {
        return $default;
    }
}

/**
 * Returns all the html files (chapters) from a file package
 *
 * @param stored_file $package file to be processed
 * @param string $type type of the package ('typezipdirs' or 'typezipfiles')
 *
 * @return array the html files found in the package
 */
function toolbook_importhtml_get_chapter_files($package, $type) {
    $packer = get_file_packer('application/zip');
    $files = $package->list_files($packer);
    $tophtmlfiles = array();
    $subhtmlfiles = array();
    $topdirs = array();

    foreach ($files as $file) {
        if (empty($file->pathname)) {
            continue;
        }
        if (substr($file->pathname, -1) === '/') {
            if (substr_count($file->pathname, '/') !== 1) {
                // skip subdirs
                continue;
            }
            if (!isset($topdirs[$file->pathname])) {
                $topdirs[$file->pathname] = array();
            }

        } else {
            $mime = mimeinfo('icon', $file->pathname);
            if ($mime !== 'html') {
                continue;
            }
            $level = substr_count($file->pathname, '/');
            if ($level === 0) {
                $tophtmlfiles[$file->pathname] = $file;
            } else if ($level === 1) {
                $subhtmlfiles[$file->pathname] = $file;
                $dir = preg_replace('|/.*$|', '', $file->pathname);
                $topdirs[$dir][$file->pathname] = $file;
            } else {
                // lower levels are not interesting
                continue;
            }
        }
    }

    collatorlib::ksort($tophtmlfiles, collatorlib::SORT_NATURAL);
    collatorlib::ksort($subhtmlfiles, collatorlib::SORT_NATURAL);
    collatorlib::ksort($topdirs, collatorlib::SORT_NATURAL);

    $chapterfiles = array();

    if ($type == 2) {
        $chapterfiles = $tophtmlfiles;

    } else if ($type == 1) {
        foreach ($topdirs as $dir => $htmlfiles) {
            if (empty($htmlfiles)) {
                continue;
            }
            collatorlib::ksort($htmlfiles, collatorlib::SORT_NATURAL);
            if (isset($htmlfiles[$dir.'/index.html'])) {
                $htmlfile = $htmlfiles[$dir.'/index.html'];
            } else if (isset($htmlfiles[$dir.'/index.htm'])) {
                $htmlfile = $htmlfiles[$dir.'/index.htm'];
            } else if (isset($htmlfiles[$dir.'/Default.htm'])) {
                $htmlfile = $htmlfiles[$dir.'/Default.htm'];
            } else {
                $htmlfile = reset($htmlfiles);
            }
            $chapterfiles[$htmlfile->pathname] = $htmlfile;
        }
    } else if ($type == 0) {
        if ($tophtmlfiles) {
            if (isset($tophtmlfiles['index.html'])) {
                $htmlfile = $tophtmlfiles['index.html'];
            } else if (isset($tophtmlfiles['index.htm'])) {
                $htmlfile = $tophtmlfiles['index.htm'];
            } else if (isset($tophtmlfiles['Default.htm'])) {
                $htmlfile = $tophtmlfiles['Default.htm'];
            } else {
                $htmlfile = reset($tophtmlfiles);
            }
        } else {
            $htmlfile = reset($subhtmlfiles);
        }
        $chapterfiles[$htmlfile->pathname] = $htmlfile;
    }

    return $chapterfiles;
}
