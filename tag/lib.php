<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle tag library
 *
 * Tag strings : you can use any character in tags, except the comma (which is
 * the separator) and the '\' (backslash).  Note that many spaces (or other
 * blank characters) will get "compressed" into one. A tag string is always a
 * rawurlencode'd string. This is the same behavior as http://del.icio.us.
 *
 * A "record" is a php array (note that an object will work too) that contains
 * the following variables :
 *  - type: the table containing the record that we are tagging (eg: for a
 *    blog, this is table 'post', and for a user it is 'user')
 *  - id: the id of the record
 *
 * TODO: turn this into a full-fledged categorization system. This could start
 * by modifying (removing, probably) the 'tag type' to use another table
 * describing the relationship between tags (parents, sibling, etc.), which
 * could then be merged with the 'course categorization' system...
 *
 * BASIC INSTRUCTIONS :
 *  - to "tag a blog post" (for example):
 *        tag_set('post', $blog_post->id, $array_of_tags);
 *
 *  - to "remove all the tags on a blog post":
 *        tag_set('post', $blog_post->id, array());
 *
 * Tag set will create tags that need to be created.
 *
 * @package    core
 * @subpackage tag
 * @see http://www.php.net/manual/en/function.urlencode.php
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('TAG_RETURN_ARRAY', 0);
define('TAG_RETURN_OBJECT', 1);
define('TAG_RETURN_TEXT', 2);
define('TAG_RETURN_HTML', 3);

define('TAG_CASE_LOWER', 0);
define('TAG_CASE_ORIGINAL', 1);

define('TAG_RELATED_ALL', 0);
define('TAG_RELATED_MANUAL', 1);
define('TAG_RELATED_CORRELATED', 2);

///////////////////////////////////////////////////////
/////////////////// PUBLIC TAG API ////////////////////

/// Functions for settings tags  //////////////////////

/**
 * Set the tags assigned to a record.  This overwrites the current tags.
 *
 * This function is meant to be fed the string coming up from the user
 * interface, which contains all tags assigned to a record.
 *
 * @param string $record_type the type of record to tag ('post' for blogs,
 *     'user' for users, 'tag' for tags, etc.
 * @param int $record_id the id of the record to tag
 * @param array $tags the array of tags to set on the record. If
 *     given an empty array, all tags will be removed.
 * @return void
 */
function tag_set($record_type, $record_id, $tags) {

    static $in_recursion_semaphore = false; // this is to prevent loops when tagging a tag
    if ( $record_type == 'tag' && !$in_recursion_semaphore) {
        $current_tagged_tag_name = tag_get_name($record_id);
    }

    $tags_ids = tag_get_id($tags, TAG_RETURN_ARRAY); // force an array, even if we only have one tag.
    $cleaned_tags = tag_normalize($tags);
    //echo 'tags-in-tag_set'; var_dump($tags); var_dump($tags_ids); var_dump($cleaned_tags);

    $current_ids = tag_get_tags_ids($record_type, $record_id);
    //var_dump($current_ids);

    // for data coherence reasons, it's better to remove deleted tags
    // before adding new data: ordering could be duplicated.
    foreach($current_ids as $current_id) {
        if (!in_array($current_id, $tags_ids)) {
            tag_delete_instance($record_type, $record_id, $current_id);
            if ( $record_type == 'tag' && !$in_recursion_semaphore) {
                // if we are removing a tag-on-a-tag (manually related tag),
                // we need to remove the opposite relationship as well.
                tag_delete_instance('tag', $current_id, $record_id);
            }
        }
    }

    if (empty($tags)) {
        return true;
    }

    foreach($tags as $ordering => $tag) {
        $tag = trim($tag);
        if (!$tag) {
            continue;
        }

        $clean_tag = $cleaned_tags[$tag];
        $tag_current_id = $tags_ids[$clean_tag];

        if ( is_null($tag_current_id) ) {
            // create new tags
            //echo "call to add tag $tag\n";
            $new_tag = tag_add($tag);
            $tag_current_id = $new_tag[$clean_tag];
        }

        tag_assign($record_type, $record_id, $tag_current_id, $ordering);

        // if we are tagging a tag (adding a manually-assigned related tag), we
        // need to create the opposite relationship as well.
        if ( $record_type == 'tag' && !$in_recursion_semaphore) {
            $in_recursion_semaphore = true;
            tag_set_add('tag', $tag_current_id, $current_tagged_tag_name);
            $in_recursion_semaphore = false;
        }
    }
}

/**
 * Adds a tag to a record, without overwriting the current tags.
 *
 * @param string $record_type the type of record to tag ('post' for blogs,
 *     'user' for users, etc.
 * @param int $record_id the id of the record to tag
 * @param string $tag the tag to add
 * @return void
 */
function tag_set_add($record_type, $record_id, $tag) {

    $new_tags = array();
    foreach( tag_get_tags($record_type, $record_id) as $current_tag ) {
        $new_tags[] = $current_tag->rawname;
    }
    $new_tags[] = $tag;

    return tag_set($record_type, $record_id, $new_tags);
}

/**
 * Removes a tag from a record, without overwriting other current tags.
 *
 * @param string $record_type the type of record to tag ('post' for blogs,
 *     'user' for users, etc.
 * @param int $record_id the id of the record to tag
 * @param string $tag the tag to delete
 * @return void
 */
function tag_set_delete($record_type, $record_id, $tag) {

    $new_tags = array();
    foreach( tag_get_tags($record_type, $record_id) as $current_tag ) {
        if ($current_tag->name != $tag) {  // Keep all tags but the one specified
            $new_tags[] = $current_tag->name;
        }
    }

    return tag_set($record_type, $record_id, $new_tags);
}

/**
 * Set the type of a tag.  At this time (version 1.9) the possible values
 * are 'default' or 'official'.  Official tags will be displayed separately "at
 * tagging time" (while selecting the tags to apply to a record).
 *
 * @param string $tagid tagid to modify
 * @param string $type either 'default' or 'official'
 * @return true on success, false otherwise
 */
function tag_type_set($tagid, $type) {
    global $DB;

    if ($tag = $DB->get_record('tag', array('id'=>$tagid), 'id')) {
        $tag->tagtype = $type;
        $tag->timemodified = time();
        return $DB->update_record('tag', $tag);
    }
    return false;
}


/**
 * Set the description of a tag
 *
 * @param int $tagid the id of the tag
 * @param string $description the description
 * @param int $descriptionformat the moodle text format of the description
 * @return true on success, false otherwise
 */
function tag_description_set($tagid, $description, $descriptionformat) {
    global $DB;

    if ($tag = $DB->get_record('tag', array('id'=>$tagid),'id')) {
        $tag->description = $description;
        $tag->descriptionformat = $descriptionformat;
        $tag->timemodified = time();
        return $DB->update_record('tag', $tag);
    }
    return false;
}






/// Functions for getting information about tags //////

/**
 * Simple function to just return a single tag object when you know the name or something
 *
 * @param string $field which field do we use to identify the tag: id, name or rawname
 * @param string $value the required value of the aforementioned field
 * @param string $returnfields which fields do we want returned?
 * @return tag object
 *
 **/
function tag_get($field, $value, $returnfields='id, name, rawname') {
    global $DB;

    if ($field == 'name') {
        $value = moodle_strtolower($value);   // To cope with input that might just be wrong case
    }
    return $DB->get_record('tag', array($field=>$value), $returnfields);
}


/**
 * Get the array of db record of tags associated to a record (instances).  Use
 * tag_get_tags_csv to get the same information in a comma-separated string.
 *
 * @param string $record_type the record type for which we want to get the tags
 * @param int $record_id the record id for which we want to get the tags
 * @param string $type the tag type (either 'default' or 'official'). By default,
 *     all tags are returned.
 * @param int $userid optional only required for course tagging
 * @return array the array of tags
 */
function tag_get_tags($record_type, $record_id, $type=null, $userid=0) {
    global $CFG, $DB;

    $params = array();

    if ($type) {
        $sql_type = "AND tg.tagtype = :type";
        $params['type'] = $type;
    } else {
        $sql_type = '';
    }

   $u = null;
    if ($userid) {
        $u =  "AND ti.tiuserid = :userid ";
        $params['userid'] = $userid;
    }

    $sql = "SELECT tg.id, tg.tagtype, tg.name, tg.rawname, tg.flag, ti.ordering
              FROM {tag_instance} ti JOIN {tag} tg ON tg.id = ti.tagid
              WHERE ti.itemtype = :recordtype AND ti.itemid = :recordid $u $sql_type
           ORDER BY ti.ordering ASC";
    $params['recordtype'] = $record_type;
    $params['recordid']   = $record_id;

    // if the fields in this query are changed, you need to do the same changes in tag_get_correlated_tags
    return $DB->get_records_sql($sql, $params);
    // This version of the query, reversing the ON clause, "correctly" returns
    // a row with NULL values for instances that are still in the DB even though
    // the tag has been deleted.  This shouldn't happen, but if it did, using
    // this query could help "clean it up".  This causes bugs at this time.
    //$tags = $DB->get_records_sql("SELECT ti.tagid, tg.tagtype, tg.name, tg.rawname, tg.flag, ti.ordering ".
    //    "FROM {tag_instance} ti LEFT JOIN {tag} tg ON ti.tagid = tg.id ".
    //    "WHERE ti.itemtype = '{$record_type}' AND ti.itemid = '{$record_id}' {$type} ".
    //    "ORDER BY ti.ordering ASC");
}

/**
 * Get the array of tags display names, indexed by id.
 *
 * @param string $record_type the record type for which we want to get the tags
 * @param int $record_id the record id for which we want to get the tags
 * @param string $type the tag type (either 'default' or 'official'). By default,
 *     all tags are returned.
 * @return array the array of tags (with the value returned by tag_display_name), indexed by id
 */
function tag_get_tags_array($record_type, $record_id, $type=null) {
    $tags = array();
    foreach(tag_get_tags($record_type, $record_id, $type) as $tag) {
        $tags[$tag->id] = tag_display_name($tag);
    }
    return $tags;
}

/**
 * Get a comma-separated string of tags associated to a record.  Use tag_get_tags
 * to get the same information in an array.
 *
 * @param string $record_type the record type for which we want to get the tags
 * @param int $record_id the record id for which we want to get the tags
 * @param int $html either TAG_RETURN_HTML or TAG_RETURN_TEXT, depending
 *     on the type of output desired
 * @param string $type either 'official' or 'default', if null, all tags are
 *     returned
 * @return string the comma-separated list of tags.
 */
function tag_get_tags_csv($record_type, $record_id, $html=TAG_RETURN_HTML, $type=null) {
    global $CFG;

    $tags_names = array();
    foreach(tag_get_tags($record_type, $record_id, $type) as $tag) {
        if ($html == TAG_RETURN_TEXT) {
            $tags_names[] = tag_display_name($tag, TAG_RETURN_TEXT);
        } else { // TAG_RETURN_HTML
            $tags_names[] = '<a href="'. $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name) .'">'. tag_display_name($tag) .'</a>';
        }
    }
    return implode(', ', $tags_names);
}

/**
 * Get an array of tag ids associated to a record.
 *
 * @param string $record_type the record type for which we want to get the tags
 * @param int $record_id the record id for which we want to get the tags
 * @return array of tag ids, indexed and sorted by 'ordering'
 */
function tag_get_tags_ids($record_type, $record_id) {

    $tag_ids = array();
    foreach (tag_get_tags($record_type, $record_id) as $tag) {
        if ( array_key_exists($tag->ordering, $tag_ids) ) {
            // until we can add a unique constraint, in table tag_instance,
            // on (itemtype, itemid, ordering), this is needed to prevent a bug
            // TODO : modify database in 2.0
            $tag->ordering++;
        }
        $tag_ids[$tag->ordering] = $tag->id;
    }
    ksort($tag_ids);
    return $tag_ids;
}

/**
 * Returns the database ID of a set of tags.
 *
 * @param mixed $tags one tag, or array of tags, to look for.
 * @param bool $return_value specify the type of the returned value. Either
 *     TAG_RETURN_OBJECT, or TAG_RETURN_ARRAY (default). If TAG_RETURN_ARRAY
 *     is specified, an array will be returned even if only one tag was
 *     passed in $tags.
 * @return mixed tag-indexed array of ids (or objects, if second parameter is
 *     TAG_RETURN_OBJECT), or only an int, if only one tag is given *and* the
 *     second parameter is null. No value for a key means the tag wasn't found.
 */
function tag_get_id($tags, $return_value=null) {
    global $CFG, $DB;

    static $tag_id_cache = array();

    $return_an_int = false;
    if (!is_array($tags)) {
        if(is_null($return_value) || $return_value == TAG_RETURN_OBJECT) {
            $return_an_int = true;
        }
        $tags = array($tags);
    }

    $result = array();

    //TODO: test this and see if it helps performance without breaking anything
    //foreach($tags as $key => $tag) {
    //    $clean_tag = moodle_strtolower($tag);
    //    if ( array_key_exists($clean_tag), $tag_id_cache) ) {
    //        $result[$clean_tag] = $tag_id_cache[$clean_tag];
    //        $tags[$key] = ''; // prevent further processing for this one.
    //    }
    //}

    $tags = array_values(tag_normalize($tags));
    foreach($tags as $key => $tag) {
        $tags[$key] = moodle_strtolower($tag);
        $result[moodle_strtolower($tag)] = null; // key must exists : no value for a key means the tag wasn't found.
    }

    if (empty($tags)) {
        return array();
    }

    list($tag_string, $params) = $DB->get_in_or_equal($tags);

    $rs = $DB->get_recordset_sql("SELECT * FROM {tag} WHERE name $tag_string ORDER BY name", $params);
    foreach ($rs as $record) {
        if ($return_value == TAG_RETURN_OBJECT) {
            $result[$record->name] = $record;
        } else { // TAG_RETURN_ARRAY
            $result[$record->name] = $record->id;
        }
    }
    $rs->close();

    if ($return_an_int) {
        return array_pop($result);
    }

    return $result;
}


/**
 * Returns tags related to a tag
 *
 * Related tags of a tag come from two sources:
 *   - manually added related tags, which are tag_instance entries for that tag
 *   - correlated tags, which are a calculated
 *
 * @param string $tag_name_or_id is a single **normalized** tag name or the id
 *     of a tag
 * @param int $type the function will return either manually
 *     (TAG_RELATED_MANUAL) related tags or correlated (TAG_RELATED_CORRELATED)
 *     tags. Default is TAG_RELATED_ALL, which returns everything.
 * @param int $limitnum return a subset comprising this many records (optional,
 *     default is 10)
 * @return array an array of tag objects
 */
function tag_get_related_tags($tagid, $type=TAG_RELATED_ALL, $limitnum=10) {

    $related_tags = array();

    if ( $type == TAG_RELATED_ALL || $type == TAG_RELATED_MANUAL) {
        //gets the manually added related tags
        $related_tags = tag_get_tags('tag', $tagid);
    }

    if ( $type == TAG_RELATED_ALL || $type == TAG_RELATED_CORRELATED ) {
        //gets the correlated tags
        $automatic_related_tags = tag_get_correlated($tagid, $limitnum);
        if (is_array($automatic_related_tags)) {
            $related_tags = array_merge($related_tags, $automatic_related_tags);
        }
    }

    return array_slice(object_array_unique($related_tags), 0 , $limitnum);
}

/**
 * Get a comma-separated list of tags related to another tag.
 *
 * @param array $related_tags the array returned by tag_get_related_tags
 * @param int $html either TAG_RETURN_HTML (default) or TAG_RETURN_TEXT : return html links, or just text.
 * @return string comma-separated list
 */
function tag_get_related_tags_csv($related_tags, $html=TAG_RETURN_HTML) {
    global $CFG;

    $tags_names = array();
    foreach($related_tags as $tag) {
        if ( $html == TAG_RETURN_TEXT) {
            $tags_names[] = tag_display_name($tag, TAG_RETURN_TEXT);
        } else {
            // TAG_RETURN_HTML
            $tags_names[] = '<a href="'. $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name) .'">'. tag_display_name($tag) .'</a>';
        }
    }

    return implode(', ', $tags_names);
}

/**
 * Change the "value" of a tag, and update the associated 'name'.
 *
 * @param int $tagid the id of the tag to modify
 * @param string $newtag the new rawname
 * @return bool true on success, false otherwise
 */
function tag_rename($tagid, $newrawname) {
    global $DB;

    if (! $newrawname_clean = array_shift(tag_normalize($newrawname, TAG_CASE_ORIGINAL)) ) {
        return false;
    }

    if (! $newname_clean = moodle_strtolower($newrawname_clean)) {
        return false;
    }

    // Prevent the rename if a tag with that name already exists
    if ($existing = tag_get('name', $newname_clean, 'id, name, rawname')) {
        if ($existing->id != $tagid) {  // Another tag already exists with this name
            return false;
        }
    }

    if ($tag = tag_get('id', $tagid, 'id, name, rawname')) {
        $tag->rawname      = $newrawname_clean;
        $tag->name         = $newname_clean;
        $tag->timemodified = time();
        return $DB->update_record('tag', $tag);
    }
    return false;
}


/**
 * Delete one or more tag, and all their instances if there are any left.
 *
 * @param mixed $tagids one tagid (int), or one array of tagids to delete
 * @return bool true on success, false otherwise
 */
function tag_delete($tagids) {
    global $DB;

    if (!is_array($tagids)) {
        $tagids = array($tagids);
    }

    $success = true;
    $context = get_context_instance(CONTEXT_SYSTEM);
    foreach( $tagids as $tagid ) {
        if (is_null($tagid)) { // can happen if tag doesn't exists
            continue;
        }
        // only delete the main entry if there were no problems deleting all the
        // instances - that (and the fact we won't often delete lots of tags)
        // is the reason for not using $DB->delete_records_select()
        if ($DB->delete_records('tag_instance', array('tagid'=>$tagid)) ) {
            $success &= (bool) $DB->delete_records('tag', array('id'=>$tagid));
            // Delete all files associated with this tag
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'tag', 'description', $tagid);
            foreach ($files as $file) {
                $file->delete();
            }
        }
    }

    return $success;
}

/**
 * Delete one instance of a tag.  If the last instance was deleted, it will
 * also delete the tag, unless its type is 'official'.
 *
 * @param string $record_type the type of the record for which to remove the instance
 * @param int $record_id the id of the record for which to remove the instance
 * @param int $tagid the tagid that needs to be removed
 * @return bool true on success, false otherwise
 */
function tag_delete_instance($record_type, $record_id, $tagid) {
    global $CFG, $DB;

    if ($DB->delete_records('tag_instance', array('tagid'=>$tagid, 'itemtype'=>$record_type, 'itemid'=>$record_id))) {
        if (!$DB->record_exists_sql("SELECT * ".
                                      "FROM {tag} tg ".
                                     "WHERE tg.id = ? AND ( tg.tagtype = 'official' OR ".
                                        "EXISTS (SELECT 1
                                                   FROM {tag_instance} ti
                                                  WHERE ti.tagid = ?) )",
                                     array($tagid, $tagid))) {
            return tag_delete($tagid);
        }
    } else {
        return false;
    }

    return true;
}


/**
 * Function that returns the name that should be displayed for a specific tag
 *
 * @param object $tag_object a line out of tag table, as returned by the adobd functions
 * @param int $html TAG_RETURN_HTML (default) will return htmlspecialchars encoded string, TAG_RETURN_TEXT will not encode.
 * @return string
 */
function tag_display_name($tagobject, $html=TAG_RETURN_HTML) {

    global $CFG;

    if (!isset($tagobject->name)) {
        return '';
    }

    if (empty($CFG->keeptagnamecase)) {
        //this is the normalized tag name
        $textlib = textlib_get_instance();
        $tagname = $textlib->strtotitle($tagobject->name);
    } else {
        //original casing of the tag name
        $tagname = $tagobject->rawname;
    }

    // clean up a bit just in case the rules change again
    $tagname = clean_param($tagname, PARAM_TAG);

    if ($html == TAG_RETURN_TEXT) {
        return $tagname;
    } else { // TAG_RETURN_HTML
        return htmlspecialchars($tagname);
    }
}

/**
 * Find all records tagged with a tag of a given type ('post', 'user', etc.)
 *
 * @param string $tag tag to look for
 * @param string $type type to restrict search to.  If null, every matching
 *     record will be returned
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return array of matching objects, indexed by record id, from the table containing the type requested
 */
function tag_find_records($tag, $type, $limitfrom='', $limitnum='') {
    global $CFG, $DB;

    if (!$tag || !$type) {
        return array();
    }

    $tagid = tag_get_id($tag);

    $query = "SELECT it.*
                FROM {".$type."} it INNER JOIN {tag_instance} tt ON it.id = tt.itemid
               WHERE tt.itemtype = ? AND tt.tagid = ?";
    $params = array($type, $tagid);

    return $DB->get_records_sql($query, $params, $limitfrom, $limitnum);
}




///////////////////////////////////////////////////////
/////////////////// PRIVATE TAG API ///////////////////

/**
 * Adds one or more tag in the database.  This function should not be called
 * directly : you should use tag_set.
 *
 * @param mixed $tags one tag, or an array of tags, to be created
 * @param string $type type of tag to be created ("default" is the default
 *     value and "official" is the only other supported value at this time). An
 *     official tag is kept even if there are no records tagged with it.
 * @return an array of tags ids, indexed by their lowercase normalized names.
 *     Any boolean false in the array indicates an error while adding the tag.
 */
function tag_add($tags, $type="default") {
    global $USER, $DB;

    if (!is_array($tags)) {
        $tags = array($tags);
    }

    $tag_object = new StdClass;
    $tag_object->tagtype      = $type;
    $tag_object->userid       = $USER->id;
    $tag_object->timemodified = time();

    $clean_tags = tag_normalize($tags, TAG_CASE_ORIGINAL);

    $tags_ids = array();
    foreach($clean_tags as $tag) {
        $tag = trim($tag);
        if (!$tag) {
            $tags_ids[$tag] = false;
        } else {
            // note that the difference between rawname and name is only
            // capitalization : the rawname is NOT the same at the rawtag.
            $tag_object->rawname = $tag;
            $tag_name_lc         = moodle_strtolower($tag);
            $tag_object->name    = $tag_name_lc;
            //var_dump($tag_object);
            $tags_ids[$tag_name_lc] = $DB->insert_record('tag', $tag_object);
        }
    }

    return $tags_ids;
}

/**
 * Assigns a tag to a record: if the record already exists, the time and
 * ordering will be updated.
 *
 * @param string $record_type the type of the record that will be tagged
 * @param int $record_id the id of the record that will be tagged
 * @param string $tagid the tag id to set on the record.
 * @param int $ordering the order of the instance for this record
 * @param int $userid optional only required for course tagging
 * @return bool true on success, false otherwise
 */
function tag_assign($record_type, $record_id, $tagid, $ordering, $userid = 0) {
    global $DB;

    if ( $tag_instance_object = $DB->get_record('tag_instance', array('tagid'=>$tagid, 'itemtype'=>$record_type, 'itemid'=>$record_id, 'tiuserid'=>$userid), 'id')) {
        $tag_instance_object->ordering     = $ordering;
        $tag_instance_object->timemodified = time();
        return $DB->update_record('tag_instance', $tag_instance_object);
    } else {
        $tag_instance_object = new StdClass;
        $tag_instance_object->tagid        = $tagid;
        $tag_instance_object->itemid       = $record_id;
        $tag_instance_object->itemtype     = $record_type;
        $tag_instance_object->ordering     = $ordering;
        $tag_instance_object->timemodified = time();
        $tag_instance_object->tiuserid     = $userid;
        return $DB->insert_record('tag_instance', $tag_instance_object);
    }
}

/**
 * Function that returns tags that start with some text, for use by the autocomplete feature
 *
 * @param string $text string that the tag names will be matched against
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function tag_autocomplete($text) {
    global $DB;
    return $DB->get_records_sql("SELECT tg.id, tg.name, tg.rawname
                                   FROM {tag} tg
                                  WHERE tg.name LIKE ?", array(moodle_strtolower($text)."%"));
}

/**
 * Clean up the tag tables, making sure all tagged object still exists.
 *
 * This should normally not be necessary, but in case related tags are not deleted
 * when the tagged record is removed, this should be done once in a while, perhaps on
 * an occasional cron run.  On a site with lots of tags, this could become an expensive
 * function to call: don't run at peak time.
 */
function tag_cleanup() {
    global $DB;

    $instances = $DB->get_recordset('tag_instance');

    // cleanup tag instances
    foreach ($instances as $instance) {
        $delete = false;

        if (!$DB->record_exists('tag', array('id'=>$instance->tagid))) {
            // if the tag has been removed, instance should be deleted.
            $delete = true;
        } else {
            switch ($instance->itemtype) {
                case 'user': // users are marked as deleted, but not actually deleted
                    if ($DB->record_exists('user', array('id'=>$instance->itemid, 'deleted'=>1))) {
                        $delete = true;
                    }
                    break;
                default: // anything else, if the instance is not there, delete.
                    if (!$DB->record_exists($instance->itemtype, array('id'=>$instance->itemid))) {
                        $delete = true;
                    }
                    break;
            }
        }
        if ($delete) {
            tag_delete_instance($instance->itemtype, $instance->itemid, $instance->tagid);
            //debugging('deleting tag_instance #'. $instance->id .', linked to tag id #'. $instance->tagid, DEBUG_DEVELOPER);
        }
    }
    $instances->close();

    // TODO: this will only clean tags of type 'default'.  This is good as
    // it won't delete 'official' tags, but the day we get more than two
    // types, we need to fix this.
    $unused_tags = $DB->get_recordset_sql("SELECT tg.id
                                             FROM {tag} tg
                                            WHERE tg.tagtype = 'default'
                                                  AND NOT EXISTS (
                                                      SELECT 'x'
                                                        FROM {tag_instance} ti
                                                       WHERE ti.tagid = tg.id
                                                  )");

    // cleanup tags
    foreach ($unused_tags as $unused_tag) {
        tag_delete($unused_tag->id);
        //debugging('deleting unused tag #'. $unused_tag->id,  DEBUG_DEVELOPER);
    }
    $unused_tags->close();
}

/**
 * Calculates and stores the correlated tags of all tags.
 * The correlations are stored in the 'tag_correlation' table.
 *
 * Two tags are correlated if they appear together a lot.
 * Ex.: Users tagged with "computers" will probably also be tagged with "algorithms".
 *
 * The rationale for the 'tag_correlation' table is performance.
 * It works as a cache for a potentially heavy load query done at the 'tag_instance' table.
 * So, the 'tag_correlation' table stores redundant information derived from the 'tag_instance' table.
 *
 * @global moodle_database $DB
 * @param int $mincorrelation Only tags with more than $mincorrelation correlations will
 *                             be identified.
 * @return void
 */
function tag_compute_correlations($mincorrelation = 2) {
    global $DB;

    // This mighty one line query fetches a row from the database for every
    // individual tag correlation. We then need to process the rows collecting
    // the correlations for each tag id.
    // The fields used by this query are as follows:
    //   tagid         : This is the tag id, there should be at least $mincorrelation
    //                   rows for each tag id.
    //   correlation   : This is the tag id that correlates to the above tagid field.
    //   correlationid : This is the id of the row in the tag_correlation table that
    //                   relates to the tagid field and will be NULL if there are no
    //                   existing correlations
    $sql = 'SELECT pairs.tagid, pairs.correlation, pairs.ocurrences,
                   co.id AS correlationid, co.correlatedtags
              FROM (
                       SELECT ta.tagid, tb.tagid AS correlation, COUNT(*) AS ocurrences
                         FROM {tag_instance} ta
                         JOIN {tag_instance} tb ON (ta.itemtype = tb.itemtype AND ta.itemid = tb.itemid AND ta.tagid <> tb.tagid)
                     GROUP BY ta.tagid, tb.tagid
                       HAVING COUNT(*) > :mincorrelation
                   ) pairs
         LEFT JOIN {tag_correlation} co ON co.tagid = pairs.tagid
          ORDER BY pairs.tagid ASC, pairs.ocurrences DESC, pairs.correlation ASC';
    $rs = $DB->get_recordset_sql($sql, array('mincorrelation' => $mincorrelation));

    // Set up an empty tag correlation object
    $tagcorrelation = new stdClass;
    $tagcorrelation->id = null;
    $tagcorrelation->tagid = null;
    $tagcorrelation->correlatedtags = array();

    // Iterate each row of the result set and build them into tag correlations.
    foreach ($rs as $row) {
        if ($row->tagid != $tagcorrelation->tagid) {
            // The tag id has changed so its now time to process the tag
            // correlation information we have.
            tag_process_computed_correlation($tagcorrelation);
            // Now we reset the tag correlation object so we can reuse it and set it
            // up for the current record.
            $tagcorrelation = new stdClass;
            $tagcorrelation->id = $row->correlationid;
            $tagcorrelation->tagid = $row->tagid;
            $tagcorrelation->correlatedtags = array();
        }
        $tagcorrelation->correlatedtags[] = $row->correlation;
    }
    // Update the current correlation after the last record.
    tag_process_computed_correlation($tagcorrelation);

    // Close the recordset
    $rs->close();
}

/**
 * This function processes a tag correlation and makes changes in the database
 * as required.
 *
 * The tag correlation object needs have both a tagid property and a correlatedtags
 * property that is an array.
 *
 * @param stdClass $tagcorrelation
 * @return bool True if the function completed, false if something was wrong.
 */
function tag_process_computed_correlation(stdClass $tagcorrelation) {
    global $DB;

    // You must provide a tagid and correlatedtags must be set and be an array
    if (empty($tagcorrelation->tagid) || !isset($tagcorrelation->correlatedtags) || !is_array($tagcorrelation->correlatedtags)) {
        return false;
    }

    // The row tagid doesn't match the current tag id which means we are onto
    // the next tag. Before we switch over we need to either insert or update
    // the correlation.
    $tagcorrelation->correlatedtags = join(',', $tagcorrelation->correlatedtags);
    if (!empty($tagcorrelation->id)) {
        // The tag correlation already exists so update it
        $DB->update_record('tag_correlation', $tagcorrelation);
    } else {
        // This is a new correlation to insert
        $DB->insert_record('tag_correlation', $tagcorrelation);
    }
    return true;
}

/**
 * Tasks that should be performed at cron time
 */
function tag_cron() {
    tag_compute_correlations();
    tag_cleanup();
}

/**
 * Search for tags with names that match some text
 *
 * @param string $text escaped string that the tag names will be matched against
 * @param boolean $ordered If true, tags are ordered by their popularity. If false, no ordering.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function tag_find_tags($text, $ordered=true, $limitfrom='', $limitnum='') {
    global $DB;

    $text = array_shift(tag_normalize($text, TAG_CASE_LOWER));

    if ($ordered) {
        $query = "SELECT tg.id, tg.name, tg.rawname, COUNT(ti.id) AS count
                    FROM {tag} tg LEFT JOIN {tag_instance} ti ON tg.id = ti.tagid
                   WHERE tg.name LIKE ?
                GROUP BY tg.id, tg.name, tg.rawname
                ORDER BY count DESC";
    } else {
        $query = "SELECT tg.id, tg.name, tg.rawname
                    FROM {tag} tg
                   WHERE tg.name LIKE ?";
    }
    $params = array("%{$text}%");
    return $DB->get_records_sql($query, $params, $limitfrom , $limitnum);
}

/**
 * Get the name of a tag
 *
 * @param mixed $tagids the id of the tag, or an array of ids
 * @return mixed string name of one tag, or id-indexed array of strings
 */
function tag_get_name($tagids) {
    global $DB;

    if (!is_array($tagids)) {
        if ($tag = $DB->get_record('tag', array('id'=>$tagids))) {
            return $tag->name;
        }
        return false;
    }

    $tag_names = array();
    foreach($DB->get_records_list('tag', 'id', $tagids) as $tag) {
        $tag_names[$tag->id] = $tag->name;
    }

    return $tag_names;
}

/**
 * Returns the correlated tags of a tag, retrieved from the tag_correlation
 * table.  Make sure cron runs, otherwise the table will be empty and this
 * function won't return anything.
 *
 * @param int $tag_id is a single tag id
 * @return array an array of tag objects, empty if no correlated tags are found
 */
function tag_get_correlated($tag_id, $limitnum=null) {
    global $DB;

    $tag_correlation = $DB->get_record('tag_correlation', array('tagid'=>$tag_id));

    if (!$tag_correlation || empty($tag_correlation->correlatedtags)) {
        return array();
    }

    // this is (and has to) return the same fields as the query in tag_get_tags
    if ( !$result = $DB->get_records_sql("SELECT DISTINCT tg.id, tg.tagtype, tg.name, tg.rawname, tg.flag, ti.ordering
                                            FROM {tag} tg INNER JOIN {tag_instance} ti ON tg.id = ti.tagid
                                           WHERE tg.id IN ({$tag_correlation->correlatedtags})") ) {
        return array();
    }

    return $result;
}

/**
 * Function that normalizes a list of tag names.
 *
 * @param mixed $tags array of tags, or a single tag.
 * @param int $case case to use for returned value (default: lower case).
 *     Either TAG_CASE_LOWER (default) or TAG_CASE_ORIGINAL
 * @return array of lowercased normalized tags, indexed by the normalized tag,
 *     in the same order as the original array. (Eg: 'Banana' => 'banana').
 */
function tag_normalize($rawtags, $case = TAG_CASE_LOWER) {

    // cache normalized tags, to prevent costly repeated calls to clean_param
    static $cleaned_tags_lc = array(); // lower case - use for comparison
    static $cleaned_tags_mc = array(); // mixed case - use for saving to database

    if ( !is_array($rawtags) ) {
        $rawtags = array($rawtags);
    }

    $result = array();
    foreach($rawtags as $rawtag) {
        $rawtag = trim($rawtag);
        if (!$rawtag) {
            continue;
        }
        if ( !array_key_exists($rawtag, $cleaned_tags_lc) ) {
            $cleaned_tags_lc[$rawtag] = moodle_strtolower( clean_param($rawtag, PARAM_TAG) );
            $cleaned_tags_mc[$rawtag] = clean_param($rawtag, PARAM_TAG);
        }
        if ( $case == TAG_CASE_LOWER ) {
            $result[$rawtag] = $cleaned_tags_lc[$rawtag];
        } else { // TAG_CASE_ORIGINAL
            $result[$rawtag] = $cleaned_tags_mc[$rawtag];
        }
    }

    return $result;
}

/**
 * Count how many records are tagged with a specific tag,
 *
 * @param string $record record to look for ('post', 'user', etc.)
 * @param int $tag is a single tag id
 * @return int number of mathing tags.
 */
function tag_record_count($record_type, $tagid) {
    global $DB;
    return $DB->count_records('tag_instance', array('itemtype'=>$record_type, 'tagid'=>$tagid));
}

/**
 * Determine if a record is tagged with a specific tag
 *
 * @param string $record_type the record type to look for
 * @param int $record_id the record id to look for
 * @param string $tag a tag name
 * @return bool true if it is tagged, false otherwise
 */
function tag_record_tagged_with($record_type, $record_id, $tag) {
    global $DB;
    if ($tagid = tag_get_id($tag)) {
        return $DB->count_records('tag_instance', array('itemtype'=>$record_type, 'itemid'=>$record_id, 'tagid'=>$tagid));
    } else {
        return 0; // tag doesn't exist
    }
}

/**
 * Flag a tag as inapropriate
 *
 * @param mixed $tagids one (int) tagid, or an array of tagids
 * @return void
 */
function tag_set_flag($tagids) {
    global $DB;

    $tagids = (array)$tagids;
    foreach ($tagids as $tagid) {
        $tag = $DB->get_record('tag', array('id'=>$tagid), 'id, flag');
        $tag->flag++;
        $tag->timemodified = time();
        $DB->update_record('tag', $tag);
    }
}

/**
 * Remove the inapropriate flag on a tag
 *
 * @param mixed $tagids one (int) tagid, or an array of tagids
 * @return bool true if function succeeds, false otherwise
 */
function tag_unset_flag($tagids) {
    global $DB;

    if ( is_array($tagids) ) {
        $tagids = implode(',', $tagids);
    }
    $timemodified = time();
    return $DB->execute("UPDATE {tag} SET flag = 0, timemodified = ? WHERE id IN ($tagids)", array($timemodified));
}
