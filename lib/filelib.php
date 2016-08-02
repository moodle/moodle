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
 * Functions for file handling.
 *
 * @package   core_files
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * BYTESERVING_BOUNDARY - string unique string constant.
 */
define('BYTESERVING_BOUNDARY', 's1k2o3d4a5k6s7');

/**
 * Unlimited area size constant
 */
define('FILE_AREA_MAX_BYTES_UNLIMITED', -1);

require_once("$CFG->libdir/filestorage/file_exceptions.php");
require_once("$CFG->libdir/filestorage/file_storage.php");
require_once("$CFG->libdir/filestorage/zip_packer.php");
require_once("$CFG->libdir/filebrowser/file_browser.php");

/**
 * Encodes file serving url
 *
 * @deprecated use moodle_url factory methods instead
 *
 * @todo MDL-31071 deprecate this function
 * @global stdClass $CFG
 * @param string $urlbase
 * @param string $path /filearea/itemid/dir/dir/file.exe
 * @param bool $forcedownload
 * @param bool $https https url required
 * @return string encoded file url
 */
function file_encode_url($urlbase, $path, $forcedownload=false, $https=false) {
    global $CFG;

//TODO: deprecate this

    if ($CFG->slasharguments) {
        $parts = explode('/', $path);
        $parts = array_map('rawurlencode', $parts);
        $path  = implode('/', $parts);
        $return = $urlbase.$path;
        if ($forcedownload) {
            $return .= '?forcedownload=1';
        }
    } else {
        $path = rawurlencode($path);
        $return = $urlbase.'?file='.$path;
        if ($forcedownload) {
            $return .= '&amp;forcedownload=1';
        }
    }

    if ($https) {
        $return = str_replace('http://', 'https://', $return);
    }

    return $return;
}

/**
 * Detects if area contains subdirs,
 * this is intended for file areas that are attached to content
 * migrated from 1.x where subdirs were allowed everywhere.
 *
 * @param context $context
 * @param string $component
 * @param string $filearea
 * @param string $itemid
 * @return bool
 */
function file_area_contains_subdirs(context $context, $component, $filearea, $itemid) {
    global $DB;

    if (!isset($itemid)) {
        // Not initialised yet.
        return false;
    }

    // Detect if any directories are already present, this is necessary for content upgraded from 1.x.
    $select = "contextid = :contextid AND component = :component AND filearea = :filearea AND itemid = :itemid AND filepath <> '/' AND filename = '.'";
    $params = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid);
    return $DB->record_exists_select('files', $select, $params);
}

/**
 * Prepares 'editor' formslib element from data in database
 *
 * The passed $data record must contain field foobar, foobarformat and optionally foobartrust. This
 * function then copies the embedded files into draft area (assigning itemids automatically),
 * creates the form element foobar_editor and rewrites the URLs so the embedded images can be
 * displayed.
 * In your mform definition, you must have an 'editor' element called foobar_editor. Then you call
 * your mform's set_data() supplying the object returned by this function.
 *
 * @category files
 * @param stdClass $data database field that holds the html text with embedded media
 * @param string $field the name of the database field that holds the html text with embedded media
 * @param array $options editor options (like maxifiles, maxbytes etc.)
 * @param stdClass $context context of the editor
 * @param string $component
 * @param string $filearea file area name
 * @param int $itemid item id, required if item exists
 * @return stdClass modified data object
 */
function file_prepare_standard_editor($data, $field, array $options, $context=null, $component=null, $filearea=null, $itemid=null) {
    $options = (array)$options;
    if (!isset($options['trusttext'])) {
        $options['trusttext'] = false;
    }
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['maxfiles'])) {
        $options['maxfiles'] = 0; // no files by default
    }
    if (!isset($options['noclean'])) {
        $options['noclean'] = false;
    }

    //sanity check for passed context. This function doesn't expect $option['context'] to be set
    //But this function is called before creating editor hence, this is one of the best places to check
    //if context is used properly. This check notify developer that they missed passing context to editor.
    if (isset($context) && !isset($options['context'])) {
        //if $context is not null then make sure $option['context'] is also set.
        debugging('Context for editor is not set in editoroptions. Hence editor will not respect editor filters', DEBUG_DEVELOPER);
    } else if (isset($options['context']) && isset($context)) {
        //If both are passed then they should be equal.
        if ($options['context']->id != $context->id) {
            $exceptionmsg = 'Editor context ['.$options['context']->id.'] is not equal to passed context ['.$context->id.']';
            throw new coding_exception($exceptionmsg);
        }
    }

    if (is_null($itemid) or is_null($context)) {
        $contextid = null;
        $itemid = null;
        if (!isset($data)) {
            $data = new stdClass();
        }
        if (!isset($data->{$field})) {
            $data->{$field} = '';
        }
        if (!isset($data->{$field.'format'})) {
            $data->{$field.'format'} = editors_get_preferred_format();
        }
        if (!$options['noclean']) {
            $data->{$field} = clean_text($data->{$field}, $data->{$field.'format'});
        }

    } else {
        if ($options['trusttext']) {
            // noclean ignored if trusttext enabled
            if (!isset($data->{$field.'trust'})) {
                $data->{$field.'trust'} = 0;
            }
            $data = trusttext_pre_edit($data, $field, $context);
        } else {
            if (!$options['noclean']) {
                $data->{$field} = clean_text($data->{$field}, $data->{$field.'format'});
            }
        }
        $contextid = $context->id;
    }

    if ($options['maxfiles'] != 0) {
        $draftid_editor = file_get_submitted_draft_itemid($field);
        $currenttext = file_prepare_draft_area($draftid_editor, $contextid, $component, $filearea, $itemid, $options, $data->{$field});
        $data->{$field.'_editor'} = array('text'=>$currenttext, 'format'=>$data->{$field.'format'}, 'itemid'=>$draftid_editor);
    } else {
        $data->{$field.'_editor'} = array('text'=>$data->{$field}, 'format'=>$data->{$field.'format'}, 'itemid'=>0);
    }

    return $data;
}

/**
 * Prepares the content of the 'editor' form element with embedded media files to be saved in database
 *
 * This function moves files from draft area to the destination area and
 * encodes URLs to the draft files so they can be safely saved into DB. The
 * form has to contain the 'editor' element named foobar_editor, where 'foobar'
 * is the name of the database field to hold the wysiwyg editor content. The
 * editor data comes as an array with text, format and itemid properties. This
 * function automatically adds $data properties foobar, foobarformat and
 * foobartrust, where foobar has URL to embedded files encoded.
 *
 * @category files
 * @param stdClass $data raw data submitted by the form
 * @param string $field name of the database field containing the html with embedded media files
 * @param array $options editor options (trusttext, subdirs, maxfiles, maxbytes etc.)
 * @param stdClass $context context, required for existing data
 * @param string $component file component
 * @param string $filearea file area name
 * @param int $itemid item id, required if item exists
 * @return stdClass modified data object
 */
function file_postupdate_standard_editor($data, $field, array $options, $context, $component=null, $filearea=null, $itemid=null) {
    $options = (array)$options;
    if (!isset($options['trusttext'])) {
        $options['trusttext'] = false;
    }
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['maxfiles'])) {
        $options['maxfiles'] = 0; // no files by default
    }
    if (!isset($options['maxbytes'])) {
        $options['maxbytes'] = 0; // unlimited
    }

    if ($options['trusttext']) {
        $data->{$field.'trust'} = trusttext_trusted($context);
    } else {
        $data->{$field.'trust'} = 0;
    }

    $editor = $data->{$field.'_editor'};

    if ($options['maxfiles'] == 0 or is_null($filearea) or is_null($itemid) or empty($editor['itemid'])) {
        $data->{$field} = $editor['text'];
    } else {
        $data->{$field} = file_save_draft_area_files($editor['itemid'], $context->id, $component, $filearea, $itemid, $options, $editor['text'], $options['forcehttps']);
    }
    $data->{$field.'format'} = $editor['format'];

    return $data;
}

/**
 * Saves text and files modified by Editor formslib element
 *
 * @category files
 * @param stdClass $data $database entry field
 * @param string $field name of data field
 * @param array $options various options
 * @param stdClass $context context - must already exist
 * @param string $component
 * @param string $filearea file area name
 * @param int $itemid must already exist, usually means data is in db
 * @return stdClass modified data obejct
 */
function file_prepare_standard_filemanager($data, $field, array $options, $context=null, $component=null, $filearea=null, $itemid=null) {
    $options = (array)$options;
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (is_null($itemid) or is_null($context)) {
        $itemid = null;
        $contextid = null;
    } else {
        $contextid = $context->id;
    }

    $draftid_editor = file_get_submitted_draft_itemid($field.'_filemanager');
    file_prepare_draft_area($draftid_editor, $contextid, $component, $filearea, $itemid, $options);
    $data->{$field.'_filemanager'} = $draftid_editor;

    return $data;
}

/**
 * Saves files modified by File manager formslib element
 *
 * @todo MDL-31073 review this function
 * @category files
 * @param stdClass $data $database entry field
 * @param string $field name of data field
 * @param array $options various options
 * @param stdClass $context context - must already exist
 * @param string $component
 * @param string $filearea file area name
 * @param int $itemid must already exist, usually means data is in db
 * @return stdClass modified data obejct
 */
function file_postupdate_standard_filemanager($data, $field, array $options, $context, $component, $filearea, $itemid) {
    $options = (array)$options;
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['maxfiles'])) {
        $options['maxfiles'] = -1; // unlimited
    }
    if (!isset($options['maxbytes'])) {
        $options['maxbytes'] = 0; // unlimited
    }

    if (empty($data->{$field.'_filemanager'})) {
        $data->$field = '';

    } else {
        file_save_draft_area_files($data->{$field.'_filemanager'}, $context->id, $component, $filearea, $itemid, $options);
        $fs = get_file_storage();

        if ($fs->get_area_files($context->id, $component, $filearea, $itemid)) {
            $data->$field = '1'; // TODO: this is an ugly hack (skodak)
        } else {
            $data->$field = '';
        }
    }

    return $data;
}

/**
 * Generate a draft itemid
 *
 * @category files
 * @global moodle_database $DB
 * @global stdClass $USER
 * @return int a random but available draft itemid that can be used to create a new draft
 * file area.
 */
function file_get_unused_draft_itemid() {
    global $DB, $USER;

    if (isguestuser() or !isloggedin()) {
        // guests and not-logged-in users can not be allowed to upload anything!!!!!!
        print_error('noguest');
    }

    $contextid = context_user::instance($USER->id)->id;

    $fs = get_file_storage();
    $draftitemid = rand(1, 999999999);
    while ($files = $fs->get_area_files($contextid, 'user', 'draft', $draftitemid)) {
        $draftitemid = rand(1, 999999999);
    }

    return $draftitemid;
}

/**
 * Initialise a draft file area from a real one by copying the files. A draft
 * area will be created if one does not already exist. Normally you should
 * get $draftitemid by calling file_get_submitted_draft_itemid('elementname');
 *
 * @category files
 * @global stdClass $CFG
 * @global stdClass $USER
 * @param int $draftitemid the id of the draft area to use, or 0 to create a new one, in which case this parameter is updated.
 * @param int $contextid This parameter and the next two identify the file area to copy files from.
 * @param string $component
 * @param string $filearea helps indentify the file area.
 * @param int $itemid helps identify the file area. Can be null if there are no files yet.
 * @param array $options text and file options ('subdirs'=>false, 'forcehttps'=>false)
 * @param string $text some html content that needs to have embedded links rewritten to point to the draft area.
 * @return string|null returns string if $text was passed in, the rewritten $text is returned. Otherwise NULL.
 */
function file_prepare_draft_area(&$draftitemid, $contextid, $component, $filearea, $itemid, array $options=null, $text=null) {
    global $CFG, $USER, $CFG;

    $options = (array)$options;
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }

    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();

    if (empty($draftitemid)) {
        // create a new area and copy existing files into
        $draftitemid = file_get_unused_draft_itemid();
        $file_record = array('contextid'=>$usercontext->id, 'component'=>'user', 'filearea'=>'draft', 'itemid'=>$draftitemid);
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
                $draftfile = $fs->create_file_from_storedfile($file_record, $file);
                // XXX: This is a hack for file manager (MDL-28666)
                // File manager needs to know the original file information before copying
                // to draft area, so we append these information in mdl_files.source field
                // {@link file_storage::search_references()}
                // {@link file_storage::search_references_count()}
                $sourcefield = $file->get_source();
                $newsourcefield = new stdClass;
                $newsourcefield->source = $sourcefield;
                $original = new stdClass;
                $original->contextid = $contextid;
                $original->component = $component;
                $original->filearea  = $filearea;
                $original->itemid    = $itemid;
                $original->filename  = $file->get_filename();
                $original->filepath  = $file->get_filepath();
                $newsourcefield->original = file_storage::pack_reference($original);
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
    } else {
        // nothing to do
    }

    if (is_null($text)) {
        return null;
    }

    // relink embedded files - editor can not handle @@PLUGINFILE@@ !
    return file_rewrite_pluginfile_urls($text, 'draftfile.php', $usercontext->id, 'user', 'draft', $draftitemid, $options);
}

/**
 * Convert encoded URLs in $text from the @@PLUGINFILE@@/... form to an actual URL.
 * Passing a new option reverse = true in the $options var will make the function to convert actual URLs in $text to encoded URLs
 * in the @@PLUGINFILE@@ form.
 *
 * @category files
 * @global stdClass $CFG
 * @param string $text The content that may contain ULRs in need of rewriting.
 * @param string $file The script that should be used to serve these files. pluginfile.php, draftfile.php, etc.
 * @param int $contextid This parameter and the next two identify the file area to use.
 * @param string $component
 * @param string $filearea helps identify the file area.
 * @param int $itemid helps identify the file area.
 * @param array $options text and file options ('forcehttps'=>false), use reverse = true to reverse the behaviour of the function.
 * @return string the processed text.
 */
function file_rewrite_pluginfile_urls($text, $file, $contextid, $component, $filearea, $itemid, array $options=null) {
    global $CFG;

    $options = (array)$options;
    if (!isset($options['forcehttps'])) {
        $options['forcehttps'] = false;
    }

    if (!$CFG->slasharguments) {
        $file = $file . '?file=';
    }

    $baseurl = "$CFG->wwwroot/$file/$contextid/$component/$filearea/";

    if ($itemid !== null) {
        $baseurl .= "$itemid/";
    }

    if ($options['forcehttps']) {
        $baseurl = str_replace('http://', 'https://', $baseurl);
    }

    if (!empty($options['reverse'])) {
        return str_replace($baseurl, '@@PLUGINFILE@@/', $text);
    } else {
        return str_replace('@@PLUGINFILE@@/', $baseurl, $text);
    }
}

/**
 * Returns information about files in a draft area.
 *
 * @global stdClass $CFG
 * @global stdClass $USER
 * @param int $draftitemid the draft area item id.
 * @param string $filepath path to the directory from which the information have to be retrieved.
 * @return array with the following entries:
 *      'filecount' => number of files in the draft area.
 *      'filesize' => total size of the files in the draft area.
 *      'foldercount' => number of folders in the draft area.
 *      'filesize_without_references' => total size of the area excluding file references.
 * (more information will be added as needed).
 */
function file_get_draft_area_info($draftitemid, $filepath = '/') {
    global $CFG, $USER;

    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();

    $results = array(
        'filecount' => 0,
        'foldercount' => 0,
        'filesize' => 0,
        'filesize_without_references' => 0
    );

    if ($filepath != '/') {
        $draftfiles = $fs->get_directory_files($usercontext->id, 'user', 'draft', $draftitemid, $filepath, true, true);
    } else {
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', true);
    }
    foreach ($draftfiles as $file) {
        if ($file->is_directory()) {
            $results['foldercount'] += 1;
        } else {
            $results['filecount'] += 1;
        }

        $filesize = $file->get_filesize();
        $results['filesize'] += $filesize;
        if (!$file->is_external_file()) {
            $results['filesize_without_references'] += $filesize;
        }
    }

    return $results;
}

/**
 * Returns whether a draft area has exceeded/will exceed its size limit.
 *
 * Please note that the unlimited value for $areamaxbytes is -1 {@link FILE_AREA_MAX_BYTES_UNLIMITED}, not 0.
 *
 * @param int $draftitemid the draft area item id.
 * @param int $areamaxbytes the maximum size allowed in this draft area.
 * @param int $newfilesize the size that would be added to the current area.
 * @param bool $includereferences true to include the size of the references in the area size.
 * @return bool true if the area will/has exceeded its limit.
 * @since Moodle 2.4
 */
function file_is_draft_area_limit_reached($draftitemid, $areamaxbytes, $newfilesize = 0, $includereferences = false) {
    if ($areamaxbytes != FILE_AREA_MAX_BYTES_UNLIMITED) {
        $draftinfo = file_get_draft_area_info($draftitemid);
        $areasize = $draftinfo['filesize_without_references'];
        if ($includereferences) {
            $areasize = $draftinfo['filesize'];
        }
        if ($areasize + $newfilesize > $areamaxbytes) {
            return true;
        }
    }
    return false;
}

/**
 * Get used space of files
 * @global moodle_database $DB
 * @global stdClass $USER
 * @return int total bytes
 */
function file_get_user_used_space() {
    global $DB, $USER;

    $usercontext = context_user::instance($USER->id);
    $sql = "SELECT SUM(files1.filesize) AS totalbytes FROM {files} files1
            JOIN (SELECT contenthash, filename, MAX(id) AS id
            FROM {files}
            WHERE contextid = ? AND component = ? AND filearea != ?
            GROUP BY contenthash, filename) files2 ON files1.id = files2.id";
    $params = array('contextid'=>$usercontext->id, 'component'=>'user', 'filearea'=>'draft');
    $record = $DB->get_record_sql($sql, $params);
    return (int)$record->totalbytes;
}

/**
 * Convert any string to a valid filepath
 * @todo review this function
 * @param string $str
 * @return string path
 */
function file_correct_filepath($str) { //TODO: what is this? (skodak) - No idea (Fred)
    if ($str == '/' or empty($str)) {
        return '/';
    } else {
        return '/'.trim($str, '/').'/';
    }
}

/**
 * Generate a folder tree of draft area of current USER recursively
 *
 * @todo MDL-31073 use normal return value instead, this does not fit the rest of api here (skodak)
 * @param int $draftitemid
 * @param string $filepath
 * @param mixed $data
 */
function file_get_drafarea_folders($draftitemid, $filepath, &$data) {
    global $USER, $OUTPUT, $CFG;
    $data->children = array();
    $context = context_user::instance($USER->id);
    $fs = get_file_storage();
    if ($files = $fs->get_directory_files($context->id, 'user', 'draft', $draftitemid, $filepath, false)) {
        foreach ($files as $file) {
            if ($file->is_directory()) {
                $item = new stdClass();
                $item->sortorder = $file->get_sortorder();
                $item->filepath = $file->get_filepath();

                $foldername = explode('/', trim($item->filepath, '/'));
                $item->fullname = trim(array_pop($foldername), '/');

                $item->id = uniqid();
                file_get_drafarea_folders($draftitemid, $item->filepath, $item);
                $data->children[] = $item;
            } else {
                continue;
            }
        }
    }
}

/**
 * Listing all files (including folders) in current path (draft area)
 * used by file manager
 * @param int $draftitemid
 * @param string $filepath
 * @return stdClass
 */
function file_get_drafarea_files($draftitemid, $filepath = '/') {
    global $USER, $OUTPUT, $CFG;

    $context = context_user::instance($USER->id);
    $fs = get_file_storage();

    $data = new stdClass();
    $data->path = array();
    $data->path[] = array('name'=>get_string('files'), 'path'=>'/');

    // will be used to build breadcrumb
    $trail = '/';
    if ($filepath !== '/') {
        $filepath = file_correct_filepath($filepath);
        $parts = explode('/', $filepath);
        foreach ($parts as $part) {
            if ($part != '' && $part != null) {
                $trail .= ($part.'/');
                $data->path[] = array('name'=>$part, 'path'=>$trail);
            }
        }
    }

    $list = array();
    $maxlength = 12;
    if ($files = $fs->get_directory_files($context->id, 'user', 'draft', $draftitemid, $filepath, false)) {
        foreach ($files as $file) {
            $item = new stdClass();
            $item->filename = $file->get_filename();
            $item->filepath = $file->get_filepath();
            $item->fullname = trim($item->filename, '/');
            $filesize = $file->get_filesize();
            $item->size = $filesize ? $filesize : null;
            $item->filesize = $filesize ? display_size($filesize) : '';

            $item->sortorder = $file->get_sortorder();
            $item->author = $file->get_author();
            $item->license = $file->get_license();
            $item->datemodified = $file->get_timemodified();
            $item->datecreated = $file->get_timecreated();
            $item->isref = $file->is_external_file();
            if ($item->isref && $file->get_status() == 666) {
                $item->originalmissing = true;
            }
            // find the file this draft file was created from and count all references in local
            // system pointing to that file
            $source = @unserialize($file->get_source());
            if (isset($source->original)) {
                $item->refcount = $fs->search_references_count($source->original);
            }

            if ($file->is_directory()) {
                $item->filesize = 0;
                $item->icon = $OUTPUT->pix_url(file_folder_icon(24))->out(false);
                $item->type = 'folder';
                $foldername = explode('/', trim($item->filepath, '/'));
                $item->fullname = trim(array_pop($foldername), '/');
                $item->thumbnail = $OUTPUT->pix_url(file_folder_icon(90))->out(false);
            } else {
                // do NOT use file browser here!
                $item->mimetype = get_mimetype_description($file);
                if (file_extension_in_typegroup($file->get_filename(), 'archive')) {
                    $item->type = 'zip';
                } else {
                    $item->type = 'file';
                }
                $itemurl = moodle_url::make_draftfile_url($draftitemid, $item->filepath, $item->filename);
                $item->url = $itemurl->out();
                $item->icon = $OUTPUT->pix_url(file_file_icon($file, 24))->out(false);
                $item->thumbnail = $OUTPUT->pix_url(file_file_icon($file, 90))->out(false);
                if ($imageinfo = $file->get_imageinfo()) {
                    $item->realthumbnail = $itemurl->out(false, array('preview' => 'thumb', 'oid' => $file->get_timemodified()));
                    $item->realicon = $itemurl->out(false, array('preview' => 'tinyicon', 'oid' => $file->get_timemodified()));
                    $item->image_width = $imageinfo['width'];
                    $item->image_height = $imageinfo['height'];
                }
            }
            $list[] = $item;
        }
    }
    $data->itemid = $draftitemid;
    $data->list = $list;
    return $data;
}

/**
 * Returns draft area itemid for a given element.
 *
 * @category files
 * @param string $elname name of formlib editor element, or a hidden form field that stores the draft area item id, etc.
 * @return int the itemid, or 0 if there is not one yet.
 */
function file_get_submitted_draft_itemid($elname) {
    // this is a nasty hack, ideally all new elements should use arrays here or there should be a new parameter
    if (!isset($_REQUEST[$elname])) {
        return 0;
    }
    if (is_array($_REQUEST[$elname])) {
        $param = optional_param_array($elname, 0, PARAM_INT);
        if (!empty($param['itemid'])) {
            $param = $param['itemid'];
        } else {
            debugging('Missing itemid, maybe caused by unset maxfiles option', DEBUG_DEVELOPER);
            return false;
        }

    } else {
        $param = optional_param($elname, 0, PARAM_INT);
    }

    if ($param) {
        require_sesskey();
    }

    return $param;
}

/**
 * Restore the original source field from draft files
 *
 * Do not use this function because it makes field files.source inconsistent
 * for draft area files. This function will be deprecated in 2.6
 *
 * @param stored_file $storedfile This only works with draft files
 * @return stored_file
 */
function file_restore_source_field_from_draft_file($storedfile) {
    $source = @unserialize($storedfile->get_source());
    if (!empty($source)) {
        if (is_object($source)) {
            $restoredsource = $source->source;
            $storedfile->set_source($restoredsource);
        } else {
            throw new moodle_exception('invalidsourcefield', 'error');
        }
    }
    return $storedfile;
}
/**
 * Saves files from a draft file area to a real one (merging the list of files).
 * Can rewrite URLs in some content at the same time if desired.
 *
 * @category files
 * @global stdClass $USER
 * @param int $draftitemid the id of the draft area to use. Normally obtained
 *      from file_get_submitted_draft_itemid('elementname') or similar.
 * @param int $contextid This parameter and the next two identify the file area to save to.
 * @param string $component
 * @param string $filearea indentifies the file area.
 * @param int $itemid helps identifies the file area.
 * @param array $options area options (subdirs=>false, maxfiles=-1, maxbytes=0)
 * @param string $text some html content that needs to have embedded links rewritten
 *      to the @@PLUGINFILE@@ form for saving in the database.
 * @param bool $forcehttps force https urls.
 * @return string|null if $text was passed in, the rewritten $text is returned. Otherwise NULL.
 */
function file_save_draft_area_files($draftitemid, $contextid, $component, $filearea, $itemid, array $options=null, $text=null, $forcehttps=false) {
    global $USER;

    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();

    $options = (array)$options;
    if (!isset($options['subdirs'])) {
        $options['subdirs'] = false;
    }
    if (!isset($options['maxfiles'])) {
        $options['maxfiles'] = -1; // unlimited
    }
    if (!isset($options['maxbytes']) || $options['maxbytes'] == USER_CAN_IGNORE_FILE_SIZE_LIMITS) {
        $options['maxbytes'] = 0; // unlimited
    }
    if (!isset($options['areamaxbytes'])) {
        $options['areamaxbytes'] = FILE_AREA_MAX_BYTES_UNLIMITED; // Unlimited.
    }
    $allowreferences = true;
    if (isset($options['return_types']) && !($options['return_types'] & FILE_REFERENCE)) {
        // we assume that if $options['return_types'] is NOT specified, we DO allow references.
        // this is not exactly right. BUT there are many places in code where filemanager options
        // are not passed to file_save_draft_area_files()
        $allowreferences = false;
    }

    // Check if the draft area has exceeded the authorised limit. This should never happen as validation
    // should have taken place before, unless the user is doing something nauthly. If so, let's just not save
    // anything at all in the next area.
    if (file_is_draft_area_limit_reached($draftitemid, $options['areamaxbytes'])) {
        return null;
    }

    $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id');
    $oldfiles   = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'id');

    // One file in filearea means it is empty (it has only top-level directory '.').
    if (count($draftfiles) > 1 || count($oldfiles) > 1) {
        // we have to merge old and new files - we want to keep file ids for files that were not changed
        // we change time modified for all new and changed files, we keep time created as is

        $newhashes = array();
        $filecount = 0;
        foreach ($draftfiles as $file) {
            if (!$options['subdirs'] && $file->get_filepath() !== '/') {
                continue;
            }
            if (!$allowreferences && $file->is_external_file()) {
                continue;
            }
            if (!$file->is_directory()) {
                if ($options['maxbytes'] and $options['maxbytes'] < $file->get_filesize()) {
                    // oversized file - should not get here at all
                    continue;
                }
                if ($options['maxfiles'] != -1 and $options['maxfiles'] <= $filecount) {
                    // more files - should not get here at all
                    continue;
                }
                $filecount++;
            }
            $newhash = $fs->get_pathname_hash($contextid, $component, $filearea, $itemid, $file->get_filepath(), $file->get_filename());
            $newhashes[$newhash] = $file;
        }

        // Loop through oldfiles and decide which we need to delete and which to update.
        // After this cycle the array $newhashes will only contain the files that need to be added.
        foreach ($oldfiles as $oldfile) {
            $oldhash = $oldfile->get_pathnamehash();
            if (!isset($newhashes[$oldhash])) {
                // delete files not needed any more - deleted by user
                $oldfile->delete();
                continue;
            }

            $newfile = $newhashes[$oldhash];
            // Now we know that we have $oldfile and $newfile for the same path.
            // Let's check if we can update this file or we need to delete and create.
            if ($newfile->is_directory()) {
                // Directories are always ok to just update.
            } else if (($source = @unserialize($newfile->get_source())) && isset($source->original)) {
                // File has the 'original' - we need to update the file (it may even have not been changed at all).
                $original = file_storage::unpack_reference($source->original);
                if ($original['filename'] !== $oldfile->get_filename() || $original['filepath'] !== $oldfile->get_filepath()) {
                    // Very odd, original points to another file. Delete and create file.
                    $oldfile->delete();
                    continue;
                }
            } else {
                // The same file name but absence of 'original' means that file was deteled and uploaded again.
                // By deleting and creating new file we properly manage all existing references.
                $oldfile->delete();
                continue;
            }

            // status changed, we delete old file, and create a new one
            if ($oldfile->get_status() != $newfile->get_status()) {
                // file was changed, use updated with new timemodified data
                $oldfile->delete();
                // This file will be added later
                continue;
            }

            // Updated author
            if ($oldfile->get_author() != $newfile->get_author()) {
                $oldfile->set_author($newfile->get_author());
            }
            // Updated license
            if ($oldfile->get_license() != $newfile->get_license()) {
                $oldfile->set_license($newfile->get_license());
            }

            // Updated file source
            // Field files.source for draftarea files contains serialised object with source and original information.
            // We only store the source part of it for non-draft file area.
            $newsource = $newfile->get_source();
            if ($source = @unserialize($newfile->get_source())) {
                $newsource = $source->source;
            }
            if ($oldfile->get_source() !== $newsource) {
                $oldfile->set_source($newsource);
            }

            // Updated sort order
            if ($oldfile->get_sortorder() != $newfile->get_sortorder()) {
                $oldfile->set_sortorder($newfile->get_sortorder());
            }

            // Update file timemodified
            if ($oldfile->get_timemodified() != $newfile->get_timemodified()) {
                $oldfile->set_timemodified($newfile->get_timemodified());
            }

            // Replaced file content
            if (!$oldfile->is_directory() &&
                    ($oldfile->get_contenthash() != $newfile->get_contenthash() ||
                    $oldfile->get_filesize() != $newfile->get_filesize() ||
                    $oldfile->get_referencefileid() != $newfile->get_referencefileid() ||
                    $oldfile->get_userid() != $newfile->get_userid())) {
                $oldfile->replace_file_with($newfile);
            }

            // unchanged file or directory - we keep it as is
            unset($newhashes[$oldhash]);
        }

        // Add fresh file or the file which has changed status
        // the size and subdirectory tests are extra safety only, the UI should prevent it
        foreach ($newhashes as $file) {
            $file_record = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'timemodified'=>time());
            if ($source = @unserialize($file->get_source())) {
                // Field files.source for draftarea files contains serialised object with source and original information.
                // We only store the source part of it for non-draft file area.
                $file_record['source'] = $source->source;
            }

            if ($file->is_external_file()) {
                $repoid = $file->get_repository_id();
                if (!empty($repoid)) {
                    $file_record['repositoryid'] = $repoid;
                    $file_record['reference'] = $file->get_reference();
                }
            }

            $fs->create_file_from_storedfile($file_record, $file);
        }
    }

    // note: do not purge the draft area - we clean up areas later in cron,
    //       the reason is that user might press submit twice and they would loose the files,
    //       also sometimes we might want to use hacks that save files into two different areas

    if (is_null($text)) {
        return null;
    } else {
        return file_rewrite_urls_to_pluginfile($text, $draftitemid, $forcehttps);
    }
}

/**
 * Convert the draft file area URLs in some content to @@PLUGINFILE@@ tokens
 * ready to be saved in the database. Normally, this is done automatically by
 * {@link file_save_draft_area_files()}.
 *
 * @category files
 * @param string $text the content to process.
 * @param int $draftitemid the draft file area the content was using.
 * @param bool $forcehttps whether the content contains https URLs. Default false.
 * @return string the processed content.
 */
function file_rewrite_urls_to_pluginfile($text, $draftitemid, $forcehttps = false) {
    global $CFG, $USER;

    $usercontext = context_user::instance($USER->id);

    $wwwroot = $CFG->wwwroot;
    if ($forcehttps) {
        $wwwroot = str_replace('http://', 'https://', $wwwroot);
    }

    // relink embedded files if text submitted - no absolute links allowed in database!
    $text = str_ireplace("$wwwroot/draftfile.php/$usercontext->id/user/draft/$draftitemid/", '@@PLUGINFILE@@/', $text);

    if (strpos($text, 'draftfile.php?file=') !== false) {
        $matches = array();
        preg_match_all("!$wwwroot/draftfile.php\?file=%2F{$usercontext->id}%2Fuser%2Fdraft%2F{$draftitemid}%2F[^'\",&<>|`\s:\\\\]+!iu", $text, $matches);
        if ($matches) {
            foreach ($matches[0] as $match) {
                $replace = str_ireplace('%2F', '/', $match);
                $text = str_replace($match, $replace, $text);
            }
        }
        $text = str_ireplace("$wwwroot/draftfile.php?file=/$usercontext->id/user/draft/$draftitemid/", '@@PLUGINFILE@@/', $text);
    }

    return $text;
}

/**
 * Set file sort order
 *
 * @global moodle_database $DB
 * @param int $contextid the context id
 * @param string $component file component
 * @param string $filearea file area.
 * @param int $itemid itemid.
 * @param string $filepath file path.
 * @param string $filename file name.
 * @param int $sortorder the sort order of file.
 * @return bool
 */
function file_set_sortorder($contextid, $component, $filearea, $itemid, $filepath, $filename, $sortorder) {
    global $DB;
    $conditions = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'filename'=>$filename);
    if ($file_record = $DB->get_record('files', $conditions)) {
        $sortorder = (int)$sortorder;
        $file_record->sortorder = $sortorder;
        $DB->update_record('files', $file_record);
        return true;
    }
    return false;
}

/**
 * reset file sort order number to 0
 * @global moodle_database $DB
 * @param int $contextid the context id
 * @param string $component
 * @param string $filearea file area.
 * @param int|bool $itemid itemid.
 * @return bool
 */
function file_reset_sortorder($contextid, $component, $filearea, $itemid=false) {
    global $DB;

    $conditions = array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea);
    if ($itemid !== false) {
        $conditions['itemid'] = $itemid;
    }

    $file_records = $DB->get_records('files', $conditions);
    foreach ($file_records as $file_record) {
        $file_record->sortorder = 0;
        $DB->update_record('files', $file_record);
    }
    return true;
}

/**
 * Returns description of upload error
 *
 * @param int $errorcode found in $_FILES['filename.ext']['error']
 * @return string error description string, '' if ok
 */
function file_get_upload_error($errorcode) {

    switch ($errorcode) {
    case 0: // UPLOAD_ERR_OK - no error
        $errmessage = '';
        break;

    case 1: // UPLOAD_ERR_INI_SIZE
        $errmessage = get_string('uploadserverlimit');
        break;

    case 2: // UPLOAD_ERR_FORM_SIZE
        $errmessage = get_string('uploadformlimit');
        break;

    case 3: // UPLOAD_ERR_PARTIAL
        $errmessage = get_string('uploadpartialfile');
        break;

    case 4: // UPLOAD_ERR_NO_FILE
        $errmessage = get_string('uploadnofilefound');
        break;

    // Note: there is no error with a value of 5

    case 6: // UPLOAD_ERR_NO_TMP_DIR
        $errmessage = get_string('uploadnotempdir');
        break;

    case 7: // UPLOAD_ERR_CANT_WRITE
        $errmessage = get_string('uploadcantwrite');
        break;

    case 8: // UPLOAD_ERR_EXTENSION
        $errmessage = get_string('uploadextension');
        break;

    default:
        $errmessage = get_string('uploadproblem');
    }

    return $errmessage;
}

/**
 * Recursive function formating an array in POST parameter
 * @param array $arraydata - the array that we are going to format and add into &$data array
 * @param string $currentdata - a row of the final postdata array at instant T
 *                when finish, it's assign to $data under this format: name[keyname][][]...[]='value'
 * @param array $data - the final data array containing all POST parameters : 1 row = 1 parameter
 */
function format_array_postdata_for_curlcall($arraydata, $currentdata, &$data) {
        foreach ($arraydata as $k=>$v) {
            $newcurrentdata = $currentdata;
            if (is_array($v)) { //the value is an array, call the function recursively
                $newcurrentdata = $newcurrentdata.'['.urlencode($k).']';
                format_array_postdata_for_curlcall($v, $newcurrentdata, $data);
            }  else { //add the POST parameter to the $data array
                $data[] = $newcurrentdata.'['.urlencode($k).']='.urlencode($v);
            }
        }
}

/**
 * Transform a PHP array into POST parameter
 * (see the recursive function format_array_postdata_for_curlcall)
 * @param array $postdata
 * @return array containing all POST parameters  (1 row = 1 POST parameter)
 */
function format_postdata_for_curlcall($postdata) {
        $data = array();
        foreach ($postdata as $k=>$v) {
            if (is_array($v)) {
                $currentdata = urlencode($k);
                format_array_postdata_for_curlcall($v, $currentdata, $data);
            }  else {
                $data[] = urlencode($k).'='.urlencode($v);
            }
        }
        $convertedpostdata = implode('&', $data);
        return $convertedpostdata;
}

/**
 * Fetches content of file from Internet (using proxy if defined). Uses cURL extension if present.
 * Due to security concerns only downloads from http(s) sources are supported.
 *
 * @category files
 * @param string $url file url starting with http(s)://
 * @param array $headers http headers, null if none. If set, should be an
 *   associative array of header name => value pairs.
 * @param array $postdata array means use POST request with given parameters
 * @param bool $fullresponse return headers, responses, etc in a similar way snoopy does
 *   (if false, just returns content)
 * @param int $timeout timeout for complete download process including all file transfer
 *   (default 5 minutes)
 * @param int $connecttimeout timeout for connection to server; this is the timeout that
 *   usually happens if the remote server is completely down (default 20 seconds);
 *   may not work when using proxy
 * @param bool $skipcertverify If true, the peer's SSL certificate will not be checked.
 *   Only use this when already in a trusted location.
 * @param string $tofile store the downloaded content to file instead of returning it.
 * @param bool $calctimeout false by default, true enables an extra head request to try and determine
 *   filesize and appropriately larger timeout based on $CFG->curltimeoutkbitrate
 * @return stdClass|string|bool stdClass object if $fullresponse is true, false if request failed, true
 *   if file downloaded into $tofile successfully or the file content as a string.
 */
function download_file_content($url, $headers=null, $postdata=null, $fullresponse=false, $timeout=300, $connecttimeout=20, $skipcertverify=false, $tofile=NULL, $calctimeout=false) {
    global $CFG;

    // Only http and https links supported.
    if (!preg_match('|^https?://|i', $url)) {
        if ($fullresponse) {
            $response = new stdClass();
            $response->status        = 0;
            $response->headers       = array();
            $response->response_code = 'Invalid protocol specified in url';
            $response->results       = '';
            $response->error         = 'Invalid protocol specified in url';
            return $response;
        } else {
            return false;
        }
    }

    $options = array();

    $headers2 = array();
    if (is_array($headers)) {
        foreach ($headers as $key => $value) {
            if (is_numeric($key)) {
                $headers2[] = $value;
            } else {
                $headers2[] = "$key: $value";
            }
        }
    }

    if ($skipcertverify) {
        $options['CURLOPT_SSL_VERIFYPEER'] = false;
    } else {
        $options['CURLOPT_SSL_VERIFYPEER'] = true;
    }

    $options['CURLOPT_CONNECTTIMEOUT'] = $connecttimeout;

    $options['CURLOPT_FOLLOWLOCATION'] = 1;
    $options['CURLOPT_MAXREDIRS'] = 5;

    // Use POST if requested.
    if (is_array($postdata)) {
        $postdata = format_postdata_for_curlcall($postdata);
    } else if (empty($postdata)) {
        $postdata = null;
    }

    // Optionally attempt to get more correct timeout by fetching the file size.
    if (!isset($CFG->curltimeoutkbitrate)) {
        // Use very slow rate of 56kbps as a timeout speed when not set.
        $bitrate = 56;
    } else {
        $bitrate = $CFG->curltimeoutkbitrate;
    }
    if ($calctimeout and !isset($postdata)) {
        $curl = new curl();
        $curl->setHeader($headers2);

        $curl->head($url, $postdata, $options);

        $info = $curl->get_info();
        $error_no = $curl->get_errno();
        if (!$error_no && $info['download_content_length'] > 0) {
            // No curl errors - adjust for large files only - take max timeout.
            $timeout = max($timeout, ceil($info['download_content_length'] * 8 / ($bitrate * 1024)));
        }
    }

    $curl = new curl();
    $curl->setHeader($headers2);

    $options['CURLOPT_RETURNTRANSFER'] = true;
    $options['CURLOPT_NOBODY'] = false;
    $options['CURLOPT_TIMEOUT'] = $timeout;

    if ($tofile) {
        $fh = fopen($tofile, 'w');
        if (!$fh) {
            if ($fullresponse) {
                $response = new stdClass();
                $response->status        = 0;
                $response->headers       = array();
                $response->response_code = 'Can not write to file';
                $response->results       = false;
                $response->error         = 'Can not write to file';
                return $response;
            } else {
                return false;
            }
        }
        $options['CURLOPT_FILE'] = $fh;
    }

    if (isset($postdata)) {
        $content = $curl->post($url, $postdata, $options);
    } else {
        $content = $curl->get($url, null, $options);
    }

    if ($tofile) {
        fclose($fh);
        @chmod($tofile, $CFG->filepermissions);
    }

/*
    // Try to detect encoding problems.
    if ((curl_errno($ch) == 23 or curl_errno($ch) == 61) and defined('CURLOPT_ENCODING')) {
        curl_setopt($ch, CURLOPT_ENCODING, 'none');
        $result = curl_exec($ch);
    }
*/

    $info       = $curl->get_info();
    $error_no   = $curl->get_errno();
    $rawheaders = $curl->get_raw_response();

    if ($error_no) {
        $error = $content;
        if (!$fullresponse) {
            debugging("cURL request for \"$url\" failed with: $error ($error_no)", DEBUG_ALL);
            return false;
        }

        $response = new stdClass();
        if ($error_no == 28) {
            $response->status    = '-100'; // Mimic snoopy.
        } else {
            $response->status    = '0';
        }
        $response->headers       = array();
        $response->response_code = $error;
        $response->results       = false;
        $response->error         = $error;
        return $response;
    }

    if ($tofile) {
        $content = true;
    }

    if (empty($info['http_code'])) {
        // For security reasons we support only true http connections (Location: file:// exploit prevention).
        $response = new stdClass();
        $response->status        = '0';
        $response->headers       = array();
        $response->response_code = 'Unknown cURL error';
        $response->results       = false; // do NOT change this, we really want to ignore the result!
        $response->error         = 'Unknown cURL error';

    } else {
        $response = new stdClass();
        $response->status        = (string)$info['http_code'];
        $response->headers       = $rawheaders;
        $response->results       = $content;
        $response->error         = '';

        // There might be multiple headers on redirect, find the status of the last one.
        $firstline = true;
        foreach ($rawheaders as $line) {
            if ($firstline) {
                $response->response_code = $line;
                $firstline = false;
            }
            if (trim($line, "\r\n") === '') {
                $firstline = true;
            }
        }
    }

    if ($fullresponse) {
        return $response;
    }

    if ($info['http_code'] != 200) {
        debugging("cURL request for \"$url\" failed, HTTP response code: ".$response->response_code, DEBUG_ALL);
        return false;
    }
    return $response->results;
}

/**
 * Returns a list of information about file types based on extensions.
 *
 * The following elements expected in value array for each extension:
 * 'type' - mimetype
 * 'icon' - location of the icon file. If value is FILENAME, then either pix/f/FILENAME.gif
 *     or pix/f/FILENAME.png must be present in moodle and contain 16x16 filetype icon;
 *     also files with bigger sizes under names
 *     FILENAME-24, FILENAME-32, FILENAME-64, FILENAME-128, FILENAME-256 are recommended.
 * 'groups' (optional) - array of filetype groups this filetype extension is part of;
 *     commonly used in moodle the following groups:
 *       - web_image - image that can be included as <img> in HTML
 *       - image - image that we can parse using GD to find it's dimensions, also used for portfolio format
 *       - video - file that can be imported as video in text editor
 *       - audio - file that can be imported as audio in text editor
 *       - archive - we can extract files from this archive
 *       - spreadsheet - used for portfolio format
 *       - document - used for portfolio format
 *       - presentation - used for portfolio format
 * 'string' (optional) - the name of the string from lang/en/mimetypes.php that displays
 *     human-readable description for this filetype;
 *     Function {@link get_mimetype_description()} first looks at the presence of string for
 *     particular mimetype (value of 'type'), if not found looks for string specified in 'string'
 *     attribute, if not found returns the value of 'type';
 * 'defaulticon' (boolean, optional) - used by function {@link file_mimetype_icon()} to find
 *     an icon for mimetype. If an entry with 'defaulticon' is not found for a particular mimetype,
 *     this function will return first found icon; Especially usefull for types such as 'text/plain'
 *
 * @category files
 * @return array List of information about file types based on extensions.
 *   Associative array of extension (lower-case) to associative array
 *   from 'element name' to data. Current element names are 'type' and 'icon'.
 *   Unknown types should use the 'xxx' entry which includes defaults.
 */
function &get_mimetypes_array() {
    // Get types from the core_filetypes function, which includes caching.
    return core_filetypes::get_types();
}

/**
 * Determine a file's MIME type based on the given filename using the function mimeinfo.
 *
 * This function retrieves a file's MIME type for a file that will be sent to the user.
 * This should only be used for file-sending purposes just like in send_stored_file, send_file, and send_temp_file.
 * Should the file's MIME type cannot be determined by mimeinfo, it will return 'application/octet-stream' as a default
 * MIME type which should tell the browser "I don't know what type of file this is, so just download it.".
 *
 * @param string $filename The file's filename.
 * @return string The file's MIME type or 'application/octet-stream' if it cannot be determined.
 */
function get_mimetype_for_sending($filename = '') {
    // Guess the file's MIME type using mimeinfo.
    $mimetype = mimeinfo('type', $filename);

    // Use octet-stream as fallback if MIME type cannot be determined by mimeinfo.
    if (!$mimetype || $mimetype === 'document/unknown') {
        $mimetype = 'application/octet-stream';
    }

    return $mimetype;
}

/**
 * Obtains information about a filetype based on its extension. Will
 * use a default if no information is present about that particular
 * extension.
 *
 * @category files
 * @param string $element Desired information (usually 'icon'
 *   for icon filename or 'type' for MIME type. Can also be
 *   'icon24', ...32, 48, 64, 72, 80, 96, 128, 256)
 * @param string $filename Filename we're looking up
 * @return string Requested piece of information from array
 */
function mimeinfo($element, $filename) {
    global $CFG;
    $mimeinfo = & get_mimetypes_array();
    static $iconpostfixes = array(256=>'-256', 128=>'-128', 96=>'-96', 80=>'-80', 72=>'-72', 64=>'-64', 48=>'-48', 32=>'-32', 24=>'-24', 16=>'');

    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (empty($filetype)) {
        $filetype = 'xxx'; // file without extension
    }
    if (preg_match('/^icon(\d*)$/', $element, $iconsizematch)) {
        $iconsize = max(array(16, (int)$iconsizematch[1]));
        $filenames = array($mimeinfo['xxx']['icon']);
        if ($filetype != 'xxx' && isset($mimeinfo[$filetype]['icon'])) {
            array_unshift($filenames, $mimeinfo[$filetype]['icon']);
        }
        // find the file with the closest size, first search for specific icon then for default icon
        foreach ($filenames as $filename) {
            foreach ($iconpostfixes as $size => $postfix) {
                $fullname = $CFG->dirroot.'/pix/f/'.$filename.$postfix;
                if ($iconsize >= $size && (file_exists($fullname.'.png') || file_exists($fullname.'.gif'))) {
                    return $filename.$postfix;
                }
            }
        }
    } else if (isset($mimeinfo[$filetype][$element])) {
        return $mimeinfo[$filetype][$element];
    } else if (isset($mimeinfo['xxx'][$element])) {
        return $mimeinfo['xxx'][$element];   // By default
    } else {
        return null;
    }
}

/**
 * Obtains information about a filetype based on the MIME type rather than
 * the other way around.
 *
 * @category files
 * @param string $element Desired information ('extension', 'icon', 'icon-24', etc.)
 * @param string $mimetype MIME type we're looking up
 * @return string Requested piece of information from array
 */
function mimeinfo_from_type($element, $mimetype) {
    /* array of cached mimetype->extension associations */
    static $cached = array();
    $mimeinfo = & get_mimetypes_array();

    if (!array_key_exists($mimetype, $cached)) {
        $cached[$mimetype] = null;
        foreach($mimeinfo as $filetype => $values) {
            if ($values['type'] == $mimetype) {
                if ($cached[$mimetype] === null) {
                    $cached[$mimetype] = '.'.$filetype;
                }
                if (!empty($values['defaulticon'])) {
                    $cached[$mimetype] = '.'.$filetype;
                    break;
                }
            }
        }
        if (empty($cached[$mimetype])) {
            $cached[$mimetype] = '.xxx';
        }
    }
    if ($element === 'extension') {
        return $cached[$mimetype];
    } else {
        return mimeinfo($element, $cached[$mimetype]);
    }
}

/**
 * Return the relative icon path for a given file
 *
 * Usage:
 * <code>
 * // $file - instance of stored_file or file_info
 * $icon = $OUTPUT->pix_url(file_file_icon($file))->out();
 * echo html_writer::empty_tag('img', array('src' => $icon, 'alt' => get_mimetype_description($file)));
 * </code>
 * or
 * <code>
 * echo $OUTPUT->pix_icon(file_file_icon($file), get_mimetype_description($file));
 * </code>
 *
 * @param stored_file|file_info|stdClass|array $file (in case of object attributes $file->filename
 *     and $file->mimetype are expected)
 * @param int $size The size of the icon. Defaults to 16 can also be 24, 32, 64, 128, 256
 * @return string
 */
function file_file_icon($file, $size = null) {
    if (!is_object($file)) {
        $file = (object)$file;
    }
    if (isset($file->filename)) {
        $filename = $file->filename;
    } else if (method_exists($file, 'get_filename')) {
        $filename = $file->get_filename();
    } else if (method_exists($file, 'get_visible_name')) {
        $filename = $file->get_visible_name();
    } else {
        $filename = '';
    }
    if (isset($file->mimetype)) {
        $mimetype = $file->mimetype;
    } else if (method_exists($file, 'get_mimetype')) {
        $mimetype = $file->get_mimetype();
    } else {
        $mimetype = '';
    }
    $mimetypes = &get_mimetypes_array();
    if ($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($extension && !empty($mimetypes[$extension])) {
            // if file name has known extension, return icon for this extension
            return file_extension_icon($filename, $size);
        }
    }
    return file_mimetype_icon($mimetype, $size);
}

/**
 * Return the relative icon path for a folder image
 *
 * Usage:
 * <code>
 * $icon = $OUTPUT->pix_url(file_folder_icon())->out();
 * echo html_writer::empty_tag('img', array('src' => $icon));
 * </code>
 * or
 * <code>
 * echo $OUTPUT->pix_icon(file_folder_icon(32));
 * </code>
 *
 * @param int $iconsize The size of the icon. Defaults to 16 can also be 24, 32, 48, 64, 72, 80, 96, 128, 256
 * @return string
 */
function file_folder_icon($iconsize = null) {
    global $CFG;
    static $iconpostfixes = array(256=>'-256', 128=>'-128', 96=>'-96', 80=>'-80', 72=>'-72', 64=>'-64', 48=>'-48', 32=>'-32', 24=>'-24', 16=>'');
    static $cached = array();
    $iconsize = max(array(16, (int)$iconsize));
    if (!array_key_exists($iconsize, $cached)) {
        foreach ($iconpostfixes as $size => $postfix) {
            $fullname = $CFG->dirroot.'/pix/f/folder'.$postfix;
            if ($iconsize >= $size && (file_exists($fullname.'.png') || file_exists($fullname.'.gif'))) {
                $cached[$iconsize] = 'f/folder'.$postfix;
                break;
            }
        }
    }
    return $cached[$iconsize];
}

/**
 * Returns the relative icon path for a given mime type
 *
 * This function should be used in conjunction with $OUTPUT->pix_url to produce
 * a return the full path to an icon.
 *
 * <code>
 * $mimetype = 'image/jpg';
 * $icon = $OUTPUT->pix_url(file_mimetype_icon($mimetype))->out();
 * echo html_writer::empty_tag('img', array('src' => $icon, 'alt' => get_mimetype_description($mimetype)));
 * </code>
 *
 * @category files
 * @todo MDL-31074 When an $OUTPUT->icon method is available this function should be altered
 * to conform with that.
 * @param string $mimetype The mimetype to fetch an icon for
 * @param int $size The size of the icon. Defaults to 16 can also be 24, 32, 64, 128, 256
 * @return string The relative path to the icon
 */
function file_mimetype_icon($mimetype, $size = NULL) {
    return 'f/'.mimeinfo_from_type('icon'.$size, $mimetype);
}

/**
 * Returns the relative icon path for a given file name
 *
 * This function should be used in conjunction with $OUTPUT->pix_url to produce
 * a return the full path to an icon.
 *
 * <code>
 * $filename = '.jpg';
 * $icon = $OUTPUT->pix_url(file_extension_icon($filename))->out();
 * echo html_writer::empty_tag('img', array('src' => $icon, 'alt' => '...'));
 * </code>
 *
 * @todo MDL-31074 When an $OUTPUT->icon method is available this function should be altered
 * to conform with that.
 * @todo MDL-31074 Implement $size
 * @category files
 * @param string $filename The filename to get the icon for
 * @param int $size The size of the icon. Defaults to 16 can also be 24, 32, 64, 128, 256
 * @return string
 */
function file_extension_icon($filename, $size = NULL) {
    return 'f/'.mimeinfo('icon'.$size, $filename);
}

/**
 * Obtains descriptions for file types (e.g. 'Microsoft Word document') from the
 * mimetypes.php language file.
 *
 * @param mixed $obj - instance of stored_file or file_info or array/stdClass with field
 *   'filename' and 'mimetype', or just a string with mimetype (though it is recommended to
 *   have filename); In case of array/stdClass the field 'mimetype' is optional.
 * @param bool $capitalise If true, capitalises first character of result
 * @return string Text description
 */
function get_mimetype_description($obj, $capitalise=false) {
    $filename = $mimetype = '';
    if (is_object($obj) && method_exists($obj, 'get_filename') && method_exists($obj, 'get_mimetype')) {
        // this is an instance of stored_file
        $mimetype = $obj->get_mimetype();
        $filename = $obj->get_filename();
    } else if (is_object($obj) && method_exists($obj, 'get_visible_name') && method_exists($obj, 'get_mimetype')) {
        // this is an instance of file_info
        $mimetype = $obj->get_mimetype();
        $filename = $obj->get_visible_name();
    } else if (is_array($obj) || is_object ($obj)) {
        $obj = (array)$obj;
        if (!empty($obj['filename'])) {
            $filename = $obj['filename'];
        }
        if (!empty($obj['mimetype'])) {
            $mimetype = $obj['mimetype'];
        }
    } else {
        $mimetype = $obj;
    }
    $mimetypefromext = mimeinfo('type', $filename);
    if (empty($mimetype) || $mimetypefromext !== 'document/unknown') {
        // if file has a known extension, overwrite the specified mimetype
        $mimetype = $mimetypefromext;
    }
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (empty($extension)) {
        $mimetypestr = mimeinfo_from_type('string', $mimetype);
        $extension = str_replace('.', '', mimeinfo_from_type('extension', $mimetype));
    } else {
        $mimetypestr = mimeinfo('string', $filename);
    }
    $chunks = explode('/', $mimetype, 2);
    $chunks[] = '';
    $attr = array(
        'mimetype' => $mimetype,
        'ext' => $extension,
        'mimetype1' => $chunks[0],
        'mimetype2' => $chunks[1],
    );
    $a = array();
    foreach ($attr as $key => $value) {
        $a[$key] = $value;
        $a[strtoupper($key)] = strtoupper($value);
        $a[ucfirst($key)] = ucfirst($value);
    }

    // MIME types may include + symbol but this is not permitted in string ids.
    $safemimetype = str_replace('+', '_', $mimetype);
    $safemimetypestr = str_replace('+', '_', $mimetypestr);
    $customdescription = mimeinfo('customdescription', $filename);
    if ($customdescription) {
        // Call format_string on the custom description so that multilang
        // filter can be used (if enabled on system context). We use system
        // context because it is possible that the page context might not have
        // been defined yet.
        $result = format_string($customdescription, true,
                array('context' => context_system::instance()));
    } else if (get_string_manager()->string_exists($safemimetype, 'mimetypes')) {
        $result = get_string($safemimetype, 'mimetypes', (object)$a);
    } else if (get_string_manager()->string_exists($safemimetypestr, 'mimetypes')) {
        $result = get_string($safemimetypestr, 'mimetypes', (object)$a);
    } else if (get_string_manager()->string_exists('default', 'mimetypes')) {
        $result = get_string('default', 'mimetypes', (object)$a);
    } else {
        $result = $mimetype;
    }
    if ($capitalise) {
        $result=ucfirst($result);
    }
    return $result;
}

/**
 * Returns array of elements of type $element in type group(s)
 *
 * @param string $element name of the element we are interested in, usually 'type' or 'extension'
 * @param string|array $groups one group or array of groups/extensions/mimetypes
 * @return array
 */
function file_get_typegroup($element, $groups) {
    static $cached = array();
    if (!is_array($groups)) {
        $groups = array($groups);
    }
    if (!array_key_exists($element, $cached)) {
        $cached[$element] = array();
    }
    $result = array();
    foreach ($groups as $group) {
        if (!array_key_exists($group, $cached[$element])) {
            // retrieive and cache all elements of type $element for group $group
            $mimeinfo = & get_mimetypes_array();
            $cached[$element][$group] = array();
            foreach ($mimeinfo as $extension => $value) {
                $value['extension'] = '.'.$extension;
                if (empty($value[$element])) {
                    continue;
                }
                if (($group === '.'.$extension || $group === $value['type'] ||
                        (!empty($value['groups']) && in_array($group, $value['groups']))) &&
                        !in_array($value[$element], $cached[$element][$group])) {
                    $cached[$element][$group][] = $value[$element];
                }
            }
        }
        $result = array_merge($result, $cached[$element][$group]);
    }
    return array_values(array_unique($result));
}

/**
 * Checks if file with name $filename has one of the extensions in groups $groups
 *
 * @see get_mimetypes_array()
 * @param string $filename name of the file to check
 * @param string|array $groups one group or array of groups to check
 * @param bool $checktype if true and extension check fails, find the mimetype and check if
 * file mimetype is in mimetypes in groups $groups
 * @return bool
 */
function file_extension_in_typegroup($filename, $groups, $checktype = false) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    if (!empty($extension) && in_array('.'.strtolower($extension), file_get_typegroup('extension', $groups))) {
        return true;
    }
    return $checktype && file_mimetype_in_typegroup(mimeinfo('type', $filename), $groups);
}

/**
 * Checks if mimetype $mimetype belongs to one of the groups $groups
 *
 * @see get_mimetypes_array()
 * @param string $mimetype
 * @param string|array $groups one group or array of groups to check
 * @return bool
 */
function file_mimetype_in_typegroup($mimetype, $groups) {
    return !empty($mimetype) && in_array($mimetype, file_get_typegroup('type', $groups));
}

/**
 * Requested file is not found or not accessible, does not return, terminates script
 *
 * @global stdClass $CFG
 * @global stdClass $COURSE
 */
function send_file_not_found() {
    global $CFG, $COURSE;

    // Allow cross-origin requests only for Web Services.
    // This allow to receive requests done by Web Workers or webapps in different domains.
    if (WS_SERVER) {
        header('Access-Control-Allow-Origin: *');
    }

    send_header_404();
    print_error('filenotfound', 'error', $CFG->wwwroot.'/course/view.php?id='.$COURSE->id); //this is not displayed on IIS??
}
/**
 * Helper function to send correct 404 for server.
 */
function send_header_404() {
    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        header("Status: 404 Not Found");
    } else {
        header('HTTP/1.0 404 not found');
    }
}

/**
 * The readfile function can fail when files are larger than 2GB (even on 64-bit
 * platforms). This wrapper uses readfile for small files and custom code for
 * large ones.
 *
 * @param string $path Path to file
 * @param int $filesize Size of file (if left out, will get it automatically)
 * @return int|bool Size read (will always be $filesize) or false if failed
 */
function readfile_allow_large($path, $filesize = -1) {
    // Automatically get size if not specified.
    if ($filesize === -1) {
        $filesize = filesize($path);
    }
    if ($filesize <= 2147483647) {
        // If the file is up to 2^31 - 1, send it normally using readfile.
        return readfile($path);
    } else {
        // For large files, read and output in 64KB chunks.
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return false;
        }
        $left = $filesize;
        while ($left > 0) {
            $size = min($left, 65536);
            $buffer = fread($handle, $size);
            if ($buffer === false) {
                return false;
            }
            echo $buffer;
            $left -= $size;
        }
        return $filesize;
    }
}

/**
 * Enhanced readfile() with optional acceleration.
 * @param string|stored_file $file
 * @param string $mimetype
 * @param bool $accelerate
 * @return void
 */
function readfile_accel($file, $mimetype, $accelerate) {
    global $CFG;

    if ($mimetype === 'text/plain') {
        // there is no encoding specified in text files, we need something consistent
        header('Content-Type: text/plain; charset=utf-8');
    } else {
        header('Content-Type: '.$mimetype);
    }

    $lastmodified = is_object($file) ? $file->get_timemodified() : filemtime($file);
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');

    if (is_object($file)) {
        header('Etag: "' . $file->get_contenthash() . '"');
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) and trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $file->get_contenthash()) {
            header('HTTP/1.1 304 Not Modified');
            return;
        }
    }

    // if etag present for stored file rely on it exclusively
    if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) and (empty($_SERVER['HTTP_IF_NONE_MATCH']) or !is_object($file))) {
        // get unixtime of request header; clip extra junk off first
        $since = strtotime(preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]));
        if ($since && $since >= $lastmodified) {
            header('HTTP/1.1 304 Not Modified');
            return;
        }
    }

    if ($accelerate and !empty($CFG->xsendfile)) {
        if (empty($CFG->disablebyteserving) and $mimetype !== 'text/plain') {
            header('Accept-Ranges: bytes');
        } else {
            header('Accept-Ranges: none');
        }

        if (is_object($file)) {
            $fs = get_file_storage();
            if ($fs->xsendfile($file->get_contenthash())) {
                return;
            }

        } else {
            require_once("$CFG->libdir/xsendfilelib.php");
            if (xsendfile($file)) {
                return;
            }
        }
    }

    $filesize = is_object($file) ? $file->get_filesize() : filesize($file);

    header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');

    if ($accelerate and empty($CFG->disablebyteserving) and $mimetype !== 'text/plain') {
        header('Accept-Ranges: bytes');

        if (!empty($_SERVER['HTTP_RANGE']) and strpos($_SERVER['HTTP_RANGE'],'bytes=') !== FALSE) {
            // byteserving stuff - for acrobat reader and download accelerators
            // see: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.35
            // inspired by: http://www.coneural.org/florian/papers/04_byteserving.php
            $ranges = false;
            if (preg_match_all('/(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $ranges, PREG_SET_ORDER)) {
                foreach ($ranges as $key=>$value) {
                    if ($ranges[$key][1] == '') {
                        //suffix case
                        $ranges[$key][1] = $filesize - $ranges[$key][2];
                        $ranges[$key][2] = $filesize - 1;
                    } else if ($ranges[$key][2] == '' || $ranges[$key][2] > $filesize - 1) {
                        //fix range length
                        $ranges[$key][2] = $filesize - 1;
                    }
                    if ($ranges[$key][2] != '' && $ranges[$key][2] < $ranges[$key][1]) {
                        //invalid byte-range ==> ignore header
                        $ranges = false;
                        break;
                    }
                    //prepare multipart header
                    $ranges[$key][0] =  "\r\n--".BYTESERVING_BOUNDARY."\r\nContent-Type: $mimetype\r\n";
                    $ranges[$key][0] .= "Content-Range: bytes {$ranges[$key][1]}-{$ranges[$key][2]}/$filesize\r\n\r\n";
                }
            } else {
                $ranges = false;
            }
            if ($ranges) {
                if (is_object($file)) {
                    $handle = $file->get_content_file_handle();
                } else {
                    $handle = fopen($file, 'rb');
                }
                byteserving_send_file($handle, $mimetype, $ranges, $filesize);
            }
        }
    } else {
        // Do not byteserve
        header('Accept-Ranges: none');
    }

    header('Content-Length: '.$filesize);

    if ($filesize > 10000000) {
        // for large files try to flush and close all buffers to conserve memory
        while(@ob_get_level()) {
            if (!@ob_end_flush()) {
                break;
            }
        }
    }

    // send the whole file content
    if (is_object($file)) {
        $file->readfile();
    } else {
        readfile_allow_large($file, $filesize);
    }
}

/**
 * Similar to readfile_accel() but designed for strings.
 * @param string $string
 * @param string $mimetype
 * @param bool $accelerate
 * @return void
 */
function readstring_accel($string, $mimetype, $accelerate) {
    global $CFG;

    if ($mimetype === 'text/plain') {
        // there is no encoding specified in text files, we need something consistent
        header('Content-Type: text/plain; charset=utf-8');
    } else {
        header('Content-Type: '.$mimetype);
    }
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Accept-Ranges: none');

    if ($accelerate and !empty($CFG->xsendfile)) {
        $fs = get_file_storage();
        if ($fs->xsendfile(sha1($string))) {
            return;
        }
    }

    header('Content-Length: '.strlen($string));
    echo $string;
}

/**
 * Handles the sending of temporary file to user, download is forced.
 * File is deleted after abort or successful sending, does not return, script terminated
 *
 * @param string $path path to file, preferably from moodledata/temp/something; or content of file itself
 * @param string $filename proposed file name when saving file
 * @param bool $pathisstring If the path is string
 */
function send_temp_file($path, $filename, $pathisstring=false) {
    global $CFG;

    // Guess the file's MIME type.
    $mimetype = get_mimetype_for_sending($filename);

    // close session - not needed anymore
    \core\session\manager::write_close();

    if (!$pathisstring) {
        if (!file_exists($path)) {
            send_header_404();
            print_error('filenotfound', 'error', $CFG->wwwroot.'/');
        }
        // executed after normal finish or abort
        core_shutdown_manager::register_function('send_temp_file_finished', array($path));
    }

    // if user is using IE, urlencode the filename so that multibyte file name will show up correctly on popup
    if (core_useragent::is_ie()) {
        $filename = urlencode($filename);
    }

    header('Content-Disposition: attachment; filename="'.$filename.'"');
    if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
        header('Cache-Control: private, max-age=10, no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: ');
    } else { //normal http - prevent caching at all cost
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0, no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
    }

    // send the contents - we can not accelerate this because the file will be deleted asap
    if ($pathisstring) {
        readstring_accel($path, $mimetype, false);
    } else {
        readfile_accel($path, $mimetype, false);
        @unlink($path);
    }

    die; //no more chars to output
}

/**
 * Internal callback function used by send_temp_file()
 *
 * @param string $path
 */
function send_temp_file_finished($path) {
    if (file_exists($path)) {
        @unlink($path);
    }
}

/**
 * Handles the sending of file data to the user's browser, including support for
 * byteranges etc.
 *
 * @category files
 * @param string $path Path of file on disk (including real filename), or actual content of file as string
 * @param string $filename Filename to send
 * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
 * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
 * @param bool $pathisstring If true (default false), $path is the content to send and not the pathname
 * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
 * @param string $mimetype Include to specify the MIME type; leave blank to have it guess the type from $filename
 * @param bool $dontdie - return control to caller afterwards. this is not recommended and only used for cleanup tasks.
 *                        if this is passed as true, ignore_user_abort is called.  if you don't want your processing to continue on cancel,
 *                        you must detect this case when control is returned using connection_aborted. Please not that session is closed
 *                        and should not be reopened.
 * @return null script execution stopped unless $dontdie is true
 */
function send_file($path, $filename, $lifetime = null , $filter=0, $pathisstring=false, $forcedownload=false, $mimetype='', $dontdie=false) {
    global $CFG, $COURSE;

    if ($dontdie) {
        ignore_user_abort(true);
    }

    if ($lifetime === 'default' or is_null($lifetime)) {
        $lifetime = $CFG->filelifetime;
    }

    \core\session\manager::write_close(); // Unlock session during file serving.

    // Use given MIME type if specified, otherwise guess it.
    if (!$mimetype || $mimetype === 'document/unknown') {
        $mimetype = get_mimetype_for_sending($filename);
    }

    // if user is using IE, urlencode the filename so that multibyte file name will show up correctly on popup
    if (core_useragent::is_ie()) {
        $filename = rawurlencode($filename);
    }

    if ($forcedownload) {
        header('Content-Disposition: attachment; filename="'.$filename.'"');
    } else if ($mimetype !== 'application/x-shockwave-flash') {
        // If this is an swf don't pass content-disposition with filename as this makes the flash player treat the file
        // as an upload and enforces security that may prevent the file from being loaded.

        header('Content-Disposition: inline; filename="'.$filename.'"');
    }

    if ($lifetime > 0) {
        $cacheability = ' public,';
        if (isloggedin() and !isguestuser()) {
            // By default, under the conditions above, this file must be cache-able only by browsers.
            $cacheability = ' private,';
        }
        $nobyteserving = false;
        header('Cache-Control:'.$cacheability.' max-age='.$lifetime.', no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
        header('Pragma: ');

    } else { // Do not cache files in proxies and browsers
        $nobyteserving = true;
        if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: private, max-age=10, no-transform');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0, no-transform');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: no-cache');
        }
    }

    if (empty($filter)) {
        // send the contents
        if ($pathisstring) {
            readstring_accel($path, $mimetype, !$dontdie);
        } else {
            readfile_accel($path, $mimetype, !$dontdie);
        }

    } else {
        // Try to put the file through filters
        if ($mimetype == 'text/html' || $mimetype == 'application/xhtml+xml') {
            $options = new stdClass();
            $options->noclean = true;
            $options->nocache = true; // temporary workaround for MDL-5136
            $text = $pathisstring ? $path : implode('', file($path));

            $output = format_text($text, FORMAT_HTML, $options, $COURSE->id);

            readstring_accel($output, $mimetype, false);

        } else if (($mimetype == 'text/plain') and ($filter == 1)) {
            // only filter text if filter all files is selected
            $options = new stdClass();
            $options->newlines = false;
            $options->noclean = true;
            $text = htmlentities($pathisstring ? $path : implode('', file($path)), ENT_QUOTES, 'UTF-8');
            $output = '<pre>'. format_text($text, FORMAT_MOODLE, $options, $COURSE->id) .'</pre>';

            readstring_accel($output, $mimetype, false);

        } else {
            // send the contents
            if ($pathisstring) {
                readstring_accel($path, $mimetype, !$dontdie);
            } else {
                readfile_accel($path, $mimetype, !$dontdie);
            }
        }
    }
    if ($dontdie) {
        return;
    }
    die; //no more chars to output!!!
}

/**
 * Handles the sending of file data to the user's browser, including support for
 * byteranges etc.
 *
 * The $options parameter supports the following keys:
 *  (string|null) preview - send the preview of the file (e.g. "thumb" for a thumbnail)
 *  (string|null) filename - overrides the implicit filename
 *  (bool) dontdie - return control to caller afterwards. this is not recommended and only used for cleanup tasks.
 *      if this is passed as true, ignore_user_abort is called.  if you don't want your processing to continue on cancel,
 *      you must detect this case when control is returned using connection_aborted. Please not that session is closed
 *      and should not be reopened
 *  (string|null) cacheability - force the cacheability setting of the HTTP response, "private" or "public",
 *      when $lifetime is greater than 0. Cacheability defaults to "private" when logged in as other than guest; otherwise,
 *      defaults to "public".
 *
 * @category files
 * @param stored_file $stored_file local file object
 * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
 * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
 * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
 * @param array $options additional options affecting the file serving
 * @return null script execution stopped unless $options['dontdie'] is true
 */
function send_stored_file($stored_file, $lifetime=null, $filter=0, $forcedownload=false, array $options=array()) {
    global $CFG, $COURSE;

    if (empty($options['filename'])) {
        $filename = null;
    } else {
        $filename = $options['filename'];
    }

    if (empty($options['dontdie'])) {
        $dontdie = false;
    } else {
        $dontdie = true;
    }

    if ($lifetime === 'default' or is_null($lifetime)) {
        $lifetime = $CFG->filelifetime;
    }

    if (!empty($options['preview'])) {
        // replace the file with its preview
        $fs = get_file_storage();
        $preview_file = $fs->get_file_preview($stored_file, $options['preview']);
        if (!$preview_file) {
            // unable to create a preview of the file, send its default mime icon instead
            if ($options['preview'] === 'tinyicon') {
                $size = 24;
            } else if ($options['preview'] === 'thumb') {
                $size = 90;
            } else {
                $size = 256;
            }
            $fileicon = file_file_icon($stored_file, $size);
            send_file($CFG->dirroot.'/pix/'.$fileicon.'.png', basename($fileicon).'.png');
        } else {
            // preview images have fixed cache lifetime and they ignore forced download
            // (they are generated by GD and therefore they are considered reasonably safe).
            $stored_file = $preview_file;
            $lifetime = DAYSECS;
            $filter = 0;
            $forcedownload = false;
        }
    }

    // handle external resource
    if ($stored_file && $stored_file->is_external_file() && !isset($options['sendcachedexternalfile'])) {
        $stored_file->send_file($lifetime, $filter, $forcedownload, $options);
        die;
    }

    if (!$stored_file or $stored_file->is_directory()) {
        // nothing to serve
        if ($dontdie) {
            return;
        }
        die;
    }

    if ($dontdie) {
        ignore_user_abort(true);
    }

    \core\session\manager::write_close(); // Unlock session during file serving.

    $filename     = is_null($filename) ? $stored_file->get_filename() : $filename;

    // Use given MIME type if specified.
    $mimetype = $stored_file->get_mimetype();

    // Otherwise guess it.
    if (!$mimetype || $mimetype === 'document/unknown') {
        $mimetype = get_mimetype_for_sending($filename);
    }

    // if user is using IE, urlencode the filename so that multibyte file name will show up correctly on popup
    if (core_useragent::is_ie()) {
        $filename = rawurlencode($filename);
    }

    if ($forcedownload) {
        header('Content-Disposition: attachment; filename="'.$filename.'"');
    } else if ($mimetype !== 'application/x-shockwave-flash') {
        // If this is an swf don't pass content-disposition with filename as this makes the flash player treat the file
        // as an upload and enforces security that may prevent the file from being loaded.

        header('Content-Disposition: inline; filename="'.$filename.'"');
    }

    if ($lifetime > 0) {
        $cacheability = ' public,';
        if (!empty($options['cacheability']) && ($options['cacheability'] === 'public')) {
            // This file must be cache-able by both browsers and proxies.
            $cacheability = ' public,';
        } else if (!empty($options['cacheability']) && ($options['cacheability'] === 'private')) {
            // This file must be cache-able only by browsers.
            $cacheability = ' private,';
        } else if (isloggedin() and !isguestuser()) {
            $cacheability = ' private,';
        }
        header('Cache-Control:'.$cacheability.' max-age='.$lifetime.', no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
        header('Pragma: ');

    } else { // Do not cache files in proxies and browsers
        if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: private, max-age=10, no-transform');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0, no-transform');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: no-cache');
        }
    }

    // Allow cross-origin requests only for Web Services.
    // This allow to receive requests done by Web Workers or webapps in different domains.
    if (WS_SERVER) {
        header('Access-Control-Allow-Origin: *');
    }

    if (empty($filter)) {
        // send the contents
        readfile_accel($stored_file, $mimetype, !$dontdie);

    } else {     // Try to put the file through filters
        if ($mimetype == 'text/html' || $mimetype == 'application/xhtml+xml') {
            $options = new stdClass();
            $options->noclean = true;
            $options->nocache = true; // temporary workaround for MDL-5136
            $text = $stored_file->get_content();
            $output = format_text($text, FORMAT_HTML, $options, $COURSE->id);

            readstring_accel($output, $mimetype, false);

        } else if (($mimetype == 'text/plain') and ($filter == 1)) {
            // only filter text if filter all files is selected
            $options = new stdClass();
            $options->newlines = false;
            $options->noclean = true;
            $text = $stored_file->get_content();
            $output = '<pre>'. format_text($text, FORMAT_MOODLE, $options, $COURSE->id) .'</pre>';

            readstring_accel($output, $mimetype, false);

        } else {    // Just send it out raw
            readfile_accel($stored_file, $mimetype, !$dontdie);
        }
    }
    if ($dontdie) {
        return;
    }
    die; //no more chars to output!!!
}

/**
 * Retrieves an array of records from a CSV file and places
 * them into a given table structure
 *
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @param string $file The path to a CSV file
 * @param string $table The table to retrieve columns from
 * @return bool|array Returns an array of CSV records or false
 */
function get_records_csv($file, $table) {
    global $CFG, $DB;

    if (!$metacolumns = $DB->get_columns($table)) {
        return false;
    }

    if(!($handle = @fopen($file, 'r'))) {
        print_error('get_records_csv failed to open '.$file);
    }

    $fieldnames = fgetcsv($handle, 4096);
    if(empty($fieldnames)) {
        fclose($handle);
        return false;
    }

    $columns = array();

    foreach($metacolumns as $metacolumn) {
        $ord = array_search($metacolumn->name, $fieldnames);
        if(is_int($ord)) {
            $columns[$metacolumn->name] = $ord;
        }
    }

    $rows = array();

    while (($data = fgetcsv($handle, 4096)) !== false) {
        $item = new stdClass;
        foreach($columns as $name => $ord) {
            $item->$name = $data[$ord];
        }
        $rows[] = $item;
    }

    fclose($handle);
    return $rows;
}

/**
 * Create a file with CSV contents
 *
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @param string $file The file to put the CSV content into
 * @param array $records An array of records to write to a CSV file
 * @param string $table The table to get columns from
 * @return bool success
 */
function put_records_csv($file, $records, $table = NULL) {
    global $CFG, $DB;

    if (empty($records)) {
        return true;
    }

    $metacolumns = NULL;
    if ($table !== NULL && !$metacolumns = $DB->get_columns($table)) {
        return false;
    }

    echo "x";

    if(!($fp = @fopen($CFG->tempdir.'/'.$file, 'w'))) {
        print_error('put_records_csv failed to open '.$file);
    }

    $proto = reset($records);
    if(is_object($proto)) {
        $fields_records = array_keys(get_object_vars($proto));
    }
    else if(is_array($proto)) {
        $fields_records = array_keys($proto);
    }
    else {
        return false;
    }
    echo "x";

    if(!empty($metacolumns)) {
        $fields_table = array_map(create_function('$a', 'return $a->name;'), $metacolumns);
        $fields = array_intersect($fields_records, $fields_table);
    }
    else {
        $fields = $fields_records;
    }

    fwrite($fp, implode(',', $fields));
    fwrite($fp, "\r\n");

    foreach($records as $record) {
        $array  = (array)$record;
        $values = array();
        foreach($fields as $field) {
            if(strpos($array[$field], ',')) {
                $values[] = '"'.str_replace('"', '\"', $array[$field]).'"';
            }
            else {
                $values[] = $array[$field];
            }
        }
        fwrite($fp, implode(',', $values)."\r\n");
    }

    fclose($fp);
    @chmod($CFG->tempdir.'/'.$file, $CFG->filepermissions);
    return true;
}


/**
 * Recursively delete the file or folder with path $location. That is,
 * if it is a file delete it. If it is a folder, delete all its content
 * then delete it. If $location does not exist to start, that is not
 * considered an error.
 *
 * @param string $location the path to remove.
 * @return bool
 */
function fulldelete($location) {
    if (empty($location)) {
        // extra safety against wrong param
        return false;
    }
    if (is_dir($location)) {
        if (!$currdir = opendir($location)) {
            return false;
        }
        while (false !== ($file = readdir($currdir))) {
            if ($file <> ".." && $file <> ".") {
                $fullfile = $location."/".$file;
                if (is_dir($fullfile)) {
                    if (!fulldelete($fullfile)) {
                        return false;
                    }
                } else {
                    if (!unlink($fullfile)) {
                        return false;
                    }
                }
            }
        }
        closedir($currdir);
        if (! rmdir($location)) {
            return false;
        }

    } else if (file_exists($location)) {
        if (!unlink($location)) {
            return false;
        }
    }
    return true;
}

/**
 * Send requested byterange of file.
 *
 * @param resource $handle A file handle
 * @param string $mimetype The mimetype for the output
 * @param array $ranges An array of ranges to send
 * @param string $filesize The size of the content if only one range is used
 */
function byteserving_send_file($handle, $mimetype, $ranges, $filesize) {
    // better turn off any kind of compression and buffering
    ini_set('zlib.output_compression', 'Off');

    $chunksize = 1*(1024*1024); // 1MB chunks - must be less than 2MB!
    if ($handle === false) {
        die;
    }
    if (count($ranges) == 1) { //only one range requested
        $length = $ranges[0][2] - $ranges[0][1] + 1;
        header('HTTP/1.1 206 Partial content');
        header('Content-Length: '.$length);
        header('Content-Range: bytes '.$ranges[0][1].'-'.$ranges[0][2].'/'.$filesize);
        header('Content-Type: '.$mimetype);

        while(@ob_get_level()) {
            if (!@ob_end_flush()) {
                break;
            }
        }

        fseek($handle, $ranges[0][1]);
        while (!feof($handle) && $length > 0) {
            core_php_time_limit::raise(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
            $buffer = fread($handle, ($chunksize < $length ? $chunksize : $length));
            echo $buffer;
            flush();
            $length -= strlen($buffer);
        }
        fclose($handle);
        die;
    } else { // multiple ranges requested - not tested much
        $totallength = 0;
        foreach($ranges as $range) {
            $totallength += strlen($range[0]) + $range[2] - $range[1] + 1;
        }
        $totallength += strlen("\r\n--".BYTESERVING_BOUNDARY."--\r\n");
        header('HTTP/1.1 206 Partial content');
        header('Content-Length: '.$totallength);
        header('Content-Type: multipart/byteranges; boundary='.BYTESERVING_BOUNDARY);

        while(@ob_get_level()) {
            if (!@ob_end_flush()) {
                break;
            }
        }

        foreach($ranges as $range) {
            $length = $range[2] - $range[1] + 1;
            echo $range[0];
            fseek($handle, $range[1]);
            while (!feof($handle) && $length > 0) {
                core_php_time_limit::raise(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
                $buffer = fread($handle, ($chunksize < $length ? $chunksize : $length));
                echo $buffer;
                flush();
                $length -= strlen($buffer);
            }
        }
        echo "\r\n--".BYTESERVING_BOUNDARY."--\r\n";
        fclose($handle);
        die;
    }
}

/**
 * RESTful cURL class
 *
 * This is a wrapper class for curl, it is quite easy to use:
 * <code>
 * $c = new curl;
 * // enable cache
 * $c = new curl(array('cache'=>true));
 * // enable cookie
 * $c = new curl(array('cookie'=>true));
 * // enable proxy
 * $c = new curl(array('proxy'=>true));
 *
 * // HTTP GET Method
 * $html = $c->get('http://example.com');
 * // HTTP POST Method
 * $html = $c->post('http://example.com/', array('q'=>'words', 'name'=>'moodle'));
 * // HTTP PUT Method
 * $html = $c->put('http://example.com/', array('file'=>'/var/www/test.txt');
 * </code>
 *
 * @package   core_files
 * @category files
 * @copyright Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class curl {
    /** @var bool Caches http request contents */
    public  $cache    = false;
    /** @var bool Uses proxy, null means automatic based on URL */
    public  $proxy    = null;
    /** @var string library version */
    public  $version  = '0.4 dev';
    /** @var array http's response */
    public  $response = array();
    /** @var array Raw response headers, needed for BC in download_file_content(). */
    public $rawresponse = array();
    /** @var array http header */
    public  $header   = array();
    /** @var string cURL information */
    public  $info;
    /** @var string error */
    public  $error;
    /** @var int error code */
    public  $errno;
    /** @var bool use workaround for open_basedir restrictions, to be changed from unit tests only! */
    public $emulateredirects = null;

    /** @var array cURL options */
    private $options;

    /** @var string Proxy host */
    private $proxy_host = '';
    /** @var string Proxy auth */
    private $proxy_auth = '';
    /** @var string Proxy type */
    private $proxy_type = '';
    /** @var bool Debug mode on */
    private $debug    = false;
    /** @var bool|string Path to cookie file */
    private $cookie   = false;
    /** @var bool tracks multiple headers in response - redirect detection */
    private $responsefinished = false;

    /**
     * Curl constructor.
     *
     * Allowed settings are:
     *  proxy: (bool) use proxy server, null means autodetect non-local from url
     *  debug: (bool) use debug output
     *  cookie: (string) path to cookie file, false if none
     *  cache: (bool) use cache
     *  module_cache: (string) type of cache
     *
     * @param array $settings
     */
    public function __construct($settings = array()) {
        global $CFG;
        if (!function_exists('curl_init')) {
            $this->error = 'cURL module must be enabled!';
            trigger_error($this->error, E_USER_ERROR);
            return false;
        }

        // All settings of this class should be init here.
        $this->resetopt();
        if (!empty($settings['debug'])) {
            $this->debug = true;
        }
        if (!empty($settings['cookie'])) {
            if($settings['cookie'] === true) {
                $this->cookie = $CFG->dataroot.'/curl_cookie.txt';
            } else {
                $this->cookie = $settings['cookie'];
            }
        }
        if (!empty($settings['cache'])) {
            if (class_exists('curl_cache')) {
                if (!empty($settings['module_cache'])) {
                    $this->cache = new curl_cache($settings['module_cache']);
                } else {
                    $this->cache = new curl_cache('misc');
                }
            }
        }
        if (!empty($CFG->proxyhost)) {
            if (empty($CFG->proxyport)) {
                $this->proxy_host = $CFG->proxyhost;
            } else {
                $this->proxy_host = $CFG->proxyhost.':'.$CFG->proxyport;
            }
            if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                $this->proxy_auth = $CFG->proxyuser.':'.$CFG->proxypassword;
                $this->setopt(array(
                            'proxyauth'=> CURLAUTH_BASIC | CURLAUTH_NTLM,
                            'proxyuserpwd'=>$this->proxy_auth));
            }
            if (!empty($CFG->proxytype)) {
                if ($CFG->proxytype == 'SOCKS5') {
                    $this->proxy_type = CURLPROXY_SOCKS5;
                } else {
                    $this->proxy_type = CURLPROXY_HTTP;
                    $this->setopt(array('httpproxytunnel'=>false));
                }
                $this->setopt(array('proxytype'=>$this->proxy_type));
            }

            if (isset($settings['proxy'])) {
                $this->proxy = $settings['proxy'];
            }
        } else {
            $this->proxy = false;
        }

        if (!isset($this->emulateredirects)) {
            $this->emulateredirects = ini_get('open_basedir');
        }
    }

    /**
     * Resets the CURL options that have already been set
     */
    public function resetopt() {
        $this->options = array();
        $this->options['CURLOPT_USERAGENT']         = 'MoodleBot/1.0';
        // True to include the header in the output
        $this->options['CURLOPT_HEADER']            = 0;
        // True to Exclude the body from the output
        $this->options['CURLOPT_NOBODY']            = 0;
        // Redirect ny default.
        $this->options['CURLOPT_FOLLOWLOCATION']    = 1;
        $this->options['CURLOPT_MAXREDIRS']         = 10;
        $this->options['CURLOPT_ENCODING']          = '';
        // TRUE to return the transfer as a string of the return
        // value of curl_exec() instead of outputting it out directly.
        $this->options['CURLOPT_RETURNTRANSFER']    = 1;
        $this->options['CURLOPT_SSL_VERIFYPEER']    = 0;
        $this->options['CURLOPT_SSL_VERIFYHOST']    = 2;
        $this->options['CURLOPT_CONNECTTIMEOUT']    = 30;

        if ($cacert = self::get_cacert()) {
            $this->options['CURLOPT_CAINFO'] = $cacert;
        }
    }

    /**
     * Get the location of ca certificates.
     * @return string absolute file path or empty if default used
     */
    public static function get_cacert() {
        global $CFG;

        // Bundle in dataroot always wins.
        if (is_readable("$CFG->dataroot/moodleorgca.crt")) {
            return realpath("$CFG->dataroot/moodleorgca.crt");
        }

        // Next comes the default from php.ini
        $cacert = ini_get('curl.cainfo');
        if (!empty($cacert) and is_readable($cacert)) {
            return realpath($cacert);
        }

        // Windows PHP does not have any certs, we need to use something.
        if ($CFG->ostype === 'WINDOWS') {
            if (is_readable("$CFG->libdir/cacert.pem")) {
                return realpath("$CFG->libdir/cacert.pem");
            }
        }

        // Use default, this should work fine on all properly configured *nix systems.
        return null;
    }

    /**
     * Reset Cookie
     */
    public function resetcookie() {
        if (!empty($this->cookie)) {
            if (is_file($this->cookie)) {
                $fp = fopen($this->cookie, 'w');
                if (!empty($fp)) {
                    fwrite($fp, '');
                    fclose($fp);
                }
            }
        }
    }

    /**
     * Set curl options.
     *
     * Do not use the curl constants to define the options, pass a string
     * corresponding to that constant. Ie. to set CURLOPT_MAXREDIRS, pass
     * array('CURLOPT_MAXREDIRS' => 10) or array('maxredirs' => 10) to this method.
     *
     * @param array $options If array is null, this function will reset the options to default value.
     * @return void
     * @throws coding_exception If an option uses constant value instead of option name.
     */
    public function setopt($options = array()) {
        if (is_array($options)) {
            foreach ($options as $name => $val) {
                if (!is_string($name)) {
                    throw new coding_exception('Curl options should be defined using strings, not constant values.');
                }
                if (stripos($name, 'CURLOPT_') === false) {
                    $name = strtoupper('CURLOPT_'.$name);
                } else {
                    $name = strtoupper($name);
                }
                $this->options[$name] = $val;
            }
        }
    }

    /**
     * Reset http method
     */
    public function cleanopt() {
        unset($this->options['CURLOPT_HTTPGET']);
        unset($this->options['CURLOPT_POST']);
        unset($this->options['CURLOPT_POSTFIELDS']);
        unset($this->options['CURLOPT_PUT']);
        unset($this->options['CURLOPT_INFILE']);
        unset($this->options['CURLOPT_INFILESIZE']);
        unset($this->options['CURLOPT_CUSTOMREQUEST']);
        unset($this->options['CURLOPT_FILE']);
    }

    /**
     * Resets the HTTP Request headers (to prepare for the new request)
     */
    public function resetHeader() {
        $this->header = array();
    }

    /**
     * Set HTTP Request Header
     *
     * @param array $header
     */
    public function setHeader($header) {
        if (is_array($header)) {
            foreach ($header as $v) {
                $this->setHeader($v);
            }
        } else {
            // Remove newlines, they are not allowed in headers.
            $this->header[] = preg_replace('/[\r\n]/', '', $header);
        }
    }

    /**
     * Get HTTP Response Headers
     * @return array of arrays
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Get raw HTTP Response Headers
     * @return array of strings
     */
    public function get_raw_response() {
        return $this->rawresponse;
    }

    /**
     * private callback function
     * Formatting HTTP Response Header
     *
     * We only keep the last headers returned. For example during a redirect the
     * redirect headers will not appear in {@link self::getResponse()}, if you need
     * to use those headers, refer to {@link self::get_raw_response()}.
     *
     * @param resource $ch Apparently not used
     * @param string $header
     * @return int The strlen of the header
     */
    private function formatHeader($ch, $header) {
        $this->rawresponse[] = $header;

        if (trim($header, "\r\n") === '') {
            // This must be the last header.
            $this->responsefinished = true;
        }

        if (strlen($header) > 2) {
            if ($this->responsefinished) {
                // We still have headers after the supposedly last header, we must be
                // in a redirect so let's empty the response to keep the last headers.
                $this->responsefinished = false;
                $this->response = array();
            }
            list($key, $value) = explode(" ", rtrim($header, "\r\n"), 2);
            $key = rtrim($key, ':');
            if (!empty($this->response[$key])) {
                if (is_array($this->response[$key])) {
                    $this->response[$key][] = $value;
                } else {
                    $tmp = $this->response[$key];
                    $this->response[$key] = array();
                    $this->response[$key][] = $tmp;
                    $this->response[$key][] = $value;

                }
            } else {
                $this->response[$key] = $value;
            }
        }
        return strlen($header);
    }

    /**
     * Set options for individual curl instance
     *
     * @param resource $curl A curl handle
     * @param array $options
     * @return resource The curl handle
     */
    private function apply_opt($curl, $options) {
        // Clean up
        $this->cleanopt();
        // set cookie
        if (!empty($this->cookie) || !empty($options['cookie'])) {
            $this->setopt(array('cookiejar'=>$this->cookie,
                            'cookiefile'=>$this->cookie
                             ));
        }

        // Bypass proxy if required.
        if ($this->proxy === null) {
            if (!empty($this->options['CURLOPT_URL']) and is_proxybypass($this->options['CURLOPT_URL'])) {
                $proxy = false;
            } else {
                $proxy = true;
            }
        } else {
            $proxy = (bool)$this->proxy;
        }

        // Set proxy.
        if ($proxy) {
            $options['CURLOPT_PROXY'] = $this->proxy_host;
        } else {
            unset($this->options['CURLOPT_PROXY']);
        }

        $this->setopt($options);

        // Reset before set options.
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this,'formatHeader'));

        // Setting the User-Agent based on options provided.
        $useragent = '';

        if (!empty($options['CURLOPT_USERAGENT'])) {
            $useragent = $options['CURLOPT_USERAGENT'];
        } else if (!empty($this->options['CURLOPT_USERAGENT'])) {
            $useragent = $this->options['CURLOPT_USERAGENT'];
        } else {
            $useragent = 'MoodleBot/1.0';
        }

        // Set headers.
        if (empty($this->header)) {
            $this->setHeader(array(
                'User-Agent: ' . $useragent,
                'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'Connection: keep-alive'
                ));
        } else if (!in_array('User-Agent: ' . $useragent, $this->header)) {
            // Remove old User-Agent if one existed.
            // We have to partial search since we don't know what the original User-Agent is.
            if ($match = preg_grep('/User-Agent.*/', $this->header)) {
                $key = array_keys($match)[0];
                unset($this->header[$key]);
            }
            $this->setHeader(array('User-Agent: ' . $useragent));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);

        if ($this->debug) {
            echo '<h1>Options</h1>';
            var_dump($this->options);
            echo '<h1>Header</h1>';
            var_dump($this->header);
        }

        // Do not allow infinite redirects.
        if (!isset($this->options['CURLOPT_MAXREDIRS'])) {
            $this->options['CURLOPT_MAXREDIRS'] = 0;
        } else if ($this->options['CURLOPT_MAXREDIRS'] > 100) {
            $this->options['CURLOPT_MAXREDIRS'] = 100;
        } else {
            $this->options['CURLOPT_MAXREDIRS'] = (int)$this->options['CURLOPT_MAXREDIRS'];
        }

        // Make sure we always know if redirects expected.
        if (!isset($this->options['CURLOPT_FOLLOWLOCATION'])) {
            $this->options['CURLOPT_FOLLOWLOCATION'] = 0;
        }

        // Limit the protocols to HTTP and HTTPS.
        if (defined('CURLOPT_PROTOCOLS')) {
            $this->options['CURLOPT_PROTOCOLS'] = (CURLPROTO_HTTP | CURLPROTO_HTTPS);
            $this->options['CURLOPT_REDIR_PROTOCOLS'] = (CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }

        // Set options.
        foreach($this->options as $name => $val) {
            if ($name === 'CURLOPT_FOLLOWLOCATION' and $this->emulateredirects) {
                // The redirects are emulated elsewhere.
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
                continue;
            }
            $name = constant($name);
            curl_setopt($curl, $name, $val);
        }

        return $curl;
    }

    /**
     * Download multiple files in parallel
     *
     * Calls {@link multi()} with specific download headers
     *
     * <code>
     * $c = new curl();
     * $file1 = fopen('a', 'wb');
     * $file2 = fopen('b', 'wb');
     * $c->download(array(
     *     array('url'=>'http://localhost/', 'file'=>$file1),
     *     array('url'=>'http://localhost/20/', 'file'=>$file2)
     * ));
     * fclose($file1);
     * fclose($file2);
     * </code>
     *
     * or
     *
     * <code>
     * $c = new curl();
     * $c->download(array(
     *              array('url'=>'http://localhost/', 'filepath'=>'/tmp/file1.tmp'),
     *              array('url'=>'http://localhost/20/', 'filepath'=>'/tmp/file2.tmp')
     *              ));
     * </code>
     *
     * @param array $requests An array of files to request {
     *                  url => url to download the file [required]
     *                  file => file handler, or
     *                  filepath => file path
     * }
     * If 'file' and 'filepath' parameters are both specified in one request, the
     * open file handle in the 'file' parameter will take precedence and 'filepath'
     * will be ignored.
     *
     * @param array $options An array of options to set
     * @return array An array of results
     */
    public function download($requests, $options = array()) {
        $options['RETURNTRANSFER'] = false;
        return $this->multi($requests, $options);
    }

    /**
     * Multi HTTP Requests
     * This function could run multi-requests in parallel.
     *
     * @param array $requests An array of files to request
     * @param array $options An array of options to set
     * @return array An array of results
     */
    protected function multi($requests, $options = array()) {
        $count   = count($requests);
        $handles = array();
        $results = array();
        $main    = curl_multi_init();
        for ($i = 0; $i < $count; $i++) {
            if (!empty($requests[$i]['filepath']) and empty($requests[$i]['file'])) {
                // open file
                $requests[$i]['file'] = fopen($requests[$i]['filepath'], 'w');
                $requests[$i]['auto-handle'] = true;
            }
            foreach($requests[$i] as $n=>$v) {
                $options[$n] = $v;
            }
            $handles[$i] = curl_init($requests[$i]['url']);
            $this->apply_opt($handles[$i], $options);
            curl_multi_add_handle($main, $handles[$i]);
        }
        $running = 0;
        do {
            curl_multi_exec($main, $running);
        } while($running > 0);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($options['CURLOPT_RETURNTRANSFER'])) {
                $results[] = true;
            } else {
                $results[] = curl_multi_getcontent($handles[$i]);
            }
            curl_multi_remove_handle($main, $handles[$i]);
        }
        curl_multi_close($main);

        for ($i = 0; $i < $count; $i++) {
            if (!empty($requests[$i]['filepath']) and !empty($requests[$i]['auto-handle'])) {
                // close file handler if file is opened in this function
                fclose($requests[$i]['file']);
            }
        }
        return $results;
    }

    /**
     * Single HTTP Request
     *
     * @param string $url The URL to request
     * @param array $options
     * @return bool
     */
    protected function request($url, $options = array()) {
        // Set the URL as a curl option.
        $this->setopt(array('CURLOPT_URL' => $url));

        // Create curl instance.
        $curl = curl_init();

        // Reset here so that the data is valid when result returned from cache.
        $this->info             = array();
        $this->error            = '';
        $this->errno            = 0;
        $this->response         = array();
        $this->rawresponse      = array();
        $this->responsefinished = false;

        $this->apply_opt($curl, $options);
        if ($this->cache && $ret = $this->cache->get($this->options)) {
            return $ret;
        }

        $ret = curl_exec($curl);
        $this->info  = curl_getinfo($curl);
        $this->error = curl_error($curl);
        $this->errno = curl_errno($curl);
        // Note: $this->response and $this->rawresponse are filled by $hits->formatHeader callback.

        if ($this->emulateredirects and $this->options['CURLOPT_FOLLOWLOCATION'] and $this->info['http_code'] != 200) {
            $redirects = 0;

            while($redirects <= $this->options['CURLOPT_MAXREDIRS']) {

                if ($this->info['http_code'] == 301) {
                    // Moved Permanently - repeat the same request on new URL.

                } else if ($this->info['http_code'] == 302) {
                    // Found - the standard redirect - repeat the same request on new URL.

                } else if ($this->info['http_code'] == 303) {
                    // 303 See Other - repeat only if GET, do not bother with POSTs.
                    if (empty($this->options['CURLOPT_HTTPGET'])) {
                        break;
                    }

                } else if ($this->info['http_code'] == 307) {
                    // Temporary Redirect - must repeat using the same request type.

                } else if ($this->info['http_code'] == 308) {
                    // Permanent Redirect - must repeat using the same request type.

                } else {
                    // Some other http code means do not retry!
                    break;
                }

                $redirects++;

                $redirecturl = null;
                if (isset($this->info['redirect_url'])) {
                    if (preg_match('|^https?://|i', $this->info['redirect_url'])) {
                        $redirecturl = $this->info['redirect_url'];
                    }
                }
                if (!$redirecturl) {
                    foreach ($this->response as $k => $v) {
                        if (strtolower($k) === 'location') {
                            $redirecturl = $v;
                            break;
                        }
                    }
                    if (preg_match('|^https?://|i', $redirecturl)) {
                        // Great, this is the correct location format!

                    } else if ($redirecturl) {
                        $current = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
                        if (strpos($redirecturl, '/') === 0) {
                            // Relative to server root - just guess.
                            $pos = strpos('/', $current, 8);
                            if ($pos === false) {
                                $redirecturl = $current.$redirecturl;
                            } else {
                                $redirecturl = substr($current, 0, $pos).$redirecturl;
                            }
                        } else {
                            // Relative to current script.
                            $redirecturl = dirname($current).'/'.$redirecturl;
                        }
                    }
                }

                curl_setopt($curl, CURLOPT_URL, $redirecturl);
                $ret = curl_exec($curl);

                $this->info  = curl_getinfo($curl);
                $this->error = curl_error($curl);
                $this->errno = curl_errno($curl);

                $this->info['redirect_count'] = $redirects;

                if ($this->info['http_code'] === 200) {
                    // Finally this is what we wanted.
                    break;
                }
                if ($this->errno != CURLE_OK) {
                    // Something wrong is going on.
                    break;
                }
            }
            if ($redirects > $this->options['CURLOPT_MAXREDIRS']) {
                $this->errno = CURLE_TOO_MANY_REDIRECTS;
                $this->error = 'Maximum ('.$this->options['CURLOPT_MAXREDIRS'].') redirects followed';
            }
        }

        if ($this->cache) {
            $this->cache->set($this->options, $ret);
        }

        if ($this->debug) {
            echo '<h1>Return Data</h1>';
            var_dump($ret);
            echo '<h1>Info</h1>';
            var_dump($this->info);
            echo '<h1>Error</h1>';
            var_dump($this->error);
        }

        curl_close($curl);

        if (empty($this->error)) {
            return $ret;
        } else {
            return $this->error;
            // exception is not ajax friendly
            //throw new moodle_exception($this->error, 'curl');
        }
    }

    /**
     * HTTP HEAD method
     *
     * @see request()
     *
     * @param string $url
     * @param array $options
     * @return bool
     */
    public function head($url, $options = array()) {
        $options['CURLOPT_HTTPGET'] = 0;
        $options['CURLOPT_HEADER']  = 1;
        $options['CURLOPT_NOBODY']  = 1;
        return $this->request($url, $options);
    }

    /**
     * HTTP POST method
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return bool
     */
    public function post($url, $params = '', $options = array()) {
        $options['CURLOPT_POST']       = 1;
        if (is_array($params)) {
            $this->_tmp_file_post_params = array();
            foreach ($params as $key => $value) {
                if ($value instanceof stored_file) {
                    $value->add_to_curl_request($this, $key);
                } else {
                    $this->_tmp_file_post_params[$key] = $value;
                }
            }
            $options['CURLOPT_POSTFIELDS'] = $this->_tmp_file_post_params;
            unset($this->_tmp_file_post_params);
        } else {
            // $params is the raw post data
            $options['CURLOPT_POSTFIELDS'] = $params;
        }
        return $this->request($url, $options);
    }

    /**
     * HTTP GET method
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return bool
     */
    public function get($url, $params = array(), $options = array()) {
        $options['CURLOPT_HTTPGET'] = 1;

        if (!empty($params)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= http_build_query($params, '', '&');
        }
        return $this->request($url, $options);
    }

    /**
     * Downloads one file and writes it to the specified file handler
     *
     * <code>
     * $c = new curl();
     * $file = fopen('savepath', 'w');
     * $result = $c->download_one('http://localhost/', null,
     *   array('file' => $file, 'timeout' => 5, 'followlocation' => true, 'maxredirs' => 3));
     * fclose($file);
     * $download_info = $c->get_info();
     * if ($result === true) {
     *   // file downloaded successfully
     * } else {
     *   $error_text = $result;
     *   $error_code = $c->get_errno();
     * }
     * </code>
     *
     * <code>
     * $c = new curl();
     * $result = $c->download_one('http://localhost/', null,
     *   array('filepath' => 'savepath', 'timeout' => 5, 'followlocation' => true, 'maxredirs' => 3));
     * // ... see above, no need to close handle and remove file if unsuccessful
     * </code>
     *
     * @param string $url
     * @param array|null $params key-value pairs to be added to $url as query string
     * @param array $options request options. Must include either 'file' or 'filepath'
     * @return bool|string true on success or error string on failure
     */
    public function download_one($url, $params, $options = array()) {
        $options['CURLOPT_HTTPGET'] = 1;
        if (!empty($params)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= http_build_query($params, '', '&');
        }
        if (!empty($options['filepath']) && empty($options['file'])) {
            // open file
            if (!($options['file'] = fopen($options['filepath'], 'w'))) {
                $this->errno = 100;
                return get_string('cannotwritefile', 'error', $options['filepath']);
            }
            $filepath = $options['filepath'];
        }
        unset($options['filepath']);
        $result = $this->request($url, $options);
        if (isset($filepath)) {
            fclose($options['file']);
            if ($result !== true) {
                unlink($filepath);
            }
        }
        return $result;
    }

    /**
     * HTTP PUT method
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return bool
     */
    public function put($url, $params = array(), $options = array()) {
        $file = $params['file'];
        if (!is_file($file)) {
            return null;
        }
        $fp   = fopen($file, 'r');
        $size = filesize($file);
        $options['CURLOPT_PUT']        = 1;
        $options['CURLOPT_INFILESIZE'] = $size;
        $options['CURLOPT_INFILE']     = $fp;
        if (!isset($this->options['CURLOPT_USERPWD'])) {
            $this->setopt(array('CURLOPT_USERPWD'=>'anonymous: noreply@moodle.org'));
        }
        $ret = $this->request($url, $options);
        fclose($fp);
        return $ret;
    }

    /**
     * HTTP DELETE method
     *
     * @param string $url
     * @param array $param
     * @param array $options
     * @return bool
     */
    public function delete($url, $param = array(), $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'DELETE';
        if (!isset($options['CURLOPT_USERPWD'])) {
            $options['CURLOPT_USERPWD'] = 'anonymous: noreply@moodle.org';
        }
        $ret = $this->request($url, $options);
        return $ret;
    }

    /**
     * HTTP TRACE method
     *
     * @param string $url
     * @param array $options
     * @return bool
     */
    public function trace($url, $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'TRACE';
        $ret = $this->request($url, $options);
        return $ret;
    }

    /**
     * HTTP OPTIONS method
     *
     * @param string $url
     * @param array $options
     * @return bool
     */
    public function options($url, $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'OPTIONS';
        $ret = $this->request($url, $options);
        return $ret;
    }

    /**
     * Get curl information
     *
     * @return string
     */
    public function get_info() {
        return $this->info;
    }

    /**
     * Get curl error code
     *
     * @return int
     */
    public function get_errno() {
        return $this->errno;
    }

    /**
     * When using a proxy, an additional HTTP response code may appear at
     * the start of the header. For example, when using https over a proxy
     * there may be 'HTTP/1.0 200 Connection Established'. Other codes are
     * also possible and some may come with their own headers.
     *
     * If using the return value containing all headers, this function can be
     * called to remove unwanted doubles.
     *
     * Note that it is not possible to distinguish this situation from valid
     * data unless you know the actual response part (below the headers)
     * will not be included in this string, or else will not 'look like' HTTP
     * headers. As a result it is not safe to call this function for general
     * data.
     *
     * @param string $input Input HTTP response
     * @return string HTTP response with additional headers stripped if any
     */
    public static function strip_double_headers($input) {
        // I have tried to make this regular expression as specific as possible
        // to avoid any case where it does weird stuff if you happen to put
        // HTTP/1.1 200 at the start of any line in your RSS file. This should
        // also make it faster because it can abandon regex processing as soon
        // as it hits something that doesn't look like an http header. The
        // header definition is taken from RFC 822, except I didn't support
        // folding which is never used in practice.
        $crlf = "\r\n";
        return preg_replace(
                // HTTP version and status code (ignore value of code).
                '~^HTTP/1\..*' . $crlf .
                // Header name: character between 33 and 126 decimal, except colon.
                // Colon. Header value: any character except \r and \n. CRLF.
                '(?:[\x21-\x39\x3b-\x7e]+:[^' . $crlf . ']+' . $crlf . ')*' .
                // Headers are terminated by another CRLF (blank line).
                $crlf .
                // Second HTTP status code, this time must be 200.
                '(HTTP/1.[01] 200 )~', '$1', $input);
    }
}

/**
 * This class is used by cURL class, use case:
 *
 * <code>
 * $CFG->repositorycacheexpire = 120;
 * $CFG->curlcache = 120;
 *
 * $c = new curl(array('cache'=>true), 'module_cache'=>'repository');
 * $ret = $c->get('http://www.google.com');
 * </code>
 *
 * @package   core_files
 * @copyright Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class curl_cache {
    /** @var string Path to cache directory */
    public $dir = '';

    /**
     * Constructor
     *
     * @global stdClass $CFG
     * @param string $module which module is using curl_cache
     */
    public function __construct($module = 'repository') {
        global $CFG;
        if (!empty($module)) {
            $this->dir = $CFG->cachedir.'/'.$module.'/';
        } else {
            $this->dir = $CFG->cachedir.'/misc/';
        }
        if (!file_exists($this->dir)) {
            mkdir($this->dir, $CFG->directorypermissions, true);
        }
        if ($module == 'repository') {
            if (empty($CFG->repositorycacheexpire)) {
                $CFG->repositorycacheexpire = 120;
            }
            $this->ttl = $CFG->repositorycacheexpire;
        } else {
            if (empty($CFG->curlcache)) {
                $CFG->curlcache = 120;
            }
            $this->ttl = $CFG->curlcache;
        }
    }

    /**
     * Get cached value
     *
     * @global stdClass $CFG
     * @global stdClass $USER
     * @param mixed $param
     * @return bool|string
     */
    public function get($param) {
        global $CFG, $USER;
        $this->cleanup($this->ttl);
        $filename = 'u'.$USER->id.'_'.md5(serialize($param));
        if(file_exists($this->dir.$filename)) {
            $lasttime = filemtime($this->dir.$filename);
            if (time()-$lasttime > $this->ttl) {
                return false;
            } else {
                $fp = fopen($this->dir.$filename, 'r');
                $size = filesize($this->dir.$filename);
                $content = fread($fp, $size);
                return unserialize($content);
            }
        }
        return false;
    }

    /**
     * Set cache value
     *
     * @global object $CFG
     * @global object $USER
     * @param mixed $param
     * @param mixed $val
     */
    public function set($param, $val) {
        global $CFG, $USER;
        $filename = 'u'.$USER->id.'_'.md5(serialize($param));
        $fp = fopen($this->dir.$filename, 'w');
        fwrite($fp, serialize($val));
        fclose($fp);
        @chmod($this->dir.$filename, $CFG->filepermissions);
    }

    /**
     * Remove cache files
     *
     * @param int $expire The number of seconds before expiry
     */
    public function cleanup($expire) {
        if ($dir = opendir($this->dir)) {
            while (false !== ($file = readdir($dir))) {
                if(!is_dir($file) && $file != '.' && $file != '..') {
                    $lasttime = @filemtime($this->dir.$file);
                    if (time() - $lasttime > $expire) {
                        @unlink($this->dir.$file);
                    }
                }
            }
            closedir($dir);
        }
    }
    /**
     * delete current user's cache file
     *
     * @global object $CFG
     * @global object $USER
     */
    public function refresh() {
        global $CFG, $USER;
        if ($dir = opendir($this->dir)) {
            while (false !== ($file = readdir($dir))) {
                if (!is_dir($file) && $file != '.' && $file != '..') {
                    if (strpos($file, 'u'.$USER->id.'_') !== false) {
                        @unlink($this->dir.$file);
                    }
                }
            }
        }
    }
}

/**
 * This function delegates file serving to individual plugins
 *
 * @param string $relativepath
 * @param bool $forcedownload
 * @param null|string $preview the preview mode, defaults to serving the original file
 * @todo MDL-31088 file serving improments
 */
function file_pluginfile($relativepath, $forcedownload, $preview = null) {
    global $DB, $CFG, $USER;
    // relative path must start with '/'
    if (!$relativepath) {
        print_error('invalidargorconf');
    } else if ($relativepath[0] != '/') {
        print_error('pathdoesnotstartslash');
    }

    // extract relative path components
    $args = explode('/', ltrim($relativepath, '/'));

    if (count($args) < 3) { // always at least context, component and filearea
        print_error('invalidarguments');
    }

    $contextid = (int)array_shift($args);
    $component = clean_param(array_shift($args), PARAM_COMPONENT);
    $filearea  = clean_param(array_shift($args), PARAM_AREA);

    list($context, $course, $cm) = get_context_info_array($contextid);

    $fs = get_file_storage();

    // ========================================================================================================================
    if ($component === 'blog') {
        // Blog file serving
        if ($context->contextlevel != CONTEXT_SYSTEM) {
            send_file_not_found();
        }
        if ($filearea !== 'attachment' and $filearea !== 'post') {
            send_file_not_found();
        }

        if (empty($CFG->enableblogs)) {
            print_error('siteblogdisable', 'blog');
        }

        $entryid = (int)array_shift($args);
        if (!$entry = $DB->get_record('post', array('module'=>'blog', 'id'=>$entryid))) {
            send_file_not_found();
        }
        if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login();
            if (isguestuser()) {
                print_error('noguest');
            }
            if ($CFG->bloglevel == BLOG_USER_LEVEL) {
                if ($USER->id != $entry->userid) {
                    send_file_not_found();
                }
            }
        }

        if ($entry->publishstate === 'public') {
            if ($CFG->forcelogin) {
                require_login();
            }

        } else if ($entry->publishstate === 'site') {
            require_login();
            //ok
        } else if ($entry->publishstate === 'draft') {
            require_login();
            if ($USER->id != $entry->userid) {
                send_file_not_found();
            }
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';

        if (!$file = $fs->get_file($context->id, $component, $filearea, $entryid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        send_stored_file($file, 10*60, 0, true, array('preview' => $preview)); // download MUST be forced - security!

    // ========================================================================================================================
    } else if ($component === 'grade') {
        if (($filearea === 'outcome' or $filearea === 'scale') and $context->contextlevel == CONTEXT_SYSTEM) {
            // Global gradebook files
            if ($CFG->forcelogin) {
                require_login();
            }

            $fullpath = "/$context->id/$component/$filearea/".implode('/', $args);

            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'feedback' and $context->contextlevel == CONTEXT_COURSE) {
            //TODO: nobody implemented this yet in grade edit form!!
            send_file_not_found();

            if ($CFG->forcelogin || $course->id != SITEID) {
                require_login($course);
            }

            $fullpath = "/$context->id/$component/$filearea/".implode('/', $args);

            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));
        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'tag') {
        if ($filearea === 'description' and $context->contextlevel == CONTEXT_SYSTEM) {

            // All tag descriptions are going to be public but we still need to respect forcelogin
            if ($CFG->forcelogin) {
                require_login();
            }

            $fullpath = "/$context->id/tag/description/".implode('/', $args);

            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, true, array('preview' => $preview));

        } else {
            send_file_not_found();
        }
    // ========================================================================================================================
    } else if ($component === 'badges') {
        require_once($CFG->libdir . '/badgeslib.php');

        $badgeid = (int)array_shift($args);
        $badge = new badge($badgeid);
        $filename = array_pop($args);

        if ($filearea === 'badgeimage') {
            if ($filename !== 'f1' && $filename !== 'f2') {
                send_file_not_found();
            }
            if (!$file = $fs->get_file($context->id, 'badges', 'badgeimage', $badge->id, '/', $filename.'.png')) {
                send_file_not_found();
            }

            \core\session\manager::write_close();
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));
        } else if ($filearea === 'userbadge'  and $context->contextlevel == CONTEXT_USER) {
            if (!$file = $fs->get_file($context->id, 'badges', 'userbadge', $badge->id, '/', $filename.'.png')) {
                send_file_not_found();
            }

            \core\session\manager::write_close();
            send_stored_file($file, 60*60, 0, true, array('preview' => $preview));
        }
    // ========================================================================================================================
    } else if ($component === 'calendar') {
        if ($filearea === 'event_description'  and $context->contextlevel == CONTEXT_SYSTEM) {

            // All events here are public the one requirement is that we respect forcelogin
            if ($CFG->forcelogin) {
                require_login();
            }

            // Get the event if from the args array
            $eventid = array_shift($args);

            // Load the event from the database
            if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'eventtype'=>'site'))) {
                send_file_not_found();
            }

            // Get the file and serve if successful
            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'event_description' and $context->contextlevel == CONTEXT_USER) {

            // Must be logged in, if they are not then they obviously can't be this user
            require_login();

            // Don't want guests here, potentially saves a DB call
            if (isguestuser()) {
                send_file_not_found();
            }

            // Get the event if from the args array
            $eventid = array_shift($args);

            // Load the event from the database - user id must match
            if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'userid'=>$USER->id, 'eventtype'=>'user'))) {
                send_file_not_found();
            }

            // Get the file and serve if successful
            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, true, array('preview' => $preview));

        } else if ($filearea === 'event_description' and $context->contextlevel == CONTEXT_COURSE) {

            // Respect forcelogin and require login unless this is the site.... it probably
            // should NEVER be the site
            if ($CFG->forcelogin || $course->id != SITEID) {
                require_login($course);
            }

            // Must be able to at least view the course. This does not apply to the front page.
            if ($course->id != SITEID && (!is_enrolled($context)) && (!is_viewing($context))) {
                //TODO: hmm, do we really want to block guests here?
                send_file_not_found();
            }

            // Get the event id
            $eventid = array_shift($args);

            // Load the event from the database we need to check whether it is
            // a) valid course event
            // b) a group event
            // Group events use the course context (there is no group context)
            if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'courseid'=>$course->id))) {
                send_file_not_found();
            }

            // If its a group event require either membership of view all groups capability
            if ($event->eventtype === 'group') {
                if (!has_capability('moodle/site:accessallgroups', $context) && !groups_is_member($event->groupid, $USER->id)) {
                    send_file_not_found();
                }
            } else if ($event->eventtype === 'course' || $event->eventtype === 'site') {
                // Ok. Please note that the event type 'site' still uses a course context.
            } else {
                // Some other type.
                send_file_not_found();
            }

            // If we get this far we can serve the file
            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'user') {
        if ($filearea === 'icon' and $context->contextlevel == CONTEXT_USER) {
            if (count($args) == 1) {
                $themename = theme_config::DEFAULT_THEME;
                $filename = array_shift($args);
            } else {
                $themename = array_shift($args);
                $filename = array_shift($args);
            }

            // fix file name automatically
            if ($filename !== 'f1' and $filename !== 'f2' and $filename !== 'f3') {
                $filename = 'f1';
            }

            if ((!empty($CFG->forcelogin) and !isloggedin()) ||
                    (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))) {
                // protect images if login required and not logged in;
                // also if login is required for profile images and is not logged in or guest
                // do not use require_login() because it is expensive and not suitable here anyway
                $theme = theme_config::load($themename);
                redirect($theme->pix_url('u/'.$filename, 'moodle')); // intentionally not cached
            }

            if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'.png')) {
                if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'.jpg')) {
                    if ($filename === 'f3') {
                        // f3 512x512px was introduced in 2.3, there might be only the smaller version.
                        if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', 'f1.png')) {
                            $file = $fs->get_file($context->id, 'user', 'icon', 0, '/', 'f1.jpg');
                        }
                    }
                }
            }
            if (!$file) {
                // bad reference - try to prevent future retries as hard as possible!
                if ($user = $DB->get_record('user', array('id'=>$context->instanceid), 'id, picture')) {
                    if ($user->picture > 0) {
                        $DB->set_field('user', 'picture', 0, array('id'=>$user->id));
                    }
                }
                // no redirect here because it is not cached
                $theme = theme_config::load($themename);
                $imagefile = $theme->resolve_image_location('u/'.$filename, 'moodle', null);
                send_file($imagefile, basename($imagefile), 60*60*24*14);
            }

            $options = array('preview' => $preview);
            if (empty($CFG->forcelogin) && empty($CFG->forceloginforprofileimage)) {
                // Profile images should be cache-able by both browsers and proxies according
                // to $CFG->forcelogin and $CFG->forceloginforprofileimage.
                $options['cacheability'] = 'public';
            }
            send_stored_file($file, 60*60*24*365, 0, false, $options); // enable long caching, there are many images on each page

        } else if ($filearea === 'private' and $context->contextlevel == CONTEXT_USER) {
            require_login();

            if (isguestuser()) {
                send_file_not_found();
            }

            if ($USER->id !== $context->instanceid) {
                send_file_not_found();
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, $component, $filearea, 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, true, array('preview' => $preview)); // must force download - security!

        } else if ($filearea === 'profile' and $context->contextlevel == CONTEXT_USER) {

            if ($CFG->forcelogin) {
                require_login();
            }

            $userid = $context->instanceid;

            if ($USER->id == $userid) {
                // always can access own

            } else if (!empty($CFG->forceloginforprofiles)) {
                require_login();

                if (isguestuser()) {
                    send_file_not_found();
                }

                // we allow access to site profile of all course contacts (usually teachers)
                if (!has_coursecontact_role($userid) && !has_capability('moodle/user:viewdetails', $context)) {
                    send_file_not_found();
                }

                $canview = false;
                if (has_capability('moodle/user:viewdetails', $context)) {
                    $canview = true;
                } else {
                    $courses = enrol_get_my_courses();
                }

                while (!$canview && count($courses) > 0) {
                    $course = array_shift($courses);
                    if (has_capability('moodle/user:viewdetails', context_course::instance($course->id))) {
                        $canview = true;
                    }
                }
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, $component, $filearea, 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, true, array('preview' => $preview)); // must force download - security!

        } else if ($filearea === 'profile' and $context->contextlevel == CONTEXT_COURSE) {
            $userid = (int)array_shift($args);
            $usercontext = context_user::instance($userid);

            if ($CFG->forcelogin) {
                require_login();
            }

            if (!empty($CFG->forceloginforprofiles)) {
                require_login();
                if (isguestuser()) {
                    print_error('noguest');
                }

                //TODO: review this logic of user profile access prevention
                if (!has_coursecontact_role($userid) and !has_capability('moodle/user:viewdetails', $usercontext)) {
                    print_error('usernotavailable');
                }
                if (!has_capability('moodle/user:viewdetails', $context) && !has_capability('moodle/user:viewdetails', $usercontext)) {
                    print_error('cannotviewprofile');
                }
                if (!is_enrolled($context, $userid)) {
                    print_error('notenrolledprofile');
                }
                if (groups_get_course_groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                    print_error('groupnotamember');
                }
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($usercontext->id, 'user', 'profile', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, true, array('preview' => $preview)); // must force download - security!

        } else if ($filearea === 'backup' and $context->contextlevel == CONTEXT_USER) {
            require_login();

            if (isguestuser()) {
                send_file_not_found();
            }
            $userid = $context->instanceid;

            if ($USER->id != $userid) {
                send_file_not_found();
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'user', 'backup', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, true, array('preview' => $preview)); // must force download - security!

        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'coursecat') {
        if ($context->contextlevel != CONTEXT_COURSECAT) {
            send_file_not_found();
        }

        if ($filearea === 'description') {
            if ($CFG->forcelogin) {
                // no login necessary - unless login forced everywhere
                require_login();
            }

            // Check if user can view this category.
            if (!has_capability('moodle/category:viewhiddencategories', $context)) {
                $coursecatvisible = $DB->get_field('course_categories', 'visible', array('id' => $context->instanceid));
                if (!$coursecatvisible) {
                    send_file_not_found();
                }
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'coursecat', 'description', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));
        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'course') {
        if ($context->contextlevel != CONTEXT_COURSE) {
            send_file_not_found();
        }

        if ($filearea === 'summary' || $filearea === 'overviewfiles') {
            if ($CFG->forcelogin) {
                require_login();
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'course', $filearea, 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'section') {
            if ($CFG->forcelogin) {
                require_login($course);
            } else if ($course->id != SITEID) {
                require_login($course);
            }

            $sectionid = (int)array_shift($args);

            if (!$section = $DB->get_record('course_sections', array('id'=>$sectionid, 'course'=>$course->id))) {
                send_file_not_found();
            }

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'course', 'section', $sectionid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else {
            send_file_not_found();
        }

    } else if ($component === 'cohort') {

        $cohortid = (int)array_shift($args);
        $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
        $cohortcontext = context::instance_by_id($cohort->contextid);

        // The context in the file URL must be either cohort context or context of the course underneath the cohort's context.
        if ($context->id != $cohort->contextid &&
            ($context->contextlevel != CONTEXT_COURSE || !in_array($cohort->contextid, $context->get_parent_context_ids()))) {
            send_file_not_found();
        }

        // User is able to access cohort if they have view cap on cohort level or
        // the cohort is visible and they have view cap on course level.
        $canview = has_capability('moodle/cohort:view', $cohortcontext) ||
                ($cohort->visible && has_capability('moodle/cohort:view', $context));

        if ($filearea === 'description' && $canview) {
            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (($file = $fs->get_file($cohortcontext->id, 'cohort', 'description', $cohort->id, $filepath, $filename))
                    && !$file->is_directory()) {
                \core\session\manager::write_close(); // Unlock session during file serving.
                send_stored_file($file, 60 * 60, 0, $forcedownload, array('preview' => $preview));
            }
        }

        send_file_not_found();

    } else if ($component === 'group') {
        if ($context->contextlevel != CONTEXT_COURSE) {
            send_file_not_found();
        }

        require_course_login($course, true, null, false);

        $groupid = (int)array_shift($args);

        $group = $DB->get_record('groups', array('id'=>$groupid, 'courseid'=>$course->id), '*', MUST_EXIST);
        if (($course->groupmodeforce and $course->groupmode == SEPARATEGROUPS) and !has_capability('moodle/site:accessallgroups', $context) and !groups_is_member($group->id, $USER->id)) {
            // do not allow access to separate group info if not member or teacher
            send_file_not_found();
        }

        if ($filearea === 'description') {

            require_login($course);

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'group', 'description', $group->id, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'icon') {
            $filename = array_pop($args);

            if ($filename !== 'f1' and $filename !== 'f2') {
                send_file_not_found();
            }
            if (!$file = $fs->get_file($context->id, 'group', 'icon', $group->id, '/', $filename.'.png')) {
                if (!$file = $fs->get_file($context->id, 'group', 'icon', $group->id, '/', $filename.'.jpg')) {
                    send_file_not_found();
                }
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, false, array('preview' => $preview));

        } else {
            send_file_not_found();
        }

    } else if ($component === 'grouping') {
        if ($context->contextlevel != CONTEXT_COURSE) {
            send_file_not_found();
        }

        require_login($course);

        $groupingid = (int)array_shift($args);

        // note: everybody has access to grouping desc images for now
        if ($filearea === 'description') {

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'grouping', 'description', $groupingid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'backup') {
        if ($filearea === 'course' and $context->contextlevel == CONTEXT_COURSE) {
            require_login($course);
            require_capability('moodle/backup:downloadfile', $context);

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'backup', 'course', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'section' and $context->contextlevel == CONTEXT_COURSE) {
            require_login($course);
            require_capability('moodle/backup:downloadfile', $context);

            $sectionid = (int)array_shift($args);

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'backup', 'section', $sectionid, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close();
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'activity' and $context->contextlevel == CONTEXT_MODULE) {
            require_login($course, false, $cm);
            require_capability('moodle/backup:downloadfile', $context);

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'backup', 'activity', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close();
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));

        } else if ($filearea === 'automated' and $context->contextlevel == CONTEXT_COURSE) {
            // Backup files that were generated by the automated backup systems.

            require_login($course);
            require_capability('moodle/site:config', $context);

            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'backup', 'automated', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 0, 0, $forcedownload, array('preview' => $preview));

        } else {
            send_file_not_found();
        }

    // ========================================================================================================================
    } else if ($component === 'question') {
        require_once($CFG->libdir . '/questionlib.php');
        question_pluginfile($course, $context, 'question', $filearea, $args, $forcedownload);
        send_file_not_found();

    // ========================================================================================================================
    } else if ($component === 'grading') {
        if ($filearea === 'description') {
            // files embedded into the form definition description

            if ($context->contextlevel == CONTEXT_SYSTEM) {
                require_login();

            } else if ($context->contextlevel >= CONTEXT_COURSE) {
                require_login($course, false, $cm);

            } else {
                send_file_not_found();
            }

            $formid = (int)array_shift($args);

            $sql = "SELECT ga.id
                FROM {grading_areas} ga
                JOIN {grading_definitions} gd ON (gd.areaid = ga.id)
                WHERE gd.id = ? AND ga.contextid = ?";
            $areaid = $DB->get_field_sql($sql, array($formid, $context->id), IGNORE_MISSING);

            if (!$areaid) {
                send_file_not_found();
            }

            $fullpath = "/$context->id/$component/$filearea/$formid/".implode('/', $args);

            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }

            \core\session\manager::write_close(); // Unlock session during file serving.
            send_stored_file($file, 60*60, 0, $forcedownload, array('preview' => $preview));
        }

        // ========================================================================================================================
    } else if (strpos($component, 'mod_') === 0) {
        $modname = substr($component, 4);
        if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
            send_file_not_found();
        }
        require_once("$CFG->dirroot/mod/$modname/lib.php");

        if ($context->contextlevel == CONTEXT_MODULE) {
            if ($cm->modname !== $modname) {
                // somebody tries to gain illegal access, cm type must match the component!
                send_file_not_found();
            }
        }

        if ($filearea === 'intro') {
            if (!plugin_supports('mod', $modname, FEATURE_MOD_INTRO, true)) {
                send_file_not_found();
            }
            require_course_login($course, true, $cm);

            // all users may access it
            $filename = array_pop($args);
            $filepath = $args ? '/'.implode('/', $args).'/' : '/';
            if (!$file = $fs->get_file($context->id, 'mod_'.$modname, 'intro', 0, $filepath, $filename) or $file->is_directory()) {
                send_file_not_found();
            }

            // finally send the file
            send_stored_file($file, null, 0, false, array('preview' => $preview));
        }

        $filefunction = $component.'_pluginfile';
        $filefunctionold = $modname.'_pluginfile';
        if (function_exists($filefunction)) {
            // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
            $filefunction($course, $cm, $context, $filearea, $args, $forcedownload, array('preview' => $preview));
        } else if (function_exists($filefunctionold)) {
            // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
            $filefunctionold($course, $cm, $context, $filearea, $args, $forcedownload, array('preview' => $preview));
        }

        send_file_not_found();

    // ========================================================================================================================
    } else if (strpos($component, 'block_') === 0) {
        $blockname = substr($component, 6);
        // note: no more class methods in blocks please, that is ....
        if (!file_exists("$CFG->dirroot/blocks/$blockname/lib.php")) {
            send_file_not_found();
        }
        require_once("$CFG->dirroot/blocks/$blockname/lib.php");

        if ($context->contextlevel == CONTEXT_BLOCK) {
            $birecord = $DB->get_record('block_instances', array('id'=>$context->instanceid), '*',MUST_EXIST);
            if ($birecord->blockname !== $blockname) {
                // somebody tries to gain illegal access, cm type must match the component!
                send_file_not_found();
            }

            if ($context->get_course_context(false)) {
                // If block is in course context, then check if user has capability to access course.
                require_course_login($course);
            } else if ($CFG->forcelogin) {
                // If user is logged out, bp record will not be visible, even if the user would have access if logged in.
                require_login();
            }

            $bprecord = $DB->get_record('block_positions', array('contextid' => $context->id, 'blockinstanceid' => $context->instanceid));
            // User can't access file, if block is hidden or doesn't have block:view capability
            if (($bprecord && !$bprecord->visible) || !has_capability('moodle/block:view', $context)) {
                 send_file_not_found();
            }
        } else {
            $birecord = null;
        }

        $filefunction = $component.'_pluginfile';
        if (function_exists($filefunction)) {
            // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
            $filefunction($course, $birecord, $context, $filearea, $args, $forcedownload, array('preview' => $preview));
        }

        send_file_not_found();

    // ========================================================================================================================
    } else if (strpos($component, '_') === false) {
        // all core subsystems have to be specified above, no more guessing here!
        send_file_not_found();

    } else {
        // try to serve general plugin file in arbitrary context
        $dir = core_component::get_component_directory($component);
        if (!file_exists("$dir/lib.php")) {
            send_file_not_found();
        }
        include_once("$dir/lib.php");

        $filefunction = $component.'_pluginfile';
        if (function_exists($filefunction)) {
            // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
            $filefunction($course, $cm, $context, $filearea, $args, $forcedownload, array('preview' => $preview));
        }

        send_file_not_found();
    }

}
