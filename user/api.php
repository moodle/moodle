<?php
/**
 * Created on 01/12/2008
 *
 * user core api
 *
 * @author Jerome Mouneyrac
 */

/**
 * DO NOT USE ANYTHING FROM THIS FILE - WORK IN PROGRESS
 */
final class user_api {

    /**
     * Returns a subset of users (DO NOT COUNT)
     * @global object $DB
     * @param string $sort A SQL snippet for the sorting criteria to use
     * @param string $recordsperpage how many records do pages have
     * @param string $page which page to return (starts from 0)
     * @param string $fields A comma separated list of fields to be returned from the chosen table.
     * @param object $selectioncriteria:
     *      ->search         string     A simple string to search for
     *      ->confirmed      bool       A switch to allow/disallow unconfirmed users
     *      ->exceptions     array(int) A list of IDs to ignore, eg 2,4,5,8,9,10
     *      ->firstinitial   string     ?
     *      ->lastinitial    string     ?
     * @return array|false Array of {@link $USER} objects. False is returned if an error is encountered.
     */
    static function tmp_get_users($sort='firstname ASC', $recordsperpage=999999, $page=0, $fields='*', $selectioncriteria=NULL) {
        global $DB;

         ///WS: convert array into an object
        if (!empty($selectioncriteria) && is_array($selectioncriteria))  {
            $selectioncriteria = (object) $selectioncriteria;
        }

        $LIKE      = $DB->sql_ilike();
        $fullname  = $DB->sql_fullname();

        $select = " username <> :guest AND deleted = 0";
        $params = array('guest'=>'guest');

        if (!empty($selectioncriteria->search)){
            $selectioncriteria->search = trim($selectioncriteria->search);
            $select .= " AND ($fullname $LIKE :search1 OR email $LIKE :search2 OR username = :search3)";
            $params['search1'] = "%".$selectioncriteria->search."%";
            $params['search2'] = "%".$selectioncriteria->search."%";
            $params['search3'] = $selectioncriteria->search;
        }

        if (!empty($selectioncriteria->confirmed)) {
            $select .= " AND confirmed = 1";
        }

        if (!empty($selectioncriteria->exceptions)) {
            list($selectioncriteria->exceptions, $eparams) = $DB->get_in_or_equal($selectioncriteria->exceptions, SQL_PARAMS_NAMED, 'ex0000', false);
            $params = $params + $eparams;
            $except = " AND id ".$selectioncriteria->exceptions;
        }

        if (!empty($selectioncriteria->firstinitial)) {
            $select .= " AND firstname $LIKE :fni";
            $params['fni'] = $selectioncriteria->firstinitial."%";
        }
        if (!empty($selectioncriteria->lastinitial)) {
            $select .= " AND lastname $LIKE :lni";
            $params['lni'] = $selectioncriteria->lastinitial."%";
        }

        if (!empty($selectioncriteria->extraselect)) {
            $select .= " AND ".$selectioncriteria->extraselect;
            if (empty($selectioncriteria->extraparams)){
                $params = $params + (array)$selectioncriteria->extraparams;
            }
        }
        varlog($DB->get_records_select('user', $select, $params, $sort, $fields, $page, $recordsperpage));
       
        return $DB->get_records_select('user', $select, $params, $sort, $fields, $page, $recordsperpage);
    }

   
    /**
     * Creates an User with given information. Required fields are:
     * -username
     * -idnumber
     * -firstname
     * -lastname
     * -email
     *
     * And there's some interesting fields:
     * -password
     * -auth
     * -confirmed
     * -timezone
     * -country
     * -emailstop
     * -theme
     * -lang
     * -mailformat
     *
     * @param assoc array or object $user
     *
     * @return userid or thrown exceptions
     */
    static function tmp_create_user($user) {
        global $CFG, $DB;
     ///WS: convert user array into an user object
        if (is_array($user))  {
            $user = (object) $user;
        }

     ///check password and auth fields
        if (!isset($user->password)) {
            $user->password = '';
        }
        if (!isset($user->auth)) {
            $user->auth = 'manual';
        }

        $required = array('username','firstname','lastname','email');
        foreach ($required as $req) {
            if (!isset($user->{$req})) {
                throw new moodle_exception('missingerequiredfield');
            }
        }

        $record = create_user_record($user->username, $user->password, $user->auth);
        if ($record) {
            $user->id = $record->id;
            if ($DB->update_record('user',$user)) {
                return $record->id;
            } else {
                $DB->delete_record('user',array('id' => $record->id));
            }
        }
        throw new moodle_exception('couldnotcreateuser');
    }

    /**
     * Marks user deleted in internal user database and notifies the auth plugin.
     * Also unenrols user from all roles and does other cleanup.
     * @param object $user       Userobject before delete    (without system magic quotes)
     * @return boolean success
     */
    static function tmp_delete_user($user) {
        global $CFG, $DB;
        require_once($CFG->libdir.'/grouplib.php');
        require_once($CFG->libdir.'/gradelib.php');

        $DB->begin_sql();

        // delete all grades - backup is kept in grade_grades_history table
        if ($grades = grade_grade::fetch_all(array('userid'=>$user->id))) {
            foreach ($grades as $grade) {
                $grade->delete('userdelete');
            }
        }

        // remove from all groups
        $DB->delete_records('groups_members', array('userid'=>$user->id));

        // unenrol from all roles in all contexts
        role_unassign(0, $user->id); // this might be slow but it is really needed - modules might do some extra cleanup!

        // now do a final accesslib cleanup - removes all role assingments in user context and context itself
        delete_context(CONTEXT_USER, $user->id);

        require_once($CFG->dirroot.'/tag/lib.php');
        tag_set('user', $user->id, array());

        // workaround for bulk deletes of users with the same email address
        $delname = "$user->email.".time();
        while ($DB->record_exists('user', array('username'=>$delname))) { // no need to use mnethostid here
            $delname++;
        }

        // mark internal user record as "deleted"
        $updateuser = new object();
        $updateuser->id           = $user->id;
        $updateuser->deleted      = 1;
        $updateuser->username     = $delname;         // Remember it just in case
        $updateuser->email        = '';               // Clear this field to free it up
        $updateuser->idnumber     = '';               // Clear this field to free it up
        $updateuser->timemodified = time();

        if ($DB->update_record('user', $updateuser)) {
            $DB->commit_sql();
            // notify auth plugin - do not block the delete even when plugin fails
            $authplugin = get_auth_plugin($user->auth);
            $authplugin->user_delete($user);

            events_trigger('user_deleted', $user);
            return true;

        } else {
            $DB->rollback_sql();
            return false;
        }
    }

    /**
     * Update a user record from its id
     * Warning: no checks are done on the data!!!
     * @param object $user 
     */
    static function tmp_update_user($user) {
        global $DB;
        if ($DB->update_record('user', $user)) {
            $DB->commit_sql();
            events_trigger('user_updated', $user);
            return true;
        } else {
            $DB->rollback_sql();
            return false;
        }
    }

}

?>
