<?php

    // Allows the admin to configure blocks (hide/show, delete and configure)

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/tablelib.php');

    admin_externalpage_setup('manageblocks');

    $confirm  = optional_param('confirm', 0, PARAM_BOOL);
    $hide     = optional_param('hide', 0, PARAM_INT);
    $show     = optional_param('show', 0, PARAM_INT);
    $delete   = optional_param('delete', 0, PARAM_INT);
    $unprotect = optional_param('unprotect', 0, PARAM_INT);
    $protect = optional_param('protect', 0, PARAM_INT);

/// Print headings

    $strmanageblocks = get_string('manageblocks');
    $strdelete = get_string('delete');
    $strversion = get_string('version');
    $strhide = get_string('hide');
    $strshow = get_string('show');
    $strsettings = get_string('settings');
    $strcourses = get_string('blockinstances', 'admin');
    $strname = get_string('name');
    $strshowblockcourse = get_string('showblockcourse');
    $strprotecthdr = get_string('blockprotect', 'admin'). $OUTPUT->help_icon('blockprotect','admin');
    $strprotect = get_string('blockprotect', 'admin');
    $strunprotect = get_string('blockunprotect', 'admin');

/// If data submitted, then process and store.

    if (!empty($hide) && confirm_sesskey()) {
        if (!$block = $DB->get_record('block', array('id'=>$hide))) {
            print_error('blockdoesnotexist', 'error');
        }
        $DB->set_field('block', 'visible', '0', array('id'=>$block->id));      // Hide block
        admin_get_root(true, false);  // settings not required - only pages
    }

    if (!empty($show) && confirm_sesskey() ) {
        if (!$block = $DB->get_record('block', array('id'=>$show))) {
            print_error('blockdoesnotexist', 'error');
        }
        $DB->set_field('block', 'visible', '1', array('id'=>$block->id));      // Show block
        admin_get_root(true, false);  // settings not required - only pages
    }

    if (!isset($CFG->undeletableblocktypes) || (!is_array($CFG->undeletableblocktypes) && !is_string($CFG->undeletableblocktypes))) {
        $undeletableblocktypes = array('navigation', 'settings');
    } else if (is_string($CFG->undeletableblocktypes)) {
        $undeletableblocktypes = explode(',', $CFG->undeletableblocktypes);
    } else {
        $undeletableblocktypes = $CFG->undeletableblocktypes;
    }

    if (!empty($protect) && confirm_sesskey()) {
        if (!$block = $DB->get_record('block', array('id'=>$protect))) {
            print_error('blockdoesnotexist', 'error');
        }
        if (!in_array($block->name, $undeletableblocktypes)) {
            $undeletableblocktypes[] = $block->name;
            set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
        }
        admin_get_root(true, false);  // settings not required - only pages
    }

    if (!empty($unprotect) && confirm_sesskey()) {
        if (!$block = $DB->get_record('block', array('id'=>$unprotect))) {
            print_error('blockdoesnotexist', 'error');
        }
        if (in_array($block->name, $undeletableblocktypes)) {
            $undeletableblocktypes = array_diff($undeletableblocktypes, array($block->name));
            set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
        }
        admin_get_root(true, false);  // settings not required - only pages
    }

    if (!empty($delete) && confirm_sesskey()) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading($strmanageblocks);

        if (!$block = blocks_get_record($delete)) {
            print_error('blockdoesnotexist', 'error');
        }

        if (get_string_manager()->string_exists('pluginname', "block_$block->name")) {
            $strblockname = get_string('pluginname', "block_$block->name");
        } else {
            $strblockname = $block->name;
        }

        if (!$confirm) {
            echo $OUTPUT->confirm(get_string('blockdeleteconfirm', '', $strblockname), 'blocks.php?delete='.$block->id.'&confirm=1', 'blocks.php');
            echo $OUTPUT->footer();
            exit;

        } else {
            // Inform block it's about to be deleted
            if (file_exists("$CFG->dirroot/blocks/$block->name/block_$block->name.php")) {
                $blockobject = block_instance($block->name);
                if ($blockobject) {
                    $blockobject->before_delete();  //only if we can create instance, block might have been already removed
                }
            }

            // First delete instances and then block
            $instances = $DB->get_records('block_instances', array('blockname' => $block->name));
            if(!empty($instances)) {
                foreach($instances as $instance) {
                    blocks_delete_instance($instance);
                }
            }

            // Delete block
            $DB->delete_records('block', array('id'=>$block->id));

            drop_plugin_tables($block->name, "$CFG->dirroot/blocks/$block->name/db/install.xml", false); // old obsoleted table names
            drop_plugin_tables('block_'.$block->name, "$CFG->dirroot/blocks/$block->name/db/install.xml", false);

            // Delete the capabilities that were defined by this block
            capabilities_cleanup('block/'.$block->name);

            // Remove event handlers and dequeue pending events
            events_uninstall('block/'.$block->name);

            $a->block = $strblockname;
            $a->directory = $CFG->dirroot.'/blocks/'.$block->name;
            notice(get_string('blockdeletefiles', '', $a), 'blocks.php');
        }
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmanageblocks);

/// Main display starts here

/// Get and sort the existing blocks

    if (!$blocks = $DB->get_records('block', array(), 'name ASC')) {
        print_error('noblocks', 'error');  // Should never happen
    }

    $incompatible = array();

/// Print the table of all blocks

    $table = new flexible_table('admin-blocks-compatible');

    $table->define_columns(array('name', 'instances', 'version', 'hideshow', 'undeletable', 'delete', 'settings'));
    $table->define_headers(array($strname, $strcourses, $strversion, $strhide.'/'.$strshow, $strprotecthdr, $strdelete, $strsettings));
    $table->define_baseurl($CFG->wwwroot.'/'.$CFG->admin.'/blocks.php');
    $table->set_attribute('class', 'compatibleblockstable blockstable generaltable');
    $table->setup();
    $tablerows = array();

    foreach ($blocks as $blockid=>$block) {
        $blockname = $block->name;

        if (!file_exists("$CFG->dirroot/blocks/$blockname/block_$blockname.php")) {
            $blockobject  = false;
            $strblockname = '<span class="notifyproblem">'.$blockname.' ('.get_string('missingfromdisk').')</span>';
            $plugin = new stdClass();
            $plugin->version = $block->version;

        } else {
            $plugin = new stdClass();
            $plugin->version = '???';
            if (file_exists("$CFG->dirroot/blocks/$blockname/version.php")) {
                include("$CFG->dirroot/blocks/$blockname/version.php");
            }

            if (!$blockobject  = block_instance($block->name)) {
                $incompatible[] = $block;
                continue;
            }
            $strblockname = get_string('pluginname', 'block_'.$blockname);
        }

        $delete = '<a href="blocks.php?delete='.$blockid.'&amp;sesskey='.sesskey().'">'.$strdelete.'</a>';

        $settings = ''; // By default, no configuration
        if ($blockobject and $blockobject->has_config()) {
            $blocksettings = admin_get_root()->locate('blocksetting' . $block->name);

            if ($blocksettings instanceof admin_externalpage) {
                $settings = '<a href="' . $blocksettings->url .  '">' . get_string('settings') . '</a>';
            } else if ($blocksettings instanceof admin_settingpage) {
                $settings = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=blocksetting'.$block->name.'">'.$strsettings.'</a>';
            } else {
                $settings = '<a href="block.php?block='.$blockid.'">'.$strsettings.'</a>';
            }
        }

        // MDL-11167, blocks can be placed on mymoodle, or the blogs page
        // and it should not show up on course search page

        $totalcount = $DB->count_records('block_instances', array('blockname'=>$blockname));
        $count = $DB->count_records('block_instances', array('blockname'=>$blockname, 'pagetypepattern'=>'course-view-*'));

        if ($count>0) {
            $blocklist = "<a href=\"{$CFG->wwwroot}/course/search.php?blocklist=$blockid&amp;sesskey=".sesskey()."\" ";
            $blocklist .= "title=\"$strshowblockcourse\" >$totalcount</a>";
        }
        else {
            $blocklist = "$totalcount";
        }
        $class = ''; // Nothing fancy, by default

        if (!$blockobject) {
            // ignore
            $visible = '';
        } else if ($blocks[$blockid]->visible) {
            $visible = '<a href="blocks.php?hide='.$blockid.'&amp;sesskey='.sesskey().'" title="'.$strhide.'">'.
                       '<img src="'.$OUTPUT->pix_url('i/hide') . '" class="icon" alt="'.$strhide.'" /></a>';
        } else {
            $visible = '<a href="blocks.php?show='.$blockid.'&amp;sesskey='.sesskey().'" title="'.$strshow.'">'.
                       '<img src="'.$OUTPUT->pix_url('i/show') . '" class="icon" alt="'.$strshow.'" /></a>';
            $class = ' class="dimmed_text"'; // Leading space required!
        }

        if ($block->version == $plugin->version) {
            $version = $block->version;
        } else {
            $version = "$block->version ($plugin->version)";
        }

        if (!$blockobject) {
            // ignore
            $undeletable = '';
        } else if (in_array($blockname, $undeletableblocktypes)) {
            $undeletable = '<a href="blocks.php?unprotect='.$blockid.'&amp;sesskey='.sesskey().'" title="'.$strunprotect.'">'.
                       '<img src="'.$OUTPUT->pix_url('t/unlock') . '" class="icon" alt="'.$strunprotect.'" /></a>';
        } else {
            $undeletable = '<a href="blocks.php?protect='.$blockid.'&amp;sesskey='.sesskey().'" title="'.$strprotect.'">'.
                       '<img src="'.$OUTPUT->pix_url('t/unlock_gray') . '" class="icon" alt="'.$strprotect.'" /></a>';
        }

        $row = array(
            '<span'.$class.'>'.$strblockname.'</span>',
            $blocklist,
            '<span'.$class.'>'.$version.'</span>',
            $visible,
            $undeletable,
            $delete,
            $settings
        );
        $tablerows[] = array(strip_tags($strblockname), $row); // first element will be used for sorting
    }

    textlib_get_instance()->asort($tablerows);
    foreach ($tablerows as $row) {
        $table->add_data($row[1]);
    }

    $table->print_html();

    if (!empty($incompatible)) {
        echo $OUTPUT->heading(get_string('incompatibleblocks', 'blockstable', 'admin'));

        $table = new flexible_table('admin-blocks-incompatible');

        $table->define_columns(array('block', 'delete'));
        $table->define_headers(array($strname, $strdelete));
        $table->define_baseurl($CFG->wwwroot.'/'.$CFG->admin.'/blocks.php');

        $table->set_attribute('class', 'incompatibleblockstable generaltable');

        $table->setup();

        foreach ($incompatible as $block) {
            $table->add_data(array(
                $block->name,
                '<a href="blocks.php?delete='.$block->id.'&amp;sesskey='.sesskey().'">'.$strdelete.'</a>',
            ));
        }
        $table->print_html();
    }

    echo $OUTPUT->footer();


