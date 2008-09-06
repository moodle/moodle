<?php  //$Id$

require_once("$CFG->libdir/file/file_info.php");
require_once("$CFG->libdir/file/file_info_module.php");
require_once("$CFG->libdir/file/file_info_stored.php");
require_once("$CFG->libdir/file/file_info_system.php");
require_once("$CFG->libdir/file/file_info_user.php");
require_once("$CFG->libdir/file/file_info_coursecat.php");
require_once("$CFG->libdir/file/file_info_course.php");
require_once("$CFG->libdir/file/file_info_coursefile.php");

/**
 * Main interface for browsing of file tree (local files, areas, virtual files, etc.).
 */
class file_browser {

    /**
     * Looks up file_info object
     * @param object $context
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return object file_info object or null if not found or access not allowed
     */
    public function get_file_info($context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $USER, $CFG, $DB, $COURSE;

        $fs = get_file_storage();

        if ($context->contextlevel == CONTEXT_SYSTEM) {
            if (is_null($filearea)) {
                return new file_info_system($this);
            }
            //TODO: question files browsing

        } else if ($context->contextlevel == CONTEXT_USER) {
            // access control: only own files
            if ($context->instanceid != $USER->id) {
                return null;
            }

            if (!is_null($filearea) and !in_array($filearea, array('user_private', 'user_draft'))) {
                // file area does not exist, sorry
                return null;
            }

            if (is_null($filearea)) {
                return new file_info_user($this, $context);
            } else {
                if ($filearea == 'user_private') {
                    if (is_null($itemid)) {
                        return new file_info_user($this, $context);
                    }
                    $filepath = is_null($filepath) ? '/' : $filepath;
                    $filename = is_null($filename) ? '.' : $filename;

                    if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                        if ($filepath === '/' and $filename === '.') {
                            $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath, $USER->id);
                        } else {
                            // not found
                            return null;
                        }
                    }
                    $urlbase = $CFG->wwwroot.'/userfile.php';
                    // TODO: localise
                    return new file_info_stored($this, $context, $storedfile, $urlbase, 'Personal files', false, true, true);

                } else if ($filearea == 'user_draft') {
                    if (empty($itemid)) {
                        return new file_info_user($this, $context);
                    }
                    $urlbase = $CFG->wwwroot.'/draftfile.php';
                    if (!$storedfile = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
                        return null;
                    }
                    //something must create the top most directory
                    // TODO: localise
                    return new file_info_stored($this, $context, $storedfile, $urlbase, 'Draft file area', true, true, true);
                }
            }

        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            if (!$category = $DB->get_record('course_categories', array('id'=>$context->instanceid))) {
                return null;
            }

            if (!$category->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                return null;
            }

            if (!is_null($filearea) and !in_array($filearea, array('coursecat_intro'))) {
                // file area does not exist, sorry
                $filearea = null;
            }

            if (is_null($filearea) or is_null($itemid)) {
                return new file_info_coursecat($this, $context, $category);

            } else {
                if ($filearea == 'coursecat_intro') {
                    if (!has_capability('moodle/course:update', $context)) {
                        return null;
                    }

                    $filepath = is_null($filepath) ? '/' : $filepath;
                    $filename = is_null($filename) ? '.' : $filename;

                    $urlbase = $CFG->wwwroot.'/pluginfile.php';
                    if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                        if ($filepath === '/' and $filename === '.') {
                            $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath);
                        } else {
                            // not found
                            return null;
                        }
                    }
                    // TODO: localise
                    return new file_info_stored($this, $context, $storedfile, $urlbase, 'Category introduction files', false, true, true);
                }
            }

        } else if ($context->contextlevel == CONTEXT_COURSE) {
            if ($context->instanceid == $COURSE->id) {
                $course = $COURSE;
            } else if (!$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
                return null;
            }

            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                return null;
            }

            if (!is_null($filearea) and !in_array($filearea, array('course_intro', 'course_content', 'course_backup'))) {
                // file area does not exist, sorry
                $filearea = null;
            }

            $filepath = is_null($filepath) ? '/' : $filepath;
            $filename = is_null($filename) ? '.' : $filename;

            if (is_null($filearea) or is_null($itemid)) {
                return new file_info_course($this, $context, $course);

            } else {
                if ($filearea === 'course_intro') {
                    if (!has_capability('moodle/course:update', $context)) {
                        return null;
                    }

                    $urlbase = $CFG->wwwroot.'/pluginfile.php';
                    if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                        if ($filepath === '/' and $filename === '.') {
                            $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath);
                        } else {
                            // not found
                            return null;
                        }
                    }
                    // TODO: localise
                    return new file_info_stored($this, $context, $storedfile, $urlbase, 'Course introduction files', false, true, true);

                } else if ($filearea == 'course_backup') {
                    if (!has_capability('moodle/site:backup', $context) and !has_capability('moodle/site:restore', $context)) {
                        return null;
                    }

                    $urlbase = $CFG->wwwroot.'/pluginfile.php';
                    if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                        if ($filepath === '/' and $filename === '.') {
                            $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath);
                        } else {
                            // not found
                            return null;
                        }
                    }

                    $downloadable = has_capability('moodle/site:backupdownload', $context);
                    $uploadable   = has_capability('moodle/site:backupupload', $context);
                    // TODO: localise
                    return new file_info_stored($this, $context, $storedfile, $urlbase, 'Backup files', false, $downloadable, $uploadable);

                } else if ($filearea == 'course_content') {
                    if (!has_capability('moodle/course:managefiles', $context)) {
                        return null;
                    }

                    if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                        if ($filepath === '/' and $filename === '.') {
                            $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath);
                        } else {
                            // not found
                            return null;
                        }
                    }

                    return new file_info_coursefile($this, $context, $storedfile);
                }
            }

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            if (!$cm = get_coursemodule_from_id('', $context->instanceid)) {
                return null;
            }

            if ($cm->course == $COURSE->id) {
                $course = $COURSE;
            } else if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
                return null;
            }

            $modinfo = get_fast_modinfo($course);

            if (empty($modinfo->cms[$cm->id]->uservisible)) {
                return null;
            }

            $modname = $modinfo->cms[$cm->id]->modname;

            $libfile = "$CFG->dirroot/mod/$modname/lib.php";
            if (!file_exists($libfile)) {
                return null;
            }
            require_once($libfile);

            $fileinfofunction = $modname.'_get_file_areas';
            if (function_exists($fileinfofunction)) {
                $areas = $fileinfofunction($course, $cm, $context);
            } else {
                return null;
            }
            if (is_null($filearea) or is_null($itemid)) {
                return new file_info_module($this, $course, $cm, $context, $areas);
                
            } else if (!isset($areas[$filearea])) {
                return null; 

            } else if ($filearea === $modname.'_intro') {
                if (!has_capability('moodle/course:managefiles', $context)) {
                    return null;
                }

                $filepath = is_null($filepath) ? '/' : $filepath;
                $filename = is_null($filename) ? '.' : $filename;

                $urlbase = $CFG->wwwroot.'/pluginfile.php';
                if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
                    if ($filepath === '/' and $filename === '.') {
                        $storedfile = $fs->create_directory($context->id, $filearea, 0, $filepath);
                    } else {
                        // not found
                        return null;
                    }
                }
                return new file_info_stored($this, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true);

            } else {
                $fileinfofunction = $modname.'_get_file_info';
                if (function_exists($fileinfofunction)) {
                    return $fileinfofunction($course, $cm, $context, $filearea, $itemid);
                }
            }
        }

        return null;
    }

    /**
     * Returns content of local directory
     */
    public function build_stored_file_children($context, $filearea, $itemid, $filepath, $urlbase, $areavisiblename, $itemidused, $readaccess, $writeaccess) {
        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($context->id, $filearea, $itemid, $filepath, false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $result[] = new file_info_stored($this, $context, $file, $urlbase, $areavisiblename, $itemidused, $readaccess, $writeaccess);
        }

        return $result;
    }

    /**
     * Returns content of coursefiles directory
     */
    public function build_coursefile_children($context, $filepath) {
        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($context->id, 'course_content', 0, $filepath, false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $result[] = new file_info_coursefile($this, $context, $file);
        }

        return $result;
    }

    public function encodepath($urlbase, $path, $forcedownload=false, $https=false) {
        global $CFG;

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

}
