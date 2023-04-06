<?php
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->dirroot/cohort/lib.php");

function local_qubits_site_render_list($search, $page, $perpage, $sortcolumn, $sortdir){
    global $PAGE, $CFG, $DB, $OUTPUT;
    $filters = array('search' => $search, "perpage" => $perpage);

    $where = '';
    if ($search != '') {
        $where .= " and s.name like '%$search%'";
    }

    $orderby = '';
    switch ($sortcolumn) {
        case 0:
            $orderby = ($sortdir == 0) ? "order by s.name asc" : "order by s.name desc";
            break;
        case 1:
            $orderby = ($sortdir == 0) ? "order by s.hostname asc" : "order by s.hostname desc";
            break;
        case 2:
            $orderby = ($sortdir == 0) ? "order by s.status asc" : "order by s.status desc";
            break;
        default:
            $orderby = ($sortdir == 0) ? "order by s.timemodified asc" : "order by s.timemodified desc";
            break;
    }
    $limit = $perpage;
    $offset = $page * $perpage;
    $sql = "SELECT s.* FROM {local_qubits_sites} as s WHERE true $where $orderby";
    $sites = $DB->get_records_sql($sql, array(), $offset, $limit);

    $sqlall = "SELECT s.id FROM {local_qubits_sites} as s WHERE true $where";
    $sitesall = $DB->get_records_sql($sqlall);
    $totalsites = count($sitesall);

    $columns = array(
        "name",
        "hostname",
        "status",
        "action"
    );

    $i = 0;
    $columns1 = array();
    foreach ($columns as $column) {
        if ($sortcolumn == 3) {
            $columns1[] = $column;
        } else {
            if ($i == $sortcolumn) {
                $columnicon = "sort";
                $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, get_string(strtolower($columnicon)), 'core', ['class' => 'iconsort']);
                $filters['sortdir'] = ($sortdir == 1) ? 0 : 1;
            } else {
                $columnicon = '';
                $filters['sortdir'] = 0;
            }
            $filters['sortcolumn'] = $i;
            if ($i == 3) {
                $columns1[] = $column;
            } else {
                $columns1[] = html_writer::link(new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php', $filters), $column . $columnicon);
            }
        }
        $i++;
    }
    $filters['sortdir'] = $sortdir;
    $filters['sortcolumn'] = $sortcolumn;

    $data = local_qubits_render_site_data($sites, $filters);
    $table = new html_table();

    $tableheader = $columns1;
    $table->head = $tableheader;
    $table->size = array('20%', '55%', '7%', '13%');
    $table->align = array('left', 'left', 'center', 'center');
    $table->width = '90%';
    $table->id = 'sitelisting';
    if (count($sites) > 0) {
        $table->data = $data;
    } else {
        $cell = new html_table_cell();
        $cell->text = get_string('no_records_found', 'local_qubitssite');
        $cell->colspan = 10;
        $row = new html_table_row();
        $row->cells[] = $cell;
        $table->data = array($row);
        $table->align = array('center');
    }
    
    $output = html_writer::table($table);
    $url = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php', $filters);
    $output .= $OUTPUT->paging_bar($totalsites, $page, $perpage, $url);
    return $output;
}

function local_qubits_render_site_data($sites, $filters) {
    global $DB, $OUTPUT, $USER;
    $data = array();
    $i = 1;
    foreach ($sites as $site) {
        $filters['publish'] = $site->id;
        $buttons = [];
        // User can edit and delete the sites in Qubits Site list page who has capabilities
        if(has_capability('local/qubitssite:edittenantsite', context_system::instance())){
            $publishurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php', $filters);
            if ($site->status == 1) {
                $publish = html_writer::link($publishurl, $OUTPUT->pix_icon('t/hide', 'Disable Site'));
            } else {
                $publish = html_writer::link($publishurl, $OUTPUT->pix_icon('t/show', 'Enable Site'));
            }
            $editsite = new moodle_url($CFG->wwwroot . '/local/qubitssite/edit.php', array('id' => $site->id, 'returnto' => 'sitelisting'));
            $buttons[] = html_writer::link($editsite, $OUTPUT->pix_icon('t/edit', 'Edit Site'));
        }

        $tenantcourses = new moodle_url($CFG->wwwroot . '/local/qubitscourse/index.php', array('siteid' => $site->id, 'returnto' => 'sitelisting'));
        $buttons[] = html_writer::link($tenantcourses, $OUTPUT->pix_icon('i/course', 'Site Courses'));

        $tenantusers = new moodle_url($CFG->wwwroot . '/local/qubitsuser/index.php', array('siteid' => $site->id, 'returnto' => 'sitelisting'));
        $buttons[] = html_writer::link($tenantusers, $OUTPUT->pix_icon('i/users', 'Site Users'));

        if(has_capability('local/qubitssite:deletetenantsite', context_system::instance())){
            $deletesite = new moodle_url($CFG->wwwroot . '/local/qubitssite/delete.php', array('id' => $site->id, 'returnto' => 'sitelisting'));
            $buttons[] = html_writer::link($deletesite, $OUTPUT->pix_icon('t/delete', 'Delete Site'));
        }

        $row = array(
            $site->name,
            $site->hostname,
            $publish,
            implode(' ', $buttons)
        );
        $data[] = $row;
    }
    return $data;
}

function local_qubitssite_publish_site($siteid) {
    global $DB, $USER, $CFG;
    $site = $DB->get_record("local_qubits_sites", array('id' => $siteid));
    if($site){
        if($site->status == 0){
            $site->status = 1;
            $site->timemodified = time();
        }else{
            $site->status = 0;
            $site->timemodified = time();
        }
        $result = $DB->update_record('local_qubits_sites', $site);
        return $result;
    } else {
        return false;
    }
}

function local_qubitssite_create_site($data, $editoroptions = NULL){
    global $DB, $CFG;

    $data->hostname = local_qubitssite_parse_url($data->hostname);
    // Check if timecreated is given.
    $data->timecreated  = !empty($data->timecreated) ? $data->timecreated : time();
    $data->timemodified = $data->timecreated;
    $data->cohortid = local_qubitssite_upsert_cohort($data->name, $data->hostname, "");

    if (!isset($data->status)) {
        // data not from form, add missing visibility info
        $data->status = 1;
    }
    $newqubitssiteid = $DB->insert_record('local_qubits_sites', $data);
    $qubitssite = $DB->get_record("local_qubits_sites", array('id' => $newqubitssiteid));
    return $qubitssite;
}

function local_qubitssite_update_site($data, $editoroptions = NULL){
    global $DB, $CFG;

    $data->hostname = local_qubitssite_parse_url($data->hostname);
    $data->timemodified = time();
    local_qubitssite_upsert_cohort($data->name, $data->hostname, $data->cohortid);

    if (!isset($data->status)) {
        // data not from form, add missing visibility info
        $data->status = 1;
    }
    $newqubitssiteid = $DB->update_record('local_qubits_sites', $data);
    $qubitssite = $DB->get_record("local_qubits_sites", array('id' => $newqubitssiteid));
    return $qubitssite;
}

// Remove Http or Https from URL.
function local_qubitssite_parse_url($url){
    if (!preg_match('#^http(s)?://#', $url)) {
        $url = 'http://' . $url;
    }
    return parse_url($url, PHP_URL_HOST);
}

function local_qubitssite_upsert_cohort($name, $cohortidnumber, $cohortid){
    $cohort = new \stdClass();
    $cohort->contextid = \context_system::instance()->id;
    $cohort->name = $name;
    $cohort->idnumber = $cohortidnumber;
    $cohort->description = 'Cohort - '.$name;
    $cohort->descriptionformat = FORMAT_HTML;
    if(empty($cohortid)){
        $cohortid = cohort_add_cohort($cohort);
    } else {
        $cohort = $DB->get_record('cohort', array('id'=>$cohortid));
        $cohort->name = $name;
        $cohort->idnumber = $cohortidnumber;
        $cohort->description = 'Cohort - '.$name;
        cohort_update_cohort($cohort);
    }
    return $cohortid;
}