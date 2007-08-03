<?php // $Id$

require_once('../config.php');

/**
 * Creates tags

 * Ex: tag_create('A VeRY   cOoL    Tag, Another NICE tag')
 * will create the following normalized {@link tag_normalize()} entries in tags table: 
 *  'a very cool tag'  
 *  'another nice tag'
 * 
 * @param string $tag_names_csv CSV tag names (can be unnormalized) to be created.
 * @param string $tag_type type of tag to be created ("default" is the default value).
 * @return an array of tags ids, indexed by their normalized names
 */
function tag_create($tag_names_csv, $tag_type="default") {
    global $USER;

    $tags = explode(",", $tag_names_csv );

    $tag_object = new StdClass;
    $tag_object->tagtype = $tag_type;
    $tag_object->userid = $USER->id;

    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
    $can_create_tags = has_capability('moodle/tag:create',$systemcontext);
    
    $norm_tag_names_csv = '';
    foreach ($tags as $tag) {

        // rawname keeps the original casing of the string
        $tag_object->rawname        = tag_normalize($tag, false);
        
        // name lowercases the string
        $tag_object->name           = tag_normalize($tag);
        $norm_tag_names_csv         .= $tag_object->name . ',';

        $tag_object->timemodified   = time();        
        
        $exists = record_exists('tag', 'name', $tag_object->name);
       
        if ( $can_create_tags && !$exists && !empty($tag_object->name) && !is_numeric($tag_object->name) ) {
            insert_record('tag', $tag_object);
        }
    }

    $norm_tag_names_csv = substr($norm_tag_names_csv,0,-1);
    
    return tags_id( $norm_tag_names_csv );

}

/**
 * Deletes tags
 * 
 * Ex 1: tag_delete('a very cool tag, another nice tag')
 * Will delete the tags with names 'a very cool tag' and 'another nice tag' from the 'tags' table, if they exist!
 *
 * Ex 2: tag_delete('computers, 123, 143, algorithms')
 *  Will delete tags with names 'computers' and 'algorithms' and tags with ids 123 and 143.
 * 
 * 
 * @param string $tag_names_or_ids_csv **normalized** tag names or ids of the tags to be deleted.
 */

function tag_delete($tag_names_or_ids_csv) {

    //covert all ids to names
    $tag_names_csv = tag_name_from_string($tag_names_or_ids_csv);
    
    //put apostrophes in names
    $tag_names_csv_with_apos = "'" . str_replace(',', "','", $tag_names_csv) . "'";

    delete_records_select('tag',"name IN ($tag_names_csv_with_apos)");

}

/**
 * Get all tags from the records
 *
 * @param string $tag_types_csv (optional, default value is "default". If '*' is passed, tags of any type will be returned).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return 
 *   (optional, by default 'id, tagtype, name'). The first field will be used as key for the
 *   array so must be a unique field such as 'id'. 
 */
function get_all_tags($tag_types_csv="default", $sort='name ASC', $fields='id, tagtype, name, rawname') {
    
    if ($tag_types_csv == '*'){
        return get_records('tag', '', '', $sort, $fields);
    }
    
    $tag_types_csv_with_apos = "'" . str_replace(',', "','", $tag_types_csv ) . "'";
    
    return get_records_list('tag', 'tagtype', $tag_types_csv_with_apos, $sort, $fields);
}

/**
 * Determines if a tag exists
 *
 * @param string $tag_name_or_id **normalized** tag name, or an id.
 * @return true if exists or false otherwise
 * 
 */
function tag_exists($tag_name_or_id) {
    
    if (is_numeric($tag_name_or_id)) {
        return record_exists('tag', 'id', $tag_name_or_id);
    }
    elseif (is_string($tag_name_or_id)) {
        return record_exists('tag', 'name', $tag_name_or_id);
    }
}

/**
 * Function that returns the id of a tag
 *
 * @param String $tag_name **normalized** name of the tag
 * @return int id of the matching tag
 */
function tag_id($tag_name) {
    $tag = get_record('tag', 'name', trim($tag_name), '', '', '', '', 'id');
    
    if ($tag){
        return $tag->id;
    }
    else{
        return false;
    }    
}

/**
 * Function that returns the ids of tags
 * 
 * Ex: tags_id('computers, algorithms')
 * 
 * @param String $tag_names_csv comma separated **normalized** tag names.
 * @return Array array with the tags ids, indexed by their **normalized** names
 */
function tags_id($tag_names_csv) {
    
    $normalized_tag_names_csv = tag_normalize($tag_names_csv);
    $tag_names_csv_with_apos = "'" . str_replace(',', "','", $normalized_tag_names_csv ) . "'";

    $tag_objects = get_records_list('tag','name', $tag_names_csv_with_apos, "" , "name, id" );

    $tags_ids = array();
    foreach ($tag_objects as $tag) {
        $tags_ids[$tag->name] = $tag->id;
    }

    return $tags_ids;
}

/**
 * Function that returns the name of a tag
 *
 * @param int $tag_id id of the tag
 * @return String name of the tag with the id passed
 */
function tag_name($tag_id) {
    $tag = get_record('tag', 'id', $tag_id, '', '', '', '', 'name');
    
    if ($tag){
        return $tag->name;
    }
    else{
        return '';
    }
}

/**
 * Function that retrieves the names of tags given their ids
 *
 * @param String $tag_ids_csv comma separated tag ids
 * @return Array an array with the tags names, indexed by their ids
 */

function tags_name($tag_ids_csv) {
    
    //remove any white spaces
    $tag_ids_csv = str_replace(' ', '', $tag_ids_csv);
    
    $tag_ids_csv_with_apos = "'" . str_replace(',', "','", $tag_ids_csv ) . "'";
    
    $tag_objects = get_records_list('tag','id', $tag_ids_csv_with_apos, "" , "name, id" );

    $tags_names = array();
    foreach ($tag_objects as $tag) {
        $tags_names[$tag->id] = $tag->name;
    }

    return $tags_names;
}

/**
 * Function that returns the name of a tag for display. 
 * 
 * @param mixed $tag_object
 * @return string
 */
function tag_display_name($tag_object){
    
    global $CFG;
    
    if( !empty($CFG->keeptagnamecase) ) {
        //this is the normalized tag name
        return mb_convert_case($tag_object->name, MB_CASE_TITLE,"UTF-8"); 
    }
    else {
        //original casing of the tag name
        return $tag_object->rawname;
    }
    
}

/**
 * Function that retrieves a tag object by its id
 *
 * @param String $tag_id
 * @return mixed a fieldset object containing the first matching record, or false if none found
 */
function tag_by_id($tag_id) {
    
    return get_record('tag','id',$tag_id);
}

/**
 * Function that retrieves a tag object by its name
 *
 * @param String $tag_name
 * @return mixed a fieldset object containing the first matching record, or false if none found
 */
function tag_by_name($tag_name) {
    $tag = get_record('tag','name',$tag_name);
    return $tag;
}

/**
 * In a comma separated string of ids or names of tags, replaces all tag names with their correspoding ids
 * 
 * Ex: 
 *  Suppose the DB contains only the following entries in the tags table:
 *      id    name 
 *      10    moodle
 *      12    science
 *      22    education
 * 
 * tag_id_from_string('moodle, 12, education, programming, 33, 11')
 *    will return '10,12,22,,33,11'
 *      
 * This is a helper function used by functions of this API to process function arguments ($tag_name_or_id)
 * 
 * @param string $tag_names_or_ids_csv comma separated **normalized** names or ids of tags
 * @return int comma separated ids of the tags 
 */
function tag_id_from_string($tag_names_or_ids_csv) {

    $tag_names_or_ids = explode(',', $tag_names_or_ids_csv);

    $tag_ids = array();
    foreach ($tag_names_or_ids as $name_or_id) {

        if (is_numeric($name_or_id)){
            $tag_ids[] = trim($name_or_id);
        }
        elseif (is_string($name_or_id)) {
            $tag_ids[] = tag_id( $name_or_id );
        }

    }

    $tag_ids_csv = implode(',',$tag_ids);
    $tag_ids_csv = str_replace(' ', '', $tag_ids_csv);
    
    return $tag_ids_csv;
}

/**
 * In a comma separated string of ids or names of tags, replaces all tag ids with their correspoding names
 * 
 * Ex: 
 *  Suppose the DB contains only the following entries in the tags table:
 *      id    name 
 *      10    moodle
 *      12    science
 *      22    education
 * 
 *  tag_name_from_string('mOOdle, 10, HiStOrY, 17, 22')
 *     will return the string 'mOOdle,moodle,HiStOrY,,education'
 * 
 * This is a helper function used by functions of this API to process function arguments ($tag_name_or_id)
 * 
 * @param string $tag_names_or_ids_csv comma separated **normalized** names or ids of tags
 * @return int comma separated ids of the tags 
 */
function tag_name_from_string($tag_names_or_ids_csv) {
    
    $tag_names_or_ids = explode(',', $tag_names_or_ids_csv);

    $tag_names = array();
    foreach ($tag_names_or_ids as $name_or_id) {

        if (is_numeric($name_or_id)){
            $tag_names[] =  tag_name($name_or_id);
        }
        elseif (is_string($name_or_id)) {
            $tag_names[] = trim($name_or_id);
        }

    }

    $tag_names_csv = implode(',',$tag_names);
    
    return $tag_names_csv;
    
}

/**
 * Associates a tag with an item
 * 
 * Ex 1: tag_an_item('user', '1', 'hisTOrY, RELIGIONS, roman' )
 *  This will tag an user whose id is 1 with "history", "religions", "roman"
 *   If the tag names passed do not exist, they will get created.
 * 
 * Ex 2: tag_an_item('user', '1', 'hisTory, 12, 11, roman')
 *   This will tag an user whose id is 1 with 'history', 'roman' and with tags of ids 12 and 11
 * 
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item to be tagged
 * @param string $tag_names_or_ids_csv comma separated tag names (can be unormalized) or ids of existing tags
 * @param string $tag_type type of the tags that are beeing added (optional, default value is "default")
 */

function tag_an_item($item_type, $item_id, $tag_names_or_ids_csv, $tag_type="default") {

    //convert any tag ids passed to their corresponding tag names
    $tag_names_csv = tag_name_from_string($tag_names_or_ids_csv);
    
    //create the tags
    $tags_created_ids = tag_create($tag_names_csv,$tag_type);

    $tag_instance = new StdClass;
    $tag_instance->itemtype = $item_type;
    $tag_instance->itemid = $item_id;

    //create tag instances
    foreach ($tags_created_ids as $tag_id) {
        
        $tag_instance->tagid = $tag_id;
        
        $exists = record_exists('tag_instance', 'tagid', $tag_id, 'itemtype', $item_type, 'itemid', $item_id);
        
        if (!$exists) {
            insert_record('tag_instance',$tag_instance);
        }
    }
   

   // update_tag_correlations($item_type, $item_id);

}


/**
 * Updates the tags associated with an item
 * 
 * Ex 1:
 *  Suppose user 1 is tagged only with "algorithms", "computers" and "software"
 *  By calling update_item_tags('user', 1, 'algorithms, software, mathematics')
 *  User 1 will now be tagged only with "algorithms", "software" and "mathematics"
 * 
 * Ex 2:
 *   update_item_tags('user', '1', 'algorithms, 12, 13')
 *   User 1 will now be tagged only with "algorithms", and with tags of ids 12 and 13
 * 
 * 
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item to be tagged
 * @param string $tag_names_or_ids_csv comma separated tag names (can be unormalized) or ids of existing tags
 * @param string $tag_type type of the tags that are beeing added (optional, default value is "default")
 */

function update_item_tags($item_type, $item_id, $tag_names_or_ids_csv, $tag_type="default") {
    
    //if $tag_names_csv is an empty string, remove all tag associations of the item
    if( empty($tag_names_or_ids_csv) ){
        untag_an_item($item_type, $item_id);
        return;
    }
    
    //convert any tag ids passed to their corresponding tag names
    $tag_names_csv = tag_name_from_string($tag_names_or_ids_csv);    
    
    //associate the tags passed with the item
    tag_an_item($item_type, $item_id, $tag_names_csv, $tag_type );

    //get the ids of the tags passed
    $existing_and_new_tags_ids = tags_id( tag_normalize($tag_names_csv) );

    // delete any tag instance with $item_type and $item_id
    // that are not in $tag_names_csv
    $tags_id_csv = "'" . implode("','", $existing_and_new_tags_ids) . "'" ;

    $select = "
        itemid = '{$item_id}'
    AND
        itemtype = '{$item_type}'
    AND
        tagid NOT IN ({$tags_id_csv})
    ";

    delete_records_select('tag_instance', $select);

}

/**
 * Removes the association of an item with a tag
 * 
 * Ex: untag_an_item('user', '1', 'history, 11, roman' )
 *  The user with id 1 will no longer be tagged with 'history', 'roman' and the tag of id 11
 *   Calling  untag_an_item('user','1')  will remove all tags associated with user 1.
 *    
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item to be untagged
 * @param string $tag_names_or_ids_csv comma separated tag **normalized** names or ids of existing tags (optional, 
 *                                                                  if none is given, all tags of the item will be removed)
 */

function untag_an_item($item_type, $item_id, $tag_names_or_ids_csv='') {
    
    if ($tag_names_or_ids_csv == ""){

        delete_records('tag_instance','itemtype', $item_type, 'itemid', $item_id);

    }
    else {
    
        $tag_ids_csv = tag_id_from_string($norm_tag_names_or_ids_csv);

        $tag_ids_csv_with_apos = "'" . str_replace(',', "','", $tag_ids_csv ) . "'";

        delete_records_select('tag_instance',
        "tagid IN ($tags_id_csv_with_apos) AND itemtype='$item_type' AND itemid='$item_id'");
    }

    //update_tag_correlations($item_type, $item_id);

}

/**
 * Function that gets the tags that are associated with an item
 * 
 * Ex: get_item_tags('user', '1')
 * 
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item beeing queried
 * @param string $fields tag fields to be selected (optional, default is 'id, name, tagtype')
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */

function get_item_tags($item_type, $item_id, $fields='id, name, rawname, tagtype, flag', $limitfrom='', $limitnum='') {
    
    global $CFG;
    
    $fields = 'tg.' . $fields;
    $fields = str_replace(',', ',tg.', $fields);
        
    $query = "
        SELECT
            {$fields}
        FROM 
            {$CFG->prefix}tag_instance ti
        INNER JOIN
            {$CFG->prefix}tag tg
        ON
            tg.id = ti.tagid
        WHERE 
            ti.itemtype = '{$item_type}' AND
            ti.itemid = '{$item_id}'";

    return get_records_sql($query, $limitfrom, $limitnum);

}



/**
 * Function that returns the items of a certain type associated with a certain tag
 * 
 * Ex 1: get_items_tagged_with('user', 'banana')
 * Ex 2: get_items_tagged_with('user', '11')
 * 
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 *      (to avoid field name ambiguity in the query, use the identifier "it" Ex: 'it.name ASC' )
 * @param string $fields a comma separated list of fields to return 
 *   (optional, by default all fields are returned). The first field will be used as key for the
 *   array so must be a unique field such as 'id'. To avoid field name ambiguity in the query, 
 *   use the identifier "it" Ex: 'it.name, it.id' )
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects indexed by their ids, or false if no records were found or an error occured.
 */

function get_items_tagged_with($item_type, $tag_name_or_id, $sort='', $fields='*', $limitfrom='', $limitnum='') {
    
    global $CFG;
   
    $tag_id = tag_id_from_string($tag_name_or_id);

    $fields = 'it.' . $fields;
    $fields = str_replace(',', ',it.', $fields);

    if ($sort) {
        $sort = ' ORDER BY '. $sort;
    } 
       
    $query = "
        SELECT
            {$fields}
        FROM 
            {$CFG->prefix}{$item_type} it
        INNER JOIN
            {$CFG->prefix}tag_instance tt
        ON
            it.id = tt.itemid
        WHERE 
            tt.itemtype = '{$item_type}' AND
            tt.tagid = '{$tag_id}' 
        {$sort}
        ";


    return get_records_sql($query, $limitfrom, $limitnum);

}

/**
 * Returns the number of items tagged with a tag
 *
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @param string $item_type name of the table where the item is stored. Ex: 'user' (optional, if none is set any 
 *                                                                                             type will be counted)
 * @return int the count. If an error occurrs, 0 is returned.
 */
function count_items_tagged_with($tag_name_or_id, $item_type='') {
    
    global $CFG;

    $tag_id = tag_id_from_string($tag_name_or_id);

    if (empty($item_type)){
        $query = "
            SELECT
            COUNT(*) AS count
            FROM 
                {$CFG->prefix}tag_instance tt
            WHERE 
                tagid = {$tag_id}";
    }
    else
    {
        $query = "
            SELECT
            COUNT(*) AS count
            FROM 
                {$CFG->prefix}{$item_type} it
            INNER JOIN
                {$CFG->prefix}tag_instance tt
            ON
                it.id = tt.itemid
            WHERE 
                tt.itemtype = '{$item_type}' AND
                tt.tagid = '{$tag_id}' ";        
    }


    return count_records_sql($query);

}


/**
 * Determines if an item is tagged with a certain tag
 *
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item beeing queried
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @return bool true if a matching record exists, else false.
 */
function is_item_tagged_with($item_type,$item_id, $tag_name_or_id) {

    $tag_id = tag_id_from_string($tag_name_or_id);

    return record_exists('tag_instance','itemtype',$item_type,'itemid',$item_id, 'tagid', $tag_id);
}

/**
 * Search for tags with names that match some text
 *
 * @param string $text string that the tag names will be matched against
 * @param boolean $ordered If true, tags are ordered by their popularity. If false, no ordering.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */

function search_tags($text, $ordered=true, $limitfrom='' , $limitnum='' ) {

    global $CFG;

    $text = tag_normalize($text);

    if ($ordered) {
        $query = "
            SELECT 
                tg.id, tg.name, tg.rawname, COUNT(ti.id) AS count 
            FROM 
                {$CFG->prefix}tag tg
            LEFT JOIN 
                {$CFG->prefix}tag_instance ti
            ON 
                tg.id = ti.tagid
            WHERE 
                tg.name
            LIKE
                '%{$text}%'            
            GROUP BY 
                tg.id 
            ORDER BY 
                count 
            DESC";
    } else {
        $query = "
            SELECT 
                tg.id, tg.name, tg.rawname
            FROM
                {$CFG->prefix}tag tg
            WHERE
                tg.name
            LIKE
                '%{$text}%'
            ";        
    }
        

    return get_records_sql($query, $limitfrom , $limitnum);
        
}

/**
 * Function that returns tags that start with some text
 *
 * @param string $text string that the tag names will be matched against
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function similar_tags($text, $limitfrom='' , $limitnum='' ) {
    
    global $CFG;

    $text = tag_normalize($text);

    $query = "
        SELECT 
            tg.id, tg.name, tg.rawname
        FROM
            {$CFG->prefix}tag tg
        WHERE
            tg.name
        LIKE
            '{$text}%'
        ";

    return get_records_sql($query, $limitfrom , $limitnum);
}

/**
 * Returns tags related to a tag
 * 
 * Related tags of a tag come from two sources: 
 *   - manually added related tags, which are tag_instance entries for that tag
 *   - correlated tags, which are a calculated
 * 
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @param int $limitnum return a subset comprising this many records (optional, default is 10)
 * @return mixed an array of tag objects
 */

function related_tags($tag_name_or_id, $limitnum=10) {

    $tag_id = tag_id_from_string($tag_name_or_id);

    //gets the manually added related tags
    $manual_related_tags = get_item_tags('tag',$tag_id);
    if ($manual_related_tags == false) $manual_related_tags = array();

    //gets the correlated tags
    $automatic_related_tags = correlated_tags($tag_id);
    if ($automatic_related_tags == false) $automatic_related_tags = array();

    $related_tags = array_merge($manual_related_tags,$automatic_related_tags);

    return array_slice( object_array_unique($related_tags) , 0 , $limitnum  ); 


}

/**
 * Returns the correlated tags of a tag
 * The correlated tags are retrieved from the tag_correlation table, which is a caching table.
 *
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @return mixed an array of tag objects
 */
function correlated_tags($tag_name_or_id) {
    
    $tag_id = tag_id_from_string($tag_name_or_id);

    if (!$tag_correlation = get_record('tag_correlation','tagid',$tag_id)) {
        return array();
    }

    $tags_id_csv_with_apos = stripcslashes($tag_correlation->correlatedtags);

    return get_records_select('tag', "id IN ({$tags_id_csv_with_apos})", '', 'id, name, rawname, tagtype');
}

/**
 * Recalculates tag correlations of all the tags associated with an item
 * This function could be called whenever the tags associations with an item changes 
 *  ( for example when tag_an_item() or untag_an_item() is called )
 *
 * @param string $item_type name of the table where the item is stored. Ex: 'user'
 * @param string $item_id id of the item
 */
function update_tag_correlations($item_type, $item_id) {

    $item_tags = get_item_tags($item_type, $item_id);

    foreach ($item_tags as $tag) {
        cache_correlated_tags($tag->id);
    }
}

/**
 * Calculates and stores the correlated tags of a tag.
 * The correlations are stored in the 'tag_correlation' table.
 *
 * Two tags are correlated if they appear together a lot.
 * Ex.: Users tagged with "computers" will probably also be tagged with "algorithms".
 * 
 * The rationale for the 'tag_correlation' table is performance. 
 * It works as a cache for a potentially heavy load query done at the 'tag_instance' table. 
 * So, the 'tag_correlation' table stores redundant information derived from the 'tag_instance' table. 
 *   
 * @param string $tag_name_or_id is a single **normalized** tag name or the id of a tag
 * @param number $min_correlation cutoff percentage (optional, default is 0.25)
 * @param int $limitnum return a subset comprising this many records (optional, default is 10)
 */
function cache_correlated_tags($tag_name_or_id, $min_correlation=0.25, $limitnum=10) {
    
    global $CFG;

    $tag_id = tag_id_from_string($tag_name_or_id);

     // query that counts how many times any tag appears together in items 
     // with the tag passed as argument ($tag_id)
    $query =
    "    SELECT
            tb.tagid , COUNT(*) nr
         FROM 
             {$CFG->prefix}tag_instance ta 
         INNER JOIN 
             {$CFG->prefix}tag_instance tb 
         ON 
             ta.itemid = tb.itemid
         WHERE 
             ta.tagid = {$tag_id}
         GROUP BY 
             tb.tagid  
         ORDER BY 
             nr DESC";

    $tag_correlations = get_records_sql($query, 0, $limitnum);

    $tags_id_csv_with_apos = "'";
    $cutoff = $tag_correlations[$tag_id]->nr * $min_correlation;

    foreach($tag_correlations as $correlation) {
        if($correlation->nr >= $cutoff && $correlation->tagid != $tag_id ){
            $tags_id_csv_with_apos .= $correlation->tagid."','";
        }
    }
    $tags_id_csv_with_apos = substr($tags_id_csv_with_apos,0,-2);


    //saves correlation info in the caching table

    $tag_correlation_obj = get_record('tag_correlation','tagid',$tag_id);

    if ($tag_correlation_obj) {
        $tag_correlation_obj->correlatedtags = addslashes($tags_id_csv_with_apos);
        update_record('tag_correlation',$tag_correlation_obj);
    }
    else {
        $tag_correlation_obj = new StdClass;
        $tag_correlation_obj->tagid = $tag_id;
        $tag_correlation_obj->correlatedtags = addslashes($tags_id_csv_with_apos);
        insert_record('tag_correlation',$tag_correlation_obj);
    }


}

/**
 * This function cleans up the 'tag_instance' table 
 * It removes orphans in 'tag_instances' table
 *
 */
function tag_instance_table_cleanup() {

    global $CFG;
 
    //get the itemtypes present in the 'tag_instance' table
    $query = "
        SELECT
        DISTINCT(itemtype)
        FROM
        {$CFG->prefix}tag_instance
    ";
    
    $items_types = get_records_sql($query);
    
    // for each itemtype, remove tag_instances that are orphans
    // That is: For a given tag_instance, if in the itemtype table there's no entry with id equal to itemid,
    //          then this tag_instance is an orphan and it will be removed.
    foreach ($items_types as $type) {
    
        $query = "
            {$CFG->prefix}tag_instance.id
        IN
            ( SELECT sq1.id 
              FROM 
                    (SELECT sq2.* 
                     FROM {$CFG->prefix}tag_instance sq2
                     LEFT JOIN {$CFG->prefix}{$type->itemtype} item
                     ON sq2.itemid = item.id 
                     WHERE item.id IS NULL 
                     AND sq2.itemtype = '{$type->itemtype}') 
              sq1
            ) ";
        
        delete_records_select('tag_instance', $query);
    }
    
    // remove tag_instances that are orphans because tagid does not correspond to an
    // existing tag
    $query = "
           {$CFG->prefix}tag_instance.id
        IN
           (SELECT sq1.id 
            FROM 
                (SELECT sq2.* 
                 FROM {$CFG->prefix}tag_instance sq2
                 LEFT JOIN {$CFG->prefix}tag tg
                 ON sq2.tagid = tg.id 
                 WHERE tg.id IS NULL ) 
             sq1
        )    
        ";
    
    delete_records_select('tag_instance', $query);
}


/**
 * Function that normalizes a tag name
 * 
 * Ex: tag_normalize('bANAana')   -> returns 'banana'
 *        tag_normalize('lots    of    spaces') -> returns 'lots of spaces'
 *        tag_normalize('%!%!% non alpha numeric %!%!%') -> returns 'non alpha numeric'
 *        tag_normalize('tag one,   TAG TWO, TAG three, and anotheR tag') 
 *                         -> returns 'tag one,tag two,tag three,and another tag' 
 *                     
 * @param string $tag_names_csv unnormalized CSV tag names
 * @return string **normalized** CSV tag names
 */

function tag_normalize($tag_names_csv, $lowercase=true) {

    $tags = explode(',', $tag_names_csv);

    if (sizeof($tags) > 1) {

        foreach ($tags as $key => $tag) {
            $tags[$key] = tag_normalize($tag);
        }

        return implode(',' , $tags);

    }

    // only one tag was passed
    else {
       
        if ($lowercase){
            $value = moodle_strtolower($tag_names_csv);
        }
        else {
            $value = $tag_names_csv;
        }
        
        //$value = preg_replace('|[^\w ]|i', '', strtolower(trim($tag_names_csv)));        
        $value = preg_replace('|[\!\@\#\$\%\^\&\*\(\)\-\+\=\~\`\\"\'\_.\[\]\{\}\:\;\?\´\^\\\/\<\>\|]|i', '', trim($value));

        //removes excess white spaces
        $value = preg_replace('/\s\s+/', ' ', $value);
        
        return $value;
    }

}

function tag_flag_inappropriate($tag_names_or_ids_csv){
    
    $tag_ids_csv = tag_id_from_string($tag_names_or_ids_csv);
    
    $tag_ids = explode(',', $tag_ids_csv);
    
    foreach ($tag_ids as $id){ 
        $tag = get_record('tag','id',$id, '', '', '', '', 'id,flag');
        
        $tag->flag++;
        $tag->timemodified = time();
        
        update_record('tag', $tag);
    }
}

function tag_flag_reset($tag_names_or_ids_csv){
    
    global $CFG;
    
    $tag_ids_csv = tag_id_from_string($tag_names_or_ids_csv);
    
    $tag_ids_csv_with_apos = "'" . str_replace(',', "','", $tag_ids_csv) . "'";
    
    $timemodified = time();
    
    $query = "
        UPDATE
            {$CFG->prefix}tag tg
        SET 
            tg.flag = 0,
            tg.timemodified = {$timemodified}
        WHERE
            tg.id 
        IN
            ({$tag_ids_csv_with_apos})
        ";
        
    execute_sql($query, false);
}

/**
 * Function that returns comma separated HTML links to the tag pages of the tags passed
 *
 * @param array $tag_objects an array of tag objects
 * @return string CSV, HTML links to tag pages
 */

function tag_links_csv($tag_objects) {
    
    global $CFG;
    $tag_links = '';

    if (empty($tag_objects)) {
        return '';
    }

    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
    $can_manage_tags = has_capability('moodle/tag:manage', $systemcontext);
        
    foreach ($tag_objects as $tag){
        //highlight tags that have been flagged as inappropriate for those who can manage them
        $tagname = tag_display_name($tag);
        if ($tag->flag > 0 && $can_manage_tags) {
            $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
        }
        $tag_links .= ' <a href="'.$CFG->wwwroot.'/tag/index.php?id='.$tag->id.'">'.$tagname.'</a>,';
    }

    return rtrim($tag_links, ',');
}

/**
 * Function that returns comma separated names of the tags passed
 * Example of string that might be returned: 'history, wars, greek history'
 * 
 * @param array $tag_objects 
 * @return string CSV tag names
 */

function tag_names_csv($tag_objects) {

    if (empty($tag_objects)) {
        return '';
    }
    
    $tags = array();

    foreach ($tag_objects as $tag){
        $tags[] = tag_display_name($tag);
    }

    return implode(', ', $tags);
}


/**
 * Returns a number of random tags, ordered by their popularity
 *
 * @param int $nr_of_tags number of random tags to be returned
 * @param unknown_type $tag_type
 * @return mixed an array of tag objects with the following fields: id, name and count
 */
function rand_tags_count($nr_of_tags=20, $tag_type = 'default') {
    
    global $CFG;
    
    if (!$tags = get_all_tags($tag_type)) {
        return array();
    }
    
    if(sizeof($tags) < $nr_of_tags) {
        $nr_of_tags = sizeof($tags);
    }
    
    $rndtags = array_rand($tags, $nr_of_tags);
    
    $tags_id_csv_with_apos = "'";
    foreach($rndtags as $tagid) {
        $tags_id_csv_with_apos .= $tags[$tagid]->id . "','";
    }
    $tags_id_csv_with_apos = substr($tags_id_csv_with_apos,0,-2);    

    
    $query = "
        SELECT 
            tg.id, tg.name, tg.rawname, COUNT(ti.id) AS count, tg.flag 
        FROM 
            {$CFG->prefix}tag_instance ti 
        INNER JOIN 
            {$CFG->prefix}tag tg 
        ON 
            tg.id = ti.tagid
        WHERE 
            ti.tagid
        IN 
            ({$tags_id_csv_with_apos}) 
        GROUP BY 
            tagid 
        ORDER BY 
            count 
        ASC";
    
    return get_records_sql($query);
    
    
}

/*-------------------- Printing functions -------------------- */

/**
 * Prints a box that contains the management links of a tag
 *
 * @param $tag_object
 */

function print_tag_management_box($tag_object) {

    global $USER, $CFG;

    $tagname  = tag_display_name($tag_object);

    print_box_start('box','tag-management-box');

    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);

    $addtaglink = '';
    if ( has_capability('moodle/tag:manage',$systemcontext) ) {
        $manage_link =  "<a href=\"{$CFG->wwwroot}/tag/manage.php\">" . get_string('managetags', 'tag') . "</a>" ;
        echo $manage_link .' | ';
    }

    // if the user is not tagged with the $tag_object tag, a link "add blahblah to my interests" will appear
    if( !is_item_tagged_with('user', $USER->id, $tag_object->id )) {
        $addtaglink = '<a href="' . $CFG->wwwroot . '/user/tag.php?action=addinterest&amp;id='. $tag_object->id .'">';
        $addtaglink .= get_string('addtagtomyinterests','tag',$tagname). '</a>';
        echo $addtaglink .' | ';
    }

    // only people with moodle/tag:edit capability may edit the tag description
    if ( has_capability('moodle/tag:edit',$systemcontext) && is_item_tagged_with('user', $USER->id, $tag_object->id ) ) {
        echo ' <a href="'. $CFG->wwwroot . '/tag/edit.php?id='.$tag_object->id .'">'.get_string('edittag', 'tag').'</a> | ';
    }

    // flag as inappropriate link
    $flagtaglink = '<a href="' . $CFG->wwwroot . '/user/tag.php?action=flaginappropriate&amp;id='. $tag_object->id .'">';
    $flagtaglink .= get_string('flagasinappropriate','tag',$tagname). '</a>';
    echo $flagtaglink;

    print_box_end();

}

/**
 * Prints a box with the description of a tag and its related tags
 *
 * @param unknown_type $tag_object
 */

function print_tag_description_box($tag_object) {
    
    global $USER, $CFG;

    $tagname  = tag_display_name($tag_object);
    $related_tags =  related_tags($tag_object->id); //get_item_tags('tags',$tag_object->id);

    print_box_start('generalbox', 'tag-description');

    print_tag_management_box($tag_object);
    
    if (!empty($tag_object->description)) {
        echo format_text($tag_object->description, $tag_object->descriptionformat );
    }
    else {
        echo format_text(get_string('thistaghasnodesc','tag'));
    }


    if ($related_tags) {
        echo '<br/><br/><b>'.get_string('relatedtags','tag').': </b>' . tag_links_csv($related_tags);
    }

    print_box_end();
}

/**
 * Prints a table of the users tagged with the tag passed as argument
 *
 * @param $tag_object
 * @param int $users_per_row number of users per row to display
 * @param int $limitfrom prints users starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum prints this many users (optional, required if $limitfrom is set).
 */

function print_tagged_users_table($tag_object, $limitfrom='' , $limitnum='') {

    //List of users with this tag
    $userlist = array_values( get_items_tagged_with(
                                        'user',
                                        $tag_object->id,
                                        'lastaccess DESC' ,
                                        'id, firstname, lastname, picture',
                                        $limitfrom,
                                        $limitnum) );

    //user table box
    print_box_start('generalbox', 'tag-user-table');

        print_user_list($userlist);

    print_box_end();
    //end table box



}

/**
 *  Prints a list of users
 *
 * @param array $userlist an array of user objects
 */
function print_user_list($userlist) {
    
    foreach ($userlist as $user){
        print_user_box( $user );
    }

    
}

/**
 * Prints an individual user box
 *
 * @param $user user object (contains the following fields: id, firstname, lastname and picture)
 */
function print_user_box($user) {
    
    global $CFG;

    $usercontext = get_context_instance(CONTEXT_USER, $user->id);

    $profilelink = '';
    if ( has_capability('moodle/user:viewdetails', $usercontext) ) {
        $profilelink = $CFG->wwwroot.'/user/view.php?id='.$user->id;
    }

    print_box_start('user-box', $user->id);

        if (!empty($profilelink)) echo '<a href="'.$profilelink.'">';
    
        //print user image
        if ($user->picture) {
            echo '<img class="user-image" src="'. $CFG->wwwroot .'/user/pix.php/'. $user->id .'/f1.jpg"'.'/>';
        }
        else {
            echo '<img class="user-image" src="'. $CFG->wwwroot .'/pix/u/f1.png"'.'/>';           
        }
    
        echo '<br/>';
        
        if (!empty($profilelink)) echo '</a>';
    
        $fullname = fullname($user);
        //truncate name if it's too big
        if (strlen($fullname) > 26) $fullname = substr($fullname,0,26) . '...';
    
        echo '<strong>' . $fullname . '</strong>';

    print_box_end();

}

/**
 * Prints the tag search box
 *
 */
function print_tag_search_box($search='') {
    
    global $CFG;
    
    print_box_start('','tag-search-box');
    
    echo '<form action="'.$CFG->wwwroot.'/tag/search.php" style="display:inline">';
    echo '<div>';
    echo '<input id="searchform_search" name="query" type="text" size="40" />';
    echo '<button id="searchform_button" type="submit">'. get_string('search', 'tag') .'</button><br />';
    echo '</div>';
    echo '</form>';
    
    print_box_end();
}

/**
 * Prints the tag search results
 *
 * @param string $query text that tag names will be matched against
 * @param int $page current page
 * @param int $perpage nr of users displayed per page
 */
function print_tag_search_results($query,  $page, $perpage) {

    global $CFG, $USER;
    
    $count = sizeof( search_tags($query,false) );
    $tags = array_values(search_tags($query, true,  $page * $perpage , $perpage));
    
    $baseurl = $CFG->wwwroot.'/tag/search.php?query=' . $query;

    // link "Add $query to my interests"
    $addtaglink = '';
    if( !is_item_tagged_with('user', $USER->id, $query )) {
        $addtaglink = '<a href="' . $CFG->wwwroot . '/user/tag.php?action=addinterest&name='. $query .'">';
        $addtaglink .= get_string('addtagtomyinterests','tag',$query). '</a>';
    }

        
    if($tags) { // there are results to display!!
        
        print_heading(get_string('searchresultsfor', 'tag', $query) . " : {$count}", '', 3);
    
        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            print_box($addtaglink,'box','tag-management-box');
        }

        $nr_of_lis_per_ul = 6;
        $nr_of_uls = ceil( sizeof($tags) / $nr_of_lis_per_ul);
        
        echo '<ul id="tag-search-results">';
            for($i = 0; $i < $nr_of_uls; $i++) {
                echo '<li>';
                foreach (array_slice($tags, $i * $nr_of_lis_per_ul, $nr_of_lis_per_ul ) as $tag) {
                    $tag_link = ' <a href="'.$CFG->wwwroot.'/tag/index.php?id='.$tag->id.'">'.tag_display_name($tag).'</a>';
                    echo '&#8226;' . $tag_link . '<br/>';
                }
                echo '</li>';        
            }
        echo '</ul>';
        echo '<div>&nbsp;</div>'; // <-- small layout hack in order to look good in Firefox
        
        print_paging_bar($count, $page, $perpage, $baseurl.'&amp;', 'page');
    }
    else { //no results were found!!
        
        print_heading(get_string('noresultsfor', 'tag', $query), '', 3);

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            print_box($addtaglink,'box','tag-management-box');
        }
                
    }

  
}

/**
 * Prints a tag cloud
 *
 * @param array $tagcloud array of tag objects (fields: id, name, rawname, count and flag)
 * @param boolean $shuffle wether or not to shuffle the array passed
 * @param int $max_size maximum text size, in percentage
 * @param int $min_size minimum text size, in percentage
 */
function print_tag_cloud($tagcloud, $shuffle=true, $max_size=180, $min_size=80) {
    
    global $CFG;

    if (empty($tagcloud)) {
        return;
    }
   
    if ( $shuffle ) {
        shuffle($tagcloud);
    }
    
    $count = array();
    foreach ($tagcloud as $key => $value){
        if(!empty($value->count)) {
            $count[$key] = log10($value->count);
        }
        else{
            $count[$key] = 0;
        }
    }
    
    $max = max($count);
    $min = min($count);
    
    $spread = $max - $min;
    if (0 == $spread) { // we don't want to divide by zero
        $spread = 1;
    }
    
    $step = ($max_size - $min_size)/($spread);

    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
    $can_manage_tags = has_capability('moodle/tag:manage', $systemcontext);
  
    //prints the tag cloud
    echo '<ul id="tag-cloud-list">';
    foreach ($tagcloud as $key => $tag) {
    
        $size = $min_size + ((log10($tag->count) - $min) * $step);
        $size = ceil($size);

        $style = 'style="font-size: '.$size.'%"';
        $title = 'title="'.s(get_string('thingstaggedwith','tag', $tag)).'"';
        $href = 'href="'.$CFG->wwwroot.'/tag/index.php?id='.$tag->id.'"';

        //highlight tags that have been flagged as inappropriate for those who can manage them
        $tagname = tag_display_name($tag);
        if ($tag->flag > 0 && $can_manage_tags) {
            $tagname =  '<span class="flagged-tag">' . tag_display_name($tag) . '</span>';
        }
        
        $tag_link = '<li><a '.$href.' '.$title.' '. $style .'>'.$tagname.'</a></li> ';
        
        echo $tag_link;
        
    }    
    echo '</ul>';
    
}

function print_tag_management_list($perpage='100') {

    global $CFG, $USER;
    require_once($CFG->libdir.'/tablelib.php');
    
    //setup table
    
    $tablecolumns = array('id','name', 'owner', 'count', 'flag', 'timemodified', '');
    $tableheaders = array(  get_string('id' , 'tag'),
                            get_string('name' , 'tag'),
                            get_string('owner','tag'),
                            get_string('count','tag'),
                            get_string('flag','tag'),
                            get_string('timemodified','tag'),
                            get_string('select', 'tag')
                            );    

    $table = new flexible_table('tag-management-list-'.$USER->id);
    
    $baseurl = $CFG->wwwroot.'/tag/manage.php';
    
    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl);
    
    $table->sortable(true, 'flag', SORT_DESC);

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'tag-management-list');
    $table->set_attribute('class', 'generaltable generalbox');

    $table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));
    
    $table->setup();    
    
    if ($table->get_sql_sort()) {
        $sort = ' ORDER BY '.$table->get_sql_sort();
    } else {
        $sort = '';
    }

    if ($table->get_sql_where()) {
        $where = 'WHERE '.$table->get_sql_where();
    } else {
        $where = '';
    }    
            
    $query = "
        SELECT 
            tg.id, tg.name, tg.rawname, COUNT(ti.id) AS count, u.id AS owner, tg.flag, tg.timemodified    
        FROM 
            {$CFG->prefix}tag_instance ti 
        RIGHT JOIN 
            {$CFG->prefix}tag tg 
        ON 
            tg.id = ti.tagid
        LEFT JOIN
            {$CFG->prefix}user u
        ON
            tg.userid = u.id
        {$where}
        GROUP BY 
            tg.id 
        {$sort}
        ";
            
    $totalcount = count_records('tag');

    $table->initialbars($totalcount > $perpage);
    $table->pagesize($perpage, $totalcount);


    echo '<form id="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">';
            
    //retrieve tags from DB    
    if ($tagrecords = get_records_sql($query, $table->get_page_start(),  $table->get_page_size())) {
   
        $taglist = array_values($tagrecords);
    
        //print_tag_cloud(array_values(get_records_sql($query)), false);
    
        //populate table with data
        foreach ($taglist as $tag ){
            
            $id             =   $tag->id;
            $name           =   '<a href="'.$CFG->wwwroot.'/tag/index.php?id='.$tag->id.'">'. tag_display_name($tag) .'</a>'; 
            $owner          =   '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$tag->owner.'">' . $tag->owner . '</a>';
            $count          =   $tag->count;
            $flag           =   $tag->flag;
            $timemodified   =   format_time(time() - $tag->timemodified);
            $checkbox       =   '<input type="checkbox" name="tagschecked[]" value="'.$tag->id.'" />';
            
            //if the tag if flagged, highlight it
            if ($tag->flag > 0) {
                $id = '<span class="flagged-tag">' . $id . '</span>';
                $name = '<span class="flagged-tag">' . $name . '</span>';
                $owner = '<span class="flagged-tag">' . $owner . '</span>';
                $count = '<span class="flagged-tag">' . $count . '</span>';
                $flag = '<span class="flagged-tag">' . $flag . '</span>';
                $timemodified = '<span class="flagged-tag">' . $timemodified . '</span>';
            }
    
            $data = array($id, $name , $owner ,$count ,$flag, $timemodified, $checkbox);
                 
            $table->add_data($data);       
        }
    

        echo '<input type="button" onclick="checkall()" value="'.get_string('selectall').'" /> ';
        echo '<input type="button" onclick="checknone()" value="'.get_string('deselectall').'" /> ';
        echo '<br/><br/>';
        echo '<select id="menuformaction" name="action">
                    <option value="" selected="selected">With selected tags...</option>
                    <option value="reset">'. get_string('resetflag', 'tag') .'</option>
                    <option value="delete">'. get_string('delete', 'tag') .'</option>
                </select>';
    
        echo '<button id="tag-management-submit" type="submit">'. get_string('ok') .'</button>';
    
    }

    $table->print_html();

    echo '</form>';
}

?>
