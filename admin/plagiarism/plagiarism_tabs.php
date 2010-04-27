<?php
    $strplagiarism = get_string('plagiarism', 'plagiarism');
    $strplagiarismreports = get_string('plagiarismreports', 'plagiarism');
    $strplagiarismdefaults = get_string('plagiarismdefaults', 'plagiarism');

    $tabs = array();
    $tabs[] = new tabobject('plagiarism', 'plagiarism.php', $strplagiarism, $strplagiarism, false);
    $tabs[] = new tabobject('plagiarismreports', 'plagiarism_reports.php', $strplagiarismreports, $strplagiarismreports, false);
    $tabs[] = new tabobject('plagiarismdefaults', 'plagiarism_defaults.php', $strplagiarismdefaults, $strplagiarismdefaults, false);
    print_tabs(array($tabs), $currenttab);