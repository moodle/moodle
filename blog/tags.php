<?php  // $Id$
require_once('../config.php');

/// main switch for form processing to perform, add/delete etc
$action = optional_param('action','',PARAM_ALPHA);

require_login();

/// blogs could be disabled altogether
if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

if (isguest()) {
    error(get_string('noguestpost', 'blog'));
}

/// blogs are site level
$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

$error = '';

$script = '';
switch ($action) {
    /// Adding an official tag from submitted value
    case 'addofficial':
        // Double check to make sure user has capability
        if (!has_capability('moodle/blog:manageofficialtags', $sitecontext)) {
            error('Can not add official tags tags');
        }
        if (data_submitted() and confirm_sesskey()) {
            
            $otag = trim(required_param('otag', PARAM_NOTAGS));
            // When adding ofical tag, we see if there's already a personal tag
            // With the same Name, if there is, we just change the type
            if ($tag = tag_by_name ($otag)) {
                if ($tag->type == 'official') {
                    // official tag already exist
                    $error = get_string('tagalready');
                    break;
                } else { 
                    $tag->type = 'official';
                    update_record('tag', $tag);
                    $tagid = $tag->id;
                }
                
            } else { // Brand new offical tag
                if (!$tagid = tag_create($otag, 'official')) {
                    error('Can not create tag!');
                }
            }

            /// Write newly added tags back into window opener.
            $script = '<script type="text/javascript">
//<![CDATA[
            var o = opener.document.createElement("option");
            o.innerHTML = "<option>'.$tag->text.'</option>";
            o.value = '.$tagid.';
            opener.document.entry[\'otags[]\'].insertBefore(o, null);
//]]>
            </script>';
        }

    break;
    
    /// Deletes a tag.
    case 'delete':
        
        if (data_submitted() and confirm_sesskey()) {
            $tagids = optional_param('tags', array(), PARAM_INT);

            if (empty($tagids) or !is_array($tagids)) {
                // TODO add error message here
                // $error = 'no data selected';
                break;
            }

            foreach ($tagids as $tagid) {

                if (!$tag = get_record('tags', 'id', $tagid)) {
                    continue; // page refreshed?
                }

                if ($tag->type == 'official' and !has_capability('moodle/blog:manageofficialtags', $sitecontext)) {
                    //can not delete
                    continue;
                }

                if ($tag->type == 'personal' and !has_capability('moodle/blog:managepersonaltags', $sitecontext)) {
                    //can not delete
                    continue;
                }
                
                // Delete the tag itself
                if (!delete_records('tags', 'id', $tagid)) {
                    error('Can not delete tag');
                }
                
                // Deleteing all references to this tag
                if (!delete_records('blog_tag_instance', 'tagid', $tagid)) {
                    error('Can not delete blog tag instances');
                }

                /// Remove parent window option via javascript.
                $script = '<script>
//<![CDATA[
                var i=0;
                while (i < window.opener.document.entry[\'otags[]\'].length) {
                    if (window.opener.document.entry[\'otags[]\'].options[i].value == '.$tagid.') {
                        window.opener.document.entry[\'otags[]\'].removeChild(opener.document.entry[\'otags[]\'].options[i]);
                    }
                    i++;
                }

                var i=0;
                while (i < window.opener.document.entry[\'ptags[]\'].length) {
                    if (window.opener.document.entry[\'ptags[]\'].options[i].value == '.$tagid.') {
                        window.opener.document.entry[\'ptags[]\'].removeChild(opener.document.entry[\'ptags[]\'].options[i]);
                    }
                    i++;
                }
//]]>
                </script>';
            }

        }
    break;

    default:
        /// Just display the tags form.
    break;
}


/// Print the table.
print_header (get_string('tagmanagement'), '', '', '', $script);
include_once('tags.html');
print_footer();


?>
