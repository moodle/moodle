<?php

/**
 * uploadlib.php - This class handles all aspects of fileuploading
 *
 * @author ?
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

//error_reporting(E_ALL ^ E_NOTICE);
/**
 * This class handles all aspects of fileuploading
 */
class upload_manager {

   /**
    * Array to hold local copies of stuff in $_FILES
    * @var array $files 
    */
    var $files;
   /**
    * Holds all configuration stuff
    * @var array $config 
    */
    var $config;
   /**
    * Keep track of if we're ok
    * (errors for each file are kept in $files['whatever']['uploadlog']
    * @var boolean $status
    */
    var $status;
   /**
    * The course this file has been uploaded for. {@link $COURSE}
    * (for logging and virus notifications)
    * @var course $course 
    */
    var $course;
   /**
    * If we're only getting one file.
    * (for logging and virus notifications)
    * @var string $inputname 
    */
    var $inputname;
   /**
    * If we're given silent=true in the constructor, this gets built 
    * up to hold info about the process.
    * @var string $notify 
    */
    var $notify;

    /**
     * Constructor, sets up configuration stuff so we know how to act.
     *
     * Note: destination not taken as parameter as some modules want to use the insertid in the path and we need to check the other stuff first.
     *
     * @uses $CFG
     * @param string $inputname If this is given the upload manager will only process the file in $_FILES with this name.
     * @param boolean $deleteothers Whether to delete other files in the destination directory (optional, defaults to false)
     * @param boolean $handlecollisions Whether to use {@link handle_filename_collision()} or not. (optional, defaults to false)
     * @param course $course The course the files are being uploaded for (for logging and virus notifications) {@link $COURSE}
     * @param boolean $recoverifmultiple If we come across a virus, or if a file doesn't validate or whatever, do we continue? optional, defaults to true.
     * @param int $modbytes Max bytes for this module - this and $course->maxbytes are used to get the maxbytes from {@link get_max_upload_file_size()}.
     * @param boolean $silent Whether to notify errors or not.
     * @param boolean $allownull Whether we care if there's no file when we've set the input name.
     * @param boolean $allownullmultiple Whether we care if there's no files AT ALL  when we've got multiples. This won't complain if we have file 1 and file 3 but not file 2, only for NO FILES AT ALL.
     */
    function upload_manager($inputname='', $deleteothers=false, $handlecollisions=false, $course=null, $recoverifmultiple=false, $modbytes=0, $silent=false, $allownull=false, $allownullmultiple=true) {
        
        global $CFG, $SITE;

        if (empty($course->id)) {
            $course = $SITE;
        }
        
        $this->config->deleteothers = $deleteothers;
        $this->config->handlecollisions = $handlecollisions;
        $this->config->recoverifmultiple = $recoverifmultiple;
        $this->config->maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, $modbytes);
        $this->config->silent = $silent;
        $this->config->allownull = $allownull;
        $this->files = array();
        $this->status = false; 
        $this->course = $course;
        $this->inputname = $inputname;
        if (empty($this->inputname)) {
            $this->config->allownull = $allownullmultiple;
        }
    }
    
    /** 
     * Gets all entries out of $_FILES and stores them locally in $files and then
     * checks each one against {@link get_max_upload_file_size()} and calls {@link cleanfilename()} 
     * and scans them for viruses etc.
     * @uses $CFG
     * @uses $_FILES
     * @return boolean
     */
    function preprocess_files() {
        global $CFG;

        foreach ($_FILES as $name => $file) {
            $this->status = true; // only set it to true here so that we can check if this function has been called.
            if (empty($this->inputname) || $name == $this->inputname) { // if we have input name, only process if it matches.
                $file['originalname'] = $file['name']; // do this first for the log.
                $this->files[$name] = $file; // put it in first so we can get uploadlog out in print_upload_log.
                $this->files[$name]['uploadlog'] = ''; // initialize error log
                $this->status = $this->validate_file($this->files[$name]); // default to only allowing empty on multiple uploads.
                if (!$this->status && ($this->files[$name]['error'] == 0 || $this->files[$name]['error'] == 4) && ($this->config->allownull || empty($this->inputname))) {
                    // this shouldn't cause everything to stop.. modules should be responsible for knowing which if any are compulsory.
                    continue; 
                }
                if ($this->status && !empty($CFG->runclamonupload)) {
                    $this->status = clam_scan_moodle_file($this->files[$name],$this->course);
                }
                if (!$this->status) {
                    if (!$this->config->recoverifmultiple && count($this->files) > 1) {
                        $a->name = $this->files[$name]['originalname'];
                        $a->problem = $this->files[$name]['uploadlog'];
                        if (!$this->config->silent) {
                            notify(get_string('uploadfailednotrecovering','moodle',$a));
                        }
                        else {
                            $this->notify .= '<br />'. get_string('uploadfailednotrecovering','moodle',$a);
                        }
                        $this->status = false;
                        return false;

                    } else if (count($this->files) == 1) {

                        if (!$this->config->silent and !$this->config->allownull) {
                            notify($this->files[$name]['uploadlog']);
                        } else {
                            $this->notify .= '<br />'. $this->files[$name]['uploadlog'];
                        }
                        $this->status = false;
                        return false;
                    }
                }
                else {
                    $newname = clean_filename($this->files[$name]['name']);
                    if ($newname != $this->files[$name]['name']) {
                        $a->oldname = $this->files[$name]['name'];
                        $a->newname = $newname;
                        $this->files[$name]['uploadlog'] .= get_string('uploadrenamedchars','moodle', $a);
                    }
                    $this->files[$name]['name'] = $newname;
                    $this->files[$name]['clear'] = true; // ok to save.
                    $this->config->somethingtosave = true;
                }
            }
        }
        if (!is_array($_FILES) || count($_FILES) == 0) {
            return $this->config->allownull;
        }
        $this->status = true;
        return true; // if we've got this far it means that we're recovering so we want status to be ok.
    }

    /**
     * Validates a single file entry from _FILES
     *
     * @param object $file The entry from _FILES to validate
     * @return boolean True if ok.
     */
    function validate_file(&$file) {
        if (empty($file)) {
            return false;
        }
        if (!is_uploaded_file($file['tmp_name']) || $file['size'] == 0) {
            $file['uploadlog'] .= "\n".$this->get_file_upload_error($file);
            return false;
        }
        if ($file['size'] > $this->config->maxbytes) {
            $file['uploadlog'] .= "\n". get_string('uploadedfiletoobig', 'moodle', $this->config->maxbytes);
            return false;
        }
        return true;
    }

    /** 
     * Moves all the files to the destination directory.
     *
     * @uses $CFG
     * @uses $USER
     * @param string $destination The destination directory.
     * @return boolean status;
     */
    function save_files($destination) {
        global $CFG, $USER;
        
        if (!$this->status) { // preprocess_files hasn't been run
            $this->preprocess_files();
        }

        // if there are no files, bail before we create an empty directory.
        if (empty($this->config->somethingtosave)) {
            return true;
        }

        $savedsomething = false;

        if ($this->status) {
            if (!(strpos($destination, $CFG->dataroot) === false)) {
                // take it out for giving to make_upload_directory
                $destination = substr($destination, strlen($CFG->dataroot)+1);
            }

            if ($destination{strlen($destination)-1} == '/') { // strip off a trailing / if we have one
                $destination = substr($destination, 0, -1);
            }

            if (!make_upload_directory($destination, true)) { //TODO maybe put this function here instead of moodlelib.php now.
                $this->status = false;
                return false;
            }
            
            $destination = $CFG->dataroot .'/'. $destination; // now add it back in so we have a full path

            $exceptions = array(); //need this later if we're deleting other files.

            foreach (array_keys($this->files) as $i) {

                if (!$this->files[$i]['clear']) {
                    // not ok to save
                    continue;
                }

                if ($this->config->handlecollisions) {
                    $this->handle_filename_collision($destination, $this->files[$i]);
                }
                if (move_uploaded_file($this->files[$i]['tmp_name'], $destination.'/'.$this->files[$i]['name'])) {
                    chmod($destination .'/'. $this->files[$i]['name'], $CFG->directorypermissions);
                    $this->files[$i]['fullpath'] = $destination.'/'.$this->files[$i]['name'];
                    $this->files[$i]['uploadlog'] .= "\n".get_string('uploadedfile');
                    $this->files[$i]['saved'] = true;
                    $exceptions[] = $this->files[$i]['name'];
                    // now add it to the log (this is important so we know who to notify if a virus is found later on)
                    clam_log_upload($this->files[$i]['fullpath'], $this->course);
                    $savedsomething=true;
                }
            }
            if ($savedsomething && $this->config->deleteothers) {
                $this->delete_other_files($destination, $exceptions);
            }
        }
        if (empty($savedsomething)) {
            $this->status = false;
            if ((empty($this->config->allownull) && !empty($this->inputname)) || (empty($this->inputname) && empty($this->config->allownullmultiple))) {
                notify(get_string('uploadnofilefound'));
            }
            return false;
        }
        return $this->status;
    }
    
    /**
     * Wrapper function that calls {@link preprocess_files()} and {@link viruscheck_files()} and then {@link save_files()}
     * Modules that require the insert id in the filepath should not use this and call these functions seperately in the required order.
     * @parameter string $destination Where to save the uploaded files to.
     * @return boolean
     */ 
    function process_file_uploads($destination) {
        if ($this->preprocess_files()) {
            return $this->save_files($destination);
        }
        return false;
    }

    /** 
     * Deletes all the files in a given directory except for the files in $exceptions (full paths)
     *
     * @param string $destination The directory to clean up.
     * @param array $exceptions Full paths of files to KEEP.
     */
    function delete_other_files($destination, $exceptions=null) {
        $deletedsomething = false;
        if ($filestodel = get_directory_list($destination)) {
            foreach ($filestodel as $file) {
                if (!is_array($exceptions) || !in_array($file, $exceptions)) {
                    unlink($destination .'/'. $file);
                    $deletedsomething = true;
                }
            }
        }
        if ($deletedsomething) {
            if (!$this->config->silent) {
                notify(get_string('uploadoldfilesdeleted'));
            }
            else {
                $this->notify .= '<br />'. get_string('uploadoldfilesdeleted');
            }
        }
    }
    
    /**
     * Handles filename collisions - if the desired filename exists it will rename it according to the pattern in $format
     * @param string $destination Destination directory (to check existing files against)
     * @param object $file Passed in by reference. The current file from $files we're processing.
     * @return void - modifies &$file parameter.
     */
    function handle_filename_collision($destination, &$file) {
        if (!file_exists($destination .'/'. $file['name'])) {
            return;
        }

        $parts     = explode('.', $file['name']);
        if (count($parts) > 1) {
            $extension = '.'.array_pop($parts);
            $name      = implode('.', $parts);
        } else {
            $extension = '';
            $name      = $file['name'];
        }

        $current = 0;
        if (preg_match('/^(.*)_(\d*)$/s', $name, $matches)) {
            $name    = $matches[1];
            $current = (int)$matches[2];
        }
        $i = $current + 1;

        while (!$this->check_before_renaming($destination, $name.'_'.$i.$extension, $file)) {
            $i++;
        }
        $a = new object();
        $a->oldname = $file['name'];
        $file['name'] = $name.'_'.$i.$extension;
        $a->newname = $file['name'];
        $file['uploadlog'] .= "\n". get_string('uploadrenamedcollision','moodle', $a);
    }
    
    /**
     * This function checks a potential filename against what's on the filesystem already and what's been saved already.
     * @param string $destination Destination directory (to check existing files against)
     * @param string $nametocheck The filename to be compared.
     * @param object $file The current file from $files we're processing.
     * return boolean
     */
    function check_before_renaming($destination, $nametocheck, $file) {
        if (!file_exists($destination .'/'. $nametocheck)) {
            return true;
        }
        if ($this->config->deleteothers) {
            foreach ($this->files as $tocheck) {
                // if we're deleting files anyway, it's not THIS file and we care about it and it has the same name and has already been saved..
                if ($file['tmp_name'] != $tocheck['tmp_name'] && $tocheck['clear'] && $nametocheck == $tocheck['name'] && $tocheck['saved']) {
                    $collision = true;
                }
            }
            if (!$collision) {
                return true;
            }
        }
        return false;
    }

    /**
     * ?
     *
     * @param object $file Passed in by reference. The current file from $files we're processing.
     * @return string
     * @todo Finish documenting this function
     */
    function get_file_upload_error(&$file) {
        
        switch ($file['error']) {
        case 0: // UPLOAD_ERR_OK
            if ($file['size'] > 0) {
                $errmessage = get_string('uploadproblem', $file['name']);
            } else {
                $errmessage = get_string('uploadnofilefound'); /// probably a dud file name
            }
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
            $errmessage = get_string('uploadproblem', $file['name']);
        }
        return $errmessage;
    }
    
    /**
     * prints a log of everything that happened (of interest) to each file in _FILES
     * @param $return - optional, defaults to false (log is echoed)
     */
    function print_upload_log($return=false,$skipemptyifmultiple=false) {
        $str = '';
        foreach (array_keys($this->files) as $i => $key) {
            if (count($this->files) > 1 && !empty($skipemptyifmultiple) && $this->files[$key]['error'] == 4) {
                continue;
            }
            $str .= '<strong>'. get_string('uploadfilelog', 'moodle', $i+1) .' '
                .((!empty($this->files[$key]['originalname'])) ? '('.$this->files[$key]['originalname'].')' : '')
                .'</strong> :'. nl2br($this->files[$key]['uploadlog']) .'<br />';
        }
        if ($return) {
            return $str;
        }
        echo $str;
    }

    /**
     * If we're only handling one file (if inputname was given in the constructor) this will return the (possibly changed) filename of the file.
     @return boolean
     */
    function get_new_filename() {
        if (!empty($this->inputname) and count($this->files) == 1 and $this->files[$this->inputname]['error'] != 4) {
            return $this->files[$this->inputname]['name'];
        }
        return false;
    }

    /** 
     * If we're only handling one file (if input name was given in the constructor) this will return the full path to the saved file.
     * @return boolean
     */
    function get_new_filepath() {
        if (!empty($this->inputname) and count($this->files) == 1 and $this->files[$this->inputname]['error'] != 4) {
            return $this->files[$this->inputname]['fullpath'];
        }
        return false;
    }

    /** 
     * If we're only handling one file (if inputname was given in the constructor) this will return the ORIGINAL filename of the file.
     * @return boolean
     */
    function get_original_filename() {
        if (!empty($this->inputname) and count($this->files) == 1 and $this->files[$this->inputname]['error'] != 4) {
            return $this->files[$this->inputname]['originalname'];
        }
        return false;
    }

    /** 
     * This function returns any errors wrapped up in red.
     * @return string
     */
    function get_errors() {
        if (!empty($this->notify)) {
        return '<p class="notifyproblem">'. $this->notify .'</p>';
        } else {
            return null;
        }
    }
}

/**************************************************************************************
THESE FUNCTIONS ARE OUTSIDE THE CLASS BECAUSE THEY NEED TO BE CALLED FROM OTHER PLACES.
FOR EXAMPLE CLAM_HANDLE_INFECTED_FILE AND CLAM_REPLACE_INFECTED_FILE USED FROM CRON
UPLOAD_PRINT_FORM_FRAGMENT DOESN'T REALLY BELONG IN THE CLASS BUT CERTAINLY IN THIS FILE
***************************************************************************************/


/**
 * This function prints out a number of upload form elements.
 *
 * @param int $numfiles The number of elements required (optional, defaults to 1)
 * @param array $names Array of element names to use (optional, defaults to FILE_n)
 * @param array $descriptions Array of strings to be printed out before each file bit.
 * @param boolean $uselabels -Whether to output text fields for file descriptions or not (optional, defaults to false)
 * @param array $labelnames Array of element names to use for labels (optional, defaults to LABEL_n)
 * @param int $coursebytes $coursebytes and $maxbytes are used to calculate upload max size ( using {@link get_max_upload_file_size})
 * @param int $modbytes $coursebytes and $maxbytes are used to calculate upload max size ( using {@link get_max_upload_file_size})
 * @param boolean $return -Whether to return the string (defaults to false - string is echoed)
 * @return string Form returned as string if $return is true
 */ 
function upload_print_form_fragment($numfiles=1, $names=null, $descriptions=null, $uselabels=false, $labelnames=null, $coursebytes=0, $modbytes=0, $return=false) {
    global $CFG;
    $maxbytes = get_max_upload_file_size($CFG->maxbytes, $coursebytes, $modbytes);
    $str = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxbytes .'" />'."\n";
    for ($i = 0; $i < $numfiles; $i++) {
        if (is_array($descriptions) && !empty($descriptions[$i])) {
            $str .= '<strong>'. $descriptions[$i] .'</strong><br />';
        }
        $name = ((is_array($names) && !empty($names[$i])) ? $names[$i] : 'FILE_'.$i);
        $str .= '<input type="file" size="50" name="'. $name .'" alt="'. $name .'" /><br />'."\n";
        if ($uselabels) {
            $lname = ((is_array($labelnames) && !empty($labelnames[$i])) ? $labelnames[$i] : 'LABEL_'.$i);
            $str .= get_string('uploadlabel').' <input type="text" size="50" name="'. $lname .'" alt="'. $lname
                .'" /><br /><br />'."\n";
        }
    }
    if ($return) {
        return $str;
    }
    else {
        echo $str;
    }
}


/**
 * Deals with an infected file - either moves it to a quarantinedir 
 * (specified in CFG->quarantinedir) or deletes it.
 *
 * If moving it fails, it deletes it.
 *
 *@uses $CFG
 * @uses $USER
 * @param string $file Full path to the file
 * @param int $userid If not used, defaults to $USER->id (there in case called from cron)
 * @param boolean $basiconly Admin level reporting or user level reporting.
 * @return string Details of what the function did.
 */
function clam_handle_infected_file($file, $userid=0, $basiconly=false) {
    
    global $CFG, $USER;
    if ($USER && !$userid) {
        $userid = $USER->id;
    }
    $delete = true;
    if (file_exists($CFG->quarantinedir) && is_dir($CFG->quarantinedir) && is_writable($CFG->quarantinedir)) {
        $now = date('YmdHis');
        if (rename($file, $CFG->quarantinedir .'/'. $now .'-user-'. $userid .'-infected')) { 
            $delete = false;
            clam_log_infected($file, $CFG->quarantinedir.'/'. $now .'-user-'. $userid .'-infected', $userid);
            if ($basiconly) {
                $notice .= "\n". get_string('clammovedfilebasic');
            }
            else {
                $notice .= "\n". get_string('clammovedfile', 'moodle', $CFG->quarantinedir.'/'. $now .'-user-'. $userid .'-infected');
            }
        }
        else {
            if ($basiconly) {
                $notice .= "\n". get_string('clamdeletedfile');
            }
            else {
                $notice .= "\n". get_string('clamquarantinedirfailed', 'moodle', $CFG->quarantinedir);
            }
        }
    }
    else {
        if ($basiconly) {
            $notice .= "\n". get_string('clamdeletedfile');
        }
        else {
            $notice .= "\n". get_string('clamquarantinedirfailed', 'moodle', $CFG->quarantinedir);
        }
    }
    if ($delete) {
        if (unlink($file)) {
            clam_log_infected($file, '', $userid);
            $notice .= "\n". get_string('clamdeletedfile');
        }
        else {
            if ($basiconly) {
                // still tell the user the file has been deleted. this is only for admins.
                $notice .= "\n". get_string('clamdeletedfile');
            }
            else {
                $notice .= "\n". get_string('clamdeletedfilefailed');
            }
        }
    }
    return $notice;
}

/**
 * Replaces the given file with a string.
 *
 * The replacement string is used to notify that the original file had a virus
 * This is to avoid missing files but could result in the wrong content-type.
 * @param string $file Full path to the file.
 * @return boolean
 */
function clam_replace_infected_file($file) {
    $newcontents = get_string('virusplaceholder');
    if (!$f = fopen($file, 'w')) {
        return false;
    }
    if (!fwrite($f, $newcontents)) {
        return false;
    }
    return true;
}


/**
 * If $CFG->runclamonupload is set, we scan a given file. (called from {@link preprocess_files()})
 *
 * This function will add on a uploadlog index in $file.
 * @param mixed $file The file to scan from $files. or an absolute path to a file.
 * @param course $course {@link $COURSE}
 * @return int 1 if good, 0 if something goes wrong (opposite from actual error code from clam)
 */ 
function clam_scan_moodle_file(&$file, $course) {
    global $CFG, $USER;

    if (is_array($file) && is_uploaded_file($file['tmp_name'])) { // it's from $_FILES
        $appendlog = true; 
        $fullpath = $file['tmp_name'];
    }
    else if (file_exists($file)) { // it's a path to somewhere on the filesystem!
        $fullpath = $file;
    }
    else {
        return false; // erm, what is this supposed to be then, huh?
    }

    $CFG->pathtoclam = trim($CFG->pathtoclam);

    if (!$CFG->pathtoclam || !file_exists($CFG->pathtoclam) || !is_executable($CFG->pathtoclam)) {
        $newreturn = 1;
        $notice = get_string('clamlost', 'moodle', $CFG->pathtoclam);
        if ($CFG->clamfailureonupload == 'actlikevirus') {
            $notice .= "\n". get_string('clamlostandactinglikevirus');
            $notice .= "\n". clam_handle_infected_file($fullpath);
            $newreturn = false; 
        }
        clam_mail_admins($notice);
        if ($appendlog) {
            $file['uploadlog'] .= "\n". get_string('clambroken');
            $file['clam'] = 1;
        }
        return $newreturn; // return 1 if we're allowing clam failures
    }
    
    $cmd = $CFG->pathtoclam .' '. $fullpath ." 2>&1";
    
    // before we do anything we need to change perms so that clamscan can read the file (clamdscan won't work otherwise)
    chmod($fullpath,0644);
    
    exec($cmd, $output, $return);
    
    
    switch ($return) {
    case 0: // glee! we're ok.
        return 1; // translate clam return code into reasonable return code consistent with everything else.
    case 1:  // bad wicked evil, we have a virus.
        if (!empty($course)) {
            $info->course = $course->fullname;
        }
        else {
            $info->course = 'No course';
        }
        $info->user = fullname($USER);
        $notice = get_string('virusfound', 'moodle', $info);
        $notice .= "\n\n". implode("\n", $output);
        $notice .= "\n\n". clam_handle_infected_file($fullpath); 
        clam_mail_admins($notice);
        if ($appendlog) {
            $info->filename = $file['originalname'];
            $file['uploadlog'] .= "\n". get_string('virusfounduser', 'moodle', $info);
            $file['virus'] = 1;
        }
        return false; // in this case, 0 means bad.
    default: 
        // error - clam failed to run or something went wrong
        $notice .= get_string('clamfailed', 'moodle', get_clam_error_code($return));
        $notice .= "\n\n". implode("\n", $output);
        $newreturn = true;
        if ($CFG->clamfailureonupload == 'actlikevirus') {
            $notice .= "\n". clam_handle_infected_file($fullpath);
            $newreturn = false;
        }
        clam_mail_admins($notice);
        if ($appendlog) {
            $file['uploadlog'] .= "\n". get_string('clambroken');
            $file['clam'] = 1;
        }
        return $newreturn; // return 1 if we're allowing failures.
    }
}

/**
 * Emails admins about a clam outcome
 *
 * @param string $notice The body of the email to be sent.
 */
function clam_mail_admins($notice) {
    
    $site = get_site();
        
    $subject = get_string('clamemailsubject', 'moodle', format_string($site->fullname));
    $admins = get_admins();
    foreach ($admins as $admin) {
        email_to_user($admin, get_admin(), $subject, $notice);
    }
}


/**
 * Returns the string equivalent of a numeric clam error code
 *
 * @param int $returncode The numeric error code in question.
 * return string The definition of the error code
 */
function get_clam_error_code($returncode) {
    $returncodes = array();
    $returncodes[0] = 'No virus found.';
    $returncodes[1] = 'Virus(es) found.';
    $returncodes[2] = ' An error occured'; // specific to clamdscan
    // all after here are specific to clamscan
    $returncodes[40] = 'Unknown option passed.';
    $returncodes[50] = 'Database initialization error.';
    $returncodes[52] = 'Not supported file type.';
    $returncodes[53] = 'Can\'t open directory.';
    $returncodes[54] = 'Can\'t open file. (ofm)';
    $returncodes[55] = 'Error reading file. (ofm)';
    $returncodes[56] = 'Can\'t stat input file / directory.';
    $returncodes[57] = 'Can\'t get absolute path name of current working directory.';
    $returncodes[58] = 'I/O error, please check your filesystem.';
    $returncodes[59] = 'Can\'t get information about current user from /etc/passwd.';
    $returncodes[60] = 'Can\'t get information about user \'clamav\' (default name) from /etc/passwd.';
    $returncodes[61] = 'Can\'t fork.'; 
    $returncodes[63] = 'Can\'t create temporary files/directories (check permissions).';
    $returncodes[64] = 'Can\'t write to temporary directory (please specify another one).';
    $returncodes[70] = 'Can\'t allocate and clear memory (calloc).';
    $returncodes[71] = 'Can\'t allocate memory (malloc).';
    if ($returncodes[$returncode])
       return $returncodes[$returncode];
    return get_string('clamunknownerror');

}

/**
 * Adds a file upload to the log table so that clam can resolve the filename to the user later if necessary
 *
 * @uses $CFG
 * @uses $USER
 * @param string $newfilepath ?
 * @param course $course {@link $COURSE}
 * @param boolean $nourl ?
 * @todo Finish documenting this function
 */
function clam_log_upload($newfilepath, $course=null, $nourl=false) {
    global $CFG, $USER;
    // get rid of any double // that might have appeared
    $newfilepath = preg_replace('/\/\//', '/', $newfilepath);
    if (strpos($newfilepath, $CFG->dataroot) === false) {
        $newfilepath = $CFG->dataroot .'/'. $newfilepath;
    }
    $courseid = 0;
    if ($course) {
        $courseid = $course->id;
    }
    add_to_log($courseid, 'upload', 'upload', ((!$nourl) ? substr($_SERVER['HTTP_REFERER'], 0, 100) : ''), $newfilepath);
}

/**
 * This function logs to error_log and to the log table that an infected file has been found and what's happened to it.
 *
 * @param string $oldfilepath Full path to the infected file before it was moved.
 * @param string $newfilepath Full path to the infected file since it was moved to the quarantine directory (if the file was deleted, leave empty).
 * @param int $userid The user id of the user who uploaded the file.
 */
function clam_log_infected($oldfilepath='', $newfilepath='', $userid=0) {

    add_to_log(0, 'upload', 'infected', $_SERVER['HTTP_REFERER'], $oldfilepath, 0, $userid);
    
    $user = get_record('user', 'id', $userid);
    
    $errorstr = 'Clam AV has found a file that is infected with a virus. It was uploaded by '
        . ((empty($user)) ? ' an unknown user ' : fullname($user))
        . ((empty($oldfilepath)) ? '. The infected file was caught on upload ('.$oldfilepath.')' 
           : '. The original file path of the infected file was '. $oldfilepath)
        . ((empty($newfilepath)) ? '. The file has been deleted ' : '. The file has been moved to a quarantine directory and the new path is '. $newfilepath);

    error_log($errorstr);
}


/**
 * Some of the modules allow moving attachments (glossary), in which case we need to hunt down an original log and change the path.
 *
 * @uses $CFG
 * @param string $oldpath The old path to the file (should be in the log)
 * @param string $newpath The new path to the file 
 * @param boolean $update If true this function will overwrite old record (used for forum moving etc).
 */
function clam_change_log($oldpath, $newpath, $update=true) {
    global $CFG;
    
    if (!$record = get_record('log', 'info', $oldpath, 'module', 'upload')) {
        return false;
    }
    $record->info = $newpath;
    if ($update) {
        update_record('log', $record);
    }
    else {
        unset($record->id);
        insert_record('log', $record);
    }
}
?>
