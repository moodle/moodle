<?php

class convert_structure_parser_processor extends grouped_parser_processor {
    /**
     * @var plan_converter
     */
    protected $converter;

    public function __construct(plan_converter $converter) {
        $this->converter = $converter;
        parent::__construct();
    }

    /**
     * Provide NULL and legacy file.php uses decoding
     */
    public function process_cdata($cdata) {
        global $CFG;
        if ($cdata === '$@NULL@$') {  // Some cases we know we can skip complete processing
            return null;
        } else if ($cdata === '') {
            return '';
        } else if (is_numeric($cdata)) {
            return $cdata;
        } else if (strlen($cdata) < 32) { // Impossible to have one link in 32cc
            return $cdata;                // (http://10.0.0.1/file.php/1/1.jpg, http://10.0.0.1/mod/url/view.php?id=)
        } else if (strpos($cdata, '$@FILEPHP@$') === false) { // No $@FILEPHP@$, nothing to convert
            return $cdata;
        }
        // Decode file.php calls
        $search = array ("$@FILEPHP@$");
        $replace = array(get_file_url($this->courseid));
        $result = str_replace($search, $replace, $cdata);
        // Now $@SLASH@$ and $@FORCEDOWNLOAD@$ MDL-18799
        $search = array('$@SLASH@$', '$@FORCEDOWNLOAD@$');
        if ($CFG->slasharguments) {
            $replace = array('/', '?forcedownload=1');
        } else {
            $replace = array('%2F', '&amp;forcedownload=1');
        }
        return str_replace($search, $replace, $result);
    }

    /**
     * Override this method so we'll be able to skip
     * dispatching some well-known chunks, like the
     * ones being 100% part of subplugins stuff. Useful
     * for allowing development without having all the
     * possible restore subplugins defined
     */
    protected function postprocess_chunk($data) {

        // Iterate over all the data tags, if any of them is
        // not 'subplugin_XXXX' or has value, then it's a valid chunk,
        // pass it to standard (parent) processing of chunks.
        foreach ($data['tags'] as $key => $value) {
            if (trim($value) !== '' || strpos($key, 'subplugin_') !== 0) {
                parent::postprocess_chunk($data);
                return;
            }
        }
        // Arrived here, all the tags correspond to sublplugins and are empty,
        // skip the chunk, and debug_developer notice
        $this->chunks--; // not counted
        debugging('Missing support on restore for ' . clean_param($data['path'], PARAM_PATH) .
                  ' subplugin (' . implode(', ', array_keys($data['tags'])) .')', DEBUG_DEVELOPER);
    }

    protected function dispatch_chunk($data) {
        $this->converter->process($data);
    }
}