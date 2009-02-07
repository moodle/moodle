<?php  //$Id$

if ($hassiteconfig) {

    $ADMIN->add('modules', new admin_category('modsettings', get_string('activities')));
    $ADMIN->add('modsettings', new admin_page_managemods());
    if ($modules = get_records('modules')) {
        $modulebyname = array();

        foreach ($modules as $module) {
            $strmodulename = get_string('modulename', $module->name);
            // Deal with modules which are lacking the language string
            if ($strmodulename == '[[modulename]]') {
                $textlib = textlib_get_instance();
                $strmodulename = $textlib->strtotitle($module->name);
            }
            $modulebyname[$strmodulename] = $module;
        }
        ksort($modulebyname);

        foreach ($modulebyname as $strmodulename=>$module) {
            $modulename = $module->name;
            if (file_exists($CFG->dirroot.'/mod/'.$modulename.'/settingstree.php')) {
                include($CFG->dirroot.'/mod/'.$modulename.'/settingstree.php');
            } else if (file_exists($CFG->dirroot.'/mod/'.$modulename.'/settings.php')) {
                // do not show disabled modules in tree, keep only settings link on manage page
                $settings = new admin_settingpage('modsetting'.$modulename, $strmodulename, 'moodle/site:config', !$module->visible);
                if ($ADMIN->fulltree) {
                    include($CFG->dirroot.'/mod/'.$modulename.'/settings.php');
                }
                $ADMIN->add('modsettings', $settings);
            } else if (file_exists($CFG->dirroot.'/mod/'.$modulename.'/config.html')) {
                $ADMIN->add('modsettings', new admin_externalpage('modsetting'.$modulename, $strmodulename, "$CFG->wwwroot/$CFG->admin/module.php?module=$modulename", 'moodle/site:config', !$module->visible));
            }
        }
    }


    $ADMIN->add('modules', new admin_category('blocksettings', get_string('blocks')));
    $ADMIN->add('blocksettings', new admin_page_manageblocks());
    $ADMIN->add('blocksettings', new admin_externalpage('stickyblocks', get_string('stickyblocks', 'admin'), "$CFG->wwwroot/$CFG->admin/stickyblocks.php"));
    if (!empty($CFG->blocks_version) and $blocks = get_records('block')) {
        $blockbyname = array();

        foreach ($blocks as $block) {
            if(($blockobject = block_instance($block->name)) === false) {
                // Failed to load
                continue;
            }
            $blockbyname[$blockobject->get_title()] = $block;
        }
        ksort($blockbyname);

        foreach ($blockbyname as $strblockname=>$block) {
            $blockname = $block->name;
            if (file_exists($CFG->dirroot.'/blocks/'.$blockname.'/settings.php')) {
                $settings = new admin_settingpage('blocksetting'.$blockname, $strblockname, 'moodle/site:config', !$block->visible);
                if ($ADMIN->fulltree) {
                    include($CFG->dirroot.'/blocks/'.$blockname.'/settings.php');
                }
                $ADMIN->add('blocksettings', $settings);

            } else if (file_exists($CFG->dirroot.'/blocks/'.$blockname.'/config_global.html')) {
                $ADMIN->add('blocksettings', new admin_externalpage('blocksetting'.$blockname, $strblockname, "$CFG->wwwroot/$CFG->admin/block.php?block=$block->id", 'moodle/site:config', !$block->visible));
            }
        }
    }

    $ADMIN->add('modules', new admin_category('filtersettings', get_string('managefilters')));
    // "filtersettings" settingpage
    $temp = new admin_settingpage('managefilters', get_string('filtersettings', 'admin'));
    if ($ADMIN->fulltree) {
        $items = array();
        $items[] = new admin_setting_managefilters();
        $items[] = new admin_setting_heading('managefilterscommonheading', get_string('commonsettings', 'admin'), '');
        $items[] = new admin_setting_configselect('cachetext', get_string('cachetext', 'admin'), get_string('configcachetext', 'admin'), 60, array(604800 => get_string('numdays','',7),
                                                                                                                                               86400 => get_string('numdays','',1),
                                                                                                                                               43200 => get_string('numhours','',12),
                                                                                                                                               10800 => get_string('numhours','',3),
                                                                                                                                               7200 => get_string('numhours','',2),
                                                                                                                                               3600 => get_string('numhours','',1),
                                                                                                                                               2700 => get_string('numminutes','',45),
                                                                                                                                               1800 => get_string('numminutes','',30),
                                                                                                                                               900 => get_string('numminutes','',15),
                                                                                                                                               600 => get_string('numminutes','',10),
                                                                                                                                               540 => get_string('numminutes','',9),
                                                                                                                                               480 => get_string('numminutes','',8),
                                                                                                                                               420 => get_string('numminutes','',7),
                                                                                                                                               360 => get_string('numminutes','',6),
                                                                                                                                               300 => get_string('numminutes','',5),
                                                                                                                                               240 => get_string('numminutes','',4),
                                                                                                                                               180 => get_string('numminutes','',3),
                                                                                                                                               120 => get_string('numminutes','',2),
                                                                                                                                               60 => get_string('numminutes','',1),
                                                                                                                                               30 => get_string('numseconds','',30),
                                                                                                                                               0 => get_string('no')));
        $items[] = new admin_setting_configselect('filteruploadedfiles', get_string('filteruploadedfiles', 'admin'), get_string('configfilteruploadedfiles', 'admin'), 0, array('0' => get_string('none'),
                                                                                                                                                                                '1' => get_string('allfiles'),
                                                                                                                                                                                '2' => get_string('htmlfilesonly')));
        $items[] = new admin_setting_configcheckbox('filtermatchoneperpage', get_string('filtermatchoneperpage', 'admin'), get_string('configfiltermatchoneperpage', 'admin'), 0);
        $items[] = new admin_setting_configcheckbox('filtermatchonepertext', get_string('filtermatchonepertext', 'admin'), get_string('configfiltermatchonepertext', 'admin'), 0);
        $items[] = new admin_setting_configcheckbox('filterall', get_string('filterall', 'admin'), get_string('configfilterall', 'admin'), 0);
        foreach ($items as $item) {
            $item->set_updatedcallback('reset_text_filters_cache');
            $temp->add($item);
        }
    }
    $ADMIN->add('filtersettings', $temp);

    if (empty($CFG->textfilters)) {
        $activefilters = array();
    } else {
        $activefilters = explode(',', $CFG->textfilters);
    }
    $filterlocations = array('mod','filter');
    foreach ($filterlocations as $filterlocation) {
        $filters = get_list_of_plugins($filterlocation);

        $filterbyname = array();

        foreach ($filters as $filter) {
            $strfiltername = get_string('filtername', $filter);
            // Deal with filters which are lacking the language string
            if ($strfiltername == '[[filtername]]') {
                $textlib = textlib_get_instance();
                $strfiltername = $textlib->strtotitle($filter);
            }
            $filterbyname[$strfiltername] = "$filterlocation/$filter";
        }
        ksort($filterbyname);

        foreach ($filterbyname as $strfiltername=>$filterfull) {
            if (file_exists("$CFG->dirroot/$filterfull/filtersettings.php")) {
                $settings = new admin_settingpage('filtersetting'.str_replace('/', '', $filterfull), $strfiltername, 'moodle/site:config', !in_array($filterfull, $activefilters));
                if ($ADMIN->fulltree) {
                    include("$CFG->dirroot/$filterfull/filtersettings.php");
                }
                $ADMIN->add('filtersettings', $settings);

            } else if (file_exists("$CFG->dirroot/$filterfull/filterconfig.html")) {
                $ADMIN->add('filtersettings', new admin_externalpage('filtersetting'.str_replace('/', '', $filterfull), $strfiltername, "$CFG->wwwroot/$CFG->admin/filter.php?filter=$filterfull", 'moodle/site:config', !in_array($filterfull, $activefilters)));
            }
        }
    }
}


/// Now add reports

foreach (get_list_of_plugins($CFG->admin.'/report') as $plugin) {
    $settings_path = "$CFG->dirroot/$CFG->admin/report/$plugin/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
        continue;
    }

    $index_path = "$CFG->dirroot/$CFG->admin/report/$plugin/index.php";
    if (!file_exists($index_path)) {
        continue;
    }
    // old style 3rd party plugin without settings.php
    $www_path = "$CFG->wwwroot/$CFG->admin/report/$plugin/index.php";
    $reportname = get_string($plugin, 'report_' . $plugin);
    $ADMIN->add('reports', new admin_externalpage('report'.$plugin, $reportname, $www_path, 'moodle/site:viewreports'));
}
