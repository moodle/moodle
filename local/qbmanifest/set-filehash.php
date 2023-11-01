<?php

require_once('../../config.php');

global $DB;

$rfiles = $DB->get_records_sql("SELECT * FROM {files} WHERE component = :component AND filesize > 0 ",[
    "component" => "qbassignsubmission_file"
]);

$cnt = 0;

foreach($rfiles as $rfile){
    $str = "/{$rfile->contextid}/qbassignsubmission_file/submission_files/{$rfile->itemid}/{$rfile->filename}";
    $hashstr = sha1($str);
    if($hashstr != $rfile->pathnamehash){
        $rfile->pathnamehash = $hashstr;
        $DB->update_record("files", $rfile);
        echo $str."<br/>";
        $cnt++;
    }
}
echo "Total Count - $cnt";
exit;