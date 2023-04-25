<?php

use core_course\external\course_summary_exporter;
class local_qubitscourse_renderer extends plugin_renderer_base {

    /**
     * A cache of strings
     * @var stdClass
     */
    protected $strings;


    public function __construct(moodle_page $page, $target) {
        $this->strings = new stdClass;
        $courseid = $page->course->id;
        parent::__construct($page, $target);
    }

    public function tenant_courses($data, $filters){
        global $OUTPUT;
        $courseids = empty($data->course_id) ? array() : $data->course_id;
        $siteid = optional_param('siteid', 0, PARAM_INT);
        $asearch = array("recursive" => true, "search" => $filters["search"]);
        $aoptions = $this->process_filters($filters);
        $allcourses = core_course_category::get(0)->search_courses($asearch, $aoptions);
        $tenantcourses = array();
        foreach($allcourses as $onecourse){
            $onecourseid = $onecourse->id;
            if(in_array($onecourseid, $courseids) !== false){
                $courseimage = course_summary_exporter::get_course_image($onecourse);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_generated_image_for_id($onecourse->id);
                }
                $course_context = context_course::instance($onecourse->id);
                $tenantcourses[] = array(
                    "id" => $onecourse->id,
                    "fullname" => $onecourse->fullname,
                    "shortname" => $onecourse->shortname,
                    'viewlink' => new moodle_url("/course/view.php", array("id" => $onecourse->id )),
                    "courseimage" => $courseimage,
                    "contextid" => $course_context->id,
                    "siteid" =>  $siteid
                );
            }
        }
        $totalcount = count($tenantcourses);
        $filters["siteid"] = $data->site_id;
        $url = new moodle_url($CFG->wwwroot . '/local/qubitscourse/index.php', $filters);
        $pagebar = $OUTPUT->paging_bar($totalcount, $filters["page"], $filters["perpage"], $url);
        $limit = $filters["perpage"];
        $offset = $filters["page"] * $filters["perpage"];
        $curpagetenantcourses = array_slice($tenantcourses, $offset, $limit);

        $templatecontext = [
            "tenantcourses" => $curpagetenantcourses,
            "pagebar" => $pagebar,
            "assigncourseslink" => new moodle_url("/local/qubitscourse/assigncourses.php", array("siteid" => $data->site_id)),
            "siteid" => $data->site_id,
            "sortcolumn" => $filters["sortcolumn"],
            "sortdir" => $filters["sortdir"],
            "search" => $filters["search"],
        ];
        //echo "<pre>"; print_r($curpagetenantcourses); echo "</pre>"; exit;
        return $this->output->render_from_template('local_qubitscourse/tenant_courses', $templatecontext);
    }

    private function process_filters($filters){
        $aoptions = array();
        $sortdir = ($filters["sortdir"]=="desc") ? -1 : 1 ;
        switch ($filters["sortcolumn"]) {
            case "coursefullname":
                $aoptions["sort"] = array("fullname" => $sortdir);
                break;
            case "courseshortname":
                $aoptions["sort"] = array("shortname" => $sortdir);
                break;
            default:
                $aoptions["sort"] = array();
                break;
        }
        //$aoptions["limit"] = $filters["perpage"];
        //$aoptions["offset"] = $filters["page"] * $filters["perpage"];
        return $aoptions;
    }

}