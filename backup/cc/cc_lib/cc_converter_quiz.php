<?php

require_once 'cc_converters.php';
require_once 'cc_general.php';
require_once 'cc_asssesment.php';

class cc_converter_quiz extends cc_converter {

    public function __construct(cc_i_item &$item, cc_i_manifest &$manifest, $rootpath, $path){
        $this->cc_type     = cc_version11::assessment;
        $this->defaultfile = 'quiz.xml';
        $this->defaultname = assesment11_resurce_file::deafultname;
        parent::__construct($item, $manifest, $rootpath, $path);
    }

    public function convert($outdir) {
        $rt = new assesment11_resurce_file();
        $title = $this->doc->nodeValue('/activity/quiz/name');
        $rt->set_title($title);

        //metadata
        $metadata = new cc_assesment_metadata();
        $rt->set_metadata($metadata);
        $metadata->enable_feedback();
        $metadata->enable_hints();
        $metadata->enable_solutions();
        //attempts
        $max_attempts = (int)$this->doc->nodeValue('/activity/quiz/attempts_number');
        if ($max_attempts > 0) {
            //qti does not support number of specific attempts bigger than 5 (??)
            if ($max_attempts > 5) {
                $max_attempts = cc_qti_values::unlimited;
            }
            $metadata->set_maxattempts($max_attempts);
        }
        //timelimit must be converted into minutes
        $timelimit = (int)floor((int)$this->doc->nodeValue('/activity/quiz/timelimit')/60);
        if ($timelimit > 0) {
            $metadata->set_timelimit($timelimit);
            $metadata->enable_latesubmissions(false);
        }

        $contextid = $this->doc->nodeValue('/activity/@contextid');
        $result = cc_helpers::process_linked_files( $this->doc->nodeValue('/activity/quiz/intro'),
                                                    $this->manifest,
                                                    $this->rootpath,
                                                    $contextid,
                                                    $outdir);
        cc_assesment_helper::add_assesment_description($rt, $result[0], cc_qti_values::htmltype);

        //section
        $section = new cc_assesment_section();
        $rt->set_section($section);

        //Process the actual questions
        $ndeps = cc_assesment_helper::process_questions($this->doc,
                                                        $this->manifest,
                                                        $section,
                                                        $this->rootpath,
                                                        $contextid,
                                                        $outdir);
        //store any additional dependencies
        $deps = array_merge($result[1], $ndeps);

        //store everything
        $this->store($rt, $outdir, $title, $deps);
        return true;
    }
}
