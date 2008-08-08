<?php  //$Id$

/**
 * Basic file related exception class
 */
class file_exception extends moodle_exception {
    function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * Table does not exist problem exception
 */
class stored_file_creation_exception extends file_exception {
    function __construct($contextid, $filearea, $itemid, $filepath, $filename, $debuginfo=null) {
        $a = new object();
        $a->contextid = $contextid;
        $a->filearea  = $filearea;
        $a->itemid    = $itemid;
        $a->filepath  = $filepath;
        $a->filename  = $filename;
        parent::__construct('storedfilenotcreated', $a, $debuginfo);
    }
}

/**
 * Table does not exist problem exception
 */
class file_access_exception extends file_exception {
    function __construct($debuginfo=null) {
        parent::__construct('nopermissions', NULL, $debuginfo);
    }
}

/**
 * Hash file content problem
 */
class file_pool_content_exception extends file_exception {
    function __construct($contenthash, $debuginfo=null) {
        parent::__construct('hashpoolproblem', $contenthash, $debuginfo);
    }
}
