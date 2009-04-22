<?php  //$Id$

require_once("$CFG->libdir/file/file_info.php");
require_once("$CFG->libdir/file/file_info_module.php");
require_once("$CFG->libdir/file/file_info_stored.php");
require_once("$CFG->libdir/file/file_info_system.php");
require_once("$CFG->libdir/file/file_info_user.php");
require_once("$CFG->libdir/file/file_info_coursecat.php");
require_once("$CFG->libdir/file/file_info_course.php");
require_once("$CFG->libdir/file/file_info_coursesection.php");
require_once("$CFG->libdir/file/file_info_coursefile.php");
require_once("$CFG->libdir/file/virtual_root_file.php");

/**
 * This class provides the main entry point for other code wishing to get
 * information about files.
 *
 * The whole file storage for a Moodle site can be seen as a huge virtual tree.
 * The spine of the tree is the tree of contexts (system, course-categories,
 * courses, modules, also users). Then, within each context, there may be any number of
 * file areas, and a file area contains folders and files. The various file_info
 * subclasses return info about the things in this tree. They should be obtained
 * from an instance of this class.
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
        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                return $this->get_file_info_system($context, $filearea, $itemid, $filepath, $filename);
            case CONTEXT_USER:
                return $this->get_file_info_user($context, $filearea, $itemid, $filepath, $filename);
            case CONTEXT_COURSECAT:
                return $this->get_file_info_coursecat($context, $filearea, $itemid, $filepath, $filename);
            case CONTEXT_COURSE:
                return $this->get_file_info_course($context, $filearea, $itemid, $filepath, $filename);
            case CONTEXT_MODULE:
                return $this->get_file_info_module($context, $filearea, $itemid, $filepath, $filename);
        }

        return null;
    }

    /**
     * Returns content of local directory
     */
    public function build_stored_file_children($context, $filearea, $itemid, $filepath, $urlbase, $topvisiblename, $itemidused, $readaccess, $writeaccess) {
        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($context->id, $filearea, $itemid, $filepath, false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $result[] = new file_info_stored($this, $context, $file, $urlbase, $topvisiblename, $itemidused, $readaccess, $writeaccess, false);
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

    /**
     * Returns info about the files at System context
     * @param object $context
     * @param string $filearea
     * @return file_info_system
     */
    private function get_file_info_system($context, $filearea=null) {
        if (is_null($filearea)) {
            return new file_info_system($this);
        }
        //TODO: question files browsing

        return null;
    }

    /**
     * Returns info about the files at User context
     * @param object $context
     * @param string $filearea
     * @return file_info_system
     */
    private function get_file_info_user($context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $USER, $DB;
        if ($context->instanceid == $USER->id) {
            $user = $USER;
        } else {
            $user = $DB->get_record('user', array('id'=>$context->instanceid));
        }

        if (isguestuser($user) or empty($user->id)) {
            // no guests or not logged in users here
            return null;
        }

        if ($user->deleted) {
            return null;
        }

        if (is_null($filearea)) {
            // access control: list areas only for myself
            if ($context->instanceid != $USER->id) {
                return null;
            }

            return new file_info_user($this, $context);

        } else {
            $methodname = "get_file_info_$filearea";
            if (method_exists($this, $methodname)) {
                return $this->$methodname($user, $context, $filearea, $itemid, $filepath, $filename);
            }
        }

        return null;
    }

    private function get_file_info_user_private($user, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $USER, $CFG;

        $fs = get_file_storage();

        // access control: only my files for now, nobody else
        if ($context->instanceid != $USER->id) {
            return null;
        }

        if (is_null($itemid)) {
            return new file_info_user($this, $context);
        }
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/userfile.php';
        return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areauserpersonal', 'repository'), false, true, true, false);
    }

    private function get_file_info_user_profile($user, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $USER, $CFG;

        $fs = get_file_storage();

        if (is_null($itemid)) {
            return new file_info_user($this, $context);
        }

        // access controll here must match user edit forms
        if ($user->id == $USER->id) {
             if (!has_capability('moodle/user:editownprofile', get_context_instance(CONTEXT_SYSTEM))) {
                return null;
             }
        } else {
            if (!has_capability('moodle/user:editprofile', $context) and !has_capability('moodle/user:update', $context)) {
                return null;
            }
        }

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/userfile.php';
        return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areauserprofile', 'repository'), false, true, true, false);
    }

    private function get_file_info_user_draft($user, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $USER, $CFG;

        $fs = get_file_storage();

        // access control: only my files
        if ($context->instanceid != $USER->id) {
            return null;
        }

        if (empty($itemid)) {
            // do not browse itemids - you most know the draftid to see what is there
            return null;
        }
        $urlbase = $CFG->wwwroot.'/draftfile.php';

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, $itemid);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areauserdraft', 'repository'), true, true, true, true);
    }

    private function get_file_info_coursecat($context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $DB, $CFG;

        $fs = get_file_storage();

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
                        $storedfile = new virtual_root_file($context->id, $filearea, 0);
                    } else {
                        // not found
                        return null;
                    }
                }
                return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areacategoryintro', 'repository'), false, true, true, false);
            }
        }

        return null;
    }

    private function get_file_info_course($context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $DB, $COURSE;

        if ($context->instanceid == $COURSE->id) {
            $course = $COURSE;
        } else if (!$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
            return null;
        }

        if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
            return null;
        }

        if (!is_null($filearea) and !in_array($filearea, array('course_intro', 'course_content', 'course_section', 'course_backup'))) {
            // file area does not exist, sorry
            $filearea = null;
        }

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (is_null($filearea)) {
            return new file_info_course($this, $context, $course);

        } else {
            $methodname = "get_file_info_$filearea";
            if (method_exists($this, $methodname)) {
                return $this->$methodname($course, $context, $filearea, $itemid, $filepath, $filename);
            }
        }

        return null;
    }

    private function get_file_info_course_intro($course, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $CFG;

        $fs = get_file_storage();

        if (!has_capability('moodle/course:update', $context)) {
            return null;
        }
        if (is_null($itemid)) {
            return new file_info_course($this, $context, $course);
        }

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areacourseintro', 'repository'), false, true, true, false);

    }

    private function get_file_info_course_section($course, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $CFG, $DB;

        $fs = get_file_storage();

        if (!has_capability('moodle/course:update', $context)) {
            return null;
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';

        if (empty($itemid)) {
            // list all sections
            return new file_info_coursesection($this, $context, $course);
        }

        if (!$section = $DB->get_record('course_sections', array('course'=>$course->id, 'id'=>$itemid))) {
            return null; // does not exist
        }

        if (!$storedfile = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, $itemid);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($this, $context, $storedfile, $urlbase, $section->section, true, true, true, false);

    }

    private function get_file_info_course_backup($course, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $CFG;

        $fs = get_file_storage();

        if (!has_capability('moodle/site:backup', $context) and !has_capability('moodle/site:restore', $context)) {
            return null;
        }
        if (is_null($itemid)) {
            return new file_info_course($this, $context, $course);
        }

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }

        $downloadable = has_capability('moodle/site:backupdownload', $context);
        $uploadable   = has_capability('moodle/site:backupupload', $context);
        return new file_info_stored($this, $context, $storedfile, $urlbase, get_string('areabackup', 'repository'), false, $downloadable, $uploadable, false);

    }

    private function get_file_info_course_content($course, $context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        $fs = get_file_storage();

        if (!has_capability('moodle/course:managefiles', $context)) {
            return null;
        }
        if (is_null($itemid)) {
            return new file_info_course($this, $context, $course);
        }

        if (!$storedfile = $fs->get_file($context->id, $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, $filearea, 0);
            } else {
                // not found
                return null;
            }
        }

        return new file_info_coursefile($this, $context, $storedfile);
    }

    private function get_file_info_module($context, $filearea=null, $itemid=null, $filepath=null, $filename=null) {
        global $COURSE, $DB, $CFG;

        $fs = get_file_storage();

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
            $areas = array();
        }
        if (!isset($areas[$modname.'_intro'])
          and plugin_supports('mod', $modname, FEATURE_MOD_INTRO, true)
          and has_capability('moodle/course:managefiles', $context)) {
            $areas[$modname.'_intro'] = get_string('moduleintro');
        }
        if (empty($areas)) {
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
                    $storedfile = new virtual_root_file($context->id, $filearea, 0);
                } else {
                    // not found
                    return null;
                }
            }
            return new file_info_stored($this, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true, false);

        } else {
            $fileinfofunction = $modname.'_get_file_info';
            if (function_exists($fileinfofunction)) {
                return $fileinfofunction($this, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename);
            }
        }

        return null;
    }
}
