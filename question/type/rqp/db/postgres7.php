<?php  //$Id$

// PostgreSQL commands for upgrading this question type

function qtype_rqp_upgrade($oldversion=0) {
    global $CFG;

    if ($oldversion < 2006032201) {
        modify_database('','CREATE TABLE prefix_question_rqp_servers (
                            id SERIAL PRIMARY KEY,
                            typeid integer NOT NULL default 0,
                            url varchar(255) NOT NULL default \'\',
                            can_render INT4 NOT NULL default 0,
                            can_author INT4 NOT NULL default 0
                       );');
    }

    return true;
}

?>
