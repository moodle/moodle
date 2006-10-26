<?PHP  //$Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.
//
// This file is tailored to PostgreSQL

function blocks_upgrade($oldversion=0) {

global $CFG;
    
    $result = true;
    
    if ($oldversion < 2004041000 and $result) {
        $result = execute_sql("
                                CREATE TABLE {$CFG->prefix}blocks 
                                (
                                  id SERIAL8 PRIMARY KEY,
                                  name varchar(40) NOT NULL default '',
                                  version INT8 NOT NULL default '0',
                                  cron INT8  NOT NULL default '0',
                                  lastcron INT8  NOT NULL default '0',
                                  visible int NOT NULL default '1'
                                )
        ") ;

    }

    if ($oldversion < 2004101900 && $result) {
        $result = execute_sql("CREATE TABLE {$CFG->prefix}block (
                        id SERIAL8 PRIMARY KEY,
                        name varchar(40) NOT NULL default '',
                        version INT8 NOT NULL default '0',
                        cron INT8 NOT NULL default '0',
                        lastcron INT8 NOT NULL default '0',
                        visible int NOT NULL default '1',
                        multiple int NOT NULL default '0'
                     ) 
                     ");

        if(!$result) {
            return false;
        }

        $records = get_records('blocks');
        if(!empty($records)) {
            foreach($records as $block) {
                $block->multiple = 0;
                insert_record('block', $block, false);
            }
            execute_sql("SELECT setval('{$CFG->prefix}block_id_seq', (SELECT MAX(id) FROM {$CFG->prefix}block), true)");
        }

        execute_sql("DROP TABLE {$CFG->prefix}blocks");
        
        $result = execute_sql("CREATE TABLE {$CFG->prefix}block_instance (
                        id SERIAL8 PRIMARY KEY,
                        blockid INT8 not null default '0',
                        pageid INT8 not null default '0',
                        pagetype varchar(12) not null default '',
                        position char not null default 'l' check (position in ('l', 'r')) ,
                        weight int not null default '0',
                        visible int not null default '0',
                        configdata text not null default ''
                    )");

        if(!$result) {
            return false;
        }
        
        $records = get_records('course', '','','', 'id, shortname, blockinfo');
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
                        $instance->pagetype   = PAGE_COURSE_VIEW;
                        $instance->position   = BLOCK_POS_LEFT;
                        $instance->weight     = $weight;
                        $instance->visible    = ($blk > 0) ? 1 : 0;
                        $instance->configdata = '';
                        insert_record('block_instance', $instance, false);
                    }
                }
                if(!empty($right)) {
                    $arr = explode(',', $right);
                    foreach($arr as $weight => $blk) {
                        $instance = new stdClass;
                        $instance->blockid    = abs($blk);
                        $instance->pageid     = $thiscourse->id;
                        $instance->pagetype   = PAGE_COURSE_VIEW;
                        $instance->position   = BLOCK_POS_RIGHT;
                        $instance->weight     = $weight;
                        $instance->visible    = ($blk > 0) ? 1 : 0;
                        $instance->configdata = '';
                        insert_record('block_instance', $instance, false);
                    }
                }
            }
        }

        execute_sql("ALTER TABLE {$CFG->prefix}course DROP COLUMN blockinfo");
    }

    if ($oldversion < 2004112900 && $result) {
        $result = $result && table_column('block_instance', 'pagetype', 'pagetype', 'varchar', '20', '');
        $result = $result && table_column('block_instance', 'position', 'position', 'varchar', '10', '');
    }

    if ($oldversion < 2005043000 && $result) {
        $records = get_records('block');
        if(!empty($records)) {
            foreach($records as $block) {
                if(!block_is_compatible($block->name)) {
                    $block->visible = 0;
                    update_record('block', $block);
                    notify('The '.$block->name.' block has been disabled because it is not compatible with Moodle 1.5 and needs to be updated by a programmer.');
                }
            }
        }
    }

    if ($oldversion < 2005022401 && $result) { // Mass cleanup of bad upgrade scripts
        execute_sql("CREATE INDEX {$CFG->prefix}block_instance_pageid_idx ON {$CFG->prefix}block_instance (pageid)",false); // this one should be quiet...
        modify_database('','ALTER TABLE prefix_block_instance ALTER pagetype SET DEFAULT \'\'');
        modify_database('','ALTER TABLE prefix_block_instance ALTER position SET DEFAULT \'\'');
        modify_database('','ALTER TABLE prefix_block_instance ALTER pagetype SET NOT NULL');
        modify_database('','ALTER TABLE prefix_block_instance ALTER position SET NOT NULL');
    }

    if ($oldversion < 2005081600) {
        modify_database('',"CREATE TABLE prefix_block_pinned ( 
            id SERIAL8 PRIMARY KEY,
            blockid INT8 NOT NULL default 0,
            pagetype varchar(20) NOT NULL default '',
            position varchar(10) NOT NULL default '',
            weight INT NOT NULL default 0,
            visible INT NOT NULL default 0,
            configdata text NOT NULL default 0
          );");
     }

    if ($oldversion < 2005090200) {
        execute_sql("CREATE INDEX {$CFG->prefix}block_instance_pagetype_idx ON {$CFG->prefix}block_instance (pagetype);",false); // do it silently, in case it's already there from 1.5
        modify_database('','CREATE INDEX prefix_block_pinned_pagetype_idx ON prefix_block_pinned (pagetype);');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    //Finally, return result
    return $result;
}
?>
