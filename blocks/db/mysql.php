<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle's
// blocks system.
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// Versions are defined by backup_version.php
//
// This file is tailored to MySQL

function blocks_upgrade($oldversion=0) {

global $CFG;
    
    $result = true;
    
    if ($oldversion < 2004041000 && $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}blocks` (
                        `id` int(10) unsigned NOT NULL auto_increment,
                        `name` varchar(40) NOT NULL default '',
                        `version` int(10) NOT NULL default '0',
                        `cron` int(10) unsigned NOT NULL default '0',
                        `lastcron` int(10) unsigned NOT NULL default '0',
                        `visible` tinyint(1) NOT NULL default '1',
                        PRIMARY KEY (`id`)
                     ) 
                     COMMENT = 'To register and update all the available blocks'");
    }

    if ($oldversion < 2004101900 && $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}block` (
                        `id` int(10) unsigned NOT NULL auto_increment,
                        `name` varchar(40) NOT NULL default '',
                        `version` int(10) NOT NULL default '0',
                        `cron` int(10) unsigned NOT NULL default '0',
                        `lastcron` int(10) unsigned NOT NULL default '0',
                        `visible` tinyint(1) NOT NULL default '1',
                        `multiple` tinyint(1) NOT NULL default '0',
                        PRIMARY KEY (`id`)
                     ) 
                     COMMENT = 'To register and update all the available blocks'");

        if(!$result) {
            return false;
        }

        $records = get_records('blocks');
        if(!empty($records)) {
            foreach($records as $block) {
                $block->multiple = 0;
                insert_record('block', $block);
            }
        }

        execute_sql("DROP TABLE `{$CFG->prefix}blocks`");

        $result = execute_sql("CREATE TABLE `{$CFG->prefix}block_instance` (
                        `id` int(10) not null auto_increment,
                        `blockid` int(10) not null default '0',
                        `pageid` int(10) not null default '0',
                        `pagetype` enum('course') not null,
                        `position` enum('l', 'r') not null,
                        `weight` tinyint(3) not null default '0',
                        `visible` tinyint(1) not null default '0',
                        `configdata` text not null default '',
                        
                        PRIMARY KEY(`id`),
                        INDEX pageid(`pageid`)
                    )");

        if(!$result) {
            return false;
        }

        $records = get_records('course');
        if(!empty($records)) {
            foreach($records as $thiscourse) {
                // The @ suppresses a notice emitted if there is no : in the string
                @list($left, $right) = split(':', $thiscourse->blockinfo);
                if(!empty($left)) {
                    $arr = explode(',', $left);
                    foreach($arr as $weight => $blk) {
                        $instance = new stdClass;
                        $instance->blockid    = abs($blk);
                        $instance->pageid     = $thiscourse->id;
                        $instance->pagetype   = MOODLE_PAGE_COURSE;
                        $instance->position   = BLOCK_POS_LEFT;
                        $instance->weight     = $weight;
                        $instance->visible    = ($blk > 0) ? 1 : 0;
                        $instance->configdata = '';
                        insert_record('block_instance', $instance);
                    }
                }
                if(!empty($right)) {
                    $arr = explode(',', $right);
                    foreach($arr as $weight => $blk) {
                        $instance = new stdClass;
                        $instance->blockid    = abs($blk);
                        $instance->pageid     = $thiscourse->id;
                        $instance->pagetype   = MOODLE_PAGE_COURSE;
                        $instance->position   = BLOCK_POS_RIGHT;
                        $instance->weight     = $weight;
                        $instance->visible    = ($blk > 0) ? 1 : 0;
                        $instance->configdata = '';
                        insert_record('block_instance', $instance);
                    }
                }
            }
        }

        execute_sql("ALTER TABLE `{$CFG->prefix}course` DROP COLUMN blockinfo");
    }

    //Finally, return result
    return $result;
}
?>
