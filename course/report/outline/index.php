<?php

// Display user activity reports for a course (totals)

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/course/lib.php');

    $id = required_param('id',PARAM_INT);       // course id

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourseid');
    }

    $PAGE->set_url('/course/report/outline/index.php', array('id'=>$id));
    $PAGE->set_pagelayout('report');

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('coursereport/outline:view', $context);

    add_to_log($course->id, 'course', 'report outline', "report/outline/index.php?id=$course->id", $course->id);

    $showlastaccess = true;
    $hiddenfields = explode(',', $CFG->hiddenuserfields);

    if (array_search('lastaccess', $hiddenfields) and !has_capability('moodle/user:viewhiddendetails', $context)) {
        $showlastaccess = false;
    }

    $stractivityreport = get_string('activityreport');
    $stractivity       = get_string('activity');
    $strlast           = get_string('lastaccess');
    $strreports        = get_string('reports');
    $strviews          = get_string('views');
    $strrelatedblogentries = get_string('relatedblogentries', 'blog');

    $PAGE->set_title($course->shortname .': '. $stractivityreport);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($course->fullname));

    if (!$logstart = $DB->get_field_sql("SELECT MIN(time) FROM {log}")) {
        print_error('logfilenotavailable');
    }

    echo $OUTPUT->container(get_string('computedfromlogs', 'admin', userdate($logstart)), 'loginfo');

    $outlinetable = new html_table();
    $outlinetable->attributes['class'] = 'generaltable boxaligncenter';
    $outlinetable->cellpadding = 5;
    $outlinetable->id = 'outlinetable';
    $outlinetable->head = array($stractivity, $strviews);

    if ($CFG->useblogassociations) {
        $outlinetable->head[] = $strrelatedblogentries;
    }

    if ($showlastaccess) {
        $outlinetable->head[] = $strlast;
    }

    $modinfo = get_fast_modinfo($course);
    $sections = get_all_sections($course->id);

    $sql = "SELECT cm.id, COUNT('x') AS numviews, MAX(time) AS lasttime
              FROM {course_modules} cm
                   JOIN {modules} m ON m.id = cm.module
                   JOIN {log} l     ON l.cmid = cm.id
             WHERE cm.course = ? AND l.action LIKE 'view%' AND m.visible = 1
          GROUP BY cm.id";
    $views = $DB->get_records_sql($sql, array($course->id));

    $prevsecctionnum = 0;
    foreach ($modinfo->sections as $sectionnum=>$section) {
        foreach ($section as $cmid) {
            $cm = $modinfo->cms[$cmid];
            if (!$cm->has_view()) {
                continue;
            }
            if (!$cm->uservisible) {
                continue;
            }
            if ($prevsecctionnum != $sectionnum) {
                $sectionrow = new html_table_row();
                $sectionrow->attributes['class'] = 'section';
                $sectioncell = new html_table_cell();
                $sectioncell->colspan = count($outlinetable->head);

                $sectiontitle = get_section_name($course, $sections[$sectionnum]);

                $sectioncell->text = $OUTPUT->heading($sectiontitle, 3);
                $sectionrow->cells[] = $sectioncell;
                $outlinetable->data[] = $sectionrow;

                $prevsecctionnum = $sectionnum;
            }

            $dimmed = $cm->visible ? '' : 'class="dimmed"';
            $modulename = get_string('modulename', $cm->modname);

            $reportrow = new html_table_row();
            $activitycell = new html_table_cell();
            $activitycell->attributes['class'] = 'activity';

            $activityicon = $OUTPUT->pix_icon('icon', $modulename, $cm->modname, array('class'=>'icon'));

            $attributes = array();
            if (!$cm->visible) {
                $attributes['class'] = 'dimmed';
            }

            $activitycell->text = $activityicon . html_writer::link("$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id", format_string($cm->name), $attributes);;

            $reportrow->cells[] = $activitycell;

            $numviewscell = new html_table_cell();
            $numviewscell->attributes['class'] = 'numviews';

            if (!empty($views[$cm->id]->numviews)) {
                $numviewscell->text = $views[$cm->id]->numviews;
            } else {
                $numviewscell->text = '-';
            }

            $reportrow->cells[] = $numviewscell;

            if ($CFG->useblogassociations) {
                require_once($CFG->dirroot.'/blog/lib.php');
                $blogcell = new html_table_cell();
                $blogcell->attributes['class'] = 'blog';
                if ($blogcount = blog_get_associated_count($course->id, $cm->id)) {
                    $blogcell->text = html_writer::link('/blog/index.php?modid='.$cm->id, $blogcount);
                } else {
                    $blogcell->text = '-';
                }
                $reportrow->cells[] = $blogcell;
            }

            if ($showlastaccess) {
                $lastaccesscell = new html_table_cell();
                $lastaccesscell->attributes['class'] = 'lastaccess';

                if (isset($views[$cm->id]->lasttime)) {
                    $timeago = format_time(time() - $views[$cm->id]->lasttime);
                    $lastaccesscell->text = userdate($views[$cm->id]->lasttime)." ($timeago)";
                }
                $reportrow->cells[] = $lastaccesscell;
            }
            $outlinetable->data[] = $reportrow;
        }
    }
    echo html_writer::table($outlinetable);

    echo $OUTPUT->footer();



