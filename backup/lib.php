<?PHP //$Id$
    //This file contains all the function needed in the backup/restore utility
    //except the mod-related funtions that are into every backuplib.php inside
    //every mod directory

    //Insert necessary category ids to backup_ids table
    function insert_category_ids ($course,$backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("INSERT INTO {$CFG->prefix}backup_ids
                                   (backup_code, table_name, old_id)
                               SELECT DISTINCT '$backup_unique_code','quiz_categories',t.category
                               FROM {$CFG->prefix}quiz_questions t,
                                    {$CFG->prefix}quiz_question_grades g,
                                    {$CFG->prefix}quiz q
                               WHERE q.course = '$course' AND
                                     g.quiz = q.id AND
                                     g.question = t.id",false);
        return $status;
    }
    
    //Delete category ids from backup_ids table
    function delete_category_ids ($backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code = '$backup_unique_code'",false);
        return $status;
    }

?>
