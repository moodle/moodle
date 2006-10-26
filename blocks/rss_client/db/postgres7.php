<?php //$Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function rss_client_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2005111400) {
        // title and description should be TEXT as we don't have control over their length.
        table_column('block_rss_client','title','title','text');
        table_column('block_rss_client','description','description','text');
    }

    if ($oldversion < 2005090201) {
        modify_database('', 'ALTER TABLE prefix_block_rss_client
            ALTER COLUMN title SET DEFAULT \'\',
            ALTER COLUMN description SET DEFAULT \'\'');
    }


    if ($oldversion < 2006091100) {

        // We need a new field to store whether an RSS feed is shared or private.
        table_column('block_rss_client', '', 'shared', 'integer');

        // Admin feeds used to be displayed to everybody (shared feeds).
        $admins = get_admins();
        if (!empty($admins)) {
            $count = 0;
            foreach($admins as $admin) {
                if (!$count) {
                    $adminsql = 'userid = '.$admin->id;
                } else {
                    $adminsql .= ' OR userid = '.$admin->id;
                }
                $count++;
            }
            if ($rssfeeds = get_records_select('block_rss_client', $adminsql)) {
                foreach ($rssfeeds as $rssfeed) {
                    if (!set_field('block_rss_client', 'shared', 1)) {
                        notice('Could not set '.$rssfeed->title.' as a shared RSS feed.');
                    }
                }
            }
        }
    }

/// see MDL-6707 for more info about problem that was here

    if ($oldversion < 2006100101) {
        
        // Upgrade block to use the Roles System.
        $block = get_record('block', 'name', 'rss_client');
        
        if ($blockinstances = get_records('block_instance', 'blockid', $block->id)) {
            
            if (!$adminroles = get_roles_with_capability('moodle/legacy:admin', CAP_ALLOW)) {
                notice('Default student role was not found. Roles and permissions '.
                       'for all your Remote RSS Feed blocks will have to be '.
                       'manually set after this upgrade.');
            }
            if (!$teacherroles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
                notice('Default teacher role was not found. Roles and permissions '.
                       'for all your Remote RSS Feed blocks will have to be '.
                       'manually set after this upgrade.');
            }
            if (!$studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                notice('Default student role was not found. Roles and permissions '.
                       'for all your Remote RSS Feed blocks will have to be '.
                       'manually set after this upgrade.');
            }
            
            foreach ($blockinstances as $bi) {
                $context = get_context_instance(CONTEXT_BLOCK, $bi->id);
                
                if ($bi->pagetype == 'course-view' && $bi->pageid == SITEID) {
                    
                    // Only the admin was allowed to manage the RSS feed block
                    // on the site home page.
                    
                    // Since this is already the default behavior set in
                    // blocks/rss_client/db/access.php, we don't need to
                    // specifically assign the capabilities here.
                    
                } else {
                    
                    // Who can add shared feeds? This was defined in lib/rsslib.php
                    // for config var block_rss_client_submitters.
                    switch ($CFG->block_rss_client_submitters) {
                        
                        case 0:
                            // SUBMITTERS_ALL_ACCOUNT_HOLDERS
                            
                            foreach ($adminroles as $adminrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_ALLOW, $adminrole->id, $context->id);
                            }
                            foreach ($teacherroles as $teacherrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_ALLOW, $teacherrole->id, $context->id);
                            }
                            foreach ($studentroles as $studentrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_ALLOW, $studentrole->id, $context->id);
                            }
                            break;
                        
                        case 1:
                            // SUBMITTERS_ADMIN_ONLY
                            
                            // Since this is already the default behavior set in
                            // blocks/rss_client/db/access.php, we don't need to
                            // specifically assign the capabilities here.
                            break;
                        
                        case 2:
                            // SUBMITTERS_ADMIN_AND_TEACHER
                            
                            foreach ($adminroles as $adminrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_ALLOW, $adminrole->id, $context->id);
                            }
                            foreach ($teacherroles as $teacherrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_ALLOW, $teacherrole->id, $context->id);
                            }
                            foreach ($studentroles as $studentrole) {
                                assign_capability('block/rss_client:createsharedfeeds', CAP_PREVENT, $studentrole->id, $context->id);
                            }
                            break;

                    } // End switch.
                    
                }
            }   
        }
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
