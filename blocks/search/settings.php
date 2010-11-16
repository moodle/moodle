<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    //Enable file indexing (y/n)
    $settings->add(new admin_setting_configcheckbox('block_search_enable_file_indexing', get_string('configenablefileindexing', 'block_search'),
                       get_string('enablefileindexing', 'block_search'), 0, 1, 0));

    //file types
    $defaultfiletypes = 'PDF,TXT,HTML,PPT,XML,DOC,HTM';
    $settings->add(new admin_setting_configtext('block_search_filetypes', get_string('configfiletypes', 'block_search'),
                       get_string('listoffiletypes', 'block_search'), $defaultfiletypes, PARAM_TEXT));

    // usemoodleroot
    $settings->add(new admin_setting_configcheckbox('block_search_usemoodleroot', get_string('usemoodleroot', 'block_search'),
                       get_string('usemoodlerootdescription', 'block_search'), 1, 1, 0));

    //limit_index_body
    $settings->add(new admin_setting_configtext('block_search_limit_index_body', get_string('configlimitindexbody', 'block_search'),
                       get_string('indexbodylimit', 'block_search'), '', PARAM_INT));

    //setup default paths for following configs.
    if ($CFG->ostype == 'WINDOWS') {
        $default_pdf_to_text_cmd = "lib/xpdf/win32/pdftotext.exe -eol dos -enc UTF-8 -q";
        $default_word_to_text_cmd = "lib/antiword/win32/antiword/antiword.exe ";
        $default_word_to_text_env = "HOME={$CFG->dirroot}\\lib\\antiword\\win32";
    } else {
        $default_pdf_to_text_cmd = "lib/xpdf/linux/pdftotext -enc UTF-8 -eol unix -q";
        $default_word_to_text_cmd = "lib/antiword/linux/usr/bin/antiword";
        $default_word_to_text_env = "ANTIWORDHOME={$CFG->dirroot}/lib/antiword/linux/usr/share/antiword";
    }

    //pdf_to_text_cmd
    $settings->add(new admin_setting_configtext('block_search_pdf_to_text_cmd', get_string('configpdftotextcmd', 'block_search'),
                       get_string('pdftotextcmd', 'block_search'), $default_pdf_to_text_cmd, PARAM_RAW, 60));

    //word_to_text_cmd
    $settings->add(new admin_setting_configtext('block_search_word_to_text_cmd', get_string('configwordtotextcmd', 'block_search'),
                       get_string('wordtotextcmd', 'block_search'), $default_word_to_text_cmd, PARAM_RAW, 60));

    //word_to_text_env
    $settings->add(new admin_setting_configtext('block_search_word_to_text_env', get_string('configwordtotextenv', 'block_search'),
                       get_string('wordtotextenv', 'block_search'), $default_word_to_text_env, PARAM_RAW, 60));


    // modules activations
    if (isset($CFG->block_search_filetypes)) {
        $types = explode(',', $CFG->block_search_filetypes);
    } else {
        $types = explode(',', $defaultfiletypes);
    }

    if (!empty($types)) {
        foreach($types as $type) {
            $utype = strtoupper($type);
            $type = strtolower($type);
            $type = trim($type);
            if (preg_match("/\\b$type\\b/i", $defaultfiletypes)) continue;

            //header
            $propname = 'block_search_'.$type.'_to_text';
            $settings->add(new admin_setting_heading($propname, get_string('handlingfor', 'block_search').' '.$utype , ''));

            //word_to_text_cmd
            $propname = 'block_search_'.$type.'_to_text_cmd';
            $settings->add(new admin_setting_configtext($propname, get_string('configtypetotxtcmd', 'block_search'),
                               get_string('cmdtoconverttotextfor', 'block_search', $type), '', PARAM_PATH, 60));

            //word_to_text_env
            $propname = 'block_search_'.$type.'_to_text_env';
            $settings->add(new admin_setting_configtext($propname, get_string('configtypetotxtenv', 'block_search'),
                               get_string('envforcmdtotextfor', 'block_search', $type), '', PARAM_PATH, 60));

        }
    }

    require_once($CFG->dirroot.'/search/lib.php' );
    $searchnames = search_collect_searchables(true, false);
    list($searchable_list, $params) = $DB->get_in_or_equal($searchnames);

    //header
    $propname = 'block_search_'.$type.'_to_text';
    $settings->add(new admin_setting_heading($propname, get_string('searchdiscovery', 'block_search') , ''));

    $found_searchable_modules = 0;
    if ($modules = $DB->get_records_select('modules', "name $searchable_list", $params, 'name', 'id,name')){
        foreach($modules as $module){
            $keyname = 'search_in_'.$module->name;
            $settings->add(new admin_setting_configcheckbox($keyname, get_string('modulename', $module->name),
                               get_string('enableindexinginmodule', 'block_search', $module->name), 1, 1, 0));
            $found_searchable_modules = 1;
        }
    }

    if (!$found_searchable_modules) {
        //header
        $propname = 'block_search_nosearchablemodules';
        $settings->add(new admin_setting_heading($propname, get_string('nosearchablemodules', 'block_search') , ''));
    }

    //header
    $propname = 'block_search_searchswitches';
    $settings->add(new admin_setting_heading($propname, get_string('blockssearchswitches', 'block_search') , ''));

    $found_searchable_blocks = 0;
    if ($blocks = $DB->get_records_select('block', "name $searchable_list", $params, 'name', 'id,name')){
        foreach($blocks as $block){
            $keyname = 'search_in_'.$block->name;
            $settings->add(new admin_setting_configcheckbox($keyname, get_string('pluginname', 'block_'.$block->name),
                               get_string('enableindexinginblock', 'block_search', $block->name), 1, 1, 0));
            $found_searchable_blocks = 1;
        }
    }
    if (!$found_searchable_blocks) {
        //header
        $propname = 'block_search_nosearchableblocks';
        $settings->add(new admin_setting_heading($propname, get_string('nosearchableblocks', 'block_search') , ''));
    }

}

