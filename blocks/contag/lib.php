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


/* ConTag library file */


/* an explanation of "unique item keys"
   basically they're a way of uniquely referencing items, whether they are custom or not
   format is "type_id", where the id is either the custom-item-table id (for custom items), or some kind of resource/module id (to be determined by creation resolution functions)
   You can use contag_find_item_from_item_key with the appropriate flag(s) to resolve to the custom version of the item, if it exists.
   The item_id in the association table will ALWAYS refer to the custom-item-table id, not the resource/module-id etc
  */


// constants
// for every type added, we need to add a table to the DB
$CONTAG_INBUILT_ITEM_TYPES = array('resource','quiz', 'lesson','glossary','wiki','forum'); // regular items
// Check to see whether the book module is installed
global $DB;
if ($DB->get_record('modules', array('name'=>'book'))) {
    array_push($CONTAG_INBUILT_ITEM_TYPES, 'book');
}
$CONTAG_INBUILT_SUBITEM_TYPES = array(); // none yet, but these will be for specific types such as forum posts
$CONTAG_ONLY_CUSTOM_ITEM_TYPES = array('generic');
$CONTAG_ALL_ITEM_TYPES = array_merge($CONTAG_INBUILT_ITEM_TYPES, $CONTAG_INBUILT_SUBITEM_TYPES, $CONTAG_ONLY_CUSTOM_ITEM_TYPES);
$CONTAG_NO_TAG_KEY = '[no tag]'; // what we will use as the key for untagged items

$CONTAG_CUSTOM_ITEM_FIELDS = array('generic' => array('description','url')); // useful for generating input forms


/** item type functions - to emulate polymorphism via switch (items need a 'type' field added, usually by 'contag_get_items_for_course') **/
// keep them in sync with the lists above
// can we instead have anonymous functions that we store in a map? So it becomes a table lookup?

function contag_get_custom_item_types(){
    global $CONTAG_ONLY_CUSTOM_ITEM_TYPES;
    return $CONTAG_ONLY_CUSTOM_ITEM_TYPES;
}

function contag_get_inbuilt_item_types(){
    global $CONTAG_INBUILT_ITEM_TYPES;
    return $CONTAG_INBUILT_ITEM_TYPES;
}

function contag_get_custom_item_fields_for_type($type){
    global $CONTAG_CUSTOM_ITEM_FIELDS;
    return $CONTAG_CUSTOM_ITEM_FIELDS[$type];
}

function contag_get_item_display_name($item){
    global $CONTAG_INBUILT_ITEM_TYPES;
    // check for cache
    if (isset($item->generated_display_name)){
        return $item->generated_display_name;
    }
    
    $type = $item->type;
    $res = 'NAME NOT FOUND';
    if (in_array($type, $CONTAG_INBUILT_ITEM_TYPES)){
        $res = get_coursemodule_from_id($type,$item->item_id)->name;
    }else if ($type == 'generic'){
        $res = $item->description;
    }
    return $res;
}

function contag_get_item_url ($item){
    global $CFG, $CONTAG_INBUILT_ITEM_TYPES;
    $type = $item->type;
    $res = '';
    if (in_array($type, $CONTAG_INBUILT_ITEM_TYPES)){
        $res = $CFG->wwwroot.'/mod'.'/'.$type.'/view.php?id='.$item->item_id;
    } else if ($type == 'generic'){
        $res = $item->url;
    }
    return $res;
}

// returns the *custom* table from item type
// if your type is non-custom, you may have to do resolution first
function contag_table_from_item_type($item_type){
    return 'block_contag_item_'.$item_type;
}

// creates a key for the item that will be unique among all items (and appropriate for when the item is non-custom)
// type and id will be divided by a '_'
function contag_get_unique_item_key($item){
    global $CONTAG_INBUILT_ITEM_TYPES;
    if (contag_is_custom_item_type($item->type)){
        return $item->type.'_'.$item->id;
    } else if (in_array($item->type, $CONTAG_INBUILT_ITEM_TYPES)){
        return $item->type.'_'.$item->item_id;
    }
}

// basically reversing the "get_unique_item_key" operation above
// returns two values - the item itself, and whether the item exists in a custom instance yet (always true for naturally custom elements)
// whereas for 'resources', there may be an inbuilt version, but not a custom instance yet
// if both resolve_to_custom and create_if_resolved_item_does_not_exist are true, then is_custom should always(?) be true, as custom instances will always be created when necessary.
// (unless item_key is out of date and can't find anything)
// If there is some error (db-interface sync and all), returns a list of (null,false)
// TODO NOTE: we are overloading the term custom here (i.e. inbuilt/custom type, vs inbuilt/custom instance in this case) - call this one something else!
function contag_find_item_from_item_key($item_key, $course_id, $resolve_to_custom_if_possible=false, $create_if_resolved_item_does_not_exist=false){
    global $DB;
    // first split key once by '_' (starting at the end)
    $num_matches=preg_match('/^(.+)_([^_]+)$/', $item_key, $matches);
    if ($num_matches==0){ // no match! Some error
        return array(null,false);
    }
    
    $item_type = $matches[1];
    $item_id = (int)$matches[2]; // TODO: Is converting to a number necessary?
    
    if (contag_is_custom_item_type($item_type)){
        // then the item is in the custom table
        $item = $DB->get_record(contag_table_from_item_type($item_type), array('id' => $item_id));
        if ($item){ // ERROR CHECK
            $item->type = $item_type;
            return array($item, true);
        } else { // the interface is out of sync with the db
            return array(null,false);
        }
    } else { // not custom item - inbuilt
        
        // NOW for the resolutions back to custom items
        if ($resolve_to_custom_if_possible){
            // resolve
            $item = $DB->get_record(contag_table_from_item_type($item_type), array('item_id'=>$item_id));
            if ($item){ // found it!
                $item->type=$item_type;
                return array($item, true);
            } else { // we want but CAN'T resolve (resolution doesn't already exist)
                // error-check that the item still exists (making sure interface is sync'd with db)
                if(!get_coursemodule_from_id($item_type, $item_id)){
                    return array(null,false); // doesn't exist, exit
                }
                
                // if create flag is true, then create
                if ($create_if_resolved_item_does_not_exist){
                    $item = new StdClass;
                    $item->item_id = $item_id;
                    $item->course_id = $course_id;
                    try{
                        $item_id=$DB->insert_record(contag_table_from_item_type($item_type),$item);
                    } catch (Exception $e){
                        return array(null,false); // an error, return!
                    }

                    $item->type = $item_type;
                    $item->id = $item_id;
                    return array($item, true);
                } else { // return non-resolved item
                    return array(contag_inbuilt_to_temp_item(get_coursemodule_from_id($item_type,$item_id), $item_type), false);
                }
            }
        } else { // we don't want a resolution
            // error-check that the item still exists (making sure interface is sync'd with db)
            if(!get_coursemodule_from_id($item_type, $item_id)){
                return array(null,false); // doesn't exist, exit
            }
            return array(contag_inbuilt_to_temp_item(get_coursemodule_from_id($item_type,$item_id), $item_type), false);
        }
    } // more resolutions can be added here, if need be
}

// creates a temp item (i.e. not necessarily in db, with no id)
// kind of cloning
// $item_from_db MUST exist
function contag_inbuilt_to_temp_item($item_from_db, $item_type){
    // transfer the info to our custom type
    $item = new StdClass();
    $item->type = $item_type;
    $item->item_id = $item_from_db->id;
    return $item;
}

/* returns all item records (normal and custom) available for this course */
// does two things - combines inbuilt moodle items and all custom item tables, and appends the type to them
function contag_get_items_for_course($course_id){
    global $CONTAG_INBUILT_ITEM_TYPES, $DB;
    $res = array();
    
    // first grab the inbuilt ones
    foreach ($CONTAG_INBUILT_ITEM_TYPES as $intype){
        foreach (contag_get_coursemodules_for_type($course_id, $intype) as $cm){
            // it might not have an id (if it hasn't been tagged yet), so let's leave that for now
            $res[] = contag_inbuilt_to_temp_item($cm, $intype);
        }
    }
    
    // for each custom item table, pull out all items related to course, then append the type so we can switch on it later
    foreach(contag_get_custom_item_types() as $type){
        $current_custom_items = $DB->get_records(contag_table_from_item_type($type),array('course_id'=>$course_id));        
        foreach ($current_custom_items as $item){
            $item->type = $type;
            $res[] = $item;
        }
    }
    return $res;
}

// end type functions

// returns the item record
function contag_get_item_for_type_and_id($item_type, $item_id, $add_type){
    global $DB;
    $record = $DB->get_record(contag_table_from_item_type($item_type), array('id'=> $item_id));
    if($add_type){
        $record->type = $item_type;
    }
    return $record;
}

// this is useful to know - if it is not custom, we'll probably have to resolve it to an actual item (i.e. with resources)
// see contag_find_item_from_item_key
function contag_is_custom_item_type($item_type){
    return in_array($item_type, contag_get_custom_item_types());
}

// adds an item of one of the custom types
// $item_object should be an object populated with the fields specific to that type
function contag_add_custom_item($item_object, $item_type, $course_id){
    global $DB;
    $item_object->course_id = $course_id;
    try{
        $DB->insert_record(contag_table_from_item_type($item_type), $item_object);
    } catch (Exception $e){
    }
}

// deletes an items from the contag db, plus all its associations
// if the item is of custom type, then it removes it completely, and it won't show any more
// if the item is inbuilt, it deletes the custom resolution (useful for deleting stale resolutions, once the resource etc itself is deleted)
function contag_delete_item($course_id, $item_key){
    // remove all related associations then del
    global $DB;
    list($item, $is_custom_resolution) = contag_find_item_from_item_key($item_key, $course_id, true, false);
    if ($is_custom_resolution){ // error-checking - db-interface sync error if this fails (as there should *always* be a custom resolution for items we are deleting)
        $DB->delete_records('block_contag_association', array('item_type'=> $item->type,'item_id' =>$item->id)); // delete associations first
        $DB->delete_records(contag_table_from_item_type($item->type),array('id' => $item->id,'course_id' => $course_id)); // delete item itself - course_id check is extra security
    }
}

// returns all modules of type 'resources' in the course
function contag_get_coursemodules_for_type($course_id, $type){
    $res = get_coursemodules_in_course($type, $course_id);
    return $res ? $res : array(); // to guarantee it returns some kind of array
}

function contag_get_associations_for_tag($tag_id){
    // 2.0: get_records now returns an empty array, not false, so we can just return that
    global $DB;
    return $DB->get_records('block_contag_association', array('tag_id' => $tag_id));
}

// Returns: all tag records defined for this course
function contag_get_defined_tags($course_id){
    global $DB;
    return $DB->get_records('block_contag_tag', array('course_id'=> $course_id)); 
    
}

function contag_get_tag_name($tag){ // property accessor (useful for callbacks)
    return $tag->tag_name;
}

/* Ensures a tag exists (creates a tag with the given tagname if it doesn't already exist)
 * ASSUMPTION: tag_name already normalised (cleaned and validated)
 * Returns the tag record id */
function contag_ensure_tag($course_id, $tag_name){
    global $DB;
    $tag_id = contag_get_tag_id($course_id, $tag_name);
    if (!$tag_id){ // if it doesn't already exist
        // create it
        $contag_tag_object = new StdClass;
        $contag_tag_object->course_id = $course_id;
        $contag_tag_object->tag_name = $tag_name;
        try{
            $tag_id = $DB->insert_record('block_contag_tag', $contag_tag_object);
        } catch (Exception $e){
            $tag_id = false;
        }
    }
    return $tag_id;
}

// cleans raw tag input string
// trims, inner spaces to '_', lowers
function contag_clean_tag_input($tag_string){
    return strtolower(preg_replace('/ /','_', trim($tag_string)));
}

// takes in a (cleaned!) tag input string, and validates whether it is a good name
function contag_validate_tag_name_as_good($tag_string){
    // must contain only lowercase, only alpha chars (including no spaces), AND not empty
    return preg_match('/^[a-z0-9_]+$/', $tag_string) ? true : false;
}

// ensures an association exists - creates it if it doesn't
// creates any necessary tags and items as well
// ASSUMPTION: the $tag_name has been cleaned and validated as a good name (doesn't necessarily have to exist)
// should we check name in parent function (is_valid_name)?
function contag_add_association ($course_id, $tag_name, $item_key){
    global $DB;
    $tag_id = contag_ensure_tag($course_id, $tag_name);
    list($item, $is_custom_resolution) = contag_find_item_from_item_key($item_key, $course_id, true, true); // will resolve and create if necessary
    if ($item && !$DB->get_record('block_contag_association', array('tag_id'=> $tag_id, 'item_type'=> $item->type, 'item_id'=> $item->id))){ // ERROR CHECK on $item, just in case it the module doesn't exist anymore (i.e. someone adding tags and someone else adding modules)
        // association doesn't exist, add it
        $contag_assoc_object = new StdClass;
        $contag_assoc_object->tag_id = $tag_id;
        $contag_assoc_object->item_type = $item->type;
        $contag_assoc_object->item_id = $item->id;
        try{
            $DB->insert_record('block_contag_association', $contag_assoc_object);
        } catch (Exception $e){
        }
    }
}


function contag_delete_association($course_id, $item_key, $tag_id){
    global $DB;
    if (contag_get_tag_from_tag_id($course_id, $tag_id)){ // ERROR CHECK - does it exist (and in this course)?
        list($item, $is_custom_resolution) = contag_find_item_from_item_key($item_key, $course_id, true); // should ALWAYS resolve in this case - ignore if not
        if ($is_custom_resolution){ // ERROR CHECK: if it didn't resolve, then we ignore
            $DB->delete_records('block_contag_association', array('tag_id'=> $tag_id, 'item_type' => $item->type, 'item_id' => $item->id)); // delete association
        }
    }
}

function contag_get_tag_id($course_id, $tag_name){
    global $DB;
    return $DB->get_field('block_contag_tag', 'id', array('course_id' => $course_id, 'tag_name' => $tag_name));
}

function contag_get_tag_from_tag_id($course_id, $tag_id){
    global $DB;
    return $DB->get_record('block_contag_tag', array('id'=> $tag_id, 'course_id'=>$course_id)); // extra security, as we're using tag ids
}

// deletes the tag, and any associations that it had
// using ids instead of names, as there's less chance of deleting a recreated tag (with the same name) if the URL is accidently called again
function contag_delete_tag($course_id, $tag_id){
    global $DB;
    if(contag_get_tag_from_tag_id($course_id, $tag_id)){ // ERROR CHECK - does it exist (and in this course)?
        $DB->delete_records('block_contag_association', array('tag_id' => $tag_id)); // delete associations (if any)
        $DB->delete_records('block_contag_tag', array('id' => $tag_id)); // delete tag itself
    }
}

// tag rename - ASSUMPTION: tag_new_name is cleaned and validated (but we will check here for name clash)
function contag_rename_tag($course_id, $tag_id, $tag_new_name){
    global $DB;
    if (!contag_get_tag_id($course_id, $tag_new_name)){ // we don't want to add if there's a name clash
        $tag = contag_get_tag_from_tag_id($course_id, $tag_id);
        if ($tag){ // ERROR-CHECK
            $tag->tag_name = $tag_new_name;
            $DB->update_record('block_contag_tag', $tag);
        }
    } else { // tag name already exists, return error flag
        return "dupe";
    }
}

// NOTE: HAS SIDE-EFFECTS
// each item will be augmented with 'generated_url', and 'generated_display_name'
function contag_generate_display_properties_for_item (&$item){
    $item->generated_url = contag_get_item_url($item);
    $item->generated_display_name = contag_get_item_display_name($item);
}

// comparator for items, sorts by display name
// Not currently being used
// CHECK IF NEEDED ANYMORE (we already have by displayname and type below)
function contag_items_by_display_name_cmp($a, $b)
{
    $al = strtolower($a->generated_display_name);
    $bl = strtolower($b->generated_display_name);
    if ($al == $bl){
        return 0;
    }
    return ($al > $bl) ? +1 : -1;
}


// comparator for items, sorts by type first, then display name
function contag_items_by_type_and_display_name_cmp($a, $b)
{
    $al = strtolower($a->generated_display_name);
    $bl = strtolower($b->generated_display_name);
    $at = strtolower($a->type);
    $bt = strtolower($b->type);
    
    if ($at == $bt){ // by type first
        if ($al == $bl){
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    } else {
        return ($at > $bt) ? +1 : -1;
    }
}


// comparator for tags, sorts by name (case insensitive)
function contag_tags_by_name_cmp($a, $b){
    $al = strtolower(contag_get_tag_name($a));
    $bl = strtolower(contag_get_tag_name($b));
    if ($al == $bl) {
        return 0;
    }
    return ($al > $bl) ? +1 : -1;
}

// returns a list of tags, with each tag having a subitem 'items', which is an array of items with that tag
// each $item will also be augmented with other properties, for callers that wish to use those (see contag_generate_display_properties_for_item)
// $items will also get a list of tags (so it's a recursive structure)
// each item will also have it's unique key set as ->unique_key
// will clean any stale items as well (i.e. the resource that they refer to does not exist anymore), if any are encountered
function contag_get_all_tags_with_their_items ($course_id){
    global $CONTAG_NO_TAG_KEY;
    $defined_tags = contag_get_defined_tags($course_id);
    
    // grab all items possible to be tagged
    // gen unique key for comp, make map for caching
    $all_items = array();
    foreach (contag_get_items_for_course($course_id) as $item){
        contag_generate_display_properties_for_item($item); // SIDE EFFECTS - $item is modified
        $item->unique_key = contag_get_unique_item_key($item);
        $all_items[$item->unique_key] = $item;
    }
    
    // keep track of stale_items, so we don't try to delete them twice
    $stale_items = array();
    
    foreach($defined_tags as $tag){
        $assocs = contag_get_associations_for_tag($tag->id);
        $tag->items = array();
        foreach ($assocs as $assoc){ // for each tagging instance
            // get item name, and output it
            $the_key = contag_get_unique_item_key(contag_get_item_for_type_and_id($assoc->item_type, $assoc->item_id, true));
            if (!isset($all_items[$the_key])){ // stale item, delete it from custom table AND association (i.e. cleanup as we go)
                if (!in_array($the_key, $stale_items)){ // ...unless we have encountered and deleted it already this session
                    contag_delete_item($course_id,$the_key);
                    $stale_items[] = $the_key;
                }
            } else { //item really exists, so we add it
                $item = $all_items[$the_key]; // grab the cached item that this assoc refers to
                $item->tagged = true; // so we don't include it in our untagged set
                $tag->items[] = $item;
            }
        }
    }

    // sort before adding [no_tag]
    // want to do it in interface code, but we would have [no_tag] in there...
    // maybe we could take into account the [no_tag] tag when comparing, pushing it to the bottom?
    usort($defined_tags, "contag_tags_by_name_cmp");
    
    // untagged items
    $tag = new StdClass;
    $tag->tag_name = $CONTAG_NO_TAG_KEY; // NOTE: does not have course_id/fake tag_id
    $tag->items = array();
    foreach($all_items as $item){
        if (!isset($item->tagged)){
            $tag->items[] = $item;
        }
    }
    
    $defined_tags[] = $tag;
    return $defined_tags;
}


// returns a array - first item is list of items, with each item having a property 'tags', which is an array of tags for that item
// each item will be augmented with 'generated_url', and 'generated_display_name', for callers that wish to use those
// second item is used tags (minus the '[NO_TAG]')
// third item of the array is a list of the orphan tags
// each tag will get a flag, is_orphan
function contag_get_tags_by_item ($course_id){
    // Invert items_by_tag list
    // maybe more work for the machine, but easiest way at the moment
    // (better performance will be gained by recreating the other function, but in reverse)
    // also, had some issues getting associations for items (too much redirection - though can be resolved with pseudo-polymorphism)
    
    global $CONTAG_NO_TAG_KEY;
    
    $items_by_tag = contag_get_all_tags_with_their_items($course_id);
    
    // our two return values
    $all_items = array();
    $used_tags = array();
    $orphan_tags = array();
    
    // invert tags->items (tags for an item will be made blank if they don't exist), and keep a list of the items.
    // also keep a record of the orphans
    foreach ($items_by_tag as $index => $tag){
        
        if (empty($tag->items)){
            if(contag_get_tag_name($tag) != $CONTAG_NO_TAG_KEY){
                $orphan_tags[] = $tag;
                $tag->is_orphan = true;
            }
        } else if (contag_get_tag_name($tag) == $CONTAG_NO_TAG_KEY){
            foreach ($tag->items as $item){
                $item->tags = array(); // create a blank array
                $all_items[contag_get_unique_item_key($item)] = $item; // record the item as seen
            }
        } else { // normal tag that has items
            $used_tags[] = $tag;
            foreach ($tag->items as $item){
                $item->tags[] = $tag;
                $tag->is_orphan = false;
                $all_items[contag_get_unique_item_key($item)] = $item;
            }
        }
    }
    
    return array(array_values($all_items), $used_tags, $orphan_tags);
}

// returns an array of random tags (up to the $maxtags number of tags)
function contag_get_random_tags($course_id, $maxtags){
    $res = array();
    $all_tags = contag_get_defined_tags($course_id);
    if (!empty($all_tags)){ // only need to do all this if there ARE tags!
        $rand_keys = array_rand($all_tags, min(count($all_tags),$maxtags));
        // now return an array with just the selected elements
        if (is_array($rand_keys)){ // array_rand will return a single item if we only pick 1!
            foreach($rand_keys as $key){
                $res[] = $all_tags[$key];
            }
        } else {
            $res[] = $all_tags[$rand_keys];
        }
    }
    return $res;
}

// the random tag that will appear on the block page
function contag_format_random_tag_link($tag){
    global $CFG;
    return '<a href="'.$CFG->wwwroot.'/blocks/contag/'.'view.php?id='.$tag->course_id.'&tag_name='.$tag->tag_name.'">'.contag_get_tag_name($tag).'</a>';
}

// returns an <a> tag
// PREFER generated urls set via contag_generate_display_properties_for_item (for cachings)
function contag_get_html_link_for_item ($item){
    $url = isset($item->generated_url) ? $item->generated_url : contag_get_item_url($item);
    $display_name = contag_get_item_display_name($item);
    return '<a href="'.$url.'">'.$display_name.'</a>';
}

function contag_print_warning($warning_string){
    print(get_string('warning_label', 'block_contag') . $warning_string);
}

// this function needs to be run as a cron - basically, it will go through all resolutions, and check that the actual modules still exist
// if not, it deletes them and any associations they had
// Really, it's here for one case: the custom resolutions that have no associations, but the module has been deleted.
// If they had associations, then they would be deleted when we come across them in "contag_get_all_tags_with_their_items"
// The consequences of NOT running this function is that the db would have unused entries that can't be deleted via the interface
// I doubt there would be many (and it's not a terrible issue), so maybe this only needs to be run once a week or so?
// Set to run as cron in the block
// This does NOT work when called from the block's get_content - the global vars aren't initialised for some reason!
function contag_purge_stale_resolutions(){
    // first grab the inbuilt ones
    global $DB;
    foreach (contag_get_inbuilt_item_types() as $intype){
        $records=$DB->get_records(contag_table_from_item_type($intype));
        foreach ($records as $item){
            if (!get_coursemodule_from_id($intype, $item->item_id)){
                //doesn't exist, delete stale item
                $DB->delete_records('block_contag_association', array('item_type'=> $intype, 'item_id'=> $item->id)); // delete associations first
                $DB->delete_records(contag_table_from_item_type($intype), array('id'=> $item->id)); // delete item itself
            }
        }
    }
}

/**
 * Prints an HTML table - based on print_table() from weblib.php
 *
 * @param $table An array containing table properties.
 * <ul>
 *     <li>$table->head - An array of column headings.
 *     <li>$table->align - An array of column alignments.
 *     <li>$table->size  - An array of column sizes.
 *     <li>$table->wrap - An array of "nowrap"s or nothing.
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->width  - A percentage of the page.
 *     <li>$table->tablealign  - Align the whole table.
 *     <li>$table->cellpadding  - Padding on each cell.
 *     <li>$table->cellspacing  - Spacing between cells.
 *     <li>$table->class - The table's class attribute.
 *     <li>$table->id - The table's ID attribute.
 *     <li>$table->rowclass[] - Classes to add to particular rows.
 *     <li>$table->summary - Description of the contents for screen readers.
 *     <li>$table->style - The table's style attribute.
 * </ul>
 * @param $return Whether to return an output string or echo now (default)
 * @return An output string or true/false.
 */
function contag_print_table($table, $return=false) {
    $output = '';

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = ' text-align:'. fix_align_rtl($aa) .';';  // Fix for RTL languages
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width:'. $ss .';';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' white-space:nowrap;';
            } else {
                $wrap[$key] = '';
            }
        }
    }

    if (empty($table->width)) {
        $table->width = '80%';
    }

    if (empty($table->tablealign)) {
        $table->tablealign = 'center';
    }

    if (!isset($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (!isset($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    if (empty($table->class)) {
        $table->class = 'generaltable';
    }
    
    if (empty($table->style)) {
        $table->style = '';
    }


    $tableid = empty($table->id) ? '' : 'id="'.$table->id.'"';

    $output .= '<table width="'.$table->width.'" ';
    if (!empty($table->summary)) {
        $output .= " summary=\"$table->summary\"";
    }
    $output .= ' style="'.$table->style.'"';
    $output .= " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class boxalign$table->tablealign\" $tableid>\n";

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        $output .= '<tr>';
        $keys=array_keys($table->head);
        $lastkey = end($keys);
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if ($key == $lastkey) {
                $extraclass = ' lastcol';
            } else {
                $extraclass = '';
            }

            $output .= '<th style="vertical-align:top;'. $align[$key].$size[$key] .';white-space:nowrap;" class="header c'.$key.$extraclass.'" scope="col">'. $heading .'</th>';
        }
        $output .= '</tr>'."\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        $keys=array_keys($table->data);
        $lastrowkey = end($keys);
        foreach ($table->data as $key => $row) {
            $oddeven = $oddeven ? 0 : 1;
            if (!isset($table->rowclass[$key])) {
                $table->rowclass[$key] = '';
            }
            if ($key == $lastrowkey) {
                $table->rowclass[$key] .= ' lastrow';
            }
            $output .= '<tr class="r'.$oddeven.' '.$table->rowclass[$key].'">'."\n";
            if ($row == 'hr' and $countcols) {
                $output .= '<td colspan="'. $countcols .'"><div class="tabledivider"></div></td>';
            } else {  /// it's a normal row of data
                $keys2=array_keys($row);
                $lastkey = end($keys2);
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = '';
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = '';
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = '';
                    }
                    if ($key == $lastkey) {
                        $extraclass = ' lastcol';
                    } else {
                        $extraclass = '';
                    }
                    $output .= '<td style="'. $align[$key].$size[$key].$wrap[$key] .'vertical-align:top;" class="cell c'.$key.$extraclass.'">'. $item .'</td>';
                }
            }
            $output .= '</tr>'."\n";
        }
    }
    $output .= '</table>'."\n";

    if ($return) {
        return $output;
    }

    echo $output;
    return true;
}


?>