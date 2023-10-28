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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README)
 * @author    Rabea de Groot, Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_pdfannotator\output\answermenu;
use mod_pdfannotator\output\questionmenu;
use mod_pdfannotator\output\reportmenu;
use mod_pdfannotator\output\index;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/pdfannotator/lib.php");
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/constants.php');

/**
 * Display embedded pdfannotator file.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function pdfannotator_display_embed($pdfannotator, $cm, $course, $file, $page = 1, $annoid = null, $commid = null) {
    global $CFG, $PAGE, $OUTPUT, $USER;

    // The revision attribute's existance is demanded by moodle for versioning and could be saved in the pdfannotator table in the future.
    // Note, however, that we forbid file replacement in order to prevent a change of meaning in other people's comments.
    $pdfannotator->revision = 1;

    $context = context_module::instance($cm->id);
    $path = '/' . $context->id . '/mod_pdfannotator/content/' . $pdfannotator->revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, false);

    $documentobject = new stdClass();
    $documentobject->annotatorid = $pdfannotator->id;
    $documentobject->fullurl = $fullurl;

    $stringman = get_string_manager();
    // With this method you get the strings of the language-Files.
    $strings = $stringman->load_component_strings('pdfannotator', 'en');
    // Method to use the language-strings in javascript.
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator');
    // Load and execute the javascript files.
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/pdf.js?ver=00002"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/textclipper.js"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/index.js?ver=00038"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/locallib.js?ver=00006"));

    // Pass parameters from PHP to JavaScript.

    // 1. Toolbar settings.
    $toolbarsettings = new stdClass();
    $toolbarsettings->use_studenttextbox = $pdfannotator->use_studenttextbox;
    $toolbarsettings->use_studentdrawing = $pdfannotator->use_studentdrawing;
    $toolbarsettings->useprint = $pdfannotator->useprint;
    $toolbarsettings->useprintcomments = $pdfannotator->useprintcomments;
    // 2. Capabilities.
    $capabilities = new stdClass();
    $capabilities->viewquestions = has_capability('mod/pdfannotator:viewquestions', $context);
    $capabilities->viewanswers = has_capability('mod/pdfannotator:viewanswers', $context);
    $capabilities->viewposts = has_capability('mod/pdfannotator:viewposts', $context);
    $capabilities->viewreports = has_capability('mod/pdfannotator:viewreports', $context);
    $capabilities->deleteany = has_capability('mod/pdfannotator:deleteany', $context);
    $capabilities->hidecomment = has_capability('mod/pdfannotator:hidecomments', $context);
    $capabilities->seehiddencomments = has_capability('mod/pdfannotator:seehiddencomments', $context);
    $capabilities->usetextbox = has_capability('mod/pdfannotator:usetextbox', $context);
    $capabilities->usedrawing = has_capability('mod/pdfannotator:usedrawing', $context);
    $capabilities->useprint = has_capability('mod/pdfannotator:printdocument', $context);
    $capabilities->useprintcomments = has_capability('mod/pdfannotator:printcomments', $context);
    // 3. Comment editor setting.
    $editorsettings = new stdClass();
    $editorsettings->active_editor = explode(',', get_config('core', 'texteditors'))[0];

    $params = [$cm, $documentobject, $context->id, $USER->id, $capabilities, $toolbarsettings, $page, $annoid, $commid, $editorsettings];
    $PAGE->requires->js_init_call('adjustPdfannotatorNavbar', null, true);
    $PAGE->requires->js_init_call('startIndex', $params, true);
    // The renderer renders the original index.php / takes the template and renders it.
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    echo $myrenderer->render_index(new index($pdfannotator, $capabilities, $file));
    $PAGE->requires->js_init_call('checkOnlyOneCheckbox', null, true);
    //pdfannotator_data_preprocessing($context, 'id_pdfannotator_content', "editor-commentlist-inputs");
    $PAGE->requires->js_init_call('checkOnlyOneCheckbox', null, true);

    pdfannotator_print_intro($pdfannotator, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

function pdfannotator_get_image_options_editor() {
    $image_options = new \stdClass();
    $image_options->maxbytes = get_config('mod_pdfannotator', 'maxbytes');
    $image_options->maxfiles = PDFANNOTATOR_EDITOR_UNLIMITED_FILES;
    $image_options->autosave = false;
    $image_options->env = 'editor';
    $draftitemid = file_get_unused_draft_itemid();
    $image_options->itemid = $draftitemid;
    return $image_options;
}

function pdfannotator_get_editor_options($context) {
    $options = [];
    $options = [
        'atto:toolbar' => get_config('mod_pdfannotator', 'attobuttons'),
        'maxbytes' => get_config('mod_pdfannotator', 'maxbytes'),
        'maxfiles' => PDFANNOTATOR_EDITOR_UNLIMITED_FILES,
        'return_types' => 15,
        'enable_filemanagement' => true, 
        'removeorphaneddrafts' => false, 
        'autosave' => false,
        'noclean' => false, 
        'trusttext' => 0,
        'subdirs' => true,
        'forcehttps' => false,
        'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
    ];
    return $options;
}

function pdfannotator_get_relativelink($content, $commentid, $context) {
    preg_match('/@@PLUGINFILE@@/', $content, $matches);
    if($matches) {
        $relativelink = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id, 'mod_pdfannotator', 'post', $commentid);
        return $relativelink;
    }
    return $content;
}

function pdfannotator_extract_images($contentarr, $itemid, $context=null) {
    // Remove quotes here, in case if there is no math form.
    if (gettype($contentarr) === 'string') {
        $str = preg_replace('/[\"]/', "", $contentarr);
        $contentarr = [$str];
    }
    $res = [];
    $index = 0;
    foreach ($contentarr as $content) {
        $index++;
        if (gettype($content) === "array") {
            $res[] = $content;
            continue;
        }
        $res = pdfannotator_split_content_image($content, $res, $itemid, $context);
    }
    return $res;
}

function pdfannotator_split_content_image($content, $res, $itemid, $context=null) {
    global $CFG;
    // Gets all files in the comment with id itemid.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'post', $itemid);
    $fileinfo = [];
    foreach($files as $file) {
        if ($file->is_directory() and $file->get_filepath() === '/') {
            continue;
        }
        $info = [];
        $info['fileid'] = $file->get_id();
        $info['filename'] = $file->get_filename();
        $info['filepath'] = $file->get_filepath();
        $info['filecontent'] = $file->get_content();
        $info['filesize'] = $file->get_filesize();
        $info['filemimetype'] = $file->get_mimetype();
        $fileinfo[] = $info;
    }

    $imgmatch = [];
    $firststr = '';
    $data = [];
    while (preg_match_all('/<img/', $content, $imgmatch)) {
        $offsetlength = strlen($content);
        
        $imgpos_start = strpos($content, '<img');                
        $imgpos_end = strpos($content, '>', $imgpos_start);

        $firststr = substr($content, 0, $imgpos_start);
        $imgstr = substr($content, $imgpos_start, $imgpos_end - $imgpos_start + 1);
        $laststr = substr($content, $imgpos_end + 1, $offsetlength - $imgpos_end);

        preg_match('/(https...{1,}[.]((gif)|(jpe)g*|(jpg)|(png)|(svg)|(svgz)))/i', $imgstr, $url);
        preg_match('/(gif)|(jpe)g*|(jpg)|(png)|(svg)|(svgz)/i', $url[0], $format);
        if (!$format) {
            throw new \moodle_exception('error:unsupportedextension', 'pdfannotator');
        }
        if (in_array('jpg', $format) || in_array('jpeg', $format) || in_array('jpe', $format) 
        || in_array('JPG', $format) || in_array('JPEG', $format) || in_array('JPE', $format)) {
            $format[0] = 'jpeg';
        }

        $tempinfo = [];
        $encodedurl = urldecode($url[0]);
        foreach($fileinfo as $file) {
            $count = substr_count($encodedurl, $file['filename']);
            if($count) {
                $tempinfo = $file;
                break;
            }
        }

        try {
            if($tempinfo) {
                $imagedata = 'data:' . $tempinfo['filemimetype'] . ';base64,' .  base64_encode($tempinfo['filecontent']);
                $data['image'] = $imagedata;
                $data['format'] = $tempinfo['filemimetype'];
                $data['fileid'] = $tempinfo['fileid'];
                $data['filename'] = $tempinfo['filename'];
                $data['filepath'] = $tempinfo['filepath'];
                $data['filesize'] = $tempinfo['filesize'];
                $data['imagestorage'] = 'intern';
            } else if (!str_contains($CFG->wwwroot, $url[0])){
                $data['imagestorage'] = 'extern';
                $data['format'] =  $format[0];
                $imgcontent = @file_get_contents($url[0]);
                if ($imgcontent) {
                    $data['image'] = 'data:image/' . $format[0] . ";base64," . base64_encode($imgcontent);
                } else {
                    throw new Exception(get_string('error:findimage', 'pdfannotator', $encodedurl));
                }
            } else {
                throw new Exception(get_string('error:findimage', 'pdfannotator', $encodedurl));
            }
    
            preg_match('/height=[0-9]+/', $imgstr, $height);
            if ($height) {
                $data['imageheight'] = str_replace("\"", "", explode('=', $height[0])[1]);
            } else if (!$height && $data['imagestorage'] === 'extern') {
                $imagemetadata = getimagesize($url[0]);
                $data['imageheight'] = $imagemetadata[1];
            } else {
                throw new Exception(get_string('error:getimageheight', 'pdfannotator', $encodedurl));
            }
            preg_match('/width=[0-9]+/', $imgstr, $width);
            if ($width) {
                $data['imagewidth'] = str_replace("\"", "", explode('=', $width[0])[1]);
            } else if (!$width && $data['imagestorage'] === 'extern') {
                $imagemetadata = getimagesize($url[0]);
                $data['imagewidth'] = $imagemetadata[0];
            } else {
                throw new Exception(get_string('error:getimagewidth', 'pdfannotator', $encodedurl));
            }
        } catch (Exception $ex) {
            $data['image'] = "error";
            $data['message'] = $ex->getMessage();
        } finally {
            $res[] = $firststr;
            $res[] = $data;
            $content = $laststr;      
        }

    }
    $res[] = $content;

    return $res;
}

function pdfannotator_data_preprocessing($context, $textarea, $draftitemid = 0) {
    global $PAGE;

    $options = pdfannotator_get_editor_options($context);

    // Check if image button is activated.
    $attobuttons = $options['atto:toolbar'];
    $grouplines = explode("\n", $attobuttons);
    $groups = [];
    $imagebtn = false;
    $image_options = new stdClass();
    foreach ($grouplines as $groupline) {
        $line = explode('=', $groupline);
        $groups = array_map('trim', explode(',', $line[1]));
        if (in_array('image', $groups)) {
            $imagebtn = true;
            break;
        }
    }
    $editor = editors_get_preferred_editor(FORMAT_HTML);
    if(!$imagebtn) {
        $editor->use_editor($textarea, $options);
    } else {
        // initialize Filepicker if image button is active.
        $args = new \stdClass();    
        // need these three to filter repositories list.    
        $args->accepted_types = ['web_image'];
        $args->return_types = 15;
        $args->context = $context;
        $args->env = 'filepicker';
        // advimage plugin
        $image_options = (object)initialise_filepicker($args);
        $image_options->context = $context;
        $image_options->client_id = uniqid();
        $image_options->maxbytes = get_config('mod_pdfannotator', 'maxbytes');
        $image_options->maxfiles = PDFANNOTATOR_EDITOR_UNLIMITED_FILES;
        $image_options->autosave = false;
        $image_options->env = 'editor';
        if (!$draftitemid) {
            $draftitemid = file_get_unused_draft_itemid();
        }
        $image_options->itemid = $draftitemid;
        $editor->use_editor($textarea, $options, ['image' => $image_options]);
    }

    // Add draftitemid and editorformat into input-tags.
    $editorformat = editors_get_preferred_format(FORMAT_HTML);

    //$PAGE->requires->js_init_call('inputDraftItemID', [$draftitemid, (int)$editorformat, $classname]);
    
    return ['draftItemId' => $draftitemid, 'editorFormat' => $editorformat];
}

/**
 * Same function as core, however we need to add files into the existing draft area!
 * Copied from hsuforum.
 */
function pdfannotator_file_prepare_draft_area(&$draftitemid, $contextid, $component, $filearea, $itemid, array $options=null, $text=null) {
    global $CFG, $USER, $CFG, $DB;

    $options = (array)$options;
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }

    $usercontext = \context_user::instance($USER->id);
    $fs = get_file_storage();

    $file_record = ['contextid'=>$usercontext->id, 'component'=>'user', 'filearea'=>'draft', 'itemid'=>$draftitemid];
    if (!is_null($itemid) and $files = $fs->get_area_files($contextid, $component, $filearea, $itemid)) {
        foreach ($files as $file) {
            if ($file->is_directory() and $file->get_filepath() === '/') {
                // we need a way to mark the age of each draft area,
                // by not copying the root dir we force it to be created automatically with current timestamp
                continue;
            }
            if (!$options['subdirs'] and ($file->is_directory() or $file->get_filepath() !== '/')) {
                continue;
            }

            // We are adding to an already existing draft area so we need to make sure we don't double add draft files!
            $checkfile = array_merge($file_record, ['filename' => $file->get_filename()]);
            $draftexists = $DB->get_record('files', $checkfile);
            if ($draftexists) {
                continue;
            }
            $draftfile = $fs->create_file_from_storedfile($file_record, $file);
            // XXX: This is a hack for file manager (MDL-28666)
            // File manager needs to know the original file information before copying
            // to draft area, so we append these information in mdl_files.source field
            // {@link file_storage::search_references()}
            // {@link file_storage::search_references_count()}
            $sourcefield = $file->get_source();
            $newsourcefield = new \stdClass;
            $newsourcefield->source = $sourcefield;
            $original = new \stdClass;
            $original->contextid = $contextid;
            $original->component = $component;
            $original->filearea  = $filearea;
            $original->itemid    = $itemid;
            $original->filename  = $file->get_filename();
            $original->filepath  = $file->get_filepath();
            $newsourcefield->original = \file_storage::pack_reference($original);
            $draftfile->set_source(serialize($newsourcefield));
            // End of file manager hack
        }
    }
    if (!is_null($text)) {
        // at this point there should not be any draftfile links yet,
        // because this is a new text from database that should still contain the @@pluginfile@@ links
        // this happens when developers forget to post process the text
        $text = str_replace("\"$CFG->httpswwwroot/draftfile.php", "\"$CFG->httpswwwroot/brokenfile.php#", $text);
    }


    if (is_null($text)) {
        return null;
    }

    // relink embedded files - editor can not handle @@PLUGINFILE@@ !
    return file_rewrite_pluginfile_urls($text, 'draftfile.php', $usercontext->id, 'user', 'draft', $draftitemid, $options);
}

function pdfannotator_get_instance_name($id) {

    global $DB;
    return $DB->get_field('pdfannotator', 'name', array('id' => $id), $strictness = MUST_EXIST);
}

function pdfannotator_get_course_name_by_id($courseid) {
    global $DB;
    return $DB->get_field('course', 'fullname', array('id' => $courseid), $strictness = MUST_EXIST);
}

function pdfannotator_get_username($userid) {
    global $DB;
    $user = $DB->get_record('user', array('id' => $userid));
    return fullname($user);
}

function pdfannotator_get_annotationtype_id($typename) {
    global $DB;
    if ($typename == 'point') {
        $typename = 'pin';
    }
    $result = $DB->get_records('pdfannotator_annotationtypes', array('name' => $typename));
    foreach ($result as $r) {
        return $r->id;
    }
}

function pdfannotator_get_annotationtype_name($typeid) {
    global $DB;
    $result = $DB->get_records('pdfannotator_annotationtypes', array('id' => $typeid));
    foreach ($result as $r) {
        return $r->name;
    }
}

function pdfannotator_handle_latex($context, string $subject) {
    global $CFG;
    $latexapi = get_config('mod_pdfannotator', 'latexapi');

    // Look for these formulae: $$ ... $$, \( ... \) and \[ ... \]
    // !!! keep indentation!
    $pattern = <<<'SIGN'
~(?:\$\$.*?\$\$)|(?:\\\(.*?\\\))|(?:\\\[.*?\\\])~
SIGN;

    $matches = array();
    $hits = preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);

    if ($hits == 0) {
        return $subject;
    }

    $textstart = 0;
    $formulalength = 0;
    $formulaoffset = 0;
    $result = [];
    $matches = $matches[0];
    foreach ($matches as $match) {
        $formulalength = strlen($match[0]);
        $formulaoffset = $match[1];
        $string = $match[0];
        $string = str_replace('\xrightarrow', '\rightarrow', $string);
        $string = str_replace('\xlefttarrow', '\leftarrow', $string);

        $pos = strpos($string, '\\[');
        if ($pos !== false) {
            $string = substr_replace($string, '', $pos, strlen('\\['));
        }

        $pos = strpos($string, '\\(');
        if ($pos !== false) {
            $string = substr_replace($string, '', $pos, strlen('\\('));
        }

        $string = str_replace('\\]', '', $string);

        $string = str_replace('\\)', '', $string);

        $string = str_replace('\begin{aligned}', '', $string);
        $string = str_replace('\end{aligned}', '', $string);

        $string = str_replace('\begin{align*}', '', $string);
        $string = str_replace('\end{align*}', '', $string);

        // Find any backslash preceding a ( or [ and replace it with \backslash
        $pattern = '~\\\\(?=[\\\(\\\[])~';
        $string = preg_replace($pattern, '\\backslash', $string);
        $match[0] = $string;

        $result[] = trim(substr($subject, $textstart, $formulaoffset - $textstart));
        if ($latexapi == LATEX_TO_PNG_GOOGLE_API) {
            $result[] = pdfannotator_process_latex_google($match[0]);
        } else {
            $result[] = pdfannotator_process_latex_moodle($context, $match[0]);
        }
        $textstart = $formulaoffset + $formulalength;
    }
    if ($textstart != strlen($subject) - 1) {
        $result[] = trim(substr($subject, $textstart, strlen($subject) - $textstart));
    }
    return $result;
}

function pdfannotator_process_latex_moodle($context, $string) {
    global $CFG;
    require_once($CFG->libdir . '/moodlelib.php');
    require_once($CFG->dirroot . '/filter/tex/latex.php');
    require_once($CFG->dirroot . '/filter/tex/lib.php');
    $result = array();
    $tex = new latex();
    $md5 = md5($string);
    $image = $tex->render($string, $md5 . 'png');
    if ($image == false) {
        return false;
    }
    $imagedata = file_get_contents($image);
    $result['mathform'] = IMAGE_PREFIX . base64_encode($imagedata);
    // Imageinfo returns an array with the info of the size of the image. In Parameter 1 there is the height, which is the only
    // thing needed here.
    $imageinfo = getimagesize($image);
    $result['mathformheight'] = $imageinfo[1];
    $result['format'] = 'PNG';
    return $result;
}
/**
 * Function takes a latex code string, modifies and url encodes it for the Google Api to process,
 * and returns the resulting image along with its height
 *
 * @param type $string
 * @return type
 */
function pdfannotator_process_latex_google(string $string) {

    $length = strlen($string);
    $im = null;
    if ($length <= 200) { // Google API constraint XXX find better alternative if possible.
        $latexdata = urlencode($string);
        $requesturl = LATEX_TO_PNG_REQUEST . $latexdata;
        $im = @file_get_contents($requesturl); // '@' suppresses warnings so that one failed google request doesn't prevent the pdf from being printed,
        // but just the one formula from being presented as a picture.
    }
    if ($im != null) {
        $array = [];
        try {
            list($width, $height) = getimagesize($requesturl); // XXX alternative: acess height by decoding the string (saving the extra server request)?
            if ($height != null) {
                $imagedata = IMAGE_PREFIX . base64_encode($im); // Image.
                $array['image'] = $imagedata;
                $array['imageheight'] = $height;
                return $array;
            }
        } catch (Exception $ex) {
            return $string;
        }
    } else {
        return $string;
    }
}

function pdfannotator_send_forward_message($recipients, $messageparams, $course, $cm, $context) {
    $name = 'forwardedquestion';
    $text = new stdClass();
    $module = get_string('modulename', 'pdfannotator');
    $modulename = format_string($cm->name, true);
    $text->text = pdfannotator_format_notification_message_text($course, $cm, $context, $module, $modulename, $messageparams, $name);
    $text->url = $messageparams->urltoquestion;

    foreach ($recipients as $recipient) {
        $text->html = pdfannotator_format_notification_message_html($course, $cm, $context, $module, $modulename, $messageparams, $name, $recipient);
        pdfannotator_notify_manager($recipient, $course, $cm, $name, $text);
    }
}

function pdfannotator_notify_manager($recipient, $course, $cm, $name, $messagetext, $anonymous = false) {

    global $USER;
    $userfrom = $USER;
    $modulename = format_string($cm->name, true);
    if ($anonymous) {
        $userfrom = clone($USER);
        $userfrom->firstname = get_string('pdfannotatorname', 'pdfannotator') . ':';
        $userfrom->lastname = $modulename;
    }
    $message = new \core\message\message();
    $message->component = 'mod_pdfannotator';
    $message->name = $name;
    $message->courseid = $course->id;
    $message->userfrom = $anonymous ? core_user::get_noreply_user() : $userfrom;
    $message->userto = $recipient;
    $message->subject = get_string('notificationsubject:' . $name, 'pdfannotator', $modulename);
    $message->fullmessage = $messagetext->text;
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml = $messagetext->html;
    $message->smallmessage = get_string('notificationsubject:' . $name, 'pdfannotator', $modulename);
    $message->notification = 1; // For personal messages '0'. Important: the 1 without '' and 0 with ''.
    $message->contexturl = $messagetext->url;
    $message->contexturlname = 'Context name';
    $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor.

    $messageid = message_send($message);

    return $messageid;
}

function pdfannotator_format_notification_message_text($course, $cm, $context, $modulename, $pdfannotatorname, $paramsforlanguagestring, $messagetype) {
    global $CFG;
    $formatparams = array('context' => $context->get_course_context());
    $posttext = format_string($course->shortname, true, $formatparams) .
        ' -> ' .
        $modulename .
        ' -> ' .
        format_string($pdfannotatorname, true, $formatparams) . "\n";
    $posttext .= '---------------------------------------------------------------------' . "\n";
    $posttext .= "\n";
    $posttext .= get_string($messagetype . 'text', 'pdfannotator', $paramsforlanguagestring) . "\n---------------------------------------------------------------------\n";
    return $posttext;
}

/**
 * Format a notification for HTML.
 *
 * @param string $messagetype
 * @param stdClass $info
 * @param stdClass $course
 * @param stdClass $context
 * @param string $modulename
 * @param stdClass $coursemodule
 * @param string $assignmentname
 */
function pdfannotator_format_notification_message_html($course, $cm, $context, $modulename, $pdfannotatorname, $report, $messagetype, $recipientid) {
    global $CFG, $USER;
    $formatparams = array('context' => $context->get_course_context());
    $posthtml = '<p><font face="sans-serif">' .
        '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' .
        format_string($course->shortname, true, $formatparams) .
        '</a> ->' .
        '<a href="' . $CFG->wwwroot . '/mod/pdfannotator/index.php?id=' . $course->id . '">' .
        $modulename .
        '</a> ->' .
        '<a href="' . $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '">' .
        format_string($pdfannotatorname, true, $formatparams) .
        '</a></font></p>';
    $posthtml .= '<hr /><font face="sans-serif">';
    $report->urltoreport = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '&action=overviewreports';
    $posthtml .= '<p>' . get_string($messagetype . 'html', 'pdfannotator', $report) . '</p>';
    $linktonotificationsettingspage = new moodle_url('/message/notificationpreferences.php', array('userid' => $recipientid));
    $linktonotificationsettingspage = $linktonotificationsettingspage->__toString();
    $posthtml .= '</font><hr />';
    $posthtml .= '<font face="sans-serif"><p>' . get_string('unsubscribe_notification', 'pdfannotator', $linktonotificationsettingspage) . '</p></font>';
    return $posthtml;
}

/**
 * Internal function - create click to open text with link.
 */
function pdfannotator_get_clicktoopen($file, $revision, $extra = '') {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/' . $file->get_contextid() . '/mod_pdfannotator/content/' . $revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, false);

    $string = get_string('clicktoopen2', 'pdfannotator', "<a href=\"$fullurl\" $extra>$filename</a>");

    return $string;
}

/**
 * Internal function - create click to open text with link.
 */
function pdfannotator_get_clicktodownload($file, $revision) {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/' . $file->get_contextid() . '/mod_pdfannotator/content/' . $revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, true);

    $string = get_string('clicktodownload', 'pdfannotator', "<a href=\"$fullurl\">$filename</a>");

    return $string;
}

/**
 * Print pdfannotator header.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @return void
 */
function pdfannotator_print_header($pdfannotator, $cm, $course) {
    global $PAGE, $OUTPUT;
    $PAGE->set_title($course->shortname . ': ' . $pdfannotator->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($pdfannotator);
    echo $OUTPUT->header();
}

/**
 * Gets details of the file to cache in course cache to be displayed using {@see pdfannotator_get_optional_details()}
 *
 * @param object $pdfannotator pdfannotator table row (only property 'displayoptions' is used here)
 * @param object $cm Course-module table row
 * @return string Size and type or empty string if show options are not enabled
 */
function pdfannotator_get_file_details($pdfannotator, $cm) {
    $filedetails = array();

    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false);
    // For a typical file pdfannotator, the sortorder is 1 for the main file
    // and 0 for all other files. This sort approach is used just in case
    // there are situations where the file has a different sort order.
    $mainfile = $files ? reset($files) : null;

    foreach ($files as $file) {
        // This will also synchronize the file size for external files if needed.
        $filedetails['size'] += $file->get_filesize();
        if ($file->get_repository_id()) {
            // If file is a reference the 'size' attribute can not be cached.
            $filedetails['isref'] = true;
        }
    }

    return $filedetails;
}

/**
 * Print pdfannotator introduction.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function pdfannotator_print_intro($pdfannotator, $cm, $course, $ignoresettings = false) {
    global $OUTPUT;
    if ($ignoresettings) {
        $gotintro = trim(strip_tags($pdfannotator->intro));
        if ($gotintro || $extraintro) {
            echo $OUTPUT->box_start('mod_introbox', 'pdfannotatorintro');
            if ($gotintro) {
                echo format_module_intro('pdfannotator', $pdfannotator, $cm->id);
            }
            echo $extraintro;
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Print warning that file can not be found.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function pdfannotator_print_filenotfound($pdfannotator, $cm, $course) {
    global $DB, $OUTPUT;

    pdfannotator_print_header($pdfannotator, $cm, $course);
    // pdfannotator_print_heading($pdfannotator, $cm, $course);//TODO Method is not defined.
    pdfannotator_print_intro($pdfannotator, $cm, $course);
    echo $OUTPUT->notification(get_string('filenotfound', 'pdfannotator'));

    echo $OUTPUT->footer();
    die;
}

/**
 * Function returns the number of new comments, drawings and textboxes*
 * in this annotator. 'New' is defined here as 'no older than 24h' but
 * can easily be changed to another time span.
 * *Drawings and textboxes cannot be commented. In their case (only),
 * therefore, annotations are counted.
 *
 */
function pdfannotator_get_number_of_new_activities($annotatorid) {

    global $DB;

    $parameters = array();
    $parameters[] = $annotatorid;
    $parameters[] = strtotime("-1 day");

    $sql = "SELECT c.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
        . "WHERE a.pdfannotatorid = ? AND c.timemodified >= ?";
    $sql2 = "SELECT a.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_annotationtypes} t ON a.annotationtypeid = t.id "
        . "WHERE a.pdfannotatorid = ? AND a.timecreated >= ? AND t.name IN('drawing','textbox')";

    return ( count($DB->get_records_sql($sql, $parameters)) + count($DB->get_records_sql($sql2, $parameters)) );
}

/**
 * Function returns the datetime of the last modification on or in the specified annotator.
 * The modification can be the creation of the annotator, a change of title or description,
 * a new annotation or a new comment. Reports are not considered.
 *
 * @param int $annotatorid
 * @return datetime $timemodified
 * The timestamp can be transformed into a readable string with this moodle method:
 * userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
 */
function pdfannotator_get_datetime_of_last_modification($annotatorid) {

    global $DB;

    // 1. When was the last time the annotator itself (i.e. its title, description or pdf) was modified?
    $timemodified = $DB->get_record('pdfannotator', array('id' => $annotatorid), 'timemodified', MUST_EXIST);
    $timemodified = $timemodified->timemodified;

    // 2. When was the last time an annotation or a comment was added in the specified annotator?
    $sql = "SELECT max(a.timecreated) AS last_annotation, max(c.timemodified) AS last_comment "
        . "FROM {pdfannotator_annotations} a LEFT OUTER JOIN {pdfannotator_comments} c ON a.id = c.annotationid "
        . "WHERE a.pdfannotatorid = ?";
    $newposts = $DB->get_records_sql($sql, array($annotatorid));

    if (!empty($newposts)) {

        foreach ($newposts as $entry) {

            // 2.a) If there is an annotation younger than the creation/modification of the annotator, set timemodified to the annotation time.
            if (!empty($entry->last_annotation) && ($entry->last_annotation > $timemodified)) {
                $timemodified = $entry->last_annotation;
            }
            // 2.b) If there is a comment younger than the creation/modification of the annotator or its newest annotation, set timemodified to the comment time.
            if (!empty($entry->last_comment) && ($entry->last_comment > $timemodified)) {
                $timemodified = $entry->last_comment;
            }
            return $timemodified;
        }
    }
}

/**
 * File browsing support class
 */
class pdfannotator_content_file_info extends file_info_stored {

    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }

    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }

}

function pdfannotator_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files; // Name from the filemanger.

    $context = context_module::instance($cmid);
    if ($draftitemid) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_pdfannotator', 'content', 0, array('subdirs' => true));
    }
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // Only one file attached, set it as main file automatically.
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_pdfannotator', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
}

function pdfannotator_render_listitem_actions(array $actions = null) {
    $menu = new action_menu();
    $menu->attributes['class'] .= ' course-item-actions item-actions';
    $hasitems = false;
    foreach ($actions as $key => $action) {
        $hasitems = true;
        $menu->add(new action_menu_link(
            $action['url'], $action['icon'], $action['string'], in_array($key, []), ['data-action' => $key, 'class' => 'action-' . $key]
        ));
    }
    if (!$hasitems) {
        return '';
    }
    return pdfannotator_render_action_menu($menu);
}

function pdfannotator_render_action_menu($menu) {
    global $OUTPUT;
    return $OUTPUT->render($menu);
}

function pdfannotator_subscribe_all($annotatorid, $context) {
    global $DB;
    $sql = "SELECT id FROM {pdfannotator_annotations} "
        . "WHERE pdfannotatorid = ? AND annotationtypeid NOT IN "
        . "(SELECT id FROM {pdfannotator_annotationtypes} WHERE name = ? OR name = ?)";
    $params = [$annotatorid, 'drawing', 'textbox'];
    $ids = $DB->get_fieldset_sql($sql, $params);
    foreach ($ids as $annotationid) {
        pdfannotator_comment::insert_subscription($annotationid, $context);
    }
}

function pdfannotator_unsubscribe_all($annotatorid) {
    global $DB, $USER;
    $sql = "SELECT a.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_subscriptions} s "
        . "ON s.annotationid = a.id AND s.userid = ? WHERE pdfannotatorid = ?";
    $ids = $DB->get_fieldset_sql($sql, [$USER->id, $annotatorid]);
    foreach ($ids as $annotationid) {
        pdfannotator_comment::delete_subscription($annotationid);
    }
}

/**
 * Checks wether a user has subscribed to all questions in an annotator.
 * Returns 1 if all questions are subscribed, 0 if no questions are subscribed and -1 if at least one but not all questions are subscribed.
 * @param type $annotatorid
 */
function pdfannotator_subscribed($annotatorid) {
    global $DB, $USER;
    $sql = "SELECT COUNT(*) FROM {pdfannotator_annotations} a JOIN {pdfannotator_subscriptions} s "
        . "ON s.annotationid = a.id AND s.userid = ? WHERE a.pdfannotatorid = ?";
    $subscriptions = $DB->count_records_sql($sql, [$USER->id, $annotatorid]);
    $sql = "SELECT COUNT(*) FROM {pdfannotator_annotations} "
        . "WHERE pdfannotatorid = ? AND annotationtypeid NOT IN "
        . "(SELECT id FROM {pdfannotator_annotationtypes} WHERE name = ? OR name = ?)";
    $params = [$annotatorid, 'drawing', 'textbox'];
    $annotations = $DB->count_records_sql($sql, $params);

    if ($subscriptions === 0) {
        return 0;
    } else if ($subscriptions === $annotations) {
        return 1;
    } else {
        return -1;
    }
}

/**
 *
 * @param type $timestamp
 * @return string Day, D Month Y, Time
 */
function pdfannotator_get_user_datetime($timestamp) {
    $userdatetime = userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
    return $userdatetime;
}

/**
 *
 * @param type $timestamp
 * @return string
 */
function pdfannotator_get_user_datetime_shortformat($timestamp) {
    $shortformat = get_string('strftimedatetime', 'pdfannotator'); // Format strings in moodle\lang\en\langconfig.php.
    $userdatetime = userdate($timestamp, $shortformat, $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
    return $userdatetime;
}

/**
 * Function is executed each time one of the overview categories is accessed.
 * It creates the tab navigation and makes javascript accessible.
 *
 * @param type $CFG
 * @param type $PAGE
 * @param type $myrenderer
 * @param type $taburl
 * @param type $action
 * @param type $pdfannotator
 * @param type $context
 */
function pdfannotator_prepare_overviewpage($cmid, $myrenderer, $taburl, $action, $pdfannotator, $context) {

    global $CFG, $PAGE;

    $PAGE->set_title("overview");

    // 1.1 Display tab navigation.
    echo $myrenderer->pdfannotator_render_tabs($taburl, $pdfannotator->name, $context, $action['tab']);

    // 1.2 Give javascript (see below) access to the language string repository.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('pdfannotator', 'en'); // Method gets the strings of the language files.
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator'); // Method to use the language-strings in javascript.
    // 1.3 Add the javascript file that determines the dynamic behaviour of the page.
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/locallib.js?ver=00002"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/overview.js?ver=00002"));

    // 1.4 Check user capabilities to view the different categories.
    // The argument 'false' disregards administrator's magical 'doanything' power.
    $capabilities = new stdClass();
    $capabilities->viewquestions = has_capability('mod/pdfannotator:viewquestions', $context);
    $capabilities->viewanswers = has_capability('mod/pdfannotator:viewanswers', $context);
    $capabilities->viewposts = has_capability('mod/pdfannotator:viewposts', $context);
    $capabilities->viewreports = has_capability('mod/pdfannotator:viewreports', $context);

    $params = array($pdfannotator->id, $cmid, $capabilities, $action['action']);
    $PAGE->requires->js_init_call('startOverview', $params, true); // 1. name of JS function, 2. parameters.
}

/**
 * Function serves as subcontroller that tells the annotator model to collect
 * all or all unsolved/solved questions asked in this course.
 *
 * @param int $openannotator
 * @param int $courseid
 * @param type $questionfilter
 * @return type
 */
function pdfannotator_get_questions($courseid, $context, $questionfilter) {

    global $DB;

    $cminfo = pdfannotator_instance::get_cm_info($courseid);
    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cminfo));

    $sql = "SELECT a.id as annoid, a.page, a.pdfannotatorid, p.name AS pdfannotatorname, p.usevotes, cm.id AS cmid, c.isquestion, "
        . "c.id as commentid, c.content, c.userid, c.visibility, c.timecreated, c.isdeleted, c.ishidden, "
        . "SUM(vote) AS votes, MAX(answ.timecreated) AS lastanswered "
        . "FROM {pdfannotator_annotations} a "
        . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
        . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
        . "JOIN {course_modules} cm ON p.id = cm.instance "
        . "LEFT JOIN {pdfannotator_votes} v ON c.id=v.commentid "
        . "LEFT JOIN {pdfannotator_comments} answ ON answ.annotationid = a.id "
        . "WHERE c.isquestion = 1 AND p.course = ? AND cm.id $insql";
    if ($questionfilter == 0) {
        $sql = $sql . ' AND c.solved = 0 ';
    }
    if ($questionfilter == 1) {
        $sql = $sql . ' AND NOT c.solved = 0 ';
    }
    $sql = $sql . "GROUP BY a.id, p.name, p.usevotes, cm.id, c.id, a.page, a.pdfannotatorid, c.content, c.userid, c.visibility,"
        . "c.timecreated, c.isdeleted, c.ishidden, c.isquestion";
    $params = array_merge([$courseid], $inparams);
    $questions = $DB->get_records_sql($sql, $params);

    $seehidden = has_capability('mod/pdfannotator:seehiddencomments', $context);
    $labelhidden = "<br><span class='tag tag-info'>" . get_string('hiddenfromstudents') . "</span>"; // XXX use moodle method if exists.
    $labelunavailable = "<br><span class='tag tag-info'>" . get_string('restricted') . "</span>";

    $res = [];
    foreach ($questions as $key => $question) {

        if (!pdfannotator_can_see_comment($question, $context)) {
            continue;
        }

        if (empty($question->votes)) {
            $question->votes = 0;
        }
        if ($question->usevotes == 0) {
            $question->votes = '-';
        }
        $question->answercount = pdfannotator_count_answers($question->annoid, $context);

        $lastanswer = pdfannotator_get_last_answer($question->annoid, $context);
        if ($lastanswer) {
            $question->lastuser = $lastanswer->userid;
            $question->lastuservisibility = $lastanswer->visibility;
        } else {
            $question->lastanswered = false;
        }

        if ($question->isdeleted == 1) {
            $question->content = "<em>" . get_string('deletedQuestion', 'pdfannotator') . "</em>";
        } else if ($question->ishidden) {
            switch ($seehidden) {
                case 0:
                    $question->content = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
                    break;
                case 1:
                    $question->content = $question->content . $labelhidden;
                    $question->displayhidden = true;
                    break;
                default:
                    $question->content = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
            }
        }

        if (!$cminfo[$question->cmid]['visible']) { // Annotator is not visible for students.
            $question->content = $question->content . $labelhidden;
            $question->displayhidden = true;
        }
        if ($cminfo[$question->cmid]['availableinfo']) { // Annotator is restricted.
            $question->content = $question->content . $labelunavailable . " " . $cminfo[$question->cmid]['availableinfo'];
            $question->displayhidden = true;
        }

        $question->content = pdfannotator_get_relativelink($question->content, $question->commentid, $context);
        $question->content = format_text($question->content, $options = ['filter' => true]);
        $question->link = (new moodle_url('/mod/pdfannotator/view.php', array('id' => $question->cmid,
            'page' => $question->page, 'annoid' => $question->annoid, 'commid' => $question->commentid)))->out();

        $res[] = $question;

    }
    return $res;
}

/**
 * Function serves as subcontroller that tells the annotator model to collect all
 * questions and answers this user posted in the course.
 *
 * @param int $courseid
 * @return type
 */
function pdfannotator_get_posts_by_this_user($courseid, $context) {

    global $DB, $USER;

    $cminfo = pdfannotator_instance::get_cm_info($courseid);
    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cminfo));

    $seehidden = has_capability('mod/pdfannotator:seehiddencomments', $context);
    $labelhidden = "<br><span class='tag tag-info'>" . get_string('hiddenforparticipants', 'pdfannotator') . "</span>";
    $labelunavailable = "<br><span class='tag tag-info'>" . get_string('restricted') . "</span>";

    $sql = "SELECT c.id as commid, c.annotationid, c.content, c.timemodified, c.ishidden, a.id AS annoid, "
        . "a.page, a.pdfannotatorid, p.name AS pdfannotatorname, p.usevotes, cm.id AS cmid, "
        . "SUM(v.vote) AS votes "
        . "FROM {pdfannotator_comments} c "
        . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
        . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
        . "JOIN {course_modules} cm ON p.id = cm.instance "
        . "LEFT JOIN {pdfannotator_votes} v ON c.id = v.commentid "
        . "WHERE c.userid = ? AND p.course = ? AND cm.id $insql "
        . "GROUP BY a.id, p.name, p.usevotes, cm.id, c.id, c.annotationid, c.content, c.timemodified, c.ishidden, a.page, a.pdfannotatorid";

    $params = array_merge([$USER->id, $courseid], $inparams);

    $posts = $DB->get_records_sql($sql, $params);

    foreach ($posts as $key => $post) {
        if (empty($post->votes)) {
            $post->votes = 0;
        }
        if ($post->usevotes == 0) {
            $post->votes = '-';
        }

        if ($post->ishidden) { // Post in annotator is hidden.
            switch ($seehidden) {
                case 0:
                    $post->content = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
                    break;
                case 1:
                    $post->content = $post->content . $labelhidden;
                    $post->displayhidden = true;
                    break;
                default:
                    $post->content = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
            }
        }

        if (!$cminfo[$post->cmid]['visible']) {  // Annotator is hidden.
            $post->content = $post->content . $labelhidden;
            $post->displayhidden = true;
        }
        if ($cminfo[$post->cmid]['availableinfo']) {  // Annotator is restricted.
            $post->content = $post->content . $labelunavailable . " " . $cminfo[$post->cmid]['availableinfo'];
            $post->displayhidden = true;
        }

        $params = array('id' => $post->cmid, 'page' => $post->page, 'annoid' => $post->annotationid, 'commid' => $post->commid);
        $post->link = (new moodle_url('/mod/pdfannotator/view.php', $params))->out();
        $post->content = pdfannotator_get_relativelink($post->content, $post->commid, $context);
        $post->content = format_text($post->content, $options = ['filter' => true]);
    }
    return $posts;
}

/**
 * Function serves as subcontroller that tells the annotator model to collect
 * all answers given to questions that the current user asked or subscribed to
 * in this course.
 *
 * @param int $courseid
 * @param Moodle object? $context
 * @param int $answerfilter
 * @return array of stdClass objects
 */
function pdfannotator_get_answers_for_this_user($courseid, $context, $answerfilter = 1) {

    global $DB, $USER;

    $cminfo = pdfannotator_instance::get_cm_info($courseid);
    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cminfo));

    $seehidden = has_capability('mod/pdfannotator:seehiddencomments', $context);
    $labelhidden = "<br><span class='tag tag-info'>" . get_string('hiddenforparticipants', 'pdfannotator') . "</span>";
    $labelunavailable = "<br><span class='tag tag-info'>" . get_string('restricted') . "</span>";

    if ($answerfilter == 0) { // Either: get all answers in this annotator.
        $sql = "SELECT c.id AS answerid, c.content AS answer, c.userid AS userid, c.visibility, "
            . "c.timemodified, c.solved AS correct, c.ishidden AS answerhidden, a.id AS annoid, a.page, q.id AS questionid,"
            . "q.userid AS questionuserid, c.isquestion, c.annotationid, "
            . "q.visibility AS questionvisibility, "
            . "q.content AS answeredquestion, q.isdeleted AS questiondeleted, q.ishidden AS questionhidden, p.id AS annotatorid, "
            . "p.name AS pdfannotatorname, cm.id AS cmid, s.id AS issubscribed "
            . "FROM {pdfannotator_annotations} a "
            . "LEFT JOIN {pdfannotator_subscriptions} s ON a.id = s.annotationid AND s.userid = ? "
            . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id " // Question comment.
            . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id " // Answer comment.
            . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
            . "JOIN {course_modules} cm ON p.id = cm.instance "
            . "WHERE p.course = ? AND q.isquestion = 1 AND NOT c.isquestion = 1 AND NOT c.isdeleted = 1 AND cm.id $insql "
            . "ORDER BY annoid ASC";
    } else { // Or: get answers to those questions the user subscribed to.
        $sql = "SELECT c.id AS answerid, c.content AS answer, c.userid AS userid, c.visibility, "
            . "c.timemodified, c.solved AS correct, c.ishidden AS answerhidden, a.id AS annoid, a.page, q.id AS questionid, "
            . "q.userid AS questionuserid, c.isquestion, c.annotationid, "
            . "q.visibility AS questionvisibility, "
            . "q.content AS answeredquestion, q.isdeleted AS questiondeleted, q.ishidden AS questionhidden, p.id AS annotatorid, "
            . "p.name AS pdfannotatorname, cm.id AS cmid "
            . "FROM {pdfannotator_subscriptions} s "
            . "JOIN {pdfannotator_annotations} a ON a.id = s.annotationid "
            . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id " // Question comment.
            . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id " // Answer comment.
            . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
            . "JOIN {course_modules} cm ON p.id = cm.instance "
            . "WHERE s.userid = ? AND p.course = ? AND q.isquestion = 1 AND NOT c.isquestion = 1 AND NOT c.isdeleted = 1 AND cm.id $insql "
            . "ORDER BY annoid ASC";
    }

    $params = array_merge([$USER->id, $courseid], $inparams);

    $entries = $DB->get_records_sql($sql, $params);

    $res = [];
    foreach ($entries as $key => $entry) {
        if (!pdfannotator_can_see_comment($entry, $context)) {
            continue;
        }
        $entry->link = (new moodle_url('/mod/pdfannotator/view.php',
            array('id' => $entry->cmid, 'page' => $entry->page, 'annoid' => $entry->annoid, 'commid' => $entry->answerid)))->out();
        $entry->questionlink = (new moodle_url('/mod/pdfannotator/view.php',
            array('id' => $entry->cmid, 'page' => $entry->page, 'annoid' => $entry->annoid, 'commid' => $entry->questionid)))->out();

        if ($entry->questiondeleted == 1) {
            $entry->answeredquestion = get_string('deletedComment', 'pdfannotator');
        } else if ($entry->questionhidden) {
            switch ($seehidden) {
                case 0:
                    $entry->answeredquestion = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
                    break;
                case 1:
                    $entry->displayquestionhidden = true;
                    $entry->answeredquestion = $entry->answeredquestion . $labelhidden;
                    break;
                default:
                    $entry->answeredquestion = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
            }
        }

        if ($entry->answerhidden) {
            switch ($seehidden) {
                case 0:
                    $entry->answer = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
                    break;
                case 1:
                    $entry->answer = $entry->answer . $labelhidden;
                    $entry->displayhidden = true;
                    break;
                default:
                    $entry->answer = "<em>" . get_string('hiddenComment', 'pdfannotator') . "</em>";
            }
        }

        if (!$cminfo[$entry->cmid]['visible']) {  // Annotator is hidden.
            $entry->answeredquestion = $entry->answeredquestion . $labelhidden;
            $entry->answer = $entry->answer . $labelhidden;
            $entry->displayhidden = true;
        }
        if ($cminfo[$entry->cmid]['availableinfo']) {  // Annotator is restricted.
            $entry->answeredquestion = $entry->answeredquestion . $labelunavailable . " ". $cminfo[$entry->cmid]['availableinfo'];;
            $entry->answer = $entry->answer . $labelunavailable . " ". $cminfo[$entry->cmid]['availableinfo'];
            $entry->displayhidden = true;
        }

        $res[] = $entry;
    }

    return $res;
}

/**
 * Function retrieves reports and their respective reported comments from db.
 * Depending on the reportfilter, only read/unread reports or all reports are retrieved.
 *
 * @param int $courseid
 * @param int $reportfilter: 0 for unread, 1 for read, 2 for all
 * @return array of report objects
 */
function pdfannotator_get_reports($courseid, $context, $reportfilter = 0) {

    global $DB;

    $cminfo = pdfannotator_instance::get_cm_info($courseid);
    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($cminfo));

    // Retrieve reports from db as an array of stdClass objects, representing a report record each.
    $sql = "SELECT r.id as reportid, r.commentid, r.message as report, r.userid AS reportinguser, r.timecreated, r.seen, "
        . "a.page, c.id AS commentid, c.annotationid, c.userid AS commentauthor, c.content AS reportedcomment, c.timecreated AS commenttime, c.visibility, "
        . "p.id AS annotatorid, p.name AS pdfannotatorname, cm.id AS cmid, cm.visible AS cmvisible "
        . "FROM {pdfannotator_reports} r "
        . "JOIN {pdfannotator_comments} c ON r.commentid = c.id "
        . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
        . "JOIN {pdfannotator} p ON a.pdfannotatorid = p.id "
        . "JOIN {course_modules} cm ON p.id = cm.instance "
        . "WHERE cm.id $insql AND r.courseid = ?"; // Be careful with order of parameters!

    if ($reportfilter != 2) {
        $sql = $sql . ' AND r.seen = ?';
        $params = array($courseid, $reportfilter);
    } else {
        $params = array($courseid);
    }
    $params = array_merge($inparams, $params); // Be careful with order of parameters!
    $reports = $DB->get_records_sql($sql, $params);

    foreach ($reports as $report) {
        $report->link = (new moodle_url('/mod/pdfannotator/view.php',
            array('id' => $report->cmid, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid)))->out();
        $report->reportedcomment = pdfannotator_get_relativelink($report->reportedcomment, $report->commentid, $context);
        $report->reportedcomment = format_text($report->reportedcomment, $options = ['filter' => true]);
        $questionid = $DB->get_record('pdfannotator_comments', ['annotationid' => $report->annotationid, 'isquestion' => 1], 'id');
        $report->report = pdfannotator_get_relativelink($report->report, $questionid, $context);
        $report->report = format_text($report->report, $options = ['filter' => true]);
    }
    return $reports;
}

/**
 * Comparison functions (for sorting tables on overview tab).
 */
class pdfannotator_compare {

    public static function compare_votes_ascending($a, $b) {
        if ($a->usevotes == 0 && $b->usevotes == 0 && $a->votes == $b->votes) {
            return 0;
        }
        return ($a->usevotes != 1 || ($a->votes < $b->votes)) ? -1 : 1;
    }

    public static function compare_votes_descending($a, $b) {
        if ($a->usevotes == 0 && $b->usevotes == 0 && $a->votes == $b->votes) {
            return 0;
        }
        return ($b->usevotes != 1 || ($a->votes > $b->votes)) ? -1 : 1;
    }

    public static function compare_answers_ascending($a, $b) {
        if ($a->answercount == $b->answercount) {
            return 0;
        }
        return ($a->answercount < $b->answercount) ? -1 : 1;
    }

    public static function compare_answers_descending($a, $b) {
        if ($a->answercount == $b->answercount) {
            return 0;
        }
        return ($a->answercount > $b->answercount) ? -1 : 1;
    }

    public static function compare_time_ascending($a, $b) {
        if ($a->timemodified == $b->timemodified) {
            return 0;
        }
        return ($a->timemodified < $b->timemodified) ? -1 : 1;
    }

    public static function compare_time_descending($a, $b) {
        if ($a->timemodified == $b->timemodified) {
            return 0;
        }
        return ($a->timemodified > $b->timemodified) ? -1 : 1;
    }

    public static function compare_lastanswertime_ascending($a, $b) {
        if ($a->lastanswered == $b->lastanswered) {
            return 0;
        }
        return ($a->lastanswered < $b->lastanswered) ? -1 : 1;
    }

    public static function compare_lastanswertime_descending($a, $b) {
        if ($a->lastanswered == $b->lastanswered) {
            return 0;
        }
        return ($a->lastanswered > $b->lastanswered) ? -1 : 1;
    }

    public static function compare_commenttime_ascending($a, $b) {
        if ($a->commenttime == $b->commenttime) {
            return 0;
        }
        return ($a->commenttime < $b->commenttime) ? -1 : 1;
    }

    public static function compare_commenttime_descending($a, $b) {
        if ($a->commenttime == $b->commenttime) {
            return 0;
        }
        return ($a->commenttime > $b->commenttime) ? -1 : 1;
    }

    public static function compare_creationtime_ascending($a, $b) {
        if ($a->timecreated == $b->timecreated) {
            return 0;
        }
        return ($a->timecreated < $b->timecreated) ? -1 : 1;
    }

    public static function compare_creationtime_descending($a, $b) {
        if ($a->timecreated == $b->timecreated) {
            return 0;
        }
        return ($a->timecreated > $b->timecreated) ? -1 : 1;
    }

    public static function compare_alphabetically_ascending($a, $b) {
        if ($a->pdfannotatorname == $b->pdfannotatorname) {
            return 0;
        }
        if (strcasecmp($a->pdfannotatorname, $b->pdfannotatorname) < 0) {
            return -1;
        } else {
            return 1;
        }
    }

    public static function compare_alphabetically_descending($a, $b) {
        if ($a->pdfannotatorname == $b->pdfannotatorname) {
            return 0;
        }
        if (strcasecmp($a->pdfannotatorname, $b->pdfannotatorname) > 0) {
            return -1;
        } else {
            return 1;
        }
    }

    public static function compare_question_ascending($a, $b) {
        if ($a->answeredquestion == $b->answeredquestion) {
            return 0;
        }
        if (strcasecmp($a->answeredquestion, $b->answeredquestion) < 0) {
            return -1;
        } else {
            return 1;
        }
    }

    public static function compare_question_descending($a, $b) {
        if ($a->answeredquestion == $b->answeredquestion) {
            return 0;
        }
        if (strcasecmp($a->answeredquestion, $b->answeredquestion) > 0) {
            return -1;
        } else {
            return 1;
        }
    }

}

/**
 * Function sorts entries in a table according to time, number of votes or annotator.
 * Function is applicable to 'unsolved questions' and 'my posts' category on overview page.
 *
 * @param array $questions
 * @param string $sortcriterium The column according to which the table should be sorted
 * @param int $sortorder 3 for descending, 4 for ascending
 */
function pdfannotator_sort_entries($questions, $sortcriterium, $sortorder) {
    switch ($sortcriterium) {
        case 'col1':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_time_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_time_descending');
            }
            break;
        case 'col2':
            if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_votes_ascending');
            } else if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_votes_descending');
            }
            break;
        case 'col3':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_alphabetically_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_alphabetically_descending');
            }
            break;
        default:
    }
    return $questions;
}

function pdfannotator_sort_questions($questions, $sortcriterium, $sortorder) {
    switch ($sortcriterium) {
        case 'col1':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_creationtime_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_creationtime_descending');
            }
            break;
        case 'col2':
            if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_votes_ascending');
            } else if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_votes_descending');
            }
            break;
        case 'col3':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_answers_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_answers_descending');
            }
            break;
        case 'col4':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_lastanswertime_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_lastanswertime_descending');
            }
            break;
        case 'col5':
            if ($sortorder === 4) {
                usort($questions, 'pdfannotator_compare::compare_alphabetically_ascending');
            } else if ($sortorder === 3) {
                usort($questions, 'pdfannotator_compare::compare_alphabetically_descending');
            }
            break;
        default:
    }
    return $questions;
}

/**
 * Function sorts entries in a table according to annotator or time.
 * Applicable for overview answers category.
 *
 * XXX Maybe rename 'colx' into something like 'time', so as to avoid code redundancy.
 *
 * @param array $answers
 * @param int $sortcriterium
 * @param int $sortorder
 * @return array $answers
 */
function pdfannotator_sort_answers($answers, $sortcriterium, $sortorder) {
    switch ($sortcriterium) {
        case 'col4':
            if ($sortorder === 4) {
                usort($answers, 'pdfannotator_compare::compare_alphabetically_ascending');
            } else if ($sortorder === 3) {
                usort($answers, 'pdfannotator_compare::compare_alphabetically_descending');
            }
            break;
        case 'col2':
            if ($sortorder === 4) {
                usort($answers, 'pdfannotator_compare::compare_time_ascending');
            } else if ($sortorder === 3) {
                usort($answers, 'pdfannotator_compare::compare_time_descending');
            }
            break;
        case 'col3':
            if ($sortorder === 4) {
                usort($answers, 'pdfannotator_compare::compare_question_ascending');
            } else if ($sortorder === 3) {
                usort($answers, 'pdfannotator_compare::compare_question_descending');
            }
            break;
        default:
    }
    return $answers;
}

/**
 *
 * @param array $reports
 * @param string $sortcriterium
 * @param int $sortorder
 * @return array $reports (sorted)
 */
function pdfannotator_sort_reports($reports, $sortcriterium, $sortorder) {
    switch ($sortcriterium) {
        case 'col1':
            if ($sortorder === 4) {
                usort($reports, 'pdfannotator_compare::compare_creationtime_ascending');
            } else if ($sortorder === 3) {
                usort($reports, 'pdfannotator_compare::compare_creationtime_descending');
            }
            break;
        case 'col3':
            if ($sortorder === 4) {
                usort($reports, 'pdfannotator_compare::compare_commenttime_ascending');
            } else if ($sortorder === 3) {
                usort($reports, 'pdfannotator_compare::compare_commenttime_descending');
            }
            break;
        default:
    }
    return $reports;
}

/**
 * Function takes an array and returns its first key.
 *
 * @param array $array
 * @return mixed
 */
function pdfannotator_get_first_key_in_array($array) {

    if (!function_exists('array_key_first')) { // Function exists in PHP version 7.3 and later.
        /**
         * Gets the first key of an array
         *
         * @param array $array
         * @return mixed
         */

        function array_key_first(array $array) {
            if (count($array)) {
                reset($array);
                return key($array);
            }
            return null;
        }

    }
    return array_key_first($array);
}

/**
 * This function renders the table of unsolved questions on the overview page.
 *
 * @param array $questions
 * @param int $thiscourse
 * @param Moodle url object $url
 * @param int $currentpage
 */
function pdfannotator_print_questions($questions, $thiscourse, $urlparams, $currentpage, $itemsperpage, $context) {

    global $CFG, $OUTPUT;
    require_once("$CFG->dirroot/mod/pdfannotator/model/overviewtable.php");

    $showdropdown = has_capability('mod/pdfannotator:forwardquestions', $context);
    $questioncount = count($questions);
    $usepagination = !($itemsperpage == -1 || $itemsperpage >= $questioncount);
    $offset = $currentpage * $itemsperpage;

    if ($usepagination == 1 && ($offset >= $questioncount)) {
        $offset = 0;
        $urlparams['page'] = 0;
    }
    $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);

    // Define flexible table.
    $table = new questionstable($url, $showdropdown);
    $table->setup();
    // $table->pageable(false);
    // Sort the entries of the table according to time or number of votes.
    if (!empty($sortinfo = $table->get_sort_columns())) {
        $sortcriterium = pdfannotator_get_first_key_in_array($sortinfo); // Returns the name (e.g. col2) of the column which was clicked for sorting.
        $sortorder = $sortinfo[$sortcriterium]; // 3 for descending, 4 for ascending.
        $questions = pdfannotator_sort_questions($questions, $sortcriterium, $sortorder);
    }

    // Add data to the table and print the requested table (page).
    if (pdfannotator_is_phone() || $itemsperpage == -1 || $itemsperpage >= $questioncount) { // No pagination.
        foreach ($questions as $question) {
            pdfannotator_questionstable_add_row($thiscourse, $table, $question, $urlparams, $showdropdown);
        }
    } else {
        $table->pagesize($itemsperpage, $questioncount);
        for ($i = $offset; $i < $questioncount; $i++) {
            $question = $questions[$i];
            if ($itemsperpage === 0) {
                break;
            }
            pdfannotator_questionstable_add_row($thiscourse, $table, $question, $urlparams, $showdropdown);
            $itemsperpage--;
        }
    }
    $table->finish_html();
}

/**
 * Function prints a table view of all answers to questions the current
 * user asked or subscribed to.
 *
 * @param int $annotator
 * @param Moodle url object $url
 * @param int $thiscourse
 */
function pdfannotator_print_answers($data, $thiscourse, $url, $currentpage, $itemsperpage, $cmid, $answerfilter, $context) {

    global $CFG, $OUTPUT;
    require_once("$CFG->dirroot/mod/pdfannotator/model/overviewtable.php");

    $table = new answerstable($url);
    $table->setup();

    // Sort the entries of the table according to time or number of votes.
    if (!empty($sortinfo = $table->get_sort_columns())) {
        $sortcriterium = pdfannotator_get_first_key_in_array($sortinfo); // Returns the name (e.g. col2) of the column which was clicked for sorting.
        $sortorder = $sortinfo[$sortcriterium]; // 3 for descending, 4 for ascending.
        $data = pdfannotator_sort_answers($data, $sortcriterium, $sortorder);
    }

    // Add data to the table and print the requested table page.
    if ($itemsperpage == -1) { // No pagination.
        foreach ($data as $answer) {
            pdfannotator_answerstable_add_row($thiscourse, $table, $answer, $cmid, $currentpage, $itemsperpage, $answerfilter, $context);
        }
    } else {
        $answercount = count($data);
        $table->pagesize($itemsperpage, $answercount);
        $offset = $currentpage * $itemsperpage;
        $rowstoprint = $itemsperpage;
        for ($i = $offset; $i < $answercount; $i++) {
            $answer = $data[$i];
            if ($rowstoprint === 0) {
                break;
            }
            pdfannotator_answerstable_add_row($thiscourse, $table, $answer, $cmid, $currentpage, $itemsperpage, $answerfilter, $context);
            $rowstoprint--;
        }
    }
    $table->finish_html();
}

/**
 *
 * @param type $posts
 * @param type $url
 * @param type $thiscourse
 */
function pdfannotator_print_this_users_posts($posts, $thiscourse, $url, $currentpage, $itemsperpage) {

    global $CFG;
    require_once("$CFG->dirroot/mod/pdfannotator/model/overviewtable.php");

    $table = new userspoststable($url);
    $table->setup();

    // Sort the entries of the table according to time or number of votes.
    if (!empty($sortinfo = $table->get_sort_columns())) {
        $sortcriterium = pdfannotator_get_first_key_in_array($sortinfo); // Returns the name (e.g. col2) of the column which was clicked for sorting.
        $sortorder = $sortinfo[$sortcriterium]; // 3 for descending, 4 for ascending.
        $posts = pdfannotator_sort_entries($posts, $sortcriterium, $sortorder);
    }

    // Add data to the table and print the requested table page.
    if ($itemsperpage == -1) {
        foreach ($posts as $post) {
            pdfannotator_userspoststable_add_row($table, $post);
        }
    } else {
        $postcount = count($posts);
        $table->pagesize($itemsperpage, $postcount);
        $offset = $currentpage * $itemsperpage;
        for ($i = $offset; $i < $postcount; $i++) {
            $post = $posts[$i];
            if ($itemsperpage === 0) {
                break;
            }
            pdfannotator_userspoststable_add_row($table, $post);
            $itemsperpage--;
        }
    }
    $table->finish_html();
}

/**
 * Function prints a table view of all comments that were reported as inappropriate.
 *
 * @param array of objects $reports
 * @param int $thiscourse
 * @param Moodle url object $url
 * @param int $currentpage
 */
function pdfannotator_print_reports($reports, $thiscourse, $url, $currentpage, $itemsperpage, $cmid, $reportfilter, $context) {

    global $CFG, $OUTPUT;
    require_once("$CFG->dirroot/mod/pdfannotator/model/overviewtable.php");

    $table = new reportstable($url);
    $table->setup();
    // Sort the entries of the table according to time or number of votes.
    if (!empty($sortinfo = $table->get_sort_columns())) {
        $sortcriterium = pdfannotator_get_first_key_in_array($sortinfo); // Returns the name (e.g. col2) of the column which was clicked for sorting.
        $sortorder = $sortinfo[$sortcriterium]; // 3 for descending, 4 for ascending.
        $reports = pdfannotator_sort_reports($reports, $sortcriterium, $sortorder);
    }
    // Add data to the table and print the requested table page.
    if ($itemsperpage == -1) {
        foreach ($reports as $report) {
            pdfannotator_reportstable_add_row($thiscourse, $table, $report, $cmid, $itemsperpage, $reportfilter, $currentpage, $context);
        }
    } else {
        $reportcount = count($reports);
        $table->pagesize($itemsperpage, $reportcount);
        $offset = $currentpage * $itemsperpage;
        $rowstoprint = $itemsperpage;
        for ($i = $offset; $i < $reportcount; $i++) {
            $report = $reports[$i];
            if ($rowstoprint === 0) {
                break;
            }
            pdfannotator_reportstable_add_row($thiscourse, $table, $report, $cmid, $itemsperpage, $reportfilter, $currentpage, $context);
            $rowstoprint--;
        }
    }
    $table->finish_html();
}

/**
 * This function adds a row of data to the overview table that displays all
 * unsolved questions in the course.
 *
 * @param int $thiscourse
 * @param questionstable $table
 * @param object $question
 */
function pdfannotator_questionstable_add_row($thiscourse, $table, $question, $urlparams, $showdropdown) {

    global $CFG, $PAGE;
    if ($question->visibility == 'anonymous') {
        $author = get_string('anonymous', 'pdfannotator');
    } else {
        $author = "<a href=" . $CFG->wwwroot . "/user/view.php?id=$question->userid&course=$thiscourse>" . pdfannotator_get_username($question->userid) . "</a>";
    }
    $time = pdfannotator_get_user_datetime_shortformat($question->timecreated);
    if (!empty($question->lastanswered)) { // ! ($question->lastanswered != $question->timecreated) {
        if ($question->lastuservisibility == 'anonymous') {
            $lastresponder = get_string('anonymous', 'pdfannotator');
        } else {
            $lastresponder = "<a href=" . $CFG->wwwroot . "/user/view.php?id=$question->lastuser&course=$thiscourse>" . pdfannotator_get_username($question->lastuser) . "</a>";
        }
        $answertime = pdfannotator_timeago($question->lastanswered);
        $lastanswered = $lastresponder . "<br>" . $answertime;
    } else {
        $lastanswered = '-';
    }
    $classname = '';
    if (isset($question->displayhidden)) {
        $classname = 'dimmed_text';
    }
    $content = "<a href=$question->link class='more'>$question->content</a>";
    $pdfannotatorname = $question->pdfannotatorname;

    $data = array($content, $author . '<br>' . $time, $question->votes, $question->answercount, $lastanswered, $pdfannotatorname);

    if ($showdropdown) {
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $dropdown = $myrenderer->render_dropdownmenu(new questionmenu($question->commentid, $urlparams));
        $data[] = $dropdown;
    }
    $table->add_data($data, $classname);
}

/**
 * This function adds a row of data to the overview table that displays
 * answers to any question the user subscribed to.
 *
 * @param int $thiscourse
 * @param answerstable $table
 * @param object $answer
 */
function pdfannotator_answerstable_add_row($thiscourse, $table, $answer, $cmid, $currentpage, $itemsperpage, $answerfilter, $context) {
    global $CFG, $PAGE;

    $answer->answer = pdfannotator_get_relativelink($answer->answer, $answer->answerid, $context);
    $answer->answer = format_text($answer->answer, $options = ['filter' => true]);
    $answer->answeredquestion = pdfannotator_get_relativelink($answer->answeredquestion, $answer->questionid, $context);
    $answer->answeredquestion = format_text($answer->answeredquestion, $options = ['filter' => true]);


    if (isset($answer->displayquestionhidden)) {
        $question = "<a class='" . $answer->annoid . " more dimmed' href=$answer->questionlink>$answer->answeredquestion</a>";
    } else {
        $question = "<a class='" . $answer->annoid . " more' href=$answer->questionlink>$answer->answeredquestion</a>";
    }
    $pdfannotatorname = $answer->pdfannotatorname;
    if ($answer->correct) {
        $checked = "<i class='icon fa fa-check fa-fw' style='color:green;'></i>";
    } else {
        $checked = "";
    }
    $answerid = 'answer_' . $answer->answerid;
    $answerlink = "<a id=$answerid data-question=$answer->questionid href=$answer->link class='more'>$answer->answer</a>";

    if ($answer->visibility == 'anonymous') {
        $answeredby = get_string('anonymous', 'pdfannotator');
    } else {
        $answeredby = "<a href=" . $CFG->wwwroot . "/user/view.php?id=$answer->userid&course=$thiscourse>" . pdfannotator_get_username($answer->userid) . "</a>";
    }
    $answertime = pdfannotator_get_user_datetime_shortformat($answer->timemodified);

    if (empty($answer->issubscribed)) {
        $issubscribed = null;
    } else {
        $issubscribed = $answer->issubscribed;
    }

    $classname = '';
    if (isset($answer->displayhidden)) {
        $classname = 'dimmed_text';
    }

    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    $dropdown = $myrenderer->render_dropdownmenu(new answermenu($answer->annoid, $issubscribed, $cmid, $currentpage, $itemsperpage, $answerfilter));

    $table->add_data(array($answerlink, $checked, $answeredby . '<br>' . $answertime, $question, $pdfannotatorname, $dropdown), $classname);
}

/**
 * This function adds a row of data to the overview table that displays all
 * comments the current user posted in this course.
 *
 * @param userspoststable $table
 * @param object $post
 */
function pdfannotator_userspoststable_add_row($table, $post) {
    $time = pdfannotator_get_user_datetime_shortformat($post->timemodified);
    $content = "<a href=$post->link class='more'>$post->content</a>";

    $classname = '';
    if (isset($post->displayhidden)) {
        $classname = 'dimmed_text';
    }
    $pdfannotatorname = $post->pdfannotatorname;
    $table->add_data(array($content, $time, $post->votes, $pdfannotatorname), $classname);
}

/**
 * This function adds a row of data to the overview table that displays all
 * comments reported in this course.
 *
 * @param int $thiscourse
 * @param reportstable $table
 * @param object $report
 * @param int $cmid
 * @param int $itemsperpage
 * @param int $reportfilter
 * @param int $currentpage
 */
function pdfannotator_reportstable_add_row($thiscourse, $table, $report, $cmid, $itemsperpage, $reportfilter, $currentpage, $context) {
    global $CFG, $PAGE, $DB;
    
    $questionid = $DB->get_record('pdfannotator_comments', ['annotationid' => $report->annotationid, 'isquestion' => 1], 'id');
    $report->report = pdfannotator_get_relativelink($report->report, $questionid, $context);
    $report->reportedcomment = pdfannotator_get_relativelink($report->reportedcomment, $report->commentid, $context);

    // Prepare report data for display.
    $reportid = 'report_' . $report->reportid;
    $reportedcommmentlink = "<a id=$reportid href=$report->link class='more'>$report->reportedcomment</a>";
    $writtenby = "<a href=" . $CFG->wwwroot . "/user/view.php?id=$report->commentauthor&course=$thiscourse>" . pdfannotator_get_username($report->commentauthor) . "</a>";
    $commenttime = pdfannotator_get_user_datetime_shortformat($report->commenttime);
    $reportedby = "<a href=" . $CFG->wwwroot . "/user/view.php?id=$report->reportinguser&course=$thiscourse>" . pdfannotator_get_username($report->reportinguser) . "</a>";
    $reporttime = pdfannotator_get_user_datetime_shortformat($report->timecreated);
    $report->report = "<div class='more'>$report->report</div>";

    $classname = '';
    if (!($report->cmvisible)) {
        $classname = 'dimmed_text';
    }

    // Create action dropdown menu.
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    $dropdown = $myrenderer->render_dropdownmenu(new reportmenu($report, $cmid, $currentpage, $itemsperpage, $reportfilter));

    // Add a new row to the reports table.
    $table->add_data(array($report->report, $reportedby . '<br>' . $reporttime, $reportedcommmentlink, $writtenby . '<br>' . $commenttime, $dropdown), $classname);
}


/**
 * Function takes a moodle timestamp, calculates how much time has since elapsed
 * and returns this information as a string (e.g.: '3 days ago').
 *
 * @param int $timestamp
 * @return string
 */
function pdfannotator_timeago($timestamp) {
    $strtime = array(get_string('second', 'pdfannotator'), get_string('minute', 'pdfannotator'), get_string('hour', 'pdfannotator'));
    $strtime[] = get_string('day', 'pdfannotator');
    $strtime[] = get_string('month', 'pdfannotator');
    $strtime[] = get_string('year', 'pdfannotator');
    $strtimeplural = array(get_string('seconds', 'pdfannotator'), get_string('minutes', 'pdfannotator'));
    $strtimeplural[] = get_string('hours', 'pdfannotator');
    $strtimeplural[] = get_string('days', 'pdfannotator');
    $strtimeplural[] = get_string('months', 'pdfannotator');
    $strtimeplural[] = get_string('years', 'pdfannotator');
    $length = array("60", "60", "24", "30", "12", "10");
    $currenttime = time();
    if ($currenttime >= $timestamp) {
        $diff = time() - $timestamp;
        if ($diff < 60) {
            return get_string('justnow', 'pdfannotator');
        }
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }
        $diff = intval(round($diff));
        if ($diff === 1) {
            $diff = $diff . ' ' . $strtime[$i];
        } else {
            $diff = $diff . ' ' . $strtimeplural[$i];
        }
        return get_string('ago', 'pdfannotator', $diff);
    }
}

/**
 * Function takes a moodle timestamp, calculates how much time has since elapsed
 * and returns this information as a string. If the timestamp is older than 2 days,
 * the ecaxt datetime is returned. Otherwise, the string looks like '3 days ago'.
 *
 * @param type $timestamp
 * @return string
 */
function pdfannotator_optional_timeago($timestamp) {
    $currenttime = time();
    // For entries older than 2 days, display the exact time.
    if ($currenttime - $timestamp > 172799) {
        return pdfannotator_get_user_datetime_shortformat($timestamp);
    } else {
        return pdfannotator_timeago($timestamp);
    }
}

function pdfannotator_is_mobile_device() {
    $param = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_DEFAULT); // XXX How to filter, here?
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $param);
}

function pdfannotator_is_phone() {
    $param = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_DEFAULT); // XXX How to filter, here?
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $param);
}


function pdfannotator_get_last_answer($annotationid, $context) {
    global $DB;
    $params = array('isquestion' => 0, 'annotationid' => $annotationid);
    $answers = $DB->get_records('pdfannotator_comments', $params, 'timecreated DESC' );

    foreach ($answers as $answer) {
        if (!pdfannotator_can_see_comment($answer, $context)) {
            continue;
        } else {
            $answer->content = pdfannotator_get_relativelink($answer->content, $answer->id, $context);
            return $answer;
        }
    }
    return null;
}

function pdfannotator_can_see_comment($comment, $context) {
    global $USER, $DB;
    if (is_array($comment)) {
        $comment = (object)$comment;
    }

    // If the comment is an answer, it is always saved as public. So, we check the visibility of the corresponding question.
    if (!$comment->isquestion) {
        $question = $DB->get_record('pdfannotator_comments', array('annotationid' => $comment->annotationid, 'isquestion' => '1'));
        $question = (object)$question;
    } else {
        $question = $comment;
    }

    // Private Comments are only displayed for the author.
    if ($question->visibility == "private" && $USER->id != $question->userid) {
        return false;
    }

    // Protected Comments are only displayed for the author and for the managers.
    if ($question->visibility == "protected" && $USER->id != $question->userid && !has_capability('mod/pdfannotator:viewprotectedcomments', $context)) {
        return false;
    }
    return true;
}

/**
 * Count how many answers has a question with $annotationid
 * return only answers that the user can see
 */
function pdfannotator_count_answers($annotationid, $context) {
    global $DB;
    $params = array('isquestion' => 0, 'annotationid' => $annotationid);
    $answers = $DB->get_records('pdfannotator_comments', $params);
    $count = 0;

    foreach ($answers as $answer) {

        if (!pdfannotator_can_see_comment($answer, $context)) {
            continue;
        }
        $count++;
    }
    return $count;
}