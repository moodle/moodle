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


// "Create, edit and associate concept tags" page (viewable by teacher)
// List all items, each followed by what tags they have (and allow the user to edit them)

// A table

// Anything labelled CUSTOM ITEMS is for a future feature (it already works somewhat, but is disabled for now).

require_once('../../config.php');
require_once('lib.php');

// returns '"item1","item2","item3"' etc - or '' if the array is empty
function php_string_array_to_javascript_array_contents($string_array){
    return $string_array ? '"'.implode('" , "', $string_array).'"' : '';
}

// possible parameter names
$CONTAG_ASSOCIATE_BUTTON_NAME = "associate";
$CONTAG_UNASSOCIATE_KEY_NAME = "unassociate";
$CONTAG_DELETE_TAG_KEY_NAME = "delete_tag";
$CONTAG_TAG_FIELD_NAME = "tagstoadd";
$CONTAG_RENAME_TAG_KEY_NAME = "rename";
// CUSTOM ITEMS (feature currently disabled)
$CONTAG_ADD_CUSTOM_KEY_NAME = 'addcustom';
$CONTAG_CUSTOM_SELECT_KEY_NAME = 'custom_select';
$CONTAG_DELETE_CUSTOM_ITEM_KEY_NAME = 'del_custom_item';

$an_error="";

// Set up necessary parameters
$courseid = required_param('id', PARAM_INT);

$url = new moodle_url('/blocks/contag/edit.php', array('id'=>$courseid));
$PAGE->set_url($url);


// SECURITY: Basic access control checks
if (!$course = $DB->get_record('course', array('id'=> $courseid))) {
    print_error('courseidunknown','block_contag');
}

require_login($course->id); // SECURITY: make sure the user has access to this course and is logged in

// Start of AUTOCOMPLETE code - from http://tracker.moodle.org/browse/MDL-19865, http://developer.yahoo.com/yui/autocomplete/, and http://developer.yahoo.com/yui/examples/autocomplete/ac_basic_array.html
// OLD, now using YUI().use
//require_js(array('yui_yahoo','yui_dom-event', 'yui_connection', 'yui_datasource', 'yui_autocomplete'));

/* PROCESS ANY SUBMISSION */

// alg:
// if we have an 'add' submission
// then loop over all the text fields
// for each one, if there is data in there (and WELL-FORMED data), then sanitise and add it
// the item we need to add it too can be derived from the key (tagtoadd_.*)

$post_keys = array_keys($_POST);

if (in_array ($CONTAG_ASSOCIATE_BUTTON_NAME, $post_keys)) { // have they clicked the add button/pressed enter in the text fields?
    // we have an add, do your thing
    foreach ($_POST as $key => $value){
        if (preg_match('/^'.$CONTAG_TAG_FIELD_NAME.'_(.*)$/', $key, $matches)){ // do we have a field?
            if (!empty($value)){ // does it have a value?
                // split, clean, validate, and add
                $splittags = preg_split('/,/', $value, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($splittags as $splittag){
                    $splittag = contag_clean_tag_input($splittag);
                    if (!empty($splittag)){
                        if (contag_validate_tag_name_as_good($splittag)){
                            contag_add_association($courseid, $splittag, $matches[1]); // matches[1] is unique item key
                        } else { // escape below to prevent injection
                            $an_error.="Error: invalid tagname: ".htmlspecialchars($splittag)." - your tag should only contain numbers, letters, any underscores (any spaces will be converted to underscores).";
                        }
                    }
                }
            }
        }
    }
 }

// CUSTOM ITEMS
if (in_array ($CONTAG_ADD_CUSTOM_KEY_NAME, $post_keys)){ // they submitted a new custom item
    $item_type = $_POST[$CONTAG_CUSTOM_SELECT_KEY_NAME];
    if(contag_is_custom_item_type($item_type)){ // error-check
        $fields = contag_get_custom_item_fields_for_type($item_type);
        $item = new StdClass;
        foreach ($fields as $field){ // for each field the item has
            $item->$field = $_POST[$field];
        }
        contag_add_custom_item($item,$item_type,$courseid);
    }
}


$get_keys = array_keys($_GET);

// if we have a 'unassociate' submission
if (in_array ($CONTAG_UNASSOCIATE_KEY_NAME, $get_keys)){
    contag_delete_association($courseid,$_GET['item_key'],$_GET['tag_id']); // it will silently ignore if not found
}

// if we have a 'delete tag' submission
if (in_array ($CONTAG_DELETE_TAG_KEY_NAME, $get_keys)){
    contag_delete_tag($courseid, $_GET['tag_id']); // it will silently ignore if not found
}

// if we have a rename event
if (in_array ($CONTAG_RENAME_TAG_KEY_NAME, $get_keys)){
    // clean and validate before adding
    $cleaned_name = contag_clean_tag_input($_GET['tag_new_name']);
    if (contag_validate_tag_name_as_good($cleaned_name)){
        if (contag_rename_tag($courseid, $_GET['tag_id'], $cleaned_name) == "dupe"){ // silently ignore if tag not found, returns "dupe" if name exists
            $an_error.="Error: Can't rename tag - '".htmlspecialchars($cleaned_name)."' already exists.";
        }

    } else {
        // escape below to prevent injection
        $an_error.="Error: invalid tagname: ".htmlspecialchars($cleaned_name)." - your tag should only contain numbers, letters, any underscores (any spaces will be converted to underscores).";
    }
}

// CUSTOM ITEMS: if we have a del custom item event
if (in_array ($CONTAG_DELETE_CUSTOM_ITEM_KEY_NAME, $get_keys)){
    contag_delete_item($courseid, $_GET['item_key']);
}


/* DISPLAY THE PAGE */

// Set up necessary strings
$formheader = get_string('editingformheader', 'block_contag');

// Print page elements
$navigation = build_navigation($formheader);
print_header_simple("$formheader", "", $navigation, "", "", true, "");
$OUTPUT->heading(get_string('editingformpageheading','block_contag'));

$context = get_context_instance(CONTEXT_COURSE, $courseid);
if (has_capability('block/contag:edit', $context)){ // can they edit tags?
    
    
    // MORE AUTOCOMPLETE code
    echo '<style>'."\n";
    include ($CFG->libdir.'/yui/autocomplete/assets/skins/sam/autocomplete.css');
    
    echo '.myAutoComplete {';
    echo 'width:15em; /* set width here or else widget will expand to fit its container */';
    echo ' padding-bottom:2em; }';
    
    echo '</style>'."\n";

    ?>


        <script type="text/javascript">
          var tag_datasource;
          var YAHOO;
        YUI().use("yui2-autocomplete", "yui2-datasource", function (Y){
        YAHOO = Y.YUI2;
             var contag_tags = [<?php echo php_string_array_to_javascript_array_contents(array_map("contag_get_tag_name", contag_get_defined_tags($courseid))); ?>];
    tag_datasource = new YAHOO.util.LocalDataSource(contag_tags);
        });
    
    
    function createNewAutocompleteWidget(inputid,containerid){
        
        
        // Instantiate the AutoComplete
        var oAC = new YAHOO.widget.AutoComplete(inputid, containerid, tag_datasource);
        
        //oAC.prehighlightClassName = "yui-ac-prehighlight";
        //oAC.useShadow = true;
        oAC.delimChar = ",";
        oAC.queryDelay = 0.1;
    }
    
    
    // set to true during submission, so that the confirm box will not pop up
    var submitting=false;
    
    function isFormFilled(){ // returns true if the add tag form contains tags to add
        for(var i=0; i<document.addtagform.elements.length; i++){
            var el = document.addtagform.elements[i];
            if (el.type=="text" && el.value.match(/\S/)){ // makes sure it's not empty
                return true;
            }        
        }
        return false;
    }
    
    function askConfirm(){
        if (!submitting && isFormFilled()){ // if this is a non-submit event, but there are values to submit
            return "You have entered tags in the tagboxes, but haven't clicked 'Save' yet.";
        }
    }
    
    window.onbeforeunload = askConfirm;
    
        
    </script>
          
          <?php
          // table starts here!
          list($tags_by_item, $used_tags, $orphan_tags) = contag_get_tags_by_item($courseid);
    
    usort($tags_by_item, "contag_items_by_type_and_display_name_cmp");
    
    // CUSTOM ITEMS
    function custom_item_del_link_if_necessary($item, $courseid){
        global $CONTAG_DELETE_CUSTOM_ITEM_KEY_NAME;
        if (contag_is_custom_item_type($item->type)){
            return '<a href="edit.php?id='.$courseid.'&'.$CONTAG_DELETE_CUSTOM_ITEM_KEY_NAME.'=TRUE&item_key='.$item->unique_key.'"><sup>[x]</sup></a>';
        } else {return '';}
    }
    
    $table = new stdClass();
    $table->head = array("Item", "Type", "Tags applied",'Add tags <input type="submit" name="'.$CONTAG_ASSOCIATE_BUTTON_NAME.'" value="Save" />');
    $table->data = array();
    
    $tabindex=1;
    
    foreach($tags_by_item as $item){
        $tagurls = array();
        foreach($item->tags as $tag){
            $tagurls[] = contag_get_tag_name($tag).'<a href="edit.php?id='.$courseid.'&'.$CONTAG_UNASSOCIATE_KEY_NAME.'=TRUE&item_key='.$item->unique_key.'&tag_id='.$tag->id.'" title="Remove tag from \''.contag_get_item_display_name($item).'\'" ><sup>[x]</sup></a>';
        }
        
        $input_id = "myinput".$tabindex;
        $container_id = "mycontainer".$tabindex;
        $table->data[] = array(contag_get_html_link_for_item($item).custom_item_del_link_if_necessary($item, $courseid), $item->type, implode(", ", $tagurls), 
                               '<div class="myAutoComplete"><input id="'.$input_id.'" type="text" name="'.$CONTAG_TAG_FIELD_NAME.'_'.$item->unique_key.'" autocomplete=off tabindex='.$tabindex++.'/> <span id="'.$container_id.'"></span></div><script type="text/javascript"> YUI().use("yui2-autocomplete", "yui2-datasource", function (Y){createNewAutocompleteWidget("'.$input_id.'","'.$container_id.'");});</script>'); // add row - second column combines all tags with ", "
        // how the tagbox will work is that the name will contain the item key - and when we process, we can derive the actual item from it
    }
    
    echo '<div class="yui-skin-sam">'; // for autocomplete
    
    print('<form name="addtagform" method="post" action="edit.php?id='.$courseid.'" onSubmit="submitting=true;return true;">');
    contag_print_table($table);
    print('</form>');
    
    echo '</div>';
    
    // prints out the tag name at the start as well
    function format_deltag_link($tag){
        global $courseid, $CONTAG_DELETE_TAG_KEY_NAME; // courseid is bad, but I can't pass it in with array_map.
        $tagname = contag_get_tag_name($tag);
        $colour_string= $tag->is_orphan ? "color: #AAAAAA;": "";
        return '<span class="editable" style="cursor: pointer;'.$colour_string.'" onmouseover="this.style.backgroundColor=\'#DDDDDD\';" onmouseout="this.style.backgroundColor=\'white\';" title="Click to rename">'.$tagname.'</span>'.'<a href="edit.php?id='.$courseid.'&'.$CONTAG_DELETE_TAG_KEY_NAME.'=TRUE&tag_id='.$tag->id.'" onclick="return confirm(\'Are you sure you want to permanently delete this tag?\');" title="Permanently delete tag"><sup>[x]</sup></a>';
    }
    
    echo '<div id="editarea">';
    
    print("<br>All tags:  ".implode(", ", array_map("format_deltag_link",array_merge($used_tags, $orphan_tags))));
    
    echo '</div>';
    
    // end table
    ?>
        
        <!-- Edit in place -->
             <!-- From http://blog.davglass.com/files/yui/editable/ - with some additions -->
    <script type="text/javascript">
      YUI().use('yui2-yahoo-dom-event', function (Y){
          var YAHOO = Y.YUI2;

             (function() {
                 var Dom = YAHOO.util.Dom,
                     Event = YAHOO.util.Event;
                 
                 editable = {
                 config: {
                     class_name: 'editable'
                 },
                 init: function() {
                         this.clicked = false;
                         this.contents = false;
                         this.input = false;
                         
                         var _items = Dom.getElementsByClassName(this.config.class_name);
                         Event.addListener(_items, 'click', editable.dbl_click, editable, true);
                     },
                 dbl_click: function(ev) {
                         var tar = Event.getTarget(ev);
                         if (!tar) {
                             return;
                         }
                         if (tar.tagName && (tar.tagName.toLowerCase() == 'input')) {
                             return false;
                         }
                         this.check();
                         this.clicked = tar;
                         this.contents = this.clicked.innerHTML;
                         this.make_input();
                     },
                 make_input: function() {
                         this.input = Dom.generateId();
                         
                         new_input = document.createElement('input');
                         new_input.setAttribute('type', 'text');
                         new_input.setAttribute('id', this.input);
                         new_input.value = this.contents;
                         new_input.setAttribute('size', this.contents.length);
                         new_input.className = 'editable_input';
                         
                         this.clicked.innerHTML = '';
                         this.clicked.appendChild(new_input);
                         new_input.select();
                         Event.addListener(new_input, 'blur', editable.check, editable, true);
                         Event.addListener(new_input, 'keyup', function (ev){ if(ev.keyCode==13){this.clear_input();} else if (ev.keyCode == 27){ this.clear_input(true)}}, editable, true); // add check for enter and esc
                     },
                 clear_input: function(cancelled) {
                         if (cancelled){
                             this.clicked.innerHTML = this.contents;
                             this.clicked = false;
                             this.contents = false;
                             this.input = false;
                             return;
                             
                         } else if (this.input) {
                             if (Dom.get(this.input).value.length > 0) {
                                 this.clean_input();
                                 this.contents_new = Dom.get(this.input).value;
                                 this.clicked.innerHTML = this.contents_new;
                                 if (this.contents_new == this.contents){ // there's no change, so don't call callback (in our ConTag situation, it is appropriate to skip if not changed)
                                     this.clicked = false;
                                     this.contents = false;
                                     this.input = false;
                                     return;
                                 }
                             } else {
                                 this.contents_new = '[removed]'
                                 this.clicked.innerHTML = this.contents_new;
                             }
                         }
                         
                         this.callback();
                         
                         this.clicked = false;
                         this.contents = false;
                         this.input = false;
                     },
                 clean_input: function() {
                         checkText   = new String(Dom.get(this.input).value);
                         regEx1      = /\"/g;
                         checkText       = String(checkText.replace(regEx1, ''));
                         Dom.get(this.input).value = checkText;
                     },
                 check: function(ev) {
                         if (this.clicked) {
                             this.clear_input();
                         }
                     },
                 callback: function() {
                     }
                 }
             })();
      }); // end yui
    
    
    var nameToIDMap = {<?php $merged = array_merge($used_tags, $orphan_tags);
                       $lastel = end($merged);
                       foreach($merged as $tag){
                           echo '"'.contag_get_tag_name($tag).'":'.$tag->id;
                           if ($lastel != $tag){
                               echo ',';
                           }
        } ?>};
    
    
    function getIDForOriginal(text){
        return nameToIDMap[text];
    }
    
    YUI().use('yui2-yahoo-dom-event', function (Y){
        var YAHOO = Y.YUI2;
    (function() {
        var Dom = YAHOO.util.Dom,
            Event = YAHOO.util.Event;
        editable.callback = function(){var id = getIDForOriginal(editable.contents); window.location="edit.php?id="+<?php echo $courseid ?>+"&<?php echo $CONTAG_RENAME_TAG_KEY_NAME ?>=TRUE&tag_id="+id+"&tag_new_name="+escape(editable.contents_new)};
        Event.onAvailable('editarea', editable.init, editable, true);
    })();
    });
    </script>
          
          <?php
          
          /*
           
           CUSTOM ITEMS CODE - when it is ready to be used, comment this back in (and remove the surrounding ?php tags)
           Commented out for now so that we don't send unnecessary data (for an unused feature) to the client
           
           <!-- NOW - some stuff for adding custom items -->
           
           <script type="text/javascript">
           
           // the fields data structure
           var customTypes = { 
           <?php
           foreach (contag_get_custom_item_types() as $type){
           echo '"'.$type.'":[';
           foreach(contag_get_custom_item_fields_for_type($type) as $field){
           echo '"'.$field.'",';
           }
           echo '],';
           }
           
           ?>
           
           };
           
           // callback for when the item type select changes
           function customTypeSelected(select){
           if (select.value !=""){
           //alert(select + " changed to " + select.value);
           
           // now generate appropriate fields for type, + submit
           var customContainer = YAHOO.util.Dom.get('customContainer');
           var runningHTML = "";
	   
           
           // custom field generateion
           //runningHTML+="<input type='text' />";
           var fields = customTypes[select.value];
           for (var i = 0; i<fields.length; i++){
           runningHTML+="<label>"+fields[i]+"</label>";
           runningHTML+='<input type="text" name="'+fields[i]+'"/>';
           }
           
	   
           runningHTML+="<input type='submit' name='<?php echo $CONTAG_ADD_CUSTOM_KEY_NAME ?>' value='Submit'/>";
           // and...DISPLAY!
           customContainer.innerHTML=runningHTML;
           
           }
           
           }
           
           </script>
           
           <hr />
           <h2>Add custom item</h2>
           <form method="post" action="edit.php?id=<?php echo $courseid ?>">
           
           <?php // A list of types 
           ?>
           <select id="customSelect" name="<?php echo $CONTAG_CUSTOM_SELECT_KEY_NAME ?>" onchange="customTypeSelected(this)">
           <option value="" selected=true>[Choose type]</option>
           <?php foreach (contag_get_custom_item_types() as $type){
           echo '<option value='.$type.'>'.$type.'</option>\n';
           }
           
           ?>
           
    </select>
    <div id="customContainer"></div>
    </form>
    
          */
          ?>
          
          <?php
          if ($an_error){
              print("<div style=\"background-color: yellow\">$an_error</div>");
          }
 }  else { // end has_capability
    print("You do not have permissions to edit concept tags.");
 }
$OUTPUT->footer($course);
?>