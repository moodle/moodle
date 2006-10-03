<?php
require_once('../config.php');
require_login();


//form process
$mode = optional_param('mode','',PARAM_ALPHA);

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

$context = get_context_instance(CONTEXT_SYSTEM, SITEID);


switch ($mode) {
    case 'addofficial':
        /// Adding official tags.
        if (!has_capability('moodle/blog:manageofficialtags', $context) || !confirm_sesskey()) {
            die('you can not add official tags');
        }
        
        if (($otag = optional_param('otag', '', PARAM_NOTAGS)) && (!get_record('tags','text',$otag))) {
            $tag->userid = $USER->id;
            $tag->text = $otag;
            $tag->type = 'official';
            $tagid = insert_record('tags', $tag);
            
            /// Write newly added tags back into window opener.
            echo '<script language="JavaScript" type="text/javascript">
            var o = opener.document.createElement("option");
            o.innerHTML = "<option>'.$tag->text.'</option>";
            o.value = '.$tagid.';
            opener.document.entry[\'otags[]\'].insertBefore(o, null);
            </script>';
        } else {
            /// Tag already exists.
            notify(get_string('tagalready'));
        }

    break;
    
    case 'addpersonal':
        /// Everyone can add personal tags as long as they can write blog entries.
        if (!confirm_sesskey() ||
                !has_capability('moodle/blog:create', $context) ||
                empty($USER->id)) {
            error ('you can not add tags');
        }
        
        if (($ptag = optional_param('ptag', '', PARAM_NOTAGS)) && (!get_record('tags','text',$ptag))) {
            $tag->userid = $USER->id;
            $tag->text = $ptag;
            $tag->type = 'personal';
            $tagid = insert_record('tags', $tag);

            /// Write newly added tags back into window opener.
            echo '<script language="JavaScript" type="text/javascript">
            var o = opener.document.createElement("option");
            o.innerHTML = "<option>'.$tag->text.'</option>";
            o.value = '.$tagid.';
            opener.document.entry[\'ptags[]\'].insertBefore(o, null);
            </script>';
        } else {  
            /// Tag already exists.
            notify(get_string('tagalready'));
        }
        
    break;
    
    case 'delete':
        /// Delete a tag.
        if (!confirm_sesskey()) {
            error('you can not delete tags');
        }
        
        if ($tags = optional_param('tags', 0, PARAM_INT)) {
        
            foreach ($tags as $tag) {

                $blogtag = get_record('tags','id',$tag);

                // You can only delete your own tags, or you have to have the
                // moodle/blog:manageofficialtags capability.
                if (!has_capability('moodle/blog:manageofficialtags', $context)
                            && $USER->id != $blogtag->userid) {
                    notify(get_string('norighttodeletetag','blog', $blogtag->text));
                    continue;
                }

                // You can only delete tags that are referenced if you have
                // the moodle/blog:manageofficialtags capability.
                if (!has_capability('moodle/blog:manageofficialtags', $context)
                            && get_records('blog_tag_instance','tagid', $tag)) {
                    notify('tag is used by other users, can not delete!');
                    continue;
                }

                delete_records('tags','id',$tag);
                delete_records('blog_tag_instance', 'tagid', $tag);

                /// Remove parent window option via javascript.
                echo '<script>
                var i=0;
                while (i < window.opener.document.entry[\'otags[]\'].length) {
                    if (window.opener.document.entry[\'otags[]\'].options[i].value == '.$tag.') {
                        window.opener.document.entry[\'otags[]\'].removeChild(opener.document.entry[\'otags[]\'].options[i]);
                    }
                    i++;
                }

                var i=0;
                while (i < window.opener.document.entry[\'ptags[]\'].length) {
                    if (window.opener.document.entry[\'ptags[]\'].options[i].value == '.$tag.') {
                        window.opener.document.entry[\'ptags[]\'].removeChild(opener.document.entry[\'ptags[]\'].options[i]);
                    }
                    i++;
                }

                </script>';
            }
        }
    break;
    
    default:
        /// Just display the tags form.
    break;
}


/// Print the table.
print_header();
include_once('tags.html');
print_footer();


?>
